# Changelog

All notable changes to the Esirom Agency Hub will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.4.0] - 2025-10-02

### Added
- **Automated KPI Tracking System**
  - Auto-tracked KPI types: Total Reach, Total Engagement, Total Impressions, Engagement Rate
  - Platform-specific auto-tracking: Per-platform Reach and Engagement
  - Manual KPI types: Followers and Custom metrics
  - Real-time data synchronization from dashboard metrics
  - Automatic progress calculation based on current vs target values
- **Bulk Goal Creation Feature**
  - "Bulk Add by Platform" modal for quick multi-platform setup
  - Select metric type and set targets for multiple platforms at once
  - Live preview of goals to be created
  - Support for Reach, Engagement, and Followers across all platforms
- **Database Storage for KPIs**
  - Migrated KPI goals from localStorage to MongoDB database
  - New API endpoint: `PUT /api/clients/:id/dashboard-config`
  - Persistent storage across sessions and devices
  - Platform selection now saved to database
  - Monthly post targets and client start dates stored in database
- **Enhanced Client Model**
  - Added `dashboardConfig` schema with platforms, kpiGoals, monthlyPostTarget, clientStartDate
  - Structured KPI goal objects with full metadata

### Changed
- KPI Update Modal now shows auto-tracked vs manual KPIs with visual indicators
- Auto-tracked KPIs display as read-only with green "Auto-tracked" badges
- Dashboard loads configuration from database first, falls back to localStorage
- Admin panel loads and saves client configuration via API
- Improved KPI form with better UX and auto-generated goal names

### Fixed
- **Multiple Goals Display Issue** - Fixed Alpine.js key binding causing goals not to render
- **Platform Selection Persistence** - Platforms now properly persist after cache clear
- **Progress Calculation** - Added null/undefined safeguards for progress values
- **Data Migration** - Automatic migration of old KPIs to new format with isAutoTracked flag

### Technical Improvements
- Enhanced error handling with try-catch blocks and fallbacks
- Comprehensive console logging for debugging KPI data flow
- Optimized data loading sequence with proper async/await
- Added data validation and type checking for KPI objects

## [1.3.0] - 2025-10-02

### Added
- Dynamic What's New section on login page
- Automatic changelog tracking system
- Version-controlled updates.json file
- Documentation for update process

### Changed
- What's New section now loads from updates.json instead of localStorage
- Version number automatically updates from latest entry

## [1.2.0] - 2025-10-01

### Added
- Admin Panel with complete user and client management
- Multi-brand support for client users
- Brand representative assignment system
- Platform updates management in admin panel
- Client dashboard customization per client
- KPI goals by year setup
- Platform selection for each client
- Widget visibility controls

### Fixed
- Critical bug in functions.php (wrong function name in add_action hook)
- Backend port configuration (moved from 5000 to 5001 to avoid macOS ControlCenter conflict)
- API URL injection into WordPress pages

## [1.1.0] - 2025-10-01

### Added
- Comprehensive KPI Management System with platform-specific goals
- Progress tracking with visual indicators
- Quick update modal for admins and brand reps
- Automatic progress calculation
- Client view-only access for KPIs
- Client selector dropdown for admins and brand reps
- Dashboard updates based on selected client
- Theme system with light/dark mode toggle
- Persistent theme selection across sessions

### Changed
- Enhanced dashboard with real-time KPI updates
- Improved user role permissions

## [1.0.0] - 2025-10-01

### Added
- Initial release of Esirom Agency Hub
- Beautiful login page with two-column layout
- JWT-based authentication system
- Dashboard with comprehensive reporting
- Monthly insights summary (Key Findings, Progress vs Last Month, Top Content)
- Annual goals progress with platform logos (Facebook, Instagram, LinkedIn, YouTube, X, TikTok)
- KPI tracking with color-coded progress bars
- Audience demographics (Age/Gender charts, Cities, Countries)
- Advertising insights & spend tracking (Total Spend, Reach, Engagement, Clicks, Impressions, ROAS)
- Platform breakdown charts
- Dark mode support throughout the application
- User roles & permissions (Admin, Brand Rep, Client)
- Backend API with Node.js/Express
- MongoDB database integration
- Social media API integration structure
- Database seeding script with demo data
- Complete WordPress theme integration
- Responsive design for all devices
- Professional UI with modern design

### Security
- JWT-based authentication
- Role-based access control
- Protected API endpoints
- Client-specific data isolation
- Session management with localStorage

---

## Version History Summary

- **v1.4.0** - Automated KPI tracking, bulk goal creation, database storage for KPIs
- **v1.3.0** - Dynamic changelog and What's New system
- **v1.2.0** - Admin panel, multi-brand support, platform updates management
- **v1.1.0** - KPI management, client selector, theme system
- **v1.0.0** - Initial release with dashboard, authentication, and reporting

---

## How to Add Updates

When making changes to the system:

1. Update this CHANGELOG.md file with the new version and changes
2. Update the `updates.json` file with user-friendly update description
3. Update version in `backend/package.json` if applicable
4. Update version in `manifest.json` if applicable

See `UPDATE_PROCESS.md` for detailed instructions.
