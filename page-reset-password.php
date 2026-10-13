<?php
/**
 * Template Name: Reset Password Page
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

$login_url = esirom_hub_page_url('login');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="h-full">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <title>Reset password | <?php bloginfo('name'); ?></title>
    <?php esirom_hub_head_assets(); ?>
    <?php wp_head(); ?>
</head>
<body class="min-h-full antialiased text-gray-900 dark:text-gray-100">
    <div class="hub-auth">
        <aside class="hub-auth__brand" aria-hidden="true">
            <div class="hub-auth__logo-card self-start">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/agencyhub-by-esirom.png" alt="" class="h-14 w-auto">
            </div>
            <div class="max-w-md">
                <h2 class="text-3xl xl:text-4xl font-bold leading-tight tracking-tight">Choose a new password.</h2>
                <p class="mt-4 text-base text-white/80 leading-relaxed">
                    Pick something memorable that you don't use anywhere else. Once saved, you'll be signed straight back in.
                </p>
            </div>
            <p class="text-xs text-white/60">Reset links expire after a short time. If yours has expired, request a new one from the sign-in page.</p>
        </aside>

        <main class="hub-auth__panel">
            <div class="hub-auth__card">
                <div class="lg:hidden mb-8 flex justify-center">
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/agencyhub-by-esirom.png" alt="Agency Hub by Esirom" class="h-16 w-auto">
                </div>

                <div class="mb-6">
                    <h1 class="hub-auth__title">Reset your password</h1>
                    <p class="hub-auth__subtitle">Enter and confirm your new password below.</p>
                </div>

                <form id="resetPasswordForm" class="space-y-4" novalidate>
                    <div id="successMessage" class="hidden hub-alert hub-alert--success" role="status" aria-live="polite">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm font-medium"></p>
                    </div>
                    <div id="errorMessage" class="hidden hub-alert hub-alert--error" role="alert" aria-live="assertive">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <p class="text-sm"></p>
                    </div>

                    <div>
                        <label for="password" class="hub-label">New password</label>
                        <input id="password" name="password" type="password" required minlength="6" autocomplete="new-password"
                               class="hub-input" placeholder="At least 6 characters">
                        <p class="hub-field-hint">Use at least 6 characters. A mix of letters, numbers and symbols is strongest.</p>
                    </div>

                    <div>
                        <label for="confirmPassword" class="hub-label">Confirm new password</label>
                        <input id="confirmPassword" name="confirmPassword" type="password" required minlength="6" autocomplete="new-password"
                               class="hub-input" placeholder="Repeat your new password">
                    </div>

                    <button type="submit" id="submitBtn" class="hub-btn hub-btn--primary hub-btn--lg hub-btn--block">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                        <span id="btnText">Save new password</span>
                        <svg id="btnSpinner" class="hidden animate-spin !h-4 !w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </form>

                <p class="mt-6 text-center">
                    <a href="<?php echo esc_url($login_url); ?>" class="text-sm font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-300 dark:hover:text-brand-200">&larr; Back to sign in</a>
                </p>
            </div>
        </main>
    </div>

    <script>
        const API_URL = typeof ESIROM_API_URL !== 'undefined' ? ESIROM_API_URL : 'https://esirom-hub-backend-production.up.railway.app/api';
        const LOGIN_URL = <?php echo wp_json_encode(esc_url_raw($login_url)); ?>;

        const urlParams = new URLSearchParams(window.location.search);
        const resetToken = urlParams.get('token');

        const resetForm = document.getElementById('resetPasswordForm');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        const errorMessage = document.getElementById('errorMessage');
        const successMessage = document.getElementById('successMessage');

        function showError(message) {
            errorMessage.querySelector('p').textContent = message;
            errorMessage.classList.remove('hidden');
            successMessage.classList.add('hidden');
        }

        function showSuccess(message) {
            successMessage.querySelector('p').textContent = message;
            successMessage.classList.remove('hidden');
            errorMessage.classList.add('hidden');
        }

        function setBusy(busy, label) {
            submitBtn.disabled = busy;
            btnText.textContent = label || 'Save new password';
            btnSpinner.classList.toggle('hidden', !busy);
        }

        if (!resetToken) {
            showError('This reset link is invalid or missing its token. Please request a new password reset link from the sign-in page.');
            submitBtn.disabled = true;
            passwordInput.disabled = true;
            confirmPasswordInput.disabled = true;
        }

        resetForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (password.length < 6) {
                showError('Your password must be at least 6 characters long.');
                passwordInput.focus();
                return;
            }

            if (password !== confirmPassword) {
                showError('The two passwords do not match.');
                confirmPasswordInput.focus();
                return;
            }

            setBusy(true, 'Saving…');
            errorMessage.classList.add('hidden');
            successMessage.classList.add('hidden');

            try {
                const response = await fetch(`${API_URL}/auth/reset-password`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ token: resetToken, password })
                });

                const data = await response.json().catch(() => ({}));

                if (response.ok && data.success) {
                    // Store the fresh session token; the sign-in page verifies it and
                    // sends the user to the right place for their role.
                    if (data.token) {
                        localStorage.setItem('token', data.token);
                        localStorage.removeItem('user');
                        localStorage.removeItem('requirePasswordChange');
                    }
                    showSuccess('Your password has been reset. Signing you in…');
                    setBusy(true, 'Done');
                    setTimeout(() => { window.location.href = LOGIN_URL; }, 1500);
                } else {
                    showError(data.message || 'We could not reset your password. The link may have expired — please request a new one.');
                    setBusy(false);
                }
            } catch (error) {
                showError('We could not reach the server. Please try again later.');
                setBusy(false);
            }
        });
    </script>

    <?php wp_footer(); ?>
</body>
</html>
