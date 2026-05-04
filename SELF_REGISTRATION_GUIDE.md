# Self-Service Registration System - Setup Guide

## 🎉 Overview

Your Agency Hub now has a complete self-service registration system with admin approval workflow!

---

## ✨ Features

### For Users (Clients/Staff)
- ✅ **Self-registration** - Create account without admin intervention
- ✅ **Custom password** - Choose their own secure password
- ✅ **Company information** - Provide company/brand details
- ✅ **Additional notes** - Add special requirements or information
- ✅ **Status tracking** - Know when account is pending approval
- ✅ **Automatic notifications** - Clear messages about account status

### For Admins
- ✅ **Pending users dashboard** - See all registration requests in one place
- ✅ **Review details** - View user info, company, and notes before approving
- ✅ **One-click approval** - Approve users and assign to clients
- ✅ **Rejection option** - Decline inappropriate registrations
- ✅ **Notification badge** - See pending count at a glance
- ✅ **Audit trail** - Track registration dates and status

---

## 🚀 Setup Instructions

### Step 1: Create Registration Page in WordPress

1. **Login to WordPress Admin** (`/wp-admin`)
2. **Go to Pages** → **Add New**
3. **Page Settings:**
   - Title: `Register` or `Sign Up`
   - Template: Select **"Registration Page"**
   - Publish the page
4. **Copy the page URL** (e.g., `https://yoursite.com/register`)

### Step 2: Add Registration Link to Login Page (Optional)

The registration page already has a link to the login page. You can add a link from login to registration:

1. Edit your login page
2. Add a link: `<a href="/register">Create an account</a>`

### Step 3: Test the Registration Flow

#### As a New User:
1. Visit the registration page
2. Fill out the form:
   - First Name
   - Last Name
   - Email
   - Company/Brand Name
   - Password (min. 6 characters)
   - Confirm Password
   - Additional Information (optional)
3. Click "Create Account"
4. See success message
5. Try to login → Should see "pending approval" message

#### As Admin:
1. Login to admin panel
2. Click "Pending Users" in sidebar
3. See the new registration request
4. Review user details
5. Click "Approve" or "Reject"
6. If approved, optionally assign to a client

#### As Approved User:
1. Try to login again
2. Should now successfully access the dashboard!

---

## 📋 API Endpoints

### Public Endpoints

**POST** `/api/auth/register-request`
- **Description**: Public self-registration
- **Body**:
  ```json
  {
    "firstName": "John",
    "lastName": "Doe",
    "email": "john@company.com",
    "companyName": "Acme Corp",
    "password": "securepass123",
    "registrationNote": "Looking forward to working with you!"
  }
  ```
- **Response**: Success message, user status set to "pending"

### Admin Endpoints (Require Auth)

**GET** `/api/users/pending/list`
- **Description**: Get all pending user registrations
- **Headers**: `Authorization: Bearer {token}`
- **Response**: Array of pending users

**PUT** `/api/users/:id/approve`
- **Description**: Approve a pending user
- **Headers**: `Authorization: Bearer {token}`
- **Body** (optional):
  ```json
  {
    "clientId": "client_id_here",
    "assignedClients": ["client1", "client2"]
  }
  ```
- **Response**: Approved user object

**PUT** `/api/users/:id/reject`
- **Description**: Reject a pending user
- **Headers**: `Authorization: Bearer {token}`
- **Response**: Success message

---

## 🔒 Security Features

1. **Password Validation**: Minimum 6 characters required
2. **Email Validation**: Valid email format enforced
3. **Duplicate Prevention**: Can't register with existing email
4. **Status Checks**: Users can't login until approved
5. **Admin-Only Approval**: Only admins can approve/reject
6. **Secure Password Storage**: Passwords are hashed with bcrypt

---

## 🎨 User Experience

### Registration Flow
```
User fills form → Submits → Account created (pending)
                              ↓
                    Success message displayed
                              ↓
                    Redirected to login (3 seconds)
                              ↓
                    Tries to login → "Pending approval" message
```

### Admin Approval Flow
```
Admin logs in → Sees "Pending Users" badge
                    ↓
            Clicks "Pending Users" tab
                    ↓
            Reviews user information
                    ↓
        Clicks "Approve" or "Reject"
                    ↓
    User status updated → User can now login
```

---

## 💡 Best Practices

### For Admins
1. **Review regularly** - Check pending users daily
2. **Verify legitimacy** - Check company name and email domain
3. **Assign clients** - Link users to appropriate clients immediately
4. **Communicate** - Email users when approved (manual for now)

### For Users
1. **Use work email** - Increases approval chances
2. **Provide details** - Add notes explaining your needs
3. **Be patient** - Wait for admin approval before trying to login
4. **Check email** - Watch for approval notifications (if implemented)

---

## 🔧 Customization Options

### Change Default Role
Edit `routes/auth.js` line 54:
```javascript
role: 'client', // Change to 'brand_rep' if needed
```

### Add Email Notifications
You can integrate email sending in the approval/rejection endpoints:
- Use a service like SendGrid, Mailgun, or AWS SES
- Send email when user is approved
- Send email when user is rejected (with reason)

### Add More Fields
Edit `models/User.js` to add fields like:
- Phone number
- Company website
- Industry
- Team size

### Customize Registration Page
Edit `page-register.php` to:
- Change colors/styling
- Add more form fields
- Modify success/error messages
- Add terms & conditions checkbox

---

## 📊 Database Schema

### User Model Updates
```javascript
{
  // Existing fields...
  status: {
    type: String,
    enum: ['pending', 'approved', 'rejected'],
    default: 'approved'  // For admin-created users
  },
  registrationNote: {
    type: String,
    trim: true
  },
  companyName: {
    type: String,
    trim: true
  }
}
```

---

## 🐛 Troubleshooting

### User Can't Register
- Check if email already exists
- Verify API URL is correct
- Check browser console for errors
- Ensure backend is deployed

### Admin Can't See Pending Users
- Verify admin is logged in
- Check if `/api/users/pending/list` endpoint is accessible
- Refresh the page
- Check browser console for errors

### Approval Doesn't Work
- Verify admin has proper permissions
- Check if client ID is valid (if assigning)
- Look at network tab for API errors
- Check backend logs

### User Still Can't Login After Approval
- Verify status changed to 'approved'
- Check if `isActive` is set to `true`
- Clear browser cache
- Try logging out and back in

---

## 📈 Future Enhancements

Potential features to add:
- [ ] Email notifications on approval/rejection
- [ ] Bulk approve/reject
- [ ] Registration form customization in admin
- [ ] User profile completion after approval
- [ ] Two-factor authentication
- [ ] Social login (Google, Microsoft)
- [ ] Invitation system (admin invites users)
- [ ] Registration analytics dashboard

---

## 🎯 Quick Reference

### WordPress Pages Needed
- ✅ Registration Page (Template: "Registration Page")
- ✅ Login Page (Already exists)
- ✅ Admin Panel (Already exists)

### User Statuses
- **pending** - Awaiting admin approval
- **approved** - Can login and access system
- **rejected** - Registration declined

### Admin Panel Tabs
- **Users** - All approved users
- **Pending Users** - Registration requests (NEW!)
- **Clients** - Client management
- **Import Posts** - Data import

---

## 📞 Support

If you encounter issues:
1. Check this guide first
2. Review browser console for errors
3. Check backend logs in Railway
4. Verify all endpoints are deployed
5. Test with a fresh browser/incognito mode

---

**Happy Registering! 🚀**

Your clients and staff can now easily request access to the platform!
