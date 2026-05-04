# WordPress Theme Setup Guide

## Quick WordPress Setup

### Step 1: Activate the Theme

1. Go to **WordPress Admin** → **Appearance** → **Themes**
2. Find "Esirom Client Hub" theme
3. Click **Activate**

### Step 2: Configure API Settings

1. Go to **Appearance** → **Hub Settings**
2. Enter your API URL (e.g., `http://localhost:5000/api`)
3. Click **Save Settings**

### Step 3: Create Required Pages

#### Create Login Page
1. Go to **Pages** → **Add New**
2. Title: "Login" (or any name you prefer)
3. In the **Page Attributes** box on the right, select **Template: Login Page**
4. Click **Publish**
5. Note the page URL (e.g., `yoursite.com/login`)

#### Create Dashboard Page
1. Go to **Pages** → **Add New**
2. Title: "Dashboard" (or any name you prefer)
3. In the **Page Attributes** box on the right, select **Template: Dashboard Page**
4. Click **Publish**
5. Note the page URL (e.g., `yoursite.com/dashboard`)

### Step 4: Update Page URLs in Templates

You need to update the redirect URLs in the page templates:

#### Update Login Page Redirect
Edit `/wp-content/themes/esiromhub/page-login.php` around line 170:

```javascript
// Change this line:
window.location.href = '<?php echo esc_url(home_url('/')); ?>';

// To point to your dashboard page:
window.location.href = '<?php echo esc_url(get_permalink(get_page_by_path('dashboard'))); ?>';
```

Or if you named your dashboard page differently, replace `'dashboard'` with your page slug.

### Step 5: Start Your Backend API

```bash
cd wp-content/themes/esiromhub/backend
npm install
cp .env.example .env
# Edit .env with your settings
npm run seed
npm run dev
```

### Step 6: Test the System

1. Visit your login page (e.g., `yoursite.com/login`)
2. Use demo credentials:
   - **Admin**: admin@esirom.com / admin123
   - **Client**: client@partnershealth.com / client123
3. You should be redirected to the dashboard after login

## Theme Structure

```
esiromhub/
├── style.css              # Theme header (required by WordPress)
├── functions.php          # Theme functions and settings
├── index.php              # Default template
├── header.php             # Header template
├── footer.php             # Footer template
├── page-login.php         # Login page template
├── page-dashboard.php     # Dashboard page template
├── backend/               # Node.js API backend
├── README.md              # Full documentation
├── SETUP_GUIDE.md         # Quick setup guide
└── WORDPRESS_SETUP.md     # This file
```

## Important Notes

### Authentication Flow
1. User visits login page
2. Credentials sent to Node.js API backend
3. API returns JWT token
4. Token stored in browser localStorage
5. Dashboard page validates token with API
6. If valid, dashboard loads with user data
7. If invalid, redirects back to login

### Security
- WordPress is only used for page routing
- All authentication happens via the Node.js API
- JWT tokens are stored in browser localStorage
- API validates all requests with JWT middleware

### Customization

#### Change Theme Colors
Edit the Tailwind classes in `page-login.php` and `page-dashboard.php`:
- Search for `indigo` and replace with your brand color
- Example: `bg-indigo-600` → `bg-blue-600`

#### Add Custom Logo
Replace the SVG logo in both templates:
- Find the `<svg>` element in the header
- Replace with your logo image or SVG

#### Modify Dashboard Widgets
Edit `page-dashboard.php`:
- KPI cards start around line 180
- Charts configuration around line 450
- Add/remove sections as needed

## Troubleshooting

### "Cannot connect to API"
- Check that backend is running: `http://localhost:5000/api/health`
- Verify API URL in **Appearance** → **Hub Settings**
- Check browser console for CORS errors

### "Page template not showing"
- Make sure you've activated the theme
- Refresh the page editor
- Check that the template file exists in the theme folder

### "Login not working"
- Verify backend is running and seeded
- Check API URL is correct
- Open browser console (F12) for error messages
- Make sure MongoDB is running

### "Redirects not working"
- Update the page URLs in `page-login.php` (line ~170)
- Update the page URLs in `page-dashboard.php` (line ~450)
- Use the correct page slug (check in WordPress Pages list)

## Advanced Configuration

### Using a Custom Domain for API

1. Deploy your backend to a server (Heroku, DigitalOcean, etc.)
2. Update API URL in **Appearance** → **Hub Settings**
3. Example: `https://api.yourdomain.com/api`
4. Make sure CORS is configured in backend `.env`:
   ```
   FRONTEND_URL=https://yourdomain.com
   ```

### SSL/HTTPS Setup

1. Install SSL certificate on your WordPress site
2. Update WordPress URL to use `https://`
3. Update API backend CORS settings
4. Test login and dashboard functionality

### Multiple Client Sites

You can use the same backend API for multiple WordPress installations:

1. Deploy backend once to a server
2. Install theme on multiple WordPress sites
3. Point all sites to the same API URL
4. Each site can have different branding/styling

## Next Steps

1. ✅ Activate theme
2. ✅ Configure API settings
3. ✅ Create login and dashboard pages
4. ✅ Start backend API
5. ✅ Test login functionality
6. 🎨 Customize branding and colors
7. 📊 Configure social media integrations
8. 🚀 Deploy to production

## Support

For issues or questions:
- Check the main `README.md` for detailed documentation
- Review `SETUP_GUIDE.md` for backend setup
- Contact: support@esirom.com

---

**Theme Version**: 1.0.0  
**WordPress Compatibility**: 5.0+  
**PHP Version**: 7.4+
