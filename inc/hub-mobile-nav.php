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

// The Workflow page doubles as the client Content Bank, so both keys light that tab.
$hub_tab_is_active = function ($key) use ($hub_mobile_active) {
    if ($key === 'content_bank') {
        return in_array($hub_mobile_active, array('content_bank', 'workflow'), true);
    }
    return $hub_mobile_active === $key;
};

$hub_tab_class = function ($key) use ($hub_tab_is_active) {
    return 'hub-tab' . ($hub_tab_is_active($key) ? ' is-active' : '');
};

$hub_current = function ($key) use ($hub_tab_is_active) {
    return $hub_tab_is_active($key) ? ' aria-current="page"' : '';
};

$hub_more_active = in_array($hub_mobile_active, [
    'overview', 'messages', 'event_coverage', 'password_vault', 'website_projects',
    'inventory', 'ads', 'admin', 'content_calendar'
], true);

$hub_more_tab_class = 'hub-tab' . ($hub_more_active ? ' is-active' : '');

// Alpine class binding for in-page (dashboard) views.
$hub_view_tab = function ($view) {
    return "class=\"hub-tab\" :class=\"activeView === '{$view}' ? 'is-active' : ''\"";
};

esirom_hub_client_nav_bootstrap();

// Icon set (Heroicons outline, 24px)
$hub_icons = array(
    'content'   => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
    'insights'  => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    'reports'   => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'posts'     => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
    'workflow'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
    'calendar'  => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
    'progress'  => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    'more'      => 'M4 6h16M4 12h16M4 18h16',
    'overview'  => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
    'messages'  => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z',
    'events'    => 'M8.25 6.75h7.5m-7.5 3h4.5m-6 7.5h10.5A2.25 2.25 0 0019.5 15V6A2.25 2.25 0 0017.25 3.75H6.75A2.25 2.25 0 004.5 6v9a2.25 2.25 0 002.25 2.25zm0 0v2.25m10.5-2.25v2.25',
    'vault'     => 'M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z',
    'website'   => 'M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5',
    'inventory' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z',
    'ads'       => 'M3.75 13.5l10.5-11.25L15 6.75m-3-3v11.25m9-5.25H9',
    'admin'     => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
);

$hub_icon = function ($name) use ($hub_icons) {
    $d = isset($hub_icons[$name]) ? $hub_icons[$name] : '';
    return '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="' . esc_attr($d) . '"/></svg>';
};
?>

