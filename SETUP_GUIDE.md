# Quick Setup Guide - Esirom Client Hub

This guide will help you get the Esirom Client Hub up and running in minutes.

## ⚡ Quick Start (5 Minutes)

### Step 1: Install MongoDB

**macOS (using Homebrew):**
```bash
brew tap mongodb/brew
brew install mongodb-community
brew services start mongodb-community
```

**Windows:**
Download and install from [MongoDB Download Center](https://www.mongodb.com/try/download/community)

**Linux (Ubuntu):**
```bash
sudo apt-get install mongodb
sudo systemctl start mongodb
```

### Step 2: Setup Backend

```bash
# Navigate to backend directory
cd backend

# Install dependencies
npm install

# Create .env file from example
cp .env.example .env

# Generate a secure JWT secret and update .env
# You can use: node -e "console.log(require('crypto').randomBytes(32).toString('hex'))"

# Seed the database with sample data
npm run seed

# Start the server
npm run dev
```

You should see:
```
✅ MongoDB Connected
🚀 Server running on port 5000
📍 Environment: development
```

### Step 3: Start Frontend

Open a new terminal window:

```bash
# Navigate to the demo directory
cd /Users/esirom-director/Local\ Sites/esirom-hub/app/public/wp-content/themes/demo

# Start a simple HTTP server (choose one):

# Option 1: Python 3
python3 -m http.server 3000

# Option 2: Python 2
python -m SimpleHTTPServer 3000

# Option 3: Node.js
npx http-server -p 3000

# Option 4: PHP
php -S localhost:3000
```

### Step 4: Access the Application

Open your browser and go to:
```
http://localhost:3000/login.html
```

### Step 5: Login

Use one of these demo accounts:

**Admin Account:**
- Email: `admin@esirom.com`
- Password: `admin123`

**Client Account:**
- Email: `client@partnershealth.com`
- Password: `client123`

**Brand Rep Account:**
- Email: `brandrep@esirom.com`
- Password: `brandrep123`

## 🎯 What You'll See

### As a Client:
- Dashboard with your social media metrics
- Pending posts awaiting your approval
- KPI progress tracking
- Recent reports

### As a Brand Rep:
- Multiple client dashboards
- Content calendar management
- Report generation tools
- Post creation and scheduling

### As an Admin:
- Full system access
- User management
- Client management
- System settings

## 🔧 Configuration

### Minimum .env Configuration

```env
PORT=5000
NODE_ENV=development
MONGODB_URI=mongodb://localhost:27017/esirom-hub
JWT_SECRET=your_generated_secret_here
JWT_EXPIRE=7d
FRONTEND_URL=http://localhost:3000
```

### Generate JWT Secret

Run this command to generate a secure secret:
```bash
node -e "console.log(require('crypto').randomBytes(32).toString('hex'))"
```

Copy the output and paste it as your `JWT_SECRET` in `.env`

## 📱 Testing the System

### 1. Test Authentication
- Try logging in with different user roles
- Verify logout functionality
- Test "Remember me" feature

### 2. Test Dashboard
- Check if metrics are displaying
- Verify charts are rendering
- Test dark mode toggle

### 3. Test Role Permissions
- Login as client - verify you can only see your data
- Login as brand rep - verify you can see assigned clients
- Login as admin - verify full access

### 4. Test Content Approval (as Client)
- Look for pending posts notification
- Navigate to content calendar
- Approve or request revisions on posts

## 🚨 Common Issues & Solutions

### Issue: "Cannot connect to MongoDB"
**Solution:**
```bash
# Check if MongoDB is running
mongosh

# If not running, start it:
# macOS
brew services start mongodb-community

# Linux
sudo systemctl start mongodb

# Windows
net start MongoDB
```

### Issue: "Port 5000 already in use"
**Solution:**
Change the PORT in `.env` to another port (e.g., 5001)

### Issue: "CORS error in browser"
**Solution:**
Make sure `FRONTEND_URL` in backend `.env` matches your frontend URL

### Issue: "Cannot find module"
**Solution:**
```bash
cd backend
rm -rf node_modules package-lock.json
npm install
```

### Issue: "Login not working"
**Solution:**
1. Check backend is running (http://localhost:5000/api/health)
2. Check browser console for errors
3. Verify API_URL in login.html and dashboard.html matches backend URL
4. Clear browser localStorage and try again

## 🔄 Resetting the Database

If you need to start fresh:

```bash
cd backend
npm run seed
```

This will:
- Clear all existing data
- Create fresh sample data
- Reset all demo accounts

## 📊 Verifying Installation

### 1. Check Backend Health
Visit: `http://localhost:5000/api/health`

You should see:
```json
{
  "status": "ok",
  "timestamp": "2025-10-01T18:11:42.000Z",
  "environment": "development"
}
```

### 2. Check Database
```bash
mongosh
use esirom-hub
db.users.countDocuments()
```

Should return: `4` (the number of seeded users)

### 3. Check Frontend
Visit: `http://localhost:3000/login.html`

You should see the login page with demo credentials displayed

## 🎨 Customization

### Change Company Branding
Edit `dashboard.html` and `login.html`:
- Update logo SVG
- Change color scheme (search for `indigo` and replace)
- Modify company name

### Add New Client
1. Login as admin
2. Use API or add directly to database:
```javascript
// In MongoDB shell
db.clients.insertOne({
  name: "New Client Name",
  brandName: "Brand Name",
  contactEmail: "client@example.com",
  isActive: true,
  onboardingDate: new Date()
})
```

### Create New User
Use the register endpoint or add via admin panel (when implemented)

## 📈 Next Steps

1. **Configure Social Media APIs**
   - Get API credentials from each platform
   - Add to `.env` file
   - Test connections

2. **Customize Dashboard**
   - Modify widgets based on client needs
   - Add custom metrics
   - Configure KPIs

3. **Set Up Production**
   - Deploy backend to cloud service
   - Use MongoDB Atlas for database
   - Deploy frontend to subdomain
   - Enable HTTPS

4. **Add Real Data**
   - Connect social media accounts
   - Import historical data
   - Set up automated data fetching

## 🆘 Getting Help

### Check Logs
**Backend logs:**
The terminal where you ran `npm run dev` will show all API requests and errors

**Frontend logs:**
Open browser Developer Tools (F12) → Console tab

### Test API Directly
Use a tool like Postman or curl:

```bash
# Test login
curl -X POST http://localhost:5000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@esirom.com","password":"admin123"}'

# Test protected endpoint (replace TOKEN with actual token)
curl http://localhost:5000/api/auth/me \
  -H "Authorization: Bearer TOKEN"
```

### Database Inspection
```bash
mongosh
use esirom-hub

# View all users
db.users.find().pretty()

# View all clients
db.clients.find().pretty()

# View all posts
db.posts.find().pretty()
```

## ✅ Setup Checklist

- [ ] MongoDB installed and running
- [ ] Backend dependencies installed
- [ ] `.env` file created and configured
- [ ] Database seeded with sample data
- [ ] Backend server running on port 5000
- [ ] Frontend server running on port 3000
- [ ] Can access login page
- [ ] Can login with demo credentials
- [ ] Dashboard loads with data
- [ ] Charts are rendering
- [ ] Can switch between views
- [ ] Can logout successfully

## 🎉 Success!

If you've completed all the steps above, your Esirom Client Hub is now running!

You can now:
- Explore the dashboard
- Test different user roles
- Review the code structure
- Start customizing for your needs

For detailed documentation, see `README.md`

---

**Need more help?** Contact support@esirom.com
