# Testing Checklist - Esirom Client Hub

## ✅ Pre-Flight Checks (All Passed)

- [x] Backend server running on port 5001
- [x] MongoDB connected and running
- [x] Database seeded with sample data
- [x] API health endpoint responding
- [x] Login endpoint tested and working
- [x] Critical bug in functions.php fixed

---

## 🧪 Manual Testing Steps

### Step 1: Create WordPress Pages (If Not Already Done)

1. **Login Page:**
   - Go to WordPress Admin → Pages → Add New
   - Title: "Client Hub Login" (or any name)
   - Template: Select **"Login Page"** from Page Attributes
   - Publish the page
   - Note the URL (e.g., `http://esirom-hub.local/client-hub-login`)

2. **Dashboard Page:**
   - Go to WordPress Admin → Pages → Add New
   - Title: "Client Hub Dashboard" (or any name)
   - Template: Select **"Dashboard Page"** from Page Attributes
   - Publish the page
   - Note the URL (e.g., `http://esirom-hub.local/client-hub-dashboard`)

3. **Admin Page (Optional):**
   - Go to WordPress Admin → Pages → Add New
   - Title: "Client Hub Admin" (or any name)
   - Template: Select **"Admin Page"** from Page Attributes
   - Publish the page

---

### Step 2: Test Login Flow

1. **Visit the Login Page**
   - Open the login page URL in your browser
   - You should see the Esirom Agency Hub login form

2. **Check Browser Console**
   - Press F12 to open DevTools
   - Go to Console tab
   - Look for any errors (there should be none)
   - Check Network tab - API_URL should be defined

3. **Test Login with Admin Account**
   - Email: `admin@esirom.com`
   - Password: `admin123`
   - Click "Sign in"
   - You should be redirected to the dashboard

4. **Verify Dashboard Loads**
   - Dashboard should display user info
   - Charts should render
   - No console errors

---

### Step 3: Test Different User Roles

#### Test as Client
1. Logout from admin account
2. Login with:
   - Email: `client@partnershealth.com`
   - Password: `client123`
3. Verify:
   - Can see only their own client data
   - Can approve/reject posts
   - Cannot access admin features

#### Test as Brand Rep
1. Logout
2. Login with:
   - Email: `brandrep@esirom.com`
   - Password: `brandrep123`
3. Verify:
   - Can see assigned clients
   - Can create posts
   - Can manage content calendar

---

### Step 4: Test Core Features

#### Dashboard View
- [ ] KPI cards display correctly
- [ ] Charts render (engagement, followers, etc.)
- [ ] Recent posts show up
- [ ] Dark mode toggle works

#### Content Calendar
- [ ] Calendar displays posts
- [ ] Can filter by platform
- [ ] Can view post details
- [ ] Approval workflow works (for clients)

#### Reports
- [ ] Can view existing reports
- [ ] Reports display metrics
- [ ] Can add annotations (if admin/brand rep)

#### KPI Tracking
- [ ] KPIs display with progress bars
- [ ] Can update KPI values (if admin/brand rep)
- [ ] Progress calculations are correct

---

## 🐛 Common Issues & Solutions

### Issue: "Cannot connect to API"
**Solution:**
```bash
# Check if backend is running
curl http://localhost:5001/api/health

# If not running, start it:
cd /Users/esirom-director/Local\ Sites/esirom-hub/app/public/wp-content/themes/esiromhub/backend
npm run dev
```

### Issue: "Login page shows but login fails"
**Solutions:**
1. Open browser console (F12) and check for errors
2. Verify API_URL is defined: Look for `ESIROM_API_URL` in page source
3. Clear browser localStorage:
   - F12 → Application → Local Storage → Clear
4. Try a different browser

### Issue: "Redirects to wrong page after login"
**Solution:**
Edit `page-login.php` around line 214 and update the redirect URL:
```php
window.location.href = '<?php echo esc_url(get_permalink(get_page_by_path('your-dashboard-page-slug'))); ?>';
```

### Issue: "Dashboard shows 'Unauthorized'"
**Solution:**
1. Clear localStorage
2. Login again
3. Check that token is being stored (F12 → Application → Local Storage)

---

## 🔍 Debugging Tips

### Check API Connection
```bash
# Test health endpoint
curl http://localhost:5001/api/health

# Test login endpoint
curl -X POST http://localhost:5001/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@esirom.com","password":"admin123"}'
```

### Check MongoDB
```bash
# Check if MongoDB is running
pgrep -f mongod

# Connect to MongoDB
mongosh

# In MongoDB shell:
use esirom-hub
db.users.find().pretty()
```

### Check WordPress Configuration
1. Go to WordPress Admin → Appearance → Hub Settings
2. Verify API URL is: `http://localhost:5001/api`
3. Save if needed

### Browser DevTools Checklist
- [ ] No errors in Console tab
- [ ] API requests in Network tab show 200 status
- [ ] localStorage has 'token' and 'user' items
- [ ] ESIROM_API_URL is defined in page source

---

## ✅ Success Criteria

Your system is working correctly if:

1. ✅ Login page loads without errors
2. ✅ Can login with any of the demo accounts
3. ✅ Dashboard loads and displays data
4. ✅ Charts render correctly
5. ✅ Can navigate between different views
6. ✅ Can logout and login again
7. ✅ Different user roles see appropriate content

---

## 📊 Test Results Template

```
Test Date: _______________
Tester: __________________

Login Page:           [ ] Pass  [ ] Fail
Admin Login:          [ ] Pass  [ ] Fail
Client Login:         [ ] Pass  [ ] Fail
Brand Rep Login:      [ ] Pass  [ ] Fail
Dashboard Display:    [ ] Pass  [ ] Fail
Charts Rendering:     [ ] Pass  [ ] Fail
Content Calendar:     [ ] Pass  [ ] Fail
Reports:              [ ] Pass  [ ] Fail
KPI Tracking:         [ ] Pass  [ ] Fail
Dark Mode:            [ ] Pass  [ ] Fail
Logout:               [ ] Pass  [ ] Fail

Notes:
_________________________________
_________________________________
_________________________________
```

---

**Ready to test? Start with Step 1 above!** 🚀
