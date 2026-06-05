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

<div class="flex items-center justify-between p-4 h-16 border-b border-gray-200 dark:border-gray-700/50">
    <a href="<?php echo $hub_workflow_url; ?>" class="flex items-center space-x-2"<?php echo $hub_label_attrs; ?>>
        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/agencyhub-icon.png" alt="Agency Hub" class="h-8 w-8">
        <div class="flex flex-col">
            <span class="text-lg font-bold text-gray-800 dark:text-white leading-tight">Agency Hub</span>
            <span class="text-xs text-gray-500 dark:text-gray-400">by esirom</span>
        </div>
    </a>
    <button type="button" @click="isSidebarOpen = !isSidebarOpen" class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
    </button>
</div>
