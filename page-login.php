<?php
/**
 * Template Name: Login Page
 * Template Post Type: page
 *
 * @package Esirom_Client_Hub
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Hide WordPress admin bar on this page
show_admin_bar(false);

$register_url = esirom_hub_page_url('register');
$workflow_url = esirom_hub_page_url('workflow');
$privacy_url = esirom_hub_page_url('privacy');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="h-full">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <title>Sign in | <?php bloginfo('name'); ?></title>
    <?php esirom_hub_head_assets(array('description' => 'Sign in to Agency Hub by Esirom — workflow, insights, content and reports for clients and the Esirom team.')); ?>
    <?php wp_head(); ?>
</head>
<body class="min-h-full antialiased text-gray-900 dark:text-gray-100">
    <div class="hub-auth">

        <!-- Brand panel (desktop) -->
        <aside class="hub-auth__brand" aria-hidden="true">
            <div class="flex items-center justify-between">
                <div class="hub-auth__logo-card">
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/agencyhub-by-esirom.png" alt="" class="h-14 w-auto">
                </div>
                <span class="text-xs font-semibold uppercase tracking-[0.2em] text-white/70">Client portal</span>
            </div>

            <div class="max-w-md">
                <h2 class="text-3xl xl:text-4xl font-bold leading-tight tracking-tight">Everything your brand does on social, in one place.</h2>
                <p class="mt-4 text-base text-white/80 leading-relaxed">
                    Review and approve content, follow the production pipeline, read monthly insights and download reports — all from a single, secure hub.
                </p>
                <div class="mt-6 grid grid-cols-2 gap-2">
                    <?php
                    $features = array('Content approvals', 'Live insights', 'Monthly reports', 'Event coverage');
                    foreach ($features as $f) : ?>
                    <div class="hub-auth__feature">
                        <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span><?php echo esc_html($f); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Collective impact (public showcase) -->
            <div id="showcaseCard">
                <div class="flex items-end justify-between gap-4 mb-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-white/70">Collective impact</p>
                        <p class="text-sm text-white/85 mt-1" id="showcasePeriod">Results across our client portfolio</p>
                    </div>
                </div>
                <div id="showcaseLoading" class="grid grid-cols-2 gap-3" aria-hidden="true">
                    <div class="hub-auth__stat h-[5.25rem] animate-pulse"></div>
                    <div class="hub-auth__stat h-[5.25rem] animate-pulse"></div>
                    <div class="hub-auth__stat h-[5.25rem] animate-pulse"></div>
                    <div class="hub-auth__stat h-[5.25rem] animate-pulse"></div>
                </div>
                <div id="showcaseEmpty" class="hidden hub-auth__stat text-sm text-white/85">
                    Performance data will appear here as posts are synced across client accounts.
                </div>
                <div id="showcaseContent" class="hidden">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="hub-auth__stat">
                            <p class="hub-auth__stat-label">Total reach</p>
                            <p class="hub-auth__stat-value" id="metricReach">—</p>
                            <p class="hub-auth__stat-sub hidden" id="changeReach"></p>
                        </div>
                        <div class="hub-auth__stat">
                            <p class="hub-auth__stat-label">Content views</p>
                            <p class="hub-auth__stat-value" id="metricViews">—</p>
                            <p class="hub-auth__stat-sub hidden" id="changeViews"></p>
                        </div>
                        <div class="hub-auth__stat">
                            <p class="hub-auth__stat-label">Engagement</p>
                            <p class="hub-auth__stat-value" id="metricEngagement">—</p>
                            <p class="hub-auth__stat-sub hidden" id="changeEngagement"></p>
                        </div>
                        <div class="hub-auth__stat">
                            <p class="hub-auth__stat-label">Combined audience</p>
                            <p class="hub-auth__stat-value" id="metricAudience">—</p>
                            <p class="hub-auth__stat-sub">Live follower counts</p>
                        </div>
                    </div>
                    <div class="mt-3 grid grid-cols-4 gap-2 text-center">
                        <div class="rounded-lg bg-white/10 px-2 py-2">
                            <p class="text-[10px] uppercase tracking-wide text-white/70">Posts</p>
                            <p class="text-sm font-semibold" id="metricPosts">—</p>
                        </div>
                        <div class="rounded-lg bg-white/10 px-2 py-2">
                            <p class="text-[10px] uppercase tracking-wide text-white/70">Impressions</p>
                            <p class="text-sm font-semibold" id="metricImpressions">—</p>
                        </div>
                        <div class="rounded-lg bg-white/10 px-2 py-2">
                            <p class="text-[10px] uppercase tracking-wide text-white/70">All-time views</p>
                            <p class="text-sm font-semibold" id="metricAllTimeViews">—</p>
                        </div>
                        <div class="rounded-lg bg-white/10 px-2 py-2">
                            <p class="text-[10px] uppercase tracking-wide text-white/70">Eng. rate</p>
                            <p class="text-sm font-semibold" id="metricEngRate">—</p>
                        </div>
                    </div>
                    <p class="mt-3 text-[11px] text-white/70 leading-relaxed" id="showcaseFootnote">Aggregated portfolio performance.</p>
                </div>
            </div>
        </aside>

        <!-- Sign-in panel -->
        <main class="hub-auth__panel">
            <div class="hub-auth__card">
                <div class="lg:hidden mb-8 flex justify-center">
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/agencyhub-by-esirom.png" alt="Agency Hub by Esirom" class="h-16 w-auto">
                </div>

                <div class="mb-6">
                    <h1 class="hub-auth__title">Welcome back</h1>
                    <p class="hub-auth__subtitle">Sign in to your Agency Hub account to continue.</p>
                </div>

                <!-- Alert messages -->
                <div id="errorMessage" class="hidden hub-alert hub-alert--error mb-4" role="alert" aria-live="assertive">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-sm"></p>
                </div>
                <div id="successMessage" class="hidden hub-alert hub-alert--success mb-4" role="status" aria-live="polite">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm"></p>
                </div>

                <!-- Google Sign-In -->
                <a id="googleSignInBtn" href="#" class="hub-btn hub-btn--secondary hub-btn--lg hub-btn--block">
                    <svg class="!h-5 !w-5" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Continue with Google
                </a>

                <div class="hub-auth__divider my-5"><span>or sign in with email</span></div>

                <!-- Email / Password Form -->
                <form id="loginForm" class="space-y-4" novalidate>
                    <div>
                        <label for="email" class="hub-label">Email address</label>
                        <input id="email" name="email" type="email" autocomplete="email" required inputmode="email" spellcheck="false"
                               class="hub-input" placeholder="you@example.com">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="hub-label !mb-0">Password</label>
                            <button type="button" onclick="showForgotPasswordModal()" class="text-xs font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-300 dark:hover:text-brand-200">
                                Forgotten your password?
                            </button>
                        </div>
                        <div class="hub-input-group">
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                   class="hub-input" placeholder="Your password">
                            <div class="hub-input-group__addon">
                                <button type="button" id="togglePassword" class="hub-btn hub-btn--ghost hub-btn--icon hub-btn--sm" aria-label="Show password" aria-pressed="false">
                                    <svg id="eyeOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg id="eyeClosed" class="hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="submitBtn" class="hub-btn hub-btn--primary hub-btn--lg hub-btn--block">
                        <span id="btnText">Sign in</span>
                        <svg id="btnSpinner" class="hidden animate-spin !h-4 !w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </form>

                <!-- Sign up -->
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-center text-gray-600 dark:text-gray-300">New to Agency Hub?</p>
                    <a href="<?php echo esc_url($register_url); ?>" class="hub-btn hub-btn--soft hub-btn--block mt-3">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        Create an account
                    </a>
                    <p class="mt-3 text-xs text-center text-gray-500 dark:text-gray-400 leading-relaxed">
                        For <span class="font-medium text-gray-700 dark:text-gray-200">clients</span> and <span class="font-medium text-gray-700 dark:text-gray-200">Esirom staff</span>. Requests are reviewed before access is granted, or use Google above for a faster sign-up.
                    </p>
                </div>

                <p class="mt-8 text-center text-xs text-gray-400 dark:text-gray-500">
                    &copy; <?php echo esc_html(gmdate('Y')); ?> Esirom &middot; <a href="<?php echo esc_url($privacy_url); ?>" class="hover:text-gray-600 dark:hover:text-gray-300 underline-offset-2 hover:underline">Privacy &amp; terms</a>
                </p>
            </div>
        </main>
    </div>

    <!-- Forgot Password Modal -->
    <div id="forgotPasswordModal" class="hidden hub-modal" role="dialog" aria-modal="true" aria-labelledby="forgotTitle">
        <div class="hub-modal__backdrop"></div>
        <div class="hub-modal__panel">
            <div class="hub-modal__header">
                <div>
                    <h2 id="forgotTitle" class="hub-modal__title">Reset your password</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Enter your email address and we'll send you a link to choose a new password.</p>
                </div>
                <button type="button" onclick="closeForgotPasswordModal()" class="hub-btn hub-btn--ghost hub-btn--icon" aria-label="Close">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form id="forgotPasswordForm" class="hub-modal__body space-y-4" novalidate>
                <div id="forgotSuccessMessage" class="hidden hub-alert hub-alert--success" role="status" aria-live="polite">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm"></p>
                </div>
                <div id="forgotErrorMessage" class="hidden hub-alert hub-alert--error" role="alert" aria-live="assertive">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-sm"></p>
                </div>
                <div>
                    <label for="forgotEmail" class="hub-label">Email address</label>
                    <input id="forgotEmail" name="email" type="email" required autocomplete="email" inputmode="email" class="hub-input" placeholder="you@example.com">
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-2">
                    <button type="button" onclick="closeForgotPasswordModal()" class="hub-btn hub-btn--secondary">Cancel</button>
                    <button type="submit" id="forgotSubmitBtn" class="hub-btn hub-btn--primary">
                        <span id="forgotBtnText">Send reset link</span>
                        <svg id="forgotBtnSpinner" class="hidden animate-spin !h-4 !w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const API_URL = typeof ESIROM_API_URL !== 'undefined' ? ESIROM_API_URL : 'https://esirom-hub-backend-production.up.railway.app/api';
        const WORKFLOW_URL = <?php echo wp_json_encode(esc_url_raw($workflow_url)); ?>;

        const loginForm = document.getElementById('loginForm');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        const errorMessage = document.getElementById('errorMessage');
        const successMessage = document.getElementById('successMessage');
        let errorTimer = null;
        const DISABLED_MESSAGE = window.HubAuth ? window.HubAuth.DISABLED_MESSAGE : 'This account has been disabled. Please contact Esirom.';

        // `persistent` keeps the alert on screen (used for the disabled-account notice).
        function showError(message, persistent = false) {
            errorMessage.querySelector('p').textContent = message;
            errorMessage.classList.remove('hidden');
            successMessage.classList.add('hidden');
            clearTimeout(errorTimer);
            if (!persistent) {
                errorTimer = setTimeout(() => errorMessage.classList.add('hidden'), 10000);
            }
        }

        function showDisabledNotice() {
            showError(DISABLED_MESSAGE, true);
        }

        function isDisabledResponse(response, data) {
            return window.HubAuth
                ? window.HubAuth.isDisabledResponse(response.status, data)
                : (response.status === 403 && data && data.code === 'ACCOUNT_DISABLED');
        }

        function showSuccess(message) {
            successMessage.querySelector('p').textContent = message;
            successMessage.classList.remove('hidden');
            errorMessage.classList.add('hidden');
        }

        function setBusy(busy, label) {
            submitBtn.disabled = busy;
            btnText.textContent = label || 'Sign in';
            btnSpinner.classList.toggle('hidden', !busy);
        }

        // Clients land on the Content Bank; staff on the Workflow dashboard.
        function redirectAfterLogin(user) {
            const isStaff = user && (user.role === 'admin' || user.role === 'brand_rep');
            window.location.href = isStaff ? WORKFLOW_URL : WORKFLOW_URL + (WORKFLOW_URL.includes('?') ? '&' : '?') + 'tab=contentBank';
        }

        function clearSession() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            localStorage.removeItem('requirePasswordChange');
        }

        // Show / hide password
        const togglePassword = document.getElementById('togglePassword');
        togglePassword.addEventListener('click', () => {
            const show = passwordInput.type === 'password';
            passwordInput.type = show ? 'text' : 'password';
            togglePassword.setAttribute('aria-pressed', show ? 'true' : 'false');
            togglePassword.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            document.getElementById('eyeOpen').classList.toggle('hidden', show);
            document.getElementById('eyeClosed').classList.toggle('hidden', !show);
        });

        document.getElementById('googleSignInBtn').addEventListener('click', (e) => {
            e.preventDefault();
            window.location.href = `${API_URL}/auth/google`;
        });

        // Handle Google OAuth redirect callback and sign-out reasons from other Hub pages
        (function handleOAuthRedirect() {
            const params = new URLSearchParams(window.location.search);
            const oauthToken = params.get('token');
            const oauthError = params.get('error');
            const oauthSuccess = params.get('success');
            const reason = window.HubAuth ? window.HubAuth.consumeSignOutReason() : params.get('reason');

            // Sent here by HubAuth after the backend rejected a disabled account mid-session,
            // or by the backend's Google callback (`?error=account_disabled`).
            if (reason === 'disabled' || (oauthError && /^account[_ -]?disabled$/i.test(oauthError.trim()))) {
                clearSession();
                showDisabledNotice();
                window.history.replaceState({}, document.title, window.location.pathname);
                return;
            }

            if (oauthError) {
                showError(decodeURIComponent(oauthError.replace(/\+/g, ' ')));
                window.history.replaceState({}, document.title, window.location.pathname);
                return;
            }

            if (oauthSuccess) {
                showSuccess(decodeURIComponent(oauthSuccess.replace(/\+/g, ' ')));
                window.history.replaceState({}, document.title, window.location.pathname);
                return;
            }

            if (!oauthToken) return;

            setBusy(true, 'Signing you in…');
            fetch(`${API_URL}/auth/me`, {
                headers: { 'Authorization': `Bearer ${oauthToken}` }
            })
            .then(async response => {
                const data = await response.json().catch(() => ({}));
                if (isDisabledResponse(response, data)) {
                    const err = new Error(DISABLED_MESSAGE);
                    err.accountDisabled = true;
                    throw err;
                }
                if (!response.ok) throw new Error('Failed to load user profile');
                return data;
            })
            .then(data => {
                if (!data || !data.user) throw new Error('Invalid session');
                localStorage.setItem('token', oauthToken);
                localStorage.setItem('user', JSON.stringify(data.user));
                window.history.replaceState({}, document.title, window.location.pathname);
                redirectAfterLogin(data.user);
            })
            .catch((error) => {
                setBusy(false);
                if (error && error.accountDisabled) {
                    showDisabledNotice();
                } else {
                    showError('Google sign-in completed but the session could not be established. Please try again.');
                }
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        })();

        // Already signed in? Verify the stored token and continue.
        (function resumeSession() {
            const token = localStorage.getItem('token');
            if (!token) return;
            fetch(`${API_URL}/auth/me`, {
                headers: { 'Authorization': `Bearer ${token}` }
            })
            .then(async response => {
                if (response.ok) return response.json();
                if (response.status === 401 || response.status === 403) {
                    clearSession();
                    const data = await response.json().catch(() => ({}));
                    if (isDisabledResponse(response, data)) showDisabledNotice();
                }
                return null;
            })
            .then(data => {
                if (data && data.user) {
                    localStorage.setItem('user', JSON.stringify(data.user));
                    redirectAfterLogin(data.user);
                }
            })
            .catch(() => { /* offline: stay on the sign-in page */ });
        })();

        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const email = emailInput.value.trim();
            const password = passwordInput.value;

            if (!email || !password) {
                showError('Please enter both your email address and password.');
                (email ? passwordInput : emailInput).focus();
                return;
            }

            setBusy(true, 'Signing in…');
            errorMessage.classList.add('hidden');

            try {
                const response = await fetch(`${API_URL}/auth/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json().catch(() => ({}));

                if (response.ok && data.success) {
                    localStorage.setItem('token', data.token);
                    localStorage.setItem('user', JSON.stringify(data.user));

                    if (data.requirePasswordChange) {
                        localStorage.setItem('requirePasswordChange', 'true');
                        setBusy(true, 'Password change required…');
                    } else {
                        localStorage.removeItem('requirePasswordChange');
                        setBusy(true, 'Signed in');
                    }

                    setTimeout(() => redirectAfterLogin(data.user), 400);
                } else if (isDisabledResponse(response, data)) {
                    clearSession();
                    showDisabledNotice();
                    setBusy(false);
                } else {
                    const fieldError = Array.isArray(data.errors) && data.errors.length ? data.errors.map(err => err.msg).join(' ') : '';
                    showError(data.message || fieldError || 'Sign-in failed. Please check your details and try again.');
                    setBusy(false);
                }
            } catch (error) {
                console.error('Login error:', error);
                showError('We could not reach the server. Please check your connection and try again.');
                setBusy(false);
            }
        });

        // Anonymous portfolio showcase (brand panel)
        function formatShowcaseNumber(value) {
            const n = Number(value) || 0;
            if (n >= 1e9) return `${(n / 1e9).toFixed(1)}B`;
            if (n >= 1e6) return `${(n / 1e6).toFixed(1)}M`;
            if (n >= 1e3) return `${(n / 1e3).toFixed(1)}K`;
            return n.toLocaleString('en-GB');
        }

        function formatChangeEl(el, change) {
            if (change === null || change === undefined || !el) return;
            const rounded = Math.round(change * 10) / 10;
            const positive = rounded >= 0;
            el.textContent = `${positive ? '+' : ''}${rounded}% vs last year`;
            el.className = `hub-auth__stat-sub font-semibold ${positive ? 'text-emerald-200' : 'text-rose-200'}`;
            el.classList.remove('hidden');
        }

        async function loadShowcaseInsights() {
            const loading = document.getElementById('showcaseLoading');
            const empty = document.getElementById('showcaseEmpty');
            const content = document.getElementById('showcaseContent');

            try {
                const response = await fetch(`${API_URL}/public/showcase-insights`);
                const data = await response.json();

                loading.classList.add('hidden');

                if (!response.ok || !data.success) {
                    empty.classList.remove('hidden');
                    empty.textContent = 'Portfolio insights are unavailable right now.';
                    return;
                }

                const metrics = data.yearToDate || {};
                const hasData =
                    (metrics.postCount || 0) > 0 ||
                    (metrics.reach || 0) > 0 ||
                    (metrics.views || 0) > 0 ||
                    (metrics.engagement || 0) > 0 ||
                    (data.audience?.total || 0) > 0;

                if (!hasData) {
                    empty.classList.remove('hidden');
                    return;
                }

                content.classList.remove('hidden');

                document.getElementById('showcasePeriod').textContent =
                    data.period?.label ? `${data.period.label} · aggregated results` : 'Results across our client portfolio';

                document.getElementById('metricReach').textContent = formatShowcaseNumber(metrics.reach);
                document.getElementById('metricViews').textContent = formatShowcaseNumber(metrics.views);
                document.getElementById('metricEngagement').textContent = formatShowcaseNumber(metrics.engagement);
                document.getElementById('metricPosts').textContent = formatShowcaseNumber(metrics.postCount);
                document.getElementById('metricAudience').textContent = formatShowcaseNumber(data.audience?.total || 0);
                document.getElementById('metricImpressions').textContent = formatShowcaseNumber(metrics.impressions);
                document.getElementById('metricEngRate').textContent =
                    metrics.engagementRate ? `${metrics.engagementRate.toFixed(2)}%` : '—';
                document.getElementById('metricAllTimeViews').textContent =
                    formatShowcaseNumber(data.allTime?.views || 0);

                if (data.comparison) {
                    formatChangeEl(document.getElementById('changeReach'), data.comparison.reach);
                    formatChangeEl(document.getElementById('changeViews'), data.comparison.views);
                    formatChangeEl(document.getElementById('changeEngagement'), data.comparison.engagement);
                    if (data.comparisonLabel) {
                        document.getElementById('showcaseFootnote').textContent =
                            `${data.comparisonLabel} · ${data.methodology || 'aggregated portfolio performance.'}`;
                    }
                } else if (data.methodology) {
                    document.getElementById('showcaseFootnote').textContent = data.methodology;
                }
            } catch (error) {
                loading.classList.add('hidden');
                empty.classList.remove('hidden');
                empty.textContent = 'Portfolio insights are unavailable right now.';
            }
        }

        if (window.matchMedia('(min-width: 1024px)').matches) {
            loadShowcaseInsights();
        }

        // Forgot password modal
        const forgotModal = document.getElementById('forgotPasswordModal');
        let lastFocused = null;

        function showForgotPasswordModal() {
            lastFocused = document.activeElement;
            forgotModal.classList.remove('hidden');
            document.getElementById('forgotEmail').value = emailInput.value.trim();
            document.getElementById('forgotSuccessMessage').classList.add('hidden');
            document.getElementById('forgotErrorMessage').classList.add('hidden');
            setTimeout(() => document.getElementById('forgotEmail').focus(), 20);
        }

        function closeForgotPasswordModal() {
            forgotModal.classList.add('hidden');
            if (lastFocused && typeof lastFocused.focus === 'function') lastFocused.focus();
        }

        forgotModal.querySelector('.hub-modal__backdrop').addEventListener('click', closeForgotPasswordModal);
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !forgotModal.classList.contains('hidden')) closeForgotPasswordModal();
        });

        document.getElementById('forgotPasswordForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const email = document.getElementById('forgotEmail').value.trim();
            const forgotBtn = document.getElementById('forgotSubmitBtn');
            const forgotText = document.getElementById('forgotBtnText');
            const forgotSpinner = document.getElementById('forgotBtnSpinner');
            const forgotSuccess = document.getElementById('forgotSuccessMessage');
            const forgotError = document.getElementById('forgotErrorMessage');

            if (!email) {
                forgotError.querySelector('p').textContent = 'Please enter your email address.';
                forgotError.classList.remove('hidden');
                return;
            }

            forgotBtn.disabled = true;
            forgotText.textContent = 'Sending…';
            forgotSpinner.classList.remove('hidden');
            forgotSuccess.classList.add('hidden');
            forgotError.classList.add('hidden');

            try {
                const response = await fetch(`${API_URL}/auth/forgot-password`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });

                const data = await response.json().catch(() => ({}));

                if (response.ok && data.success) {
                    forgotSuccess.querySelector('p').textContent = data.message || 'If an account exists for that address, a reset link is on its way. Check your inbox.';
                    forgotSuccess.classList.remove('hidden');
                    document.getElementById('forgotEmail').value = '';
                    setTimeout(closeForgotPasswordModal, 4000);
                } else {
                    forgotError.querySelector('p').textContent = data.message || 'We could not send a reset link. Please try again.';
                    forgotError.classList.remove('hidden');
                }
            } catch (error) {
                forgotError.querySelector('p').textContent = 'We could not reach the server. Please try again later.';
                forgotError.classList.remove('hidden');
            } finally {
                forgotBtn.disabled = false;
                forgotText.textContent = 'Send reset link';
                forgotSpinner.classList.add('hidden');
            }
        });
    </script>

    <?php wp_footer(); ?>
</body>
</html>
