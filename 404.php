<?php
/**
 * 404 Error Page
 *
 * @package Esirom_Client_Hub
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Hide WordPress admin bar
show_admin_bar(false);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="h-full">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | <?php bloginfo('name'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    <?php wp_head(); ?>
</head>
<body class="h-full bg-gradient-to-br from-indigo-50 via-white to-purple-50">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full text-center">
            <!-- Logo -->
            <div class="flex justify-center mb-8">
                <img src="<?php echo get_template_directory_uri(); ?>/img/agencyhub-by-esirom.png" alt="Agency Hub by Esirom" class="h-20 w-auto">
            </div>

            <!-- 404 Illustration -->
            <div class="mb-8">
                <svg class="mx-auto h-64 w-64 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <!-- Error Message -->
            <h1 class="text-9xl font-bold text-indigo-600 mb-4">404</h1>
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Page Not Found</h2>
            <p class="text-lg text-gray-600 mb-8">
                Oops! The page you're looking for doesn't exist. It might have been moved or deleted.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Go Home
                </a>
                <?php
                $dashboard_page = get_page_by_path('dashboard');
                if ($dashboard_page) :
                ?>
                <a href="<?php echo get_permalink($dashboard_page->ID); ?>" class="inline-flex items-center justify-center px-6 py-3 border-2 border-indigo-600 text-base font-medium rounded-lg text-indigo-600 bg-white hover:bg-indigo-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Go to Dashboard
                </a>
                <?php endif; ?>
            </div>

            <!-- Helpful Links -->
            <div class="mt-12 pt-8 border-t border-gray-200">
                <p class="text-sm text-gray-500 mb-4">You might be looking for:</p>
                <div class="flex flex-wrap gap-4 justify-center text-sm">
                    <?php
                    $pages = get_pages(array('number' => 5));
                    foreach ($pages as $page) :
                    ?>
                    <a href="<?php echo get_permalink($page->ID); ?>" class="text-indigo-600 hover:text-indigo-800 hover:underline">
                        <?php echo esc_html($page->post_title); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Support -->
            <div class="mt-8">
                <p class="text-sm text-gray-600">
                    Need help? 
                    <a href="mailto:support@esirom.com" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Contact Support
                    </a>
                </p>
            </div>
        </div>
    </div>
    <?php wp_footer(); ?>
</body>
</html>
