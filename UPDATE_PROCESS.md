# Update Process for Esirom Agency Hub

This document outlines the process for tracking and publishing updates to the Esirom Agency Hub platform.

## 📋 Overview

The platform now features a **dynamic What's New system** that automatically displays the latest updates on the login page. Updates are tracked in two places:

1. **CHANGELOG.md** - Detailed technical changelog following Keep a Changelog format
2. **updates.json** - User-friendly updates displayed on the login page

## 🔄 When to Create an Update

Create a new update entry when you:

- Add a new feature or functionality
- Make significant improvements to existing features
- Fix critical bugs that affect user experience
- Release a new version of the platform
- Make changes that users should be aware of

## 📝 How to Add an Update

### Step 1: Determine Version Number

Follow [Semantic Versioning](https://semver.org/):

- **MAJOR (X.0.0)**: Breaking changes or complete redesigns
  - Example: 1.0.0 → 2.0.0
  
- **MINOR (1.X.0)**: New features, non-breaking changes
  - Example: 1.2.0 → 1.3.0
  
- **PATCH (1.2.X)**: Bug fixes, minor improvements
  - Example: 1.2.0 → 1.2.1

### Step 2: Update CHANGELOG.md

Add a new entry at the **top** of the changelog (after the header):

```markdown
## [1.4.0] - 2025-10-15

### Added
- New content calendar feature with drag-and-drop functionality
- Bulk post scheduling capability
- Export reports to PDF

### Changed
- Improved dashboard loading speed by 40%
- Enhanced mobile responsiveness

### Fixed
- Resolved issue with KPI calculation for Instagram
- Fixed dark mode toggle persistence
```

**Categories to use:**
- **Added** - New features
- **Changed** - Changes to existing functionality
- **Deprecated** - Features that will be removed soon
- **Removed** - Features that have been removed
- **Fixed** - Bug fixes
- **Security** - Security improvements

### Step 3: Update updates.json

Add a new entry at the **beginning** of the array (index 0):

```json
[
  {
    "id": 5,
    "title": "Content Calendar & Scheduling",
    "version": "1.4.0",
    "description": "Introducing the new Content Calendar! Schedule posts with drag-and-drop, bulk schedule content, and export your reports to PDF for easy sharing with clients.",
    "date": "2025-10-15"
  },
  // ... existing entries
]
```

**Guidelines for updates.json:**
- **id**: Increment from the previous highest ID
- **title**: Short, catchy title (3-6 words)
- **version**: Must match the version in CHANGELOG.md
- **description**: User-friendly description (1-2 sentences, focus on benefits)
- **date**: Release date in YYYY-MM-DD format

### Step 4: Update Version Numbers

Update the version in these files:

1. **backend/package.json**
   ```json
   {
     "version": "1.4.0"
   }
   ```

2. **manifest.json**
   ```json
   {
     "version": "1.4.0"
   }
   ```

### Step 5: Verify Changes

1. **Check the login page:**
   - Visit the login page
   - Verify the version number shows the latest version
   - Confirm the new update appears at the top of the What's New section

2. **Check console for errors:**
   - Open browser DevTools (F12)
   - Look for any JavaScript errors
   - Verify updates.json loads successfully

## 🎯 Quick Reference

### Files to Update

| File | What to Update | Format |
|------|---------------|--------|
| `CHANGELOG.md` | Detailed technical changes | Markdown |
| `updates.json` | User-friendly update description | JSON |
| `backend/package.json` | Version number | JSON |
| `manifest.json` | Version number | JSON |

### Version Numbering Examples

| Change Type | Example | New Version |
|-------------|---------|-------------|
| New major feature | Content Calendar added | 1.3.0 → 1.4.0 |
| Bug fix | Fixed login issue | 1.3.0 → 1.3.1 |
| Multiple features | Calendar + Reports + Analytics | 1.3.0 → 1.4.0 |
| Breaking change | Complete UI redesign | 1.3.0 → 2.0.0 |

## 📊 Current Update System

### How It Works

1. **Login page loads** → Fetches `updates.json`
2. **Displays latest 5 updates** in the What's New section
3. **Shows current version** from the first entry in updates.json
4. **Updates automatically** when you modify updates.json

### Storage

- ✅ **No localStorage** - Updates load from JSON file
- ✅ **No database** - Simple file-based system
- ✅ **Automatic** - Changes reflect immediately after file update
- ✅ **Version controlled** - Can track in Git

## 🔧 Troubleshooting

### Updates not showing on login page?

1. **Clear browser cache:**
   ```
   Ctrl/Cmd + Shift + R (hard refresh)
   ```

2. **Check JSON syntax:**
   - Use a JSON validator (jsonlint.com)
   - Ensure proper comma placement
   - Verify all quotes are double quotes

3. **Check file path:**
   - Verify `updates.json` is in theme root directory
   - Check file permissions (should be readable)

### Version number not updating?

1. **Check updates.json:**
   - Ensure the first entry has the correct version
   - Verify JSON is valid

2. **Check browser console:**
   - Look for fetch errors
   - Verify file is loading

## 📅 Update Schedule Recommendations

### Regular Updates
- **Weekly**: Minor improvements, bug fixes (patch versions)
- **Monthly**: New features, enhancements (minor versions)
- **Quarterly**: Major features, redesigns (major versions)

### Best Practices

✅ **DO:**
- Write clear, user-friendly descriptions
- Focus on benefits, not technical details
- Keep descriptions concise (1-2 sentences)
- Use consistent date format (YYYY-MM-DD)
- Test updates on login page before committing

❌ **DON'T:**
- Use technical jargon in updates.json
- Skip version number updates
- Add too many updates at once (max 5 visible)
- Forget to update CHANGELOG.md

## 🎨 Writing Great Update Descriptions

### Good Examples

✅ **"Multi-Brand Support"**
> "Clients can now manage multiple brands from a single account! Switch seamlessly between brands and view consolidated analytics across all your properties."

✅ **"Dark Mode"**
> "Easy on the eyes! Toggle between light and dark modes for comfortable viewing any time of day. Your preference is saved automatically."

### Bad Examples

❌ **Too Technical:**
> "Implemented JWT-based authentication with role-based access control using MongoDB aggregation pipelines."

❌ **Too Vague:**
> "Various improvements and bug fixes."

❌ **Too Long:**
> "We've completely redesigned the dashboard from the ground up with a new component architecture, improved state management, better performance optimization, and a whole new set of features including..."

## 🚀 Example: Complete Update Process

Let's say you just added a new Analytics Export feature:

### 1. Update CHANGELOG.md
```markdown
## [1.4.0] - 2025-10-15

### Added
- Export analytics reports to PDF and Excel formats
- Customizable date ranges for exports
- Email reports directly to clients
```

### 2. Update updates.json
```json
[
  {
    "id": 5,
    "title": "Export Your Analytics",
    "version": "1.4.0",
    "description": "Share insights with ease! Export your analytics reports to PDF or Excel, choose custom date ranges, and email reports directly to your clients.",
    "date": "2025-10-15"
  },
  // ... previous entries
]
```

### 3. Update package.json
```json
{
  "version": "1.4.0"
}
```

### 4. Update manifest.json
```json
{
  "version": "1.4.0"
}
```

### 5. Test
- Visit login page
- Verify "v1.4.0" shows in top right
- Confirm new update appears first in What's New section

## 📞 Questions?

If you have questions about the update process:
- Review this document
- Check CHANGELOG.md for examples
- Look at updates.json for formatting reference

---

**Last Updated:** October 2, 2025  
**Current Version:** 1.3.0  
**System:** Dynamic What's New with JSON-based updates
