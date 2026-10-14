<?php
/**
 * The main template file (fallback for any non-Hub request).
 *
 * The Hub is made of page templates (login, dashboard, workflow, …), so this
 * file is only reached for stray URLs such as the blog index. It presents a
 * small branded landing card that points people to the sign-in page.
 *
 * @package Esirom_Client_Hub
 */

if (!defined('ABSPATH')) {
    exit;
}

show_admin_bar(false);

$login_url = esirom_hub_page_url('login');
$version = wp_get_theme()->get('Version');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="h-full">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <title><?php bloginfo('name'); ?></title>
    <?php esirom_hub_head_assets(); ?>
    <?php wp_head(); ?>
</head>
<body <?php body_class('min-h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased'); ?>>
    <?php wp_body_open(); ?>
    <main class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="hub-card hub-card--elevated max-w-md w-full p-8 text-center">
            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/agencyhub-by-esirom.png" alt="Agency Hub by Esirom" class="h-16 w-auto mx-auto">
            <h1 class="mt-6 text-2xl font-bold tracking-tight">Agency Hub</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                The Esirom client portal for content approvals, insights, reports and the production workflow.
            </p>
            <a href="<?php echo esc_url($login_url); ?>" class="hub-btn hub-btn--primary hub-btn--lg hub-btn--block mt-6">Sign in to the Hub</a>
            <?php if (current_user_can('manage_options')) : ?>
            <p class="mt-6 text-xs text-gray-500 dark:text-gray-400">
                Theme v<?php echo esc_html($version); ?> &middot; <a href="<?php echo esc_url(admin_url('themes.php?page=esirom-hub-settings')); ?>" class="underline underline-offset-2">Hub settings</a>
            </p>
            <?php endif; ?>
        </div>
    </main>
    <?php wp_footer(); ?>
</body>
</html>
