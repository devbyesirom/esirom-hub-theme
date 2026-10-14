<?php
/**
 * Template Name: Registration Page
 * Template Post Type: page
 *
 * @package Esirom_Client_Hub
 */

if (!defined('ABSPATH')) {
    exit;
}

show_admin_bar(false);

$login_url = esirom_hub_page_url('login');
$privacy_url = esirom_hub_page_url('privacy');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="h-full">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <title>Create an account | <?php bloginfo('name'); ?></title>
    <?php esirom_hub_head_assets(array('description' => 'Request access to Agency Hub by Esirom.')); ?>
    <?php wp_head(); ?>
</head>
<body class="min-h-full antialiased text-gray-900 dark:text-gray-100">
    <div class="hub-auth">
        <aside class="hub-auth__brand" aria-hidden="true">
            <div class="hub-auth__logo-card self-start">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/agencyhub-by-esirom.png" alt="" class="h-14 w-auto">
            </div>
            <div class="max-w-md">
                <h2 class="text-3xl xl:text-4xl font-bold leading-tight tracking-tight">Join your brand's hub.</h2>
                <p class="mt-4 text-base text-white/80 leading-relaxed">
                    Clients request access with the form; Esirom staff sign up with their Google Workspace account. Every request is reviewed by the team before access is granted.
                </p>
                <ol class="mt-6 space-y-3 text-sm text-white/85">
                    <li class="flex gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/15 text-xs font-bold">1</span><span>Tell us who you are and which brand you represent.</span></li>
                    <li class="flex gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/15 text-xs font-bold">2</span><span>We match your account to your brand's workspace.</span></li>
                    <li class="flex gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/15 text-xs font-bold">3</span><span>You'll receive an email as soon as your access is approved.</span></li>
                </ol>
            </div>
            <p class="text-xs text-white/60">Already have an account? Use the sign-in link on the right.</p>
        </aside>

        <main class="hub-auth__panel">
            <div class="hub-auth__card">
                <div class="lg:hidden mb-8 flex justify-center">
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/agencyhub-by-esirom.png" alt="Agency Hub by Esirom" class="h-16 w-auto">
                </div>

                <div class="mb-6">
                    <h1 class="hub-auth__title">Create your account</h1>
                    <p class="hub-auth__subtitle">
                        Already have an account?
                        <a href="<?php echo esc_url($login_url); ?>" class="font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-300 dark:hover:text-brand-200">Sign in</a>
                    </p>
                </div>

                <div id="errorMessage" class="hidden hub-alert hub-alert--error mb-4" role="alert" aria-live="assertive">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-sm"></p>
                </div>
                <div id="successMessage" class="hidden hub-alert hub-alert--success mb-4" role="status" aria-live="polite">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm"></p>
                </div>

                <!-- Role selection -->
                <div class="mb-5">
                    <span class="hub-label">I am a…</span>
                    <div class="hub-segmented w-full" role="radiogroup" aria-label="Account type">
                        <button type="button" data-role="client" class="hub-segmented__item flex-1 is-active" role="radio" aria-checked="true">Client</button>
                        <button type="button" data-role="brand_rep" class="hub-segmented__item flex-1" role="radio" aria-checked="false">Esirom staff</button>
                    </div>
                    <select id="role" name="role" class="sr-only" tabindex="-1" aria-hidden="true">
                        <option value="client" selected>Client</option>
                        <option value="brand_rep">Brand Representative (Esirom staff)</option>
                    </select>
                </div>

                <!-- Brand rep: Google sign-up only -->
                <div id="brandRepPanel" class="hidden space-y-4">
                    <div class="hub-alert hub-alert--info">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p>Esirom brand representatives sign up with their <strong>@esirom.com</strong> Google account. No form required — we'll take your name from Google and review your access request.</p>
                    </div>
                    <a id="googleBrandRepBtn" href="#" class="hub-btn hub-btn--secondary hub-btn--lg hub-btn--block">
                        <svg class="!h-5 !w-5" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Sign up with Google
                    </a>
                    <p class="text-xs text-center text-gray-500 dark:text-gray-400">Your account will be reviewed before you can sign in.</p>
                </div>

                <!-- Client: registration form -->
                <div id="clientPanel">
                    <form id="registerForm" class="space-y-4" novalidate>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="firstName" class="hub-label">First name</label>
                                <input id="firstName" name="firstName" type="text" required autocomplete="given-name" class="hub-input" placeholder="Jane">
                            </div>
                            <div>
                                <label for="lastName" class="hub-label">Last name</label>
                                <input id="lastName" name="lastName" type="text" required autocomplete="family-name" class="hub-input" placeholder="Smith">
                            </div>
                        </div>

                        <div>
                            <label for="email" class="hub-label">Work email address</label>
                            <input id="email" name="email" type="email" required autocomplete="email" inputmode="email" spellcheck="false" class="hub-input" placeholder="you@company.com">
                        </div>

                        <div>
                            <label for="companyName" class="hub-label">Company or brand name</label>
                            <input id="companyName" name="companyName" type="text" required autocomplete="organization" class="hub-input" placeholder="Your company">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="password" class="hub-label">Password</label>
                                <input id="password" name="password" type="password" required minlength="6" autocomplete="new-password" class="hub-input" placeholder="Min. 6 characters">
                            </div>
                            <div>
                                <label for="confirmPassword" class="hub-label">Confirm password</label>
                                <input id="confirmPassword" name="confirmPassword" type="password" required minlength="6" autocomplete="new-password" class="hub-input" placeholder="Repeat password">
                            </div>
                        </div>

                        <div>
                            <label for="registrationNote" class="hub-label">Anything we should know? <span class="font-normal text-gray-400">(optional)</span></label>
                            <textarea id="registrationNote" name="registrationNote" rows="2" class="hub-textarea" placeholder="Tell us about your needs…"></textarea>
                        </div>

                        <button type="submit" id="submitBtn" class="hub-btn hub-btn--primary hub-btn--lg hub-btn--block">
                            <span id="submitText">Request access</span>
                            <svg id="submitLoader" class="hidden animate-spin !h-4 !w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>

                        <p class="text-xs text-center text-gray-500 dark:text-gray-400 leading-relaxed">
                            Your request will be reviewed by our team before you can sign in. By continuing you agree to our <a href="<?php echo esc_url($privacy_url); ?>" class="underline underline-offset-2 hover:text-gray-700 dark:hover:text-gray-200">privacy policy and terms</a>.
                        </p>
                    </form>
                </div>

                <p class="mt-8 text-center">
                    <a href="<?php echo esc_url($login_url); ?>" class="text-sm font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-300 dark:hover:text-brand-200">&larr; Back to sign in</a>
                </p>
            </div>
        </main>
    </div>

    <script>
        const API_URL = typeof ESIROM_API_URL !== 'undefined' ? ESIROM_API_URL : 'https://esirom-hub-backend-production.up.railway.app/api';
        const LOGIN_URL = <?php echo wp_json_encode(esc_url_raw($login_url)); ?>;

        const roleSelect = document.getElementById('role');
        const roleButtons = Array.from(document.querySelectorAll('[data-role]'));
        const brandRepPanel = document.getElementById('brandRepPanel');
        const clientPanel = document.getElementById('clientPanel');
        const errorMessage = document.getElementById('errorMessage');
        const successMessage = document.getElementById('successMessage');

        function showError(message) {
            errorMessage.querySelector('p').textContent = message;
            errorMessage.classList.remove('hidden');
            successMessage.classList.add('hidden');
            errorMessage.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }

        function showSuccess(message) {
            successMessage.querySelector('p').textContent = message;
            successMessage.classList.remove('hidden');
            errorMessage.classList.add('hidden');
        }

        function setRole(role) {
            roleSelect.value = role;
            roleButtons.forEach((btn) => {
                const active = btn.dataset.role === role;
                btn.classList.toggle('is-active', active);
                btn.setAttribute('aria-checked', active ? 'true' : 'false');
            });
            updateRolePanels();
        }

        function updateRolePanels() {
            const isBrandRep = roleSelect.value === 'brand_rep';
            brandRepPanel.classList.toggle('hidden', !isBrandRep);
            clientPanel.classList.toggle('hidden', isBrandRep);

            clientPanel.querySelectorAll('input, textarea').forEach((el) => {
                if (isBrandRep) {
                    el.removeAttribute('required');
                } else if (el.id !== 'registrationNote') {
                    el.setAttribute('required', 'required');
                }
            });
        }

        roleButtons.forEach((btn) => btn.addEventListener('click', () => setRole(btn.dataset.role)));
        updateRolePanels();

        document.getElementById('googleBrandRepBtn').addEventListener('click', (e) => {
            e.preventDefault();
            window.location.href = `${API_URL}/auth/google?intent=signup&role=brand_rep&returnTo=register`;
        });

        (function handleOAuthRedirect() {
            const params = new URLSearchParams(window.location.search);
            const oauthError = params.get('error');
            const oauthSuccess = params.get('success');

            if (oauthError) {
                setRole('brand_rep');
                showError(decodeURIComponent(oauthError.replace(/\+/g, ' ')));
                window.history.replaceState({}, document.title, window.location.pathname);
                return;
            }

            if (oauthSuccess) {
                setRole('brand_rep');
                showSuccess(decodeURIComponent(oauthSuccess.replace(/\+/g, ' ')));
                window.history.replaceState({}, document.title, window.location.pathname);
                setTimeout(() => { window.location.href = LOGIN_URL; }, 4000);
            }
        })();

        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const form = e.currentTarget;
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitLoader = document.getElementById('submitLoader');

            errorMessage.classList.add('hidden');
            successMessage.classList.add('hidden');

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (password !== confirmPassword) {
                showError('The two passwords do not match.');
                document.getElementById('confirmPassword').focus();
                return;
            }

            submitBtn.disabled = true;
            submitText.textContent = 'Sending request…';
            submitLoader.classList.remove('hidden');

            try {
                const response = await fetch(`${API_URL}/auth/register-request`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        firstName: document.getElementById('firstName').value.trim(),
                        lastName: document.getElementById('lastName').value.trim(),
                        email: document.getElementById('email').value.trim(),
                        companyName: document.getElementById('companyName').value.trim(),
                        role: 'client',
                        password,
                        registrationNote: document.getElementById('registrationNote').value.trim()
                    })
                });

                const data = await response.json().catch(() => ({}));

                if (response.ok && data.success) {
                    showSuccess(data.message || 'Thanks — your request has been submitted. We will email you once your access is approved.');
                    form.reset();
                    setTimeout(() => { window.location.href = LOGIN_URL; }, 4000);
                } else {
                    const fieldError = Array.isArray(data.errors) && data.errors.length ? data.errors.map(err => err.msg).join(' ') : '';
                    showError(data.message || fieldError || 'We could not submit your request. Please try again.');
                }
            } catch (err) {
                showError('We could not reach the server. Please try again later.');
            } finally {
                submitBtn.disabled = false;
                submitText.textContent = 'Request access';
                submitLoader.classList.add('hidden');
            }
        });
    </script>

    <?php wp_footer(); ?>
</body>
</html>
