# Production Deployment Guide - Esirom Client Hub

This guide will walk you through deploying the Esirom Client Hub to production using:
- **GitHub** - Code repository
- **Railway** - Backend API hosting
- **MongoDB Atlas** - Cloud database
- **Your WordPress Site** - Frontend (already hosted)

---

## 📋 Prerequisites

- [ ] GitHub account
- [ ] Railway account (sign up at https://railway.app)
- [ ] MongoDB Atlas account (sign up at https://www.mongodb.com/cloud/atlas)
- [ ] Your WordPress site is accessible online

---

## Step 1: Set Up MongoDB Atlas

### 1.1 Create a New Cluster

1. Go to https://www.mongodb.com/cloud/atlas
2. Sign in or create an account
3. Click **"Build a Database"**
4. Choose **"M0 Free"** tier
5. Select a cloud provider and region (choose one close to your users)
6. Click **"Create Cluster"**

### 1.2 Create Database User

1. Go to **Database Access** (left sidebar)
2. Click **"Add New Database User"**
3. Choose **"Password"** authentication
4. Username: `esirom-admin` (or your choice)
5. Click **"Autogenerate Secure Password"** - **SAVE THIS PASSWORD!**
6. Database User Privileges: **"Atlas admin"**
7. Click **"Add User"**

### 1.3 Configure Network Access

1. Go to **Network Access** (left sidebar)
2. Click **"Add IP Address"**
3. Click **"Allow Access from Anywhere"** (0.0.0.0/0)
   - Note: For production, you should restrict this to Railway's IP ranges
4. Click **"Confirm"**

### 1.4 Get Connection String

1. Go to **Database** → **Connect**
2. Choose **"Connect your application"**
3. Driver: **Node.js**, Version: **4.1 or later**
4. Copy the connection string - it looks like:
   ```
   mongodb+srv://esirom-admin:<password>@cluster0.xxxxx.mongodb.net/?retryWrites=true&w=majority
   ```
5. Replace `<password>` with the password you saved earlier
6. Add the database name before the `?`:
   ```
   mongodb+srv://esirom-admin:YOUR_PASSWORD@cluster0.xxxxx.mongodb.net/esirom-hub?retryWrites=true&w=majority
   ```
7. **SAVE THIS CONNECTION STRING!**

---

## Step 2: Prepare Backend for GitHub

### 2.1 Create Railway Configuration

Create a new file in the backend directory:

**File:** `backend/railway.json`
```json
{
  "$schema": "https://railway.app/railway.schema.json",
  "build": {
    "builder": "NIXPACKS"
  },
  "deploy": {
    "startCommand": "npm start",
    "restartPolicyType": "ON_FAILURE",
    "restartPolicyMaxRetries": 10
  }
}
```

### 2.2 Update package.json

Make sure your `backend/package.json` has the correct start script:
```json
{
  "scripts": {
    "start": "node server.js",
    "dev": "nodemon server.js",
    "seed": "node scripts/seedDatabase.js"
  }
}
```

### 2.3 Create Production Environment Template

Create `backend/.env.production.example`:
```env
# Server Configuration
PORT=5001
NODE_ENV=production

# Database - MongoDB Atlas
MONGODB_URI=mongodb+srv://username:password@cluster.mongodb.net/esirom-hub?retryWrites=true&w=majority

# JWT Secret (Generate a new one for production!)
JWT_SECRET=your_production_jwt_secret_here
JWT_EXPIRE=7d

# Social Media API Keys (Optional)
META_APP_ID=
META_APP_SECRET=
YOUTUBE_API_KEY=
LINKEDIN_CLIENT_ID=
LINKEDIN_CLIENT_SECRET=
X_API_KEY=
X_API_SECRET=

# Frontend URL (Your WordPress site)
FRONTEND_URL=https://yourdomain.com
```

---

## Step 3: Push to GitHub

### 3.1 Initialize Git Repository (Backend Only)

```bash
cd /Users/esirom-director/Local\ Sites/esirom-hub/app/public/wp-content/themes/esiromhub/backend

# Initialize git if not already done
git init

# Add all files
git add .

# Commit
git commit -m "Initial commit - Esirom Client Hub Backend"
```

### 3.2 Create GitHub Repository

1. Go to https://github.com
2. Click **"New repository"**
3. Repository name: `esirom-client-hub-backend`
4. Description: "Backend API for Esirom Social Media Client Hub"
5. Choose **Private** (recommended)
6. **DO NOT** initialize with README, .gitignore, or license
7. Click **"Create repository"**

### 3.3 Push to GitHub

```bash
# Add remote (replace YOUR_USERNAME with your GitHub username)
git remote add origin https://github.com/YOUR_USERNAME/esirom-client-hub-backend.git

# Push to GitHub
git branch -M main
git push -u origin main
```

---

## Step 4: Deploy to Railway

### 4.1 Create New Project

1. Go to https://railway.app
2. Sign in with GitHub
3. Click **"New Project"**
4. Choose **"Deploy from GitHub repo"**
5. Select your `esirom-client-hub-backend` repository
6. Click **"Deploy Now"**

### 4.2 Configure Environment Variables

1. In Railway, click on your deployed service
2. Go to **"Variables"** tab
3. Click **"New Variable"** and add these one by one:

```
NODE_ENV=production
PORT=5001
MONGODB_URI=mongodb+srv://esirom-admin:YOUR_PASSWORD@cluster0.xxxxx.mongodb.net/esirom-hub?retryWrites=true&w=majority
JWT_SECRET=your_production_jwt_secret_here
JWT_EXPIRE=7d
FRONTEND_URL=https://yourdomain.com
```

**Important:** 
- Replace `MONGODB_URI` with your actual MongoDB Atlas connection string
- Generate a new JWT secret for production:
  ```bash
  node -e "console.log(require('crypto').randomBytes(64).toString('hex'))"
  ```
- Replace `FRONTEND_URL` with your actual WordPress site URL

### 4.3 Get Railway URL

1. Go to **"Settings"** tab
2. Scroll to **"Domains"**
3. Click **"Generate Domain"**
4. Copy the generated URL (e.g., `https://esirom-client-hub-backend-production.up.railway.app`)
5. **SAVE THIS URL!**

### 4.4 Verify Deployment

```bash
# Test health endpoint (replace with your Railway URL)
curl https://your-app.up.railway.app/api/health
```

You should see:
```json
{
  "status": "ok",
  "timestamp": "2025-10-02T...",
  "environment": "production"
}
```

---

## Step 5: Seed Production Database

### 5.1 Update Seed Script for Production

You can seed the database from your local machine:

```bash
cd /Users/esirom-director/Local\ Sites/esirom-hub/app/public/wp-content/themes/esiromhub/backend

# Create a temporary production .env
cat > .env.production << EOF
MONGODB_URI=mongodb+srv://esirom-admin:YOUR_PASSWORD@cluster0.xxxxx.mongodb.net/esirom-hub?retryWrites=true&w=majority
EOF

# Run seed with production env
NODE_ENV=production node scripts/seedDatabase.js
```

**OR** use Railway's CLI:

```bash
# Install Railway CLI
npm install -g @railway/cli

# Login
railway login

# Link to your project
railway link

# Run seed command
railway run npm run seed
```

---

## Step 6: Update WordPress Configuration

### 6.1 Update API URL in WordPress

1. Go to **WordPress Admin** → **Appearance** → **Hub Settings**
2. Update API URL to your Railway URL:
   ```
   https://your-app.up.railway.app/api
   ```
3. Click **"Save Settings"**

### 6.2 Update CORS in Backend (if needed)

If you get CORS errors, update the `FRONTEND_URL` environment variable in Railway to match your WordPress site URL exactly.

---

## Step 7: Test Production Deployment

### 7.1 Test Login

1. Visit your WordPress login page
2. Try logging in with:
   - Email: `admin@esirom.com`
   - Password: `admin123`
3. You should be redirected to the dashboard

### 7.2 Check Browser Console

1. Press F12 to open DevTools
2. Go to Console tab
3. Verify API_URL points to your Railway URL
4. Check for any errors

### 7.3 Test API Endpoints

```bash
# Replace with your Railway URL

# Test health
curl https://your-app.up.railway.app/api/health

# Test login
curl -X POST https://your-app.up.railway.app/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@esirom.com","password":"admin123"}'
```

---

## 🔒 Security Checklist

- [ ] Changed default passwords for all users
- [ ] Generated strong JWT secret for production
- [ ] FRONTEND_URL matches your WordPress site exactly
- [ ] MongoDB Atlas network access is configured
- [ ] GitHub repository is private
- [ ] Environment variables are set in Railway (not in code)
- [ ] HTTPS is enabled (Railway provides this automatically)

---

## 🔄 Continuous Deployment

Railway automatically deploys when you push to GitHub:

```bash
cd backend
git add .
git commit -m "Update feature"
git push origin main
```

Railway will automatically:
1. Pull the latest code
2. Build the application
3. Deploy the new version
4. Zero-downtime deployment

---

## 📊 Monitoring

### Railway Dashboard

- **Logs:** View real-time logs in Railway dashboard
- **Metrics:** CPU, Memory, Network usage
- **Deployments:** History of all deployments

### MongoDB Atlas

- **Metrics:** Database performance
- **Alerts:** Set up alerts for issues
- **Backup:** Automatic backups (on paid tiers)

---

## 🆘 Troubleshooting

### Issue: "Cannot connect to database"
- Check MongoDB Atlas connection string is correct
- Verify network access allows Railway's IPs
- Check database user credentials

### Issue: "CORS errors"
- Verify `FRONTEND_URL` in Railway matches your WordPress URL exactly
- Include protocol (https://) and no trailing slash

### Issue: "Login fails in production"
- Check Railway logs for errors
- Verify database is seeded
- Test API endpoints directly with curl

### Issue: "Railway deployment fails"
- Check build logs in Railway dashboard
- Verify package.json has correct scripts
- Ensure all dependencies are in package.json

---

## 📝 Environment Variables Reference

| Variable | Development | Production |
|----------|-------------|------------|
| `NODE_ENV` | development | production |
| `PORT` | 5001 | 5001 |
| `MONGODB_URI` | localhost | MongoDB Atlas |
| `JWT_SECRET` | dev secret | Strong random string |
| `FRONTEND_URL` | http://localhost:3000 | https://yourdomain.com |

---

## 🎉 Success!

If everything is working:
- ✅ Backend is deployed on Railway
- ✅ Database is on MongoDB Atlas
- ✅ WordPress connects to production API
- ✅ Users can login and use the hub

**Your Esirom Client Hub is now live in production!** 🚀