<nav class="hub-mobile-bottom-nav md:hidden fixed bottom-0 left-0 right-0 z-40" aria-label="Primary"
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
    <div class="flex justify-around items-stretch h-16 max-w-lg mx-auto">

        <!-- ── Client primary tabs ─────────────────────────────────────── -->
        <a x-show="(viewMode === 'client' || user?.role === 'client') && (typeof isWebsiteOnlyClient === 'undefined' || !isWebsiteOnlyClient)" href="<?php echo $hub_workflow_content_url; ?>"
           class="<?php echo esc_attr($hub_tab_class('content_bank')); ?>"<?php echo $hub_current('content_bank'); ?>>
            <span class="hub-tab__icon"><?php echo $hub_icon('content'); ?></span>
            <span>Content</span>
        </a>

        <?php if ($hub_is_dashboard) : ?>
        <a <?php echo $hub_client_show; ?> href="#overview" @click="activeView = 'dashboard'; moreOpen = false" <?php echo $hub_view_tab('dashboard'); ?>>
            <span class="hub-tab__icon"><?php echo $hub_icon('insights'); ?></span>
            <span>Insights</span>
        </a>
        <a x-show="(viewMode === 'client' || user?.role === 'client') && (typeof isWebsiteOnlyClient === 'undefined' || !isWebsiteOnlyClient)" href="#reports" @click="activeView = 'reports'; moreOpen = false" <?php echo $hub_view_tab('reports'); ?>>
            <span class="hub-tab__icon"><?php echo $hub_icon('reports'); ?></span>
            <span>Reports</span>
        </a>
        <a x-show="(viewMode === 'client' || user?.role === 'client') && (typeof isWebsiteOnlyClient === 'undefined' || !isWebsiteOnlyClient)" href="#content-calendar" @click="activeView = 'calendar'; moreOpen = false" <?php echo $hub_view_tab('calendar'); ?>>
            <span class="hub-tab__icon"><?php echo $hub_icon('posts'); ?></span>
            <span>Posts</span>
        </a>
        <?php else : ?>
        <a <?php echo $hub_client_show; ?> href="<?php echo $hub_dashboard_url; ?>#overview" class="<?php echo esc_attr($hub_tab_class('insights')); ?>"<?php echo $hub_current('insights'); ?>>
            <span class="hub-tab__icon"><?php echo $hub_icon('insights'); ?></span>
            <span>Insights</span>
        </a>
        <a x-show="(viewMode === 'client' || user?.role === 'client') && (typeof isWebsiteOnlyClient === 'undefined' || !isWebsiteOnlyClient)" href="<?php echo $hub_dashboard_url; ?>#reports" class="<?php echo esc_attr($hub_tab_class('reports')); ?>"<?php echo $hub_current('reports'); ?>>
            <span class="hub-tab__icon"><?php echo $hub_icon('reports'); ?></span>
            <span>Reports</span>
        </a>
        <a x-show="(viewMode === 'client' || user?.role === 'client') && (typeof isWebsiteOnlyClient === 'undefined' || !isWebsiteOnlyClient)" href="<?php echo $hub_dashboard_url; ?>#content-calendar" class="<?php echo esc_attr($hub_tab_class('published_posts')); ?>"<?php echo $hub_current('published_posts'); ?>>
            <span class="hub-tab__icon"><?php echo $hub_icon('posts'); ?></span>
            <span>Posts</span>
        </a>
        <?php endif; ?>

        <button type="button" <?php echo $hub_client_show; ?> @click="moreOpen = !moreOpen" :aria-expanded="moreOpen" class="<?php echo esc_attr($hub_more_tab_class); ?>" :class="moreOpen ? 'is-active' : ''">
            <span class="hub-tab__icon"><?php echo $hub_icon('more'); ?></span>
            <span>More</span>
        </button>

        <!-- ── Staff primary tabs ──────────────────────────────────────── -->
        <a <?php echo $hub_staff_show; ?> href="<?php echo $hub_workflow_url; ?>" class="<?php echo esc_attr($hub_tab_class('workflow')); ?>"<?php echo $hub_current('workflow'); ?>>
            <span class="hub-tab__icon"><?php echo $hub_icon('workflow'); ?></span>
            <span>Workflow</span>
        </a>

        <?php if ($hub_is_dashboard) : ?>
        <a <?php echo $hub_staff_show; ?> href="#overview" @click="activeView = 'dashboard'; moreOpen = false" <?php echo $hub_view_tab('dashboard'); ?>>
            <span class="hub-tab__icon"><?php echo $hub_icon('insights'); ?></span>
            <span>Insights</span>
        </a>
        <?php else : ?>
        <a <?php echo $hub_staff_show; ?> href="<?php echo $hub_dashboard_url; ?>#overview" class="<?php echo esc_attr($hub_tab_class('insights')); ?>"<?php echo $hub_current('insights'); ?>>
            <span class="hub-tab__icon"><?php echo $hub_icon('insights'); ?></span>
            <span>Insights</span>
        </a>
        <?php endif; ?>

        <a <?php echo $hub_staff_show; ?> href="<?php echo $hub_calendar_url; ?>" class="<?php echo esc_attr($hub_tab_class('content_calendar')); ?>"<?php echo $hub_current('content_calendar'); ?>>
            <span class="hub-tab__icon"><?php echo $hub_icon('calendar'); ?></span>
            <span>Calendar</span>
        </a>

        <a <?php echo $hub_staff_show; ?> href="<?php echo $hub_progress_url; ?>" class="<?php echo esc_attr($hub_tab_class('progress')); ?>"<?php echo $hub_current('progress'); ?>>
            <span class="hub-tab__icon"><?php echo $hub_icon('progress'); ?></span>
            <span>Progress</span>
        </a>

        <button type="button" <?php echo $hub_staff_show; ?> @click="moreOpen = !moreOpen" :aria-expanded="moreOpen" class="<?php echo esc_attr($hub_more_tab_class); ?>" :class="moreOpen ? 'is-active' : ''">
            <span class="hub-tab__icon"><?php echo $hub_icon('more'); ?></span>
            <span>More</span>
        </button>
    </div>

    <!-- More menu sheet -->
    <div x-show="moreOpen" x-cloak @click.outside="moreOpen = false" @keydown.escape.window="moreOpen = false"
         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         class="absolute bottom-full left-0 right-0 px-3 pb-2">
        <div class="hub-card hub-card--elevated overflow-hidden max-h-[min(24rem,60dvh)] overflow-y-auto rounded-2xl">
            <div class="px-4 py-2.5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <p class="hub-eyebrow">More sections</p>
                <button type="button" @click="moreOpen = false" class="hub-btn hub-btn--ghost hub-btn--icon hub-btn--sm" aria-label="Close menu">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Client more links -->
            <div <?php echo $hub_client_show; ?> class="py-1">
                <a href="<?php echo $hub_calendar_url; ?>" @click="moreOpen = false" class="hub-sheet-link"><?php echo $hub_icon('calendar'); ?> Content Calendar</a>
                <a href="<?php echo $hub_dashboard_url; ?>#event-coverage" @click="moreOpen = false" class="hub-sheet-link"><?php echo $hub_icon('events'); ?> Event Coverage</a>
                <a href="<?php echo $hub_vault_url; ?>" @click="moreOpen = false" class="hub-sheet-link"><?php echo $hub_icon('vault'); ?> Password Vault</a>
                <a href="<?php echo $hub_website_projects_url; ?>" x-show="$store.clientNav.hasWebsiteProjects" @click="moreOpen = false" class="hub-sheet-link"><?php echo $hub_icon('website'); ?> Website Projects</a>
            </div>

            <!-- Staff more links -->
            <div <?php echo $hub_staff_show; ?> class="py-1">
                <a href="<?php echo $hub_overview_url; ?>" @click="moreOpen = false" class="hub-sheet-link"><?php echo $hub_icon('overview'); ?> Agency Overview</a>
                <a href="<?php echo $hub_messages_url; ?>" @click="moreOpen = false" class="hub-sheet-link"><?php echo $hub_icon('messages'); ?> Messages</a>
                <a href="<?php echo $hub_dashboard_url; ?>#content-calendar" @click="moreOpen = false" class="hub-sheet-link"><?php echo $hub_icon('posts'); ?> Published Posts</a>
                <a href="<?php echo $hub_dashboard_url; ?>#reports" @click="moreOpen = false" class="hub-sheet-link"><?php echo $hub_icon('reports'); ?> Reports</a>
                <a href="<?php echo $hub_dashboard_url; ?>#event-coverage" @click="moreOpen = false" class="hub-sheet-link"><?php echo $hub_icon('events'); ?> Event Coverage</a>
                <a href="<?php echo $hub_vault_url; ?>" @click="moreOpen = false" class="hub-sheet-link"><?php echo $hub_icon('vault'); ?> Password Vault</a>
                <a href="<?php echo $hub_website_projects_url; ?>" @click="moreOpen = false" class="hub-sheet-link"><?php echo $hub_icon('website'); ?> Website Projects</a>
                <a href="<?php echo $hub_inventory_url; ?>" @click="moreOpen = false" class="hub-sheet-link"><?php echo $hub_icon('inventory'); ?> Inventory</a>
                <a href="<?php echo $hub_ads_url; ?>" @click="moreOpen = false" class="hub-sheet-link"><?php echo $hub_icon('ads'); ?> Ads</a>
                <a x-show="user?.role === 'admin' && viewMode === 'admin'" href="<?php echo $hub_admin_url; ?>" @click="moreOpen = false" class="hub-sheet-link"><?php echo $hub_icon('admin'); ?> Admin Panel</a>
            </div>
        </div>
    </div>
</nav>
