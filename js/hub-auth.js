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
 *
 * It also owns the "disabled account" handling: when the backend answers any
 * API call with 403 + `code: "ACCOUNT_DISABLED"` the stored session is cleared
 * and the user is sent to the sign-in page with `?reason=disabled`, so a
 * client whose brand has been disabled is signed out everywhere at once.
 */
(function (window) {
    'use strict';

    var ROLES = ['admin', 'brand_rep', 'client'];
    var STORAGE_KEY = 'viewMode';
    var DISABLED_CODE = 'ACCOUNT_DISABLED';
    var DISABLED_REASON = 'disabled';
    var DISABLED_MESSAGE = 'This account has been disabled. Please contact Esirom.';
    var SIGN_OUT_REASON_KEY = 'hubSignOutReason';
    var guardInstalled = false;
    var redirecting = false;

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

    /* ── Disabled accounts ─────────────────────────────────────────────── */

    /**
     * Whether a client (brand) record is usable. Disabled brands carry
     * `isActive: false`; anything else (including legacy records without the
     * flag) counts as active.
     */
    function isClientActive(client) {
        return !!client && typeof client === 'object' && client.isActive !== false;
    }

    /**
     * Drop disabled brands from a list returned by the backend. Used as a
     * belt-and-braces filter on every brand picker outside the Admin Panel.
     */
    function activeClients(list) {
        return Array.isArray(list) ? list.filter(isClientActive) : [];
    }

    /**
     * Whether an API response means "this account has been disabled".
     * Contract with the backend: HTTP 403 and a JSON body carrying
     * `code: "ACCOUNT_DISABLED"` (a `message` mentioning a disabled account is
     * accepted as a fallback).
     */
    function isDisabledResponse(status, data) {
        if (Number(status) !== 403 || !data || typeof data !== 'object') return false;
        if (data.code === DISABLED_CODE || data.accountDisabled === true) return true;
        var message = typeof data.message === 'string' ? data.message : '';
        return /account (has been |is )?disabled/i.test(message);
    }

    function clearSession() {
        try {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            localStorage.removeItem('requirePasswordChange');
        } catch (e) { /* storage unavailable */ }
        verifiedUser = null;
    }

    function loginUrl(reason) {
        var base = typeof window.ESIROM_LOGIN_URL === 'string' && window.ESIROM_LOGIN_URL ? window.ESIROM_LOGIN_URL : '/';
        if (!reason) return base;
        return base + (base.indexOf('?') === -1 ? '?' : '&') + 'reason=' + encodeURIComponent(reason);
    }

    /**
     * Sign out locally and send the browser to the sign-in page, optionally
     * with a reason the sign-in page turns into a friendly message. The reason
     * is also kept in sessionStorage because a page's own 401/403 handler may
     * navigate to the plain sign-in URL a moment later and win the race.
     */
    function redirectToLogin(reason) {
        if (redirecting) return;
        redirecting = true;
        clearSession();
        if (reason) {
            try { sessionStorage.setItem(SIGN_OUT_REASON_KEY, reason); } catch (e) { /* storage unavailable */ }
        }
        window.location.href = loginUrl(reason);
    }

    /**
     * Read (and clear) a sign-out reason left by redirectToLogin() or passed
     * in the URL (`?reason=disabled`). Used by the sign-in page.
     */
    function consumeSignOutReason() {
        var reason = '';
        try {
            reason = new URLSearchParams(window.location.search).get('reason') || '';
        } catch (e) { /* no URLSearchParams */ }
        try {
            if (!reason) reason = sessionStorage.getItem(SIGN_OUT_REASON_KEY) || '';
            sessionStorage.removeItem(SIGN_OUT_REASON_KEY);
        } catch (e) { /* storage unavailable */ }
        return reason;
    }

    function apiBase() {
        return typeof window.ESIROM_API_URL === 'string' ? window.ESIROM_API_URL.replace(/\/+$/, '') : '';
    }

    function requestUrl(input) {
        if (typeof input === 'string') return input;
        if (input && typeof input === 'object' && typeof input.url === 'string') return input.url;
        return '';
    }

    /**
     * Wrap `window.fetch` so that any backend reply of 403 ACCOUNT_DISABLED
     * ends the session immediately. Pages do not need to know about it: their
     * own 401/403 handling still runs, but by then the redirect is underway.
     * Not installed on the sign-in / registration / reset pages.
     */
    function installDisabledAccountGuard() {
        if (guardInstalled || typeof window.fetch !== 'function') return false;
        guardInstalled = true;
        var nativeFetch = window.fetch;
        window.fetch = function (input, init) {
            var promise = nativeFetch.call(window, input, init);
            var base = apiBase();
            var url = requestUrl(input);
            if (!base || url.indexOf(base) !== 0) return promise;
            return promise.then(function (response) {
                if (!response || response.status !== 403 || redirecting) return response;
                return response.clone().json().then(function (data) {
                    if (isDisabledResponse(response.status, data)) redirectToLogin(DISABLED_REASON);
                    return response;
                }, function () {
                    return response;
                });
            });
        };
        return true;
    }

    var api = {
        ROLES: ROLES.slice(),
        DISABLED_CODE: DISABLED_CODE,
        DISABLED_REASON: DISABLED_REASON,
        DISABLED_MESSAGE: DISABLED_MESSAGE,
        setUser: setUser,
        getUser: getUser,
        isSuperAdmin: isSuperAdmin,
        roleViewMode: roleViewMode,
        readStoredViewMode: readStoredViewMode,
        resolveViewMode: resolveViewMode,
        persistViewMode: persistViewMode,
        applyViewMode: applyViewMode,
        isSimulating: isSimulating,
        isClientActive: isClientActive,
        activeClients: activeClients,
        isDisabledResponse: isDisabledResponse,
        clearSession: clearSession,
        loginUrl: loginUrl,
        redirectToLogin: redirectToLogin,
        consumeSignOutReason: consumeSignOutReason,
        installDisabledAccountGuard: installDisabledAccountGuard
    };

    Object.defineProperty(window, 'HubAuth', {
        value: Object.freeze(api),
        writable: false,
        configurable: false,
        enumerable: true
    });
})(window);
