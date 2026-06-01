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
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="h-full">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?> <?php bloginfo('name'); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png">
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png">
    
    <script>
        // Suppress Tailwind CDN production warning
        window.tailwindCDNWarning = false;
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    <?php wp_head(); ?>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50">
    <div class="min-h-screen flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-5xl">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">

                <!-- Left Column — Login Card -->
                <div class="w-full">
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">

                        <!-- Card header with logo -->
                        <div class="bg-gradient-to-br from-indigo-600 to-purple-600 px-8 py-6 flex items-center justify-center">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/agencyhub-by-esirom.png" alt="Agency Hub by Esirom" class="h-20 w-auto">
                        </div>

                        <div class="px-8 py-8 space-y-5">
                            <div>
                                <h1 class="text-xl font-bold text-gray-900">Welcome back</h1>
                                <p class="text-sm text-gray-500 mt-1">Sign in to your account to continue</p>
                            </div>

                            <!-- Alert messages -->
                            <div id="errorMessage" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
                                <p class="text-sm"></p>
                            </div>
                            <div id="successMessage" class="hidden bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg" role="alert">
                                <p class="text-sm"></p>
                            </div>

                            <!-- Google Sign-In -->
                            <a
                                id="googleSignInBtn"
                                href="#"
                                class="w-full flex items-center justify-center gap-3 py-3 px-4 border-2 border-gray-200 rounded-xl text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all"
                            >
                                <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                                Continue with Google
                            </a>

                            <!-- Divider -->
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-gray-200"></div>
                                </div>
                                <div class="relative flex justify-center text-xs">
                                    <span class="px-3 bg-white text-gray-400 uppercase tracking-wide font-medium">or sign in with email</span>
                                </div>
                            </div>

                            <!-- Email / Password Form -->
                            <form id="loginForm" class="space-y-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                                        Email address
                                    </label>
                                    <input
                                        id="email"
                                        name="email"
                                        type="email"
                                        autocomplete="email"
                                        required
                                        class="block w-full px-4 py-2.5 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-sm"
                                        placeholder="you@example.com"
                                    >
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label for="password" class="block text-sm font-medium text-gray-700">
                                            Password
                                        </label>
                                        <button type="button" onclick="showForgotPasswordModal()" class="text-xs font-medium text-indigo-600 hover:text-indigo-500 transition-colors">
                                            Forgot password?
                                        </button>
                                    </div>
                                    <input
                                        id="password"
                                        name="password"
                                        type="password"
                                        autocomplete="current-password"
                                        required
                                        class="block w-full px-4 py-2.5 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-sm"
                                        placeholder="••••••••"
                                    >
                                </div>

                                <div class="flex items-center">
                                    <input
                                        id="remember-me"
                                        name="remember-me"
                                        type="checkbox"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    >
                                    <label for="remember-me" class="ml-2 text-sm text-gray-600">
                                        Remember me
                                    </label>
                                </div>

                                <button
                                    type="submit"
                                    id="submitBtn"
                                    class="w-full flex items-center justify-center gap-2 py-2.5 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span id="btnText">Sign in</span>
                                    <svg id="btnSpinner" class="hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Column — Info panels -->
                <div class="hidden lg:flex flex-col gap-6">
                    <!-- About Agency Hub -->
                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
                        <div class="flex items-start mb-3">
                            <div class="bg-white/20 rounded-xl p-2 mr-3 flex-shrink-0">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold leading-tight">What is Agency Hub?</h3>
                                <p class="text-indigo-200 text-xs mt-0.5">Your all-in-one social media command center</p>
                            </div>
                        </div>
                        <p class="text-sm text-indigo-50 leading-relaxed mb-4">
                            A comprehensive platform to streamline content workflows, track performance metrics, and collaborate seamlessly with your team and clients.
                        </p>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <?php
                            $features = ['Published Posts', 'KPI Tracking', 'Campaign Management', 'Team Collaboration'];
                            foreach ($features as $f): ?>
                            <div class="flex items-center gap-2 bg-white/10 rounded-lg px-3 py-2">
                                <svg class="h-3.5 w-3.5 text-green-300 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="font-medium"><?php echo esc_html($f); ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- What's New -->
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">What's New</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Latest platform updates</p>
                            </div>
                            <span class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full font-semibold" id="currentVersion">v1.0.0</span>
                        </div>
                        <div class="space-y-3" id="updatesContainer">
                            <!-- Updates injected by JS -->
                        </div>
                        <button id="viewMoreBtn" class="hidden w-full mt-4 text-sm text-indigo-600 hover:text-indigo-700 font-medium py-2 hover:bg-indigo-50 rounded-lg transition-colors">
                            View Update History →
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div id="forgotPasswordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Reset Password</h2>
                <button onclick="closeForgotPasswordModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <p class="text-gray-600 mb-6">Enter your email address and we'll send you a link to reset your password.</p>
            
            <form id="forgotPasswordForm" class="space-y-4">
                <!-- Success Message -->
                <div id="forgotSuccessMessage" class="hidden bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg" role="alert">
                    <p class="text-sm"></p>
                </div>
                
                <!-- Error Message -->
                <div id="forgotErrorMessage" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
                    <p class="text-sm"></p>
                </div>
                
                <div>
                    <label for="forgotEmail" class="block text-sm font-medium text-gray-700 mb-2">
                        Email address
                    </label>
                    <input 
                        id="forgotEmail" 
                        name="email" 
                        type="email" 
                        required 
                        class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                        placeholder="you@example.com"
                    >
                </div>
                
                <button 
                    type="submit" 
                    id="forgotSubmitBtn"
                    class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span id="forgotBtnText">Send Reset Link</span>
                    <svg id="forgotBtnSpinner" class="hidden animate-spin ml-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <script>
        // Configuration - Use WordPress localized variable or fallback
        const API_URL = typeof ESIROM_API_URL !== 'undefined' ? ESIROM_API_URL : 'https://esirom-hub-backend-production.up.railway.app/api';

        // Get form elements
        const loginForm = document.getElementById('loginForm');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        const errorMessage = document.getElementById('errorMessage');
        const successMessage = document.getElementById('successMessage');

        function showError(message) {
            errorMessage.querySelector('p').textContent = message;
            errorMessage.classList.remove('hidden');
            successMessage.classList.add('hidden');
            setTimeout(() => errorMessage.classList.add('hidden'), 8000);
        }

        function showSuccess(message) {
            successMessage.querySelector('p').textContent = message;
            successMessage.classList.remove('hidden');
            errorMessage.classList.add('hidden');
        }

        function redirectAfterLogin(user) {
            if (user.role === 'admin' || user.role === 'brand_rep') {
                window.location.href = '<?php echo esc_url(get_permalink(get_page_by_path('workflow'))); ?>';
            } else {
                window.location.href = '<?php echo esc_url(get_permalink(get_page_by_path('workflow'))); ?>?tab=contentBank';
            }
        }

        document.getElementById('googleSignInBtn').addEventListener('click', (e) => {
            e.preventDefault();
            window.location.href = `${API_URL}/auth/google`;
        });

        // Handle Google OAuth redirect callback
        (function handleOAuthRedirect() {
            const params = new URLSearchParams(window.location.search);
            const oauthToken = params.get('token');
            const oauthError = params.get('error');
            const oauthSuccess = params.get('success');

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

            fetch(`${API_URL}/auth/me`, {
                headers: { 'Authorization': `Bearer ${oauthToken}` }
            })
            .then(response => {
                if (!response.ok) throw new Error('Failed to load user profile');
                return response.json();
            })
            .then(data => {
                if (!data || !data.user) throw new Error('Invalid session');
                localStorage.setItem('token', oauthToken);
                localStorage.setItem('user', JSON.stringify(data.user));
                window.history.replaceState({}, document.title, window.location.pathname);
                redirectAfterLogin(data.user);
            })
            .catch(() => {
                showError('Google sign-in completed but the session could not be established. Please try again.');
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        })();

        // Check if already logged in
        const token = localStorage.getItem('token');
        if (token) {
            fetch(`${API_URL}/auth/me`, {
                headers: { 'Authorization': `Bearer ${token}` }
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                }
            })
            .then(data => {
                if (data && data.user) {
                    // Redirect based on role: admins/brand_reps go to Workflow, clients go to dashboard
                    if (data.user.role === 'admin' || data.user.role === 'brand_rep') {
                        window.location.href = '<?php echo esc_url(get_permalink(get_page_by_path('workflow'))); ?>';
                    } else {
                        window.location.href = '<?php echo esc_url(get_permalink(get_page_by_path('workflow'))); ?>?tab=contentBank';
                    }
                }
            })
            .catch(() => {
                localStorage.removeItem('token');
                localStorage.removeItem('user');
            });
        }

        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const email = emailInput.value.trim();
            const password = passwordInput.value;

            if (!email || !password) {
                showError('Please enter both email and password');
                return;
            }

            submitBtn.disabled = true;
            btnText.textContent = 'Signing in...';
            btnSpinner.classList.remove('hidden');
            errorMessage.classList.add('hidden');

            try {
                const response = await fetch(`${API_URL}/auth/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    localStorage.setItem('token', data.token);
                    localStorage.setItem('user', JSON.stringify(data.user));
                    
                    // Check if password change is required
                    if (data.requirePasswordChange) {
                        localStorage.setItem('requirePasswordChange', 'true');
                        btnText.textContent = 'Password change required...';
                    } else {
                        btnText.textContent = 'Success!';
                    }
                    
                    setTimeout(() => {
                        // Redirect based on role: admins/brand_reps go to Workflow, clients go to dashboard
                        if (data.user.role === 'admin' || data.user.role === 'brand_rep') {
                            window.location.href = '<?php echo esc_url(get_permalink(get_page_by_path('workflow'))); ?>';
                        } else {
                            window.location.href = '<?php echo esc_url(get_permalink(get_page_by_path('workflow'))); ?>?tab=contentBank';
                        }
                    }, 500);
                } else {
                    showError(data.message || 'Login failed. Please check your credentials.');
                    submitBtn.disabled = false;
                    btnText.textContent = 'Sign in';
                    btnSpinner.classList.add('hidden');
                }
            } catch (error) {
                console.error('Login error:', error);
                showError('Unable to connect to server. Please try again later.');
                submitBtn.disabled = false;
                btnText.textContent = 'Sign in';
                btnSpinner.classList.add('hidden');
            }
        });

        // Quick login for demo
        document.querySelectorAll('[data-email]').forEach(box => {
            box.addEventListener('click', function() {
                emailInput.value = this.dataset.email;
                passwordInput.value = this.dataset.password;
            });
        });

        // Load platform updates from JSON file
        async function loadPlatformUpdates() {
            try {
                const response = await fetch('<?php echo get_template_directory_uri(); ?>/updates.json?v=<?php echo time(); ?>');
                const updates = await response.json();

                const container = document.getElementById('updatesContainer');
                const versionEl = document.getElementById('currentVersion');
                const viewMoreBtn = document.getElementById('viewMoreBtn');
                let showingAll = false;
                
                if (updates && updates.length > 0) {
                    // Display the latest version
                    versionEl.textContent = 'v' + updates[0].version;
                    
                    // Function to render updates
                    const renderUpdates = (count) => {
                        const updatesToShow = updates.slice(0, count);
                        container.innerHTML = updatesToShow.map((update, index) => `
                            <div class="relative pl-5 ${index < updatesToShow.length - 1 ? 'pb-3 border-l-2 border-indigo-100' : ''}">
                                <div class="absolute left-0 top-1 w-2.5 h-2.5 rounded-full bg-indigo-500 -translate-x-[7px] ring-2 ring-white"></div>
                                <div class="bg-gray-50 rounded-xl p-3.5 hover:bg-indigo-50/50 transition-colors">
                                    <div class="flex items-start justify-between gap-2 mb-1">
                                        <h4 class="font-semibold text-gray-900 text-sm leading-tight">${update.title}</h4>
                                        <span class="text-xs text-indigo-600 font-semibold bg-indigo-100 px-2 py-0.5 rounded-full flex-shrink-0">v${update.version}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 leading-relaxed mb-1.5">${update.description}</p>
                                    <p class="text-xs text-gray-400 flex items-center gap-1">
                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        ${new Date(update.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                                    </p>
                                </div>
                            </div>
                        `).join('');
                    };
                    
                    // Initially show only the most recent update
                    renderUpdates(1);
                    
                    // Show "View More" button if there are more updates
                    if (updates.length > 1) {
                        viewMoreBtn.classList.remove('hidden');
                        
                        viewMoreBtn.addEventListener('click', () => {
                            if (showingAll) {
                                renderUpdates(1);
                                viewMoreBtn.textContent = 'View Update History →';
                                showingAll = false;
                            } else {
                                renderUpdates(updates.length);
                                viewMoreBtn.textContent = '← Show Less';
                                showingAll = true;
                            }
                        });
                    }
                }
            } catch (error) {
                console.error('Error loading updates:', error);
                // Fallback to default message if JSON fails to load
                const container = document.getElementById('updatesContainer');
                container.innerHTML = `
                    <div class="text-center text-gray-500 py-8">
                        <p class="text-sm">Unable to load updates. Please refresh the page.</p>
                    </div>
                `;
            }
        }

        loadPlatformUpdates();

        // Forgot Password Modal Functions
        function showForgotPasswordModal() {
            document.getElementById('forgotPasswordModal').classList.remove('hidden');
            document.getElementById('forgotEmail').value = '';
            document.getElementById('forgotSuccessMessage').classList.add('hidden');
            document.getElementById('forgotErrorMessage').classList.add('hidden');
        }

        function closeForgotPasswordModal() {
            document.getElementById('forgotPasswordModal').classList.add('hidden');
        }

        // Close modal on outside click
        document.getElementById('forgotPasswordModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeForgotPasswordModal();
            }
        });

        // Handle forgot password form submission
        document.getElementById('forgotPasswordForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const email = document.getElementById('forgotEmail').value.trim();
            const submitBtn = document.getElementById('forgotSubmitBtn');
            const btnText = document.getElementById('forgotBtnText');
            const btnSpinner = document.getElementById('forgotBtnSpinner');
            const successMessage = document.getElementById('forgotSuccessMessage');
            const errorMessage = document.getElementById('forgotErrorMessage');

            if (!email) {
                errorMessage.querySelector('p').textContent = 'Please enter your email address';
                errorMessage.classList.remove('hidden');
                return;
            }

            submitBtn.disabled = true;
            btnText.textContent = 'Sending...';
            btnSpinner.classList.remove('hidden');
            successMessage.classList.add('hidden');
            errorMessage.classList.add('hidden');

            try {
                const response = await fetch(`${API_URL}/auth/forgot-password`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    successMessage.querySelector('p').textContent = data.message || 'Password reset link sent! Check your email.';
                    successMessage.classList.remove('hidden');
                    document.getElementById('forgotEmail').value = '';
                    
                    // Close modal after 3 seconds
                    setTimeout(() => {
                        closeForgotPasswordModal();
                    }, 3000);
                } else {
                    errorMessage.querySelector('p').textContent = data.message || 'Failed to send reset link. Please try again.';
                    errorMessage.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Forgot password error:', error);
                errorMessage.querySelector('p').textContent = 'Unable to connect to server. Please try again later.';
                errorMessage.classList.remove('hidden');
            } finally {
                submitBtn.disabled = false;
                btnText.textContent = 'Send Reset Link';
                btnSpinner.classList.add('hidden');
            }
        });
    </script>

    <?php wp_footer(); ?>
</body>
</html>
