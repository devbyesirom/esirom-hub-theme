<?php
/**
 * Shared sidebar header (Agency Hub logo + collapse toggle).
 *
 * Expects:
 *   $hub_nav_alpine_labels bool
 */

if (!defined('ABSPATH')) {
    exit;
}

$hub_nav_alpine_labels = !empty($hub_nav_alpine_labels);
$hub_label_attrs = $hub_nav_alpine_labels ? ' x-show="isSidebarOpen"' : '';
$hub_workflow_url = esc_url(esirom_hub_page_url('workflow'));
?>

<div class="flex items-center justify-between px-3 h-16 border-b border-gray-200 dark:border-gray-700/50">
    <a href="<?php echo $hub_workflow_url; ?>" class="hub-sidebar-brand pl-1" aria-label="Agency Hub home"<?php echo $hub_label_attrs; ?>>
        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/agencyhub-icon.png" alt="" class="h-8 w-8 flex-shrink-0">
        <span class="flex flex-col nav-text">
            <span class="hub-sidebar-brand__name">Agency Hub</span>
            <span class="hub-sidebar-brand__sub">by esirom</span>
        </span>
    </a>
    <button type="button" @click="isSidebarOpen = !isSidebarOpen" class="hub-btn hub-btn--ghost hub-btn--icon" :aria-expanded="isSidebarOpen" aria-label="Toggle sidebar">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>
</div>
