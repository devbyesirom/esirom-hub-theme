# Quick Reference Card - Esirom Client Hub

## 🔑 Login Credentials

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@esirom.com | admin123 |
| **Client** | client@partnershealth.com | client123 |
| **Client** | client@techinnovators.com | client123 |
| **Brand Rep** | brandrep@esirom.com | brandrep123 |

---

## 🌐 URLs

| Service | URL |
|---------|-----|
| **Backend API** | http://localhost:5001/api |
| **Health Check** | http://localhost:5001/api/health |
| **WordPress Site** | http://esirom-hub.local (or your local domain) |

---

## 🛠️ Common Commands

### Start Backend Server
```bash
cd /Users/esirom-director/Local\ Sites/esirom-hub/app/public/wp-content/themes/esiromhub/backend
npm run dev
```

### Stop Backend Server
```bash
# Press Ctrl+C in the terminal where it's running
```

### Reseed Database
```bash
cd /Users/esirom-director/Local\ Sites/esirom-hub/app/public/wp-content/themes/esiromhub/backend
npm run seed
```

### Check Backend Status
```bash
curl http://localhost:5001/api/health
```

### Check MongoDB Status
```bash
pgrep -f mongod
```

---

## 📁 Important Files

| File | Purpose |
|------|---------|
| `functions.php` | WordPress theme functions & API URL configuration |
| `page-login.php` | Login page template |
| `page-dashboard.php` | Dashboard page template |
| `page-admin.php` | Admin panel template |
| `backend/.env` | Backend configuration (PORT, MongoDB, JWT secret) |
| `backend/server.js` | Express server entry point |

---

## 🔧 WordPress Settings

**Location:** WordPress Admin → Appearance → Hub Settings

**API URL:** `http://localhost:5001/api`

---

## 🎨 Page Templates

When creating WordPress pages, use these templates:

1. **Login Page** - For user authentication
2. **Dashboard Page** - Main client hub interface
3. **Admin Page** - Admin panel for managing users/clients

---

## 🐛 Quick Troubleshooting

| Problem | Solution |
|---------|----------|
| Login fails | Check backend is running: `curl http://localhost:5001/api/health` |
| API errors | Clear browser localStorage (F12 → Application → Clear) |
| Port conflict | Port 5001 is correct (5000 used by macOS) |
| MongoDB error | Check MongoDB is running: `pgrep -f mongod` |
| Page not loading | Check browser console (F12) for errors |

---

## 📞 Support Files

- **START_HERE.md** - Quick start guide
- **FIXES_APPLIED.md** - What was fixed and why
- **TESTING_CHECKLIST.md** - Complete testing guide
- **README.md** - Full documentation
- **SETUP_GUIDE.md** - Detailed setup instructions

---

## ⚡ Quick Test

```bash
# Test backend is working
curl http://localhost:5001/api/health

# Test login endpoint
curl -X POST http://localhost:5001/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@esirom.com","password":"admin123"}'
```

Both should return JSON responses without errors.

---

**Everything you need at a glance!** 📋
