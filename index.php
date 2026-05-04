<?php
/**
 * The main template file
 *
 * @package Esirom_Client_Hub
 */

get_header();
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-4xl font-bold">Welcome to Esirom Client Hub</h1>
            <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium">v2.3.0</span>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
            <h2 class="text-2xl font-semibold mb-4">🚀 Latest Updates</h2>
            <div class="grid md:grid-cols-2 gap-4 mb-6">
                <div class="border-l-4 border-teal-500 pl-4">
                    <h3 class="font-semibold text-teal-700">Client Approved Status</h3>
                    <p class="text-sm text-gray-600">New "Client Approved" status card in workflow dashboard with teal progress bar segment showing as "Completed" in workload distribution.</p>
                </div>
                <div class="border-l-4 border-violet-500 pl-4">
                    <h3 class="font-semibold text-violet-700">Service Type Filtering</h3>
                    <p class="text-sm text-gray-600">Create Creative/Multimedia clients that appear in Concepts & Production but are filtered from social media dashboard, overview, and calendar.</p>
                </div>
                <div class="border-l-4 border-blue-500 pl-4">
                    <h3 class="font-semibold text-blue-700">Enhanced Instagram Parser</h3>
                    <p class="text-sm text-gray-600">Case-insensitive parsing for Instagram exports ensures all metrics are correctly extracted from JSON files.</p>
                </div>
                <div class="border-l-4 border-red-500 pl-4">
                    <h3 class="font-semibold text-red-700">Post Cleanup Tools</h3>
                    <p class="text-sm text-gray-600">Admin tools to delete all posts for a client and prevent data bleed between brands during uploads.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
            <h2 class="text-2xl font-semibold mb-4">Getting Started</h2>
            <p class="mb-4">This theme provides a complete social media client hub with dashboard, reporting, and content management capabilities.</p>
            
            <h3 class="text-xl font-semibold mb-3">Setup Steps:</h3>
            <ol class="list-decimal list-inside space-y-2 mb-6">
                <li>Configure your API URL in <a href="<?php echo esc_url(admin_url('themes.php?page=esirom-hub-settings')); ?>" class="text-blue-600 hover:underline">Hub Settings</a></li>
                <li>Create a page with the "Login" template for user authentication</li>
                <li>Create a page with the "Dashboard" template for the main hub interface</li>
                <li>Create pages for "Workflow", "Content Calendar", and "Overview" templates</li>
                <li>Make sure your backend API is running (see backend documentation)</li>
            </ol>
            
            <div class="flex gap-4">
                <a href="<?php echo esc_url(admin_url('themes.php?page=esirom-hub-settings')); ?>" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                    Configure Settings
                </a>
                <a href="<?php echo esc_url(admin_url('post-new.php?post_type=page')); ?>" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
                    Create Pages
                </a>
            </div>
        </div>
        
        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold mb-3">📚 Documentation</h3>
                <p class="text-gray-600 mb-4">Read the complete documentation to understand all features and capabilities.</p>
                <a href="<?php echo esc_url(get_theme_file_uri('README.md')); ?>" target="_blank" class="text-indigo-600 hover:underline">View Documentation →</a>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold mb-3">⚡ Quick Setup</h3>
                <p class="text-gray-600 mb-4">Follow the quick setup guide to get started in minutes.</p>
                <a href="<?php echo esc_url(get_theme_file_uri('SETUP_GUIDE.md')); ?>" target="_blank" class="text-indigo-600 hover:underline">View Setup Guide →</a>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
