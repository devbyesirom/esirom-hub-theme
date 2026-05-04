<?php
/**
 * Template Name: Workflow
 * Description: Workflow management for social media and creative teams
 */
if (!defined('ABSPATH')) {
    exit;
}

$esirom_workflow_page_path = '/workflow';
$esirom_wf_id = (int) get_queried_object_id();
if ($esirom_wf_id < 1 && function_exists('get_page_by_path')) {
    $p = get_page_by_path('workflow');
    if ($p && ! is_wp_error($p) && ! empty($p->ID)) {
        $esirom_wf_id = (int) $p->ID;
    }
}
if ($esirom_wf_id > 0) {
    $wlink = get_permalink($esirom_wf_id);
    if ($wlink) {
        $path_part = parse_url($wlink, PHP_URL_PATH);
        if (is_string($path_part) && $path_part !== '') {
            $esirom_workflow_page_path = rtrim($path_part, '/');
        }
    }
}

show_admin_bar(false);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workflow - Agency Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        const theme = localStorage.getItem('theme') || 'light';
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        .sidebar {
            transition: width 0.3s ease;
        }
        .sidebar-collapsed {
            width: 5rem;
        }
        .sidebar-expanded {
            width: 16rem;
        }
        .sidebar .nav-text {
            display: inline;
        }
        .sidebar-collapsed .nav-text {
            display: none;
        }
        .sidebar-collapsed .justify-between {
            justify-content: center;
        }

        .wf-tab {
            -webkit-tap-highlight-color: transparent;
        }
        .wf-tab:focus-visible {
            outline: 2px solid rgba(99, 102, 241, 0.5);
            outline-offset: 2px;
        }

        #wpadminbar { display: none !important; }
        html { margin-top: 0 !important; }
        body { margin-top: 0 !important; }
    </style>
