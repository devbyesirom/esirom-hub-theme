<?php
/**
 * Shared sidebar navigation for admin / brand_rep (when viewMode !== 'client').
 *
 * Expects:
 *   $hub_nav_active   string  workflow|overview|insights|reports|event_coverage|published_posts|content_calendar|progress
 *   $hub_nav_context  string  site|dashboard  (dashboard = hash routes + Alpine active states)
 *   $hub_nav_alpine_labels bool  wrap labels in x-show="isSidebarOpen"
 */

if (!defined('ABSPATH')) {
    exit;
}

$hub_nav_active = isset($hub_nav_active) ? $hub_nav_active : '';
$hub_nav_context = isset($hub_nav_context) ? $hub_nav_context : 'site';
$hub_nav_alpine_labels = !empty($hub_nav_alpine_labels);

$hub_dashboard_url = esc_url(get_permalink(get_page_by_path('dashboard')));
$hub_workflow_url = esc_url(get_permalink(get_page_by_path('workflow')));
$hub_overview_url = esc_url(get_permalink(get_page_by_path('overview')));
$hub_calendar_url = esc_url(get_permalink(get_page_by_path('content-calendar')));
$hub_progress_url = esc_url(get_permalink(get_page_by_path('progress')));

$hub_is_dashboard = ($hub_nav_context === 'dashboard');

$hub_nav_item_class = function ($key) use ($hub_nav_active, $hub_is_dashboard) {
    $base = 'flex items-center p-3 rounded-lg transition-colors duration-200';
    if ($hub_nav_active !== $key) {
        return $base . ' text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700';
    }
    if ($hub_is_dashboard && $key === 'published_posts') {
        return $base . ' bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400';
    }
    return $base . ' bg-indigo-500 text-white';
};

$hub_label_attrs = $hub_nav_alpine_labels ? ' x-show="isSidebarOpen"' : '';
$hub_staff_show = 'x-show="viewMode !== \'client\'"';
?>

<!-- ADMIN/BRAND REP VIEW NAVIGATION (shared) -->
<a <?php echo $hub_staff_show; ?>
   href="<?php echo $hub_nav_active === 'workflow' && !$hub_is_dashboard ? '#' : $hub_workflow_url; ?>"
   class="<?php echo esc_attr($hub_nav_item_class('workflow')); ?>">
    <svg class="h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
    <span class="ml-4 nav-text"<?php echo $hub_label_attrs; ?>>Workflow</span>
</a>

<a <?php echo $hub_staff_show; ?> href="<?php echo $hub_overview_url; ?>"
   class="<?php echo esc_attr($hub_nav_item_class('overview')); ?>">
    <svg class="h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
    <span class="ml-4 nav-text"<?php echo $hub_label_attrs; ?>>Agency Overview</span>
</a>

<?php if ($hub_is_dashboard) : ?>
<a <?php echo $hub_staff_show; ?> href="#overview" @click="activeView = 'dashboard'"
   :class="activeView === 'dashboard' ? 'bg-indigo-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
   class="flex items-center p-3 rounded-lg transition-colors duration-200">
    <svg class="h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
    <span class="ml-4 nav-text">Insights</span>
</a>
<a <?php echo $hub_staff_show; ?> href="#reports" @click="activeView = 'reports'"
   :class="activeView === 'reports' ? 'bg-indigo-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
   class="flex items-center p-3 rounded-lg transition-colors duration-200">
    <svg class="h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
    <span class="ml-4 nav-text">Reports</span>
</a>
<a <?php echo $hub_staff_show; ?> href="#event-coverage" @click="activeView = 'eventCoverage'"
   :class="activeView === 'eventCoverage' ? 'bg-indigo-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
   class="flex items-center p-3 rounded-lg transition-colors duration-200">
    <svg class="h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h7.5m-7.5 3h4.5m-6 7.5h10.5A2.25 2.25 0 0019.5 15V6A2.25 2.25 0 0017.25 3.75H6.75A2.25 2.25 0 004.5 6v9a2.25 2.25 0 002.25 2.25zm0 0v2.25m10.5-2.25v2.25" /></svg>
    <span class="ml-4 nav-text">Event Coverage</span>
</a>
<a <?php echo $hub_staff_show; ?> href="#content-calendar" @click="activeView = 'calendar'"
   class="flex items-center p-3 rounded-lg transition-colors duration-200"
   :class="activeView === 'calendar' ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'">
    <svg class="h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
    <span class="ml-4 nav-text">Published Posts</span>
</a>
<?php else : ?>
<a <?php echo $hub_staff_show; ?> href="<?php echo $hub_dashboard_url; ?>"
   class="<?php echo esc_attr($hub_nav_item_class('insights')); ?>">
    <svg class="h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
    <span class="ml-4 nav-text"<?php echo $hub_label_attrs; ?>>Insights</span>
</a>
<a <?php echo $hub_staff_show; ?> href="<?php echo $hub_dashboard_url; ?>#reports"
   class="<?php echo esc_attr($hub_nav_item_class('reports')); ?>">
    <svg class="h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
    <span class="ml-4 nav-text"<?php echo $hub_label_attrs; ?>>Reports</span>
</a>
<a <?php echo $hub_staff_show; ?> href="<?php echo $hub_dashboard_url; ?>#event-coverage"
   class="<?php echo esc_attr($hub_nav_item_class('event_coverage')); ?>">
    <svg class="h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h7.5m-7.5 3h4.5m-6 7.5h10.5A2.25 2.25 0 0019.5 15V6A2.25 2.25 0 0017.25 3.75H6.75A2.25 2.25 0 004.5 6v9a2.25 2.25 0 002.25 2.25zm0 0v2.25m10.5-2.25v2.25" /></svg>
    <span class="ml-4 nav-text"<?php echo $hub_label_attrs; ?>>Event Coverage</span>
</a>
<a <?php echo $hub_staff_show; ?> href="<?php echo $hub_dashboard_url; ?>#content-calendar"
   class="<?php echo esc_attr($hub_nav_item_class('published_posts')); ?>">
    <svg class="h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
    <span class="ml-4 nav-text"<?php echo $hub_label_attrs; ?>>Published Posts</span>
</a>
<?php endif; ?>

<a <?php echo $hub_staff_show; ?>
   href="<?php echo ($hub_nav_active === 'content_calendar' && !$hub_is_dashboard) ? '#' : $hub_calendar_url; ?>"
   class="<?php echo esc_attr($hub_nav_item_class('content_calendar')); ?>">
    <svg class="h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
    <span class="ml-4 nav-text"<?php echo $hub_label_attrs; ?>>Content Calendar</span>
</a>

<a <?php echo $hub_staff_show; ?> href="<?php echo $hub_progress_url; ?>"
   class="<?php echo esc_attr($hub_nav_item_class('progress')); ?>">
    <svg class="h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
    <span class="ml-4 nav-text"<?php echo $hub_label_attrs; ?>>My Progress</span>
</a>
