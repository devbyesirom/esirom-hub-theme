/**
 * HubAuth — single source of truth for "who is this user really" and which
 * view mode they are allowed to be in.
 *
 * Loaded by inc/hub-head.php before Alpine so every page script can rely on it.
 *
 * View modes (`admin`, `brand_rep`, `client`) are a *preview* feature: only
 * super admins may switch away from their real role. For everyone else the
 * view mode is always derived from the role the backend reports via /auth/me,
 * regardless of what is in localStorage, the URL or the console.
 */
(function (window) {
    'use strict';

    var ROLES = ['admin', 'brand_rep', 'client'];
    var STORAGE_KEY = 'viewMode';

    // Last user payload verified against the backend (/auth/me). Kept in this
    // closure rather than on an Alpine component so it cannot be edited from
    // the console through `$data`/`Alpine.$data()`.
    var verifiedUser = null;

    function normaliseEmail(value) {
        return typeof value === 'string' ? value.trim().toLowerCase() : '';
    }

    function allowlist() {
        var list = window.ESIROM_SUPER_ADMIN_EMAILS;
        if (!Array.isArray(list)) return [];
        return list.map(normaliseEmail).filter(Boolean);
    }

    function pickUser(user) {
        return user === undefined ? verifiedUser : user;
    }

    /**
     * Remember the user object returned by /auth/me.
     */
    function setUser(user) {
        if (!user || typeof user !== 'object') {
            verifiedUser = null;
            return null;
        }
        try {
            verifiedUser = JSON.parse(JSON.stringify(user));
        } catch (e) {
            verifiedUser = null;
        }
        return verifiedUser;
    }

    function getUser() {
        return verifiedUser;
    }

    /**
     * Super admin = `admin` role from the backend AND (backend `isSuperAdmin`
     * flag OR email on the theme's super admin allowlist).
     */
    function isSuperAdmin(user) {
        var u = pickUser(user);
        if (!u || typeof u !== 'object') return false;
        if (u.role !== 'admin') return false;
        if (u.isSuperAdmin === true) return true;
        var email = normaliseEmail(u.email);
        return email !== '' && allowlist().indexOf(email) !== -1;
    }

    /**
     * The only view mode a non-super-admin may ever be in: their real role.
     */
    function roleViewMode(user) {
        var u = pickUser(user);
        var role = u && typeof u === 'object' ? u.role : '';
        return ROLES.indexOf(role) !== -1 ? role : 'admin';
    }

    function readStoredViewMode() {
        try {
            return localStorage.getItem(STORAGE_KEY) || 'admin';
        } catch (e) {
            return 'admin';
        }
    }

    /**
     * Resolve the view mode a user is allowed to be in given a requested mode
     * (from localStorage, the UI or anywhere else).
     */
    function resolveViewMode(requested, user) {
        var u = pickUser(user);
        if (!isSuperAdmin(u)) return roleViewMode(u);
        var mode = requested === undefined ? readStoredViewMode() : requested;
        return ROLES.indexOf(mode) !== -1 ? mode : 'admin';
    }

    /**
     * Persist a view mode and notify shared components (mobile nav etc.).
     */
    function persistViewMode(mode) {
        try {
            localStorage.setItem(STORAGE_KEY, mode);
        } catch (e) { /* storage unavailable */ }
        try {
            window.dispatchEvent(new CustomEvent('viewModeChanged', { detail: { viewMode: mode } }));
        } catch (e) { /* no CustomEvent */ }
        return mode;
    }

    /**
     * Resolve + persist in one step. Returns the effective view mode.
     */
    function applyViewMode(requested, user) {
        return persistViewMode(resolveViewMode(requested, user));
    }

    /**
     * Whether `viewAs` should be sent to the backend: only when a super admin
     * is previewing a role other than their own.
     */
    function isSimulating(viewMode, user) {
        var u = pickUser(user);
        return isSuperAdmin(u) && ROLES.indexOf(viewMode) !== -1 && viewMode !== 'admin';
    }

    var api = {
        ROLES: ROLES.slice(),
        setUser: setUser,
        getUser: getUser,
        isSuperAdmin: isSuperAdmin,
        roleViewMode: roleViewMode,
        readStoredViewMode: readStoredViewMode,
        resolveViewMode: resolveViewMode,
        persistViewMode: persistViewMode,
        applyViewMode: applyViewMode,
        isSimulating: isSimulating
    };

    Object.defineProperty(window, 'HubAuth', {
        value: Object.freeze(api),
        writable: false,
        configurable: false,
        enumerable: true
    });
})(window);
