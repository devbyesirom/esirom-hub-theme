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
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="h-full">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | <?php bloginfo('name'); ?></title>
    
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
<body class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50">
    <div class="min-h-screen flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="flex justify-center mb-6">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/agencyhub-by-esirom.png" alt="Agency Hub by Esirom" class="h-32 w-auto">
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Reset Your Password</h1>
                <p class="mt-2 text-sm text-gray-600">Enter your new password below</p>
            </div>

            <!-- Reset Password Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <form id="resetPasswordForm" class="space-y-6">
                    <!-- Success Message -->
                    <div id="successMessage" class="hidden bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg" role="alert">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-sm font-medium"></p>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
                        <p class="text-sm"></p>
                    </div>

                    <!-- New Password Input -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            New Password
                        </label>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required 
                            minlength="6"
                            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            placeholder="Enter new password (min 6 characters)"
                        >
                    </div>

                    <!-- Confirm Password Input -->
                    <div>
                        <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password
                        </label>
                        <input 
                            id="confirmPassword" 
                            name="confirmPassword" 
                            type="password" 
                            required 
                            minlength="6"
                            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            placeholder="Confirm new password"
                        >
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button 
                            type="submit" 
                            id="submitBtn"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <span id="btnText">Reset Password</span>
                            <svg id="btnSpinner" class="hidden animate-spin ml-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Back to Login Link -->
                <div class="mt-6 text-center">
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('login'))); ?>" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                        ← Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Configuration
        const API_URL = typeof ESIROM_API_URL !== 'undefined' ? ESIROM_API_URL : 'https://esirom-hub-backend-production.up.railway.app/api';
        const LOGIN_URL = '<?php echo esc_url(get_permalink(get_page_by_path('login'))); ?>';
        const DASHBOARD_URL = '<?php echo esc_url(get_permalink(get_page_by_path('dashboard'))); ?>';

        // Get token from URL
        const urlParams = new URLSearchParams(window.location.search);
        const resetToken = urlParams.get('token');

        // Get form elements
        const resetForm = document.getElementById('resetPasswordForm');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        const errorMessage = document.getElementById('errorMessage');
        const successMessage = document.getElementById('successMessage');

        // Check if token exists
        if (!resetToken) {
            errorMessage.querySelector('p').textContent = 'Invalid or missing reset token. Please request a new password reset link.';
            errorMessage.classList.remove('hidden');
            submitBtn.disabled = true;
        }

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

        resetForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            // Validate passwords
            if (password.length < 6) {
                showError('Password must be at least 6 characters long');
                return;
            }

            if (password !== confirmPassword) {
                showError('Passwords do not match');
                return;
            }

            submitBtn.disabled = true;
            btnText.textContent = 'Resetting...';
            btnSpinner.classList.remove('hidden');
            errorMessage.classList.add('hidden');
            successMessage.classList.add('hidden');

            try {
                const response = await fetch(`${API_URL}/auth/reset-password`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        token: resetToken,
                        password: password 
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Store token and redirect to dashboard
                    localStorage.setItem('token', data.token);
                    
                    showSuccess('Password reset successful! Redirecting to dashboard...');
                    btnText.textContent = 'Success!';
                    
                    setTimeout(() => {
                        window.location.href = DASHBOARD_URL;
                    }, 2000);
                } else {
                    showError(data.message || 'Failed to reset password. The link may have expired.');
                    submitBtn.disabled = false;
                    btnText.textContent = 'Reset Password';
                    btnSpinner.classList.add('hidden');
                }
            } catch (error) {
                console.error('Reset password error:', error);
                showError('Unable to connect to server. Please try again later.');
                submitBtn.disabled = false;
                btnText.textContent = 'Reset Password';
                btnSpinner.classList.add('hidden');
            }
        });
    </script>

    <?php wp_footer(); ?>
</body>
</html>
