<?php
/**
 * Template Name: Agency Overview
 * Template Post Type: page
 *
 * @package Esirom_Client_Hub
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Hide WordPress admin bar on this page
$esirom_overview_page_path = '/overview';
$esirom_ov_id = (int) get_queried_object_id();
if ($esirom_ov_id < 1 && function_exists('get_page_by_path')) {
    $p = get_page_by_path('overview');
    if ($p && ! is_wp_error($p) && ! empty($p->ID)) {
        $esirom_ov_id = (int) $p->ID;
    }
}
if ($esirom_ov_id > 0) {
    $ov_link = get_permalink($esirom_ov_id);
    if ($ov_link) {
        $ov_path = parse_url($ov_link, PHP_URL_PATH);
        if (is_string($ov_path) && $ov_path !== '') {
            $esirom_overview_page_path = rtrim($ov_path, '/');
        }
    }
}

show_admin_bar(false);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="h-full">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agency Overview | <?php bloginfo('name'); ?></title>
    
    <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        const theme = localStorage.getItem('theme') || 'light';
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
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
        .ov-focus {
            -webkit-tap-highlight-color: transparent;
        }
        .ov-focus:focus-visible {
            outline: 2px solid rgba(99, 102, 241, 0.5);
            outline-offset: 2px;
        }
        [x-cloak] { 
            display: none !important; 
        }
        #wpadminbar { display: none !important; }
        html { margin-top: 0 !important; }
        body { margin-top: 0 !important; }
    </style>
    <?php wp_head(); ?>
</head>
<body class="h-full bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-white pb-16 md:pb-0" x-data="agencyOverview()" x-init="init()">
    <div class="flex h-full flex-col md:flex-row">
        <!-- Mobile Header -->
        <div class="md:hidden fixed top-0 left-0 right-0 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 z-40 px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png" alt="Agency Hub" class="h-8 w-8">
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-gray-800 dark:text-white leading-tight">Overview</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Agency Hub</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Admin Role Switcher (mobile) -->
                    <div x-show="user?.role === 'admin'" class="relative" x-data="{ showRoleSwitcher: false }">
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
                <a x-show="viewMode === 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('workflow'))); ?>?tab=contentBank" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    <span class="ml-4 nav-text">Content Bank</span>
                </a>
                <!-- Published Posts - Client View Only -->
                <a x-show="viewMode === 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('dashboard'))); ?>#content-calendar" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
                    <span class="ml-4 nav-text">Published Posts</span>
                </a>
                <!-- Page Overview - Client View Only -->
                <a x-show="viewMode === 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('dashboard'))); ?>#overview" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    <span class="ml-4 nav-text">Page Overview</span>
                </a>
                <!-- Content Calendar - Client View Only -->
                <a x-show="viewMode === 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('content-calendar'))); ?>" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span class="ml-4 nav-text">Content Calendar</span>
                </a>
                <!-- ADMIN/BRAND REP VIEW NAVIGATION -->
                <!-- Workflow -->
                <a x-show="viewMode !== 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('workflow'))); ?>" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
                    <span class="ml-4 nav-text">Workflow</span>
                </a>
                <!-- Agency Overview - active on this page -->
                <a x-show="viewMode !== 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('overview'))); ?>" class="flex items-center p-3 rounded-lg bg-indigo-500 text-white transition-colors duration-200">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    <span class="ml-4 nav-text">Agency Overview</span>
                </a>
                <!-- Insights -->
                <a x-show="viewMode !== 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('dashboard'))); ?>" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    <span class="ml-4 nav-text">Insights</span>
                </a>
                <!-- Published Posts -->
                <a x-show="viewMode !== 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('dashboard'))); ?>#content-calendar" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-4.5 12h22.5" /></svg>
                    <span class="ml-4 nav-text">Published Posts</span>
                </a>
                <!-- Content Calendar -->
                <a x-show="viewMode !== 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('content-calendar'))); ?>" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span class="ml-4 nav-text">Content Calendar</span>
                </a>
            </nav>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700/50">
                <!-- Admin Panel - only for actual admins in admin view mode -->
                <a x-show="user?.role === 'admin' && viewMode === 'admin'" href="<?php echo esc_url(get_permalink(get_page_by_path('admin'))); ?>" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 mb-2">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.438.995a6.473 6.473 0 010 .255c0 .382.145.755.438.995l1.003.827c.48.398.668 1.05.26 1.431l-1.296 2.247a1.125 1.125 0 01-1.37.49l-1.217-.456c-.355-.133-.75-.072-1.075.124a6.57 6.57 0 01-.22.127c-.332.183-.582.495-.645.87l-.213 1.281c-.09.543-.56.94-1.11.94h-2.593c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.063-.374-.313-.686-.645-.87a6.52 6.52 0 01-.22-.127c-.324-.196-.72-.257-1.075-.124l-1.217.456a1.125 1.125 0 01-1.37-.49l-1.296-2.247a1.125 1.125 0 01.26-1.431l1.003-.827c.293-.24.438-.613.438-.995a6.473 6.473 0 010-.255c0-.382-.145-.755-.438-.995l-1.003-.827a1.125 1.125 0 01-.26-1.431l1.296-2.247a1.125 1.125 0 011.37-.49l1.217.456c.355.133.75.072 1.075-.124a6.57 6.57 0 01.22-.127c.332-.183.582-.495.645-.87l.213-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <span class="ml-4 nav-text">Admin Panel</span>
                </a>
                <a @click.prevent="logout()" href="#" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                    <span class="ml-4 nav-text">Logout</span>
                </a>
            </div>
        </aside>

        <main class="flex-1 bg-gray-50 dark:bg-gray-900 overflow-y-auto mt-14 md:mt-0">
            <!-- Header -->
            <header class="hidden md:flex items-center justify-between p-4 min-h-16 bg-white/80 dark:bg-gray-900/70 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700/50 sticky top-0 z-10 shadow-sm">
                <div class="flex flex-1 min-w-0 flex-col lg:flex-row lg:items-end gap-2 lg:gap-6 pr-2">
                    <div class="min-w-0">
                        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Agency Performance Overview</h1>
                        <p x-show="periodInfo && periodInfo.label" x-text="periodInfo.label" class="text-sm text-gray-500 dark:text-gray-400 mt-0.5"></p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="flex flex-wrap items-center gap-1.5" x-show="!rangeAll" x-cloak>
                            <label for="overview-month" class="sr-only">Select calendar month</label>
                            <input type="month" id="overview-month" x-model="selectedYearMonth" @change="onMonthRangeChange()"
                                class="ov-focus rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-1.5 text-sm max-w-full" />
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5">
                            <button type="button" @click="setOverviewThisMonth()" class="ov-focus px-2.5 py-1.5 text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800">This month</button>
                            <button type="button" @click="setOverviewLastMonth()" class="ov-focus px-2.5 py-1.5 text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800">Last month</button>
                            <label class="ov-focus inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 cursor-pointer select-none rounded-lg border border-gray-200 dark:border-gray-600 px-2.5 py-1.5" :class="rangeAll ? 'bg-indigo-50 dark:bg-indigo-900/30 border-indigo-200 dark:border-indigo-700' : ''">
                                <input type="checkbox" x-model="rangeAll" @change="onAllTimeChange()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                <span>All time</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500 dark:text-gray-400" x-text="currentDate"></span>
                    <!-- Admin Role Switcher -->
                    <div x-show="user?.role === 'admin'" class="relative" x-data="{ showRoleSwitcher: false }">
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
                                    <select x-model="selectedViewClient" @change="localStorage.setItem('selectedViewClient', selectedViewClient); loadData();" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <template x-for="brand in brands" :key="brand._id">
                                            <option :value="brand._id" x-text="brand.brandName || brand.name"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button @click="toggleTheme()" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Toggle theme">
                        <svg x-show="theme === 'light'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        <svg x-show="theme === 'dark'" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </button>
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2">
                            <img class="h-8 w-8 rounded-full" :src="user?.clientId?.logo || 'https://placehold.co/100x100/4a5568/ffffff?text=' + (user?.firstName?.[0] || 'U')" :alt="user?.fullName">
                            <span class="hidden sm:inline" x-text="user?.fullName"></span>
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.23 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                        </button>
                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-20">
                            <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-sm font-medium" x-text="user?.fullName"></p>
                                <p class="text-xs text-gray-500" x-text="user?.email"></p>
                                <p class="text-xs text-indigo-600 mt-1 capitalize" x-text="user?.role?.replace('_', ' ')"></p>
                            </div>
                            <a @click.prevent="logout()" href="#" class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Logout</a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Loading State -->
            <div x-show="loading" class="flex items-center justify-center min-h-[10rem] py-8">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
            </div>

            <!-- Main Content -->
            <div x-show="!loading" x-cloak class="px-4 sm:px-6 lg:px-8 pt-3 sm:pt-4 pb-4 sm:pb-6 lg:pb-8">
            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 sm:gap-5 mb-4 sm:mb-5">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200/80 dark:border-gray-700/50 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Brands</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" x-text="stats.totalBrands"></p>
                        </div>
                        <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-full">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200/80 dark:border-gray-700/50 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Posts</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" x-text="stats.totalPosts"></p>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200/80 dark:border-gray-700/50 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Avg Posts/Brand</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" x-text="stats.avgPostsPerBrand"></p>
                        </div>
                        <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200/80 dark:border-gray-700/50 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Posts Remaining</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                                <span x-text="stats.totalRemainingPosts"></span>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <span x-text="stats.totalRequiredPosts"></span> target • <span x-text="stats.overallProgress"></span>% complete
                            </p>
                        </div>
                        <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10M4 18h10"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="h-2 rounded-full bg-purple-600 transition-all" :style="`width: ${Math.min(stats.overallProgress || 0, 100)}%`"></div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200/80 dark:border-gray-700/50 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Below Target</p>
                            <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2" x-text="stats.belowTarget"></p>
                        </div>
                        <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="mb-4 flex flex-col sm:flex-row gap-3 sm:items-center sm:space-x-4 sm:gap-0 p-1 rounded-xl border border-gray-200/80 dark:border-gray-700/50 bg-white/50 dark:bg-gray-800/30">
                <div class="flex-1 min-w-0">
                    <label class="sr-only" for="overview-brand-search">Search brands</label>
                    <input 
                        id="overview-brand-search"
                        type="search" 
                        x-model="searchQuery" 
                        @input="filterBrands()"
                        placeholder="Search brands…"
                        autocomplete="off"
                        class="ov-focus w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    >
                </div>
                <label class="sr-only" for="overview-filter-status">Filter by status</label>
                <select id="overview-filter-status" x-model="filterStatus" @change="filterBrands()" class="ov-focus shrink-0 sm:w-48 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="all">All Brands</option>
                    <option value="on-track">On Track</option>
                    <option value="below-target">Below Target</option>
                    <option value="no-posts">No Posts</option>
                </select>
            </div>

            <!-- Brand Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="brand in filteredBrands" :key="brand._id">
                    <div 
                        @click="navigateToBrand(brand._id)"
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200/80 dark:border-gray-700/50 hover:shadow-md transition-shadow cursor-pointer hover:border-indigo-500/60"
                    >
                        <div class="p-6">
                            <!-- Brand Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="brand.name"></h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400" x-text="brand.industry"></p>
                                </div>
                                <span 
                                    :class="{
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': brand.status === 'on-track',
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': brand.status === 'below-target',
                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': brand.status === 'no-posts'
                                    }"
                                    class="px-2 py-1 text-xs font-medium rounded-full"
                                    x-text="brand.statusLabel"
                                ></span>
                            </div>

                            <!-- Post Count Progress -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Monthly Posts</span>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                                        <span x-text="brand.postsThisMonth"></span> / <span x-text="brand.requiredPosts"></span>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Remaining</span>
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300" x-text="brand.remainingPosts"></span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div 
                                        :class="{
                                            'bg-green-500': brand.progress >= 100,
                                            'bg-yellow-500': brand.progress >= 50 && brand.progress < 100,
                                            'bg-red-500': brand.progress < 50
                                        }"
                                        class="h-2 rounded-full transition-all"
                                        :style="`width: ${Math.min(brand.progress, 100)}%`"
                                    ></div>
                                </div>
                            </div>

                            <!-- Platform Breakdown -->
                            <div class="flex items-center space-x-4 text-sm">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                    <span class="text-gray-600 dark:text-gray-400" x-text="brand.platforms.facebook"></span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-pink-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                    </svg>
                                    <span class="text-gray-600 dark:text-gray-400" x-text="brand.platforms.instagram"></span>
                                </div>
                            </div>

                            <!-- Click to view -->
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <span class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">Click to view dashboard →</span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Empty State -->
            <div x-show="!loading && filteredBrands.length === 0" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No brands found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filter.</p>
            </div>
            </div>
        </main>
    </div>

    <script>
        const API_URL = typeof ESIROM_API_URL !== 'undefined' ? ESIROM_API_URL : 'https://esirom-hub-backend-production.up.railway.app/api';
        const DASHBOARD_URL = '<?php echo esc_url(get_permalink(get_page_by_path('dashboard'))); ?>';
        const LOGIN_URL = '<?php echo esc_url(get_permalink(get_page_by_path('login'))); ?>';
        const OVERVIEW_PAGE_PATH = <?php echo wp_json_encode($esirom_overview_page_path, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;

        function agencyOverview() {
            return {
                loading: true,
                user: {},
                viewMode: localStorage.getItem('viewMode') || 'admin',
                selectedViewClient: localStorage.getItem('selectedViewClient') || null,
                theme: localStorage.getItem('theme') || 'light',
                isSidebarOpen: true,
                toasts: [],
                currentDate: '',
                selectedYearMonth: '',
                rangeAll: false,
                periodInfo: { label: '' },
                searchQuery: '',
                filterStatus: 'all',
                brands: [],
                filteredBrands: [],
                stats: {
                    totalBrands: 0,
                    totalPosts: 0,
                    avgPostsPerBrand: 0,
                    belowTarget: 0,
                    totalRequiredPosts: 0,
                    totalRemainingPosts: 0,
                    overallProgress: 0
                },

                showToast(message, type = 'info', duration = 3000) {
                    const id = Date.now();
                    this.toasts.push({ id, message, type });
                    setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, duration);
                },

                defaultYearMonth() {
                    const d = new Date();
                    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
                },

                async init() {
                    await this.checkAuth();
                    this.applyTheme();
                    this.setCurrentDate();
                    if (!this.selectedYearMonth) this.selectedYearMonth = this.defaultYearMonth();
                    this.syncPeriodFromUrl();
                    window.addEventListener('popstate', () => {
                        this.syncPeriodFromUrl();
                        this.loadData();
                    });
                    await this.loadData();
                    this.replaceOverviewUrl();
                },

                async checkAuth() {
                    const token = localStorage.getItem('token');
                    if (!token) {
                        window.location.href = LOGIN_URL;
                        return;
                    }
                    try {
                        const res = await fetch(`${API_URL}/auth/me`, {
                            headers: { 'Authorization': `Bearer ${token}` }
                        });
                        if (!res.ok) throw new Error('Auth failed');
                        const data = await res.json();
                        this.user = data.user || {};
                        if (this.user && Object.keys(this.user).length) {
                            localStorage.setItem('user', JSON.stringify(this.user));
                        }
                    } catch (e) {
                        localStorage.removeItem('token');
                        localStorage.removeItem('user');
                        window.location.href = LOGIN_URL;
                        return;
                    }
                    if (this.user?.role !== 'admin' && this.user?.role !== 'brand_rep') {
                        window.location.href = DASHBOARD_URL;
                    }
                },

                setCurrentDate() {
                    try {
                        const now = new Date();
                        this.currentDate = now.toLocaleDateString('en-US', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                    } catch (e) {
                        this.currentDate = '';
                    }
                },

                applyTheme() {
                    localStorage.setItem('theme', this.theme);
                    if (this.theme === 'dark') {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                },

                switchViewMode(mode) {
                    this.viewMode = mode;
                    localStorage.setItem('viewMode', mode);
                    window.dispatchEvent(new CustomEvent('viewModeChanged', { detail: { viewMode: mode } }));
                    
                    // For client view, select first client if not already selected
                    if (mode === 'client' && !this.selectedViewClient && this.brands.length > 0) {
                        this.selectedViewClient = this.brands[0]._id;
                        localStorage.setItem('selectedViewClient', this.selectedViewClient);
                    } else if (mode !== 'client') {
                        this.selectedViewClient = null;
                        localStorage.removeItem('selectedViewClient');
                    }
                    
                    this.loadData();
                },

                toggleTheme() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    this.applyTheme();
                },

                syncPeriodFromUrl() {
                    try {
                        const sp = new URLSearchParams(window.location.search);
                        if (sp.get('range') === 'all' || sp.get('period') === 'all') {
                            this.rangeAll = true;
                            if (!this.selectedYearMonth) this.selectedYearMonth = this.defaultYearMonth();
                            return;
                        }
                        this.rangeAll = false;
                        const m = sp.get('month');
                        if (m && /^\d{4}-\d{2}$/.test(m)) {
                            this.selectedYearMonth = m;
                            return;
                        }
                        const p = sp.get('period');
                        if (p === 'last') {
                            const d = new Date();
                            d.setMonth(d.getMonth() - 1);
                            this.selectedYearMonth = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
                        } else {
                            this.selectedYearMonth = this.defaultYearMonth();
                        }
                    } catch (e) { /* ignore */ }
                },

                replaceOverviewUrl() {
                    const base = (typeof OVERVIEW_PAGE_PATH !== 'undefined' && OVERVIEW_PAGE_PATH)
                        ? String(OVERVIEW_PAGE_PATH).replace(/\/$/, '')
                        : '/overview';
                    const u = new URL(base, window.location.origin);
                    u.search = '';
                    if (this.rangeAll) {
                        u.searchParams.set('range', 'all');
                    } else {
                        u.searchParams.set('month', this.selectedYearMonth || this.defaultYearMonth());
                    }
                    history.replaceState({}, '', u.pathname + u.search);
                },

                onMonthRangeChange() {
                    this.rangeAll = false;
                    this.replaceOverviewUrl();
                    this.loadData();
                },

                onAllTimeChange() {
                    this.replaceOverviewUrl();
                    this.loadData();
                },

                setOverviewThisMonth() {
                    this.rangeAll = false;
                    this.selectedYearMonth = this.defaultYearMonth();
                    this.replaceOverviewUrl();
                    this.loadData();
                },

                setOverviewLastMonth() {
                    this.rangeAll = false;
                    const d = new Date();
                    d.setMonth(d.getMonth() - 1);
                    this.selectedYearMonth = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
                    this.replaceOverviewUrl();
                    this.loadData();
                },

                async loadData() {
                    this.loading = true;
                    try {
                        const token = localStorage.getItem('token');
                        const q = new URLSearchParams();
                        if (this.rangeAll) {
                            q.set('range', 'all');
                        } else {
                            q.set('month', this.selectedYearMonth || this.defaultYearMonth());
                        }
                        const response = await fetch(`${API_URL}/analytics/agency-overview?${q}`, {
                            headers: {
                                'Authorization': `Bearer ${token}`
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Failed to load data');
                        }

                        const data = await response.json();
                        this.brands = Array.isArray(data.brands) ? data.brands : [];
                        this.stats = {
                            totalBrands: 0,
                            totalPosts: 0,
                            avgPostsPerBrand: 0,
                            belowTarget: 0,
                            totalRequiredPosts: 0,
                            totalRemainingPosts: 0,
                            overallProgress: 0,
                            ...((data && data.stats) || {})
                        };
                        this.periodInfo = (data && data.period) ? data.period : { label: '' };
                        this.filterBrands();
                    } catch (error) {
                        console.error('Error loading data:', error);
                        this.showToast('Failed to load agency overview data', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                filterBrands() {
                    const list = Array.isArray(this.brands) ? this.brands : [];
                    let filtered = list;

                    // Search filter
                    if (this.searchQuery) {
                        const query = this.searchQuery.toLowerCase();
                        filtered = filtered.filter((brand) => {
                            const n = (brand && brand.name) ? String(brand.name) : '';
                            const ind = (brand && brand.industry) ? String(brand.industry) : '';
                            return n.toLowerCase().includes(query) || ind.toLowerCase().includes(query);
                        });
                    }

                    // Status filter
                    if (this.filterStatus !== 'all') {
                        filtered = filtered.filter(brand => brand.status === this.filterStatus);
                    }

                    this.filteredBrands = filtered;
                },

                navigateToBrand(brandId) {
                    localStorage.setItem('selectedClientId', brandId);
                    window.location.href = DASHBOARD_URL;
                },

                logout() {
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    window.location.href = LOGIN_URL;
                }
            }
        }
    </script>

    <!-- Mobile Bottom Navigation -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 z-40" x-data="{ viewMode: localStorage.getItem('viewMode') || 'admin', user: (() => { try { return JSON.parse(localStorage.getItem('user') || '{}') || {}; } catch (e) { return {}; } })(), init() { window.addEventListener('viewModeChanged', (e) => { this.viewMode = e?.detail?.viewMode || (localStorage.getItem('viewMode') || 'admin'); }); } }">
        <div class="flex justify-around items-center h-16">
            <!-- Overview - current page, hidden in client view -->
            <a x-show="viewMode !== 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('overview'))); ?>" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-indigo-600 dark:text-indigo-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="text-xs font-medium">Overview</span>
            </a>
            <a href="<?php echo esc_url(get_permalink(get_page_by_path('dashboard'))); ?>" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 dark:text-gray-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-xs">Dashboard</span>
            </a>
            <a href="<?php echo esc_url(get_permalink(get_page_by_path('dashboard'))); ?>#content-calendar" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 dark:text-gray-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-xs">Calendar</span>
            </a>
            <!-- Workflow - for admin and brand rep views -->
            <a x-show="viewMode !== 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('workflow'))); ?>" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 dark:text-gray-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-xs">Workflow</span>
            </a>
            <!-- Content Bank - for client view only -->
            <a x-show="viewMode === 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('workflow'))); ?>?tab=contentBank" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 dark:text-gray-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span class="text-xs">Content Bank</span>
            </a>
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
            <div x-show="true" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-full" x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="w-full shadow-lg rounded-lg pointer-events-auto overflow-hidden" :class="{'bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-700': toast.type === 'success', 'bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700': toast.type === 'error', 'bg-blue-50 dark:bg-blue-900/50 border border-blue-200 dark:border-blue-700': toast.type === 'info'}">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg x-show="toast.type === 'success'" class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <svg x-show="toast.type === 'error'" class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <svg x-show="toast.type === 'info'" class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium" :class="{'text-green-800 dark:text-green-200': toast.type === 'success', 'text-red-800 dark:text-red-200': toast.type === 'error', 'text-blue-800 dark:text-blue-200': toast.type === 'info'}" x-text="toast.message"></p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</body>
</html>
