<?php
/**
 * Shared sidebar navigation for admin / brand_rep (when viewMode !== 'client').
 * Grouped into collapsible sections to save vertical space.
 *
 * Expects:
 *   $hub_nav_active   string  workflow|overview|insights|reports|event_coverage|published_posts|content_calendar|progress|inventory
 *   $hub_nav_context  string  site|dashboard
 *   $hub_nav_alpine_labels bool
 */

if (!defined('ABSPATH')) {
    exit;
}

$hub_nav_active = isset($hub_nav_active) ? $hub_nav_active : '';
$hub_nav_context = isset($hub_nav_context) ? $hub_nav_context : 'site';
$hub_nav_alpine_labels = !empty($hub_nav_alpine_labels);

$hub_dashboard_url = esc_url(esirom_hub_page_url('dashboard'));
$hub_workflow_url = esc_url(esirom_hub_page_url('workflow'));
$hub_overview_url = esc_url(esirom_hub_page_url('overview'));
$hub_calendar_url = esc_url(esirom_hub_page_url('content-calendar'));
$hub_inventory_url = esc_url(esirom_hub_page_url('inventory'));
$hub_progress_url = esc_url(esirom_hub_page_url('progress'));

$hub_is_dashboard = ($hub_nav_context === 'dashboard');
$hub_label_attrs = $hub_nav_alpine_labels ? ' x-show="isSidebarOpen"' : '';
$hub_staff_show = 'x-show="viewMode !== \'client\'"';

$hub_group_has_active = function (array $keys) use ($hub_nav_active) {
    return in_array($hub_nav_active, $keys, true);
};

$hub_item_class = function ($key) use ($hub_nav_active, $hub_is_dashboard) {
    $base = 'flex items-center px-3 py-2 rounded-lg transition-colors duration-200 text-sm';
    if ($hub_nav_active !== $key) {
        return $base . ' text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700';
    }
    if ($hub_is_dashboard && $key === 'published_posts') {
        return $base . ' bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 font-medium';
    }
    return $base . ' bg-indigo-500 text-white font-medium';
};

$hub_sub_class = function ($key) use ($hub_item_class) {
    return $hub_item_class($key) . ' ml-2';
};

$hub_group_header_class = function (array $keys) use ($hub_group_has_active) {
    $base = 'flex items-center justify-between w-full px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-colors';
    if ($hub_group_has_active($keys)) {
        return $base . ' text-indigo-600 dark:text-indigo-400';
    }
    return $base . ' text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300';
};

$hub_group_init_open = function ($storage_key, array $keys) use ($hub_group_has_active) {
    if ($hub_group_has_active($keys)) {
        return 'true';
    }
    return "(localStorage.getItem('{$storage_key}') === '1')";
};
?>

