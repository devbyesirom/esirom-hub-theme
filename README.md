# Esirom Social Media Client Hub

A comprehensive, client-facing web application that centralizes social media reporting and content management. The platform provides a customizable and interactive experience for clients to review their social media performance, approve content, and track their marketing goals.

## 🚀 Features

### Core Functionality
- **Client Dashboard**: Secure, customizable portal for each client with key metrics and KPI tracking
- **Content Calendar**: Interactive calendar for planning and managing social media content across multiple platforms
- **Reporting System**: Flexible system for generating automated, manual, and hybrid reports
- **KPI Tracking**: Module for setting and tracking client-specific goals with visual progress indicators
- **Multi-Platform Support**: Integrations for Meta (Facebook & Instagram), YouTube, LinkedIn, and X (Twitter)

### User Roles
- **Admin**: Full access to the platform, can manage clients, users, and system settings
- **Brand Representative**: Can manage content calendar and reports for assigned clients
- **Client**: Can view their dashboard, reports, and content calendar; can approve or reject posts

### Security Features
- JWT-based authentication
- Role-based access control (RBAC)
- Secure password hashing with bcrypt
- Protected API endpoints
- Token expiration and refresh

## 📋 Prerequisites

- **Node.js** (v16 or higher)
- **MongoDB** (v5.0 or higher)
- **npm** or **yarn**

## 🛠️ Installation

### 1. Backend Setup

```bash
# Navigate to backend directory
cd backend

# Install dependencies
npm install

# Create environment file
cp .env.example .env

# Edit .env file with your configuration
nano .env
```

### 2. Configure Environment Variables

Edit the `.env` file with your settings:

```env
# Server Configuration
PORT=5000
NODE_ENV=development

# Database
MONGODB_URI=mongodb://localhost:27017/esirom-hub

# JWT Secret (Generate a strong random string)
JWT_SECRET=your_secure_jwt_secret_here
JWT_EXPIRE=7d

# Social Media API Keys (Optional - add when ready)
META_APP_ID=your_meta_app_id
META_APP_SECRET=your_meta_app_secret
YOUTUBE_API_KEY=your_youtube_api_key
LINKEDIN_CLIENT_ID=your_linkedin_client_id
LINKEDIN_CLIENT_SECRET=your_linkedin_client_secret
X_API_KEY=your_x_api_key
X_API_SECRET=your_x_api_secret

# Frontend URL
FRONTEND_URL=http://localhost:3000
```

### 3. Database Setup

Make sure MongoDB is running, then seed the database with sample data:

```bash
# Seed the database
npm run seed
```

This will create:
- Sample clients
- Admin, brand rep, and client users
- Sample posts and reports
- KPIs

### 4. Start the Backend Server

```bash
# Development mode (with auto-reload)
npm run dev

# Production mode
npm start
```

The backend API will be available at `http://localhost:5000`

### 5. Frontend Setup

The frontend is built with vanilla JavaScript and can be served with any static file server.

For development, you can use:

```bash
# Using Python
python -m http.server 3000

# Using Node.js http-server
npx http-server -p 3000

# Using PHP
php -S localhost:3000
```

Access the application at `http://localhost:3000/login.html`

## 🔑 Default Login Credentials

After seeding the database, use these credentials to log in:

### Admin Account
- **Email**: admin@esirom.com
- **Password**: admin123

### Brand Representative Account
- **Email**: brandrep@esirom.com
- **Password**: brandrep123

### Client Account (Partner's Heart & Health)
- **Email**: client@partnershealth.com
- **Password**: client123

### Client Account (Tech Innovators)
- **Email**: client@techinnovators.com
- **Password**: client123

## 📁 Project Structure

```
demo/
├── backend/
│   ├── models/              # Database models
│   │   ├── User.js
│   │   ├── Client.js
│   │   ├── Post.js
│   │   ├── Report.js
│   │   └── KPI.js
│   ├── routes/              # API routes
│   │   ├── auth.js
│   │   ├── users.js
│   │   ├── clients.js
│   │   ├── posts.js
│   │   ├── reports.js
│   │   ├── kpis.js
│   │   └── dashboard.js
│   ├── middleware/          # Custom middleware
│   │   └── auth.js
│   ├── services/            # Business logic & integrations
│   │   └── socialMedia/
│   │       ├── MetaService.js
│   │       ├── LinkedInService.js
│   │       ├── XService.js
│   │       ├── YouTubeService.js
│   │       └── index.js
│   ├── scripts/             # Utility scripts
│   │   └── seedDatabase.js
│   ├── server.js            # Express server
│   ├── package.json
│   └── .env.example
├── login.html               # Login page
├── dashboard.html           # Main dashboard
├── hub.html                 # Original static demo
├── Technical Brief_ Esirom Social Media Client Hub.md
└── README.md
```

## 🔌 API Endpoints

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login user
- `GET /api/auth/me` - Get current user
- `PUT /api/auth/update-password` - Update password

### Users (Admin only)
- `GET /api/users` - Get all users
- `GET /api/users/:id` - Get single user
- `PUT /api/users/:id` - Update user
- `DELETE /api/users/:id` - Delete user

### Clients
- `GET /api/clients` - Get all clients (filtered by role)
- `GET /api/clients/:id` - Get single client
- `POST /api/clients` - Create client (Admin only)
- `PUT /api/clients/:id` - Update client
- `DELETE /api/clients/:id` - Delete client (Admin only)

