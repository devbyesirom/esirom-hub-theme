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

$login_page = get_page_by_path('login');
$login_url = $login_page ? get_permalink($login_page) : home_url('/login/');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="h-full">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php bloginfo('name'); ?></title>
    <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png">
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png">
    <script>window.tailwindCDNWarning = false;</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
    <?php wp_head(); ?>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50">
    <div class="min-h-screen flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-br from-indigo-600 to-purple-600 px-8 py-5 flex items-center justify-center">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/agencyhub-by-esirom.png" alt="Agency Hub by Esirom" class="h-16 w-auto">
                </div>

                <div class="px-8 py-8 space-y-5">
                    <div class="text-center">
                        <h1 class="text-xl font-bold text-gray-900">Create your account</h1>
                        <p class="text-sm text-gray-500 mt-1">
                            Already have an account?
                            <a href="<?php echo esc_url($login_url); ?>" class="font-medium text-indigo-600 hover:text-indigo-500">Sign in</a>
                        </p>
                    </div>

                    <div id="errorMessage" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
                        <p class="text-sm"></p>
                    </div>
                    <div id="successMessage" class="hidden bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg" role="alert">
                        <p class="text-sm"></p>
                    </div>

                    <!-- Role selection -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1.5">I am a *</label>
                        <select
                            id="role"
                            name="role"
                            class="block w-full px-4 py-2.5 border border-gray-300 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
                        >
                            <option value="client">Client</option>
                            <option value="brand_rep">Brand Representative (Esirom staff)</option>
                        </select>
                    </div>

                    <!-- Brand rep: Google sign-up only -->
                    <div id="brandRepPanel" class="hidden space-y-4">
                        <div class="rounded-xl bg-indigo-50 border border-indigo-100 px-4 py-3">
                            <p class="text-sm text-indigo-900 leading-relaxed">
                                Esirom brand representatives sign up with a <strong>@esirom.com</strong> Google account. No form required — we’ll pull your name from Google and review your access request.
                            </p>
                        </div>
                        <a
                            id="googleBrandRepBtn"
                            href="#"
                            class="w-full flex items-center justify-center gap-3 py-3 px-4 border-2 border-gray-200 rounded-xl text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all"
                        >
                            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Sign up with Google
                        </a>
                        <p class="text-xs text-center text-gray-500">
                            Your account will be reviewed before you can sign in.
                        </p>
                    </div>

                    <!-- Client: registration form -->
                    <div id="clientPanel" class="space-y-4">
                        <form id="registerForm" class="space-y-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1.5">First name *</label>
                                    <input id="firstName" name="firstName" type="text" required
                                        class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        placeholder="John">
                                </div>
                                <div>
                                    <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1.5">Last name *</label>
                                    <input id="lastName" name="lastName" type="text" required
                                        class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        placeholder="Doe">
                                </div>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email address *</label>
                                <input id="email" name="email" type="email" required
                                    class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="you@company.com">
                            </div>

                            <div>
                                <label for="companyName" class="block text-sm font-medium text-gray-700 mb-1.5">Company / brand name *</label>
                                <input id="companyName" name="companyName" type="text" required
                                    class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="Your Company">
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password *</label>
                                <input id="password" name="password" type="password" required minlength="6"
                                    class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="Min. 6 characters">
                            </div>

                            <div>
                                <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1.5">Confirm password *</label>
                                <input id="confirmPassword" name="confirmPassword" type="password" required minlength="6"
                                    class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="Confirm password">
                            </div>

                            <div>
                                <label for="registrationNote" class="block text-sm font-medium text-gray-700 mb-1.5">Additional information (optional)</label>
                                <textarea id="registrationNote" name="registrationNote" rows="2"
                                    class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="Tell us about your needs…"></textarea>
                            </div>

                            <button type="submit" id="submitBtn"
                                class="w-full flex items-center justify-center gap-2 py-2.5 px-4 text-sm font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 transition-all">
                                <span id="submitText">Create account</span>
                                <svg id="submitLoader" class="hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>

                            <p class="text-xs text-center text-gray-500">
                                Your account will be reviewed by our team before you can sign in.
                            </p>
                        </form>
                    </div>
                </div>
            </div>

            <p class="text-center mt-6">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="text-sm text-gray-500 hover:text-gray-800">← Back to home</a>
            </p>
        </div>
    </div>

    <script>
        const API_URL = typeof ESIROM_API_URL !== 'undefined' ? ESIROM_API_URL : 'https://esirom-hub-backend-production.up.railway.app/api';
        const LOGIN_URL = '<?php echo esc_js($login_url); ?>';

        const roleSelect = document.getElementById('role');
        const brandRepPanel = document.getElementById('brandRepPanel');
        const clientPanel = document.getElementById('clientPanel');
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

        function updateRolePanels() {
            const isBrandRep = roleSelect.value === 'brand_rep';
            brandRepPanel.classList.toggle('hidden', !isBrandRep);
            clientPanel.classList.toggle('hidden', isBrandRep);

            const clientFields = clientPanel.querySelectorAll('input, textarea');
            clientFields.forEach((el) => {
                if (isBrandRep) {
                    el.removeAttribute('required');
                } else if (el.id !== 'registrationNote') {
                    el.setAttribute('required', 'required');
                }
            });
        }

        roleSelect.addEventListener('change', updateRolePanels);
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
                roleSelect.value = 'brand_rep';
                updateRolePanels();
                showError(decodeURIComponent(oauthError.replace(/\+/g, ' ')));
                window.history.replaceState({}, document.title, window.location.pathname);
                return;
            }

            if (oauthSuccess) {
                roleSelect.value = 'brand_rep';
                updateRolePanels();
                showSuccess(decodeURIComponent(oauthSuccess.replace(/\+/g, ' ')));
                window.history.replaceState({}, document.title, window.location.pathname);
                setTimeout(() => { window.location.href = LOGIN_URL; }, 4000);
            }
        })();

        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitLoader = document.getElementById('submitLoader');

            errorMessage.classList.add('hidden');
            successMessage.classList.add('hidden');

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (password !== confirmPassword) {
                showError('Passwords do not match');
                return;
            }

            submitBtn.disabled = true;
            submitText.classList.add('hidden');
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

                const data = await response.json();

                if (response.ok && data.success) {
                    showSuccess(data.message || 'Registration submitted! Check your email for confirmation.');
                    document.getElementById('registerForm').reset();
                    setTimeout(() => { window.location.href = LOGIN_URL; }, 3000);
                } else {
                    showError(data.message || 'Registration failed. Please try again.');
                }
            } catch (err) {
                console.error('Registration error:', err);
                showError('Unable to connect to the server. Please try again later.');
            } finally {
                submitBtn.disabled = false;
                submitText.classList.remove('hidden');
                submitLoader.classList.add('hidden');
            }
        });
    </script>

    <?php wp_footer(); ?>
</body>
</html>
