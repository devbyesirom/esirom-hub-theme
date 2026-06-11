<?php
/**
 * Shared mobile bottom navigation for agency staff (admin / brand_rep).
 *
 * Expects:
 *   $hub_mobile_active  string  workflow|overview|dashboard|inventory|progress|content_calendar
 */

if (!defined('ABSPATH')) {
    exit;
}

$hub_mobile_active = isset($hub_mobile_active) ? $hub_mobile_active : '';

$hub_workflow_url = function_exists('esirom_hub_page_url') ? esirom_hub_page_url('workflow') : esc_url(get_permalink(get_page_by_path('workflow')));
$hub_overview_url = function_exists('esirom_hub_page_url') ? esirom_hub_page_url('overview') : esc_url(get_permalink(get_page_by_path('overview')));
$hub_dashboard_url = function_exists('esirom_hub_page_url') ? esirom_hub_page_url('dashboard') : esc_url(get_permalink(get_page_by_path('dashboard')));
$hub_inventory_url = function_exists('esirom_hub_page_url') ? esirom_hub_page_url('inventory') : esc_url(get_permalink(get_page_by_path('inventory')));
$hub_website_projects_url = function_exists('esirom_hub_page_url') ? esirom_hub_page_url('website-projects') : esc_url(get_permalink(get_page_by_path('website-projects')));
$hub_progress_url = function_exists('esirom_hub_page_url') ? esirom_hub_page_url('progress') : esc_url(get_permalink(get_page_by_path('progress')));
$hub_calendar_url = function_exists('esirom_hub_page_url') ? esirom_hub_page_url('content-calendar') : esc_url(get_permalink(get_page_by_path('content-calendar')));
$hub_admin_url = function_exists('esirom_hub_page_url') ? esirom_hub_page_url('admin') : esc_url(get_permalink(get_page_by_path('admin')));

$hub_mobile_class = function ($key) use ($hub_mobile_active) {
    $base = 'flex flex-col items-center justify-center flex-1 h-full space-y-1';
    if ($hub_mobile_active === $key) {
        return $base . ' text-indigo-600 dark:text-indigo-400';
    }
    return $base . ' text-gray-600 dark:text-gray-400';
};

$hub_mobile_label_class = function ($key) use ($hub_mobile_active) {
    return $hub_mobile_active === $key ? 'text-xs font-medium' : 'text-xs';
};
?>

<nav class="hub-mobile-bottom-nav md:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 z-40"
     x-data="{ viewMode: localStorage.getItem('viewMode') || 'admin', user: (() => { try { return JSON.parse(localStorage.getItem('user') || '{}') || {}; } catch (e) { return {}; } })(), init() { window.addEventListener('viewModeChanged', (e) => { this.viewMode = e?.detail?.viewMode || (localStorage.getItem('viewMode') || 'admin'); }); } }">
    <div class="flex justify-around items-center h-16 overflow-x-auto">
        <a x-show="viewMode !== 'client'" href="<?php echo esc_url($hub_workflow_url); ?>" class="<?php echo esc_attr($hub_mobile_class('workflow')); ?>">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span class="<?php echo esc_attr($hub_mobile_label_class('workflow')); ?>">Workflow</span>
        </a>
        <a x-show="viewMode !== 'client'" href="<?php echo esc_url($hub_dashboard_url); ?>" class="<?php echo esc_attr($hub_mobile_class('dashboard')); ?>">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="<?php echo esc_attr($hub_mobile_label_class('dashboard')); ?>">Insights</span>
        </a>
        <a x-show="viewMode !== 'client'" href="<?php echo esc_url($hub_website_projects_url); ?>" class="<?php echo esc_attr($hub_mobile_class('website_projects')); ?>">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            <span class="<?php echo esc_attr($hub_mobile_label_class('website_projects')); ?>">Websites</span>
        </a>
        <a x-show="viewMode !== 'client'" href="<?php echo esc_url($hub_progress_url); ?>" class="<?php echo esc_attr($hub_mobile_class('progress')); ?>">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="<?php echo esc_attr($hub_mobile_label_class('progress')); ?>">Progress</span>
        </a>
        <a x-show="user?.role === 'admin' && viewMode === 'admin'" href="<?php echo esc_url($hub_admin_url); ?>" class="<?php echo esc_attr($hub_mobile_class('admin')); ?>">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="<?php echo esc_attr($hub_mobile_label_class('admin')); ?>">Admin</span>
        </a>
    </div>
</nav>
