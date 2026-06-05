<?php
/**
 * Shared sidebar footer for staff (Update KPIs, Admin Panel, Logout).
 *
 * Expects:
 *   $hub_nav_context  site|dashboard
 *   $hub_nav_alpine_labels bool
 *   $hub_footer_show_logout bool (default true)
 */

if (!defined('ABSPATH')) {
    exit;
}

$hub_nav_context = isset($hub_nav_context) ? $hub_nav_context : 'site';
$hub_nav_alpine_labels = !empty($hub_nav_alpine_labels);
$hub_footer_show_logout = !isset($hub_footer_show_logout) || $hub_footer_show_logout;
$hub_dashboard_url = esc_url(esirom_hub_page_url('dashboard'));
$hub_admin_url = esc_url(esirom_hub_page_url('admin'));
$hub_label_attrs = $hub_nav_alpine_labels ? ' x-show="isSidebarOpen"' : '';
$hub_is_dashboard = ($hub_nav_context === 'dashboard');
$hub_footer_link_class = 'flex items-center px-3 py-2 rounded-lg transition-colors duration-200 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700';
?>

<div class="space-y-0.5">
<?php if ($hub_is_dashboard) : ?>
<a x-show="viewMode !== 'client'" @click.prevent="showKPIUpdateModal = true" href="#" class="<?php echo esc_attr($hub_footer_link_class); ?>">
    <svg class="h-5 w-5 flex-shrink-0 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
    <span class="nav-text"<?php echo $hub_label_attrs; ?>>Update KPIs</span>
</a>
<?php else : ?>
<a x-show="viewMode !== 'client'" href="<?php echo $hub_dashboard_url; ?>" class="<?php echo esc_attr($hub_footer_link_class); ?>" title="Open Insights dashboard to update KPIs">
    <svg class="h-5 w-5 flex-shrink-0 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
    <span class="nav-text"<?php echo $hub_label_attrs; ?>>Update KPIs</span>
</a>
<?php endif; ?>

<a x-show="user?.role === 'admin' && viewMode === 'admin'" href="<?php echo $hub_admin_url; ?>" class="<?php echo esc_attr($hub_footer_link_class); ?>">
    <svg class="h-5 w-5 flex-shrink-0 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.438.995a6.473 6.473 0 010 .255c0 .382.145.755.438.995l1.003.827c.48.398.668 1.05.26 1.431l-1.296 2.247a1.125 1.125 0 01-1.37.49l-1.217-.456c-.355-.133-.75-.072-1.075.124a6.57 6.57 0 01-.22.127c-.332.183-.582.495-.645.87l-.213 1.281c-.09.543-.56.94-1.11.94h-2.593c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.063-.374-.313-.686-.645-.87a6.52 6.52 0 01-.22-.127c-.324-.196-.72-.257-1.075-.124l-1.217.456a1.125 1.125 0 01-1.37-.49l-1.296-2.247a1.125 1.125 0 01.26-1.431l1.003-.827c.293-.24.438-.613.438-.995a6.473 6.473 0 010-.255c0-.382-.145-.755-.438-.995l-1.003-.827a1.125 1.125 0 01-.26-1.431l1.296-2.247a1.125 1.125 0 011.37-.49l1.217.456c.355.133.75.072 1.075-.124a6.57 6.57 0 01.22-.127c.332-.183.582-.495.645-.87l.213-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
    <span class="nav-text"<?php echo $hub_label_attrs; ?>>Admin Panel</span>
</a>

<?php if ($hub_footer_show_logout) : ?>
<a @click.prevent="logout()" href="#" class="<?php echo esc_attr($hub_footer_link_class); ?>">
    <svg class="h-5 w-5 flex-shrink-0 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
    <span class="nav-text"<?php echo $hub_label_attrs; ?>>Logout</span>
</a>
<?php endif; ?>
</div>