### Posts
- `GET /api/posts` - Get all posts (filtered by role)
- `GET /api/posts/:id` - Get single post
- `POST /api/posts` - Create post (Admin/Brand Rep)
- `PUT /api/posts/:id` - Update post
- `PUT /api/posts/:id/approve` - Approve post
- `PUT /api/posts/:id/request-revision` - Request revision
- `DELETE /api/posts/:id` - Delete post

### Reports
- `GET /api/reports` - Get all reports (filtered by role)
- `GET /api/reports/:id` - Get single report
- `POST /api/reports` - Create report
- `PUT /api/reports/:id` - Update report
- `POST /api/reports/:id/annotations` - Add annotation
- `DELETE /api/reports/:id` - Delete report

### KPIs
- `GET /api/kpis` - Get all KPIs (filtered by role)
- `GET /api/kpis/:id` - Get single KPI
- `POST /api/kpis` - Create KPI
- `PUT /api/kpis/:id` - Update KPI
- `POST /api/kpis/:id/update-value` - Update KPI value
- `DELETE /api/kpis/:id` - Delete KPI

### Dashboard
- `GET /api/dashboard/:clientId` - Get dashboard data for client

## 🔐 Authentication Flow

1. User submits credentials to `/api/auth/login`
2. Server validates credentials and returns JWT token
3. Frontend stores token in localStorage
4. All subsequent API requests include token in Authorization header: `Bearer <token>`
5. Backend middleware validates token and attaches user to request
6. Routes check user role and permissions before processing

## 🎨 Frontend Architecture

The frontend uses:
- **Tailwind CSS** for styling
- **Alpine.js** for reactive components
- **Chart.js** for data visualization
- **Vanilla JavaScript** for API communication

### Key Features
- Dark mode support
- Responsive design
- Real-time data updates
- Role-based UI rendering
- Collapsible sidebar

## 🔗 Social Media Integration

The platform includes service classes for integrating with social media APIs:

### Meta (Facebook & Instagram)
- Fetch page/account insights
- Get post performance
- Publish posts
- Track engagement metrics

### LinkedIn
- Get organization statistics
- Fetch post analytics
- Publish posts
- Track follower growth

### X (Twitter)
- Get tweet metrics
- Fetch user statistics
- Post tweets
- Aggregate engagement data

### YouTube
- Get channel statistics
- Fetch video analytics
- Track subscriber growth
- Monitor watch time

### Setup Instructions

1. Register your application with each platform
2. Obtain API credentials
3. Add credentials to `.env` file
4. Update client social media accounts in database
5. Services will automatically fetch data when credentials are available

## 🚀 Deployment

### Recommended Deployment Strategy

1. **Backend**: Deploy to a cloud platform (Heroku, DigitalOcean, AWS, etc.)
2. **Database**: Use MongoDB Atlas for managed database
3. **Frontend**: Deploy to subdomain (e.g., hub.esirom.com) using Siteground or similar

### Environment Setup for Production

```env
NODE_ENV=production
MONGODB_URI=mongodb+srv://username:password@cluster.mongodb.net/esirom-hub
JWT_SECRET=<strong-random-secret>
FRONTEND_URL=https://hub.esirom.com
```

### Security Checklist

- [ ] Change all default passwords
- [ ] Use strong JWT secret
- [ ] Enable HTTPS
- [ ] Set up CORS properly
- [ ] Use environment variables for all secrets
- [ ] Enable rate limiting
- [ ] Set up database backups
- [ ] Monitor error logs

## 📊 Database Schema

### Users Collection
- Authentication credentials
- Role assignment (admin, brand_rep, client)
- Client associations
- Profile information

### Clients Collection
- Company information
- Social media account connections
- Dashboard configuration
- Custom settings

### Posts Collection
- Content and media
- Platform assignment
- Status tracking (draft → pending → approved → scheduled → published)
- Performance metrics
- Approval workflow

### Reports Collection
- Date ranges
- Platform data
- Metrics aggregation
- Annotations
- PDF generation

### KPIs Collection
- Goal definitions
- Target values
- Progress tracking
- Historical data

## 🛠️ Development

### Adding a New Feature

1. Create database model (if needed) in `backend/models/`
2. Create API routes in `backend/routes/`
3. Add middleware/validation as needed
4. Update frontend to consume new endpoints
5. Test thoroughly with different user roles

### Testing Different User Roles

Use the seeded accounts to test:
- Admin: Full system access
- Brand Rep: Client management and content creation
- Client: View-only with approval capabilities

## 📝 Future Enhancements

- [ ] Real-time notifications
- [ ] Advanced analytics and insights
- [ ] AI-powered content suggestions
- [ ] Automated report generation
- [ ] Mobile app
- [ ] Multi-language support
- [ ] White-label capabilities
- [ ] Advanced scheduling with timezone support
- [ ] Content library and asset management
- [ ] Team collaboration features

## 🐛 Troubleshooting

### Backend won't start
- Check MongoDB is running
- Verify `.env` file exists and is configured
- Check port 5000 is not in use

### Can't log in
- Verify backend is running
- Check browser console for errors
- Ensure API_URL in frontend matches backend URL
- Try clearing localStorage

### Database connection fails
- Verify MongoDB is running: `mongod --version`
- Check MONGODB_URI in `.env`
- Ensure database user has proper permissions

### CORS errors
- Check FRONTEND_URL in backend `.env`
- Verify CORS middleware is configured
- Check browser console for specific error

## 📞 Support

For questions or issues:
- Email: support@esirom.com
- Documentation: See Technical Brief

## 📄 License

Copyright © 2025 Esirom. All rights reserved.

---

Built with ❤️ by Esirom
