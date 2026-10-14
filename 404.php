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

$home_url = esirom_hub_page_url('login');
$dashboard_page = get_page_by_path('dashboard');
$workflow_page = get_page_by_path('workflow');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="h-full">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <title>Page not found | <?php bloginfo('name'); ?></title>
    <?php esirom_hub_head_assets(); ?>
    <?php wp_head(); ?>
</head>
<body class="min-h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased">
    <main class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-lg w-full text-center">
            <div class="flex justify-center mb-10">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/agencyhub-by-esirom.png" alt="Agency Hub by Esirom" class="h-16 w-auto">
            </div>

            <p class="hub-eyebrow">Error 404</p>
            <h1 class="mt-3 text-3xl sm:text-4xl font-bold tracking-tight">We can't find that page</h1>
            <p class="mt-4 text-base text-gray-600 dark:text-gray-300 leading-relaxed">
                The page you're looking for doesn't exist, or it may have been moved. Check the address, or head back to the Hub.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
                <a href="<?php echo esc_url($home_url); ?>" class="hub-btn hub-btn--primary hub-btn--lg">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Go to the Hub
                </a>
                <?php if ($workflow_page) : ?>
                <a href="<?php echo esc_url(get_permalink($workflow_page->ID)); ?>" class="hub-btn hub-btn--secondary hub-btn--lg">Open Workflow</a>
                <?php elseif ($dashboard_page) : ?>
                <a href="<?php echo esc_url(get_permalink($dashboard_page->ID)); ?>" class="hub-btn hub-btn--secondary hub-btn--lg">Open Insights</a>
                <?php endif; ?>
            </div>

            <p class="mt-10 text-sm text-gray-500 dark:text-gray-400">
                Need help? <a href="mailto:support@esirom.com" class="font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-300">Contact support</a>
            </p>
        </div>
    </main>
    <?php wp_footer(); ?>
</body>
</html>
