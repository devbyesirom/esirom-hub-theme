<?php
/**
 * Template Name: Registration Page
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
    <title>Register - <?php bloginfo('name'); ?></title>
    
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
        body { font-family: 'Inter', sans-serif; }
    </style>
    <?php wp_head(); ?>
</head>
<body class="h-full bg-gradient-to-br from-indigo-50 via-white to-purple-50">
    <div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo -->
            <div class="text-center">
                <img src="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png" alt="Agency Hub" class="mx-auto h-16 w-auto">
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Create your account
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Already have an account?
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('login'))); ?>" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Sign in
                    </a>
                </p>
            </div>

            <!-- Registration Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <form id="registerForm" class="space-y-6">
                    <!-- First Name -->
                    <div>
                        <label for="firstName" class="block text-sm font-medium text-gray-700">
                            First Name *
                        </label>
                        <input 
                            id="firstName" 
                            name="firstName" 
                            type="text" 
                            required 
                            class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="John"
                        >
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="lastName" class="block text-sm font-medium text-gray-700">
                            Last Name *
                        </label>
                        <input 
                            id="lastName" 
                            name="lastName" 
                            type="text" 
                            required 
                            class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Doe"
                        >
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email Address *
                        </label>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            required 
                            class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="john@company.com"
                        >
                    </div>

                    <!-- Company Name -->
                    <div>
                        <label for="companyName" class="block text-sm font-medium text-gray-700">
                            Company/Brand Name *
                        </label>
                        <input 
                            id="companyName" 
                            name="companyName" 
                            type="text" 
                            required 
                            class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Your Company"
                        >
                    </div>

                    <!-- Role Selection -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">
                            I am a *
                        </label>
                        <select 
                            id="role" 
                            name="role" 
                            required 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="client">Client</option>
                            <option value="brand_rep">Brand Representative</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Select your role. Brand Representatives manage multiple client accounts.</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password *
                        </label>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required 
                            minlength="6"
                            class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Min. 6 characters"
                        >
                        <p class="mt-1 text-xs text-gray-500">Must be at least 6 characters</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="confirmPassword" class="block text-sm font-medium text-gray-700">
                            Confirm Password *
                        </label>
                        <input 
                            id="confirmPassword" 
                            name="confirmPassword" 
                            type="password" 
                            required 
                            minlength="6"
                            class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Confirm your password"
                        >
                    </div>

                    <!-- Registration Note (Optional) -->
                    <div>
                        <label for="registrationNote" class="block text-sm font-medium text-gray-700">
                            Additional Information (Optional)
                        </label>
                        <textarea 
                            id="registrationNote" 
                            name="registrationNote" 
                            rows="3"
                            class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Tell us about your needs or any special requirements..."
                        ></textarea>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" class="hidden p-4 rounded-lg bg-red-50 border border-red-200">
                        <p class="text-sm text-red-800"></p>
                    </div>

                    <!-- Success Message -->
                    <div id="successMessage" class="hidden p-4 rounded-lg bg-green-50 border border-green-200">
                        <p class="text-sm text-green-800"></p>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button 
                            type="submit" 
                            id="submitBtn"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                        >
                            <span id="submitText">Create Account</span>
                            <span id="submitLoader" class="hidden">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>

                    <p class="text-xs text-center text-gray-500">
                        By registering, you agree that your account will be reviewed by our team. You'll receive an email once approved.
                    </p>
                </form>
            </div>

            <!-- Back to Home -->
            <div class="text-center">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Back to home
                </a>
            </div>
        </div>
    </div>

    <script>
        const API_URL = typeof ESIROM_API_URL !== 'undefined' ? ESIROM_API_URL : 'https://esirom-hub-backend-production.up.railway.app/api';

        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitLoader = document.getElementById('submitLoader');
            const errorMessage = document.getElementById('errorMessage');
            const successMessage = document.getElementById('successMessage');

            // Hide messages
            errorMessage.classList.add('hidden');
            successMessage.classList.add('hidden');

            // Get form data
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const email = document.getElementById('email').value.trim();
            const companyName = document.getElementById('companyName').value.trim();
            const role = document.getElementById('role').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const registrationNote = document.getElementById('registrationNote').value.trim();

            // Validate passwords match
            if (password !== confirmPassword) {
                errorMessage.querySelector('p').textContent = 'Passwords do not match';
                errorMessage.classList.remove('hidden');
                return;
            }

            // Disable button and show loader
            submitBtn.disabled = true;
            submitText.classList.add('hidden');
            submitLoader.classList.remove('hidden');

            try {
                const response = await fetch(`${API_URL}/auth/register-request`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        firstName,
                        lastName,
                        email,
                        companyName,
                        role,
                        password,
                        registrationNote
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Show success message
                    successMessage.querySelector('p').textContent = data.message;
                    successMessage.classList.remove('hidden');

                    // Reset form
                    document.getElementById('registerForm').reset();

                    // Redirect to login after 3 seconds
                    setTimeout(() => {
                        window.location.href = '<?php echo esc_url(get_permalink(get_page_by_path('login'))); ?>';
                    }, 3000);
                } else {
                    // Show error message
                    errorMessage.querySelector('p').textContent = data.message || 'Registration failed. Please try again.';
                    errorMessage.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Registration error:', error);
                errorMessage.querySelector('p').textContent = 'An error occurred. Please try again later.';
                errorMessage.classList.remove('hidden');
            } finally {
                // Re-enable button and hide loader
                submitBtn.disabled = false;
                submitText.classList.remove('hidden');
                submitLoader.classList.add('hidden');
            }
        });
    </script>

    <?php wp_footer(); ?>
</body>
</html>
