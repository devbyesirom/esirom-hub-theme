<?php
/**
 * Shared sidebar navigation for client view (viewMode === 'client').
 *
 * Expects:
 *   $hub_client_nav_active   content_bank|password_vault|published_posts|overview|reports|event_coverage|content_calendar|website_projects
 *   $hub_client_nav_context    site|dashboard
 *   $hub_nav_alpine_labels     bool
 */

if (!defined('ABSPATH')) {
    exit;
}

$hub_client_nav_active = isset($hub_client_nav_active) ? $hub_client_nav_active : '';
$hub_client_nav_context = isset($hub_client_nav_context) ? $hub_client_nav_context : 'site';
$hub_nav_alpine_labels = !empty($hub_nav_alpine_labels);
$hub_label_attrs = $hub_nav_alpine_labels ? ' x-show="isSidebarOpen"' : '';

$hub_workflow_url = esc_url(esirom_hub_page_url('workflow')) . '?tab=contentBank';
$hub_dashboard_url = esc_url(esirom_hub_page_url('dashboard'));
$hub_vault_url = esc_url(esirom_hub_page_url('password-vault'));
$hub_calendar_url = esc_url(esirom_hub_page_url('content-calendar'));
$hub_website_projects_url = esc_url(esirom_hub_page_url('website-projects'));
$hub_is_dashboard = ($hub_client_nav_context === 'dashboard');

$hub_client_show = esirom_hub_client_nav_show_attr();
$hub_website_projects_show = 'x-show="$store.clientNav.hasWebsiteProjects"';

$hub_link_class = function ($key) use ($hub_client_nav_active) {
    return 'hub-nav-link' . ($hub_client_nav_active === $key ? ' is-active' : '');
};

$hub_current = function ($key) use ($hub_client_nav_active) {
    return $hub_client_nav_active === $key ? ' aria-current="page"' : '';
};

$hub_view_class = function ($view) {
    return ":class=\"activeView === '{$view}' ? 'is-active' : ''\"";
};
?>

<div <?php echo $hub_client_show; ?> class="space-y-3">

    <div class="space-y-0.5">
        <p class="hub-nav-group-title"<?php echo $hub_label_attrs; ?>><span>Content</span></p>
        <a href="<?php echo $hub_workflow_url; ?>" class="<?php echo esc_attr($hub_link_class('content_bank')); ?>"<?php echo $hub_current('content_bank'); ?>>
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span class="nav-text"<?php echo $hub_label_attrs; ?>>Content Bank</span>
        </a>
        <a <?php echo $hub_website_projects_show; ?> href="<?php echo $hub_website_projects_url; ?>" class="<?php echo esc_attr($hub_link_class('website_projects')); ?>"<?php echo $hub_current('website_projects'); ?>>
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"/></svg>
            <span class="nav-text"<?php echo $hub_label_attrs; ?>>Website Projects</span>
        </a>
    </div>

    <div x-show="typeof isWebsiteOnlyClient === 'undefined' || !isWebsiteOnlyClient" class="space-y-0.5">
        <p class="hub-nav-group-title"<?php echo $hub_label_attrs; ?>><span>Performance</span></p>
        <?php if ($hub_is_dashboard) : ?>
        <a href="#overview" @click="activeView = 'dashboard'" class="hub-nav-link" <?php echo $hub_view_class('dashboard'); ?>>
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="nav-text"<?php echo $hub_label_attrs; ?>>Insights</span>
        </a>
        <a href="#reports" @click="activeView = 'reports'" class="hub-nav-link" <?php echo $hub_view_class('reports'); ?>>
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span class="nav-text"<?php echo $hub_label_attrs; ?>>Reports</span>
        </a>
        <a href="#event-coverage" @click="activeView = 'eventCoverage'" class="hub-nav-link" <?php echo $hub_view_class('eventCoverage'); ?>>
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h7.5m-7.5 3h4.5m-6 7.5h10.5A2.25 2.25 0 0019.5 15V6A2.25 2.25 0 0017.25 3.75H6.75A2.25 2.25 0 004.5 6v9a2.25 2.25 0 002.25 2.25zm0 0v2.25m10.5-2.25v2.25"/></svg>
            <span class="nav-text"<?php echo $hub_label_attrs; ?>>Event Coverage</span>
        </a>
        <a href="#content-calendar" @click="activeView = 'calendar'" class="hub-nav-link" <?php echo $hub_view_class('calendar'); ?>>
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
            <span class="nav-text"<?php echo $hub_label_attrs; ?>>Published Posts</span>
        </a>
        <?php else : ?>
        <a href="<?php echo $hub_dashboard_url; ?>#overview" class="<?php echo esc_attr($hub_link_class('overview')); ?>"<?php echo $hub_current('overview'); ?>>
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="nav-text"<?php echo $hub_label_attrs; ?>>Insights</span>
        </a>
        <a href="<?php echo $hub_dashboard_url; ?>#reports" class="<?php echo esc_attr($hub_link_class('reports')); ?>"<?php echo $hub_current('reports'); ?>>
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span class="nav-text"<?php echo $hub_label_attrs; ?>>Reports</span>
        </a>
        <a href="<?php echo $hub_dashboard_url; ?>#event-coverage" class="<?php echo esc_attr($hub_link_class('event_coverage')); ?>"<?php echo $hub_current('event_coverage'); ?>>
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h7.5m-7.5 3h4.5m-6 7.5h10.5A2.25 2.25 0 0019.5 15V6A2.25 2.25 0 0017.25 3.75H6.75A2.25 2.25 0 004.5 6v9a2.25 2.25 0 002.25 2.25zm0 0v2.25m10.5-2.25v2.25"/></svg>
            <span class="nav-text"<?php echo $hub_label_attrs; ?>>Event Coverage</span>
        </a>
        <a href="<?php echo $hub_dashboard_url; ?>#content-calendar" class="<?php echo esc_attr($hub_link_class('published_posts')); ?>"<?php echo $hub_current('published_posts'); ?>>
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
            <span class="nav-text"<?php echo $hub_label_attrs; ?>>Published Posts</span>
        </a>
        <?php endif; ?>

        <a href="<?php echo $hub_calendar_url; ?>" class="<?php echo esc_attr($hub_link_class('content_calendar')); ?>"<?php echo $hub_current('content_calendar'); ?>>
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
            <span class="nav-text"<?php echo $hub_label_attrs; ?>>Content Calendar</span>
        </a>
    </div>

    <div class="space-y-0.5">
        <p class="hub-nav-group-title"<?php echo $hub_label_attrs; ?>><span>Account</span></p>
        <a href="<?php echo $hub_vault_url; ?>" class="<?php echo esc_attr($hub_link_class('password_vault')); ?>"<?php echo $hub_current('password_vault'); ?>>
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
            <span class="nav-text"<?php echo $hub_label_attrs; ?>>Password Vault</span>
        </a>
    </div>
</div>
