# Changelog

All notable changes to the Esirom Agency Hub will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.3.78] - 2026-10-30

### Fixed
- AI Studio: a failed generation now shows a plain-English, wrapped error with the time of the attempt and a "Try again" button. Raw provider messages (e.g. "credits depleted") no longer linger looking like a live status.

## [2.3.77] - 2026-10-30

### Fixed
- AI Studio: on mobile the freestyle note no longer covers the bottom of the feed. It now sits inside the prompt bar with shorter mobile wording, and the feed scrolls clear of the taller bar.

## [2.3.76] - 2026-10-30

### Added
- AI Studio Freestyle mode (staff and admins only): generate with draft or no brand info, with or without a product. Capped at 3 finished pieces per person per month (admins can change each person's cap in Usage & budgets). Own counter ring in the header. Outputs are labelled "Freestyle, not client-approved info" in the feed, logs and review.
- "Add to Content Bank" on finished image and caption pairs (brand reps and admins). Creates a Content Bank item pending client approval. Nothing is posted or emailed; clients get an in-portal notification only.

## [2.3.75] - 2026-10-30
### Changed
- AI Studio Create is now a conversational canvas. Each generation appears as a message in a scrollable feed: your prompt, then 1-3 previews side by side.
- Choosing a preview reveals 3 captions straight underneath. "Send to review" sits on the final image and caption pair.
- A sticky prompt bar has pill pickers for Company › Brand › Product (client-approved only), Format, Position, Previews and Scene template, with a spark Generate button inside the input. Press Enter to generate and Shift+Enter for a new line.
- If no client-approved brand and product is selected, the input is disabled and its placeholder reads "Select a client-approved brand to begin".
- The budget cards are replaced by compact header indicators: image and daily-request rings, previews left, and estimated cost.
- Shimmer skeletons show while images and captions are generated. The brand's recent generations reload from the log, so you can scroll back.
- Backend endpoints, caps, the product check and approval rules are unchanged.

## [2.3.74] - 2026-10-30
### Added
- Messages is now a unified inbox: Facebook and Instagram post comments sit alongside Messenger and Instagram DMs.
- Comments show the post thumbnail, caption and link, plus the reply thread.
- Reply to a comment publicly on the post or privately by DM (Meta allows one private reply per comment, within 7 days).
- Hide, unhide or delete comments when the brand's Meta permissions allow it.
- Mark any item done (it reopens if new activity arrives) or assign it to a team member.
- Filter by channel, type (DMs or comments), open or done, unanswered, and assignee.
- AI suggested replies now work on comments too, under the same rules: never sent automatically, sensitive items flagged and confirmed, every reply logged.
- A banner lists any Meta permissions missing from the brand's Page token.

## [2.3.73] - 2026-10-30
### Added
- Inbox: "Suggest replies" gives 3 AI reply options for a DM, using the client-approved brand profile and voice plus replies your team actually sent. Staff click one to put it in the reply box, edit, then press Send — nothing is sent automatically.
- Sensitive messages (complaints, refunds, legal, medical) are flagged for a person; suggestions become holding replies and Send needs a "checked by a person" tick.
- AI Studio usage tab shows reply suggestions separately from the image allowance.
- Same feature flag as AI Studio (admins only for now).

## [2.3.72] - 2026-10-30

### Added
- **AI Studio brand knowledge hierarchy**: Company → Brands → Products/SKUs (name, SKU code, description, key claims, sizes/variants, cut-out images). Every change is versioned with history and an activity log. Status: draft → awaiting client approval → client approved; images and captions use only client-approved versions, and unapproved items show an "Awaiting client approval" badge.
- **Brand info page** (`/brand-info/`) for clients to review, approve, request changes or edit their own company's details (client edits are approved on save). Hidden from clients until the backend switch `AI_KNOWLEDGE_CLIENT_ACCESS=on`.
- AI Studio now picks company → brand → product; the image allowance stays per company. Old single brand-profile and product tabs are replaced by the Brand knowledge tab.

## [2.3.71] - 2026-10-30

### Added
- **AI Studio (beta, admins first)** at `/ai-studio/`: product-safe AI scenes (Gemini, 2K) with the real product cut-out composited on top and a pixel check that blocks altered products; on-voice caption suggestions (3 options) learned from approved Content Bank captions and top-performing synced posts, with human approval; brand profiles (company, products, voice, do/don't words, hashtags, emoji use) editable by staff; product library; monthly image allowance from the client's posts-per-month allocation, admin top-up and pause, per-person daily cap; admin usage and estimated-cost dashboard. Finished work is sent to Workflow for review; nothing is posted automatically. Needs backend `feat/ai-studio` and `GEMINI_API_KEY`.

## [2.3.66] - 2026-10-29

### Fixed
- **My Progress / Agency Overview accuracy** (backend `fix/accurate-progress-metrics-carousels`): Posts Published and Monthly Posts count each piece of content once (carousel/album = 1 post, same caption on FB + IG the same Jamaica day = 1, stories excluded, only published posts), in Jamaica calendar months instead of UTC; on-time handoffs use the end of the due day in Jamaica (was 6:59 pm) and the first design upload when there is no submit stamp; monthly reports finalised/sent/uploaded by anyone count for the brand; Team Efficiency uses due-this-month and handed-off, like My Progress, and no longer drops people with no pending work

### Added
- Carousel badge ("Carousel · N slides") and a posts / singles / reels / carousels summary on the brand Published Posts view; Monthly Posts cards show the same breakdown plus FB + IG cross-posts
- Theme version bumped to 2.3.66

## [2.3.65] - 2026-10-22

### Added
- **Disable / re-enable clients** (Admin Panel → Clients). A disabled client keeps all of its data but its users cannot sign in and any existing sessions or tokens stop working on their next request. The brand is hidden from brand pickers and client lists; admins see it under the new **Active / Disabled / All** filter with a red **Disabled** badge and a "Disabled *date* by *admin*" audit line. A confirmation dialog (bottom sheet on phones) lists the client accounts that will lose access before anything changes; **Re-enable client** restores access. Staff accounts are never affected
- Sign-in page shows "This account has been disabled. Please contact Esirom." when a disabled client signs in, when a stored session is resumed, when a session is terminated mid-use and on the Google sign-in callback (`?error=account_disabled`)
- `HubAuth` gained `isClientActive` / `activeClients`, `isDisabledResponse`, `clearSession`, `redirectToLogin`, `consumeSignOutReason` and a `fetch` guard (installed by `inc/hub-head.php` on every app page) that signs the user out as soon as the API answers `403 ACCOUNT_DISABLED`
- Users tab shows a **Disabled** badge for client accounts whose brands are all disabled; brand pickers in the user form, approval modal and import tools list active brands only (plus any disabled brand that is already assigned, labelled)
- `DISABLE_CLIENT_BACKEND.md` documents the backend contract (`PUT /clients/:id/status`, audit fields, lockout in login and auth middleware, default list filtering)

### Changed
- Brand lists on Workflow, Insights, Agency Overview, Content Calendar, Messages, Ads, Inventory and Website Projects drop `isActive: false` brands client-side as a safety net; the API remains the source of truth
- Theme version bumped to 2.3.65

## [2.3.64] - 2026-10-14

### Changed
- The "Viewing as" role switcher (eye icon in the Workflow header, and on Insights and Agency Overview) is only shown to super admins. Super admin = `admin` role from `/auth/me` plus either a backend `isSuperAdmin: true` flag or an email on the new **Hub Settings → Super admin emails** list (default `alex@esirom.com`)
- New `js/hub-auth.js` (`HubAuth`) is the single place that derives the allowed view mode from the verified role; every page, the shared mobile nav and the client nav bootstrap use it
- Theme version bumped to 2.3.64

### Fixed
- Brand reps, clients and regular admins could land in a different view through a stored `viewMode`, and the switcher briefly rendered before Alpine initialised; the view mode is now re-derived from the backend role on every page load, `switchViewMode()` refuses non-super-admins (including console calls), the switcher is `x-cloak`ed, and visibility is decided on the verified `/auth/me` payload rather than the reactive user object
- Workflow Concepts on phones (375–430px): the search / All Status / All Brands toolbar overflowed sideways and the page scrolled horizontally; filters now stack and the app main area never pans sideways
- Workflow concept detail on phones: the 192px media thumbnail, status badge and close button pushed past the right edge and the title was squeezed; the detail now opens as a full-height sheet with the title, close button and status first, media scaled to the panel width, a 2-column action grid and shrinkable link inputs

## [2.3.63] - 2026-10-13

### Added
- Shared design system (`css/hub-ui.css`) with brand tokens, type scale, buttons, cards, forms, badges, tables, navigation, modal, toast and empty/loading/error components
- Shared `<head>` partial (`inc/hub-head.php`) providing fonts, Tailwind brand config, dark-mode bootstrap and the API URL for every template
- Shared account menu (`inc/hub-account-menu.php`) and toast stack (`inc/hub-toasts.php`); Change password is now available on every page
- Mobile headers with brand selector / view switcher on Insights, Workflow, Agency Overview and Content Calendar; mobile layout and bottom navigation for the Admin Panel
- Loading skeletons, empty states and error states for Insights, Reports, Event Coverage and Published Posts

### Changed
- Redesigned login, registration, password reset, 404, offline and fallback pages (logo now on a legible white card; split brand/form layout on desktop)
- Sidebar and mobile navigation restyled with consistent active states, grouped sections and SVG icons; `aria-current`/`aria-expanded` added
- Dates and numbers formatted for en-GB; UK English spellings in UI copy
- Service worker is network-first for pages and versions its cache per release so GitDock deploys are picked up immediately
- Theme version bumped to 2.3.63

### Fixed
- Workflow: toasts and the change-password modal were rendered outside the Alpine root and never appeared; multi-root `x-if` templates dropped video overlays and carousel controls; duplicate `getDesignAttachments` ignored carousel slide order; mention markup was injected without escaping (`x-html`); concept form `required` on hidden inputs; unclosed page wrapper; missing rewrite rules for `/workflow/assets` and `/workflow/dashboard`
- Insights: `posts` undeclared at first render (console errors), reports/events/posts failures were silent, KPI "Save" could do nothing, UTC-shifted date parameters to GA/narratives, widget preferences never persisted for clients, auth check signed users out on any network error, division by zero in KPI modal, review modal bound to a null post, `[object Object]`/raw float values in cards, hash not kept in sync when switching views, global mobile CSS that forced 44px links and single-column grids
- Admin: unusable on mobile (sidebar always visible, no navigation); Facebook export import unreachable; CSV/JSON import blocked by the folder picker; "Setup guide" button did nothing; dashboard widgets and post-type breakdown were never saved; silent delete/load failures and no expired-session handling; stale "Add user/client" forms; media upload always returned 401 for Hub users, and `upload-media.php` accepted any token and unsanitised paths
- Ads / Inventory / Messages / Vault / Website Projects: a failed data load was shown as "You must be logged in"; `viewMode` changes were not persisted so the mobile nav could disagree with the page; vault edit could wipe a stored password and `<option x-for>` rendered nothing; null-safe bindings on Messages, My Progress and Agency Overview
- functions.php: Tailwind CDN script enqueued as a stylesheet, duplicate `ESIROM_API_URL` injection, mismatched default API URLs, redirects via `get_permalink(get_page_by_path())` that resolved to the current page when a slug was missing, duplicate `<title>` from `title-tag`
- Removed dead files: `hub.html`, `login.html`, `js/role-switcher.js`, `inc/hub-staff-mobile-nav.php`, `inc/hub-client-mobile-nav.php`

## [1.9.0] - 2026-06-01

### Added
- Google Sign-In on the login page (staff @esirom.com, client email matching, new client registration via Google)
- OAuth callback handling on login page (`token`, `error`, `success` query params)

### Changed
- Login page layout: two-column design, Google button as primary sign-in, logo in card header
- Theme version bumped to 1.9.0; What's New entry for Google sign-in

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
