<?php
/**
 * Shared mobile bottom navigation for client view.
 *
 * Expects:
 *   $hub_client_nav_context  site|dashboard
 */

if (!defined('ABSPATH')) {
    exit;
}

$hub_client_nav_context = isset($hub_client_nav_context) ? $hub_client_nav_context : 'site';
$hub_is_dashboard = ($hub_client_nav_context === 'dashboard');

$hub_workflow_url = esc_url(esirom_hub_page_url('workflow')) . '?tab=contentBank';
$hub_dashboard_url = esc_url(esirom_hub_page_url('dashboard'));
$hub_vault_url = esc_url(esirom_hub_page_url('password-vault'));
$hub_calendar_url = esc_url(esirom_hub_page_url('content-calendar'));
$hub_website_projects_url = esc_url(esirom_hub_page_url('website-projects'));

$hub_client_show = esirom_hub_client_nav_show_attr();
$hub_mobile_class = 'flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 dark:text-gray-400 min-w-0';
?>

<a <?php echo $hub_client_show; ?> href="<?php echo $hub_website_projects_url; ?>" class="<?php echo esc_attr($hub_mobile_class); ?>">
    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
    <span class="text-[10px]">Websites</span>
</a>
<a <?php echo $hub_client_show; ?> href="<?php echo $hub_workflow_url; ?>" x-show="typeof isWebsiteOnlyClient === 'undefined' || !isWebsiteOnlyClient" class="<?php echo esc_attr($hub_mobile_class); ?>">
    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
    <span class="text-[10px]">Content</span>
</a>
<?php if ($hub_is_dashboard) : ?>
<a <?php echo $hub_client_show; ?> href="#overview" @click="activeView = 'dashboard'" :class="activeView === 'dashboard' ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400'" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 min-w-0">
    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
    <span class="text-[10px]" :class="activeView === 'dashboard' ? 'font-medium' : ''">Insights</span>
</a>
<a <?php echo $hub_client_show; ?> href="#reports" @click="activeView = 'reports'" :class="activeView === 'reports' ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400'" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 min-w-0">
    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    <span class="text-[10px]" :class="activeView === 'reports' ? 'font-medium' : ''">Reports</span>
</a>
<a <?php echo $hub_client_show; ?> href="#content-calendar" @click="activeView = 'calendar'" :class="activeView === 'calendar' ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400'" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 min-w-0">
    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    <span class="text-[10px]" :class="activeView === 'calendar' ? 'font-medium' : ''">Posts</span>
</a>
<?php else : ?>
<a <?php echo $hub_client_show; ?> href="<?php echo $hub_dashboard_url; ?>#overview" class="<?php echo esc_attr($hub_mobile_class); ?>">
    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
    <span class="text-[10px]">Insights</span>
</a>
<a <?php echo $hub_client_show; ?> href="<?php echo $hub_dashboard_url; ?>#reports" class="<?php echo esc_attr($hub_mobile_class); ?>">
    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    <span class="text-[10px]">Reports</span>
</a>
<a <?php echo $hub_client_show; ?> href="<?php echo $hub_dashboard_url; ?>#content-calendar" class="<?php echo esc_attr($hub_mobile_class); ?>">
    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    <span class="text-[10px]">Posts</span>
</a>
<?php endif; ?>
<a <?php echo $hub_client_show; ?> href="<?php echo $hub_vault_url; ?>" class="<?php echo esc_attr($hub_mobile_class); ?>">
    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
    <span class="text-[10px]">Passwords</span>
</a>
<a <?php echo $hub_client_show; ?> href="<?php echo $hub_calendar_url; ?>" class="<?php echo esc_attr($hub_mobile_class); ?>">
    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    <span class="text-[10px]">Calendar</span>
</a>
