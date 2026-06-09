<?php
/**
 * Shared sidebar navigation for client view (viewMode === 'client').
 *
 * Expects:
 *   $hub_client_nav_active   content_bank|password_vault|published_posts|overview|reports|event_coverage|content_calendar
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
$hub_is_dashboard = ($hub_client_nav_context === 'dashboard');

$hub_client_show = 'x-show="viewMode === \'client\'"';

$hub_link_class = function ($key) use ($hub_client_nav_active) {
    $base = 'flex items-center p-3 rounded-lg transition-colors duration-200';
    if ($hub_client_nav_active === $key) {
        return $base . ' bg-indigo-500 text-white';
    }
    return $base . ' text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700';
};
?>

<a <?php echo $hub_client_show; ?> href="<?php echo $hub_workflow_url; ?>" class="<?php echo esc_attr($hub_link_class('content_bank')); ?>">
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
    <span class="ml-4 nav-text"<?php echo $hub_label_attrs; ?>>Content Bank</span>
</a>

<a <?php echo $hub_client_show; ?> href="<?php echo $hub_vault_url; ?>" class="<?php echo esc_attr($hub_link_class('password_vault')); ?>">
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
    <span class="ml-4 nav-text"<?php echo $hub_label_attrs; ?>>Password Vault</span>
</a>

<?php if ($hub_is_dashboard) : ?>
<a <?php echo $hub_client_show; ?> href="#content-calendar" @click="activeView = 'calendar'" class="flex items-center p-3 rounded-lg transition-colors duration-200" :class="activeView === 'calendar' ? 'bg-indigo-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'">
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"></path></svg>
    <span class="ml-4 nav-text"<?php echo $hub_label_attrs; ?>>Published Posts</span>
</a>
<a <?php echo $hub_client_show; ?> href="#overview" @click="activeView = 'dashboard'" class="flex items-center p-3 rounded-lg transition-colors duration-200" :class="activeView === 'dashboard' ? 'bg-indigo-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'">
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
    <span class="ml-4 nav-text"<?php echo $hub_label_attrs; ?>>Page Overview</span>
</a>
<a <?php echo $hub_client_show; ?> href="#reports" @click="activeView = 'reports'" class="flex items-center p-3 rounded-lg transition-colors duration-200" :class="activeView === 'reports' ? 'bg-indigo-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'">
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
    <span class="ml-4 nav-text"<?php echo $hub_label_attrs; ?>>Reports</span>
</a>
<a <?php echo $hub_client_show; ?> href="#event-coverage" @click="activeView = 'eventCoverage'" class="flex items-center p-3 rounded-lg transition-colors duration-200" :class="activeView === 'eventCoverage' ? 'bg-indigo-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'">
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 6.75h7.5m-7.5 3h4.5m-6 7.5h10.5A2.25 2.25 0 0019.5 15V6A2.25 2.25 0 0017.25 3.75H6.75A2.25 2.25 0 004.5 6v9a2.25 2.25 0 002.25 2.25zm0 0v2.25m10.5-2.25v2.25"></path></svg>
    <span class="ml-4 nav-text"<?php echo $hub_label_attrs; ?>>Event Coverage</span>
</a>
<?php else : ?>
<a <?php echo $hub_client_show; ?> href="<?php echo $hub_dashboard_url; ?>#content-calendar" class="<?php echo esc_attr($hub_link_class('published_posts')); ?>">
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"></path></svg>
    <span class="ml-4 nav-text"<?php echo $hub_label_attrs; ?>>Published Posts</span>
</a>
<a <?php echo $hub_client_show; ?> href="<?php echo $hub_dashboard_url; ?>#overview" class="<?php echo esc_attr($hub_link_class('overview')); ?>">
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
    <span class="ml-4 nav-text"<?php echo $hub_label_attrs; ?>>Page Overview</span>
</a>
<a <?php echo $hub_client_show; ?> href="<?php echo $hub_dashboard_url; ?>#reports" class="<?php echo esc_attr($hub_link_class('reports')); ?>">
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
    <span class="ml-4 nav-text"<?php echo $hub_label_attrs; ?>>Reports</span>
</a>
<a <?php echo $hub_client_show; ?> href="<?php echo $hub_dashboard_url; ?>#event-coverage" class="<?php echo esc_attr($hub_link_class('event_coverage')); ?>">
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 6.75h7.5m-7.5 3h4.5m-6 7.5h10.5A2.25 2.25 0 0019.5 15V6A2.25 2.25 0 0017.25 3.75H6.75A2.25 2.25 0 004.5 6v9a2.25 2.25 0 002.25 2.25zm0 0v2.25m10.5-2.25v2.25"></path></svg>
    <span class="ml-4 nav-text"<?php echo $hub_label_attrs; ?>>Event Coverage</span>
</a>
<?php endif; ?>

<a <?php echo $hub_client_show; ?> href="<?php echo $hub_calendar_url; ?>" class="<?php echo esc_attr($hub_link_class('content_calendar')); ?>">
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
    <span class="ml-4 nav-text"<?php echo $hub_label_attrs; ?>>Content Calendar</span>
</a>
