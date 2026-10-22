# Disable / re-enable clients — backend requirements

Theme 2.3.65 adds **Disable client** to the Admin Panel. The theme can only
*ask* the backend to disable a client and *react* to the backend refusing a
disabled account; the lockout itself has to be enforced by the Hub API
(`devbyesirom/esirom-hub-backend`, Node/Express + Mongoose, deployed on
Railway). This document is the exact contract the theme expects.

Everything the theme does is already in place and degrades gracefully: until
the backend ships, "Disable" either works through the legacy `PUT /clients/:id`
(if that route persists `isActive`) or shows a clear error pointing here.

## 1. Data model (`models/Client.js`)

The `Client` schema already has `isActive` (default `true`). Add the audit
fields:

```js
isActive:      { type: Boolean, default: true, index: true },
disabledAt:    { type: Date, default: null },
disabledBy:    { type: mongoose.Schema.Types.ObjectId, ref: 'User', default: null },
reactivatedAt: { type: Date, default: null },
reactivatedBy: { type: mongoose.Schema.Types.ObjectId, ref: 'User', default: null },
```

No data is deleted when a client is disabled; `isActive: false` is the only
state change. Everything that references the client (posts, reports, KPIs,
concepts, insights, settings) stays untouched.

## 2. Status endpoint (`routes/clients.js`)

```
PUT /api/clients/:id/status
Authorization: Bearer <admin JWT>
Body: { "isActive": true | false }
```

- Admin only (`protect` + `authorize('admin')`); anything else → `403`.
- `isActive` must be a boolean → otherwise `400 { success:false, message }`.
- On `false`: set `isActive=false`, `disabledAt=new Date()`, `disabledBy=req.user._id`.
- On `true`: set `isActive=true`, `disabledAt=null`, `disabledBy=null`,
  `reactivatedAt=new Date()`, `reactivatedBy=req.user._id`.
- Respond `200 { success:true, data: client }` with `disabledBy` / `reactivatedBy`
  populated (`firstName lastName email`) — the theme renders
  "Disabled 14 Oct 2026, 09:12 by Alex Morrison".

```js
router.put('/:id/status', protect, authorize('admin'), async (req, res) => {
  const { isActive } = req.body;
  if (typeof isActive !== 'boolean') {
    return res.status(400).json({ success: false, message: 'isActive (boolean) is required' });
  }
  const update = isActive
    ? { isActive: true, disabledAt: null, disabledBy: null, reactivatedAt: new Date(), reactivatedBy: req.user._id }
    : { isActive: false, disabledAt: new Date(), disabledBy: req.user._id };
  const client = await Client.findByIdAndUpdate(req.params.id, update, { new: true })
    .populate('disabledBy', 'firstName lastName email')
    .populate('reactivatedBy', 'firstName lastName email');
  if (!client) return res.status(404).json({ success: false, message: 'Client not found' });
  res.json({ success: true, data: client });
});
```

`GET /api/clients` and `GET /api/clients/:id` should populate `disabledBy`
the same way so the audit line shows on the Clients tab.

## 3. Listing: hide disabled clients by default

`GET /api/clients` already accepts `?includeInactive=true` (the Admin Panel
sends it). Make sure:

- Without `includeInactive=true` the query includes `isActive: { $ne: false }`
  (`$ne` so legacy documents without the field still count as active).
- `includeInactive=true` is honoured **only** for `admin` users.
- Every other brand list used by pickers excludes disabled clients too:
  `GET /calendar/clients`, `GET /inventory/clients`,
  `GET /website-projects/clients`, `GET /workflow/*` brand lookups,
  `GET /analytics/agency-overview` (`brands`), the email-preview endpoints
  under `/admin/*` (monthly report, reminders, announcements, inactive pages)
  and any scheduled emails (digests, reminders, monthly reports) — a disabled
  client must not receive automated email.

The theme also filters `isActive === false` out of these lists client-side
(`HubAuth.activeClients`) as a belt-and-braces measure, but the API is the
source of truth.

## 4. Enforcement: disabled clients cannot sign in or use the API

