# Changelog - Version 1.5.0
**Release Date:** January 23, 2026

## 🎉 New Features

### Instagram to Facebook Mirroring
- **Mirror Instagram posts to Facebook** in the content calendar
- Display Instagram content with Facebook branding
- **Separate KPI tracking** for Facebook when mirroring is enabled
- Facebook KPIs can be imported via CSV or entered manually
- Media files are not required for Facebook posts when mirroring

### Database Migration Tools
- **Migration API endpoint** (`/api/migrations/mirror-ig-to-fb`)
- **Migration status endpoint** (`/api/migrations/status`)
- **Node.js migration script** for command-line execution
- **HTML migration UI** for browser-based migration
- Comprehensive migration instructions and documentation

---

## 🐛 Bug Fixes

### Toast Notifications
- **Fixed visibility issues** - notifications now display fully on screen
- **Improved positioning** - proper spacing from screen edges
- **Fixed text overflow** - long messages now wrap correctly
- **Connected to Alpine.js data** - resolved scope issues

### Console Errors
- **Suppressed Tailwind CDN warning** for admin panel
- **Fixed mirrorIGToFB undefined error** in database queries
- **Optimized production environment** configuration

---

## 🔧 Improvements

### Backend API
- Added `/api/migrations` route for database migrations
- Enhanced Client model with `mirrorIGToFB` field
- Updated dashboard config endpoint to handle new field
- Improved error handling and logging

### Frontend
- Optimized Tailwind CSS configuration for admin panel
- Enhanced toast notification styling and animations
- Better responsive design for notifications
- Improved user feedback messages

### Database
- Added `mirrorIGToFB` field to Client schema
- Default value: `false`
- Backward compatible with existing clients
- Migration tools for updating existing records

---

## 📝 Files Changed

### Theme Files
- `page-admin.php` - Tailwind optimization, toast notification fixes
- `style.css` - Version bump to 1.5.0
- `updates.json` - Added v1.5.0 changelog entry

### Backend Files
- `models/Client.js` - Added mirrorIGToFB field
- `routes/clients.js` - Updated dashboard config endpoint
- `routes/migrations.js` - New migration endpoints
- `server.js` - Added migrations route
- `package.json` - Version bump to 1.5.0

### New Files
- `scripts/migrateMirrorIGToFB.js` - Node.js migration script
- `scripts/migration-ui.html` - Browser-based migration tool
- `scripts/MIGRATION_INSTRUCTIONS.md` - Migration documentation
- `CHANGELOG_v1.5.0.md` - This file

---

## 🚀 Deployment

### Git Commits
1. `5a5c898` - Merge remote changes and keep mirrorIGToFB feature
2. `821f7d5` - feat: Add migration tools for mirrorIGToFB field
3. `4d81611` - chore: Bump version to 1.5.0

### Railway Deployment
- ✅ Automatically deployed to production
- ✅ Migration endpoints available
- ✅ All tests passing

---

## 📊 Migration Status

### Database Migration
- **Total Clients**: 12
- **Migrated**: 12 (100%)
- **Status**: ✅ Complete

---

## 🎯 What's Next

### Recommended Actions
1. ✅ Verify toast notifications are working
2. ✅ Test Instagram to Facebook mirroring feature
3. ✅ Check console for any remaining errors
4. ✅ Update "What's New" section (completed)

### Future Enhancements
- Add bulk edit for client dashboard configurations
- Implement notification preferences
- Add export functionality for client data
- Enhanced reporting features

---

## 📚 Documentation

### Migration Instructions
See `scripts/MIGRATION_INSTRUCTIONS.md` for detailed migration steps.

### API Documentation
- **GET** `/api/migrations/status` - Check migration status
- **POST** `/api/migrations/mirror-ig-to-fb` - Run migration

### Feature Documentation
- Instagram to Facebook mirroring can be enabled per client
- Access via Admin Panel → Customize Client → Mirror Instagram to Facebook checkbox
- When enabled, Instagram posts will show both Instagram and Facebook icons

---

## 🙏 Credits

**Developed by:** Esirom Development Team  
**Version:** 1.5.0  
**Release Date:** January 23, 2026

---

## 📞 Support

For issues or questions:
- Check the console for error messages
- Review migration instructions
- Contact support team

---

**Happy Managing! 🚀**
