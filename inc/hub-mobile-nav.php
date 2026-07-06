<?php
/**
 * Unified mobile bottom navigation (PWA) for clients and agency staff.
 *
 * Expects:
 *   $hub_mobile_active   string  workflow|overview|insights|reports|event_coverage|published_posts|content_calendar|messages|ads|progress|inventory|website_projects|password_vault|admin|content_bank
 *   $hub_mobile_context  string  site|dashboard
 */

if (!defined('ABSPATH')) {
    exit;
}

$hub_mobile_active = isset($hub_mobile_active) ? $hub_mobile_active : '';
$hub_mobile_context = isset($hub_mobile_context) ? $hub_mobile_context : 'site';
$hub_is_dashboard = ($hub_mobile_context === 'dashboard');

$hub_workflow_url = esc_url(esirom_hub_page_url('workflow'));
$hub_workflow_content_url = esc_url(esirom_hub_page_url('workflow')) . '?tab=contentBank';
$hub_overview_url = esc_url(esirom_hub_page_url('overview'));
$hub_dashboard_url = esc_url(esirom_hub_page_url('dashboard'));
$hub_calendar_url = esc_url(esirom_hub_page_url('content-calendar'));
$hub_messages_url = esc_url(esirom_hub_page_url('messages'));
$hub_ads_url = esc_url(esirom_hub_page_url('ads'));
$hub_inventory_url = esc_url(esirom_hub_page_url('inventory'));
$hub_website_projects_url = esc_url(esirom_hub_page_url('website-projects'));
$hub_progress_url = esc_url(esirom_hub_page_url('progress'));
$hub_vault_url = esc_url(esirom_hub_page_url('password-vault'));
$hub_admin_url = esc_url(esirom_hub_page_url('admin'));

$hub_staff_show = esirom_hub_staff_nav_show_attr();
$hub_client_show = esirom_hub_client_nav_show_attr();

$hub_tab_class = function ($key) use ($hub_mobile_active) {
    $base = 'flex flex-col items-center justify-center flex-1 h-full space-y-0.5 min-w-0 px-0.5';
    if ($hub_mobile_active === $key) {
        return $base . ' text-indigo-600 dark:text-indigo-400';
    }
    return $base . ' text-gray-600 dark:text-gray-400';
};

$hub_label_class = function ($key) use ($hub_mobile_active) {
    return $hub_mobile_active === $key ? 'text-[10px] font-medium leading-tight' : 'text-[10px] leading-tight';
};

$hub_more_active = in_array($hub_mobile_active, [
    'overview', 'messages', 'event_coverage', 'password_vault', 'website_projects',
    'inventory', 'ads', 'admin', 'content_calendar'
], true);