<div <?php echo $hub_staff_show; ?> class="space-y-2">

    <!-- ── Workflow ─────────────────────────────────────────────────────── -->
    <div x-data="{ open: <?php echo $hub_group_init_open('hubNav_workflow', ['workflow', 'overview']); ?> }"
         x-init="$watch('open', v => localStorage.setItem('hubNav_workflow', v ? '1' : '0'))">
        <button type="button" @click="open = !open" class="<?php echo esc_attr($hub_group_header_class(['workflow', 'overview'])); ?>">
            <span<?php echo $hub_label_attrs; ?>>Workflow</span>
            <svg class="w-3 h-3 transition-transform flex-shrink-0" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
        <div x-show="open" x-cloak class="mt-0.5 space-y-0.5">
            <a href="<?php echo $hub_nav_active === 'workflow' && !$hub_is_dashboard ? '#' : $hub_workflow_url; ?>"
               class="<?php echo esc_attr($hub_sub_class('workflow')); ?>">
                <svg class="h-5 w-5 flex-shrink-0 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
                <span class="nav-text"<?php echo $hub_label_attrs; ?>>Workflow</span>
            </a>
            <a href="<?php echo $hub_overview_url; ?>" class="<?php echo esc_attr($hub_sub_class('overview')); ?>">
                <svg class="h-5 w-5 flex-shrink-0 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span class="nav-text"<?php echo $hub_label_attrs; ?>>Agency Overview</span>
            </a>
        </div>
    </div>

    <!-- ── Insights ─────────────────────────────────────────────────────── -->
    <div x-data="{ open: <?php echo $hub_group_init_open('hubNav_insights', ['insights', 'reports', 'event_coverage']); ?> }"
         x-init="$watch('open', v => localStorage.setItem('hubNav_insights', v ? '1' : '0'))">
        <button type="button" @click="open = !open" class="<?php echo esc_attr($hub_group_header_class(['insights', 'reports', 'event_coverage'])); ?>">
            <span<?php echo $hub_label_attrs; ?>>Insights</span>
            <svg class="w-3 h-3 transition-transform flex-shrink-0" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
        <div x-show="open" x-cloak class="mt-0.5 space-y-0.5">
            <?php if ($hub_is_dashboard) : ?>
            <a href="#overview" @click="activeView = 'dashboard'"
               :class="activeView === 'dashboard' ? 'flex items-center px-3 py-2 ml-2 rounded-lg text-sm bg-indigo-500 text-white font-medium' : 'flex items-center px-3 py-2 ml-2 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'">
                <svg class="h-5 w-5 flex-shrink-0 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span class="nav-text">Insights</span>
            </a>
            <a href="#reports" @click="activeView = 'reports'"
               :class="activeView === 'reports' ? 'flex items-center px-3 py-2 ml-2 rounded-lg text-sm bg-indigo-500 text-white font-medium' : 'flex items-center px-3 py-2 ml-2 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'">
                <svg class="h-5 w-5 flex-shrink-0 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="nav-text">Reports</span>
            </a>
            <a href="#event-coverage" @click="activeView = 'eventCoverage'"
               :class="activeView === 'eventCoverage' ? 'flex items-center px-3 py-2 ml-2 rounded-lg text-sm bg-indigo-500 text-white font-medium' : 'flex items-center px-3 py-2 ml-2 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'">
                <svg class="h-5 w-5 flex-shrink-0 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h7.5m-7.5 3h4.5m-6 7.5h10.5A2.25 2.25 0 0019.5 15V6A2.25 2.25 0 0017.25 3.75H6.75A2.25 2.25 0 004.5 6v9a2.25 2.25 0 002.25 2.25zm0 0v2.25m10.5-2.25v2.25"/></svg>
                <span class="nav-text">Event Coverage</span>
            </a>
            <?php else : ?>
            <a href="<?php echo $hub_dashboard_url; ?>" class="<?php echo esc_attr($hub_sub_class('insights')); ?>">
                <svg class="h-5 w-5 flex-shrink-0 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span class="nav-text"<?php echo $hub_label_attrs; ?>>Insights</span>
            </a>
            <a href="<?php echo $hub_dashboard_url; ?>#reports" class="<?php echo esc_attr($hub_sub_class('reports')); ?>">
                <svg class="h-5 w-5 flex-shrink-0 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="nav-text"<?php echo $hub_label_attrs; ?>>Reports</span>
            </a>
            <a href="<?php echo $hub_dashboard_url; ?>#event-coverage" class="<?php echo esc_attr($hub_sub_class('event_coverage')); ?>">
                <svg class="h-5 w-5 flex-shrink-0 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h7.5m-7.5 3h4.5m-6 7.5h10.5A2.25 2.25 0 0019.5 15V6A2.25 2.25 0 0017.25 3.75H6.75A2.25 2.25 0 004.5 6v9a2.25 2.25 0 002.25 2.25zm0 0v2.25m10.5-2.25v2.25"/></svg>
                <span class="nav-text"<?php echo $hub_label_attrs; ?>>Event Coverage</span>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── Published Posts ────────────────────────────────────────────────── -->
    <div x-data="{ open: <?php echo $hub_group_init_open('hubNav_content', ['published_posts', 'content_calendar']); ?> }"
         x-init="$watch('open', v => localStorage.setItem('hubNav_content', v ? '1' : '0'))">
        <button type="button" @click="open = !open" class="<?php echo esc_attr($hub_group_header_class(['published_posts', 'content_calendar'])); ?>">
            <span<?php echo $hub_label_attrs; ?>>Published Posts</span>
            <svg class="w-3 h-3 transition-transform flex-shrink-0" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
        <div x-show="open" x-cloak class="mt-0.5 space-y-0.5">
            <?php if ($hub_is_dashboard) : ?>
            <a href="#content-calendar" @click="activeView = 'calendar'"
               :class="activeView === 'calendar' ? 'flex items-center px-3 py-2 ml-2 rounded-lg text-sm bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 font-medium' : 'flex items-center px-3 py-2 ml-2 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'">
                <svg class="h-5 w-5 flex-shrink-0 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span class="nav-text">Published Posts</span>
            </a>
            <?php else : ?>
            <a href="<?php echo $hub_dashboard_url; ?>#content-calendar" class="<?php echo esc_attr($hub_sub_class('published_posts')); ?>">
                <svg class="h-5 w-5 flex-shrink-0 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span class="nav-text"<?php echo $hub_label_attrs; ?>>Published Posts</span>
            </a>
            <?php endif; ?>
            <a href="<?php echo ($hub_nav_active === 'content_calendar' && !$hub_is_dashboard) ? '#' : $hub_calendar_url; ?>"
               class="<?php echo esc_attr($hub_sub_class('content_calendar')); ?>">
                <svg class="h-5 w-5 flex-shrink-0 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                <span class="nav-text"<?php echo $hub_label_attrs; ?>>Content Calendar</span>
            </a>
        </div>
    </div>

    <div class="border-t border-gray-100 dark:border-gray-700/60 my-1"></div>

    <!-- ── Standalone items ───────────────────────────────────────────────── -->
    <a href="<?php echo $hub_progress_url; ?>" class="<?php echo esc_attr($hub_item_class('progress')); ?>">
        <svg class="h-5 w-5 flex-shrink-0 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        <span class="nav-text"<?php echo $hub_label_attrs; ?>>My Progress</span>
    </a>

    <a href="<?php echo $hub_inventory_url; ?>" class="<?php echo esc_attr($hub_item_class('inventory')); ?>">
        <svg class="h-5 w-5 flex-shrink-0 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625-10.928a1.125 1.125 0 00-1.124-.99h-4.5a1.125 1.125 0 00-1.124.99L12.25 7.5M3.75 7.5h16.5M4.5 7.5v10.125c0 .621.504 1.125 1.125 1.125h13.5c.621 0 1.125-.504 1.125-1.125V7.5M9.75 11.25h4.5"/></svg>
        <span class="nav-text"<?php echo $hub_label_attrs; ?>>Inventory</span>
    </a>

</div>