</head>
<body class="h-full bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-white pb-16 md:pb-0">
    <div x-data="workflowApp()" x-init="init()" class="flex h-full flex-col md:flex-row">
        <!-- Mobile Header -->
        <div class="md:hidden fixed top-0 left-0 right-0 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 z-40 px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png" alt="Agency Hub" class="h-8 w-8">
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-gray-800 dark:text-white leading-tight">Workflow</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Agency Hub</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Admin Role Switcher (mobile) -->
                    <div x-show="user.role === 'admin'" class="relative" x-data="{ showRoleSwitcher: false }">
                        <button @click="showRoleSwitcher = !showRoleSwitcher" class="flex items-center gap-2 px-3 py-2 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 hover:bg-purple-200 dark:hover:bg-purple-900/50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <span class="text-sm font-medium" x-text="viewMode === 'admin' ? 'Admin View' : viewMode === 'brand_rep' ? 'Brand Rep View' : 'Client View'"></span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="showRoleSwitcher" @click.away="showRoleSwitcher = false" class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50">
                            <div class="p-2">
                                <button @click="switchViewMode('admin'); showRoleSwitcher = false" :class="viewMode === 'admin' ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">Admin View</button>
                                <button @click="switchViewMode('brand_rep'); showRoleSwitcher = false" :class="viewMode === 'brand_rep' ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">Brand Rep View</button>
                                <button @click="switchViewMode('client'); showRoleSwitcher = false" :class="viewMode === 'client' ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">Client View</button>
                            </div>
                        </div>
                    </div>
                    <!-- Theme Toggle (mobile) -->
                    <button @click="toggleTheme()" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg x-show="theme === 'light'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <svg x-show="theme === 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <aside :class="isSidebarOpen ? 'sidebar-expanded' : 'sidebar-collapsed'" class="sidebar hidden md:flex bg-white dark:bg-gray-900/70 dark:backdrop-blur-sm border-r border-gray-200 dark:border-gray-700/50 flex-col">
            <div class="flex items-center justify-between p-4 h-16 border-b border-gray-200 dark:border-gray-700/50">
                <a href="#" class="flex items-center space-x-2" x-show="isSidebarOpen">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png" alt="Agency Hub" class="h-8 w-8">
                    <div class="flex flex-col">
                        <span class="text-lg font-bold text-gray-800 dark:text-white leading-tight">Agency Hub</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">by esirom</span>
                    </div>
                </a>
                <button @click="isSidebarOpen = !isSidebarOpen" class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                </button>
            </div>
            <nav class="flex-1 px-2 py-4 space-y-2">
                <!-- CLIENT VIEW NAVIGATION -->
                <!-- Content Bank - Client View Only -->
                <a x-show="viewMode === 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('workflow'))); ?>?tab=contentBank" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200" :class="activeTab === 'contentBank' ? 'bg-indigo-500 text-white' : ''">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span class="ml-4 nav-text" x-show="isSidebarOpen">Content Bank</span>
                </a>
                <!-- Published Posts - Client View Only -->
                <a x-show="viewMode === 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('dashboard'))); ?>#content-calendar" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"></path></svg>
                    <span class="ml-4 nav-text" x-show="isSidebarOpen">Published Posts</span>
                </a>
                <!-- Page Overview - Client View Only -->
                <a x-show="viewMode === 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('dashboard'))); ?>#overview" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span class="ml-4 nav-text" x-show="isSidebarOpen">Page Overview</span>
                </a>
                <!-- Content Calendar - Client View Only -->
                <a x-show="viewMode === 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('content-calendar'))); ?>" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="ml-4 nav-text" x-show="isSidebarOpen">Content Calendar</span>
                </a>
                <!-- Workflow - Client View Only (active on this page) -->
                <a x-show="viewMode === 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('workflow'))); ?>" class="flex items-center p-3 rounded-lg transition-colors duration-200" :class="activeTab !== 'contentBank' ? 'bg-indigo-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    <span class="ml-4 nav-text" x-show="isSidebarOpen">Workflow</span>
                </a>

                <!-- ADMIN/BRAND REP VIEW NAVIGATION -->
                <!-- Workflow - active on this page -->
                <a x-show="viewMode !== 'client'" href="#" class="flex items-center p-3 rounded-lg bg-indigo-500 text-white transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    <span class="ml-4 nav-text" x-show="isSidebarOpen">Workflow</span>
                </a>
                <!-- Agency Overview -->
                <a x-show="viewMode !== 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('overview'))); ?>" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span class="ml-4 nav-text" x-show="isSidebarOpen">Agency Overview</span>
                </a>
                <!-- Insights (Brand Dashboard) -->
                <a x-show="viewMode !== 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('dashboard'))); ?>" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span class="ml-4 nav-text" x-show="isSidebarOpen">Insights</span>
                </a>
                <!-- Published Posts -->
                <a x-show="viewMode !== 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('dashboard'))); ?>#content-calendar" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span class="ml-4 nav-text" x-show="isSidebarOpen">Published Posts</span>
                </a>
                <!-- Content Calendar -->
                <a x-show="viewMode !== 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('content-calendar'))); ?>" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="ml-4 nav-text" x-show="isSidebarOpen">Content Calendar</span>
                </a>
            </nav>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700/50">
                <!-- Admin Panel - only for actual admins, hidden in simulated views -->
                <template x-if="user.role === 'admin' && viewMode === 'admin'">
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('admin'))); ?>" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 mb-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="ml-4 nav-text" x-show="isSidebarOpen">Admin Panel</span>
                    </a>
                </template>
                <a @click.prevent="logout()" href="#" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span class="ml-4 nav-text" x-show="isSidebarOpen">Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 bg-gray-50 dark:bg-gray-900 overflow-y-auto mt-14 md:mt-0">
            <!-- Top Bar -->
            <header class="hidden md:flex items-center justify-between p-4 h-16 bg-white/80 dark:bg-gray-900/70 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700/50 sticky top-0 z-10 shadow-sm">
                <div class="flex items-center space-x-4">
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Workflow Management</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500 dark:text-gray-400" x-text="currentDate"></span>
                    <!-- Notifications -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 relative">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span x-show="unreadNotifications > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center" x-text="unreadNotifications"></span>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border dark:border-gray-700 max-h-96 overflow-y-auto">
                            <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                                <span class="font-semibold text-gray-900 dark:text-white">Notifications</span>
                                <button @click="markAllRead()" class="text-sm text-indigo-600 hover:text-indigo-800">Mark all read</button>
                            </div>
                            <template x-for="notification in notifications" :key="notification._id">
                                <div @click="handleNotificationClick(notification); open = false" class="p-4 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" :class="{ 'bg-indigo-50 dark:bg-indigo-900/20': !notification.isRead }">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="notification.title"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="notification.message"></p>
                                    <p class="text-xs text-gray-400 mt-1" x-text="formatDate(notification.createdAt)"></p>
                                </div>
                            </template>
                            <div x-show="notifications.length === 0" class="p-4 text-center text-gray-500">No notifications</div>
                        </div>
                    </div>
                    <!-- Admin Role Switcher (only for admins) -->
                    <div x-show="user.role === 'admin'" class="relative" x-data="{ showRoleSwitcher: false }">
                        <button @click="showRoleSwitcher = !showRoleSwitcher" class="flex items-center gap-2 px-3 py-2 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 hover:bg-purple-200 dark:hover:bg-purple-900/50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <span class="text-sm font-medium" x-text="viewMode === 'admin' ? 'Admin View' : viewMode === 'brand_rep' ? 'Brand Rep View' : 'Client View'"></span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="showRoleSwitcher" @click.away="showRoleSwitcher = false" class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50">
                            <div class="p-2">
                                <button @click="switchViewMode('admin'); showRoleSwitcher = false" :class="viewMode === 'admin' ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Admin View
                                </button>
                                <button @click="switchViewMode('brand_rep'); showRoleSwitcher = false" :class="viewMode === 'brand_rep' ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Brand Rep View
                                </button>
                                <button @click="switchViewMode('client'); showRoleSwitcher = false" :class="viewMode === 'client' ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Client View
                                </button>
                                <!-- Client Selector (only when in client view) -->
                                <div x-show="viewMode === 'client'" class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 px-3">View as Client:</label>
                                    <select x-model="selectedViewClient" @change="localStorage.setItem('selectedViewClient', selectedViewClient); loadConcepts(); loadContentBank(); loadProductions();" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <template x-for="client in clients" :key="client._id">
                                            <option :value="client._id" x-text="client.brandName || client.name"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Theme Toggle -->
                    <button @click="toggleTheme()" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Toggle theme">
                        <svg x-show="theme === 'light'" class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        <svg x-show="theme === 'dark'" x-cloak class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </button>
                    <!-- User Menu Dropdown -->
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2">
                            <img class="h-8 w-8 rounded-full" :src="user.clientId?.logo || 'https://placehold.co/100x100/4a5568/ffffff?text=' + (user.firstName?.[0] || 'U')" :alt="user.firstName + ' ' + user.lastName">
                            <span class="hidden sm:inline text-gray-700 dark:text-gray-300" x-text="user.firstName + ' ' + user.lastName"></span>
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.23 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                        </button>
                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-20">
                            <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-sm font-medium" x-text="user.firstName + ' ' + user.lastName"></p>
                                <p class="text-xs text-gray-500" x-text="user.email"></p>
                                <p class="text-xs text-indigo-600 mt-1 capitalize" x-text="user.role?.replace('_', ' ')"></p>
                            </div>
                            <a @click.prevent="logout()" href="#" class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Logout</a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Loading State -->
            <div x-show="loading" class="flex items-center justify-center h-64">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
            </div>

            <!-- Main Workflow Content -->
            <div x-show="!loading" x-cloak class="p-4 sm:p-6 lg:p-8">
                <!-- Tabs - Client View only sees Content Bank - scrollable on mobile -->
                <div class="overflow-x-auto -mx-4 px-4 md:mx-0 md:px-0 mb-6" role="tablist" aria-label="Workflow sections">
                    <div class="flex space-x-1 bg-gray-200/90 dark:bg-gray-800/90 rounded-xl p-1 w-max md:w-fit border border-gray-200/80 dark:border-gray-600/50 shadow-sm">
                        <button type="button" role="tab" :aria-selected="activeTab === 'dashboard'" x-show="viewMode !== 'client'" @click="navigateTab('dashboard')" :class="activeTab === 'dashboard' ? 'bg-white dark:bg-gray-800 shadow text-indigo-700 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-400'" class="wf-tab shrink-0 px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-medium transition-all whitespace-nowrap flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span class="hidden sm:inline">Dashboard</span>
                        </button>
                        <button type="button" role="tab" :aria-selected="activeTab === 'concepts'" x-show="viewMode !== 'client'" @click="navigateTab('concepts')" :class="activeTab === 'concepts' ? 'bg-white dark:bg-gray-800 shadow text-indigo-700 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-400'" class="wf-tab shrink-0 px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-medium transition-all whitespace-nowrap flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                            <span class="hidden sm:inline">Concepts</span>
                        </button>
                        <button type="button" role="tab" :aria-selected="activeTab === 'productions'" x-show="viewMode !== 'client'" @click="navigateTab('productions')" :class="activeTab === 'productions' ? 'bg-white dark:bg-gray-800 shadow text-indigo-700 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-400'" class="wf-tab shrink-0 px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-medium transition-all whitespace-nowrap flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            <span class="hidden sm:inline">Production</span>
                        </button>
                        <button type="button" role="tab" :aria-selected="activeTab === 'contentBank'" @click="navigateTab('contentBank')" :class="activeTab === 'contentBank' ? 'bg-white dark:bg-gray-800 shadow text-indigo-700 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-400'" class="wf-tab shrink-0 px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-medium transition-all whitespace-nowrap flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <span class="hidden sm:inline">Content Bank</span>
                        </button>
                        <button type="button" role="tab" :aria-selected="activeTab === 'tasks'" x-show="viewMode !== 'client'" @click="navigateTab('tasks')" :class="activeTab === 'tasks' ? 'bg-white dark:bg-gray-800 shadow text-indigo-700 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-400'" class="wf-tab shrink-0 px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-medium transition-all whitespace-nowrap flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            <span class="hidden sm:inline">My Tasks</span>
                        </button>
                        <button type="button" role="tab" :aria-selected="activeTab === 'planner'" x-show="viewMode !== 'client'" @click="navigateTab('planner')" :class="activeTab === 'planner' ? 'bg-white dark:bg-gray-800 shadow text-indigo-700 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-400'" class="wf-tab shrink-0 px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-medium transition-all whitespace-nowrap flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
                            <span class="hidden sm:inline">Planner</span>
                        </button>
                        <button type="button" role="tab" :aria-selected="activeTab === 'feed'" @click="navigateTab('feed')" :class="activeTab === 'feed' ? 'bg-white dark:bg-gray-800 shadow text-indigo-700 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-400'" class="wf-tab shrink-0 px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-medium transition-all whitespace-nowrap relative flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                            <span class="hidden sm:inline">Feed</span>
                            <span x-show="unreadFeedCount > 0" x-text="unreadFeedCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1"></span>
                        </button>
                    </div>
                </div>

                <!-- Dashboard Tab -->
                <div x-show="activeTab === 'dashboard'">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-6 mb-8">
                        <button @click="filterByStatus('draft')" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-left hover:shadow-lg transition-shadow cursor-pointer" :class="{'ring-2 ring-gray-500': conceptStatusFilter === 'draft'}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Draft Concepts</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-white" x-text="dashboardStats.concepts?.draft || 0"></p>
                                </div>
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full">
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </div>
                            </div>
                        </button>
                        <button @click="filterByStatus('in_progress')" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-left hover:shadow-lg transition-shadow cursor-pointer" :class="{'ring-2 ring-blue-500': conceptStatusFilter === 'in_progress'}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">In Progress</p>
                                    <p class="text-3xl font-bold text-blue-600" x-text="dashboardStats.concepts?.in_progress || 0"></p>
                                </div>
                                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </div>
                            </div>
                        </button>
                        <button @click="filterByStatus('pending_review')" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-left hover:shadow-lg transition-shadow cursor-pointer" :class="{'ring-2 ring-amber-500': conceptStatusFilter === 'pending_review'}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Pending Review</p>
                                    <p class="text-3xl font-bold text-amber-600" x-text="dashboardStats.concepts?.pending_review || 0"></p>
                                </div>
                                <div class="p-3 bg-amber-100 dark:bg-amber-900 rounded-full">
                                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </div>
                            </div>
                        </button>
                        <button @click="filterByStatus('in_content_bank')" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-left hover:shadow-lg transition-shadow cursor-pointer" :class="{'ring-2 ring-purple-500': conceptStatusFilter === 'in_content_bank'}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Approved Internally</p>
                                    <p class="text-3xl font-bold text-purple-600" x-text="dashboardStats.concepts?.in_content_bank || 0"></p>
                                </div>
                                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            </div>
                        </button>
                        <button @click="filterByStatus('client_approved')" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-left hover:shadow-lg transition-shadow cursor-pointer" :class="{'ring-2 ring-teal-500': conceptStatusFilter === 'client_approved'}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Client Approved</p>
                                    <p class="text-3xl font-bold text-teal-600" x-text="dashboardStats.concepts?.client_approved || 0"></p>
                                </div>
                                <div class="p-3 bg-teal-100 dark:bg-teal-900 rounded-full">
                                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                        </button>
                        <button @click="filterByStatus('overdue')" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-left hover:shadow-lg transition-shadow cursor-pointer" :class="{'ring-2 ring-red-500': conceptStatusFilter === 'overdue'}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Overdue</p>
                                    <p class="text-3xl font-bold text-red-600" x-text="(dashboardStats.overdue?.concepts || 0) + (dashboardStats.overdue?.tasks || 0)"></p>
                                </div>
                                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            </div>
                        </button>
                    </div>

                    <!-- My Assigned Work -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                            <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                                <h3 class="font-semibold text-gray-900 dark:text-white">My Assigned Concepts</h3>
                                <button @click="activeTab = 'concepts'" class="text-sm text-indigo-600 hover:text-indigo-800">View All</button>
                            </div>
                            <div class="divide-y dark:divide-gray-700">
                                <template x-for="concept in myWork.concepts" :key="concept._id">
                                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" @click="viewConcept(concept)">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white" x-text="concept.title"></p>
                                                <p class="text-sm text-gray-500" x-text="concept.clientId?.brandName || concept.clientId?.name"></p>
                                            </div>
                                            <span :class="getStatusClass(concept.status)" class="px-2 py-1 text-xs rounded-full" x-text="formatStatus(concept.status)"></span>
                                        </div>
                                        <div class="mt-2 flex items-center text-sm text-gray-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span x-text="formatDate(concept.dueDate)"></span>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="myWork.concepts?.length === 0" class="p-8 text-center text-gray-500">No assigned concepts</div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                            <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                                <h3 class="font-semibold text-gray-900 dark:text-white">Needs Attention</h3>
                                <button @click="navigateTab('tasks')" class="text-sm text-indigo-600 hover:text-indigo-800">View All</button>
                            </div>
                            <div class="divide-y dark:divide-gray-700">
                                <template x-for="task in myWork.tasks" :key="task._id">
                                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" @click="viewConcept(task)">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white" x-text="task.title"></p>
                                                <p class="text-sm text-gray-500" x-text="task.clientId?.brandName || task.clientId?.name"></p>
                                            </div>
                                            <span :class="getStatusClass(task.status)" class="px-2 py-1 text-xs rounded-full" x-text="formatStatus(task.status)"></span>
                                        </div>
                                        <div class="mt-2 flex items-center text-sm text-gray-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span x-text="formatDate(task.dueDate)"></span>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="myWork.tasks?.length === 0" class="p-8 text-center text-gray-500">No items needing attention</div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow">
                            <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                                <h3 class="font-semibold text-gray-900 dark:text-white">All Assigned Concepts</h3>
                                <button @click="activeTab = 'concepts'" class="text-sm text-indigo-600 hover:text-indigo-800">View All</button>
                            </div>
                            <div class="divide-y dark:divide-gray-700 max-h-96 overflow-y-auto">
                                <template x-for="brand in getConceptsByBrand()" :key="brand.brandId">
                                    <div x-data="{ expanded: false }">
                                        <button @click="expanded = !expanded" class="w-full p-4 flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4" :style="'border-left-color: ' + getBrandColor(brand.brandId)">
                                            <div class="flex items-center gap-2">
                                                <svg :class="expanded ? 'rotate-90' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                <span class="font-medium text-gray-900 dark:text-white" x-text="brand.brandName"></span>
                                                <span class="text-xs text-gray-500 bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded-full" x-text="brand.concepts.length"></span>
                                            </div>
                                        </button>
                                        <div x-show="expanded" class="divide-y dark:divide-gray-700">
                                            <template x-for="c in brand.concepts" :key="c._id">
                                                <div class="p-4 pl-10 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" @click="viewConcept(c)">
                                                    <div class="flex items-start justify-between gap-4">
                                                        <template x-if="getConceptImages(c).length > 0">
                                                            <img :src="getConceptImages(c)[0]" class="w-12 h-12 rounded object-cover shrink-0" :alt="c.title + ' thumbnail'" @error="$event.target.style.display='none'" />
                                                        </template>
                                                        <template x-if="getConceptImages(c).length === 0">
                                                            <div class="w-12 h-12 rounded bg-gray-200 dark:bg-gray-600 shrink-0 flex items-center justify-center">
                                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                            </div>
                                                        </template>
                                                        <div class="min-w-0 flex-1">
                                                            <p class="font-medium text-gray-900 dark:text-white truncate" x-text="c.title"></p>
                                                            <div class="mt-1 text-xs text-gray-500 flex flex-wrap gap-x-4 gap-y-1">
                                                                <span x-text="'Due: ' + formatDate(c.dueDate)"></span>
                                                                <span x-text="'Assigned to: ' + (c.assignedTo ? (c.assignedTo.firstName + ' ' + c.assignedTo.lastName) : 'Unassigned')"></span>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center gap-2 shrink-0">
                                                            <span :class="getStatusClass(c.status)" class="px-2 py-1 text-xs rounded-full" x-text="formatStatus(c.status)"></span>
                                                            <span :class="getPriorityClass(c.priority)" class="px-2 py-1 text-xs rounded-full" x-text="c.priority"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="assignedConcepts.length === 0" class="p-8 text-center text-gray-500">No assigned concepts yet</div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                            <div class="p-4 border-b dark:border-gray-700">
                                <h3 class="font-semibold text-gray-900 dark:text-white">Workload Distribution</h3>
                                <p class="text-xs text-gray-500 mt-1">Pending assigned concepts per creative</p>
                            </div>
                            <div class="p-4 space-y-4">
                                <template x-for="w in workloadByAssignee" :key="w.assignee?._id">
                                    <div @click="filterConceptsByAssignee(w.assignee?._id)" class="cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 p-2 -mx-2 rounded-lg transition-colors">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline" x-text="(w.assignee?.firstName || 'Unknown') + ' ' + (w.assignee?.lastName || '')"></p>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="w.pendingCount"></p>
                                        </div>
                                        <div class="mt-2 h-3 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden flex">
                                            <div class="h-full bg-gray-400" :style="'width:' + ((w.statusBreakdown?.draft || 0) / w.pendingCount * 100) + '%'" :title="'Draft: ' + (w.statusBreakdown?.draft || 0)"></div>
                                            <div class="h-full bg-blue-500" :style="'width:' + ((w.statusBreakdown?.in_progress || 0) / w.pendingCount * 100) + '%'" :title="'In Progress: ' + (w.statusBreakdown?.in_progress || 0)"></div>
                                            <div class="h-full bg-amber-500" :style="'width:' + ((w.statusBreakdown?.pending_review || 0) / w.pendingCount * 100) + '%'" :title="'Pending Review: ' + (w.statusBreakdown?.pending_review || 0)"></div>
                                            <div class="h-full bg-purple-500" :style="'width:' + ((w.statusBreakdown?.in_content_bank || 0) / w.pendingCount * 100) + '%'" :title="'Approved Internally: ' + (w.statusBreakdown?.in_content_bank || 0)"></div>
                                            <div class="h-full bg-teal-500" :style="'width:' + ((w.statusBreakdown?.client_approved || 0) / w.pendingCount * 100) + '%'" :title="'Completed: ' + (w.statusBreakdown?.client_approved || 0)"></div>
                                            <div class="h-full bg-red-500" :style="'width:' + ((w.statusBreakdown?.overdue || 0) / w.pendingCount * 100) + '%'" :title="'Overdue: ' + (w.statusBreakdown?.overdue || 0)"></div>
                                        </div>
                                        <div class="mt-1 flex flex-wrap gap-2 text-xs">
                                            <span x-show="w.statusBreakdown?.draft > 0" class="flex items-center gap-1">
                                                <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                                <span class="text-gray-600 dark:text-gray-400">Draft: <span x-text="w.statusBreakdown?.draft"></span></span>
                                            </span>
                                            <span x-show="w.statusBreakdown?.in_progress > 0" class="flex items-center gap-1">
                                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                                <span class="text-gray-600 dark:text-gray-400">In Progress: <span x-text="w.statusBreakdown?.in_progress"></span></span>
                                            </span>
                                            <span x-show="w.statusBreakdown?.pending_review > 0" class="flex items-center gap-1">
                                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                                <span class="text-gray-600 dark:text-gray-400">Pending Review: <span x-text="w.statusBreakdown?.pending_review"></span></span>
                                            </span>
                                            <span x-show="w.statusBreakdown?.in_content_bank > 0" class="flex items-center gap-1">
                                                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                                <span class="text-gray-600 dark:text-gray-400">Approved Internally: <span x-text="w.statusBreakdown?.in_content_bank"></span></span>
                                            </span>
                                            <span x-show="w.statusBreakdown?.client_approved > 0" class="flex items-center gap-1">
                                                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                                                <span class="text-gray-600 dark:text-gray-400">Completed: <span x-text="w.statusBreakdown?.client_approved"></span></span>
                                            </span>
                                            <span x-show="w.statusBreakdown?.overdue > 0" class="flex items-center gap-1">
                                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                                <span class="text-gray-600 dark:text-gray-400">Overdue: <span x-text="w.statusBreakdown?.overdue"></span></span>
                                            </span>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="workloadByAssignee.length === 0" class="py-6 text-center text-gray-500">No workload data yet</div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                            <div class="p-4 border-b dark:border-gray-700 flex items-center justify-between gap-3">
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Monthly Team Efficiency</h3>
                                    <p class="text-xs text-gray-500 mt-1">Assigned vs completed work for a selected month (completion rate per assignee)</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <label class="text-sm text-gray-600 dark:text-gray-400">Month:</label>
                                    <input type="month" :value="`${efficiencyYear}-${String(efficiencyMonth).padStart(2, '0')}`" @change="updateEfficiencyMonth($event.target.value)" class="px-3 py-1.5 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                </div>
                            </div>
                            <div class="p-4 space-y-3">
                                <template x-for="w in teamMonthlyEfficiency" :key="w.assignee?._id">
                                    <div @click="filterConceptsByEfficiency(w.assignee?._id)" class="p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 cursor-pointer hover:border-indigo-300 dark:hover:border-indigo-600 transition-colors">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400" x-text="(w.assignee?.firstName || 'Unknown') + ' ' + (w.assignee?.lastName || '')"></p>
                                            <span class="text-sm font-bold" :class="w.completionRate >= 70 ? 'text-green-600' : (w.completionRate >= 40 ? 'text-amber-600' : 'text-red-600')" x-text="w.completionRate + '%'"></span>
                                        </div>
                                        <div class="h-2 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden mb-2">
                                            <div class="h-full bg-indigo-600" :style="'width:' + w.completionRate + '%'"></div>
                                        </div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                            Completed <span class="font-semibold text-gray-900 dark:text-white" x-text="w.completedCount"></span> /
                                            Assigned <span class="font-semibold text-gray-900 dark:text-white" x-text="w.assignedCount"></span>
                                            · In Progress <span class="font-semibold text-blue-600" x-text="w.inProgressCount"></span>
                                        </p>
                                    </div>
                                </template>
                                <div x-show="teamMonthlyEfficiency.length === 0" class="py-6 text-center text-gray-500">No assignments found for this month</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
                        <div class="p-4 border-b dark:border-gray-700 flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Content Quota Tracking</h3>
                                <p class="text-xs text-gray-500 mt-1">Monthly post targets vs actual delivery (including carryover from previous years)</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="isQuotaOpen = !isQuotaOpen" class="px-2 py-1 text-xs rounded border dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" x-text="isQuotaOpen ? 'Hide' : 'Show'"></button>
                                <label class="text-sm text-gray-600 dark:text-gray-400">Year:</label>
                                <select x-model="quotaYear" @change="loadInitialData()" class="px-3 py-1.5 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                    <option value="2027">2027</option>
                                </select>
                            </div>
                        </div>
                        <div class="p-4" x-show="isQuotaOpen">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <template x-for="quota in contentQuotaTracking" :key="quota.clientId">
                                    <div class="border dark:border-gray-700 rounded-lg p-4" :class="quota.status === 'behind' ? 'border-l-4 border-l-red-500' : 'border-l-4 border-l-green-500'">
                                        <div class="flex items-start justify-between mb-3">
                                            <h4 class="font-medium text-gray-900 dark:text-white text-sm" x-text="quota.clientName"></h4>
                                            <span class="px-2 py-0.5 text-xs rounded-full" :class="quota.status === 'behind' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'" x-text="quota.status === 'behind' ? 'Behind' : 'On Track'"></span>
                                        </div>
                                        <div class="space-y-2 text-xs">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">Monthly Target:</span>
                                                <span class="font-semibold text-gray-900 dark:text-white" x-text="quota.monthlyTarget"></span>
                                            </div>
                                            <div class="flex justify-between" x-show="quota.postsThisMonth !== undefined && quota.year === new Date().getFullYear()">
                                                <span class="text-gray-600 dark:text-gray-400">This Month:</span>
                                                <span class="font-semibold" :class="quota.remainingThisMonth > 0 ? 'text-orange-600' : 'text-green-600'" x-text="quota.postsThisMonth + ' / ' + quota.monthlyTarget"></span>
                                            </div>
                                            <div class="flex justify-between pt-2 border-t dark:border-gray-700">
                                                <span class="text-gray-600 dark:text-gray-400">Owed in <span x-text="quota.year"></span>:</span>
                                                <span class="font-semibold text-gray-900 dark:text-white" x-text="quota.totalPostsOwedThisYear"></span>
                                            </div>
                                            <div class="flex justify-between" x-show="quota.carryoverFromPreviousYears > 0">
                                                <span class="text-gray-600 dark:text-gray-400">+ Carryover:</span>
                                                <span class="font-semibold text-orange-600" x-text="quota.carryoverFromPreviousYears"></span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">Posted in <span x-text="quota.year"></span>:</span>
                                                <span class="font-semibold text-gray-900 dark:text-white" x-text="quota.actualPostsPublishedThisYear"></span>
                                            </div>
                                            <div class="flex justify-between pt-2 border-t dark:border-gray-700">
                                                <span class="text-gray-600 dark:text-gray-400 font-medium">Remaining Needed:</span>
                                                <span class="font-bold" :class="quota.remainingNeeded > 0 ? 'text-red-600' : 'text-green-600'" x-text="quota.remainingNeeded"></span>
                                            </div>
                                            <div class="mt-3 pt-2 border-t dark:border-gray-700">
                                                <div class="flex justify-between mb-1">
                                                    <span class="text-gray-600 dark:text-gray-400">In Progress:</span>
                                                    <span class="text-blue-600 font-medium" x-text="quota.conceptsInProgress"></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">Approved:</span>
                                                    <span class="text-green-600 font-medium" x-text="quota.approvedConcepts"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="contentQuotaTracking.length === 0" class="col-span-full p-8 text-center text-gray-500">No quota data available</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
                        <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Approved Content Not Posted</h3>
                                <p class="text-xs text-gray-500 mt-1">Your approved/scheduled posts that haven't been published yet</p>
                            </div>
                            <a href="<?php echo esc_url(get_permalink(get_page_by_path('dashboard'))); ?>#content-calendar" class="text-sm text-indigo-600 hover:text-indigo-800">Open Calendar</a>
                        </div>
                        <div class="divide-y dark:divide-gray-700">
                            <template x-for="p in approvedNotPosted" :key="p._id">
                                <div class="p-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="(p.clientId?.brandName || p.clientId?.name || '—') + ' • ' + (p.platform || '—')"></p>
                                            <p class="text-xs text-gray-500 mt-1" x-text="p.scheduledDate ? ('Scheduled: ' + formatDate(p.scheduledDate)) : 'Not scheduled yet'"></p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300" x-text="p.status"></span>
                                    </div>
                                </div>
                            </template>
                            <div x-show="approvedNotPosted.length === 0" class="p-8 text-center text-gray-500">No approved content pending posting</div>
                        </div>
                    </div>
                </div>

                <!-- Concepts Tab -->
                <div x-show="activeTab === 'concepts'">
                    <!-- Header with filters and create button -->
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <div class="flex items-center space-x-4">
                            <input type="text" x-model="conceptSearch" @input.debounce.300ms="loadConcepts()" placeholder="Search concepts..." class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <select x-model="conceptStatusFilter" @change="loadConcepts()" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">All Status</option>
                                <option value="draft">Draft</option>
                                <option value="assigned">Assigned</option>
                                <option value="in_progress">In Progress</option>
                                <option value="needs_revision">Needs Revision</option>
                                <option value="pending_review">Pending Review</option>
                                <option value="in_content_bank">In Content Bank</option>
                                <option value="client_approved">Client Approved</option>
                                <option value="posted">Posted</option>
                                <option value="overdue">Overdue</option>
                            </select>
                            <select x-model="conceptClientFilter" @change="loadConcepts()" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">All Brands</option>
                                <template x-for="client in clients" :key="client._id">
                                    <option :value="client._id" x-text="client.brandName || client.name"></option>
                                </template>
                            </select>
                            <button x-show="efficiencyAssigneeFilter" type="button" @click="clearEfficiencyFilter()" class="px-3 py-2 text-xs rounded-lg border dark:border-gray-600 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                                Clear Efficiency Filter
                            </button>
                        </div>
                        <button @click="openConceptModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>New Concept</span>
                        </button>
                    </div>
                    <div x-show="efficiencyAssigneeFilter" class="mb-4 px-4 py-2 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 text-sm text-indigo-700 dark:text-indigo-300">
                        Showing concepts assigned in <span class="font-semibold" x-text="new Date(efficiencyYear, efficiencyMonth - 1, 1).toLocaleString('en-US', { month: 'long', year: 'numeric' })"></span> for this team member.
                    </div>

                    <!-- Concepts Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <template x-for="concept in concepts" :key="concept._id">
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow cursor-pointer overflow-hidden" @click="viewConcept(concept)">
                                <!-- Design Preview -->
                                <div class="relative h-80 bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                    <template x-if="getLatestDesignImage(concept)">
                                        <img :src="getLatestDesignImage(concept)" :alt="concept.title" class="w-full h-full object-cover" />
                                    </template>
                                    <template x-if="!getLatestDesignImage(concept)">
                                        <div class="flex items-center justify-center h-full">
                                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    </template>
                                    <!-- Status and Priority Badges -->
                                    <div class="absolute top-3 left-3">
                                        <span :class="getStatusClass(concept.status)" class="px-2 py-1 text-xs rounded-full shadow-lg" x-text="formatStatus(concept.status)"></span>
                                    </div>
                                    <div class="absolute top-3 right-3">
                                        <span :class="getPriorityClass(concept.priority)" class="px-2 py-1 text-xs rounded-full shadow-lg" x-text="concept.priority"></span>
                                    </div>
                                </div>
                                <div class="p-5">
                                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2" x-text="concept.title"></h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3 line-clamp-2" x-text="concept.description"></p>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-indigo-600 dark:text-indigo-400" x-text="concept.clientId?.brandName || concept.clientId?.name"></span>
                                        <span class="text-gray-500" x-text="concept.contentType?.replace('_', ' ')"></span>
                                    </div>
                                    <div class="mt-3 pt-3 border-t dark:border-gray-700 flex items-center justify-between text-sm">
                                        <div class="flex items-center text-gray-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span x-text="formatDate(concept.dueDate)"></span>
                                        </div>
                                        <div x-show="concept.assignedTo" class="flex items-center">
                                            <div class="w-6 h-6 bg-indigo-500 rounded-full flex items-center justify-center text-white text-xs" x-text="concept.assignedTo?.firstName?.charAt(0) || '?'"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div x-show="concepts.length === 0" class="text-center py-12 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p>No concepts found. Create your first concept to get started!</p>
                    </div>
                </div>

                <!-- Tasks Tab (My Assigned Concepts) -->
                <div x-show="activeTab === 'tasks'">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <div class="flex items-center space-x-4">
                            <select x-model="taskStatusFilter" @change="loadTasks()" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">All Status</option>
                                <option value="assigned">Assigned</option>
                                <option value="in_progress">In Progress</option>
                                <option value="needs_revision">Needs Revision</option>
                                <option value="pending_review">Pending Review</option>
                                <option value="in_content_bank">Approved</option>
                                <option value="client_approved">Client Approved</option>
                            </select>
                            <select x-model="taskPriorityFilter" @change="loadTasks()" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">All Priority</option>
                                <option value="urgent">Urgent</option>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                    </div>

                    <!-- My Assigned Concepts List -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Concept</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Brand</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Priority</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y dark:divide-gray-700">
                                <template x-for="task in tasks" :key="task._id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" @click="navigateTab('concepts'); $nextTick(() => viewConcept({_id: task._id}))">
                                        <td class="px-6 py-4">
                                            <p class="font-medium text-gray-900 dark:text-white" x-text="task.title"></p>
                                            <p class="text-xs text-gray-500 mt-1 truncate max-w-xs" x-text="task.description"></p>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500" x-text="task.clientId?.brandName || task.clientId?.name"></td>
                                        <td class="px-6 py-4 text-sm text-gray-500 capitalize" x-text="task.contentType?.replace('_', ' ')"></td>
                                        <td class="px-6 py-4 text-sm" :class="task.dueDate && new Date(task.dueDate) < new Date() && !['in_content_bank','client_approved','posted'].includes(task.status) ? 'text-red-600 font-medium' : 'text-gray-500'" x-text="formatDate(task.dueDate)"></td>
                                        <td class="px-6 py-4"><span :class="getPriorityClass(task.priority)" class="px-2 py-1 text-xs rounded-full" x-text="task.priority"></span></td>
                                        <td class="px-6 py-4"><span :class="getStatusClass(task.status)" class="px-2 py-1 text-xs rounded-full" x-text="task.status?.replace('_', ' ')"></span></td>
                                        <td class="px-6 py-4">
                                            <button @click.stop="navigateTab('concepts'); $nextTick(() => viewConcept({_id: task._id}))" class="px-3 py-1 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700">View</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <div x-show="tasks.length === 0" class="p-8 text-center text-gray-500">No assigned concepts found</div>
                    </div>
                </div>

                <!-- Production Tab -->
                <div x-show="activeTab === 'productions'">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <div class="flex items-center space-x-4">
                            <select x-model="productionStatusFilter" @change="loadProductions()" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">All Status</option>
                                <option value="scripting">Scripting</option>
                                <option value="script_review">Script Review</option>
                                <option value="filming">Filming</option>
                                <option value="editing">Editing</option>
                                <option value="internal_review">Internal Review</option>
                                <option value="client_review">Client Review</option>
                                <option value="revisions">Revisions</option>
                                <option value="approved">Approved</option>
                                <option value="final_delivery">Final Delivery</option>
                            </select>
                            <select x-model="productionClientFilter" @change="loadProductions()" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">All Clients</option>
                                <template x-for="client in clients" :key="client._id">
                                    <option :value="client._id" x-text="client.brandName || client.name"></option>
                                </template>
                            </select>
                        </div>
                        <button @click="openProductionModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            New Production
                        </button>
                    </div>

                    <!-- Production Projects List -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Project</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Client</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Editor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">SLA</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y dark:divide-gray-700">
                                <template x-for="project in productions" :key="project._id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" :class="project.isOverdue ? 'bg-red-50 dark:bg-red-900/20' : ''" @click="viewProduction(project)">
                                        <td class="px-6 py-4">
                                            <p class="font-medium text-gray-900 dark:text-white" x-text="project.title"></p>
                                            <p class="text-xs text-gray-500" x-text="project.videoType"></p>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500" x-text="project.clientId?.brandName || project.clientId?.name"></td>
                                        <td class="px-6 py-4">
                                            <span :class="getProductionStatusClass(project.status)" class="px-2 py-1 text-xs rounded-full" x-text="formatProductionStatus(project.status)"></span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500" x-text="project.editor ? (project.editor.firstName + ' ' + project.editor.lastName) : 'Unassigned'"></td>
                                        <td class="px-6 py-4 text-sm text-gray-500" x-text="formatDate(project.dueDate)"></td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-16 h-2 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                                                    <div :class="getSLABarClass(project)" :style="'width: ' + getSLAPercentage(project) + '%'" class="h-full"></div>
                                                </div>
                                                <span class="text-xs" :class="getSLATextClass(project)" x-text="getHoursInStatus(project) + 'h'"></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <button @click.stop="viewProduction(project)" class="text-indigo-600 hover:text-indigo-800 text-sm">View</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <div x-show="productions.length === 0" class="p-8 text-center text-gray-500">No production projects found</div>
                    </div>
                </div>

                <!-- Content Bank Tab -->
                <div x-show="activeTab === 'contentBank'">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <div class="flex items-center space-x-4">
                            <!-- Status Filter: Admin/Brand Rep see all statuses, Clients see only Pending/Approved -->
                            <select x-model="contentBankStatusFilter" @change="loadContentBank()" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <template x-if="viewMode === 'client'">
                                    <option value="pending" selected>Pending Approval</option>
                                </template>
                                <template x-if="viewMode === 'client'">
                                    <option value="approved">Approved</option>
                                </template>
                                <template x-if="viewMode !== 'client'">
                                    <option value="">All Status</option>
                                </template>
                                <template x-if="viewMode !== 'client'">
                                    <option value="pending">Awaiting Client Review</option>
                                </template>
                                <template x-if="viewMode !== 'client'">
                                    <option value="approved">Client Approved</option>
                                </template>
                                <template x-if="viewMode !== 'client'">
                                    <option value="needs_changes">Needs Changes</option>
                                </template>
                            </select>
                            <!-- Client Filter: Admin/Brand Rep see All Clients dropdown, Clients see their assigned brands -->
                            <template x-if="viewMode !== 'client'">
                                <select x-model="contentBankClientFilter" @change="loadContentBank()" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All Clients</option>
                                    <template x-for="client in clients" :key="client._id">
                                        <option :value="client._id" x-text="client.brandName || client.name"></option>
                                    </template>
                                </select>
                            </template>
                            <!-- Client view: show brand selector if multiple brands assigned -->
                            <template x-if="viewMode === 'client' && clients.length > 1">
                                <select x-model="contentBankClientFilter" @change="loadContentBank()" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All My Brands</option>
                                    <template x-for="client in clients" :key="client._id">
                                        <option :value="client._id" x-text="client.brandName || client.name"></option>
                                    </template>
                                </select>
                            </template>
                            <!-- Client view: show single brand name if only one brand assigned -->
                            <template x-if="viewMode === 'client' && clients.length === 1">
                                <span class="px-4 py-2 text-gray-700 dark:text-gray-300 font-medium" x-text="clients[0]?.brandName || clients[0]?.name"></span>
                            </template>
                        </div>
                        <button x-show="user.role === 'admin' || user.role === 'brand_rep'" @click="showManualUploadModal = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>Upload Pre-Approved Content</span>
                        </button>
                    </div>

                    <!-- Gallery Grid -->
                    <div x-show="contentBankItems.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <template x-for="item in contentBankItems" :key="item._id">
                            <div @click="viewContentBankItem(item)" class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden cursor-pointer transform transition-all duration-200 hover:scale-105 hover:shadow-xl">
                                <!-- Design Preview -->
                                <div class="relative h-80 bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                    <template x-if="getDesignPreview(item)">
                                        <img :src="getDesignPreview(item)" :alt="item.title" class="w-full h-full object-cover" />
                                    </template>
                                    <template x-if="!getDesignPreview(item)">
                                        <div class="flex items-center justify-center h-full">
                                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    </template>
                                    <!-- Approval Status Badge -->
                                    <div class="absolute top-3 right-3">
                                        <span :class="getClientApprovalClass(item.clientApprovalStatus)" class="px-3 py-1 text-xs font-semibold rounded-full shadow-lg" x-text="formatClientApprovalStatus(item.clientApprovalStatus)"></span>
                                    </div>
                                    <!-- Scheduled Badge -->
                                    <template x-if="item.scheduledPostDate">
                                        <div class="absolute bottom-3 left-3">
                                            <span class="px-3 py-1 text-xs font-semibold bg-blue-500 text-white rounded-full shadow-lg flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <span x-text="formatDate(item.scheduledPostDate)"></span>
                                            </span>
                                        </div>
                                    </template>
                                </div>
                                
                                <!-- Card Content -->
                                <div class="p-4">
                                    <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2 truncate" x-text="item.title"></h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2" x-text="item.description"></p>
                                    
                                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-3">
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            <span x-text="item.clientId?.brandName || item.clientId?.name"></span>
                                        </div>
                                        <div class="capitalize" x-text="item.contentType?.replace('_', ' ')"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <!-- Empty State -->
                    <div x-show="contentBankItems.length === 0" class="text-center py-16">
                        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No content in bank yet</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Approved concepts will appear here for client review</p>
                    </div>
                </div>

                <!-- Planner Tab -->
                <div x-show="activeTab === 'planner'" x-cloak>
                    
                    <!-- Plan List View (when no plan is open) -->
                    <div x-show="!activePlan">
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                            <div class="flex items-center space-x-4">
                                <select x-model="planStatusFilter" @change="loadPlans()" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="active">Active Plans</option>
                                    <option value="archived">Archived</option>
                                </select>
                                <input type="text" x-model="planSearchQuery" @input.debounce.300ms="loadPlans()" placeholder="Search plans..." class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <button @click="createNewPlan()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span>New Plan</span>
                            </button>
                        </div>

                        <!-- Plans Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <template x-for="plan in plans" :key="plan._id">
                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-all cursor-pointer group overflow-hidden" @click="openPlan(plan._id)">
                                    <!-- Cover -->
                                    <div class="h-32 relative" :style="`background: ${plan.coverColor || '#667eea'}`">
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"></path></svg>
                                        </div>
                                        <div class="absolute top-2 right-2 flex space-x-1">
                                            <span class="px-2 py-0.5 text-xs rounded-full bg-white/20 text-white" x-text="plan.slideCount + ' slides'"></span>
                                        </div>
                                        <!-- Permission badge -->
                                        <div class="absolute bottom-2 left-2">
                                            <span class="px-2 py-0.5 text-xs rounded-full" 
                                                :class="plan.myPermission === 'owner' ? 'bg-indigo-500 text-white' : plan.myPermission === 'edit' ? 'bg-green-500 text-white' : plan.myPermission === 'comment' ? 'bg-yellow-500 text-white' : 'bg-gray-500 text-white'"
                                                x-text="plan.myPermission === 'owner' ? 'Owner' : plan.myPermission.charAt(0).toUpperCase() + plan.myPermission.slice(1)"></span>
                                        </div>
                                    </div>
                                    <!-- Info -->
                                    <div class="p-4">
                                        <h3 class="font-bold text-gray-900 dark:text-white truncate" x-text="plan.title"></h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2" x-text="plan.description || 'No description'"></p>
                                        <div class="flex items-center justify-between mt-3 text-xs text-gray-400">
                                            <span x-text="'by ' + (plan.createdBy?.firstName || 'Unknown')"></span>
                                            <span x-text="plan.lastEditedAt ? 'Edited ' + new Date(plan.lastEditedAt).toLocaleDateString() : ''"></span>
                                        </div>
                                        <!-- Collaborators avatars -->
                                        <div class="flex items-center mt-3 -space-x-2" x-show="plan.collaborators?.length > 0">
                                            <template x-for="(collab, ci) in (plan.collaborators || []).slice(0, 4)" :key="ci">
                                                <div class="w-7 h-7 rounded-full bg-indigo-500 text-white text-xs flex items-center justify-center border-2 border-white dark:border-gray-800 font-medium" :title="collab.userId?.firstName + ' ' + collab.userId?.lastName" x-text="(collab.userId?.firstName?.[0] || '') + (collab.userId?.lastName?.[0] || '')"></div>
                                            </template>
                                            <div x-show="(plan.collaborators || []).length > 4" class="w-7 h-7 rounded-full bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-300 text-xs flex items-center justify-center border-2 border-white dark:border-gray-800" x-text="'+' + ((plan.collaborators || []).length - 4)"></div>
                                        </div>
                                    </div>
                                    <!-- Actions (on hover) -->
                                    <div class="px-4 pb-3 flex justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click.stop="duplicatePlan(plan._id)" class="p-1.5 text-gray-400 hover:text-indigo-600 rounded" title="Duplicate">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        </button>
                                        <button x-show="plan.isOwner" @click.stop="showSharePlanModal(plan)" class="p-1.5 text-gray-400 hover:text-green-600 rounded" title="Share">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                                        </button>
                                        <button x-show="plan.isOwner" @click.stop="archivePlan(plan._id)" class="p-1.5 text-gray-400 hover:text-yellow-600 rounded" :title="planStatusFilter === 'archived' ? 'Restore' : 'Archive'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                        </button>
                                        <button x-show="plan.isOwner" @click.stop="deletePlan(plan._id)" class="p-1.5 text-gray-400 hover:text-red-600 rounded" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Empty State -->
                        <div x-show="plans.length === 0" class="text-center py-16">
                            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"></path></svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No plans yet</h3>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Create a plan to start organizing your content strategy</p>
                            <button @click="createNewPlan()" class="mt-4 px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Create Your First Plan</button>
                        </div>
                    </div>

                    <!-- Slide Editor View (when a plan is open) -->
                    <div x-show="activePlan" x-cloak>
                        <!-- Editor Header -->
                        <div class="flex items-center justify-between mb-4 bg-white dark:bg-gray-800 rounded-lg shadow px-4 py-3">
                            <div class="flex items-center space-x-3">
                                <button @click="closePlanEditor()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="Back to plans">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <input type="text" :value="activePlan?.title || ''" @input="if(activePlan) activePlan.title = $event.target.value" @change="savePlanMeta()" class="text-lg font-bold bg-transparent border-none focus:ring-0 text-gray-900 dark:text-white w-64" :disabled="activePlan?.myPermission === 'view' || activePlan?.myPermission === 'comment'">
                            </div>
                            <div class="flex items-center space-x-2">
                                <!-- Collaborator avatars -->
                                <div class="flex -space-x-2 mr-2">
                                    <template x-for="(collab, ci) in (activePlan?.collaborators || []).slice(0, 3)" :key="ci">
                                        <div class="w-7 h-7 rounded-full bg-indigo-500 text-white text-xs flex items-center justify-center border-2 border-white dark:border-gray-800 font-medium" :title="collab.userId?.firstName + ' ' + collab.userId?.lastName + ' (' + collab.permission + ')'" x-text="(collab.userId?.firstName?.[0] || '') + (collab.userId?.lastName?.[0] || '')"></div>
                                    </template>
                                </div>
                                <button x-show="activePlan?.isOwner" @click="showSharePlanModal(activePlan)" class="px-3 py-1.5 text-sm border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                                    <span>Share</span>
                                </button>
                                <button x-show="activePlan?.myPermission === 'owner' || activePlan?.myPermission === 'edit'" @click="saveAllSlides()" class="px-3 py-1.5 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                    <span>Save</span>
                                </button>
                                <!-- Comments toggle -->
                                <button @click="showPlanComments = !showPlanComments" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg relative" title="Comments">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                    <span x-show="activePlan?.comments?.length > 0" class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center" x-text="activePlan?.comments?.length"></span>
                                </button>
                            </div>
                        </div>

                        <div class="flex gap-4" style="height: calc(100vh - 240px);">
                            <!-- Slide Thumbnails Panel -->
                            <div class="w-48 flex-shrink-0 bg-white dark:bg-gray-800 rounded-lg shadow overflow-y-auto p-3 space-y-3">
                                <template x-for="(slide, si) in (activePlan?.slides || [])" :key="slide._id || si">
                                    <div @click="activeSlideIndex = si" class="cursor-pointer rounded-lg border-2 transition-all p-1 relative" :class="activeSlideIndex === si ? 'border-indigo-500 shadow-md' : 'border-transparent hover:border-gray-300'">
                                        <div class="aspect-video rounded bg-gray-50 dark:bg-gray-700 relative overflow-hidden" :style="`background-color: ${slide.backgroundColor || '#ffffff'}`">
                                            <!-- Mini preview of elements -->
                                            <template x-for="(el, ei) in (slide.elements || []).slice(0, 5)" :key="ei">
                                                <div class="absolute text-[3px] leading-tight overflow-hidden" :style="`left:${el.x/10}%;top:${el.y/10}%;width:${el.width/10}px;height:${el.height/10}px;background:${el.type === 'image' ? '#e5e7eb' : el.style?.backgroundColor || 'transparent'};color:${el.style?.color || '#000'};border-radius:${(el.style?.borderRadius || 0)/10}px`">
                                                    <template x-if="el.type === 'text' || el.type === 'sticky_note'">
                                                        <span x-text="el.content?.substring(0, 20)"></span>
                                                    </template>
                                                    <template x-if="el.type === 'image' && el.src">
                                                        <img :src="el.src" class="w-full h-full object-cover">
                                                    </template>
                                                </div>
                                            </template>
                                            <!-- Delete slide button (top-right corner) -->
                                            <button x-show="activeSlideIndex === si && activePlan?.slides?.length > 1 && (activePlan?.myPermission === 'owner' || activePlan?.myPermission === 'edit')" @click.stop="deleteSlide(slide._id, si)" class="absolute top-0 right-0 p-1 bg-red-500 hover:bg-red-600 text-white rounded-bl-lg z-10" title="Delete slide">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                        <div class="text-xs text-center mt-1 text-gray-500 truncate" x-text="(si + 1) + '. ' + (slide.title || 'Untitled')"></div>
                                    </div>
                                </template>
                                <!-- Add Slide Button -->
                                <button x-show="activePlan?.myPermission === 'owner' || activePlan?.myPermission === 'edit'" @click="addSlide()" class="w-full aspect-video rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 flex items-center justify-center transition-colors">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>

                            <!-- Main Canvas Area -->
                            <div class="flex-1 bg-gray-100 dark:bg-gray-900 rounded-lg shadow overflow-hidden flex flex-col">
                                <!-- Toolbar -->
                                <div x-show="activePlan?.myPermission === 'owner' || activePlan?.myPermission === 'edit'" class="bg-white dark:bg-gray-800 border-b dark:border-gray-700 px-4 py-2 flex items-center space-x-2 flex-wrap">
                                    <button @click="addElement('text')" class="px-3 py-1.5 text-sm rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-1" title="Add Text">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        <span>Text</span>
                                    </button>
                                    <button @click="addElement('sticky_note')" class="px-3 py-1.5 text-sm rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-1" title="Add Sticky Note">
                                        <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8l6-6V5c0-1.1-.9-2-2-2zm-7 11H7v-2h5v2zm5-4H7V8h10v2z"></path></svg>
                                        <span>Note</span>
                                    </button>
                                    <button @click="$refs.planImageInput.click()" class="px-3 py-1.5 text-sm rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-1" title="Add Image">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span>Image</span>
                                    </button>
                                    <input type="file" x-ref="planImageInput" @change="uploadSlideImage($event)" accept="image/*" class="hidden">
                                    <button @click="addElement('shape')" class="px-3 py-1.5 text-sm rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-1" title="Add Shape">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"></path></svg>
                                        <span>Shape</span>
                                    </button>
                                    <div class="border-l dark:border-gray-600 h-6 mx-2"></div>
                                    <!-- Element style controls (shown when element selected) -->
                                    <template x-if="selectedElement !== null">
                                        <div class="flex items-center space-x-2">
                                            <input type="color" x-model="getSelectedElement().style.color" @input="markDirty()" class="w-7 h-7 rounded cursor-pointer" title="Text Color">
                                            <input type="color" x-model="getSelectedElement().style.backgroundColor" @input="markDirty()" class="w-7 h-7 rounded cursor-pointer" title="Background">
                                            <select x-model="getSelectedElement().style.fontSize" @change="markDirty()" class="text-xs px-2 py-1 border rounded dark:bg-gray-700 dark:border-gray-600">
                                                <option value="12">12px</option>
                                                <option value="14">14px</option>
                                                <option value="16">16px</option>
                                                <option value="20">20px</option>
                                                <option value="24">24px</option>
                                                <option value="32">32px</option>
                                                <option value="48">48px</option>
                                            </select>
                                            <button @click="getSelectedElement().style.fontWeight = getSelectedElement().style.fontWeight === 'bold' ? 'normal' : 'bold'; markDirty()" class="px-2 py-1 text-sm rounded hover:bg-gray-100 dark:hover:bg-gray-700 font-bold" :class="getSelectedElement().style?.fontWeight === 'bold' ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300' : ''">B</button>
                                            <button @click="deleteSelectedElement()" class="p-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded" title="Delete element">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </template>
                                    <div class="flex-1"></div>
                                    <!-- Slide background color -->
                                    <label class="text-xs text-gray-500 mr-1">Slide BG:</label>
                                    <input type="color" :value="getCurrentSlide()?.backgroundColor || '#ffffff'" @input="if(getCurrentSlide()) { getCurrentSlide().backgroundColor = $event.target.value; markDirty(); }" class="w-7 h-7 rounded cursor-pointer">
                                </div>

                                <!-- Canvas -->
                                <div class="flex-1 overflow-hidden p-4 flex items-center justify-center" @click="selectedElement = null">
                                    <div class="relative bg-white shadow-2xl" style="width: 960px; height: 540px; min-width: 960px; min-height: 540px; overflow: visible;" :style="`background-color: ${getCurrentSlide()?.backgroundColor || '#ffffff'}`" @dragover.prevent @drop.prevent="handleCanvasDrop($event)">
                                        <!-- Elements -->
                                        <template x-for="(el, ei) in (getCurrentSlide()?.elements || [])" :key="el._id || ei">
                                            <div class="absolute cursor-move group/el" 
                                                :style="`left:${el.x}px;top:${el.y}px;width:${el.width}px;height:${el.height}px;z-index:${el.zIndex || 1};transform:rotate(${el.rotation || 0}deg);opacity:${el.style?.opacity ?? 1}`"
                                                @click.stop="selectedElement = ei"
                                                @mousedown.stop="startDragElement($event, ei)"
                                                :class="selectedElement === ei ? 'ring-2 ring-indigo-500' : ''">
                                                
                                                <!-- Text Element -->
                                                <template x-if="el.type === 'text'">
                                                    <div class="w-full h-full overflow-hidden p-2" 
                                                        :style="`font-size:${el.style?.fontSize || 16}px;font-weight:${el.style?.fontWeight || 'normal'};color:${el.style?.color || '#000'};background:${el.style?.backgroundColor || 'transparent'};text-align:${el.style?.textAlign || 'left'};border-radius:${el.style?.borderRadius || 0}px;border:${el.style?.borderWidth || 0}px solid ${el.style?.borderColor || 'transparent'};user-select:none`"
                                                        :contenteditable="selectedElement === ei && (activePlan?.myPermission === 'owner' || activePlan?.myPermission === 'edit')"
                                                        @blur="el.content = $event.target.innerText; markDirty()"
                                                        @dblclick.stop="selectedElement = ei; $event.target.contentEditable = 'true'; $event.target.focus(); $event.target.style.userSelect = 'text'"
                                                        @mousedown.stop="if($event.target.contentEditable !== 'true') { startDragElement($event, ei) } else { $event.stopPropagation() }"
                                                        x-text="el.content"></div>
                                                </template>

                                                <!-- Sticky Note -->
                                                <template x-if="el.type === 'sticky_note'">
                                                    <div class="w-full h-full p-3 shadow-md" 
                                                        :style="`background:${el.style?.backgroundColor || '#fef3c7'};border-radius:4px;font-size:${el.style?.fontSize || 14}px;color:${el.style?.color || '#92400e'};user-select:none`"
                                                        :contenteditable="selectedElement === ei && (activePlan?.myPermission === 'owner' || activePlan?.myPermission === 'edit')"
                                                        @blur="el.content = $event.target.innerText; markDirty()"
                                                        @dblclick.stop="selectedElement = ei; $event.target.contentEditable = 'true'; $event.target.focus(); $event.target.style.userSelect = 'text'"
                                                        @mousedown.stop="if($event.target.contentEditable !== 'true') { startDragElement($event, ei) } else { $event.stopPropagation() }"
                                                        x-text="el.content"></div>
                                                </template>

                                                <!-- Image Element -->
                                                <template x-if="el.type === 'image'">
                                                    <img :src="el.src" class="w-full h-full object-contain pointer-events-none" :style="`border-radius:${el.style?.borderRadius || 0}px`" draggable="false">
                                                </template>

                                                <!-- Shape Element -->
                                                <template x-if="el.type === 'shape'">
                                                    <div class="w-full h-full" :style="`background:${el.style?.backgroundColor || '#e5e7eb'};border-radius:${el.style?.borderRadius || 0}px;border:${el.style?.borderWidth || 2}px solid ${el.style?.borderColor || '#9ca3af'}`" @click.stop="selectedElement = ei"></div>
                                                </template>

                                                <!-- Resize handle -->
                                                <div x-show="selectedElement === ei && (activePlan?.myPermission === 'owner' || activePlan?.myPermission === 'edit')" class="absolute bottom-0 right-0 w-3 h-3 bg-indigo-500 cursor-se-resize" @mousedown.stop="startResizeElement($event, ei)"></div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Slide Notes -->
                                <div class="bg-white dark:bg-gray-800 border-t dark:border-gray-700 px-4 py-2">
                                    <input type="text" :value="getCurrentSlide()?.notes || ''" @input="if(getCurrentSlide()) { getCurrentSlide().notes = $event.target.value; markDirty(); }" placeholder="Slide notes..." class="w-full text-sm bg-transparent border-none focus:ring-0 text-gray-600 dark:text-gray-400" :disabled="activePlan?.myPermission === 'view'">
                                </div>
                            </div>

                            <!-- Comments Panel -->
                            <div x-show="showPlanComments" class="w-72 flex-shrink-0 bg-white dark:bg-gray-800 rounded-lg shadow flex flex-col overflow-hidden">
                                <div class="p-3 border-b dark:border-gray-700 flex items-center justify-between">
                                    <h4 class="font-semibold text-sm">Comments</h4>
                                    <button @click="showPlanComments = false" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                                <div class="flex-1 overflow-y-auto p-3 space-y-3">
                                    <template x-for="(comment, ci) in (activePlan?.comments || [])" :key="ci">
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs font-medium text-gray-900 dark:text-white" x-text="(comment.userId?.firstName || '') + ' ' + (comment.userId?.lastName || '')"></span>
                                                <span class="text-xs text-gray-400" x-text="new Date(comment.createdAt).toLocaleDateString()"></span>
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-300" x-text="comment.message"></p>
                                            <span x-show="comment.slideIndex !== null && comment.slideIndex !== undefined" class="text-xs text-indigo-500 mt-1 inline-block cursor-pointer hover:underline" @click="activeSlideIndex = comment.slideIndex" x-text="'Slide ' + ((comment.slideIndex || 0) + 1)"></span>
                                        </div>
                                    </template>
                                    <div x-show="!activePlan?.comments?.length" class="text-center text-sm text-gray-400 py-4">No comments yet</div>
                                </div>
                                <!-- Add comment -->
                                <div x-show="activePlan?.myPermission !== 'view'" class="p-3 border-t dark:border-gray-700">
                                    <div class="flex space-x-2">
                                        <input type="text" x-model="newPlanComment" @keydown.enter="addPlanComment()" placeholder="Add comment..." class="flex-1 text-sm px-3 py-1.5 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <button @click="addPlanComment()" class="px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Send</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feed Tab -->
                <div x-show="activeTab === 'feed'" x-cloak>
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Activity Feed</h2>
                        <div class="flex items-center gap-3">
                            <!-- Client Filter -->
                            <select x-model="feedClientFilter" @change="loadFeed()" class="px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                <option value="">All Clients</option>
                                <template x-for="client in clients" :key="client._id">
                                    <option :value="client._id" x-text="client.brandName || client.name"></option>
                                </template>
                            </select>
                            <!-- Type Filter -->
                            <select x-model="feedTypeFilter" @change="loadFeed()" class="px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                <option value="">All Activity</option>
                                <option value="team_comment">Team Comments</option>
                                <option value="client_feedback">Client Feedback</option>
                            </select>
                        </div>
                    </div>

                    <!-- Loading -->
                    <div x-show="feedLoading" class="flex justify-center py-12">
                        <svg class="animate-spin h-8 w-8 text-indigo-500" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>

                    <!-- Feed Items -->
                    <div x-show="!feedLoading" class="space-y-4">
                        <template x-for="item in feedItems" :key="item._id">
                            <div @click="openConceptFromFeed(item.conceptId)" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow cursor-pointer hover:border-indigo-300 dark:hover:border-indigo-600">
                                <div class="flex">
                                    <!-- Thumbnail -->
                                    <div x-show="item.thumbnail" class="w-20 h-20 md:w-28 md:h-28 flex-shrink-0 bg-gray-100 dark:bg-gray-700">
                                        <img :src="item.thumbnail" class="w-full h-full object-cover" :alt="item.conceptTitle">
                                    </div>
                                    <div x-show="!item.thumbnail" class="w-20 h-20 md:w-28 md:h-28 flex-shrink-0 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <!-- Content -->
                                    <div class="flex-1 p-4">
                                        <div class="flex items-start justify-between mb-1">
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <svg class="w-4 h-4" :class="getFeedColor(item.type)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getFeedIcon(item.type)"></path></svg>
                                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full" :class="item.type === 'client_feedback' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'" x-text="getFeedLabel(item.type)"></span>
                                                    <span class="text-xs text-gray-400" x-text="formatDateTime(item.createdAt)"></span>
                                                </div>
                                                <h4 class="font-semibold text-gray-900 dark:text-white text-sm" x-text="item.conceptTitle"></h4>
                                            </div>
                                            <span class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-full whitespace-nowrap" x-text="item.clientName"></span>
                                        </div>
                                        <div class="flex items-start gap-2 mt-2">
                                            <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0">
                                                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400" x-text="item.from?.firstName?.charAt(0) || '?'"></span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300" x-text="item.from?.firstName ? item.from.firstName + ' ' + (item.from.lastName || '') : 'Unknown'"></span>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5 line-clamp-2" x-text="item.message"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Empty State -->
                        <div x-show="feedItems.length === 0 && !feedLoading" class="text-center py-16">
                            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                            <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400">No activity yet</h3>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Comments and feedback will appear here as they come in</p>
                        </div>

                        <!-- Pagination -->
                        <div x-show="feedTotalPages > 1" class="flex justify-center gap-2 pt-4">
                            <button @click="if(feedPage > 1) { feedPage--; loadFeed(); }" :disabled="feedPage <= 1" class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Previous</button>
                            <span class="px-3 py-1.5 text-sm text-gray-500" x-text="'Page ' + feedPage + ' of ' + feedTotalPages"></span>
                            <button @click="if(feedPage < feedTotalPages) { feedPage++; loadFeed(); }" :disabled="feedPage >= feedTotalPages" class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Next</button>
                        </div>
                    </div>
                </div>

        </main>

        <!-- Share Plan Modal -->
        <div x-show="showShareModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="showShareModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
                    <div class="p-6 border-b dark:border-gray-700 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Share Plan</h2>
                        <button @click="showShareModal = false" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Add collaborator -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Add Team Member</label>
                            <div class="flex space-x-2">
                                <select x-model="shareForm.userId" class="flex-1 px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                    <option value="">Select member...</option>
                                    <template x-for="member in teamMembers.filter(m => m._id !== user?._id && !sharePlanCollaborators.find(c => c.userId === m._id || c.userId?._id === m._id))" :key="member._id">
                                        <option :value="member._id" x-text="member.firstName + ' ' + member.lastName + ' (' + member.role + ')'"></option>
                                    </template>
                                </select>
                                <select x-model="shareForm.permission" class="px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                    <option value="view">View</option>
                                    <option value="comment">Comment</option>
                                    <option value="edit">Edit</option>
                                </select>
                                <button @click="addCollaborator()" class="px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">Add</button>
                            </div>
                        </div>
                        <!-- Current collaborators -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Collaborators</label>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                <template x-for="(collab, ci) in sharePlanCollaborators" :key="ci">
                                    <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded-lg px-3 py-2">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-8 h-8 rounded-full bg-indigo-500 text-white text-xs flex items-center justify-center font-medium" x-text="(collab.userId?.firstName?.[0] || collab.firstName?.[0] || '') + (collab.userId?.lastName?.[0] || collab.lastName?.[0] || '')"></div>
                                            <span class="text-sm text-gray-900 dark:text-white" x-text="(collab.userId?.firstName || collab.firstName || '') + ' ' + (collab.userId?.lastName || collab.lastName || '')"></span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <select x-model="collab.permission" class="text-xs px-2 py-1 border rounded dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                                <option value="view">View</option>
                                                <option value="comment">Comment</option>
                                                <option value="edit">Edit</option>
                                            </select>
                                            <button @click="sharePlanCollaborators.splice(ci, 1)" class="p-1 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="sharePlanCollaborators.length === 0" class="text-sm text-gray-400 text-center py-2">No collaborators yet</div>
                            </div>
                        </div>
                        <button @click="saveCollaborators()" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Save Sharing Settings</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Concept Modal -->
        <div x-show="showConceptModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="showConceptModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6 border-b dark:border-gray-700">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white" x-text="editingConcept ? 'Edit Concept' : 'Create New Concept'"></h2>
                    </div>
                    <form @submit.prevent="saveConcept()" class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title *</label>
                                <input type="text" x-model="conceptForm.title" required class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description * <span class="text-xs text-gray-500 font-normal">(Instructions for designer)</span></label>
                                <div class="flex items-center justify-end gap-2 mb-2">
                                    <label class="text-xs text-gray-500">Text color</label>
                                    <input type="color" x-model="conceptForm.briefDetails.descriptionColor" class="h-7 w-9 p-0 border rounded cursor-pointer bg-transparent">
                                </div>
                                <textarea x-model="conceptForm.description" required rows="3" :style="`color:${conceptForm.briefDetails.descriptionColor || '#111827'}`" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" placeholder="Instructions for the designer to create this content..."></textarea>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Caption <span class="text-xs text-gray-500 font-normal">(Post text for client review)</span></label>
                                <div class="flex items-center justify-end gap-2 mb-2">
                                    <label class="text-xs text-gray-500">Text color</label>
                                    <input type="color" x-model="conceptForm.briefDetails.captionColor" class="h-7 w-9 p-0 border rounded cursor-pointer bg-transparent">
                                </div>
                                <textarea x-model="conceptForm.caption" rows="3" :style="`color:${conceptForm.briefDetails.captionColor || '#6B7280'}`" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" placeholder="The caption/text that will be posted with this content..."></textarea>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reference Images (up to 2)</label>
                                <div class="flex items-start gap-4">
                                    <!-- Reference Image 1 -->
                                    <div class="flex-1">
                                        <div class="w-full h-24 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 overflow-hidden bg-gray-50 dark:bg-gray-700 flex items-center justify-center mb-2 cursor-pointer transition-colors"
                                            :class="{'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20': dragOverIndex === 0}"
                                            @dragover.prevent="dragOverIndex = 0"
                                            @dragleave.prevent="dragOverIndex = null"
                                            @drop.prevent="handleReferenceDrop($event, 0)"
                                            @click="$refs.refInput0.click()">
                                            <template x-if="referenceImagePreviews[0]">
                                                <img :src="referenceImagePreviews[0]" alt="Reference image 1" class="w-full h-full object-cover" />
                                            </template>
                                            <template x-if="!referenceImagePreviews[0]">
                                                <div class="text-xs text-gray-500 text-center px-2">Image 1<br><span class="text-gray-400">Drop or click</span></div>
                                            </template>
                                        </div>
                                        <input type="file" accept="image/*" @change="handleReferenceImage($event, 0)" x-ref="refInput0" class="hidden" />
                                        <div class="mt-1 flex items-center gap-2">
                                            <button type="button" x-show="referenceImageFiles[0]" @click="clearReferenceImage(0)" class="text-xs text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Remove</button>
                                            <span class="text-xs text-gray-500 truncate" x-show="referenceImageFiles[0]" x-text="referenceImageFiles[0]?.name"></span>
                                        </div>
                                    </div>
                                    <!-- Reference Image 2 -->
                                    <div class="flex-1">
                                        <div class="w-full h-24 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 overflow-hidden bg-gray-50 dark:bg-gray-700 flex items-center justify-center mb-2 cursor-pointer transition-colors"
                                            :class="{'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20': dragOverIndex === 1}"
                                            @dragover.prevent="dragOverIndex = 1"
                                            @dragleave.prevent="dragOverIndex = null"
                                            @drop.prevent="handleReferenceDrop($event, 1)"
                                            @click="$refs.refInput1.click()">
                                            <template x-if="referenceImagePreviews[1]">
                                                <img :src="referenceImagePreviews[1]" alt="Reference image 2" class="w-full h-full object-cover" />
                                            </template>
                                            <template x-if="!referenceImagePreviews[1]">
                                                <div class="text-xs text-gray-500 text-center px-2">Image 2<br><span class="text-gray-400">Drop or click</span></div>
                                            </template>
                                        </div>
                                        <input type="file" accept="image/*" @change="handleReferenceImage($event, 1)" x-ref="refInput1" class="hidden" />
                                        <div class="mt-1 flex items-center gap-2">
                                            <button type="button" x-show="referenceImageFiles[1]" @click="clearReferenceImage(1)" class="text-xs text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Remove</button>
                                            <span class="text-xs text-gray-500 truncate" x-show="referenceImageFiles[1]" x-text="referenceImageFiles[1]?.name"></span>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">PNG/JPG up to 10MB each. Useful for carousel posts or multiple references.</p>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reference Link</label>
                                <input type="url" x-model="conceptForm.referenceLink" placeholder="https://example.com/animation-reference" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                                <p class="mt-1 text-xs text-gray-500">For animations/motion graphics, paste a link to the reference video or animation.</p>
                            </div>
                            <!-- Brand Searchable Dropdown -->
                            <div class="relative" x-data="{ open: false, search: '' }" @click.away="open = false">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Brand *</label>
                                <div class="relative">
                                    <input type="text" 
                                        x-model="search" 
                                        @focus="open = true" 
                                        @input="open = true"
                                        :placeholder="conceptForm.clientId ? clients.find(c => c._id === conceptForm.clientId)?.brandName || clients.find(c => c._id === conceptForm.clientId)?.name || 'Select Brand' : 'Select Brand'"
                                        class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white pr-8">
                                    <button type="button" @click="open = !open" class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                </div>
                                <div x-show="open" x-cloak class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                    <template x-for="client in clients.filter(c => (c.brandName || c.name).toLowerCase().includes(search.toLowerCase()))" :key="client._id">
                                        <div @click="conceptForm.clientId = client._id; search = ''; open = false" 
                                            class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200"
                                            :class="{'bg-indigo-50 dark:bg-indigo-900/30': conceptForm.clientId === client._id}"
                                            x-text="client.brandName || client.name"></div>
                                    </template>
                                    <div x-show="clients.filter(c => (c.brandName || c.name).toLowerCase().includes(search.toLowerCase())).length === 0" class="px-4 py-2 text-gray-500 dark:text-gray-400">No brands found</div>
                                </div>
                                <input type="hidden" x-model="conceptForm.clientId" required>
                            </div>
                            <!-- Content Type Searchable Dropdown -->
                            <div class="relative" x-data="{ open: false, search: '', types: [{value: 'graphic', label: 'Graphic'}, {value: 'motion_graphic', label: 'Motion Graphic'}, {value: 'video', label: 'Video'}, {value: 'carousel', label: 'Carousel'}, {value: 'story', label: 'Story'}, {value: 'reel', label: 'Reel'}] }" @click.away="open = false">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content Type *</label>
                                <div class="relative">
                                    <input type="text" 
                                        x-model="search" 
                                        @focus="open = true" 
                                        @input="open = true"
                                        :placeholder="conceptForm.contentType ? types.find(t => t.value === conceptForm.contentType)?.label || 'Select Type' : 'Select Type'"
                                        class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white pr-8">
                                    <button type="button" @click="open = !open" class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                </div>
                                <div x-show="open" x-cloak class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                    <template x-for="type in types.filter(t => t.label.toLowerCase().includes(search.toLowerCase()))" :key="type.value">
                                        <div @click="conceptForm.contentType = type.value; search = ''; open = false" 
                                            class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200"
                                            :class="{'bg-indigo-50 dark:bg-indigo-900/30': conceptForm.contentType === type.value}"
                                            x-text="type.label"></div>
                                    </template>
                                    <div x-show="types.filter(t => t.label.toLowerCase().includes(search.toLowerCase())).length === 0" class="px-4 py-2 text-gray-500 dark:text-gray-400">No types found</div>
                                </div>
                                <input type="hidden" x-model="conceptForm.contentType" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Platform(s) *</label>
                                <div class="flex flex-wrap gap-3">
                                    <!-- Facebook -->
                                    <button type="button" @click="conceptForm.platform.includes('facebook') ? conceptForm.platform = conceptForm.platform.filter(x => x !== 'facebook') : conceptForm.platform.push('facebook')"
                                        :class="conceptForm.platform.includes('facebook') ? 'bg-blue-600 text-white ring-2 ring-blue-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                        class="w-10 h-10 rounded-lg flex items-center justify-center transition-all" title="Facebook">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    </button>
                                    <!-- Instagram -->
                                    <button type="button" @click="conceptForm.platform.includes('instagram') ? conceptForm.platform = conceptForm.platform.filter(x => x !== 'instagram') : conceptForm.platform.push('instagram')"
                                        :class="conceptForm.platform.includes('instagram') ? 'bg-gradient-to-br from-purple-600 via-pink-500 to-orange-400 text-white ring-2 ring-pink-500' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                        class="w-10 h-10 rounded-lg flex items-center justify-center transition-all" title="Instagram">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                    </button>
                                    <!-- LinkedIn -->
                                    <button type="button" @click="conceptForm.platform.includes('linkedin') ? conceptForm.platform = conceptForm.platform.filter(x => x !== 'linkedin') : conceptForm.platform.push('linkedin')"
                                        :class="conceptForm.platform.includes('linkedin') ? 'bg-blue-700 text-white ring-2 ring-blue-700' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                        class="w-10 h-10 rounded-lg flex items-center justify-center transition-all" title="LinkedIn">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                    </button>
                                    <!-- YouTube -->
                                    <button type="button" @click="conceptForm.platform.includes('youtube') ? conceptForm.platform = conceptForm.platform.filter(x => x !== 'youtube') : conceptForm.platform.push('youtube')"
                                        :class="conceptForm.platform.includes('youtube') ? 'bg-red-600 text-white ring-2 ring-red-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                        class="w-10 h-10 rounded-lg flex items-center justify-center transition-all" title="YouTube">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                    </button>
                                    <!-- X (Twitter) -->
                                    <button type="button" @click="conceptForm.platform.includes('x') ? conceptForm.platform = conceptForm.platform.filter(p => p !== 'x') : conceptForm.platform.push('x')"
                                        :class="conceptForm.platform.includes('x') ? 'bg-black text-white ring-2 ring-gray-800' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                        class="w-10 h-10 rounded-lg flex items-center justify-center transition-all" title="X">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                    </button>
                                </div>
                            </div>
                            <!-- Priority Searchable Dropdown -->
                            <div class="relative" x-data="{ open: false, search: '', priorities: [{value: 'low', label: 'Low'}, {value: 'medium', label: 'Medium'}, {value: 'high', label: 'High'}, {value: 'urgent', label: 'Urgent'}] }" @click.away="open = false">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
                                <div class="relative">
                                    <input type="text" 
                                        x-model="search" 
                                        @focus="open = true" 
                                        @input="open = true"
                                        :placeholder="priorities.find(p => p.value === conceptForm.priority)?.label || 'Medium'"
                                        class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white pr-8">
                                    <button type="button" @click="open = !open" class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                </div>
                                <div x-show="open" x-cloak class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                    <template x-for="priority in priorities.filter(p => p.label.toLowerCase().includes(search.toLowerCase()))" :key="priority.value">
                                        <div @click="conceptForm.priority = priority.value; search = ''; open = false" 
                                            class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200"
                                            :class="{'bg-indigo-50 dark:bg-indigo-900/30': conceptForm.priority === priority.value}"
                                            x-text="priority.label"></div>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Due Date *</label>
                                <input type="date" x-model="conceptForm.dueDate" required class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <!-- Assign To Searchable Dropdown -->
                            <div class="relative" x-data="{ open: false, search: '' }" @click.away="open = false">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assign To (Creative)</label>
                                <div class="relative">
                                    <input type="text" 
                                        x-model="search" 
                                        @focus="open = true" 
                                        @input="open = true"
                                        :placeholder="conceptForm.assignedTo ? (teamMembers.find(u => u._id === conceptForm.assignedTo) ? teamMembers.find(u => u._id === conceptForm.assignedTo).firstName + ' ' + teamMembers.find(u => u._id === conceptForm.assignedTo).lastName : 'Unassigned') : 'Unassigned'"
                                        class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white pr-8">
                                    <button type="button" @click="open = !open" class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                </div>
                                <div x-show="open" x-cloak class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                    <div @click="conceptForm.assignedTo = ''; search = ''; open = false" 
                                        class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200"
                                        :class="{'bg-indigo-50 dark:bg-indigo-900/30': !conceptForm.assignedTo}">Unassigned</div>
                                    <template x-for="u in teamMembers.filter(m => (m.firstName + ' ' + m.lastName).toLowerCase().includes(search.toLowerCase()))" :key="u._id">
                                        <div @click="conceptForm.assignedTo = u._id; search = ''; open = false" 
                                            class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200"
                                            :class="{'bg-indigo-50 dark:bg-indigo-900/30': conceptForm.assignedTo === u._id}"
                                            x-text="u.firstName + ' ' + u.lastName"></div>
                                    </template>
                                    <div x-show="teamMembers.filter(m => (m.firstName + ' ' + m.lastName).toLowerCase().includes(search.toLowerCase())).length === 0 && search" class="px-4 py-2 text-gray-500 dark:text-gray-400">No team members found</div>
                                </div>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Key Message</label>
                                <input type="text" x-model="conceptForm.briefDetails.keyMessage" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Call to Action</label>
                                <input type="text" x-model="conceptForm.briefDetails.callToAction" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Additional Notes</label>
                                <textarea x-model="conceptForm.briefDetails.additionalNotes" rows="2" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 pt-4 border-t dark:border-gray-700">
                            <button type="button" @click="showConceptModal = false" class="px-4 py-2 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                <span x-text="editingConcept ? 'Update Concept' : 'Create Concept'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- View Concept Detail Modal -->
        <div x-show="showConceptDetail" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="showConceptDetail = false; loadConcepts()"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6 border-b dark:border-gray-700">
                        <div class="flex gap-6 items-start">
                            <template x-if="getLatestDesignImage(selectedConcept)">
                                <button @click="lightboxImage = getLatestDesignImage(selectedConcept)" class="shrink-0 cursor-pointer hover:opacity-90 transition-opacity focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-lg">
                                    <img :src="getLatestDesignImage(selectedConcept)" class="w-48 h-48 rounded-lg object-cover border border-gray-200 dark:border-gray-600" alt="Latest design" @error="$event.target.style.display='none'" />
                                </button>
                            </template>
                            <template x-if="!getLatestDesignImage(selectedConcept)">
                                <div class="w-48 h-48 rounded-lg bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 shrink-0 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            </template>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1 min-w-0">
                                        <h2 class="text-xl font-bold text-gray-900 dark:text-white" x-text="selectedConcept?.title"></h2>
                                        <p class="text-sm text-gray-500" x-text="selectedConcept?.clientId?.brandName || selectedConcept?.clientId?.name"></p>
                                    </div>
                                    <div class="flex items-center space-x-2 shrink-0">
                                        <span :class="getStatusClass(selectedConcept?.status)" class="px-3 py-1 text-sm rounded-full" x-text="formatStatus(selectedConcept?.status)"></span>
                                        <button @click="showConceptDetail = false; loadConcepts()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Content Type</p>
                                <p class="font-medium text-gray-900 dark:text-white capitalize" x-text="selectedConcept?.contentType?.replace('_', ' ')"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Due Date</p>
                                <p class="font-medium text-gray-900 dark:text-white" x-text="formatDate(selectedConcept?.dueDate)"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Assigned To</p>
                                <p class="font-medium text-gray-900 dark:text-white" x-text="selectedConcept?.assignedTo ? selectedConcept.assignedTo.firstName + ' ' + selectedConcept.assignedTo.lastName : 'Unassigned'"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Assigned By</p>
                                <p class="font-medium text-gray-900 dark:text-white" x-text="selectedConcept?.createdBy ? selectedConcept.createdBy.firstName + ' ' + selectedConcept.createdBy.lastName : 'Unknown'"></p>
                            </div>
                        </div>
                        <div class="mb-6">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Description</p>
                            <p class="whitespace-pre-wrap" :style="`color:${selectedConcept?.briefDetails?.descriptionColor || '#111827'}`" x-text="selectedConcept?.description"></p>
                        </div>
                        <div x-show="selectedConcept?.caption" class="mb-6">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Caption</p>
                            <p class="whitespace-pre-wrap" :style="`color:${selectedConcept?.briefDetails?.captionColor || '#6B7280'}`" x-text="selectedConcept?.caption"></p>
                        </div>
                        <div x-show="selectedConcept?.briefDetails?.keyMessage" class="mb-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Key Message</p>
                            <p class="text-gray-900 dark:text-white" x-text="selectedConcept?.briefDetails?.keyMessage"></p>
                        </div>
                        <!-- Reference Images Preview -->
                        <div x-show="getReferenceAttachments(selectedConcept).length > 0 || selectedConcept?.referenceLink" class="mb-6">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">References</p>
                            <div class="flex flex-wrap gap-4">
                                <template x-for="attachment in getReferenceAttachments(selectedConcept)" :key="attachment._id">
                                    <div class="relative group">
                                        <button type="button" @click="lightboxImage = attachment.url" class="block cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-lg">
                                            <img :src="attachment.url" :alt="'Reference image'" class="h-32 w-auto rounded-lg border border-gray-200 dark:border-gray-600 object-cover hover:opacity-90 transition-opacity" @error="$event.target.style.display='none'; $event.target.nextElementSibling.style.display='flex'" />
                                            <div class="hidden h-32 w-32 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 items-center justify-center">
                                                <div class="text-center p-2">
                                                    <svg class="w-8 h-8 mx-auto text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    <span class="text-xs text-gray-500">Image unavailable</span>
                                                </div>
                                            </div>
                                        </button>
                                        <button @click.stop="deleteAttachment(selectedConcept._id, attachment._id)" class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity shadow-lg">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </template>
                                <template x-if="selectedConcept?.referenceLink">
                                    <a :href="selectedConcept?.referenceLink" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center h-32 w-32 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <div class="text-center p-2">
                                            <svg class="w-8 h-8 mx-auto text-indigo-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                            <span class="text-xs text-gray-600 dark:text-gray-300">Reference Link</span>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>

                        <!-- Uploaded Designs Section -->
                        <div x-show="['in_progress', 'needs_revision', 'rejected', 'pending_review'].includes(selectedConcept?.status)" class="border-t dark:border-gray-700 pt-6 mt-6 mb-6">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">
                                <span x-show="selectedConcept?.status === 'needs_revision' || selectedConcept?.status === 'rejected'">Upload New Design (Replace Previous)</span>
                                <span x-show="selectedConcept?.status !== 'needs_revision' && selectedConcept?.status !== 'rejected'">Uploaded Designs</span>
                            </h3>
                            
                            <!-- Revision Notice -->
                            <div x-show="selectedConcept?.status === 'needs_revision' || selectedConcept?.status === 'rejected'" class="mb-4 p-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-orange-800 dark:text-orange-300">Revisions Requested</p>
                                        <p class="text-sm text-orange-700 dark:text-orange-400 mt-1">Upload your revised design below and click "Submit for Review" when ready.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Designs List -->
                            <div x-show="selectedConcept?.attachments?.length > 0" class="mb-4 space-y-2">
                                <template x-for="attachment in selectedConcept?.attachments" :key="attachment._id">
                                    <div x-show="attachment?.kind === 'design' || (!attachment?.kind && !attachment?.mimetype?.startsWith('image/'))" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <svg class="w-5 h-5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="attachment.originalName || attachment.filename"></p>
                                                <p class="text-xs text-gray-500" x-text="formatDateTime(attachment.uploadedAt)"></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <button @click="downloadAttachment(attachment)" class="px-3 py-1 text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">Download</button>
                                            <button @click="deleteAttachment(selectedConcept._id, attachment._id)" class="p-1 text-red-600 hover:text-red-800 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Upload Design -->
                            <div class="border-2 border-dashed rounded-lg p-4 transition-colors"
                                 :class="conceptDragOver ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600'"
                                 @dragover.prevent="conceptDragOver = true"
                                 @dragleave.prevent="conceptDragOver = false"
                                 @drop.prevent="conceptDragOver = false; handleConceptFileDrop($event)">
                                <input type="file" @change="handleConceptFileUpload($event)" accept=".pdf,.ai,.psd,.png,.jpg,.jpeg,.svg,.eps" class="hidden" id="conceptFileInput">
                                <label for="conceptFileInput" class="cursor-pointer flex flex-col items-center">
                                    <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span x-show="selectedConcept?.status === 'needs_revision' || selectedConcept?.status === 'rejected'">Click or drag and drop to upload revised design</span>
                                        <span x-show="selectedConcept?.status !== 'needs_revision' && selectedConcept?.status !== 'rejected'">Click to upload design or drag and drop</span>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">PDF, AI, PSD, PNG, JPG, SVG, EPS (max 50MB)</p>
                                </label>
                            </div>

                            <!-- Google Drive Link for PSD/Source Files -->
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-yellow-500" viewBox="0 0 24 24" fill="currentColor"><path d="M7.71 3.5L1.15 15l3.43 5.97h6.56l-3.43-5.97L7.71 3.5zm1.14 0l6.56 11.5H21.97l-6.56-11.5H8.85zm6.56 12.5L12 22h12l-3.43-5.97H15.41z"/></svg>
                                        Google Drive Link (PSD / Source Files) <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <p x-show="!selectedConcept?.driveLink" class="text-xs text-red-500 mb-2">Required before submitting for review</p>
                                <!-- Show existing link -->
                                <div x-show="selectedConcept?.driveLink" class="flex items-center gap-2 mb-2 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <a :href="selectedConcept?.driveLink" target="_blank" rel="noopener noreferrer" class="text-sm text-green-700 dark:text-green-400 hover:underline truncate flex-1" x-text="selectedConcept?.driveLink"></a>
                                    <button @click="selectedConcept.driveLink = ''; saveDriveLink(selectedConcept)" class="text-xs text-red-500 hover:text-red-700 shrink-0">Remove</button>
                                </div>
                                <!-- Input for new link -->
                                <div x-show="!selectedConcept?.driveLink" class="flex gap-2">
                                    <input type="url" x-model="driveLinkInput" placeholder="https://drive.google.com/drive/folders/..." class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                                    <button @click="saveDriveLink(selectedConcept)" :disabled="!driveLinkInput" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white text-sm rounded-lg transition-colors">
                                        Save
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Paste the Google Drive folder link containing the PSD and source files</p>
                            </div>
                        </div>

                        <!-- Status Actions -->
                        <div class="border-t dark:border-gray-700 pt-6 mt-6">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                            <div class="flex flex-wrap gap-3">
                                <template x-if="selectedConcept?.status === 'draft' || selectedConcept?.status === 'assigned'">
                                    <button @click="updateConceptStatus('in_progress')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Start Working</button>
                                </template>
                                <template x-if="selectedConcept?.status === 'in_progress' || selectedConcept?.status === 'needs_revision' || selectedConcept?.status === 'rejected'">
                                    <button @click="if(!selectedConcept?.driveLink){ showToast('Please add the Google Drive link for PSD/source files before submitting', 'error'); return; } updateConceptStatus('pending_review')" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700">Submit for Review</button>
                                </template>
                                <template x-if="selectedConcept?.status === 'pending_review'">
                                    <div class="flex gap-3">
                                        <button @click="updateConceptStatus('approved')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Approve → Content Bank</button>
                                        <button @click="updateConceptStatus('needs_revision')" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">Request Revision</button>
                                    </div>
                                </template>
                                <button @click="editConcept(selectedConcept)" class="px-4 py-2 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">Edit</button>
                                <button @click="deleteConcept(selectedConcept)" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>
                            </div>
                        </div>

                        <!-- Revision History Section -->
                        <div x-show="selectedConcept?.revisions?.length > 0" class="border-t dark:border-gray-700 pt-6 mt-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-gray-900 dark:text-white">Revision History</h3>
                                <span class="text-xs px-2 py-1 bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300 rounded-full" x-text="selectedConcept?.revisions?.length + ' revision' + (selectedConcept?.revisions?.length !== 1 ? 's' : '')"></span>
                            </div>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                <template x-for="(revision, idx) in selectedConcept?.revisions?.slice().reverse()" :key="revision._id || idx">
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">Version <span x-text="revision.version"></span></span>
                                                <span class="text-xs text-gray-500" x-text="formatDateTime(revision.createdAt)"></span>
                                            </div>
                                            <p class="text-xs text-gray-600 dark:text-gray-400" x-text="'Uploaded by ' + (revision.createdBy?.firstName || 'Unknown') + ' ' + (revision.createdBy?.lastName || '')"></p>
                                            <p x-show="revision.notes" class="text-xs text-gray-500 dark:text-gray-500 mt-1" x-text="revision.notes"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Feedback Section with @mentions -->
                        <div class="border-t dark:border-gray-700 pt-6 mt-6">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Feedback & Comments</h3>
                            <div class="space-y-4 mb-4 max-h-60 overflow-y-auto">
                                <template x-for="fb in selectedConcept?.feedback" :key="fb._id">
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="font-medium text-gray-900 dark:text-white" x-text="fb.from?.firstName + ' ' + fb.from?.lastName"></span>
                                            <span class="text-xs text-gray-500" x-text="formatDate(fb.createdAt)"></span>
                                        </div>
                                        <p class="text-gray-700 dark:text-gray-300" x-html="formatMessageWithMentions(fb.message)"></p>
                                    </div>
                                </template>
                                <div x-show="!selectedConcept?.feedback?.length" class="text-center text-gray-500 py-4">No feedback yet</div>
                            </div>
                            <div class="relative">
                                <textarea 
                                    x-model="newFeedback" 
                                    @input="handleConceptMentionInput($event)"
                                    placeholder="Add a comment... Use @ to mention someone" 
                                    rows="3"
                                    class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </textarea>
                                <!-- Mention Dropdown -->
                                <div x-show="showConceptMentionDropdown" class="absolute z-10 mt-1 w-64 bg-white dark:bg-gray-800 border dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                    <template x-for="user in filteredConceptUsers" :key="user._id">
                                        <div @click="selectConceptMention(user)" class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                                            <span class="text-sm text-gray-900 dark:text-white" x-text="user.firstName + ' ' + user.lastName"></span>
                                            <span class="text-xs text-gray-500 ml-2" x-text="user.role"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="mentionId in selectedConceptMentions" :key="mentionId">
                                            <span class="px-2 py-1 text-xs bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300 rounded-full">
                                                <span x-text="getMentionFirstName(mentionId)"></span>
                                            </span>
                                        </template>
                                    </div>
                                    <button @click="addFeedback()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Send</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Production Modal -->
        <div x-show="showProductionModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="showProductionModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6 border-b dark:border-gray-700">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white" x-text="editingProduction ? 'Edit Production' : 'Create New Production'"></h2>
                    </div>
                    <form @submit.prevent="saveProduction()" class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Project Title *</label>
                                <input type="text" x-model="productionForm.title" required class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description *</label>
                                <textarea x-model="productionForm.description" required rows="3" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Client *</label>
                                <select x-model="productionForm.clientId" required class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Client</option>
                                    <template x-for="client in clients" :key="client._id">
                                        <option :value="client._id" x-text="client.brandName || client.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Video Type *</label>
                                <select x-model="productionForm.videoType" required class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="commercial">Commercial</option>
                                    <option value="social">Social Media</option>
                                    <option value="explainer">Explainer</option>
                                    <option value="testimonial">Testimonial</option>
                                    <option value="event">Event</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Platform(s) *</label>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="p in ['youtube', 'instagram', 'facebook', 'linkedin', 'tiktok', 'website', 'tv']" :key="p">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" :value="p" x-model="productionForm.platform" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 capitalize" x-text="p"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
                                <select x-model="productionForm.priority" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Due Date *</label>
                                <input type="date" x-model="productionForm.dueDate" required class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Duration</label>
                                <input type="text" x-model="productionForm.duration" placeholder="e.g., 30 seconds, 2 minutes" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Drive Backup URL</label>
                                <input type="url" x-model="productionForm.driveBackupUrl" placeholder="https://drive.google.com/..." class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="col-span-2 border-t dark:border-gray-600 pt-4 mt-2">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Team Assignments</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Scriptwriter -->
                                    <div class="relative" x-data="{ scriptwriterOpen: false, scriptwriterSearch: '' }" @click.away="scriptwriterOpen = false">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Scriptwriter</label>
                                        <div class="relative">
                                            <input type="text" 
                                                x-model="scriptwriterSearch" 
                                                @focus="scriptwriterOpen = true" 
                                                @input="scriptwriterOpen = true"
                                                :placeholder="productionForm.scriptwriter ? (teamMembers.find(u => u._id === productionForm.scriptwriter) ? teamMembers.find(u => u._id === productionForm.scriptwriter).firstName + ' ' + teamMembers.find(u => u._id === productionForm.scriptwriter).lastName : 'Unassigned') : 'Unassigned'"
                                                class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white pr-8">
                                            <button type="button" @click="scriptwriterOpen = !scriptwriterOpen" class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                        </div>
                                        <div x-show="scriptwriterOpen" x-cloak class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                            <div @click="productionForm.scriptwriter = ''; scriptwriterSearch = ''; scriptwriterOpen = false" 
                                                class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200"
                                                :class="{'bg-indigo-50 dark:bg-indigo-900/30': !productionForm.scriptwriter}">Unassigned</div>
                                            <template x-for="u in teamMembers.filter(m => (m.firstName + ' ' + m.lastName).toLowerCase().includes(scriptwriterSearch.toLowerCase()))" :key="u._id">
                                                <div @click="productionForm.scriptwriter = u._id; scriptwriterSearch = ''; scriptwriterOpen = false" 
                                                    class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200"
                                                    :class="{'bg-indigo-50 dark:bg-indigo-900/30': productionForm.scriptwriter === u._id}"
                                                    x-text="u.firstName + ' ' + u.lastName"></div>
                                            </template>
                                            <div x-show="teamMembers.filter(m => (m.firstName + ' ' + m.lastName).toLowerCase().includes(scriptwriterSearch.toLowerCase())).length === 0 && scriptwriterSearch" class="px-4 py-2 text-gray-500 dark:text-gray-400">No team members found</div>
                                        </div>
                                    </div>
                                    <!-- Director -->
                                    <div class="relative" x-data="{ directorOpen: false, directorSearch: '' }" @click.away="directorOpen = false">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Director</label>
                                        <div class="relative">
                                            <input type="text" 
                                                x-model="directorSearch" 
                                                @focus="directorOpen = true" 
                                                @input="directorOpen = true"
                                                :placeholder="productionForm.director ? (teamMembers.find(u => u._id === productionForm.director) ? teamMembers.find(u => u._id === productionForm.director).firstName + ' ' + teamMembers.find(u => u._id === productionForm.director).lastName : 'Unassigned') : 'Unassigned'"
                                                class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white pr-8">
                                            <button type="button" @click="directorOpen = !directorOpen" class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                        </div>
                                        <div x-show="directorOpen" x-cloak class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                            <div @click="productionForm.director = ''; directorSearch = ''; directorOpen = false" 
                                                class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200"
                                                :class="{'bg-indigo-50 dark:bg-indigo-900/30': !productionForm.director}">Unassigned</div>
                                            <template x-for="u in teamMembers.filter(m => (m.firstName + ' ' + m.lastName).toLowerCase().includes(directorSearch.toLowerCase()))" :key="u._id">
                                                <div @click="productionForm.director = u._id; directorSearch = ''; directorOpen = false" 
                                                    class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200"
                                                    :class="{'bg-indigo-50 dark:bg-indigo-900/30': productionForm.director === u._id}"
                                                    x-text="u.firstName + ' ' + u.lastName"></div>
                                            </template>
                                            <div x-show="teamMembers.filter(m => (m.firstName + ' ' + m.lastName).toLowerCase().includes(directorSearch.toLowerCase())).length === 0 && directorSearch" class="px-4 py-2 text-gray-500 dark:text-gray-400">No team members found</div>
                                        </div>
                                    </div>
                                    <!-- Editor -->
                                    <div class="relative" x-data="{ editorOpen: false, editorSearch: '' }" @click.away="editorOpen = false">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Editor</label>
                                        <div class="relative">
                                            <input type="text" 
                                                x-model="editorSearch" 
                                                @focus="editorOpen = true" 
                                                @input="editorOpen = true"
                                                :placeholder="productionForm.editor ? (teamMembers.find(u => u._id === productionForm.editor) ? teamMembers.find(u => u._id === productionForm.editor).firstName + ' ' + teamMembers.find(u => u._id === productionForm.editor).lastName : 'Unassigned') : 'Unassigned'"
                                                class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white pr-8">
                                            <button type="button" @click="editorOpen = !editorOpen" class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                        </div>
                                        <div x-show="editorOpen" x-cloak class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                            <div @click="productionForm.editor = ''; editorSearch = ''; editorOpen = false" 
                                                class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200"
                                                :class="{'bg-indigo-50 dark:bg-indigo-900/30': !productionForm.editor}">Unassigned</div>
                                            <template x-for="u in teamMembers.filter(m => (m.firstName + ' ' + m.lastName).toLowerCase().includes(editorSearch.toLowerCase()))" :key="u._id">
                                                <div @click="productionForm.editor = u._id; editorSearch = ''; editorOpen = false" 
                                                    class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200"
                                                    :class="{'bg-indigo-50 dark:bg-indigo-900/30': productionForm.editor === u._id}"
                                                    x-text="u.firstName + ' ' + u.lastName"></div>
                                            </template>
                                            <div x-show="teamMembers.filter(m => (m.firstName + ' ' + m.lastName).toLowerCase().includes(editorSearch.toLowerCase())).length === 0 && editorSearch" class="px-4 py-2 text-gray-500 dark:text-gray-400">No team members found</div>
                                        </div>
                                    </div>
                                    <!-- Producer -->
                                    <div class="relative" x-data="{ producerOpen: false, producerSearch: '' }" @click.away="producerOpen = false">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Producer</label>
                                        <div class="relative">
                                            <input type="text" 
                                                x-model="producerSearch" 
                                                @focus="producerOpen = true" 
                                                @input="producerOpen = true"
                                                :placeholder="productionForm.producer ? (teamMembers.find(u => u._id === productionForm.producer) ? teamMembers.find(u => u._id === productionForm.producer).firstName + ' ' + teamMembers.find(u => u._id === productionForm.producer).lastName : 'Unassigned') : 'Unassigned'"
                                                class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white pr-8">
                                            <button type="button" @click="producerOpen = !producerOpen" class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                        </div>
                                        <div x-show="producerOpen" x-cloak class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                            <div @click="productionForm.producer = ''; producerSearch = ''; producerOpen = false" 
                                                class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200"
                                                :class="{'bg-indigo-50 dark:bg-indigo-900/30': !productionForm.producer}">Unassigned</div>
                                            <template x-for="u in teamMembers.filter(m => (m.firstName + ' ' + m.lastName).toLowerCase().includes(producerSearch.toLowerCase()))" :key="u._id">
                                                <div @click="productionForm.producer = u._id; producerSearch = ''; producerOpen = false" 
                                                    class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200"
                                                    :class="{'bg-indigo-50 dark:bg-indigo-900/30': productionForm.producer === u._id}"
                                                    x-text="u.firstName + ' ' + u.lastName"></div>
                                            </template>
                                            <div x-show="teamMembers.filter(m => (m.firstName + ' ' + m.lastName).toLowerCase().includes(producerSearch.toLowerCase())).length === 0 && producerSearch" class="px-4 py-2 text-gray-500 dark:text-gray-400">No team members found</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 pt-4 border-t dark:border-gray-700">
                            <button type="button" @click="showProductionModal = false" class="px-4 py-2 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                <span x-text="editingProduction ? 'Update Production' : 'Create Production'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- View Production Detail Modal -->
        <div x-show="showProductionDetail" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="showProductionDetail = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-5xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6 border-b dark:border-gray-700 flex justify-between items-start">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white" x-text="selectedProduction?.title"></h2>
                            <p class="text-sm text-gray-500" x-text="selectedProduction?.clientId?.brandName || selectedProduction?.clientId?.name"></p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span :class="getProductionStatusClass(selectedProduction?.status)" class="px-3 py-1 text-sm rounded-full" x-text="formatProductionStatus(selectedProduction?.status)"></span>
                            <button @click="editProduction(selectedProduction)" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button @click="deleteProduction(selectedProduction._id)" class="p-2 hover:bg-red-100 dark:hover:bg-red-900 text-red-600 rounded-lg" title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                            <button @click="showProductionDetail = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="Close">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-4 gap-6 mb-6">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Video Type</p>
                                <p class="font-medium text-gray-900 dark:text-white capitalize" x-text="selectedProduction?.videoType"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Due Date</p>
                                <p class="font-medium text-gray-900 dark:text-white" x-text="formatDate(selectedProduction?.dueDate)"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Editor</p>
                                <p class="font-medium text-gray-900 dark:text-white" x-text="selectedProduction?.editor ? selectedProduction.editor.firstName + ' ' + selectedProduction.editor.lastName : 'Unassigned'"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">SLA Status</p>
                                <div class="flex items-center gap-2">
                                    <div class="w-20 h-2 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                                        <div :class="getSLABarClass(selectedProduction)" :style="'width: ' + getSLAPercentage(selectedProduction) + '%'" class="h-full"></div>
                                    </div>
                                    <span class="text-sm" :class="getSLATextClass(selectedProduction)" x-text="getHoursInStatus(selectedProduction) + 'h'"></span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Description</p>
                            <p class="text-gray-900 dark:text-white" x-text="selectedProduction?.description"></p>
                        </div>

                        <!-- Team Section -->
                        <div class="mb-6 grid grid-cols-4 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Scriptwriter</p>
                                <p class="text-sm text-gray-900 dark:text-white" x-text="selectedProduction?.scriptwriter ? (selectedProduction.scriptwriter.firstName + ' ' + selectedProduction.scriptwriter.lastName) : 'Unassigned'"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Director</p>
                                <p class="text-sm text-gray-900 dark:text-white" x-text="selectedProduction?.director ? (selectedProduction.director.firstName + ' ' + selectedProduction.director.lastName) : 'Unassigned'"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Editor</p>
                                <p class="text-sm text-gray-900 dark:text-white" x-text="selectedProduction?.editor ? (selectedProduction.editor.firstName + ' ' + selectedProduction.editor.lastName) : 'Unassigned'"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Producer</p>
                                <p class="text-sm text-gray-900 dark:text-white" x-text="selectedProduction?.producer ? (selectedProduction.producer.firstName + ' ' + selectedProduction.producer.lastName) : 'Unassigned'"></p>
                            </div>
                        </div>

                        <!-- Script & Attachments Section -->
                        <div class="border-t dark:border-gray-700 pt-6 mt-6 mb-6">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Script & Files</h3>
                            
                            <!-- Attachments List -->
                            <div x-show="selectedProduction?.attachments?.length > 0" class="mb-4 space-y-2">
                                <template x-for="attachment in selectedProduction?.attachments" :key="attachment._id">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="attachment.filename"></p>
                                                <p class="text-xs text-gray-500" x-text="formatDate(attachment.uploadedAt)"></p>
                                            </div>
                                        </div>
                                        <button @click="downloadAttachment(attachment)" class="px-3 py-1 text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">Download</button>
                                    </div>
                                </template>
                            </div>

                            <!-- Upload Script -->
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4">
                                <input type="file" @change="handleProductionFileUpload($event)" accept=".pdf,.doc,.docx,.txt" class="hidden" id="productionFileInput">
                                <label for="productionFileInput" class="cursor-pointer flex flex-col items-center">
                                    <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Click to upload script or drag and drop</p>
                                    <p class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX, TXT (max 50MB)</p>
                                </label>
                            </div>
                        </div>

                        <!-- Status Actions -->
                        <div class="border-t dark:border-gray-700 pt-6 mt-6">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Status Actions</h3>
                            <div class="flex flex-wrap gap-3">
                                <template x-if="selectedProduction?.status === 'scripting'">
                                    <button @click="updateProductionStatus('script_review')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Submit for Review</button>
                                </template>
                                <template x-if="selectedProduction?.status === 'script_review'">
                                    <div class="flex gap-3">
                                        <button @click="updateProductionStatus('filming')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Approve Script</button>
                                        <button @click="updateProductionStatus('scripting')" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Request Changes</button>
                                    </div>
                                </template>
                                <template x-if="selectedProduction?.status === 'filming'">
                                    <button @click="updateProductionStatus('editing')" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Start Editing</button>
                                </template>
                                <template x-if="selectedProduction?.status === 'editing'">
                                    <button @click="updateProductionStatus('internal_review')" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700">Submit for Internal Review</button>
                                </template>
                                <template x-if="selectedProduction?.status === 'internal_review'">
                                    <div class="flex gap-3">
                                        <button @click="updateProductionStatus('client_review')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Send to Client</button>
                                        <button @click="updateProductionStatus('revisions')" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">Request Revisions</button>
                                    </div>
                                </template>
                                <template x-if="selectedProduction?.status === 'revisions'">
                                    <button @click="updateProductionStatus('internal_review')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Resubmit for Review</button>
                                </template>
                                <template x-if="selectedProduction?.status === 'client_review'">
                                    <button @click="updateProductionStatus('approved')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Client Approved</button>
                                </template>
                                <template x-if="selectedProduction?.status === 'approved'">
                                    <button @click="updateProductionStatus('final_delivery')" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">Prepare Final Delivery</button>
                                </template>
                                <template x-if="selectedProduction?.status === 'final_delivery'">
                                    <button @click="updateProductionStatus('delivered')" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Mark as Delivered</button>
                                </template>
                            </div>
                        </div>

                        <!-- Comments Section with @mentions -->
                        <div class="border-t dark:border-gray-700 pt-6 mt-6">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Comments</h3>
                            <div class="space-y-4 mb-4 max-h-60 overflow-y-auto">
                                <template x-for="comment in selectedProduction?.comments" :key="comment._id">
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <span class="font-medium text-gray-900 dark:text-white" x-text="comment.from?.firstName + ' ' + comment.from?.lastName"></span>
                                                <span x-show="comment.isClientComment" class="ml-2 px-2 py-0.5 text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 rounded">Client</span>
                                            </div>
                                            <span class="text-xs text-gray-500" x-text="formatDate(comment.createdAt)"></span>
                                        </div>
                                        <p class="text-gray-700 dark:text-gray-300" x-text="comment.message"></p>
                                        <p x-show="comment.timestamp" class="text-xs text-indigo-600 dark:text-indigo-400 mt-1" x-text="'@ ' + comment.timestamp"></p>
                                    </div>
                                </template>
                                <div x-show="!selectedProduction?.comments?.length" class="text-center text-gray-500 py-4">No comments yet</div>
                            </div>
                            <div class="relative">
                                <textarea 
                                    x-model="newProductionComment" 
                                    @input="handleMentionInput($event)"
                                    placeholder="Add a comment... Use @ to mention someone" 
                                    rows="3"
                                    class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </textarea>
                                <!-- Mention Dropdown -->
                                <div x-show="showMentionDropdown" class="absolute z-10 mt-1 w-64 bg-white dark:bg-gray-800 border dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                    <template x-for="user in filteredUsers" :key="user._id">
                                        <div @click="selectMention(user)" class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                                            <span class="text-sm text-gray-900 dark:text-white" x-text="user.firstName + ' ' + user.lastName"></span>
                                            <span class="text-xs text-gray-500 ml-2" x-text="user.role"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="mentionId in selectedMentions" :key="mentionId">
                                            <span class="px-2 py-1 text-xs bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300 rounded-full">
                                                <span x-text="getMentionFirstName(mentionId)"></span>
                                            </span>
                                        </template>
                                    </div>
                                    <button @click="addProductionComment()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Send</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manual Upload Modal -->
        <div x-show="showManualUploadModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showManualUploadModal = false">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showManualUploadModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showManualUploadModal = false"></div>
                <div x-show="showManualUploadModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-2xl overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-lg shadow-xl sm:my-8 sm:align-middle">
                    <div class="px-6 py-4 border-b dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upload Pre-Approved Content</h3>
                            <button @click="showManualUploadModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Client *</label>
                            <select x-model="manualUploadForm.clientId" required class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select a client</option>
                                <template x-for="client in clients" :key="client._id">
                                    <option :value="client._id" x-text="client.brandName || client.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title *</label>
                            <input x-model="manualUploadForm.title" type="text" required placeholder="Enter content title" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <textarea x-model="manualUploadForm.description" rows="3" placeholder="Enter content description" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Platform</label>
                            <select x-model="manualUploadForm.platform" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="instagram">Instagram</option>
                                <option value="facebook">Facebook</option>
                                <option value="linkedin">LinkedIn</option>
                                <option value="youtube">YouTube</option>
                                <option value="x">X (Twitter)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload Files (Images/Videos)</label>
                            <input id="manualUploadFiles" type="file" multiple accept="image/*,video/*" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            <p class="text-xs text-gray-500 mt-1">Upload pre-approved content that was created before this system</p>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex justify-end gap-3">
                        <button @click="showManualUploadModal = false" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500">Cancel</button>
                        <button @click="uploadManualContent()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Upload to Content Bank</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Bank Detail Modal -->
        <div x-show="showContentBankDetail" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showContentBankDetail = false">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showContentBankDetail" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showContentBankDetail = false"></div>
                <div x-show="showContentBankDetail" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative inline-block w-full max-w-4xl my-8 text-left align-middle transition-all transform bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white" x-text="selectedContentBankItem?.title"></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="selectedContentBankItem?.clientId?.brandName || selectedContentBankItem?.clientId?.name"></p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span :class="getClientApprovalClass(selectedContentBankItem?.clientApprovalStatus)" class="px-3 py-1 text-sm rounded-full" x-text="formatClientApprovalStatus(selectedContentBankItem?.clientApprovalStatus)"></span>
                                <button @click="showContentBankDetail = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-6 max-h-[70vh] overflow-y-auto">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Image/Media Preview -->
                            <div>
                                <template x-if="getDesignPreview(selectedContentBankItem)">
                                    <div class="relative">
                                        <img :src="getDesignPreview(selectedContentBankItem)" :alt="selectedContentBankItem?.title" @click="lightboxImage = getDesignPreview(selectedContentBankItem)" class="w-full rounded-lg cursor-pointer hover:opacity-90 transition-opacity" />
                                        <div class="absolute bottom-3 right-3 flex items-center gap-2">
                                            <button @click="downloadAttachment({ url: getDesignPreview(selectedContentBankItem), originalName: (selectedContentBankItem?.title || 'download') + '.png' })" class="px-3 py-1.5 bg-black/50 hover:bg-black/70 text-white text-xs rounded-lg flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                Download
                                            </button>
                                            <button @click="lightboxImage = getDesignPreview(selectedContentBankItem)" class="px-3 py-1.5 bg-black/50 hover:bg-black/70 text-white text-xs rounded-lg flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                                View Full Size
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!getDesignPreview(selectedContentBankItem)">
                                    <div class="flex items-center justify-center h-64 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </template>
                                
                                <!-- Upload/Replace Media (Admin & Brand Rep only) -->
                                <div x-show="user.role === 'admin' || user.role === 'brand_rep'" class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload/Replace Final Media</label>
                                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 transition-colors"
                                         :class="{'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20': contentBankDragOver}"
                                         @dragover.prevent="contentBankDragOver = true"
                                         @dragleave.prevent="contentBankDragOver = false"
                                         @drop.prevent="contentBankDragOver = false; handleContentBankMediaDrop($event)">
                                        <input type="file" @change="handleContentBankMediaUpload($event)" accept="image/*,video/*" class="hidden" :id="'contentBankMedia-' + selectedContentBankItem?._id">
                                        <label :for="'contentBankMedia-' + selectedContentBankItem?._id" class="cursor-pointer flex flex-col items-center">
                                            <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Drag and drop or click to upload</p>
                                            <p class="text-xs text-gray-500 mt-1">Images or videos</p>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Details -->
                            <div class="space-y-4">
                                <!-- Meta Info -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Content Type</p>
                                        <p class="font-medium text-gray-900 dark:text-white capitalize" x-text="selectedContentBankItem?.contentType?.replace('_', ' ')"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Platform</p>
                                        <p class="font-medium text-gray-900 dark:text-white capitalize" x-text="Array.isArray(selectedContentBankItem?.platform) ? selectedContentBankItem?.platform.join(', ') : selectedContentBankItem?.platform"></p>
                                    </div>
                                    <div x-show="selectedContentBankItem?.scheduledPostDate">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Scheduled Date</p>
                                        <p class="font-medium text-blue-600 dark:text-blue-400" x-text="formatDate(selectedContentBankItem?.scheduledPostDate)"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Approved By</p>
                                        <p class="font-medium text-gray-900 dark:text-white" x-text="selectedContentBankItem?.approvedBy ? selectedContentBankItem.approvedBy.firstName + ' ' + selectedContentBankItem.approvedBy.lastName : 'N/A'"></p>
                                    </div>
                                </div>
                                
                                <!-- Google Drive Link -->
                                <div x-show="selectedContentBankItem?.driveLink">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Google Drive (Source Files)</p>
                                    <a :href="selectedContentBankItem?.driveLink" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors">
                                        <svg class="w-5 h-5 text-yellow-600 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M7.71 3.5L1.15 15l3.43 5.97h6.56l-3.43-5.97L7.71 3.5zm1.14 0l6.56 11.5H21.97l-6.56-11.5H8.85zm6.56 12.5L12 22h12l-3.43-5.97H15.41z"/></svg>
                                        <span class="text-sm text-yellow-800 dark:text-yellow-300 hover:underline truncate">Open Google Drive Folder</span>
                                        <svg class="w-4 h-4 text-yellow-600 shrink-0 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </div>

                                <!-- Caption (what clients see) -->
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Caption</p>
                                    <div x-show="!editingContentBankItem" class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <p class="text-gray-900 dark:text-white whitespace-pre-wrap" x-text="selectedContentBankItem?.caption || selectedContentBankItem?.description || 'No caption'"></p>
                                    </div>
                                    <div x-show="editingContentBankItem">
                                        <textarea x-model="contentBankEditForm.caption" rows="4" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Post caption for client review..."></textarea>
                                    </div>
                                </div>
                                
                                <!-- Edit Form Fields (shown when editing) -->
                                <div x-show="editingContentBankItem" class="space-y-4">
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Title</label>
                                        <input type="text" x-model="contentBankEditForm.title" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Scheduled Post Date</label>
                                        <input type="date" x-model="contentBankEditForm.scheduledPostDate" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                </div>
                                
                                <!-- Feedback & Comments Section -->
                                <div class="border-t dark:border-gray-700 pt-4 mt-4">
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Feedback & Comments</h4>
                                    <div class="space-y-3 mb-4 max-h-60 overflow-y-auto">
                                        <!-- Combined feed: client feedback + team comments sorted by date -->
                                        <template x-for="fb in [...(selectedContentBankItem?.clientFeedback || []).map(f => ({...f, _source: 'client'})), ...(selectedContentBankItem?.feedback || []).map(f => ({...f, _source: 'team'}))].sort((a, b) => new Date(a.createdAt) - new Date(b.createdAt))" :key="fb._id">
                                            <div class="p-3 rounded-lg" :class="fb._source === 'client' ? 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800' : 'bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800'">
                                                <div class="flex justify-between items-start mb-1">
                                                    <span class="font-medium text-sm text-gray-900 dark:text-white" x-text="fb.from?.firstName ? fb.from.firstName + ' ' + (fb.from.lastName || '') : (fb._source === 'client' ? 'Client' : 'Team')"></span>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-[10px] px-1.5 py-0.5 rounded-full" :class="fb._source === 'client' ? 'bg-yellow-200 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200' : 'bg-blue-200 text-blue-800 dark:bg-blue-800 dark:text-blue-200'" x-text="fb._source === 'client' ? 'Client' : 'Team'"></span>
                                                        <span class="text-xs text-gray-500" x-text="formatDateTime(fb.createdAt)"></span>
                                                    </div>
                                                </div>
                                                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap" x-html="formatMessageWithMentions(fb.message)"></p>
                                            </div>
                                        </template>
                                        <div x-show="!selectedContentBankItem?.clientFeedback?.length && !selectedContentBankItem?.feedback?.length" class="text-center text-gray-500 py-4 text-sm">No feedback yet</div>
                                    </div>
                                    <!-- Add Comment (Admin & Brand Rep) -->
                                    <div x-show="user.role === 'admin' || user.role === 'brand_rep'" class="relative">
                                        <textarea 
                                            x-model="newContentBankFeedback" 
                                            @input="handleCBMentionInput($event)"
                                            placeholder="Add a comment... Use @ to mention someone" 
                                            rows="3"
                                            class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                        </textarea>
                                        <!-- Mention Dropdown -->
                                        <div x-show="showCBMentionDropdown" class="absolute z-10 mt-1 w-64 bg-white dark:bg-gray-800 border dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                            <template x-for="member in filteredCBUsers" :key="member._id">
                                                <div @click="selectCBMention(member)" class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                                                    <span class="text-sm text-gray-900 dark:text-white" x-text="member.firstName + ' ' + member.lastName"></span>
                                                    <span class="text-xs text-gray-500 ml-2" x-text="member.role"></span>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="flex justify-between items-center mt-2">
                                            <div class="flex flex-wrap gap-1">
                                                <template x-for="mentionId in selectedCBMentions" :key="mentionId">
                                                    <span class="px-2 py-1 text-xs bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300 rounded-full">
                                                        <span x-text="getMentionFirstName(mentionId)"></span>
                                                    </span>
                                                </template>
                                            </div>
                                            <button @click="addContentBankFeedback()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">Send</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer Actions -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t dark:border-gray-600 flex flex-wrap justify-between gap-3">
                        <div class="flex gap-2">
                            <!-- Edit Button (Admin & Brand Rep) -->
                            <button x-show="(user.role === 'admin' || user.role === 'brand_rep') && !editingContentBankItem" @click="startEditContentBankItem()" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Edit
                            </button>
                            <!-- Delete Button (Admin only) -->
                            <button x-show="user.role === 'admin' && !editingContentBankItem" @click="deleteContentBankItem(selectedContentBankItem)" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Delete
                            </button>
                            <!-- Save/Cancel Edit Buttons -->
                            <button x-show="editingContentBankItem" @click="saveContentBankEdit()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Save Changes
                            </button>
                            <button x-show="editingContentBankItem" @click="editingContentBankItem = false" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500">
                                Cancel
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <!-- Approve Button (Admin & Client only) -->
                            <button x-show="(user.role === 'admin' || viewMode === 'client') && selectedContentBankItem?.clientApprovalStatus !== 'approved' && !editingContentBankItem" @click="approveContentBankItem()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Approve
                            </button>
                            <!-- Request Changes Button (Admin & Client only) -->
                            <button x-show="(user.role === 'admin' || viewMode === 'client') && selectedContentBankItem?.clientApprovalStatus !== 'needs_changes' && !editingContentBankItem" @click="requestContentBankChanges()" class="px-4 py-2 text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/40 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Request Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fullscreen Image Lightbox -->
        <div x-show="lightboxImage" x-cloak @click="lightboxImage = null" @keydown.escape.window="lightboxImage = null" class="fixed inset-0 z-[100] bg-black/90 flex items-center justify-center p-4 cursor-pointer" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <button @click="lightboxImage = null" class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors z-10">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <img :src="lightboxImage" @click.stop class="max-w-full max-h-full object-contain rounded-lg shadow-2xl cursor-default" alt="Full size reference image" />
        </div>
    </div>

    <script>
        const API_URL = typeof ESIROM_API_URL !== 'undefined' ? ESIROM_API_URL : 'https://esirom-hub-backend-production.up.railway.app/api';
        const LOGIN_URL = '<?php echo esc_url(get_permalink(get_page_by_path('login'))); ?>';
        const DASHBOARD_URL = '<?php echo esc_url(get_permalink(get_page_by_path('dashboard'))); ?>';
        const WORKFLOW_PAGE_PATH = <?php echo wp_json_encode($esirom_workflow_page_path, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;

        function workflowApp() {
            return {
                loading: true,
                user: {},
                viewMode: localStorage.getItem('viewMode') || 'admin',
                theme: localStorage.getItem('theme') || 'light',
                isSidebarOpen: true,
                currentDate: '',
                activeTab: 'dashboard',
                toasts: [],
                concepts: [],
                tasks: [],
                clients: [],
                teamMembers: [],
                mentionableUsers: [],
                notifications: [],
                unreadNotifications: 0,
                dashboardStats: { concepts: {}, tasks: {}, overdue: {} },
                myWork: { concepts: [], tasks: [] },
                assignedConcepts: [],
                workloadByAssignee: [],
                teamMonthlyEfficiency: [],
                approvedNotPosted: [],
                contentQuotaTracking: [],
                isQuotaOpen: false,
                quotaYear: new Date().getFullYear(),
                efficiencyYear: new Date().getFullYear(),
                efficiencyMonth: new Date().getMonth() + 1,
                conceptSearch: '',
                conceptStatusFilter: '',
                conceptClientFilter: '',
                efficiencyAssigneeFilter: '',
                taskStatusFilter: '',
                taskPriorityFilter: '',
                showConceptModal: false,
                showConceptDetail: false,
                showTaskModal: false,
                editingConcept: null,
                selectedConcept: null,
                driveLinkInput: '',
                newFeedback: '',
                selectedConceptMentions: [],
                showConceptMentionDropdown: false,
                filteredConceptUsers: [],
                referenceImageFiles: [null, null],
                referenceImagePreviews: ['', ''],
                dragOverIndex: null,
                lightboxImage: null,
                conceptForm: {
                    title: '', description: '', caption: '', clientId: '', contentType: '', platform: [],
                    priority: 'medium', dueDate: '', assignedTo: '', referenceLink: '',
                    briefDetails: { keyMessage: '', callToAction: '', additionalNotes: '', descriptionColor: '#111827', captionColor: '#6B7280' }
                },
                taskForm: {
                    title: '', description: '', taskType: '', assignedTo: '', dueDate: '', priority: 'medium'
                },
                productions: [],
                productionStatusFilter: '',
                productionClientFilter: '',
                showProductionModal: false,
                showProductionDetail: false,
                editingProduction: null,
                selectedProduction: null,
                productionForm: {
                    title: '', description: '', clientId: '', videoType: 'commercial',
                    platform: [], priority: 'medium', dueDate: '',
                    scriptwriter: '', director: '', editor: '', producer: '',
                    driveBackupUrl: '', duration: ''
                },
                newProductionComment: '',
                selectedMentions: [],
                showMentionDropdown: false,
                filteredUsers: [],
                contentBankItems: [],
                contentBankStatusFilter: localStorage.getItem('viewMode') === 'client' ? 'pending' : '',
                contentBankClientFilter: '',
                selectedContentBankItem: null,
                showContentBankDetail: false,
                editingContentBankItem: false,
                contentBankEditForm: {
                    title: '',
                    caption: '',
                    scheduledPostDate: ''
                },
                newContentBankFeedback: '',
                showCBMentionDropdown: false,
                filteredCBUsers: [],
                selectedCBMentions: [],
                // Feed tab data
                feedItems: [],
                feedClientFilter: '',
                feedTypeFilter: '',
                feedLoading: false,
                feedPage: 1,
                feedTotalPages: 1,
                unreadFeedCount: 0,
                lastFeedCheck: null,
                feedPoller: null,

                conceptDragOver: false,
                contentBankDragOver: false,
                showManualUploadModal: false,
                manualUploadForm: {
                    clientId: '',
                    title: '',
                    description: '',
                    platform: 'instagram',
                    files: []
                },
                selectedViewClient: null,

                // Planner data
                plans: [],
                activePlan: null,
                activeSlideIndex: 0,
                selectedElement: null,
                planStatusFilter: 'active',
                planSearchQuery: '',
                showPlanComments: false,
                newPlanComment: '',
                showShareModal: false,
                sharePlanId: null,
                sharePlanCollaborators: [],
                shareForm: { userId: '', permission: 'view' },
                planDirty: false,
                dragState: null,
                resizeState: null,

                async init() {
                    await this.checkAuth();

                    // Ensure true client accounts always use client viewMode
                    if (this.user?.role === 'client') {
                        this.viewMode = 'client';
                        localStorage.setItem('viewMode', 'client');
                        window.dispatchEvent(new CustomEvent('viewModeChanged', { detail: { viewMode: 'client' } }));
                    }

                    this.applyTheme();
                    this.setCurrentDate();
                    // Restore selectedViewClient from localStorage
                    const savedViewClient = localStorage.getItem('selectedViewClient');
                    if (savedViewClient) this.selectedViewClient = savedViewClient;
                    this.syncActiveTabFromLocation();
                    // Client view should only see Content Bank tab and default to pending status filter
                    if (this.viewMode === 'client') {
                        if (this.activeTab !== 'feed') {
                            this.activeTab = 'contentBank';
                        }
                        this.contentBankStatusFilter = 'pending';
                    }
                    // Handle browser back/forward (read ?tab= and legacy /workflow/.../ paths)
                    window.addEventListener('popstate', () => {
                        this.syncActiveTabFromLocation();
                        if (this.viewMode === 'client') {
                            if (this.activeTab !== 'feed') this.activeTab = 'contentBank';
                        }
                        this.loadTabData();
                    });
                    await this.loadInitialData();
                    // Deep links from notifications: /workflow?tab=concepts&concept=ID (nested /workflow/concepts/ID 404s on WordPress)
                    try {
                        const sp = new URLSearchParams(window.location.search);
                        const deepConcept = sp.get('concept');
                        if (deepConcept) {
                            this.activeTab = 'concepts';
                            await this.openConceptFromFeed(deepConcept);
                        }
                        const deepContent = sp.get('content');
                        if (deepContent) {
                            this.activeTab = 'contentBank';
                            await this.loadContentBank();
                            await this.viewContentBankItem({ _id: deepContent });
                        }
                    } catch (e) { console.error('Hub deep link error', e); }
                },

                setWorkflowQuery(tab, extra) {
                    const base = (typeof WORKFLOW_PAGE_PATH !== 'undefined' && WORKFLOW_PAGE_PATH) ? String(WORKFLOW_PAGE_PATH).replace(/\/$/, '') : '/workflow';
                    const u = new URL(base, window.location.origin);
                    u.search = '';
                    u.searchParams.set('tab', tab);
                    if (extra && typeof extra === 'object') {
                        Object.keys(extra).forEach((k) => {
                            const v = extra[k];
                            if (v != null && v !== '') u.searchParams.set(k, String(v));
                        });
                    }
                    history.pushState({ tab: tab }, '', u.pathname + u.search);
                },

                syncActiveTabFromLocation() {
                    const sp = new URLSearchParams(window.location.search);
                    const t = sp.get('tab');
                    const fromQuery = { dashboard: 'dashboard', concepts: 'concepts', productions: 'productions', contentBank: 'contentBank', tasks: 'tasks', planner: 'planner', feed: 'feed' };
                    if (t && fromQuery[t]) {
                        this.activeTab = fromQuery[t];
                        return;
                    }
                    const path = window.location.pathname;
                    const base = (typeof WORKFLOW_PAGE_PATH !== 'undefined' && WORKFLOW_PAGE_PATH) ? String(WORKFLOW_PAGE_PATH).replace(/\/$/, '') : '/workflow';
                    const baseLower = base.toLowerCase();
                    const pathLower = path.toLowerCase();
                    if (pathLower.startsWith(baseLower + '/concepts')) {
                        this.activeTab = 'concepts';
                    } else if (pathLower.startsWith(baseLower + '/production')) {
                        this.activeTab = 'productions';
                    } else if (pathLower.startsWith(baseLower + '/contentbank')) {
                        this.activeTab = 'contentBank';
                    } else if (pathLower.startsWith(baseLower + '/tasks')) {
                        this.activeTab = 'tasks';
                    } else if (pathLower.startsWith(baseLower + '/planner')) {
                        this.activeTab = 'planner';
                    } else if (pathLower.startsWith(baseLower + '/feed')) {
                        this.activeTab = 'feed';
                    } else {
                        this.activeTab = 'dashboard';
                    }
                },

                navigateTab(tab) {
                    this.activeTab = tab;
                    this.setWorkflowQuery(tab, {});
                    this.loadTabData();
                },

                loadTabData() {
                    if (this.activeTab === 'contentBank') this.loadContentBank();
                    else if (this.activeTab === 'productions') this.loadProductions();
                    else if (this.activeTab === 'planner') this.loadPlans();
                    else if (this.activeTab === 'feed') this.loadFeed();
                },

                showToast(message, type = 'info', duration = 3000) {
                    const id = Date.now();
                    this.toasts.push({ id, message, type });
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, duration);
                },

                async checkAuth() {
                    const token = localStorage.getItem('token');
                    if (!token) { window.location.href = LOGIN_URL; return; }
                    try {
                        const response = await fetch(`${API_URL}/auth/me`, { headers: { 'Authorization': `Bearer ${token}` } });
                        if (!response.ok) throw new Error('Auth failed');
                        const data = await response.json();
                        this.user = data.user;
                        localStorage.setItem('user', JSON.stringify(data.user));
                        if (this.user?.role === 'client') {
                            this.viewMode = 'client';
                            localStorage.setItem('viewMode', 'client');
                            window.dispatchEvent(new CustomEvent('viewModeChanged', { detail: { viewMode: 'client' } }));
                        }
                        // Allow clients to access Content Bank only
                        if (!['admin', 'brand_rep', 'client'].includes(this.user.role)) { window.location.href = DASHBOARD_URL; }
                    } catch (error) {
                        localStorage.removeItem('token');
                        window.location.href = LOGIN_URL;
                    }
                },

                async loadInitialData() {
                    const token = localStorage.getItem('token');
                    try {
                        const [dashboardRes, clientsRes, usersRes, notifRes, mentionsRes] = await Promise.all([
                            fetch(`${API_URL}/workflow/dashboard?quotaYear=${this.quotaYear}&efficiencyYear=${this.efficiencyYear}&efficiencyMonth=${this.efficiencyMonth}`, { headers: { 'Authorization': `Bearer ${token}` } }),
                            fetch(`${API_URL}/clients`, { headers: { 'Authorization': `Bearer ${token}` } }),
                            fetch(`${API_URL}/workflow/team-members`, { headers: { 'Authorization': `Bearer ${token}` } }),
                            fetch(`${API_URL}/workflow/notifications`, { headers: { 'Authorization': `Bearer ${token}` } }),
                            fetch(`${API_URL}/workflow/mentionable-users`, { headers: { 'Authorization': `Bearer ${token}` } })
                        ]);
                        if (dashboardRes.ok) {
                            const d = await dashboardRes.json();
                            this.dashboardStats = d.stats || { concepts: {}, tasks: {}, overdue: {} };
                            this.myWork = d.myWork || { concepts: [], tasks: [] };
                            this.assignedConcepts = d.assignedConcepts || [];
                            this.workloadByAssignee = d.workloadByAssignee || [];
                            this.teamMonthlyEfficiency = d.teamMonthlyEfficiency || [];
                            this.approvedNotPosted = d.approvedNotPosted || [];
                            this.contentQuotaTracking = d.contentQuotaTracking || [];
                        }
                        if (clientsRes.ok) { const d = await clientsRes.json(); this.clients = d.data || []; }
                        if (usersRes.ok) { const d = await usersRes.json(); this.teamMembers = d.data || []; } else { await usersRes.text(); }
                        if (mentionsRes && mentionsRes.ok) {
                            const d = await mentionsRes.json();
                            this.mentionableUsers = d.data || [];
                        }
                        if (notifRes.ok) { const d = await notifRes.json(); this.notifications = d.notifications || []; this.unreadNotifications = d.unreadCount || 0; }
                        await this.loadConcepts();
                        await this.loadTasks();
                        await this.loadProductions();
                        if (this.activeTab === 'planner') await this.loadPlans();
                        if (this.activeTab === 'feed') await this.loadFeed();
                        // Load last feed check from localStorage and check for unread items
                        this.lastFeedCheck = localStorage.getItem('lastFeedCheck');
                        await this.checkUnreadFeed();
                        // Poll for new feed items every 30 seconds (single timer)
                        if (this.feedPoller) clearInterval(this.feedPoller);
                        this.feedPoller = setInterval(() => { if (this.activeTab !== 'feed') this.checkUnreadFeed(); }, 30000);
                    } catch (error) { console.error('Load data error:', error); }
                    finally { this.loading = false; }
                },

                get maxWorkloadCount() {
                    const counts = (this.workloadByAssignee || []).map(x => x.pendingCount || 0);
                    return counts.length ? Math.max(...counts) : 0;
                },

                async loadConcepts() {
                    const token = localStorage.getItem('token');
                    const params = new URLSearchParams();
                    if (this.conceptSearch) params.append('search', this.conceptSearch);
                    if (this.conceptStatusFilter) params.append('status', this.conceptStatusFilter);
                    if (this.conceptClientFilter) params.append('clientId', this.conceptClientFilter);
                    if (this.efficiencyAssigneeFilter) {
                        params.append('assignedTo', this.efficiencyAssigneeFilter);
                        params.append('assignedYear', this.efficiencyYear);
                        params.append('assignedMonth', this.efficiencyMonth);
                    }
                    // Add viewAs for role simulation
                    if (this.viewMode && this.viewMode !== 'admin') {
                        params.append('viewAs', this.viewMode);
                        if (this.viewMode === 'client' && this.selectedViewClient) {
                            params.append('viewAsClientId', this.selectedViewClient);
                        }
                    }
                    try {
                        const response = await fetch(`${API_URL}/workflow/concepts?${params}`, { headers: { 'Authorization': `Bearer ${token}` } });
                        if (response.ok) { const data = await response.json(); this.concepts = data.concepts || []; }
                    } catch (error) { console.error('Load concepts error:', error); }
                },

                async loadTasks() {
                    const token = localStorage.getItem('token');
                    const params = new URLSearchParams();
                    if (this.taskStatusFilter) params.append('status', this.taskStatusFilter);
                    if (this.taskPriorityFilter) params.append('priority', this.taskPriorityFilter);
                    // Fetch concepts assigned to the current user
                    params.append('assignedTo', this.user?._id || this.user?.id);
                    params.append('limit', '100');
                    try {
                        const response = await fetch(`${API_URL}/workflow/concepts?${params}`, { headers: { 'Authorization': `Bearer ${token}` } });
                        if (response.ok) { const data = await response.json(); this.tasks = data.concepts || []; }
                    } catch (error) { console.error('Load tasks error:', error); }
                },

                openConceptModal() {
                    this.editingConcept = null;
                    this.conceptForm = {
                        title: '',
                        description: '',
                        caption: '',
                        clientId: '',
                        contentType: '',
                        platform: [],
                        priority: 'medium',
                        dueDate: '',
                        assignedTo: '',
                        referenceLink: '',
                        briefDetails: { keyMessage: '', callToAction: '', additionalNotes: '', descriptionColor: '#111827', captionColor: '#6B7280' }
                    };
                    this.clearAllReferenceImages();
                    this.showConceptModal = true;
                },

                editConcept(concept) {
                    this.editingConcept = concept;
                    this.conceptForm = {
                        title: concept.title, description: concept.description, caption: concept.caption || '', clientId: concept.clientId?._id || concept.clientId,
                        contentType: concept.contentType, platform: concept.platform || [], priority: concept.priority,
                        dueDate: concept.dueDate ? concept.dueDate.split('T')[0] : '', assignedTo: concept.assignedTo?._id || concept.assignedTo || '',
                        referenceLink: concept.referenceLink || '',
                        briefDetails: {
                            keyMessage: concept.briefDetails?.keyMessage || '',
                            callToAction: concept.briefDetails?.callToAction || '',
                            additionalNotes: concept.briefDetails?.additionalNotes || '',
                            descriptionColor: concept.briefDetails?.descriptionColor || '#111827',
                            captionColor: concept.briefDetails?.captionColor || '#6B7280'
                        }
                    };
                    this.clearAllReferenceImages();
                    const existingImages = this.getConceptImages(concept);
                    existingImages.forEach((url, idx) => {
                        if (url && idx < 2) this.referenceImagePreviews[idx] = url;
                    });
                    this.showConceptDetail = false;
                    this.showConceptModal = true;
                },

                handleReferenceImage(event, index) {
                    const file = event?.target?.files?.[0];
                    if (!file || index < 0 || index > 1) return;
                    this.referenceImageFiles[index] = file;
                    if (this.referenceImagePreviews[index] && this.referenceImagePreviews[index].startsWith('blob:')) {
                        URL.revokeObjectURL(this.referenceImagePreviews[index]);
                    }
                    this.referenceImagePreviews[index] = URL.createObjectURL(file);
                },

                handleReferenceDrop(event, index) {
                    this.dragOverIndex = null;
                    const file = event.dataTransfer?.files?.[0];
                    if (!file || index < 0 || index > 1) return;
                    // Validate it's an image
                    if (!file.type.startsWith('image/')) {
                        this.showToast('Please drop an image file (PNG, JPG, etc.)', 'error');
                        return;
                    }
                    // Validate size (10MB max)
                    if (file.size > 10 * 1024 * 1024) {
                        this.showToast('Image must be under 10MB', 'error');
                        return;
                    }
                    this.referenceImageFiles[index] = file;
                    if (this.referenceImagePreviews[index] && this.referenceImagePreviews[index].startsWith('blob:')) {
                        URL.revokeObjectURL(this.referenceImagePreviews[index]);
                    }
                    this.referenceImagePreviews[index] = URL.createObjectURL(file);
                },

                clearReferenceImage(index) {
                    if (index < 0 || index > 1) return;
                    this.referenceImageFiles[index] = null;
                    if (this.referenceImagePreviews[index] && this.referenceImagePreviews[index].startsWith('blob:')) {
                        URL.revokeObjectURL(this.referenceImagePreviews[index]);
                    }
                    this.referenceImagePreviews[index] = '';
                },

                clearAllReferenceImages() {
                    for (let i = 0; i < 2; i++) {
                        this.clearReferenceImage(i);
                    }
                },

                getConceptImages(concept) {
                    const finals = concept?.finalAssets;
                    if (Array.isArray(finals) && finals.length > 0) {
                        const finalImages = finals.filter(x => (x?.mimetype || '').startsWith('image/')).map(x => x?.url || '').filter(Boolean);
                        if (finalImages.length > 0) {
                            return finalImages.slice(0, 2);
                        }
                    }
                    
                    const a = concept?.attachments;
                    if (!Array.isArray(a) || a.length === 0) return [];
                    let refs = a.filter(x => x?.kind === 'reference' && (x?.mimetype || '').startsWith('image/')).map(x => x?.url || '').filter(Boolean);
                    if (!refs.length) {
                        refs = a.filter(x => (x?.mimetype || '').startsWith('image/')).map(x => x?.url || '').filter(Boolean);
                    }
                    return refs.slice(0, 2);
                },

                getReferenceAttachments(concept) {
                    const a = concept?.attachments;
                    if (!Array.isArray(a) || a.length === 0) return [];
                    let refs = a.filter(x => x?.kind === 'reference' && (x?.mimetype || '').startsWith('image/'));
                    if (!refs.length) {
                        refs = a.filter(x => (x?.mimetype || '').startsWith('image/'));
                    }
                    return refs;
                },

                getLatestDesignImage(concept) {
                    const a = concept?.attachments;
                    if (!Array.isArray(a) || a.length === 0) return null;
                    const designs = a.filter(x => {
                        const isDesignKind = x?.kind === 'design';
                        const isImage = (x?.mimetype || '').startsWith('image/');
                        const notReference = x?.kind !== 'reference';
                        return isImage && (isDesignKind || notReference);
                    });
                    if (designs.length === 0) return null;
                    const latest = designs.sort((a, b) => new Date(b.uploadedAt) - new Date(a.uploadedAt))[0];
                    return latest?.url || null;
                },

                formatDateTime(date) {
                    if (!date) return '';
                    const d = new Date(date);
                    const dateStr = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    const timeStr = d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                    return `${dateStr} at ${timeStr}`;
                },

                formatMessageWithMentions(message) {
                    if (!message) return '';
                    // Replace @mentions with styled badges
                    return message.replace(/@(\w+(?:\s+\w+)?)/g, '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-700">@$1</span>');
                },

                getMentionCandidates() {
                    return (this.mentionableUsers && this.mentionableUsers.length > 0)
                        ? this.mentionableUsers
                        : this.teamMembers;
                },

                getMentionFirstName(userId) {
                    const users = this.getMentionCandidates();
                    const user = users.find((u) => String(u._id) === String(userId));
                    return user?.firstName || 'User';
                },

                updateEfficiencyMonth(monthValue) {
                    if (!monthValue || !monthValue.includes('-')) return;
                    const [year, month] = monthValue.split('-');
                    const parsedYear = parseInt(year, 10);
                    const parsedMonth = parseInt(month, 10);
                    if (!Number.isNaN(parsedYear) && !Number.isNaN(parsedMonth)) {
                        this.efficiencyYear = parsedYear;
                        this.efficiencyMonth = parsedMonth;
                        this.loadInitialData();
                    }
                },

                getBrandColor(brandId) {
                    const colors = [
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B',
                        '#EF4444',
                        '#8B5CF6',
                        '#EC4899',
                        '#14B8A6',
                        '#F97316'
                    ];
                    let hash = 0;
                    const str = brandId || 'unknown';
                    for (let i = 0; i < str.length; i++) {
                        hash = str.charCodeAt(i) + ((hash << 5) - hash);
                    }
                    return colors[Math.abs(hash) % colors.length];
                },

                getConceptsByBrand() {
                    const priorityOrder = { urgent: 0, high: 1, medium: 2, low: 3 };
                    const brandMap = {};
                    
                    for (const c of this.assignedConcepts) {
                        const brandId = c.clientId?._id || 'unknown';
                        const brandName = c.clientId?.brandName || c.clientId?.name || 'Unknown Brand';
                        
                        if (!brandMap[brandId]) {
                            brandMap[brandId] = { brandId, brandName, concepts: [] };
                        }
                        brandMap[brandId].concepts.push(c);
                    }
                    
                    for (const brand of Object.values(brandMap)) {
                        brand.concepts.sort((a, b) => {
                            const pA = priorityOrder[a.priority] ?? 2;
                            const pB = priorityOrder[b.priority] ?? 2;
                            if (pA !== pB) return pA - pB;
                            return new Date(a.dueDate) - new Date(b.dueDate);
                        });
                    }
                    
                    return Object.values(brandMap).sort((a, b) => a.brandName.localeCompare(b.brandName));
                },

                filterByStatus(status) {
                    // Toggle filter: if clicking the same status, clear filter; otherwise set new filter
                    if (this.conceptStatusFilter === status) {
                        this.conceptStatusFilter = '';
                    } else {
                        this.conceptStatusFilter = status;
                    }
                    this.activeTab = 'concepts';
                    this.conceptClientFilter = '';
                    this.conceptSearch = '';
                    this.loadConcepts();
                },

                filterConceptsByAssignee(assigneeId) {
                    if (!assigneeId) return;
                    this.activeTab = 'concepts';
                    this.conceptStatusFilter = '';
                    this.conceptClientFilter = '';
                    this.conceptSearch = '';
                    this.efficiencyAssigneeFilter = '';
                    // Filter concepts to show only those assigned to this person
                    this.loadConceptsByAssignee(assigneeId);
                },

                filterConceptsByEfficiency(assigneeId) {
                    if (!assigneeId) return;
                    this.activeTab = 'concepts';
                    this.conceptStatusFilter = '';
                    this.conceptClientFilter = '';
                    this.conceptSearch = '';
                    this.efficiencyAssigneeFilter = assigneeId;
                    this.loadConcepts();
                },

                clearEfficiencyFilter() {
                    this.efficiencyAssigneeFilter = '';
                    this.loadConcepts();
                },

                async loadConceptsByAssignee(assigneeId) {
                    const token = localStorage.getItem('token');
                    try {
                        const response = await fetch(`${API_URL}/workflow/concepts?assignedTo=${assigneeId}`, { headers: { 'Authorization': `Bearer ${token}` } });
                        if (response.ok) { const data = await response.json(); this.concepts = data.concepts || []; }
                    } catch (error) { console.error('Load concepts by assignee error:', error); }
                },

                async downloadAttachment(attachment) {
                    try {
                        const response = await fetch(attachment.url);
                        const blob = await response.blob();
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = attachment.originalName || attachment.filename || 'download';
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        window.URL.revokeObjectURL(url);
                    } catch (error) {
                        console.error('Download error:', error);
                        this.showToast('Failed to download file', 'error');
                    }
                },

                async saveDriveLink(concept) {
                    const token = localStorage.getItem('token');
                    const link = this.driveLinkInput || concept.driveLink || '';
                    try {
                        const response = await fetch(`${API_URL}/workflow/concepts/${concept._id}`, {
                            method: 'PUT',
                            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
                            body: JSON.stringify({ driveLink: link })
                        });
                        if (response.ok) {
                            const data = await response.json();
                            this.selectedConcept = data.concept;
                            this.driveLinkInput = '';
                            this.showToast(link ? 'Google Drive link saved' : 'Google Drive link removed', 'success');
                        } else {
                            this.showToast('Failed to save Drive link', 'error');
                        }
                    } catch (error) {
                        console.error('Save drive link error:', error);
                        this.showToast('Failed to save Drive link', 'error');
                    }
                },

                async deleteAttachment(conceptId, attachmentId) {
                    if (!confirm('Are you sure you want to delete this design?')) return;
                    const token = localStorage.getItem('token');
                    try {
                        const response = await fetch(`${API_URL}/workflow/concepts/${conceptId}/attachments/${attachmentId}`, {
                            method: 'DELETE',
                            headers: { 'Authorization': `Bearer ${token}` }
                        });
                        if (response.ok) {
                            const updatedConcept = await fetch(`${API_URL}/workflow/concepts/${conceptId}`, {
                                headers: { 'Authorization': `Bearer ${token}` }
                            });
                            if (updatedConcept.ok) {
                                const data = await updatedConcept.json();
                                this.selectedConcept = data.concept;
                            }
                            await this.loadConcepts();
                            await this.loadInitialData();
                        } else {
                            const error = await response.json();
                            this.showToast(error.message || 'Failed to delete attachment', 'error');
                        }
                    } catch (error) {
                        console.error('Delete attachment error:', error);
                        this.showToast('Failed to delete attachment', 'error');
                    }
                },

                async saveConcept() {
                    const token = localStorage.getItem('token');
                    const url = this.editingConcept ? `${API_URL}/workflow/concepts/${this.editingConcept._id}` : `${API_URL}/workflow/concepts`;
                    const method = this.editingConcept ? 'PUT' : 'POST';
                    try {
                        const response = await fetch(url, { method, headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }, body: JSON.stringify(this.conceptForm) });
                        if (response.ok) {
                            const d = await response.json();
                            const conceptId = d.concept?._id || this.editingConcept?._id;

                            if (conceptId) {
                                for (let i = 0; i < 2; i++) {
                                    if (this.referenceImageFiles[i]) {
                                        const fd = new FormData();
                                        fd.append('file', this.referenceImageFiles[i]);
                                        const up = await fetch(`${API_URL}/workflow/concepts/${conceptId}/attachments?kind=reference`, {
                                            method: 'POST',
                                            headers: { 'Authorization': `Bearer ${token}` },
                                            body: fd
                                        });
                                        if (!up.ok) {
                                            const err = await up.json().catch(() => ({}));
                                            this.showToast(err.message || `Image ${i + 1} upload failed`, 'error');
                                        }
                                    }
                                }
                            }

                            this.showConceptModal = false;
                            this.clearAllReferenceImages();
                            await this.loadConcepts();
                            await this.loadInitialData();
                        }
                        else { const error = await response.json(); this.showToast(error.message || 'Failed to save concept', 'error'); }
                    } catch (error) { console.error('Save concept error:', error); this.showToast('Failed to save concept', 'error'); }
                },

                async viewConcept(concept) {
                    const token = localStorage.getItem('token');
                    const conceptId = concept._id || concept.id;
                    if (!conceptId) { console.error('No concept ID found', concept); return; }
                    try {
                        const response = await fetch(`${API_URL}/workflow/concepts/${conceptId}`, { headers: { 'Authorization': `Bearer ${token}` } });
                        if (response.ok) { const data = await response.json(); this.selectedConcept = data.concept; this.showConceptDetail = true; }
                        else { console.error('View concept failed:', response.status); }
                    } catch (error) { console.error('View concept error:', error); }
                },

                async updateConceptStatus(newStatus) {
                    const token = localStorage.getItem('token');
                    try {
                        const response = await fetch(`${API_URL}/workflow/concepts/${this.selectedConcept._id}`, { method: 'PUT', headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }, body: JSON.stringify({ status: newStatus }) });
                        if (response.ok) {
                            const data = await response.json();
                            this.selectedConcept = data.concept;
                            await this.loadConcepts();
                            await this.loadInitialData();
                            // If the item moved into content bank, refresh bank list too
                            if (this.selectedConcept?.status === 'in_content_bank' || newStatus === 'approved') {
                                await this.loadContentBank();
                            }
                        }
                    } catch (error) { console.error('Update status error:', error); }
                },

                async addFeedback() {
                    if (!this.newFeedback.trim()) return;
                    const token = localStorage.getItem('token');
                    try {
                        const response = await fetch(`${API_URL}/workflow/concepts/${this.selectedConcept._id}/feedback`, { 
                            method: 'POST', 
                            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }, 
                            body: JSON.stringify({ message: this.newFeedback, type: 'comment', mentions: this.selectedConceptMentions }) 
                        });
                        if (response.ok) { 
                            const data = await response.json(); 
                            this.selectedConcept = data.concept; 
                            this.newFeedback = ''; 
                            this.selectedConceptMentions = [];
                            this.showConceptMentionDropdown = false;
                        }
                    } catch (error) { console.error('Add feedback error:', error); }
                },

                async handleConceptFileDrop(event) {
                    const file = event?.dataTransfer?.files?.[0];
                    if (!file) return;
                    // Reuse the same upload logic by creating a fake event
                    const fakeEvent = { target: { files: [file] } };
                    await this.handleConceptFileUpload(fakeEvent);
                },

                async handleConceptFileUpload(event) {
                    const file = event?.target?.files?.[0];
                    if (!file) return;
                    
                    const maxSize = 50 * 1024 * 1024; // 50MB
                    if (file.size > maxSize) {
                        this.showToast('File size must be less than 50MB', 'error');
                        return;
                    }
                    
                    const token = localStorage.getItem('token');
                    const formData = new FormData();
                    formData.append('file', file);

                    const shouldReplace = ['needs_revision', 'rejected'].includes(this.selectedConcept?.status);
                    const uploadUrl = `${API_URL}/workflow/concepts/${this.selectedConcept._id}/attachments?kind=design&replace=${shouldReplace ? 'true' : 'false'}`;
                    
                    try {
                        const response = await fetch(uploadUrl, {
                            method: 'POST',
                            headers: { 'Authorization': `Bearer ${token}` },
                            body: formData
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
                            if (data?.concept) {
                                this.selectedConcept = data.concept;
                            } else {
                                // Safety: refresh full concept so status/actions don't disappear
                                const r = await fetch(`${API_URL}/workflow/concepts/${this.selectedConcept._id}`, { headers: { 'Authorization': `Bearer ${token}` } });
                                if (r.ok) { const d = await r.json(); this.selectedConcept = d.concept; }
                            }
                            this.showToast('File uploaded successfully', 'success');
                            event.target.value = '';
                        } else {
                            const error = await response.json().catch(() => ({ message: 'Upload failed' }));
                            this.showToast(error.message || 'Failed to upload file', 'error');
                        }
                    } catch (error) {
                        console.error('File upload error:', error);
                        this.showToast('Failed to upload file', 'error');
                    }
                },

                handleConceptMentionInput(event) {
                    const text = event.target.value;
                    const cursorPos = event.target.selectionStart;
                    const textBeforeCursor = text.substring(0, cursorPos);
                    const match = textBeforeCursor.match(/@(\w*)$/);
                    
                    if (match) {
                        this.showConceptMentionDropdown = true;
                        const query = match[1].toLowerCase();
                        this.filteredConceptUsers = this.getMentionCandidates().filter(u => 
                            (u.firstName + ' ' + u.lastName).toLowerCase().includes(query)
                        );
                    } else {
                        this.showConceptMentionDropdown = false;
                    }
                },

                selectConceptMention(user) {
                    if (!this.selectedConceptMentions.includes(user._id)) {
                        this.selectedConceptMentions.push(user._id);
                    }
                    const textarea = document.querySelector('textarea[x-model="newFeedback"]');
                    if (textarea) {
                        const text = this.newFeedback;
                        const cursorPos = textarea.selectionStart;
                        const textBeforeCursor = text.substring(0, cursorPos);
                        const match = textBeforeCursor.match(/@(\w*)$/);
                        if (match) {
                            const beforeMention = textBeforeCursor.substring(0, match.index);
                            const afterCursor = text.substring(cursorPos);
                            this.newFeedback = beforeMention + '@' + user.firstName + ' ' + user.lastName + ' ' + afterCursor;
                        }
                    }
                    this.showConceptMentionDropdown = false;
                },


                async updateTaskStatus(taskId, newStatus) {
                    const token = localStorage.getItem('token');
                    try { await fetch(`${API_URL}/workflow/tasks/${taskId}/status`, { method: 'PUT', headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }, body: JSON.stringify({ status: newStatus }) }); await this.loadTasks(); }
                    catch (error) { console.error('Update task status error:', error); }
                },

                async deleteConcept(concept) {
                    if (!confirm(`Are you sure you want to delete "${concept.title}"? This will also delete all associated tasks.`)) return;
                    const token = localStorage.getItem('token');
                    try {
                        const response = await fetch(`${API_URL}/workflow/concepts/${concept._id}`, { method: 'DELETE', headers: { 'Authorization': `Bearer ${token}` } });
                        if (response.ok) { 
                            this.showConceptDetail = false; 
                            await this.loadConcepts(); 
                            await this.loadInitialData(); 
                            this.showToast('Concept deleted successfully', 'success'); 
                        } else { 
                            const error = await response.json(); 
                            this.showToast(error.message || 'Failed to delete concept', 'error'); 
                        }
                    } catch (error) { console.error('Delete concept error:', error); this.showToast('Failed to delete concept', 'error'); }
                },

                async handleNotificationClick(notification) {
                    // Mark this notification as read
                    if (!notification.isRead) {
                        notification.isRead = true;
                        this.unreadNotifications = Math.max(0, this.unreadNotifications - 1);
                        const token = localStorage.getItem('token');
                        try {
                            await fetch(`${API_URL}/workflow/notifications/read`, {
                                method: 'PUT',
                                headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
                                body: JSON.stringify({ notificationIds: [notification._id] })
                            });
                        } catch (e) { console.error('Mark notification read error:', e); }
                    }

                    // Navigate based on notification type and relatedModel
                    const id = notification.relatedId;
                    const model = notification.relatedModel;

                    if (model === 'Concept' && id) {
                        const token = localStorage.getItem('token');
                        try {
                            // First check the concept status to decide which detail to open
                            const res = await fetch(`${API_URL}/workflow/concepts/${id}`, {
                                headers: { 'Authorization': `Bearer ${token}` }
                            });
                            if (res.ok) {
                                const data = await res.json();
                                const concept = data.concept;
                                if (concept) {
                                    if (['in_content_bank', 'client_approved', 'posted', 'needs_revision'].includes(concept.status) && concept.clientApprovalStatus) {
                                        // Open Content Bank detail popup
                                        this.activeTab = 'contentBank';
                                        this.setWorkflowQuery('contentBank', { content: String(concept._id) });
                                        await this.loadContentBank();
                                        await this.viewContentBankItem(concept);
                                    } else {
                                        // Open Concepts detail popup
                                        this.activeTab = 'concepts';
                                        this.setWorkflowQuery('concepts', { concept: String(id) });
                                        this.selectedConcept = concept;
                                        this.showConceptDetail = true;
                                    }
                                }
                            }
                        } catch (e) { console.error('Notification navigate error:', e); }
                    } else if (model === 'Production' && id) {
                        this.activeTab = 'productions';
                        this.setWorkflowQuery('productions', {});
                        const token = localStorage.getItem('token');
                        try {
                            const res = await fetch(`${API_URL}/production/projects/${id}`, {
                                headers: { 'Authorization': `Bearer ${token}` }
                            });
                            if (res.ok) {
                                const data = await res.json();
                                if (data.project) {
                                    this.selectedProduction = data.project;
                                    this.showProductionDetail = true;
                                }
                            }
                        } catch (e) { console.error('Notification navigate error:', e); }
                    }
                },

                async markAllRead() {
                    const token = localStorage.getItem('token');
                    try { await fetch(`${API_URL}/workflow/notifications/read`, { method: 'PUT', headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }, body: JSON.stringify({}) }); this.notifications.forEach(n => n.isRead = true); this.unreadNotifications = 0; }
                    catch (error) { console.error('Mark read error:', error); }
                },

                // Production Workflow Functions
                async loadProductions() {
                    const token = localStorage.getItem('token');
                    const params = new URLSearchParams();
                    if (this.productionStatusFilter) params.append('status', this.productionStatusFilter);
                    if (this.productionClientFilter) params.append('clientId', this.productionClientFilter);
                    try {
                        const response = await fetch(`${API_URL}/production/projects?${params}`, { headers: { 'Authorization': `Bearer ${token}` } });
                        if (response.ok) { const data = await response.json(); this.productions = data.projects || []; }
                    } catch (error) { console.error('Load productions error:', error); }
                },

                openProductionModal() {
                    this.editingProduction = null;
                    this.productionForm = { title: '', description: '', clientId: '', videoType: 'commercial', platform: [], priority: 'medium', dueDate: '', scriptwriter: '', director: '', editor: '', producer: '', driveBackupUrl: '', duration: '' };
                    this.showProductionModal = true;
                },

                async saveProduction() {
                    const token = localStorage.getItem('token');
                    const url = this.editingProduction ? `${API_URL}/production/projects/${this.editingProduction._id}` : `${API_URL}/production/projects`;
                    const method = this.editingProduction ? 'PUT' : 'POST';
                    
                    // Ensure platform is an array
                    if (!Array.isArray(this.productionForm.platform)) {
                        this.productionForm.platform = [];
                    }
                    
                    // Validate required fields
                    if (!this.productionForm.title || !this.productionForm.description || !this.productionForm.clientId || !this.productionForm.dueDate) {
                        this.showToast('Please fill in all required fields', 'error');
                        return;
                    }
                    
                    if (this.productionForm.platform.length === 0) {
                        this.showToast('Please select at least one platform', 'error');
                        return;
                    }
                    
                    try {
                        console.log('Sending production data:', this.productionForm);
                        const response = await fetch(url, { method, headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }, body: JSON.stringify(this.productionForm) });
                        if (response.ok) {
                            this.showProductionModal = false;
                            await this.loadProductions();
                            this.showToast(this.editingProduction ? 'Production updated successfully' : 'Production created successfully', 'success');
                        } else { 
                            const error = await response.json().catch(() => ({ message: 'Unknown error' })); 
                            console.error('Production save error:', error);
                            this.showToast(error.message || error.error || 'Failed to save production', 'error'); 
                        }
                    } catch (error) { 
                        console.error('Save production error:', error); 
                        this.showToast('Failed to save production: ' + error.message, 'error'); 
                    }
                },

                async viewProduction(project) {
                    const token = localStorage.getItem('token');
                    try {
                        const response = await fetch(`${API_URL}/production/projects/${project._id}`, { headers: { 'Authorization': `Bearer ${token}` } });
                        if (response.ok) { const data = await response.json(); this.selectedProduction = data.project; this.showProductionDetail = true; }
                    } catch (error) { console.error('View production error:', error); }
                },

                async updateProductionStatus(newStatus) {
                    const token = localStorage.getItem('token');
                    try {
                        const response = await fetch(`${API_URL}/production/projects/${this.selectedProduction._id}`, { method: 'PUT', headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }, body: JSON.stringify({ status: newStatus }) });
                        if (response.ok) { const data = await response.json(); this.selectedProduction = data.project; await this.loadProductions(); }
                    } catch (error) { console.error('Update production status error:', error); }
                },

                async addProductionComment() {
                    if (!this.newProductionComment.trim()) return;
                    const token = localStorage.getItem('token');
                    try {
                        const response = await fetch(`${API_URL}/production/projects/${this.selectedProduction._id}/comments`, {
                            method: 'POST',
                            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
                            body: JSON.stringify({ message: this.newProductionComment, mentions: this.selectedMentions })
                        });
                        if (response.ok) {
                            const data = await response.json();
                            this.selectedProduction = data.project;
                            this.newProductionComment = '';
                            this.selectedMentions = [];
                        }
                    } catch (error) { console.error('Add production comment error:', error); }
                },

                editProduction(production) {
                    this.editingProduction = production;
                    this.productionForm = {
                        title: production.title,
                        description: production.description,
                        clientId: production.clientId?._id || production.clientId,
                        videoType: production.videoType,
                        platform: production.platform || [],
                        priority: production.priority,
                        dueDate: production.dueDate ? production.dueDate.split('T')[0] : '',
                        scriptwriter: production.scriptwriter?._id || production.scriptwriter || '',
                        director: production.director?._id || production.director || '',
                        editor: production.editor?._id || production.editor || '',
                        producer: production.producer?._id || production.producer || '',
                        driveBackupUrl: production.driveBackupUrl || '',
                        duration: production.duration || ''
                    };
                    this.showProductionDetail = false;
                    this.showProductionModal = true;
                },

                async deleteProduction(productionId) {
                    if (!confirm('Are you sure you want to delete this production project? This action cannot be undone.')) return;
                    const token = localStorage.getItem('token');
                    try {
                        const response = await fetch(`${API_URL}/production/projects/${productionId}`, {
                            method: 'DELETE',
                            headers: { 'Authorization': `Bearer ${token}` }
                        });
                        if (response.ok) {
                            this.showProductionDetail = false;
                            await this.loadProductions();
                            this.showToast('Production deleted successfully', 'success');
                        } else {
                            const error = await response.json().catch(() => ({ message: 'Failed to delete production' }));
                            this.showToast(error.message || 'Failed to delete production', 'error');
                        }
                    } catch (error) {
                        console.error('Delete production error:', error);
                        this.showToast('Failed to delete production', 'error');
                    }
                },

                async handleProductionFileUpload(event) {
                    const file = event?.target?.files?.[0];
                    if (!file) return;
                    
                    const maxSize = 50 * 1024 * 1024; // 50MB
                    if (file.size > maxSize) {
                        this.showToast('File size must be less than 50MB', 'error');
                        return;
                    }
                    
                    const token = localStorage.getItem('token');
                    const formData = new FormData();
                    formData.append('file', file);
                    
                    try {
                        const response = await fetch(`${API_URL}/production/projects/${this.selectedProduction._id}/attachments`, {
                            method: 'POST',
                            headers: { 'Authorization': `Bearer ${token}` },
                            body: formData
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
                            this.selectedProduction = data.project;
                            this.showToast('File uploaded successfully', 'success');
                            event.target.value = '';
                        } else {
                            const error = await response.json().catch(() => ({ message: 'Upload failed' }));
                            this.showToast(error.message || 'Failed to upload file', 'error');
                        }
                    } catch (error) {
                        console.error('File upload error:', error);
                        this.showToast('Failed to upload file', 'error');
                    }
                },

                handleMentionInput(event) {
                    const text = event.target.value;
                    const cursorPos = event.target.selectionStart;
                    const textBeforeCursor = text.substring(0, cursorPos);
                    const match = textBeforeCursor.match(/@(\w*)$/);
                    
                    if (match) {
                        this.showMentionDropdown = true;
                        const query = match[1].toLowerCase();
                        this.filteredUsers = this.getMentionCandidates().filter(u => 
                            (u.firstName + ' ' + u.lastName).toLowerCase().includes(query)
                        );
                    } else {
                        this.showMentionDropdown = false;
                    }
                },

                selectMention(user) {
                    if (!this.selectedMentions.includes(user._id)) {
                        this.selectedMentions.push(user._id);
                    }
                    const textarea = document.querySelector('textarea[x-model="newProductionComment"]');
                    if (textarea) {
                        const text = this.newProductionComment;
                        const cursorPos = textarea.selectionStart;
                        const textBeforeCursor = text.substring(0, cursorPos);
                        const match = textBeforeCursor.match(/@(\w*)$/);
                        if (match) {
                            const beforeMention = textBeforeCursor.substring(0, match.index);
                            const afterCursor = text.substring(cursorPos);
                            this.newProductionComment = beforeMention + '@' + user.firstName + ' ' + user.lastName + ' ' + afterCursor;
                        }
                    }
                    this.showMentionDropdown = false;
                },

                formatProductionStatus(status) {
                    const statusMap = {
                        'scripting': 'Scripting',
                        'script_review': 'Script Review',
                        'filming': 'Filming',
                        'editing': 'Editing',
                        'internal_review': 'Internal Review',
                        'client_review': 'Client Review',
                        'revisions': 'Revisions',
                        'approved': 'Approved',
                        'final_delivery': 'Final Delivery',
                        'delivered': 'Delivered'
                    };
                    return statusMap[status] || status;
                },

                getProductionStatusClass(status) {
                    const classes = {
                        'scripting': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                        'script_review': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                        'filming': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                        'editing': 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
                        'internal_review': 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300',
                        'client_review': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                        'revisions': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                        'approved': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                        'final_delivery': 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-300',
                        'delivered': 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300'
                    };
                    return classes[status] || classes.scripting;
                },

                getHoursInStatus(project) {
                    if (!project?.statusChangedAt) return 0;
                    const now = new Date();
                    const changed = new Date(project.statusChangedAt);
                    const diffMs = now - changed;
                    return Math.floor(diffMs / (1000 * 60 * 60));
                },

                getSLAPercentage(project) {
                    const hours = this.getHoursInStatus(project);
                    const slaGoal = project?.slaGoals?.[project.status];
                    if (!slaGoal) return 0;
                    return Math.min(100, (hours / slaGoal) * 100);
                },

                getSLABarClass(project) {
                    const percentage = this.getSLAPercentage(project);
                    if (percentage >= 100) return 'bg-red-500';
                    if (percentage >= 80) return 'bg-yellow-500';
                    return 'bg-green-500';
                },

                getSLATextClass(project) {
                    const percentage = this.getSLAPercentage(project);
                    if (percentage >= 100) return 'text-red-600 dark:text-red-400 font-semibold';
                    if (percentage >= 80) return 'text-yellow-600 dark:text-yellow-400';
                    return 'text-green-600 dark:text-green-400';
                },

                // Content Bank Functions
                getDesignPreview(item) {
                    const attachments = item?.attachments || [];
                    if (!Array.isArray(attachments) || attachments.length === 0) return null;
                    const designs = attachments.filter(a => {
                        const isDesignKind = a?.kind === 'design';
                        const isImage = (a?.mimetype || '').startsWith('image/');
                        const notReference = a?.kind !== 'reference';
                        return isImage && (isDesignKind || notReference);
                    });
                    if (designs.length === 0) return null;
                    const latest = designs.sort((a, b) => new Date(b.uploadedAt || 0) - new Date(a.uploadedAt || 0))[0];
                    return latest?.url || null;
                },

                async loadContentBank() {
                    const token = localStorage.getItem('token');
                    try {
                        let url = `${API_URL}/content-bank?`;
                        if (this.contentBankStatusFilter) url += `status=${this.contentBankStatusFilter}&`;
                        if (this.contentBankClientFilter) url += `clientId=${this.contentBankClientFilter}&`;
                        // Add viewAs for role simulation
                        if (this.viewMode && this.viewMode !== 'admin') {
                            url += `viewAs=${this.viewMode}&`;
                            if (this.viewMode === 'client' && this.selectedViewClient) {
                                url += `viewAsClientId=${this.selectedViewClient}&`;
                                // Also pass clientId directly for client view filtering
                                if (!this.contentBankClientFilter) {
                                    url += `clientId=${this.selectedViewClient}&`;
                                }
                            }
                        }
                        const response = await fetch(url, { headers: { 'Authorization': `Bearer ${token}` } });
                        if (response.ok) { const data = await response.json(); this.contentBankItems = data.concepts || []; }
                    } catch (error) { console.error('Load content bank error:', error); }
                },

                async viewContentBankItem(item) {
                    const token = localStorage.getItem('token');
                    try {
                        const response = await fetch(`${API_URL}/content-bank/${item._id}`, { headers: { 'Authorization': `Bearer ${token}` } });
                        if (response.ok) { 
                            const data = await response.json(); 
                            this.selectedContentBankItem = data.concept; 
                            this.editingContentBankItem = false;
                            this.showContentBankDetail = true; 
                        }
                    } catch (error) { console.error('View content bank item error:', error); }
                },

                startEditContentBankItem() {
                    this.contentBankEditForm = {
                        title: this.selectedContentBankItem?.title || '',
                        caption: this.selectedContentBankItem?.caption || this.selectedContentBankItem?.description || '',
                        scheduledPostDate: this.selectedContentBankItem?.scheduledPostDate ? this.selectedContentBankItem.scheduledPostDate.split('T')[0] : ''
                    };
                    this.editingContentBankItem = true;
                },

                async saveContentBankEdit() {
                    const token = localStorage.getItem('token');
                    try {
                        const response = await fetch(`${API_URL}/content-bank/${this.selectedContentBankItem._id}`, {
                            method: 'PUT',
                            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
                            body: JSON.stringify(this.contentBankEditForm)
                        });
                        if (response.ok) {
                            const data = await response.json();
                            this.selectedContentBankItem = data.concept;
                            this.editingContentBankItem = false;
                            await this.loadContentBank();
                            this.showToast('Content updated successfully', 'success');
                        } else {
                            const error = await response.json();
                            this.showToast(error.message || 'Failed to update content', 'error');
                        }
                    } catch (error) { console.error('Save content bank edit error:', error); this.showToast('Failed to save changes', 'error'); }
                },

                async approveContentBankItem() {
                    if (!confirm('Are you sure you want to approve this content?')) return;
                    const token = localStorage.getItem('token');
                    try {
                        const response = await fetch(`${API_URL}/content-bank/${this.selectedContentBankItem._id}/approve`, {
                            method: 'PUT',
                            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }
                        });
                        if (response.ok) {
                            const data = await response.json();
                            this.selectedContentBankItem = data.concept;
                            await this.loadContentBank();
                            this.showToast('Content approved successfully', 'success');
                        } else {
                            const error = await response.json();
                            this.showToast(error.message || 'Failed to approve content', 'error');
                        }
                    } catch (error) { console.error('Approve content bank item error:', error); this.showToast('Failed to approve content', 'error'); }
                },

                async requestContentBankChanges() {
                    const feedback = prompt('Please provide feedback on what changes are needed:');
                    if (!feedback) return;
                    const token = localStorage.getItem('token');
                    try {
                        const response = await fetch(`${API_URL}/content-bank/${this.selectedContentBankItem._id}/request-changes`, {
                            method: 'PUT',
                            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
                            body: JSON.stringify({ feedback })
                        });
                        if (response.ok) {
                            const data = await response.json();
                            this.selectedContentBankItem = data.concept;
                            await this.loadContentBank();
                            this.showToast('Change request submitted successfully', 'success');
                        } else {
                            const error = await response.json();
                            this.showToast(error.message || 'Failed to request changes', 'error');
                        }
                    } catch (error) { console.error('Request changes error:', error); this.showToast('Failed to request changes', 'error'); }
                },

                async deleteContentBankItem(item) {
                    if (!confirm('Are you sure you want to permanently delete this content? This action cannot be undone.')) return;
                    const token = localStorage.getItem('token');
                    try {
                        const response = await fetch(`${API_URL}/workflow/concepts/${item._id}`, {
                            method: 'DELETE',
                            headers: { 'Authorization': `Bearer ${token}` }
                        });
                        if (response.ok) {
                            this.showContentBankDetail = false;
                            this.selectedContentBankItem = null;
                            await this.loadContentBank();
                            await this.loadConcepts();
                            await this.loadInitialData();
                            this.showToast('Content deleted successfully', 'success');
                        } else {
                            const error = await response.json();
                            this.showToast(error.message || 'Failed to delete content', 'error');
                        }
                    } catch (error) { console.error('Delete content bank item error:', error); this.showToast('Failed to delete content', 'error'); }
                },

                async handleContentBankMediaUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    
                    const token = localStorage.getItem('token');
                    const formData = new FormData();
                    formData.append('file', file);
                    
                    try {
                        const response = await fetch(`${API_URL}/content-bank/${this.selectedContentBankItem._id}/upload-media`, {
                            method: 'POST',
                            headers: { 'Authorization': `Bearer ${token}` },
                            body: formData
                        });
                        if (response.ok) {
                            const data = await response.json();
                            this.selectedContentBankItem = data.concept;
                            await this.loadContentBank();
                            this.showToast('Media uploaded successfully', 'success');
                        } else {
                            const error = await response.json();
                            this.showToast(error.message || 'Failed to upload media', 'error');
                        }
                    } catch (error) { console.error('Upload media error:', error); this.showToast('Failed to upload media', 'error'); }
                    event.target.value = '';
                },

                async handleContentBankMediaDrop(event) {
                    const files = event.dataTransfer.files;
                    if (files.length === 0) return;
                    const file = files[0];
                    
                    // Validate file type
                    if (!file.type.startsWith('image/') && !file.type.startsWith('video/')) {
                        this.showToast('Please upload an image or video file', 'error');
                        return;
                    }
                    
                    const token = localStorage.getItem('token');
                    const formData = new FormData();
                    formData.append('file', file);
                    
                    try {
                        const response = await fetch(`${API_URL}/content-bank/${this.selectedContentBankItem._id}/upload-media`, {
                            method: 'POST',
                            headers: { 'Authorization': `Bearer ${token}` },
                            body: formData
                        });
                        if (response.ok) {
                            const data = await response.json();
                            this.selectedContentBankItem = data.concept;
                            await this.loadContentBank();
                            this.showToast('Media uploaded successfully', 'success');
                        } else {
                            const error = await response.json();
                            this.showToast(error.message || 'Failed to upload media', 'error');
                        }
                    } catch (error) { console.error('Upload media error:', error); this.showToast('Failed to upload media', 'error'); }
                },

                async uploadManualContent() {
                    if (!this.manualUploadForm.clientId || !this.manualUploadForm.title) {
                        this.showToast('Please select a client and enter a title', 'error');
                        return;
                    }

                    const token = localStorage.getItem('token');
                    const formData = new FormData();
                    formData.append('clientId', this.manualUploadForm.clientId);
                    formData.append('title', this.manualUploadForm.title);
                    formData.append('description', this.manualUploadForm.description);
                    formData.append('platform', this.manualUploadForm.platform);
                    
                    const fileInput = document.getElementById('manualUploadFiles');
                    if (fileInput && fileInput.files) {
                        for (let i = 0; i < fileInput.files.length; i++) {
                            formData.append('files', fileInput.files[i]);
                        }
                    }

                    try {
                        const response = await fetch(`${API_URL}/content-bank/manual-upload`, {
                            method: 'POST',
                            headers: { 'Authorization': `Bearer ${token}` },
                            body: formData
                        });

                        if (response.ok) {
                            this.showToast('Content uploaded successfully to Content Bank!', 'success');
                            this.showManualUploadModal = false;
                            this.manualUploadForm = { clientId: '', title: '', description: '', platform: 'instagram', files: [] };
                            if (fileInput) fileInput.value = '';
                            await this.loadContentBank();
                        } else {
                            const error = await response.json();
                            this.showToast(error.message || 'Failed to upload content', 'error');
                        }
                    } catch (error) {
                        console.error('Manual upload error:', error);
                        this.showToast('Failed to upload content', 'error');
                    }
                },

                async scheduleContent(item) {
                    const date = prompt('Enter scheduled post date (YYYY-MM-DD):');
                    if (!date) return;
                    const token = localStorage.getItem('token');
                    try {
                        const response = await fetch(`${API_URL}/content-bank/${item._id}/schedule`, {
                            method: 'PUT',
                            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
                            body: JSON.stringify({ scheduledPostDate: date })
                        });
                        if (response.ok) { await this.loadContentBank(); this.showToast('Content scheduled successfully', 'success'); }
                    } catch (error) { console.error('Schedule content error:', error); this.showToast('Failed to schedule content', 'error'); }
                },

                async markAsPosted(item) {
                    if (!confirm('Mark this content as posted?')) return;
                    const token = localStorage.getItem('token');
                    try {
                        const response = await fetch(`${API_URL}/content-bank/${item._id}/mark-posted`, {
                            method: 'PUT',
                            headers: { 'Authorization': `Bearer ${token}` }
                        });
                        if (response.ok) { await this.loadContentBank(); this.showToast('Content marked as posted', 'success'); }
                    } catch (error) { console.error('Mark posted error:', error); this.showToast('Failed to mark as posted', 'error'); }
                },

                formatClientApprovalStatus(status) {
                    const map = { 'pending': 'Awaiting Review', 'approved': 'Approved', 'needs_changes': 'Needs Changes' };
                    return map[status] || status;
                },

                getClientApprovalClass(status) {
                    const c = {
                        'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                        'approved': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                        'needs_changes': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                    };
                    return c[status] || c.pending;
                },

                switchViewMode(mode) {
                    this.viewMode = mode;
                    localStorage.setItem('viewMode', mode);
                    window.dispatchEvent(new CustomEvent('viewModeChanged', { detail: { viewMode: mode } }));
                    
                    // For client view, prompt to select a client if not already selected
                    if (mode === 'client' && !this.selectedViewClient && this.clients.length > 0) {
                        this.selectedViewClient = this.clients[0]._id;
                        localStorage.setItem('selectedViewClient', this.selectedViewClient);
                    } else if (mode !== 'client') {
                        this.selectedViewClient = null;
                        localStorage.removeItem('selectedViewClient');
                    }
                    
                    // Client view should only see Content Bank tab
                    if (mode === 'client') {
                        this.activeTab = 'contentBank';
                    }
                    
                    this.loadInitialData();
                    this.loadConcepts();
                    this.loadContentBank();
                    this.loadProductions();
                },

                applyTheme() { if (this.theme === 'dark') { document.documentElement.classList.add('dark'); } else { document.documentElement.classList.remove('dark'); } },
                toggleTheme() { this.theme = this.theme === 'light' ? 'dark' : 'light'; localStorage.setItem('theme', this.theme); this.applyTheme(); },
                setCurrentDate() { this.currentDate = new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }); },
                formatDate(date) { if (!date) return 'N/A'; return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }); },
                formatStatus(status) { if (!status) return ''; return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()); },
                getStatusClass(status) { const c = { draft: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300', assigned: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300', in_progress: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300', pending_review: 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300', needs_revision: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300', approved: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300', in_content_bank: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300', client_approved: 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-300', posted: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300', rejected: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300', completed: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300' }; return c[status] || c.draft; },
                getTaskStatusClass(status) { const c = { pending: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300', in_progress: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300', completed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' }; return c[status] || c.pending; },
                getPriorityClass(priority) { const c = { low: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300', medium: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300', high: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300', urgent: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }; return c[priority] || c.medium; },
                // ===== CONTENT BANK COMMENT METHODS =====
                handleCBMentionInput(event) {
                    const val = event.target.value;
                    const cursorPos = event.target.selectionStart;
                    const textBeforeCursor = val.substring(0, cursorPos);
                    const atMatch = textBeforeCursor.match(/@(\w*)$/);
                    if (atMatch) {
                        const search = atMatch[1].toLowerCase();
                        this.filteredCBUsers = this.getMentionCandidates().filter(u => 
                            (u.firstName + ' ' + u.lastName).toLowerCase().includes(search)
                        ).slice(0, 8);
                        this.showCBMentionDropdown = this.filteredCBUsers.length > 0;
                    } else {
                        this.showCBMentionDropdown = false;
                    }
                },

                selectCBMention(member) {
                    const textarea = document.querySelector('[x-model="newContentBankFeedback"]');
                    if (!textarea) return;
                    const val = this.newContentBankFeedback;
                    const cursorPos = textarea.selectionStart;
                    const textBeforeCursor = val.substring(0, cursorPos);
                    const atIndex = textBeforeCursor.lastIndexOf('@');
                    this.newContentBankFeedback = val.substring(0, atIndex) + '@' + member.firstName + ' ' + val.substring(cursorPos);
                    if (!this.selectedCBMentions.includes(member._id)) {
                        this.selectedCBMentions.push(member._id);
                    }
                    this.showCBMentionDropdown = false;
                },

                async addContentBankFeedback() {
                    if (!this.newContentBankFeedback.trim() || !this.selectedContentBankItem) return;
                    const token = localStorage.getItem('token');
                    try {
                        const response = await fetch(`${API_URL}/content-bank/${this.selectedContentBankItem._id}/feedback`, {
                            method: 'POST',
                            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
                            body: JSON.stringify({ message: this.newContentBankFeedback, type: 'comment', mentions: this.selectedCBMentions })
                        });
                        if (response.ok) {
                            const data = await response.json();
                            this.selectedContentBankItem = data.concept;
                            this.newContentBankFeedback = '';
                            this.selectedCBMentions = [];
                            this.showCBMentionDropdown = false;
                            this.showToast('Comment added', 'success');
                        }
                    } catch (error) { console.error('Add CB feedback error:', error); this.showToast('Failed to add comment', 'error'); }
                },

                // ===== FEED METHODS =====
                async loadFeed() {
                    this.feedLoading = true;
                    const token = localStorage.getItem('token');
                    const params = new URLSearchParams({ page: this.feedPage, limit: 15 });
                    if (this.feedClientFilter) params.append('clientId', this.feedClientFilter);
                    try {
                        const res = await fetch(`${API_URL}/workflow/feed?${params}`, { headers: { 'Authorization': `Bearer ${token}` } });
                        if (res.ok) {
                            const data = await res.json();
                            this.feedItems = data.feed || [];
                            this.feedTotalPages = data.pages || 1;
                            // Apply local type filter
                            if (this.feedTypeFilter) {
                                this.feedItems = this.feedItems.filter(f => f.type === this.feedTypeFilter);
                            }
                            // Mark feed as read when user visits the tab
                            if (this.activeTab === 'feed') {
                                this.lastFeedCheck = new Date().toISOString();
                                localStorage.setItem('lastFeedCheck', this.lastFeedCheck);
                                this.unreadFeedCount = 0;
                            }
                        }
                    } catch (error) { console.error('Load feed error:', error); }
                    finally { this.feedLoading = false; }
                },

                async checkUnreadFeed() {
                    const token = localStorage.getItem('token');
                    try {
                        const res = await fetch(`${API_URL}/workflow/feed?page=1&limit=50`, { headers: { 'Authorization': `Bearer ${token}` } });
                        if (res.ok) {
                            const data = await res.json();
                            const lastCheck = this.lastFeedCheck || localStorage.getItem('lastFeedCheck');
                            if (lastCheck) {
                                const lastCheckDate = new Date(lastCheck);
                                this.unreadFeedCount = (data.feed || []).filter(item => new Date(item.createdAt) > lastCheckDate).length;
                            } else {
                                this.unreadFeedCount = data.total || 0;
                            }
                        }
                    } catch (error) { console.error('Check unread feed error:', error); }
                },

                getFeedIcon(type) {
                    if (type === 'client_feedback') return 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z';
                    return 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z';
                },

                getFeedColor(type) {
                    if (type === 'client_feedback') return 'text-yellow-500';
                    return 'text-blue-500';
                },

                getFeedBg(type) {
                    if (type === 'client_feedback') return 'bg-yellow-50 dark:bg-yellow-900/10 border-yellow-200 dark:border-yellow-800';
                    return 'bg-blue-50 dark:bg-blue-900/10 border-blue-200 dark:border-blue-800';
                },

                getFeedLabel(type) {
                    if (type === 'client_feedback') return 'Client Feedback';
                    return 'Team Comment';
                },

                async openConceptFromFeed(conceptId) {
                    if (!conceptId) return;
                    const token = localStorage.getItem('token');
                    try {
                        const res = await fetch(`${API_URL}/workflow/concepts/${conceptId}`, { headers: { 'Authorization': `Bearer ${token}` } });
                        if (res.ok) {
                            const data = await res.json();
                            this.selectedConcept = data.concept;
                            this.showConceptDetail = true;
                            this.activeTab = 'concepts';
                            this.setWorkflowQuery('concepts', { concept: conceptId });
                        }
                    } catch (error) { console.error('Open concept from feed error:', error); }
                },

                // ===== PLANNER METHODS =====
                async loadPlans() {
                    const token = localStorage.getItem('token');
                    const params = new URLSearchParams({ status: this.planStatusFilter });
                    if (this.planSearchQuery) params.append('search', this.planSearchQuery);
                    try {
                        const res = await fetch(`${API_URL}/planner/plans?${params}`, { headers: { 'Authorization': `Bearer ${token}` } });
                        if (res.ok) { const data = await res.json(); this.plans = data.plans || []; }
                    } catch (error) { console.error('Load plans error:', error); }
                },

                async createNewPlan() {
                    const token = localStorage.getItem('token');
                    try {
                        const res = await fetch(`${API_URL}/planner/plans`, {
                            method: 'POST',
                            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
                            body: JSON.stringify({ title: 'Untitled Plan', coverColor: ['#667eea','#f093fb','#4facfe','#43e97b','#fa709a','#fee140'][Math.floor(Math.random()*6)] })
                        });
                        if (res.ok) {
                            const data = await res.json();
                            this.showToast('Plan created', 'success');
                            await this.openPlan(data.plan._id);
                        }
                    } catch (error) { console.error('Create plan error:', error); this.showToast('Failed to create plan', 'error'); }
                },

                async openPlan(planId) {
                    const token = localStorage.getItem('token');
                    try {
                        const res = await fetch(`${API_URL}/planner/plans/${planId}`, { headers: { 'Authorization': `Bearer ${token}` } });
                        if (res.ok) {
                            const data = await res.json();
                            this.activePlan = data.plan;
                            this.activeSlideIndex = 0;
                            this.selectedElement = null;
                            this.planDirty = false;
                        } else {
                            this.showToast('Failed to open plan', 'error');
                        }
                    } catch (error) { console.error('Open plan error:', error); this.showToast('Failed to open plan', 'error'); }
                },

                closePlanEditor() {
                    if (this.planDirty) {
                        if (!confirm('You have unsaved changes. Save before closing?')) {
                            this.activePlan = null;
                            this.loadPlans();
                            return;
                        }
                        this.saveAllSlides().then(() => { this.activePlan = null; this.loadPlans(); });
                        return;
                    }
                    this.activePlan = null;
                    this.loadPlans();
                },

                async savePlanMeta() {
                    if (!this.activePlan) return;
                    const token = localStorage.getItem('token');
                    try {
                        await fetch(`${API_URL}/planner/plans/${this.activePlan._id}`, {
                            method: 'PUT',
                            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
                            body: JSON.stringify({ title: this.activePlan.title, description: this.activePlan.description, coverColor: this.activePlan.coverColor, tags: this.activePlan.tags })
                        });
                    } catch (error) { console.error('Save plan meta error:', error); }
                },

                async saveAllSlides() {
                    if (!this.activePlan) return;
                    const token = localStorage.getItem('token');
                    try {
                        const res = await fetch(`${API_URL}/planner/plans/${this.activePlan._id}/slides`, {
                            method: 'PUT',
                            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
                            body: JSON.stringify({ slides: this.activePlan.slides })
                        });
                        if (res.ok) { this.planDirty = false; this.showToast('Slides saved', 'success'); }
                        else { this.showToast('Failed to save slides', 'error'); }
                    } catch (error) { console.error('Save slides error:', error); this.showToast('Failed to save', 'error'); }
                },

                async addSlide() {
                    if (!this.activePlan) return;
                    const token = localStorage.getItem('token');
                    try {
                        const res = await fetch(`${API_URL}/planner/plans/${this.activePlan._id}/slides`, {
                            method: 'POST',
                            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
                            body: JSON.stringify({})
                        });
                        if (res.ok) {
                            const data = await res.json();
                            this.activePlan.slides = data.plan.slides;
                            this.activeSlideIndex = this.activePlan.slides.length - 1;
                        }
                    } catch (error) { console.error('Add slide error:', error); }
                },

                async deleteSlide(slideId, index) {
                    if (!this.activePlan || this.activePlan.slides.length <= 1) return;
                    if (!confirm('Delete this slide?')) return;
                    const token = localStorage.getItem('token');
                    try {
                        const res = await fetch(`${API_URL}/planner/plans/${this.activePlan._id}/slides/${slideId}`, {
                            method: 'DELETE',
                            headers: { 'Authorization': `Bearer ${token}` }
                        });
                        if (res.ok) {
                            const data = await res.json();
                            this.activePlan.slides = data.plan.slides;
                            if (this.activeSlideIndex >= this.activePlan.slides.length) {
                                this.activeSlideIndex = this.activePlan.slides.length - 1;
                            }
                            this.selectedElement = null;
                        }
                    } catch (error) { console.error('Delete slide error:', error); }
                },

                getCurrentSlide() {
                    return this.activePlan?.slides?.[this.activeSlideIndex] || null;
                },

                getSelectedElement() {
                    const slide = this.getCurrentSlide();
                    if (!slide || this.selectedElement === null) return { style: {} };
                    return slide.elements[this.selectedElement] || { style: {} };
                },

                markDirty() { this.planDirty = true; },

                addElement(type) {
                    const slide = this.getCurrentSlide();
                    if (!slide) return;
                    const defaults = {
                        text: { type: 'text', content: 'Double-click to edit', x: 100, y: 100, width: 250, height: 60, style: { fontSize: 18, fontWeight: 'normal', color: '#000000', backgroundColor: 'transparent', borderRadius: 0, textAlign: 'left', opacity: 1 }, zIndex: (slide.elements?.length || 0) + 1 },
                        sticky_note: { type: 'sticky_note', content: 'Note...', x: 120, y: 120, width: 200, height: 150, style: { fontSize: 14, fontWeight: 'normal', color: '#92400e', backgroundColor: '#fef3c7', borderRadius: 4, opacity: 1 }, zIndex: (slide.elements?.length || 0) + 1 },
                        image: { type: 'image', src: '', x: 150, y: 80, width: 300, height: 200, style: { borderRadius: 0, opacity: 1 }, zIndex: (slide.elements?.length || 0) + 1 },
                        shape: { type: 'shape', x: 200, y: 150, width: 150, height: 100, style: { backgroundColor: '#e5e7eb', borderColor: '#9ca3af', borderWidth: 2, borderRadius: 8, opacity: 1 }, zIndex: (slide.elements?.length || 0) + 1 }
                    };
                    if (!slide.elements) slide.elements = [];
                    slide.elements.push(defaults[type]);
                    this.selectedElement = slide.elements.length - 1;
                    this.markDirty();
                },

                deleteSelectedElement() {
                    const slide = this.getCurrentSlide();
                    if (!slide || this.selectedElement === null) return;
                    slide.elements.splice(this.selectedElement, 1);
                    this.selectedElement = null;
                    this.markDirty();
                },

                async uploadSlideImage(event) {
                    const file = event.target.files?.[0];
                    if (!file || !this.activePlan) return;
                    const token = localStorage.getItem('token');
                    const formData = new FormData();
                    formData.append('image', file);
                    try {
                        const res = await fetch(`${API_URL}/planner/plans/${this.activePlan._id}/upload`, {
                            method: 'POST',
                            headers: { 'Authorization': `Bearer ${token}` },
                            body: formData
                        });
                        if (res.ok) {
                            const data = await res.json();
                            this.addElement('image');
                            const slide = this.getCurrentSlide();
                            if (slide && slide.elements.length > 0) {
                                slide.elements[slide.elements.length - 1].src = data.url;
                            }
                            this.markDirty();
                        } else { this.showToast('Failed to upload image', 'error'); }
                    } catch (error) { console.error('Upload slide image error:', error); this.showToast('Upload failed', 'error'); }
                    event.target.value = '';
                },

                handleCanvasDrop(event) {
                    const files = event.dataTransfer?.files;
                    if (files?.length > 0 && files[0].type.startsWith('image/')) {
                        const fakeEvent = { target: { files: [files[0]] } };
                        this.uploadSlideImage(fakeEvent);
                    }
                },

                startDragElement(event, index) {
                    if (this.activePlan?.myPermission !== 'owner' && this.activePlan?.myPermission !== 'edit') return;
                    const el = this.getCurrentSlide()?.elements?.[index];
                    if (!el) return;
                    this.selectedElement = index;
                    const startX = event.clientX;
                    const startY = event.clientY;
                    const origX = el.x;
                    const origY = el.y;
                    const onMove = (e) => { el.x = origX + (e.clientX - startX); el.y = origY + (e.clientY - startY); };
                    const onUp = () => { document.removeEventListener('mousemove', onMove); document.removeEventListener('mouseup', onUp); this.markDirty(); };
                    document.addEventListener('mousemove', onMove);
                    document.addEventListener('mouseup', onUp);
                },

                startResizeElement(event, index) {
                    const el = this.getCurrentSlide()?.elements?.[index];
                    if (!el) return;
                    const startX = event.clientX;
                    const startY = event.clientY;
                    const origW = el.width;
                    const origH = el.height;
                    const onMove = (e) => { el.width = Math.max(30, origW + (e.clientX - startX)); el.height = Math.max(20, origH + (e.clientY - startY)); };
                    const onUp = () => { document.removeEventListener('mousemove', onMove); document.removeEventListener('mouseup', onUp); this.markDirty(); };
                    document.addEventListener('mousemove', onMove);
                    document.addEventListener('mouseup', onUp);
                },

                async archivePlan(planId) {
                    const newStatus = this.planStatusFilter === 'archived' ? 'active' : 'archived';
                    const token = localStorage.getItem('token');
                    try {
                        const res = await fetch(`${API_URL}/planner/plans/${planId}/status`, {
                            method: 'PUT',
                            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
                            body: JSON.stringify({ status: newStatus })
                        });
                        if (res.ok) { this.showToast(`Plan ${newStatus}`, 'success'); this.loadPlans(); }
                    } catch (error) { console.error('Archive plan error:', error); }
                },

                async deletePlan(planId) {
                    if (!confirm('Permanently delete this plan? This cannot be undone.')) return;
                    const token = localStorage.getItem('token');
                    try {
                        const res = await fetch(`${API_URL}/planner/plans/${planId}/status`, {
                            method: 'PUT',
                            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
                            body: JSON.stringify({ status: 'deleted' })
                        });
                        if (res.ok) { this.showToast('Plan deleted', 'success'); this.loadPlans(); }
                    } catch (error) { console.error('Delete plan error:', error); }
                },

                async duplicatePlan(planId) {
                    const token = localStorage.getItem('token');
                    try {
                        const res = await fetch(`${API_URL}/planner/plans/${planId}/duplicate`, {
                            method: 'POST',
                            headers: { 'Authorization': `Bearer ${token}` }
                        });
                        if (res.ok) { this.showToast('Plan duplicated', 'success'); this.loadPlans(); }
                    } catch (error) { console.error('Duplicate plan error:', error); }
                },

                showSharePlanModal(plan) {
                    this.sharePlanId = plan._id;
                    this.sharePlanCollaborators = JSON.parse(JSON.stringify(plan.collaborators || []));
                    this.shareForm = { userId: '', permission: 'view' };
                    this.showShareModal = true;
                },

                addCollaborator() {
                    if (!this.shareForm.userId) return;
                    const member = this.teamMembers.find(m => m._id === this.shareForm.userId);
                    if (!member) return;
                    this.sharePlanCollaborators.push({
                        userId: this.shareForm.userId,
                        firstName: member.firstName,
                        lastName: member.lastName,
                        permission: this.shareForm.permission
                    });
                    this.shareForm.userId = '';
                },

                async saveCollaborators() {
                    if (!this.sharePlanId) return;
                    const token = localStorage.getItem('token');
                    const collabs = this.sharePlanCollaborators.map(c => ({
                        userId: c.userId?._id || c.userId,
                        permission: c.permission
                    }));
                    try {
                        const res = await fetch(`${API_URL}/planner/plans/${this.sharePlanId}/collaborators`, {
                            method: 'PUT',
                            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
                            body: JSON.stringify({ collaborators: collabs })
                        });
                        if (res.ok) {
                            this.showShareModal = false;
                            this.showToast('Sharing settings saved', 'success');
                            if (this.activePlan && this.activePlan._id === this.sharePlanId) {
                                const data = await res.json();
                                this.activePlan.collaborators = data.collaborators;
                            }
                            this.loadPlans();
                        }
                    } catch (error) { console.error('Save collaborators error:', error); this.showToast('Failed to save sharing', 'error'); }
                },

                async addPlanComment() {
                    if (!this.newPlanComment.trim() || !this.activePlan) return;
                    const token = localStorage.getItem('token');
                    try {
                        const res = await fetch(`${API_URL}/planner/plans/${this.activePlan._id}/comments`, {
                            method: 'POST',
                            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
                            body: JSON.stringify({ message: this.newPlanComment, slideIndex: this.activeSlideIndex })
                        });
                        if (res.ok) {
                            const data = await res.json();
                            this.activePlan.comments = data.comments;
                            this.newPlanComment = '';
                        }
                    } catch (error) { console.error('Add comment error:', error); }
                },

                logout() { localStorage.removeItem('token'); localStorage.removeItem('user'); window.location.href = LOGIN_URL; }
            };
        }
    </script>

    <!-- Mobile Bottom Navigation -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 z-40" x-data="{ viewMode: localStorage.getItem('viewMode') || 'admin', user: (() => { try { return JSON.parse(localStorage.getItem('user') || '{}') || {}; } catch (e) { return {}; } })(), init() { window.addEventListener('viewModeChanged', (e) => { this.viewMode = e?.detail?.viewMode || (localStorage.getItem('viewMode') || 'admin'); }); } }">
        <div class="flex justify-around items-center h-16">
            <!-- CLIENT VIEW MOBILE NAV -->
            <!-- Content Hub - Client View -->
            <a x-show="viewMode === 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('workflow'))); ?>?tab=contentBank" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 dark:text-gray-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span class="text-xs">Content Hub</span>
            </a>
            <!-- Published Posts - Client View -->
            <a x-show="viewMode === 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('dashboard'))); ?>#content-calendar" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 dark:text-gray-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-xs">Posts</span>
            </a>
            <!-- Page Overview - Client View -->
            <a x-show="viewMode === 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('dashboard'))); ?>#overview" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 dark:text-gray-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="text-xs">Overview</span>
            </a>
            <!-- Content Calendar - Client View -->
            <a x-show="viewMode === 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('content-calendar'))); ?>" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 dark:text-gray-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-xs">Calendar</span>
            </a>
            <!-- Workflow - Client View (active on this page) -->
            <a x-show="viewMode === 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('workflow'))); ?>" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-indigo-600 dark:text-indigo-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                <span class="text-xs font-medium">Workflow</span>
            </a>

            <!-- ADMIN/BRAND REP VIEW MOBILE NAV -->
            <!-- Workflow - current page -->
            <a x-show="viewMode !== 'client'" href="#" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-indigo-600 dark:text-indigo-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-xs font-medium">Workflow</span>
            </a>
            <!-- Agency Overview -->
            <a x-show="viewMode !== 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('overview'))); ?>" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 dark:text-gray-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="text-xs">Overview</span>
            </a>
            <!-- Insights -->
            <a x-show="viewMode !== 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('dashboard'))); ?>" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 dark:text-gray-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="text-xs">Insights</span>
            </a>
            <!-- Published Posts -->
            <a x-show="viewMode !== 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('dashboard'))); ?>#content-calendar" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 dark:text-gray-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-xs">Posts</span>
            </a>
            <!-- Admin Panel - only for admins -->
            <a x-show="user?.role === 'admin' && viewMode === 'admin'" href="<?php echo esc_url(get_permalink(get_page_by_path('admin'))); ?>" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 dark:text-gray-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-xs">Admin</span>
            </a>
        </div>
    </nav>

    <!-- Toast Notifications -->
    <div class="fixed top-20 right-4 md:top-4 z-[9999] space-y-2 w-full max-w-sm pr-4">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-full"
                 x-transition:enter-end="opacity-100 transform translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="w-full shadow-lg rounded-lg pointer-events-auto overflow-hidden"
                 :class="{
                     'bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-700': toast.type === 'success',
                     'bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700': toast.type === 'error',
                     'bg-blue-50 dark:bg-blue-900/50 border border-blue-200 dark:border-blue-700': toast.type === 'info',
                     'bg-yellow-50 dark:bg-yellow-900/50 border border-yellow-200 dark:border-yellow-700': toast.type === 'warning'
                 }">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg x-show="toast.type === 'success'" class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <svg x-show="toast.type === 'error'" class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <svg x-show="toast.type === 'info'" class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <svg x-show="toast.type === 'warning'" class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium" :class="{'text-green-800 dark:text-green-200': toast.type === 'success', 'text-red-800 dark:text-red-200': toast.type === 'error', 'text-blue-800 dark:text-blue-200': toast.type === 'info', 'text-yellow-800 dark:text-yellow-200': toast.type === 'warning'}" x-text="toast.message"></p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</body>
</html>
<?php get_footer(); ?>
