# Admin Features Guide

## ✅ New Features Added

### 1. Admin Panel
A complete admin dashboard for managing users, clients, and platform updates.

**Access:** Create a WordPress page with the **"Admin Panel"** template

**Features:**
- ✅ User Management (Create, Edit, Delete)
- ✅ Client Management (Create, Edit, Delete)
- ✅ Admin-only access (automatically checks user role)

### 2. Multi-Brand Support
Clients can now be assigned to multiple brands!

**How it works:**
- When creating a **Client user**, you can select multiple clients from the dropdown
- Hold Ctrl/Cmd to select multiple clients
- The user will have access to all assigned client dashboards

### 3. Brand Rep Assignment
Brand representatives can be assigned to multiple clients.

**How it works:**
- When creating a **Brand Rep user**, select which clients they should manage
- They will only see data for their assigned clients

### 4. Platform Updates on Login Page
The login page shows the latest platform updates loaded from `updates.json` in the theme. No admin management is required.

**Features:**
- Displays latest updates
- Shows current version number

## 🚀 Setup Instructions

### Step 1: Create Admin Panel Page
1. Go to **WordPress Admin → Pages → Add New**
2. Title: "Admin" (or any name)
3. Template: **Admin Panel**
4. Publish

### Step 2: Access Admin Panel
1. Login as admin (admin@esirom.com / admin123)
2. Visit your admin panel page
3. You'll see tabs: Users, Clients

### Step 3: Create Your First Client
1. Click **"Add New Client"** button
2. Fill in:
   - Client Name (e.g., "John Smith")
   - Brand Name (e.g., "Smith Marketing")
   - Contact Email
   - Contact Phone (optional)
   - Industry (optional)
   - Logo URL (optional)
3. Click **Save**

### Step 4: Create a Brand Rep
1. Go to **Users** tab
2. Click **"Add New User"**
3. Fill in details
4. Select Role: **Brand Representative**
5. In "Assign Clients" dropdown, select which clients they should manage (hold Ctrl/Cmd for multiple)
6. Click **Save**

### Step 5: Create a Client User (with Multiple Brands)
1. Go to **Users** tab
2. Click **"Add New User"**
3. Fill in details
4. Select Role: **Client**
5. In "Assign to Clients" dropdown, select multiple clients (hold Ctrl/Cmd)
6. Click **Save**

<!-- Platform Updates admin management removed -->

## 📊 User Roles Explained

### Admin
- Full access to everything
- Can create/edit/delete users and clients
- Can manage platform updates
- Access to admin panel

### Brand Representative
- Can manage assigned clients only
- Can create posts, reports, KPIs
- Cannot create new clients or users
- No access to admin panel

### Client
- Can view their own data only
- Can approve/reject posts
- Can view reports and KPIs
- Cannot create or edit anything
- Can be assigned to **multiple brands/clients**

## 🎨 Multi-Brand Client Example

**Scenario:** You have a client "John Smith" who manages 3 different brands:
- Brand A: "Smith Marketing"
- Brand B: "Smith Consulting"  
- Brand C: "Smith Media"

**Setup:**
1. Create 3 clients in admin panel (one for each brand)
2. Create one user account for John Smith
3. Role: **Client**
4. Assign to Clients: Select all 3 brands
5. When John logs in, he can switch between all 3 brands

<!-- Platform Updates best practices removed (no admin management) -->

## 🎯 Quick Actions

### Create a Brand Rep for Multiple Clients
1. Admin Panel → Users → Add New User
2. Role: Brand Representative
3. Assign Clients: Select multiple (Ctrl/Cmd + Click)
4. Save

### Create a Client User with Multiple Brands
1. Admin Panel → Users → Add New User
2. Role: Client
3. Assign to Clients: Select multiple brands
4. Save

<!-- Quick action for Platform Updates removed -->

## 📝 Notes

- Platform updates are displayed on the login page from `updates.json`
- Users can only see clients they're assigned to
- Admins can see everything
 

## 🆘 Troubleshooting

**Can't access admin panel?**
- Make sure you're logged in as admin
- Check that you created a page with "Admin Panel" template

**Updates not showing on login page?**
- Refresh the page
- Check browser console for errors
- Make sure you saved the update in admin panel

**Can't assign multiple clients?**
- Hold Ctrl (Windows) or Cmd (Mac) while clicking
- Make sure you created the clients first

---

**Need help?** Contact support@esirom.com