Add one helper (e.g. `utils/accountStatus.js`) and use it in **both** places
below:

```js
// A client-role account is disabled when the user itself is inactive or when
// every brand it is assigned to is disabled. Staff (admin / brand_rep) are
// never locked out by a disabled client.
async function isAccountDisabled(user) {
  if (!user) return true;
  if (user.isActive === false) return true;
  if (user.role !== 'client') return false;
  const ids = [user.clientId, ...(user.clientIds || [])].filter(Boolean).map(String);
  if (!ids.length) return false;
  const active = await Client.countDocuments({ _id: { $in: ids }, isActive: { $ne: false } });
  return active === 0;
}

const ACCOUNT_DISABLED = {
  success: false,
  code: 'ACCOUNT_DISABLED',
  message: 'This account has been disabled. Please contact Esirom.'
};
```

### 4a. Login (`routes/auth.js` → `POST /auth/login`)

After the password check succeeds and before issuing the JWT:

```js
if (await isAccountDisabled(user)) return res.status(403).json(ACCOUNT_DISABLED);
```

### 4b. Every authenticated request (`middleware/auth.js` → `protect`)

After the JWT is verified and the user loaded:

```js
if (await isAccountDisabled(req.user)) return res.status(403).json(ACCOUNT_DISABLED);
```

This is what makes **existing sessions and tokens stop working**: the JWT is
still cryptographically valid, but the very next request (including
`/auth/me`, which every Hub page calls on load) is refused with
`403 ACCOUNT_DISABLED`. The theme's `HubAuth` fetch guard sees that code,
clears `localStorage`, and sends the browser to the sign-in page, which shows
"This account has been disabled. Please contact Esirom."

Keep this check cheap: `countDocuments` on an indexed `_id` set is fine; cache
per request if the middleware runs more than once.

### 4c. Password reset / registration

- `POST /auth/forgot-password`: respond with the usual neutral success message
  for disabled accounts but do **not** send the email.
- `POST /auth/reset-password`: refuse with `403 ACCOUNT_DISABLED`.

### 4d. Google sign-in (`GET /auth/google/callback`)

When the matched user is disabled, redirect to the Hub sign-in page with
`?error=account_disabled` (the theme turns this into the friendly message):

```js
return res.redirect(`${process.env.FRONTEND_URL}/login/?error=account_disabled`);
```

## 5. Response contract the theme relies on

| Situation | Status | Body |
|-----------|--------|------|
| Disabled client signs in | `403` | `{ success:false, code:"ACCOUNT_DISABLED", message:"This account has been disabled. Please contact Esirom." }` |
| Disabled client uses an existing token | `403` | same body, on every protected route incl. `/auth/me` |
| Google callback for disabled client | `302` | redirect to `/login/?error=account_disabled` |
| Status change OK | `200` | `{ success:true, data: <client with disabledBy populated> }` |
| Status change by non-admin | `403` | `{ success:false, message }` (no `code`) |

`HubAuth.isDisabledResponse()` recognises `code === 'ACCOUNT_DISABLED'`
(preferred) and, as a fallback, a `message` containing "account … disabled".

## 6. Deploy

Railway auto-deploys `main`:

```bash
git add . && git commit -m "Disable/re-enable clients: status route, lockout, audit fields" && git push origin main
```

Then verify:

```bash
# status route (admin token)
curl -X PUT "$API/clients/<id>/status" -H "Authorization: Bearer $ADMIN" -H "Content-Type: application/json" -d '{"isActive":false}'
# disabled client cannot sign in
curl -X POST "$API/auth/login" -H "Content-Type: application/json" -d '{"email":"client@example.com","password":"..."}'   # → 403 ACCOUNT_DISABLED
# existing token stops working
curl "$API/auth/me" -H "Authorization: Bearer $CLIENT_TOKEN"   # → 403 ACCOUNT_DISABLED
# default list hides the client; admin list with includeInactive shows it
curl "$API/clients" -H "Authorization: Bearer $ADMIN"
curl "$API/clients?includeInactive=true" -H "Authorization: Bearer $ADMIN"
```