$hub_more_tab_class = 'flex flex-col items-center justify-center flex-1 h-full space-y-0.5 min-w-0 px-0.5 ' .
    ($hub_more_active ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400');

esirom_hub_client_nav_bootstrap();
?>

<nav class="hub-mobile-bottom-nav md:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 z-40"
     x-data="{
        viewMode: localStorage.getItem('viewMode') || 'admin',
        user: (() => { try { return JSON.parse(localStorage.getItem('user') || '{}') || {}; } catch (e) { return {}; } })(),
        moreOpen: false,
        init() {
            window.addEventListener('viewModeChanged', (e) => {
                this.viewMode = e?.detail?.viewMode || (localStorage.getItem('viewMode') || 'admin');
            });
        }
     }">
    <div class="flex justify-around items-center h-16 max-w-lg mx-auto">

        <!-- ── Client primary tabs ─────────────────────────────────────── -->
        <a <?php echo $hub_client_show; ?> href="<?php echo $hub_workflow_content_url; ?>"
           x-show="typeof isWebsiteOnlyClient === 'undefined' || !isWebsiteOnlyClient"
           class="<?php echo esc_attr($hub_tab_class('content_bank')); ?>">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span class="<?php echo esc_attr($hub_label_class('content_bank')); ?>">Content</span>
        </a>

        <?php if ($hub_is_dashboard) : ?>
        <a <?php echo $hub_client_show; ?> href="#overview" @click="activeView = 'dashboard'; moreOpen = false"
           :class="activeView === 'dashboard' ? 'flex flex-col items-center justify-center flex-1 h-full space-y-0.5 min-w-0 px-0.5 text-indigo-600 dark:text-indigo-400' : 'flex flex-col items-center justify-center flex-1 h-full space-y-0.5 min-w-0 px-0.5 text-gray-600 dark:text-gray-400'">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="text-[10px] leading-tight" :class="activeView === 'dashboard' ? 'font-medium' : ''">Insights</span>
        </a>
        <a <?php echo $hub_client_show; ?> href="#reports" @click="activeView = 'reports'; moreOpen = false"
           x-show="typeof isWebsiteOnlyClient === 'undefined' || !isWebsiteOnlyClient"
           :class="activeView === 'reports' ? 'flex flex-col items-center justify-center flex-1 h-full space-y-0.5 min-w-0 px-0.5 text-indigo-600 dark:text-indigo-400' : 'flex flex-col items-center justify-center flex-1 h-full space-y-0.5 min-w-0 px-0.5 text-gray-600 dark:text-gray-400'">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span class="text-[10px] leading-tight" :class="activeView === 'reports' ? 'font-medium' : ''">Reports</span>
        </a>
        <a <?php echo $hub_client_show; ?> href="#content-calendar" @click="activeView = 'calendar'; moreOpen = false"
           x-show="typeof isWebsiteOnlyClient === 'undefined' || !isWebsiteOnlyClient"
           :class="activeView === 'calendar' ? 'flex flex-col items-center justify-center flex-1 h-full space-y-0.5 min-w-0 px-0.5 text-indigo-600 dark:text-indigo-400' : 'flex flex-col items-center justify-center flex-1 h-full space-y-0.5 min-w-0 px-0.5 text-gray-600 dark:text-gray-400'">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span class="text-[10px] leading-tight" :class="activeView === 'calendar' ? 'font-medium' : ''">Posts</span>
        </a>
        <?php else : ?>
        <a <?php echo $hub_client_show; ?> href="<?php echo $hub_dashboard_url; ?>#overview" class="<?php echo esc_attr($hub_tab_class('insights')); ?>">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="<?php echo esc_attr($hub_label_class('insights')); ?>">Insights</span>
        </a>
        <a <?php echo $hub_client_show; ?> href="<?php echo $hub_dashboard_url; ?>#reports" x-show="typeof isWebsiteOnlyClient === 'undefined' || !isWebsiteOnlyClient" class="<?php echo esc_attr($hub_tab_class('reports')); ?>">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span class="<?php echo esc_attr($hub_label_class('reports')); ?>">Reports</span>
        </a>
        <a <?php echo $hub_client_show; ?> href="<?php echo $hub_dashboard_url; ?>#content-calendar" x-show="typeof isWebsiteOnlyClient === 'undefined' || !isWebsiteOnlyClient" class="<?php echo esc_attr($hub_tab_class('published_posts')); ?>">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span class="<?php echo esc_attr($hub_label_class('published_posts')); ?>">Posts</span>
        </a>
        <?php endif; ?>

        <button type="button" <?php echo $hub_client_show; ?> @click="moreOpen = !moreOpen" class="<?php echo esc_attr($hub_more_tab_class); ?>">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <span class="text-[10px] leading-tight" :class="moreOpen ? 'font-medium' : ''">More</span>
        </button>

        <!-- ── Staff primary tabs ──────────────────────────────────────── -->
        <a <?php echo $hub_staff_show; ?> href="<?php echo ($hub_mobile_active === 'workflow' && !$hub_is_dashboard) ? '#' : $hub_workflow_url; ?>" class="<?php echo esc_attr($hub_tab_class('workflow')); ?>">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span class="<?php echo esc_attr($hub_label_class('workflow')); ?>">Workflow</span>
        </a>

        <?php if ($hub_is_dashboard) : ?>
        <a <?php echo $hub_staff_show; ?> href="#overview" @click="activeView = 'dashboard'; moreOpen = false"
           :class="activeView === 'dashboard' ? 'flex flex-col items-center justify-center flex-1 h-full space-y-0.5 min-w-0 px-0.5 text-indigo-600 dark:text-indigo-400' : 'flex flex-col items-center justify-center flex-1 h-full space-y-0.5 min-w-0 px-0.5 text-gray-600 dark:text-gray-400'">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="text-[10px] leading-tight" :class="activeView === 'dashboard' ? 'font-medium' : ''">Insights</span>
        </a>
        <?php else : ?>
        <a <?php echo $hub_staff_show; ?> href="<?php echo $hub_dashboard_url; ?>#overview" class="<?php echo esc_attr($hub_tab_class('insights')); ?>">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="<?php echo esc_attr($hub_label_class('insights')); ?>">Insights</span>
        </a>
        <?php endif; ?>

        <a <?php echo $hub_staff_show; ?> href="<?php echo ($hub_mobile_active === 'content_calendar' && !$hub_is_dashboard) ? '#' : $hub_calendar_url; ?>" class="<?php echo esc_attr($hub_tab_class('content_calendar')); ?>">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="<?php echo esc_attr($hub_label_class('content_calendar')); ?>">Calendar</span>
        </a>

        <a <?php echo $hub_staff_show; ?> href="<?php echo ($hub_mobile_active === 'progress' && !$hub_is_dashboard) ? '#' : $hub_progress_url; ?>" class="<?php echo esc_attr($hub_tab_class('progress')); ?>">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="<?php echo esc_attr($hub_label_class('progress')); ?>">Progress</span>
        </a>

        <button type="button" <?php echo $hub_staff_show; ?> @click="moreOpen = !moreOpen" class="<?php echo esc_attr($hub_more_tab_class); ?>">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <span class="text-[10px] leading-tight" :class="moreOpen ? 'font-medium' : ''">More</span>
        </button>
    </div>

    <!-- More menu sheet -->
    <div x-show="moreOpen" x-cloak @click.outside="moreOpen = false" class="absolute bottom-full left-0 right-0 mb-0 px-3 pb-2">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-xl overflow-hidden max-h-[min(22rem,55dvh)] overflow-y-auto">
            <div class="px-4 py-2.5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">More sections</p>
                <button type="button" @click="moreOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Client more links -->
            <div <?php echo $hub_client_show; ?> class="py-1">
                <a href="<?php echo $hub_calendar_url; ?>" @click="moreOpen = false" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <span class="text-base">📅</span> Content Calendar
                </a>
                <a href="<?php echo $hub_dashboard_url; ?>#event-coverage" @click="moreOpen = false" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <span class="text-base">📋</span> Event Coverage
                </a>
                <a href="<?php echo $hub_vault_url; ?>" @click="moreOpen = false" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <span class="text-base">🔐</span> Password Vault
                </a>
                <a href="<?php echo $hub_website_projects_url; ?>" x-show="$store.clientNav.hasWebsiteProjects" @click="moreOpen = false" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <span class="text-base">🌐</span> Website Projects
                </a>
            </div>

            <!-- Staff more links -->
            <div <?php echo $hub_staff_show; ?> class="py-1">
                <a href="<?php echo $hub_overview_url; ?>" @click="moreOpen = false" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <span class="text-base">🏢</span> Agency Overview
                </a>
                <a href="<?php echo $hub_messages_url; ?>" @click="moreOpen = false" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <span class="text-base">💬</span> Messages
                </a>
                <a href="<?php echo $hub_dashboard_url; ?>#content-calendar" @click="moreOpen = false" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <span class="text-base">📱</span> Published Posts
                </a>
                <a href="<?php echo $hub_dashboard_url; ?>#reports" @click="moreOpen = false" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <span class="text-base">📊</span> Reports
                </a>
                <a href="<?php echo $hub_dashboard_url; ?>#event-coverage" @click="moreOpen = false" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <span class="text-base">📋</span> Event Coverage
                </a>
                <a href="<?php echo $hub_vault_url; ?>" @click="moreOpen = false" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <span class="text-base">🔐</span> Password Vault
                </a>
                <a href="<?php echo $hub_website_projects_url; ?>" @click="moreOpen = false" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <span class="text-base">🌐</span> Website Projects
                </a>
                <a href="<?php echo $hub_inventory_url; ?>" @click="moreOpen = false" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <span class="text-base">📦</span> Inventory
                </a>
                <a href="<?php echo $hub_ads_url; ?>" @click="moreOpen = false" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <span class="text-base">📈</span> Ads
                </a>
                <a x-show="user?.role === 'admin' && viewMode === 'admin'" href="<?php echo $hub_admin_url; ?>" @click="moreOpen = false" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <span class="text-base">⚙️</span> Admin Panel
                </a>
            </div>
        </div>
    </div>
</nav>
