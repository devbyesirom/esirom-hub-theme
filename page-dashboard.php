<?php
/**
 * Template Name: Dashboard Page
 * Template Post Type: page
 *
 * @package Esirom_Client_Hub
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Hide WordPress admin bar on this page
show_admin_bar(false);

// Get dashboard page slug for redirect
$dashboard_page = get_page_by_path('dashboard'); // Change 'dashboard' to your actual dashboard page slug
$dashboard_url = $dashboard_page ? get_permalink($dashboard_page->ID) : home_url('/');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="h-full">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#4F46E5">
    <meta name="description" content="Social media management platform for agencies and clients">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ESIROM Hub">
    <title><?php wp_title('|', true, 'right'); ?> <?php bloginfo('name'); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png">
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/manifest.json">
    
    <!-- iOS Icons -->
    <link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script>
        // Set theme immediately to avoid FOUC (Flash of Unstyled Content)
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
        [x-cloak] { 
            display: none !important; 
        }
        /* Hide any WordPress admin elements */
        #wpadminbar { display: none !important; }
        html { margin-top: 0 !important; }
        body { margin-top: 0 !important; }
        /* Hide any stray checkboxes in top-left (aggressive) */
        body > input[type="checkbox"] { display: none !important; }
        body > label > input[type="checkbox"] { display: none !important; }
        body input[type="checkbox"][style*="position: fixed"] { display: none !important; }
        body input[type="checkbox"][style*="position: absolute"] { display: none !important; }
        input[type="checkbox"]:not(.w-5):not(.rounded) { display: none !important; }
        body::before { display: none !important; }
        /* Target the specific stray checkbox by position */
        aside + input[type="checkbox"] { display: none !important; }
        .sidebar + input[type="checkbox"] { display: none !important; }
        
        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            /* Touch-friendly buttons */
            button, a {
                min-height: 44px;
                min-width: 44px;
            }
            
            /* Larger text on mobile */
            .text-sm { font-size: 0.9375rem; }
            .text-xs { font-size: 0.8125rem; }
            
            /* Responsive grid */
            .grid-cols-2, .grid-cols-3, .grid-cols-4 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
            
            /* Responsive padding */
            .p-6 { padding: 1rem; }
            .p-8 { padding: 1.5rem; }
            
            /* Full width modals on mobile */
            .max-w-2xl, .max-w-3xl, .max-w-4xl, .max-w-5xl {
                max-width: 100%;
                margin: 0;
                border-radius: 0;
            }
            
            /* Scrollable tables */
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
        
        /* Tablet breakpoint */
        @media (min-width: 768px) and (max-width: 1024px) {
            .grid-cols-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        <?php esirom_hub_layout_styles(); ?>
    </style>
    <?php wp_head(); ?>
    <script>
        // Remove any stray checkboxes on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Remove WordPress admin bar
            const adminBar = document.getElementById('wpadminbar');
            if (adminBar) adminBar.remove();
            
            // Remove any top-level checkboxes
            const topCheckboxes = document.querySelectorAll('body > input[type="checkbox"]');
            topCheckboxes.forEach(cb => cb.remove());
            
            // Remove any checkboxes before main content
            const firstDiv = document.querySelector('body > div');
            if (firstDiv) {
                const prevElements = [];
                let prev = firstDiv.previousElementSibling;
                while (prev) {
                    prevElements.push(prev);
                    prev = prev.previousElementSibling;
                }
                prevElements.forEach(el => {
                    if (el.tagName === 'INPUT' || el.querySelector('input[type="checkbox"]')) {
                        el.remove();
                    }
                });
            }
        });
    </script>
</head>
<body class="hub-has-mobile-nav h-full bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-white pb-16 md:pb-0" x-data="clientHub">
    <div class="flex h-full flex-col md:flex-row">
        <!-- Mobile Header -->
        <div class="md:hidden fixed top-0 left-0 right-0 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 z-40 px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png" alt="Agency Hub" class="h-8 w-8">
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-gray-800 dark:text-white leading-tight">Agency Hub</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">by esirom</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
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
                                    <select x-model="selectedViewClient" @change="handleSelectedViewClientChange()" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <option value="">All Brands</option>
                                        <template x-for="client in availableClients" :key="client._id">
                                            <option :value="client._id" x-text="client.brandName || client.name"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Theme Toggle -->
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

        <!-- Desktop Sidebar -->
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
                <a x-show="viewMode === 'client'" href="#content-calendar" @click="activeView = 'calendar'" class="flex items-center p-3 rounded-lg transition-colors duration-200" :class="activeView === 'calendar' ? 'bg-indigo-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
                    <span class="ml-4 nav-text">Published Posts</span>
                </a>
                <!-- Page Overview - Client View Only (renamed from Agency Overview) -->
                <a x-show="viewMode === 'client'" href="#overview" @click="activeView = 'dashboard'" class="flex items-center p-3 rounded-lg transition-colors duration-200" :class="activeView === 'dashboard' ? 'bg-indigo-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    <span class="ml-4 nav-text">Page Overview</span>
                </a>
                <a x-show="viewMode === 'client'" href="#reports" @click="activeView = 'reports'" :class="activeView === 'reports' ? 'bg-indigo-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="flex items-center p-3 rounded-lg transition-colors duration-200">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <span class="ml-4 nav-text">Reports</span>
                </a>
                <a x-show="viewMode === 'client'" href="#event-coverage" @click="activeView = 'eventCoverage'" :class="activeView === 'eventCoverage' ? 'bg-indigo-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="flex items-center p-3 rounded-lg transition-colors duration-200">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h7.5m-7.5 3h4.5m-6 7.5h10.5A2.25 2.25 0 0019.5 15V6A2.25 2.25 0 0017.25 3.75H6.75A2.25 2.25 0 004.5 6v9a2.25 2.25 0 002.25 2.25zm0 0v2.25m10.5-2.25v2.25" /></svg>
                    <span class="ml-4 nav-text">Event Coverage</span>
                </a>
                <!-- Content Calendar - Client View Only -->
                <a x-show="viewMode === 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('content-calendar'))); ?>" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                    <span class="ml-4 nav-text">Content Calendar</span>
                </a>
                <?php esirom_hub_staff_sidebar_nav('', 'dashboard', false); ?>
            </nav>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700/50">
                <?php esirom_hub_staff_sidebar_footer('dashboard', false); ?>
                <a @click.prevent="logout()" href="#" class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                    <span class="ml-4 nav-text">Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main content -->
        <main class="flex-1 bg-gray-50 dark:bg-gray-900 overflow-y-auto mt-14 md:mt-0">
            <header class="hidden md:flex items-center justify-between p-4 h-16 bg-white/80 dark:bg-gray-900/70 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700/50 sticky top-0 z-10 shadow-sm">
                <div class="flex items-center space-x-4">
                    <h1 class="text-xl font-semibold" x-text="pageTitle"></h1>
                    <!-- Client Selector (for admins and brand reps) -->
                    <div x-show="user.role === 'admin' || user.role === 'brand_rep'" x-data="{ clientDropdownOpen: false }" class="relative">
                        <button @click="clientDropdownOpen = !clientDropdownOpen" class="flex items-center space-x-2 px-3 py-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300" x-text="selectedClient?.brandName || selectedClient?.companyName || 'Select Client'"></span>
                            <svg class="h-4 w-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="clientDropdownOpen" @click.away="clientDropdownOpen = false" x-cloak class="absolute left-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-20 max-h-96 overflow-y-auto">
                            <template x-for="client in availableClients" :key="client._id">
                                <button @click="switchClient(client); clientDropdownOpen = false" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2">
                                    <img :src="client.logo || 'https://placehold.co/40x40/4a5568/ffffff?text=' + (client.brandName || client.companyName || client.name || 'C').charAt(0)" class="h-8 w-8 rounded-full" :alt="client.brandName || client.companyName || client.name">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white" x-text="client.brandName || client.companyName || client.name"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="client.industry || 'N/A'"></p>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                    <!-- Client multi-brand scope (for client users with multiple brands) -->
                    <div x-show="user.role === 'client' && availableClients.length > 1" class="flex items-center gap-2">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Brand Scope:</span>
                        <button @click="setClientBrandScope('')"
                                :class="!selectedViewClient ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                                class="px-2.5 py-1 rounded-full text-xs font-medium transition-colors">
                            All Brands
                        </button>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600">
                                Choose Brand
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak class="absolute left-0 mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-30 max-h-72 overflow-y-auto">
                                <template x-for="client in availableClients" :key="`client-scope-${client._id}`">
                                    <button @click="setClientBrandScope(client._id); open = false"
                                            :class="selectedViewClient === client._id ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                            class="w-full text-left px-3 py-2 text-sm">
                                        <span x-text="client.brandName || client.companyName || client.name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                     <span class="text-sm text-gray-500 dark:text-gray-400" x-text="currentDate"></span>
                    <button x-show="user.role === 'admin' || user.role === 'brand_rep'" @click="showUploadInsightsModal = true" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <span class="hidden sm:inline">Upload Insights</span>
                    </button>
                    <button @click="toggleTheme()" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Toggle theme">
                        <svg x-show="theme === 'light'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        <svg x-show="theme === 'dark'" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </button>
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <img class="h-7 w-7 rounded-full object-cover" :src="user.clientId?.logo || 'https://placehold.co/100x100/4a5568/ffffff?text=' + (user.firstName?.[0] || 'U')" :alt="user.fullName">
                            <span class="hidden sm:inline text-sm font-medium text-gray-700 dark:text-gray-300" x-text="user.clientId?.brandName || user.clientId?.companyName || user.fullName"></span>
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-cloak class="absolute right-0 mt-2 w-52 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 py-1 z-20">
                            <div class="px-4 py-2.5 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="user.fullName"></p>
                                <p class="text-xs text-gray-500 mt-0.5" x-text="user.email"></p>
                                <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-0.5 capitalize font-medium" x-text="user.role?.replace('_', ' ')"></p>
                            </div>
                            <a @click.prevent="showPwModal = true; dropdownOpen = false" href="#" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Change Password
                            </a>
                            <a @click.prevent="logout(); dropdownOpen = false" href="#" class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-b-2xl transition-colors">Logout</a>
                        </div>
                    </div>
                </div>
            </header>

            <div class="px-4 sm:px-6 lg:px-8 pt-3 sm:pt-4 pb-4 sm:pb-6 lg:pb-8">
                <!-- Dashboard View -->
                <div x-show="activeView === 'dashboard'" x-cloak>
                    <!-- Loading State -->
                    <div x-show="loading" class="flex items-center justify-center py-12">
                        <svg class="animate-spin h-12 w-12 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    <!-- Dashboard Content -->
                    <div x-show="!loading">
                        <!-- Pending Approvals Alert (for clients) -->
                        <div x-show="user.role === 'client' && dashboardData.pendingPosts?.count > 0" class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                        You have <span x-text="dashboardData.pendingPosts?.count || 0"></span> post(s) pending your approval
                                    </p>
                                </div>
                                <button @click="activeView = 'calendar'" class="ml-4 text-sm font-medium text-yellow-800 dark:text-yellow-200 hover:text-yellow-900 dark:hover:text-yellow-100">
                                    Review →
                                </button>
                            </div>
                        </div>

                        <!-- Recent reports (saved / PDF) — #reports scrolls here -->
                        <div id="client-reports-section" class="mb-6 bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between gap-3 mb-4">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Reports
                                </h3>
                                <button
                                    x-show="viewMode === 'client' || user.role === 'client'"
                                    @click="generateOverviewReport()"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed"
                                    :disabled="generatingReport">
                                    <span x-show="!generatingReport">Generate From Overview</span>
                                    <span x-show="generatingReport">Generating...</span>
                                </button>
                            </div>
                            <div x-show="!dashboardData.recentReports || dashboardData.recentReports.length === 0" class="text-sm text-gray-500 dark:text-gray-400">
                                <p class="mb-1">No saved reports yet. When you generate or save a report (including PDF export), it will list here with the date range and status.</p>
                            </div>
                            <ul x-show="dashboardData.recentReports && dashboardData.recentReports.length > 0" class="divide-y divide-gray-200 dark:divide-gray-600">
                                <template x-for="report in (dashboardData.recentReports || [])" :key="report._id">
                                    <li class="py-3 first:pt-0 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white" x-text="report.name"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="formatDateRange(report.dateRange)"></p>
                                        </div>
                                        <div class="flex items-center flex-wrap gap-2">
                                            <span class="text-xs font-medium px-2 py-0.5 rounded capitalize"
                                                :class="report.status === 'finalized' || report.status === 'sent' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200'"
                                                x-text="report.status || 'draft'"></span>
                                            <button @click.prevent="openReport(report)" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">View</button>
                                            <button @click.prevent="downloadReport(report)" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Download PDF</button>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <!-- Monthly Insights Summary -->
                        <div x-show="dashboardData.insights" class="mb-6 bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-lg p-6 shadow-sm border border-indigo-100 dark:border-indigo-800">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                                    <svg class="w-6 h-6 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                    Monthly Insights
                                </h3>
                                <span class="text-sm text-gray-600 dark:text-gray-400" x-text="dashboardData.insights?.month || 'Current Month'"></span>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <!-- Key Findings -->
                                <div class="bg-white dark:bg-gray-800/50 rounded-lg p-4">
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Key Findings
                                    </h4>
                                    <ul class="space-y-2">
                                        <template x-for="finding in dashboardData.insights?.keyFindings" :key="finding">
                                            <li class="text-sm text-gray-700 dark:text-gray-300 flex items-start">
                                                <span class="text-indigo-600 dark:text-indigo-400 mr-2">•</span>
                                                <span x-text="finding"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>

                                <!-- Progress vs Last Month -->
                                <div class="bg-white dark:bg-gray-800/50 rounded-lg p-4">
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                        Progress vs Last Month
                                    </h4>
                                    <div class="space-y-3">
                                        <div x-show="dashboardData.insights?.progressVsLastMonth?.improved?.length > 0">
                                            <p class="text-xs font-medium text-green-600 dark:text-green-400 mb-1">↑ Improved</p>
                                            <template x-for="item in dashboardData.insights?.progressVsLastMonth?.improved" :key="item">
                                                <span class="inline-block text-xs bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 px-2 py-1 rounded mr-1 mb-1" x-text="item"></span>
                                            </template>
                                        </div>
                                        <div x-show="dashboardData.insights?.progressVsLastMonth?.declined?.length > 0">
                                            <p class="text-xs font-medium text-red-600 dark:text-red-400 mb-1">↓ Declined</p>
                                            <template x-for="item in dashboardData.insights?.progressVsLastMonth?.declined" :key="item">
                                                <span class="inline-block text-xs bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 px-2 py-1 rounded mr-1 mb-1" x-text="item"></span>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Top Performing Content -->
                                <div class="bg-white dark:bg-gray-800/50 rounded-lg p-4">
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                                        Top Content
                                    </h4>
                                    <div class="space-y-2">
                                        <template x-for="(content, index) in dashboardData.insights?.topContent?.slice(0, 3)" :key="content.id">
                                            <div class="text-sm">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-medium text-gray-900 dark:text-white" x-text="(index + 1) + '. ' + (content.title || content.type)"></span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400" x-text="content.platform"></span>
                                                </div>
                                                <p class="text-xs text-gray-600 dark:text-gray-400" x-text="formatNumber(content.engagement) + ' engagements'"></p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Date Range & Platform Filters -->
                        <div class="mb-6 bg-white dark:bg-gray-800/50 rounded-lg p-4 shadow-sm">
                            <!-- Date Range Filter -->
                            <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Date Range</h3>
                                    <div x-show="getTotalFollowers() > 0" class="flex items-center gap-3">
                                        <template x-for="platform in Object.keys(platformFollowers).filter(p => platformFollowers[p] > 0)" :key="platform">
                                            <div class="flex items-center gap-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg px-4 py-2 shadow-sm">
                                                <svg x-show="platform === 'instagram'" class="w-5 h-5 text-pink-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                                <svg x-show="platform === 'facebook'" class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                                <svg x-show="platform === 'youtube'" class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                                <svg x-show="platform === 'linkedin'" class="w-5 h-5 text-blue-700" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                                <svg x-show="platform === 'x' || platform === 'twitter'" class="w-5 h-5 text-black dark:text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                                <div class="flex flex-col">
                                                    <span class="text-base font-bold text-gray-900 dark:text-white leading-tight" x-text="formatNumber(platformFollowers[platform])"></span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 capitalize" x-text="platform + ' followers'"></span>
                                                </div>
                                            </div>
                                        </template>
                                        <div x-show="Object.keys(platformFollowers).filter(p => platformFollowers[p] > 0).length > 1" class="flex items-center gap-2 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700 rounded-lg px-4 py-2 shadow-sm">
                                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                                            <div class="flex flex-col">
                                                <span class="text-base font-bold text-indigo-700 dark:text-indigo-300 leading-tight" x-text="formatNumber(getTotalFollowers())"></span>
                                                <span class="text-xs text-indigo-500 dark:text-indigo-400">Total followers</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <button @click="setDateRange('ytd')" :class="dateRange === 'ytd' ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all">
                                        Year to Date
                                    </button>
                                    <button @click="setDateRange('current_month')" :class="dateRange === 'current_month' ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all">
                                        Current Month
                                    </button>
                                    <button @click="setDateRange('last_month')" :class="dateRange === 'last_month' ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all">
                                        Last Month
                                    </button>
                                    <button @click="setDateRange('last_3_months')" :class="dateRange === 'last_3_months' ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all">
                                        Last 3 Months
                                    </button>
                                    <button @click="setDateRange('last_year')" :class="dateRange === 'last_year' ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all">
                                        Last Year
                                    </button>
                                    <select @change="setDateRange('full_year_' + $event.target.value)" :class="dateRange.startsWith('full_year_') ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all cursor-pointer appearance-none pr-8" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 20 20%22 fill=%22currentColor%22%3E%3Cpath fill-rule=%22evenodd%22 d=%22M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z%22 clip-rule=%22evenodd%22/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1em;">
                                        <option value="" disabled selected>Select Year</option>
                                        <option :value="new Date().getFullYear()" x-text="new Date().getFullYear()"></option>
                                        <option :value="new Date().getFullYear() - 1" x-text="new Date().getFullYear() - 1"></option>
                                        <option :value="new Date().getFullYear() - 2" x-text="new Date().getFullYear() - 2"></option>
                                    </select>
                                    <button @click="showCustomDatePicker = !showCustomDatePicker" :class="dateRange === 'custom' ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        Custom
                                    </button>
                                </div>
                                
                                <!-- Custom Date Picker -->
                                <div x-show="showCustomDatePicker" x-collapse class="mt-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                                            <input type="date" x-model="customStartDate" @change="applyCustomDateRange()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-800 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                                            <input type="date" x-model="customEndDate" @change="applyCustomDateRange()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-800 dark:text-white">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Current Period Display -->
                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span x-text="currentPeriodLabel"></span>
                                </div>
                            </div>
                            
                            <!-- Platform Filter -->
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Filter by Platform</h3>
                                <button @click="showCustomizeWidgets = !showCustomizeWidgets" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium transition-all" :class="showCustomizeWidgets ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                                    <span>Customize Widgets</span>
                                </button>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button x-show="clientPlatforms.includes('facebook')" @click="togglePlatformFilter('facebook')" :class="activePlatforms.length === clientPlatforms.length ? 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400' : (activePlatforms.includes('facebook') ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500')" class="px-3 py-1 rounded-full text-sm font-medium hover:opacity-80 transition-all">
                                    Facebook
                                </button>
                                <button x-show="clientPlatforms.includes('instagram')" @click="togglePlatformFilter('instagram')" :class="activePlatforms.length === clientPlatforms.length ? 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400' : (activePlatforms.includes('instagram') ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500')" class="px-3 py-1 rounded-full text-sm font-medium hover:opacity-80 transition-all">
                                    Instagram
                                </button>
                                <button x-show="clientPlatforms.includes('linkedin')" @click="togglePlatformFilter('linkedin')" :class="activePlatforms.length === clientPlatforms.length ? 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400' : (activePlatforms.includes('linkedin') ? 'bg-blue-700 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500')" class="px-3 py-1 rounded-full text-sm font-medium hover:opacity-80 transition-all">
                                    LinkedIn
                                </button>
                                <button x-show="clientPlatforms.includes('youtube')" @click="togglePlatformFilter('youtube')" :class="activePlatforms.length === clientPlatforms.length ? 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400' : (activePlatforms.includes('youtube') ? 'bg-red-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500')" class="px-3 py-1 rounded-full text-sm font-medium hover:opacity-80 transition-all">
                                    YouTube
                                </button>
                                <button x-show="clientPlatforms.includes('x')" @click="togglePlatformFilter('x')" :class="activePlatforms.length === clientPlatforms.length ? 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400' : (activePlatforms.includes('x') ? 'bg-black dark:bg-white text-white dark:text-black' : 'bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500')" class="px-3 py-1 rounded-full text-sm font-medium hover:opacity-80 transition-all">
                                    X (Twitter)
                                </button>
                                <button x-show="clientPlatforms.includes('tiktok')" @click="togglePlatformFilter('tiktok')" :class="activePlatforms.length === clientPlatforms.length ? 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400' : (activePlatforms.includes('tiktok') ? 'bg-black text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500')" class="px-3 py-1 rounded-full text-sm font-medium hover:opacity-80 transition-all">
                                    TikTok
                                </button>
                                <button @click="activePlatforms = [...clientPlatforms]; updateDashboardMetrics()" :class="activePlatforms.length === clientPlatforms.length ? 'bg-indigo-700 ring-2 ring-indigo-300' : 'bg-indigo-600 hover:bg-indigo-700'" class="px-3 py-1 rounded-full text-sm font-medium text-white transition-all">
                                    All Platforms
                                </button>
                            </div>
                            
                            <!-- Widget Customization Panel -->
                            <div x-show="showCustomizeWidgets" x-collapse class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Show/Hide Dashboard Widgets</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Toggle which metrics you want to see on your dashboard</p>
                                <div class="grid grid-cols-2 gap-2">
                                    <!-- Only show widgets that are enabled by admin -->
                                    <template x-for="widget in getAvailableWidgets()" :key="widget.id">
                                        <label class="flex items-center space-x-2 p-2 rounded-lg cursor-pointer transition-colors" :class="userWidgetPreferences.includes(widget.id) ? 'bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800' : 'bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700'">
                                            <input type="checkbox" :checked="userWidgetPreferences.includes(widget.id)" @change="toggleWidgetPreference(widget.id)" class="rounded text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300" x-text="widget.name"></span>
                                        </label>
                                    </template>
                                </div>
                                <div class="mt-3 flex justify-end gap-2">
                                    <button @click="resetWidgetPreferences()" class="text-xs px-3 py-1.5 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600">
                                        Reset to Default
                                    </button>
                                    <button @click="showCustomizeWidgets = false" class="text-xs px-3 py-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                                        Done
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- KPI Goals Section (TOP OF DASHBOARD) -->
                        <div x-show="dashboardData.kpis && dashboardData.kpis.length > 0" class="mb-6 bg-white dark:bg-gray-800/50 rounded-lg p-6 shadow-sm">
                            <h3 class="text-xl font-bold mb-5 text-gray-900 dark:text-white">Annual Goals Progress</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <template x-for="(kpi, index) in dashboardData.kpis" :key="index">
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex items-start space-x-3">
                                                <!-- Platform Logo -->
                                                <div class="flex-shrink-0">
                                                    <div x-show="kpi.platform === 'facebook'" class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                                    </div>
                                                    <div x-show="kpi.platform === 'instagram'" class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-600 via-pink-600 to-orange-500 flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                                    </div>
                                                    <div x-show="kpi.platform === 'linkedin'" class="w-10 h-10 rounded-full bg-blue-700 flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                                    </div>
                                                    <div x-show="kpi.platform === 'youtube'" class="w-10 h-10 rounded-full bg-red-600 flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                                    </div>
                                                    <div x-show="kpi.platform === 'x'" class="w-10 h-10 rounded-full bg-black dark:bg-white flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-white dark:text-black" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                                    </div>
                                                    <div x-show="kpi.platform === 'tiktok'" class="w-10 h-10 rounded-full bg-black flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                                                    </div>
                                                    <div x-show="!kpi.platform || kpi.platform === 'other'" class="w-10 h-10 rounded-full bg-gray-500 flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h4 class="font-semibold text-gray-900 dark:text-white" x-text="kpi.name"></h4>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="kpi.platform ? kpi.platform.charAt(0).toUpperCase() + kpi.platform.slice(1) : 'General'"></p>
                                                </div>
                                            </div>
                                            <span class="text-lg font-bold" :class="(kpi.progress || 0) >= 100 ? 'text-green-600 dark:text-green-400' : (kpi.progress || 0) >= 75 ? 'text-blue-600 dark:text-blue-400' : (kpi.progress || 0) >= 50 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400'" x-text="(kpi.progress || 0).toFixed(0) + '%'"></span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 mb-2">
                                            <div class="h-3 rounded-full transition-all" :class="(kpi.progress || 0) >= 100 ? 'bg-green-600' : (kpi.progress || 0) >= 75 ? 'bg-blue-600' : (kpi.progress || 0) >= 50 ? 'bg-yellow-600' : 'bg-red-600'" :style="`width: ${Math.min(100, kpi.progress || 0)}%`"></div>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">
                                                <span class="font-semibold text-gray-900 dark:text-white" x-text="kpi.currentValue"></span>
                                                <span x-text="kpi.unit === 'percentage' ? '%' : ''"></span>
                                            </span>
                                            <span class="text-gray-500 dark:text-gray-400">
                                                Target: <span x-text="kpi.targetValue"></span><span x-text="kpi.unit === 'percentage' ? '%' : ''"></span>
                                            </span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Metric Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div x-show="isWidgetVisible('reach')" class="bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Reach</h3>
                                    <svg class="h-6 w-6 text-teal-500 dark:text-teal-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" /></svg>
                                </div>
                                <p class="text-3xl font-semibold mt-2" x-text="formatNumber(dashboardData.metrics?.reach?.current || 0)"></p>
                                <p class="text-sm flex items-center mt-1" :class="(dashboardData.metrics?.reach?.change || 0) >= 0 ? 'text-green-500 dark:text-green-400' : 'text-red-500 dark:text-red-400'">
                                    <span x-text="formatChange(dashboardData.metrics?.reach?.change || 0)"></span>
                                </p>
                            </div>
                            <div x-show="isWidgetVisible('engagement_rate')" class="bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Engagement Rate</h3>
                                    <svg class="h-6 w-6 text-pink-500 dark:text-pink-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
                                </div>
                                <p class="text-3xl font-semibold mt-2" x-text="(dashboardData.metrics?.engagementRate?.current || 0).toFixed(2) + '%'"></p>
                                <p class="text-sm flex items-center mt-1" :class="(dashboardData.metrics?.engagementRate?.change || 0) >= 0 ? 'text-green-500 dark:text-green-400' : 'text-red-500 dark:text-red-400'">
                                    <span x-text="formatChange(dashboardData.metrics?.engagementRate?.change || 0)"></span>
                                </p>
                            </div>
                            <div x-show="isWidgetVisible('total_engagement')" class="bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Engagement</h3>
                                    <svg class="h-6 w-6 text-blue-500 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" /></svg>
                                </div>
                                <p class="text-3xl font-semibold mt-2" x-text="formatNumber(dashboardData.metrics?.engagement?.current || 0)"></p>
                                <p class="text-sm flex items-center mt-1" :class="(dashboardData.metrics?.engagement?.change || 0) >= 0 ? 'text-green-500 dark:text-green-400' : 'text-red-500 dark:text-red-400'">
                                    <span x-text="formatChange(dashboardData.metrics?.engagement?.change || 0)"></span>
                                </p>
                            </div>
                            <div x-show="isWidgetVisible('ad_spend')" class="bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Ad Spend</h3>
                                    <svg class="h-6 w-6 text-yellow-500 dark:text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <p class="text-3xl font-semibold mt-2">$<span x-text="(dashboardData.metrics?.adSpend?.current || 0).toFixed(2)"></span></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-1">This month</p>
                            </div>
                        </div>

                        <!-- Additional Metrics - Row 2 -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-6 mt-6">
                            <div x-show="isWidgetVisible('impressions')" class="bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Impressions</h3>
                                    <svg class="h-6 w-6 text-orange-500 dark:text-orange-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" /></svg>
                                </div>
                                <p class="text-3xl font-semibold mt-2" x-text="formatNumber(calculateFilteredKPI('impressions'))"></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-1">Total impressions</p>
                            </div>
                            <div x-show="isWidgetVisible('views')" class="bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Views</h3>
                                    <svg class="h-6 w-6 text-cyan-500 dark:text-cyan-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                </div>
                                <p class="text-3xl font-semibold mt-2" x-text="formatNumber(calculateFilteredKPI('views'))"></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-1">Video/Reel views</p>
                            </div>
                            <div x-show="isWidgetVisible('likes')" class="bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Likes</h3>
                                    <svg class="h-6 w-6 text-red-500 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>
                                </div>
                                <p class="text-3xl font-semibold mt-2" x-text="formatNumber(calculateFilteredKPI('likes'))"></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-1">From completed posts</p>
                            </div>
                            <div x-show="isWidgetVisible('comments')" class="bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Comments</h3>
                                    <svg class="h-6 w-6 text-indigo-500 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>
                                </div>
                                <p class="text-3xl font-semibold mt-2" x-text="formatNumber(calculateFilteredKPI('comments'))"></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-1">From completed posts</p>
                            </div>
                            <div x-show="isWidgetVisible('shares')" class="bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Shares</h3>
                                    <svg class="h-6 w-6 text-green-500 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" /></svg>
                                </div>
                                <p class="text-3xl font-semibold mt-2" x-text="formatNumber(calculateFilteredKPI('shares'))"></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-1">From completed posts</p>
                            </div>
                            <div x-show="isWidgetVisible('saves')" class="bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Saves</h3>
                                    <svg class="h-6 w-6 text-purple-500 dark:text-purple-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" /></svg>
                                </div>
                                <p class="text-3xl font-semibold mt-2" x-text="formatNumber(calculateFilteredKPI('saves'))"></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-1">From completed posts</p>
                            </div>
                        </div>

                        <!-- Additional Metrics - Row 3 (Video/Reel Specific) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
                            <div x-show="isWidgetVisible('watch_time')" class="bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Watch Time</h3>
                                    <svg class="h-6 w-6 text-blue-500 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <p class="text-3xl font-semibold mt-2" x-text="formatWatchTime(calculateFilteredKPI('watch_time'))"></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-1">Total minutes watched</p>
                            </div>
                            <div x-show="isWidgetVisible('skip_rate')" class="bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg Skip Rate</h3>
                                    <svg class="h-6 w-6 text-amber-500 dark:text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8.688c0-.864.933-1.405 1.683-.977l7.108 4.062a1.125 1.125 0 010 1.953l-7.108 4.062A1.125 1.125 0 013 16.81V8.688zM12.75 8.688c0-.864.933-1.405 1.683-.977l7.108 4.062a1.125 1.125 0 010 1.953l-7.108 4.062a1.125 1.125 0 01-1.683-.977V8.688z" /></svg>
                                </div>
                                <p class="text-3xl font-semibold mt-2" x-text="calculateAvgSkipRate() + '%'"></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-1">Video skip rate</p>
                            </div>
                            <div x-show="isWidgetVisible('follower_views')" class="bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Follower Views</h3>
                                    <svg class="h-6 w-6 text-emerald-500 dark:text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                                </div>
                                <p class="text-3xl font-semibold mt-2" x-text="formatNumber(calculateFilteredKPI('views_followers'))"></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-1">Views from followers</p>
                            </div>
                            <div x-show="isWidgetVisible('non_follower_views')" class="bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Non-Follower Views</h3>
                                    <svg class="h-6 w-6 text-pink-500 dark:text-pink-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                                </div>
                                <p class="text-3xl font-semibold mt-2" x-text="formatNumber(calculateFilteredKPI('views_non_followers'))"></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-1">Discovery potential</p>
                            </div>
                        </div>


                        <!-- Audience Demographics & Advertising -->
                        <div x-show="dashboardData.demographics || dashboardData.advertising" class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                            
                            <!-- Audience Demographics -->
                            <div x-show="dashboardData.demographics" class="bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm">
                                <h3 class="text-lg font-semibold mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    Audience Demographics
                                </h3>
                                
                                <!-- Age & Gender -->
                                <div class="mb-6">
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Age & Gender Distribution</h4>
                                    <div style="height: 200px;">
                                        <canvas id="demographicsChart"></canvas>
                                    </div>
                                </div>

                                <!-- Top Cities & Countries -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Top Cities</h4>
                                        <div class="space-y-2">
                                            <template x-for="city in dashboardData.demographics?.cities?.slice(0, 5)" :key="city.name">
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-600 dark:text-gray-400" x-text="city.name"></span>
                                                    <span class="font-medium text-gray-900 dark:text-white" x-text="city.percentage + '%'"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Top Countries</h4>
                                        <div class="space-y-2">
                                            <template x-for="country in dashboardData.demographics?.countries?.slice(0, 5)" :key="country.name">
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-600 dark:text-gray-400" x-text="country.name"></span>
                                                    <span class="font-medium text-gray-900 dark:text-white" x-text="country.percentage + '%'"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Advertising Insights -->
                            <div x-show="dashboardData.advertising" class="bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm">
                                <h3 class="text-lg font-semibold mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Advertising Insights & Spend
                                </h3>

                                <!-- Ad Metrics Grid -->
                                <div class="grid grid-cols-2 gap-3 mb-4">
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Total Spend</p>
                                        <p class="text-xl font-bold text-gray-900 dark:text-white">$<span x-text="(dashboardData.advertising?.totalSpend || 0).toFixed(2)"></span></p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Reach</p>
                                        <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="formatNumber(dashboardData.advertising?.reach || 0)"></p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Engagement</p>
                                        <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="formatNumber(dashboardData.advertising?.engagement || 0)"></p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Clicks</p>
                                        <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="formatNumber(dashboardData.advertising?.clicks || 0)"></p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Impressions</p>
                                        <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="formatNumber(dashboardData.advertising?.impressions || 0)"></p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                                        <p class="text-xs text-gray-600 dark:text-gray-400">ROAS</p>
                                        <p class="text-xl font-bold text-green-600 dark:text-green-400" x-text="(dashboardData.advertising?.roas || 0).toFixed(1) + 'x'"></p>
                                    </div>
                                </div>

                                <!-- Spend by Platform -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Spend by Platform</h4>
                                    <div style="height: 180px;">
                                        <canvas id="adSpendChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reports View -->
                <div x-show="activeView === 'reports'" x-cloak>
                    <div class="bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Reports</h2>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Report Name</label>
                                <input type="text" x-model="reportBuilder.name" class="w-full border rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Monthly Social Performance Report">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                <select x-model="reportBuilder.status" class="w-full border rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="draft">Draft</option>
                                    <option value="finalized">Finalized</option>
                                    <option value="sent">Sent</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                                <input type="date" x-model="reportBuilder.startDate" class="w-full border rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                                <input type="date" x-model="reportBuilder.endDate" class="w-full border rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Platforms</h3>
                                <button @click="reportBuilder.platforms = [...clientPlatforms]" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Select all</button>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="platform in clientPlatforms" :key="`report-platform-${platform}`">
                                    <button @click="toggleReportPlatform(platform)"
                                            :class="reportBuilder.platforms.includes(platform) ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                                            class="px-3 py-1 rounded-full text-sm font-medium transition-colors capitalize"
                                            x-text="platform"></button>
                                </template>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 mb-2">
                                <input type="checkbox" x-model="reportBuilder.useSelectedPosts" class="rounded">
                                <span>Choose specific published posts for this report</span>
                            </label>
                            <div x-show="reportBuilder.useSelectedPosts" class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 max-h-52 overflow-y-auto space-y-2">
                                <template x-for="post in reportCandidatePosts" :key="`report-post-${post.id}`">
                                    <label class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" :checked="reportBuilder.selectedPostIds.includes(post.id)" @change="toggleReportPostSelection(post.id)" class="mt-1 rounded">
                                        <span>
                                            <span class="font-medium capitalize" x-text="post.platforms?.[0] || post.platform || 'post'"></span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="formatDate(post.publishedDate || post.scheduledDate || post.createdAt)"></span>
                                            <span class="block text-xs text-gray-500 dark:text-gray-400 truncate" x-text="post.caption || 'Untitled post'"></span>
                                        </span>
                                    </label>
                                </template>
                                <p x-show="!reportCandidatePosts.length" class="text-xs text-gray-500 dark:text-gray-400">No published posts available for current filters.</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 mb-4">
                            <button @click="previewReportBuilder()" class="px-3 py-2 rounded-lg text-sm font-medium bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600" :disabled="reportPreviewLoading">
                                <span x-show="!reportPreviewLoading">Preview Report</span>
                                <span x-show="reportPreviewLoading">Previewing...</span>
                            </button>
                            <button @click="generateReportFromBuilder()" class="px-3 py-2 rounded-lg text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed" :disabled="generatingReport">
                                <span x-show="!generatingReport">Generate Report</span>
                                <span x-show="generatingReport">Generating...</span>
                            </button>
                        </div>

                        <div x-show="reportPreview" class="mb-6 border border-indigo-200 dark:border-indigo-700 rounded-lg p-3 bg-indigo-50/50 dark:bg-indigo-900/10">
                            <p class="text-sm font-medium text-gray-900 dark:text-white mb-2">Preview Metrics</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                <div><p class="text-gray-500 dark:text-gray-400">Reach</p><p class="font-semibold text-gray-900 dark:text-white" x-text="formatNumber(reportPreview?.metrics?.totalReach || 0)"></p></div>
                                <div><p class="text-gray-500 dark:text-gray-400">Impressions</p><p class="font-semibold text-gray-900 dark:text-white" x-text="formatNumber(reportPreview?.metrics?.totalImpressions || 0)"></p></div>
                                <div><p class="text-gray-500 dark:text-gray-400">Engagement</p><p class="font-semibold text-gray-900 dark:text-white" x-text="formatNumber(reportPreview?.metrics?.totalEngagement || 0)"></p></div>
                                <div><p class="text-gray-500 dark:text-gray-400">Engagement Rate</p><p class="font-semibold text-gray-900 dark:text-white" x-text="(reportPreview?.metrics?.engagementRate || 0).toFixed(2) + '%'"></p></div>
                            </div>
                        </div>

                        <div x-show="!reports.length" class="text-sm text-gray-500 dark:text-gray-400 py-6 text-center border border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
                            No reports found for this client yet.
                        </div>

                        <ul x-show="reports.length" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="report in reports" :key="report._id">
                                <li class="py-3 first:pt-0 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white" x-text="report.name"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="formatDateRange(report.dateRange)"></p>
                                    </div>
                                    <div class="flex items-center flex-wrap gap-3">
                                        <span class="text-xs font-medium px-2 py-0.5 rounded capitalize"
                                            :class="report.status === 'finalized' || report.status === 'sent' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200'"
                                            x-text="report.status || 'draft'"></span>
                                        <button @click.prevent="openReport(report)" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">View</button>
                                        <button @click.prevent="startReportEdit(report)" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Edit</button>
                                        <button @click.prevent="deleteReport(report)" class="text-sm text-red-600 dark:text-red-400 hover:underline">Delete</button>
                                        <button @click.prevent="downloadReport(report)" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Download PDF</button>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <!-- Event Coverage View -->
                <div x-show="activeView === 'eventCoverage'" x-cloak>
                    <div class="bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                        <div class="flex items-center justify-between gap-3 mb-5">
                            <div>
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Event Coverage</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Request event coverage with your preferred services. We'll review, provide an estimate, and confirm your booking.</p>
                            </div>
                            <span class="hidden sm:inline-flex text-xs px-2.5 py-1 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-medium whitespace-nowrap">Booking Workflow</span>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div class="lg:col-span-2 space-y-5">

                                <!-- Brand Scope Indicator -->
                                <div x-show="user.role === 'client'" class="flex items-center gap-3 p-3 rounded-lg border"
                                     :class="getClientId() ? 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-700/50' : 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-700/50'">
                                    <div :class="getClientId() ? 'text-indigo-500' : 'text-amber-500'">
                                        <svg x-show="getClientId()" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        <svg x-show="!getClientId()" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold" :class="getClientId() ? 'text-indigo-700 dark:text-indigo-300' : 'text-amber-700 dark:text-amber-300'">
                                            <span x-show="getClientId()">Booking for: <span class="font-bold" x-text="availableClients.find(c => c._id === (selectedViewClient?._id || selectedViewClient))?.brandName || 'Selected Brand'"></span></span>
                                            <span x-show="!getClientId()">No brand selected — choose a specific brand above before submitting.</span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Event Name -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Event Name / Title <span class="text-red-400">*</span></label>
                                    <input type="text" x-model="eventCoverageForm.eventName" placeholder="e.g. Annual Gala 2026, Product Launch Event..." class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3.5 py-2.5 text-sm bg-white dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition-all" />
                                </div>

                                <!-- Services -->
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Services <span class="text-red-400">*</span></label>
                                        <span x-show="eventCoverageForm.services.length > 0" class="text-xs text-indigo-600 dark:text-indigo-400 font-medium" x-text="eventCoverageForm.services.length + ' selected'"></span>
                                    </div>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                        <template x-for="service in eventCoverageServiceOptions" :key="service.id">
                                            <button type="button" @click="toggleEventCoverageService(service.id)"
                                                    class="relative border-2 rounded-xl p-3.5 text-left transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-400"
                                                    :class="eventCoverageForm.services.includes(service.id)
                                                        ? 'border-indigo-500 dark:border-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 shadow-md ring-2 ring-indigo-200 dark:ring-indigo-700'
                                                        : 'border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 hover:border-indigo-300 dark:hover:border-indigo-600 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/10'">
                                                <!-- Checkmark badge -->
                                                <div x-show="eventCoverageForm.services.includes(service.id)"
                                                     class="absolute top-2 right-2 w-5 h-5 bg-indigo-500 rounded-full flex items-center justify-center shadow-sm">
                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                </div>
                                                <div class="text-2xl mb-2" x-text="service.icon"></div>
                                                <div class="text-xs font-semibold leading-tight"
                                                     :class="eventCoverageForm.services.includes(service.id) ? 'text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300'"
                                                     x-text="service.label"></div>
                                            </button>
                                        </template>
                                    </div>
                                    <!-- Selected summary -->
                                    <div x-show="eventCoverageForm.services.length > 0" class="mt-2 flex flex-wrap gap-1.5">
                                        <template x-for="sid in eventCoverageForm.services" :key="sid">
                                            <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 font-medium">
                                                <span x-text="(eventCoverageServiceOptions.find(o => o.id === sid) || {}).icon"></span>
                                                <span x-text="(eventCoverageServiceOptions.find(o => o.id === sid) || {}).label"></span>
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <!-- Date + Location -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Event Date & Time <span class="text-red-400">*</span></label>
                                        <input type="datetime-local" x-model="eventCoverageForm.eventDate" class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3.5 py-2.5 text-sm bg-white dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Location <span class="text-red-400">*</span></label>
                                        <input type="text" x-model="eventCoverageForm.location" placeholder="Venue / address" class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3.5 py-2.5 text-sm bg-white dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition-all">
                                    </div>
                                </div>

                                <!-- More Details -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">More Details</label>
                                    <textarea x-model="eventCoverageForm.details" rows="3" placeholder="Audience size, shot list, streaming requirements, special requests..." class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3.5 py-2.5 text-sm bg-white dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition-all resize-none"></textarea>
                                </div>

                                <!-- Submit -->
                                <div class="flex items-center gap-3 pt-1">
                                    <button @click="submitEventCoverageBooking()" :disabled="submittingEventCoverage"
                                            class="px-5 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed transition-colors shadow-sm flex items-center gap-2">
                                        <svg x-show="!submittingEventCoverage" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <svg x-show="submittingEventCoverage" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        <span x-show="!submittingEventCoverage">Request Booking</span>
                                        <span x-show="submittingEventCoverage">Submitting...</span>
                                    </button>
                                    <span class="text-xs text-amber-600 dark:text-amber-400 font-medium" x-show="user.role === 'client' && !getClientId()">⚠ Select a specific brand to submit.</span>
                                </div>
                            </div>

                            <!-- How It Works sidebar -->
                            <div class="space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-700/40 rounded-xl p-4">
                                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        How It Works
                                    </h3>
                                    <ol class="space-y-3">
                                        <li class="flex gap-2.5 text-sm text-gray-600 dark:text-gray-400">
                                            <span class="flex-shrink-0 w-5 h-5 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 text-xs font-bold flex items-center justify-center mt-0.5">1</span>
                                            <span>Submit your event request with desired services, date, and location.</span>
                                        </li>
                                        <li class="flex gap-2.5 text-sm text-gray-600 dark:text-gray-400">
                                            <span class="flex-shrink-0 w-5 h-5 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 text-xs font-bold flex items-center justify-center mt-0.5">2</span>
                                            <span>Our team reviews availability and may suggest modifications.</span>
                                        </li>
                                        <li class="flex gap-2.5 text-sm text-gray-600 dark:text-gray-400">
                                            <span class="flex-shrink-0 w-5 h-5 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 text-xs font-bold flex items-center justify-center mt-0.5">3</span>
                                            <span>A cost estimate is provided and the event is confirmed.</span>
                                        </li>
                                        <li class="flex gap-2.5 text-sm text-gray-600 dark:text-gray-400">
                                            <span class="flex-shrink-0 w-5 h-5 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 text-xs font-bold flex items-center justify-center mt-0.5">4</span>
                                            <span>After the event, we upload your content to Google Drive.</span>
                                        </li>
                                        <li class="flex gap-2.5 text-sm text-gray-600 dark:text-gray-400">
                                            <span class="flex-shrink-0 w-5 h-5 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 text-xs font-bold flex items-center justify-center mt-0.5">5</span>
                                            <span>Your event history is stored here as a searchable gallery.</span>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between gap-2 mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Event Log</h3>
                            <button @click="loadEventCoverageBookings()" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Refresh</button>
                        </div>

                        <div x-show="!eventCoverageBookings.length" class="text-sm text-gray-500 dark:text-gray-400 py-6 text-center border border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
                            No event coverage bookings yet.
                        </div>

                        <div x-show="eventCoverageBookings.length" class="space-y-3">
                            <template x-for="booking in eventCoverageBookings" :key="booking._id">
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="font-semibold text-gray-900 dark:text-white" x-text="booking.eventName || 'Untitled Event'"></p>
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300" x-text="booking.clientId?.brandName || booking.clientId?.name || 'Client'"></span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                <svg class="inline w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span x-text="new Date(booking.eventDate).toLocaleString('en-US', { dateStyle: 'medium', timeStyle: 'short' })"></span>
                                                <span class="mx-1">·</span>
                                                <svg class="inline w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span x-text="booking.location"></span>
                                            </p>
                                            <div class="flex flex-wrap gap-1 mt-2">
                                                <template x-for="srv in (booking.services || [])" :key="`${booking._id}-${srv}`">
                                                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-medium capitalize" x-text="srv.replace(/_/g, ' ')"></span>
                                                </template>
                                            </div>
                                        </div>
                                        <!-- Status + Calendar sync health (stacked, right-aligned) -->
                                        <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                                            <!-- Booking status badge -->
                                            <span class="text-xs px-2.5 py-1 rounded-full font-medium capitalize whitespace-nowrap"
                                                  :class="booking.status === 'accepted' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : booking.status === 'declined' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : booking.status === 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300'"
                                                  x-text="booking.status.replace(/_/g, ' ')"></span>
                                            <!-- Calendar Sync Health — admin/brand_rep only -->
                                            <template x-if="user.role !== 'client' && booking.calendarSyncStatus">
                                                <span class="inline-flex items-center gap-1 text-[11px] font-medium px-2 py-0.5 rounded-full cursor-default whitespace-nowrap"
                                                      :class="{
                                                          'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400 border border-green-200 dark:border-green-800': booking.calendarSyncStatus === 'synced',
                                                          'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400 border border-red-200 dark:border-red-800': booking.calendarSyncStatus === 'sync_failed',
                                                          'bg-gray-100 text-gray-500 dark:bg-gray-700/60 dark:text-gray-400 border border-gray-200 dark:border-gray-600': booking.calendarSyncStatus === 'not_configured'
                                                      }"
                                                      :title="booking.calendarSyncStatus === 'sync_failed' ? ('Sync error: ' + (booking.calendarSyncError || 'Unknown error')) : booking.calendarSyncStatus === 'synced' ? ('Last synced: ' + (booking.calendarLastSyncedAt ? new Date(booking.calendarLastSyncedAt).toLocaleString() : 'N/A')) : 'Google Calendar not configured on the server'">
                                                    <!-- Calendar icon dot -->
                                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                              :d="booking.calendarSyncStatus === 'synced' ? 'M5 13l4 4L19 7' : booking.calendarSyncStatus === 'sync_failed' ? 'M6 18L18 6M6 6l12 12' : 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'"/>
                                                    </svg>
                                                    <span x-text="booking.calendarSyncStatus === 'synced' ? 'Cal Synced' : booking.calendarSyncStatus === 'sync_failed' ? 'Sync Failed' : 'Cal Off'"></span>
                                                    <!-- Link to calendar event when synced -->
                                                    <a x-show="booking.calendarSyncStatus === 'synced' && booking.googleCalendarEventLink"
                                                       :href="booking.googleCalendarEventLink" target="_blank" rel="noopener noreferrer"
                                                       class="ml-0.5 underline underline-offset-2"
                                                       @click.stop
                                                       title="Open in Google Calendar">↗</a>
                                                </span>
                                            </template>
                                        </div>
                                    </div>

                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-3" x-text="booking.details || 'No extra details provided.'"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2" x-show="booking.estimate && (booking.estimate.amount !== null || booking.estimate.notes)">
                                        Estimate: <span x-text="booking.estimate.amount != null ? '$' + Number(booking.estimate.amount).toFixed(2) : 'Pending amount'"></span>
                                        <span x-show="booking.estimate?.notes"> - <span x-text="booking.estimate.notes"></span></span>
                                    </p>

                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <a x-show="booking.driveFolderUrl" :href="booking.driveFolderUrl" target="_blank" rel="noopener noreferrer" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Open Google Drive Folder</a>

                                        <template x-if="user.role === 'client' && booking.status === 'modification_proposed'">
                                            <div class="flex gap-2">
                                                <button @click="respondToEventCoverageProposal(booking, true)" class="text-sm text-green-600 dark:text-green-400 hover:underline">Approve Changes</button>
                                                <button @click="respondToEventCoverageProposal(booking, false)" class="text-sm text-red-600 dark:text-red-400 hover:underline">Reject Changes</button>
                                            </div>
                                        </template>

                                        <template x-if="user.role !== 'client'">
                                            <div class="flex flex-wrap gap-2">
                                                <button @click="reviewEventCoverageBooking(booking, 'accept')" class="text-sm text-green-600 dark:text-green-400 hover:underline">Accept</button>
                                                <button @click="reviewEventCoverageBooking(booking, 'decline')" class="text-sm text-red-600 dark:text-red-400 hover:underline">Decline</button>
                                                <button @click="reviewEventCoverageBooking(booking, 'propose')" class="text-sm text-amber-600 dark:text-amber-400 hover:underline">Suggest Modification</button>
                                                <button @click="reviewEventCoverageBooking(booking, 'estimate')" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Add Estimate</button>
                                                <button x-show="booking.status === 'accepted'" @click="completeEventCoverageBooking(booking)" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Complete + Drive Link</button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Published Posts View -->
                <div x-show="activeView === 'calendar'" x-cloak>
                    <!-- Posts Header -->
                    <div class="bg-white dark:bg-gray-800/50 rounded-lg p-5 shadow-sm mb-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Published Posts</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">View all live posts from your social media accounts</p>
                            </div>
                            <div class="flex gap-2">
                                <!-- Bulk Actions -->
                                <div x-show="selectedPosts.length > 0" class="flex items-center gap-2 mr-4">
                                    <span class="text-sm text-gray-600 dark:text-gray-400"><span x-text="selectedPosts.length"></span> selected</span>
                                    <button @click="bulkDeletePosts()" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Delete Selected
                                    </button>
                                    <button @click="selectedPosts = []" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">Clear</button>
                                </div>
                                
                                <!-- Select All (only show for admins/brand reps when no selections) -->
                                <button x-show="(user.role === 'admin' || user.role === 'brand_rep') && selectedPosts.length === 0"
                                        @click="selectAllPosts()"
                                        class="px-3 py-2 rounded-lg text-sm font-medium bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 flex items-center gap-2 border border-gray-300 dark:border-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Select All</span>
                                </button>
                                
                                <button x-show="user.role === 'admin' || user.role === 'brand_rep'" @click="showCreatePostModal = true" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 flex items-center">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    New Post
                                </button>
                            </div>
                        </div>

                        <!-- Monthly Post Progress Tracker -->
                        <div x-show="monthlyPostTarget > 0" class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-3">
                                <div>
                                    <h3 class="font-semibold text-gray-800 dark:text-white">Post Progress</h3>
                                    <span class="text-xs text-gray-600 dark:text-gray-400" x-text="getProgressMonthName()"></span>
                                </div>
                                <div class="flex items-center gap-1 bg-white dark:bg-gray-700 rounded-lg p-1">
                                    <button @click="progressViewMode = 'month'" :class="progressViewMode === 'month' ? 'bg-indigo-500 text-white' : 'text-gray-600 dark:text-gray-400'" class="px-3 py-1 rounded text-xs font-medium transition-colors">Month</button>
                                    <button @click="progressViewMode = 'year'" :class="progressViewMode === 'year' ? 'bg-indigo-500 text-white' : 'text-gray-600 dark:text-gray-400'" class="px-3 py-1 rounded text-xs font-medium transition-colors">Year</button>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Posts Created</span>
                                <span class="font-bold text-lg" :class="getProgressPostCount() >= getProgressTargetCount() ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400'" x-text="getProgressPostCount() + ' / ' + getProgressTargetCount()"></span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 mb-2">
                                <div class="h-3 rounded-full transition-all duration-300" :class="getProgressPostCount() >= getProgressTargetCount() ? 'bg-green-500' : 'bg-blue-500'" :style="'width: ' + Math.min((getProgressPostCount() / getProgressTargetCount()) * 100, 100) + '%'"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span x-text="getAheadBehindText()"></span>
                                <span x-text="Math.round((getProgressPostCount() / getProgressTargetCount()) * 100) + '% complete'"></span>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="flex flex-wrap gap-3 mt-4">
                            <input type="text" x-model="searchQuery" placeholder="Search posts..." class="border dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-white flex-1 min-w-[200px]">
                            
                            <select x-model="filterPlatform" class="border dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                                <option value="">All Platforms</option>
                                <template x-for="platform in clientPlatforms" :key="platform">
                                    <option :value="platform" x-text="platform.charAt(0).toUpperCase() + platform.slice(1)"></option>
                                </template>
                            </select>
                            
                            <select x-model="filterMonth" class="border dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                                <option value="">All Months</option>
                                <option value="01">January</option>
                                <option value="02">February</option>
                                <option value="03">March</option>
                                <option value="04">April</option>
                                <option value="05">May</option>
                                <option value="06">June</option>
                                <option value="07">July</option>
                                <option value="08">August</option>
                                <option value="09">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                            
                            <select x-model="filterYear" class="border dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                                <template x-for="year in getAvailableYears()" :key="year">
                                    <option :value="year" x-text="year"></option>
                                </template>
                            </select>
                            
                            <select x-model="filterContentType" class="border dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                                <option value="">All Content Types</option>
                                <option value="static">Static Image</option>
                                <option value="video">Video</option>
                                <option value="reel">Reel</option>
                            </select>
                            
                            <select x-model="filterCampaign" class="border dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                                <option value="">All Campaigns</option>
                                <template x-for="campaign in campaigns" :key="campaign.id">
                                    <option :value="campaign.id" x-text="campaign.name"></option>
                                </template>
                            </select>
                            
                            <select x-model="filterStatus" class="border dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                                <option value="">All Statuses</option>
                                <option value="draft">Draft</option>
                                <option value="concept_review">Concept Review</option>
                                <option value="pending_approval">Pending Approval</option>
                                <option value="needs_review">Needs Review</option>
                                <option value="approved">Approved</option>
                                <option value="posted">Posted</option>
                                <option value="completed">Completed</option>
                            </select>
                            
                            <button x-show="user.role !== 'client'" @click="createCampaign()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap">
                                + Campaign
                            </button>
                        </div>
                    </div>

                    <!-- Posts Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <template x-for="post in filteredPosts" :key="post.id">
                            <div class="relative bg-white dark:bg-gray-800/50 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow cursor-pointer" :class="selectedPosts.includes(post.id) ? 'ring-2 ring-indigo-500' : ''" @click="togglePostSelection(post.id, $event)">
                                <!-- Post Thumbnail -->
                                <div class="relative bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 overflow-hidden group" :class="post.contentType === 'reel' ? 'aspect-[9/16]' : 'aspect-square'" x-data="{ playing: false, currentTime: 0, duration: 0 }">
                                    <!-- Video Player for Reels/Videos -->
                                    <template x-if="(post.contentType === 'reel' || post.contentType === 'video') && post.finalContent">
                                        <video 
                                            :src="post.finalContent" 
                                            class="w-full h-full object-cover cursor-pointer"
                                            preload="metadata"
                                            @click="playing = !$el.paused; $el.paused ? $el.play() : $el.pause()"
                                            @play="playing = true"
                                            @pause="playing = false"
                                            @timeupdate="currentTime = $el.currentTime; duration = $el.duration"
                                            @loadedmetadata="duration = $el.duration"
                                            @ended="currentTime = 0"
                                            @error="console.log('Video load error:', post.finalContent)"
                                            playsinline
                                            loop
                                            x-ref="videoPlayer"
                                        >
                                            <source :src="post.finalContent" type="video/mp4">
                                        </video>
                                    </template>
                                    
                                    <!-- Image for Static Posts -->
                                    <template x-if="post.contentType === 'static' && post.finalContent">
                                        <img :src="post.finalContent" class="w-full h-full object-cover" alt="Post preview" @error="console.log('Image load error:', post.finalContent)">
                                    </template>
                                    
                                    <!-- Placeholder if no media loaded yet -->
                                    <template x-if="!post.finalContent || post.finalContent.includes('undefined')">
                                        <div class="flex items-center justify-center h-full flex-col p-4 text-center">
                                            <!-- Video/Reel Icon -->
                                            <template x-if="post.contentType === 'reel' || post.contentType === 'video'">
                                                <div class="flex flex-col items-center">
                                                    <div class="w-20 h-20 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center mb-3">
                                                        <svg class="w-10 h-10 text-indigo-600 dark:text-indigo-300" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize" x-text="post.contentType"></span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-show="post.mediaFile" x-text="post.mediaFile"></span>
                                                </div>
                                            </template>
                                            
                                            <!-- Static Post Icon -->
                                            <template x-if="post.contentType === 'static'">
                                                <div class="flex flex-col items-center">
                                                    <div class="w-20 h-20 rounded-full bg-pink-100 dark:bg-pink-900 flex items-center justify-center mb-3">
                                                        <svg class="w-10 h-10 text-pink-600 dark:text-pink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Image Post</span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    
                                    <!-- Video Controls Overlay -->
                                    <div x-show="(post.contentType === 'reel' || post.contentType === 'video') && post.finalContent" class="absolute inset-0 pointer-events-none">
                                        <!-- Play/Pause Button (center) -->
                                        <div class="absolute inset-0 flex items-center justify-center transition-opacity" :class="playing ? 'opacity-0 group-hover:opacity-100' : 'opacity-100'">
                                            <div class="w-16 h-16 rounded-full bg-black/60 flex items-center justify-center backdrop-blur-sm pointer-events-auto cursor-pointer" @click="$refs.videoPlayer.paused ? $refs.videoPlayer.play() : $refs.videoPlayer.pause()">
                                                <!-- Play Icon -->
                                                <svg x-show="!playing" class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                <!-- Pause Icon -->
                                                <svg x-show="playing" class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"/></svg>
                                            </div>
                                        </div>
                                        
                                        <!-- Video Duration (bottom right) -->
                                        <div x-show="duration > 0" class="absolute bottom-2 right-2 px-2 py-1 rounded bg-black/70 text-white text-xs font-medium backdrop-blur-sm">
                                            <span x-text="Math.floor(currentTime / 60) + ':' + String(Math.floor(currentTime % 60)).padStart(2, '0')"></span>
                                            <span class="text-gray-300">/</span>
                                            <span x-text="Math.floor(duration / 60) + ':' + String(Math.floor(duration % 60)).padStart(2, '0')"></span>
                                        </div>
                                        
                                        <!-- Sound Toggle (top right) -->
                                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button @click="$refs.videoPlayer.muted = !$refs.videoPlayer.muted" class="p-2 rounded-full bg-black/60 text-white backdrop-blur-sm pointer-events-auto hover:bg-black/80">
                                                <!-- Muted Icon -->
                                                <svg x-show="$refs.videoPlayer?.muted" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/></svg>
                                                <!-- Unmuted Icon -->
                                                <svg x-show="!$refs.videoPlayer?.muted" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Status Badge -->
                                    <div class="absolute top-2 right-2">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold" :class="{
                                            'bg-gray-500 text-white': post.status === 'draft',
                                            'bg-blue-500 text-white': post.status === 'concept_review',
                                            'bg-yellow-500 text-white': post.status === 'pending_approval',
                                            'bg-orange-500 text-white': post.status === 'needs_review',
                                            'bg-green-500 text-white': post.status === 'approved',
                                            'bg-purple-500 text-white': post.status === 'posted',
                                            'bg-indigo-600 text-white': post.status === 'completed'
                                        }" x-text="post.status.replace(/_/g, ' ').toUpperCase()"></span>
                                    </div>
                                    <!-- Platform Icons -->
                                    <div class="absolute top-2 left-2 flex gap-1">
                                        <template x-for="platform in post.platforms" :key="platform">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-md" :class="{
                                                'bg-blue-600': platform === 'facebook',
                                                'bg-gradient-to-br from-purple-600 via-pink-600 to-orange-500': platform === 'instagram',
                                                'bg-blue-700': platform === 'linkedin',
                                                'bg-red-600': platform === 'youtube',
                                                'bg-black dark:bg-white': platform === 'x',
                                                'bg-black': platform === 'tiktok'
                                            }">
                                                <!-- Facebook -->
                                                <svg x-show="platform === 'facebook'" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                                <!-- Instagram -->
                                                <svg x-show="platform === 'instagram'" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                                <!-- LinkedIn -->
                                                <svg x-show="platform === 'linkedin'" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                                <!-- YouTube -->
                                                <svg x-show="platform === 'youtube'" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                                <!-- X (Twitter) -->
                                                <svg x-show="platform === 'x'" class="w-4 h-4" :class="platform === 'x' ? 'text-white dark:text-black' : 'text-white'" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                                <!-- TikTok -->
                                                <svg x-show="platform === 'tiktok'" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Post Info -->
                                <div class="p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex flex-col">
                                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="new Date(post.scheduledDate).toLocaleDateString()"></span>
                                            <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400" x-text="new Date(post.scheduledDate).toLocaleDateString('en-US', { month: 'long', year: 'numeric' })"></span>
                                        </div>
                                        <div class="flex flex-col items-end">
                                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300 capitalize" x-text="post.contentType"></span>
                                            <span x-show="post.campaignId" class="text-xs text-green-600 dark:text-green-400 font-medium" x-text="getCampaignName(post.campaignId)"></span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2 mb-3" x-text="post.caption || 'No caption'"></p>
                                    
                                    <!-- Actions -->
                                    <div class="flex gap-1 flex-wrap">
                                        <button @click="viewPost(post)" class="flex-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-2 rounded text-xs hover:bg-gray-300 dark:hover:bg-gray-600">View</button>
                                        <button x-show="post.finalContent" @click.stop="downloadAsset(post)" class="flex-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-2 rounded text-xs hover:bg-gray-300 dark:hover:bg-gray-600">Download</button>
                                        <button x-show="user.role === 'client' && (post.status === 'concept_review' || post.status === 'pending_approval' || post.status === 'needs_review')" @click="reviewPost(post)" class="flex-1 bg-indigo-600 text-white px-2 py-2 rounded text-xs hover:bg-indigo-700">Review</button>
                                        <button x-show="(user.role === 'admin' || user.role === 'brand_rep') && post.status === 'approved'" @click="markAsPosted(post)" class="flex-1 bg-green-600 text-white px-2 py-2 rounded text-xs hover:bg-green-700">Mark Posted</button>
                                        <button x-show="(user.role === 'admin' || user.role === 'brand_rep') && (post.status === 'posted' || post.status === 'completed')" @click="addPostKPIs(post)" class="flex-1 bg-purple-600 text-white px-2 py-2 rounded text-xs hover:bg-purple-700" x-text="post.status === 'completed' ? 'Edit KPIs' : 'Add KPIs'"></button>
                                        <button x-show="(user.role === 'admin' || user.role === 'brand_rep') && post.status === 'completed'" @click="showCreatePostModal = true; editPost(post)" class="flex-1 bg-blue-600 text-white px-2 py-2 rounded text-xs hover:bg-blue-700">Edit</button>
                                        <button x-show="user.role === 'admin' || user.role === 'brand_rep'" @click="deletePost(post)" class="bg-red-600 text-white px-2 py-2 rounded text-xs hover:bg-red-700" title="Delete Post">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Empty State -->
                    <div x-show="filteredPosts.length === 0" class="bg-white dark:bg-gray-800/50 rounded-lg p-12 text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-4.5 12h22.5"></path></svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No posts found</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first post</p>
                        <button x-show="user.role === 'admin' || user.role === 'brand_rep'" @click="showCreatePostModal = true" class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Create Post</button>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Create Post Modal -->
    <div x-show="showCreatePostModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showCreatePostModal = false">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800 mb-10">
            <h3 class="text-lg font-bold mb-4 dark:text-white" x-text="postForm.id ? 'Edit Post' : 'Create New Post'"></h3>
            <form @submit.prevent="createPost()" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Platforms (Select one or more)</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center space-x-2 p-2 border rounded cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700" :class="postForm.platforms.includes('facebook') ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600'">
                            <input type="checkbox" :checked="postForm.platforms.includes('facebook')" @change="togglePlatform('facebook')" class="rounded">
                            <span class="text-sm dark:text-gray-300">Facebook</span>
                        </label>
                        <label class="flex items-center space-x-2 p-2 border rounded cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700" :class="postForm.platforms.includes('instagram') ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600'">
                            <input type="checkbox" :checked="postForm.platforms.includes('instagram')" @change="togglePlatform('instagram')" class="rounded">
                            <span class="text-sm dark:text-gray-300">Instagram</span>
                        </label>
                        <label class="flex items-center space-x-2 p-2 border rounded cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700" :class="postForm.platforms.includes('linkedin') ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600'">
                            <input type="checkbox" :checked="postForm.platforms.includes('linkedin')" @change="togglePlatform('linkedin')" class="rounded">
                            <span class="text-sm dark:text-gray-300">LinkedIn</span>
                        </label>
                        <label class="flex items-center space-x-2 p-2 border rounded cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700" :class="postForm.platforms.includes('youtube') ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600'">
                            <input type="checkbox" :checked="postForm.platforms.includes('youtube')" @change="togglePlatform('youtube')" class="rounded">
                            <span class="text-sm dark:text-gray-300">YouTube</span>
                        </label>
                        <label class="flex items-center space-x-2 p-2 border rounded cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700" :class="postForm.platforms.includes('x') ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600'">
                            <input type="checkbox" :checked="postForm.platforms.includes('x')" @change="togglePlatform('x')" class="rounded">
                            <span class="text-sm dark:text-gray-300">X (Twitter)</span>
                        </label>
                        <label class="flex items-center space-x-2 p-2 border rounded cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700" :class="postForm.platforms.includes('tiktok') ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600'">
                            <input type="checkbox" :checked="postForm.platforms.includes('tiktok')" @change="togglePlatform('tiktok')" class="rounded">
                            <span class="text-sm dark:text-gray-300">TikTok</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 dark:text-gray-300">Content Type</label>
                    <select x-model="postForm.contentType" required class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="static">Static Image (1080x1080)</option>
                        <option value="video">Video (1920x1080)</option>
                        <option value="reel">Reel/Story (1080x1920)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 dark:text-gray-300">Scheduled Date</label>
                    <input type="date" x-model="postForm.scheduledDate" required class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 dark:text-gray-300">Campaign (Optional)</label>
                    <select x-model="postForm.campaignId" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">No Campaign</option>
                        <template x-for="campaign in campaigns" :key="campaign.id">
                            <option :value="campaign.id" x-text="campaign.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 dark:text-gray-300">Caption (Optional)</label>
                    <textarea x-model="postForm.caption" rows="3" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Write your caption here..."></textarea>
                </div>
                <div>
                    <label class="flex items-center space-x-2 mb-3">
                        <input type="checkbox" x-model="postForm.skipConcept" class="rounded">
                        <span class="text-sm dark:text-gray-300">Skip concept review (upload final content directly)</span>
                    </label>
                </div>
                <div x-show="!postForm.skipConcept">
                    <label class="block text-sm font-medium mb-1 dark:text-gray-300">Concept Image/Video (Optional)</label>
                    <input type="file" @change="handleFileUpload($event, 'concept')" accept="image/*,video/*" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <img x-show="postForm.conceptImage" :src="postForm.conceptImage" class="mt-2 max-h-40 rounded">
                </div>
                <div x-show="postForm.skipConcept">
                    <label class="block text-sm font-medium mb-1 dark:text-gray-300">Final Content *</label>
                    <input type="file" @change="handleFileUpload($event, 'final')" accept="image/*,video/*" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <img x-show="postForm.finalContent" :src="postForm.finalContent" class="mt-2 max-h-40 rounded">
                </div>
                <div x-show="postForm.contentType === 'video' || postForm.contentType === 'reel'">
                    <label class="block text-sm font-medium mb-1 dark:text-gray-300">YouTube URL (Optional - for video content)</label>
                    <input type="url" x-model="postForm.youtubeUrl" placeholder="https://youtube.com/watch?v=..." class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">If video is on YouTube, you can link it here instead of uploading</p>
                </div>
                <div class="flex justify-end space-x-2 pt-4 border-t dark:border-gray-600">
                    <button type="button" @click="showCreatePostModal = false; resetPostForm()" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded hover:bg-gray-400 dark:hover:bg-gray-500 dark:text-white">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700" x-text="postForm.id ? 'Update Post' : 'Create Post'"></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Detail Modal -->
    <div x-show="showReportModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showReportModal = false">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white dark:bg-gray-800 mb-10">
            <h3 class="text-lg font-bold mb-4 dark:text-white" x-text="selectedReport?.name || 'Report'"></h3>
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <p class="text-gray-500 dark:text-gray-400">Date Range</p>
                        <p class="font-semibold text-gray-900 dark:text-white" x-text="formatDateRange(selectedReport?.dateRange)"></p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <p class="text-gray-500 dark:text-gray-400">Status</p>
                        <p class="font-semibold text-gray-900 dark:text-white capitalize" x-text="selectedReport?.status || 'draft'"></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Reach</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="formatNumber(selectedReport?.metrics?.totalReach || 0)"></p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Impressions</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="formatNumber(selectedReport?.metrics?.totalImpressions || 0)"></p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Engagement</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="formatNumber(selectedReport?.metrics?.totalEngagement || 0)"></p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Engagement Rate</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="(selectedReport?.metrics?.engagementRate || 0).toFixed(2) + '%'"></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <p class="text-gray-500 dark:text-gray-400">Platforms</p>
                        <p class="font-semibold text-gray-900 dark:text-white capitalize" x-text="(selectedReport?.platforms || []).join(', ') || 'N/A'"></p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <p class="text-gray-500 dark:text-gray-400">Selected Posts</p>
                        <p class="font-semibold text-gray-900 dark:text-white" x-text="selectedReport?.customData?.selectedPostCount || selectedReport?.selectedPostIds?.length || 0"></p>
                    </div>
                </div>

                <div x-show="reportEditForm" class="space-y-3 border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Edit Report</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Name</label>
                            <input type="text" x-model="reportEditForm.name" class="w-full border rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Status</label>
                            <select x-model="reportEditForm.status" class="w-full border rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="draft">Draft</option>
                                <option value="finalized">Finalized</option>
                                <option value="sent">Sent</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t dark:border-gray-600">
                    <button type="button" @click="showReportModal = false" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded hover:bg-gray-400 dark:hover:bg-gray-500 dark:text-white">Close</button>
                    <button type="button" @click="saveReportEdits()" x-show="reportEditForm" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-60 disabled:cursor-not-allowed" :disabled="savingReportEdit">Save</button>
                    <button type="button" @click="downloadReport(selectedReport)" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Download PDF</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Post Modal (for admins/brand reps) -->
    <div x-show="showViewPostModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showViewPostModal = false">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white dark:bg-gray-800 mb-10">
            <h3 class="text-lg font-bold mb-4 dark:text-white">View Post</h3>
            <div class="space-y-4">
                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
                    <img x-show="currentPost?.finalContent || currentPost?.conceptImage" :src="currentPost?.finalContent || currentPost?.conceptImage" class="w-full max-h-96 object-contain rounded">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 dark:text-gray-300">Platforms</label>
                    <div class="flex gap-2">
                        <template x-for="platform in currentPost?.platforms" :key="platform">
                            <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300 rounded-full text-sm capitalize" x-text="platform"></span>
                        </template>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 dark:text-gray-300">Caption</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap" x-text="currentPost?.caption || 'No caption'"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 dark:text-gray-300">Current Status</label>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold" :class="{
                        'bg-gray-500 text-white': currentPost?.status === 'draft',
                        'bg-blue-500 text-white': currentPost?.status === 'concept_review',
                        'bg-yellow-500 text-white': currentPost?.status === 'pending_approval',
                        'bg-orange-500 text-white': currentPost?.status === 'needs_review',
                        'bg-green-500 text-white': currentPost?.status === 'approved',
                        'bg-purple-500 text-white': currentPost?.status === 'posted'
                    }" x-text="currentPost?.status?.replace(/_/g, ' ').toUpperCase()"></span>
                </div>
                <div x-show="user.role === 'admin' || user.role === 'brand_rep'">
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Update Status</label>
                    <div class="flex flex-wrap gap-2">
                        <button @click="updatePostStatus('concept_review')" class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">Concept Review</button>
                        <button @click="updatePostStatus('pending_approval')" class="px-3 py-1 bg-yellow-600 text-white rounded text-sm hover:bg-yellow-700">Pending Approval</button>
                        <button @click="updatePostStatus('approved')" class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700">Approved</button>
                        <button @click="updatePostStatus('needs_review')" class="px-3 py-1 bg-orange-600 text-white rounded text-sm hover:bg-orange-700">Needs Review</button>
                        <button @click="updatePostStatus('posted')" class="px-3 py-1 bg-purple-600 text-white rounded text-sm hover:bg-purple-700">Posted</button>
                    </div>
                </div>
                <div class="flex justify-end space-x-2 pt-4 border-t dark:border-gray-600">
                    <button type="button" @click="showViewPostModal = false" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded hover:bg-gray-400 dark:hover:bg-gray-500 dark:text-white">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Post Modal (for clients) -->
    <div x-show="showReviewPostModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showReviewPostModal = false">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white dark:bg-gray-800 mb-10">
            <h3 class="text-lg font-bold mb-4 dark:text-white">Review Post</h3>
            <div class="space-y-4">
                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
                    <img x-show="currentPost?.finalContent || currentPost?.conceptImage" :src="currentPost?.finalContent || currentPost?.conceptImage" class="w-full max-h-96 object-contain rounded">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 dark:text-gray-300">Caption</label>
                    <textarea x-model="currentPost.caption" rows="3" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 dark:text-gray-300">Feedback (if requesting changes)</label>
                    <textarea x-model="currentPost.clientFeedback" rows="2" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Provide feedback for changes..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 dark:text-gray-300">Caption Suggestion (optional)</label>
                    <textarea x-model="currentPost.captionSuggestion" rows="2" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Suggest a different caption..."></textarea>
                </div>
                <div class="flex justify-end space-x-2 pt-4 border-t dark:border-gray-600">
                    <button type="button" @click="showReviewPostModal = false" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded hover:bg-gray-400 dark:hover:bg-gray-500 dark:text-white">Cancel</button>
                    <button @click="requestReview()" class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">Request Changes</button>
                    <button @click="approvePost()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Approve</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Post KPIs Modal -->
    <div x-show="showPostKPIsModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showPostKPIsModal = false">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white dark:bg-gray-800 mb-10">
            <h3 class="text-lg font-bold mb-4 dark:text-white">Add Post KPIs - Per Platform</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Enter KPIs for each platform this post was published on</p>
            
            <div class="space-y-6 max-h-96 overflow-y-auto">
                <template x-for="platform in currentPost?.platforms" :key="platform">
                    <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4">
                        <h4 class="font-semibold mb-3 text-gray-900 dark:text-white capitalize flex items-center">
                            <span class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center mr-2">
                                <span class="text-sm font-bold text-indigo-600 dark:text-indigo-300" x-text="platform.charAt(0).toUpperCase()"></span>
                            </span>
                            <span x-text="platform"></span>
                        </h4>
                        <div class="grid grid-cols-3 gap-3">
                            <!-- Core Metrics -->
                            <div>
                                <label class="block text-xs font-medium mb-1 dark:text-gray-300">Reach</label>
                                <input type="number" x-model="currentPost.kpis[platform + '_reach']" class="w-full border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1 dark:text-gray-300">Impressions</label>
                                <input type="number" x-model="currentPost.kpis[platform + '_impressions']" class="w-full border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1 dark:text-gray-300">Engagement</label>
                                <input type="number" x-model="currentPost.kpis[platform + '_engagement']" class="w-full border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="0">
                            </div>
                            
                            <!-- Video/Reel Metrics -->
                            <div x-show="currentPost.contentType === 'reel' || currentPost.contentType === 'video'">
                                <label class="block text-xs font-medium mb-1 dark:text-gray-300">Views</label>
                                <input type="number" x-model="currentPost.kpis[platform + '_views']" class="w-full border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="0">
                            </div>
                            <div x-show="currentPost.contentType === 'reel' || currentPost.contentType === 'video'">
                                <label class="block text-xs font-medium mb-1 dark:text-gray-300">Watch Time (sec)</label>
                                <input type="number" x-model="currentPost.kpis[platform + '_watch_time']" class="w-full border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="0">
                            </div>
                            <div x-show="currentPost.contentType === 'reel' || currentPost.contentType === 'video'">
                                <label class="block text-xs font-medium mb-1 dark:text-gray-300">Skip Rate (%)</label>
                                <input type="number" x-model="currentPost.kpis[platform + '_skip_rate']" class="w-full border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="0">
                            </div>
                            
                            <!-- Engagement Actions -->
                            <div>
                                <label class="block text-xs font-medium mb-1 dark:text-gray-300">Likes</label>
                                <input type="number" x-model="currentPost.kpis[platform + '_likes']" class="w-full border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1 dark:text-gray-300">Comments</label>
                                <input type="number" x-model="currentPost.kpis[platform + '_comments']" class="w-full border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1 dark:text-gray-300">Shares</label>
                                <input type="number" x-model="currentPost.kpis[platform + '_shares']" class="w-full border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1 dark:text-gray-300">Saves</label>
                                <input type="number" x-model="currentPost.kpis[platform + '_saves']" class="w-full border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1 dark:text-gray-300">Clicks</label>
                                <input type="number" x-model="currentPost.kpis[platform + '_clicks']" class="w-full border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="0">
                            </div>
                            
                            <!-- Audience Breakdown -->
                            <div x-show="currentPost.contentType === 'reel' || currentPost.contentType === 'video'">
                                <label class="block text-xs font-medium mb-1 dark:text-gray-300">Follower Views</label>
                                <input type="number" x-model="currentPost.kpis[platform + '_views_followers']" class="w-full border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="0">
                            </div>
                            <div x-show="currentPost.contentType === 'reel' || currentPost.contentType === 'video'">
                                <label class="block text-xs font-medium mb-1 dark:text-gray-300">Non-Follower Views</label>
                                <input type="number" x-model="currentPost.kpis[platform + '_views_non_followers']" class="w-full border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="0">
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            
            <div class="flex justify-end space-x-2 mt-6 pt-4 border-t dark:border-gray-600">
                <button type="button" @click="showPostKPIsModal = false" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded hover:bg-gray-400 dark:hover:bg-gray-500 dark:text-white">Cancel</button>
                <button @click="savePostKPIs()" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Save All KPIs</button>
            </div>
        </div>
    </div>

    <!-- KPI Update Modal (for admins/brand reps) -->
    <div x-show="showKPIUpdateModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showKPIUpdateModal = false">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800 mb-10">
            <h3 class="text-lg font-bold mb-4 dark:text-white">Update KPI Progress</h3>
            <div class="space-y-4 max-h-96 overflow-y-auto">
                <template x-for="(kpi, index) in dashboardData.kpis" :key="index">
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white" x-text="kpi.name"></h4>
                                    <span x-show="kpi.isAutoTracked" class="text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-0.5 rounded">Auto-tracked</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="kpi.platform ? kpi.platform.charAt(0).toUpperCase() + kpi.platform.slice(1) : ''"></p>
                            </div>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Target: <span x-text="formatNumber(kpi.targetValue)"></span></span>
                        </div>
                        
                        <!-- Auto-tracked KPIs (read-only) -->
                        <div x-show="kpi.isAutoTracked" class="mb-2">
                            <label class="text-sm text-gray-700 dark:text-gray-300 mb-1 block">Current Value (Auto-updated)</label>
                            <div class="w-full border rounded px-3 py-2 bg-gray-100 dark:bg-gray-600 dark:border-gray-500 text-gray-700 dark:text-gray-300 font-medium">
                                <span x-text="formatNumber(kpi.currentValue)"></span>
                            </div>
                            <p class="text-xs text-green-600 dark:text-green-400 mt-1">✓ This value is automatically updated from your dashboard metrics</p>
                        </div>
                        
                        <!-- Manual KPIs (editable) -->
                        <div x-show="!kpi.isAutoTracked" class="mb-2">
                            <label class="text-sm text-gray-700 dark:text-gray-300 mb-1 block">Current Value</label>
                            <input type="number" x-model="kpi.currentValue" @input="kpi.progress = (kpi.currentValue / kpi.targetValue) * 100" class="w-full border rounded px-3 py-2 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        </div>
                        
                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all" :class="(kpi.progress || 0) >= 100 ? 'bg-green-600' : (kpi.progress || 0) >= 75 ? 'bg-blue-600' : (kpi.progress || 0) >= 50 ? 'bg-yellow-600' : 'bg-red-600'" :style="`width: ${Math.min(100, kpi.progress || 0)}%`"></div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="(kpi.progress || 0).toFixed(1) + '% complete'"></p>
                    </div>
                </template>
                <div x-show="!dashboardData.kpis || dashboardData.kpis.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-8">
                    No KPIs configured yet. Go to Admin Panel → Clients → Customize Dashboard to add KPIs.
                </div>
            </div>
            <div class="flex justify-end space-x-2 mt-6 pt-4 border-t dark:border-gray-600">
                <button type="button" @click="showKPIUpdateModal = false" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded hover:bg-gray-400 dark:hover:bg-gray-500 dark:text-white">Cancel</button>
                <button @click="saveKPIUpdates()" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Save Updates</button>
            </div>
        </div>
    </div>

    <!-- Password Change Modal (Required on First Login) -->
    <div x-show="showPasswordChangeModal" x-cloak class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50" style="display: none;">
        <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-lg rounded-lg bg-white dark:bg-gray-800">
            <div class="text-center mb-6">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold mb-2 text-gray-900 dark:text-white">Password Change Required</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">You must change your temporary password before accessing the dashboard.</p>
            </div>
            <form @submit.prevent="changeRequiredPassword()">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 dark:text-gray-300">Current Password</label>
                    <input type="password" x-model="passwordChangeForm.currentPassword" required class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Enter your temporary password">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 dark:text-gray-300">New Password</label>
                    <input type="password" x-model="passwordChangeForm.newPassword" required minlength="6" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Minimum 6 characters">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-1 dark:text-gray-300">Confirm New Password</label>
                    <input type="password" x-model="passwordChangeForm.confirmPassword" required minlength="6" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Re-enter new password">
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Change Password
                </button>
            </form>
        </div>
    </div>

    <!-- Upload Insights Modal -->
    <div x-show="showUploadInsightsModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showUploadInsightsModal = false" @keydown.escape.window="showUploadInsightsModal = false">
        <div class="relative top-10 mx-auto p-6 border w-full max-w-xl shadow-lg rounded-lg bg-white dark:bg-gray-800 mb-10">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    Upload Insights
                </h3>
                <button @click="showUploadInsightsModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Step 1: Select platform -->
            <div x-show="!insightsUploadParsed && !insightsUploadResults" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Platform</label>
                    <select x-model="insightsUploadPlatform" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        <option value="instagram">Instagram</option>
                        <option value="facebook">Facebook</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Brand</label>
                    <div class="flex items-center gap-2">
                        <p class="flex-1 text-sm text-gray-600 dark:text-gray-400 px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded-lg" x-text="selectedClient?.brandName || selectedClient?.companyName || 'Select a client first'"></p>
                        <button @click="deleteAllClientPosts()" class="px-3 py-2 text-xs font-medium text-red-600 hover:text-white border border-red-300 hover:bg-red-600 rounded-lg transition-colors" title="Delete all existing posts for this brand before re-uploading">Clear Posts</button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload Insights</label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Upload the entire Instagram/Facebook export folder, or just the posts.json file. The system will automatically find and parse the key insight files.</p>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center hover:border-indigo-400 dark:hover:border-indigo-500 transition-colors cursor-pointer"
                             @click="$refs.insightsFolderInput.click()">
                            <input type="file" x-ref="insightsFolderInput" @change="handleInsightsFolderSelect($event)" webkitdirectory directory multiple class="hidden">
                            <svg class="mx-auto w-8 h-8 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Upload Folder</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Entire IG/FB export</p>
                        </div>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center hover:border-indigo-400 dark:hover:border-indigo-500 transition-colors cursor-pointer"
                             @click="$refs.insightsFileInput.click()">
                            <input type="file" x-ref="insightsFileInput" @change="handleInsightsFileSelect($event)" accept=".json,.csv" class="hidden">
                            <svg class="mx-auto w-8 h-8 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Upload File</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Single .json or .csv</p>
                        </div>
                    </div>

                    <div x-show="insightsUploadFile" class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-3">
                        <p class="text-sm text-indigo-700 dark:text-indigo-300 flex items-center gap-1">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span x-text="insightsUploadFile?.name || 'File selected'"></span>
                        </p>
                        <p x-show="insightsUploadFolderFiles?.length > 0" class="text-xs text-indigo-600 dark:text-indigo-400 mt-1" x-text="'Found ' + (insightsUploadFolderFiles?.length || 0) + ' insight file(s) in folder'"></p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t dark:border-gray-700">
                    <button @click="showUploadInsightsModal = false; resetInsightsUpload()" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button>
                    <button @click="parseInsightsFile()" :disabled="!insightsUploadFile || insightsUploadLoading" class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg x-show="insightsUploadLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Parse & Preview
                    </button>
                </div>
            </div>

            <!-- Step 2: Preview parsed data -->
            <div x-show="insightsUploadParsed && !insightsUploadResults" class="space-y-4">
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-green-800 dark:text-green-300 mb-2 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        File Parsed Successfully
                    </h4>
                    <p class="text-xs text-green-700 dark:text-green-400" x-text="insightsUploadFile?.name"></p>
                </div>

                <!-- Page-level metrics preview -->
                <div x-show="insightsUploadParsed?.pageMetrics">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Page-Level Metrics</h4>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <template x-for="[key, value] in Object.entries(insightsUploadParsed?.pageMetrics || {}).filter(([k, v]) => !['startDate','endDate'].includes(k) && v > 0)" :key="key">
                            <div class="flex justify-between bg-gray-50 dark:bg-gray-700 rounded px-3 py-2">
                                <span class="text-gray-600 dark:text-gray-400 capitalize" x-text="key.replace(/_/g, ' ')"></span>
                                <span class="font-semibold text-gray-900 dark:text-white" x-text="typeof value === 'number' ? value.toLocaleString() : value"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Post-level metrics preview -->
                <div x-show="insightsUploadParsed?.postMetrics?.length > 0">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                        Post-Level Metrics (<span x-text="insightsUploadParsed?.postMetrics?.length || 0"></span> posts)
                    </h4>
                    <div class="max-h-48 overflow-y-auto space-y-2">
                        <template x-for="(pm, idx) in (insightsUploadParsed?.postMetrics || []).slice(0, 10)" :key="idx">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-sm">
                                <p class="font-medium text-gray-900 dark:text-white truncate" x-text="pm.caption ? pm.caption.substring(0, 80) + (pm.caption.length > 80 ? '...' : '') : '[No caption]'"></p>
                                <div class="flex gap-3 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    <span x-show="pm.publishedDate" x-text="new Date(pm.publishedDate).toLocaleDateString()"></span>
                                    <span x-show="pm.reach">Reach: <span class="text-gray-900 dark:text-white" x-text="(pm.reach || 0).toLocaleString()"></span></span>
                                    <span x-show="pm.likes">Likes: <span class="text-gray-900 dark:text-white" x-text="(pm.likes || 0).toLocaleString()"></span></span>
                                    <span x-show="pm.comments">Comments: <span class="text-gray-900 dark:text-white" x-text="(pm.comments || 0).toLocaleString()"></span></span>
                                </div>
                            </div>
                        </template>
                        <p x-show="(insightsUploadParsed?.postMetrics?.length || 0) > 10" class="text-xs text-gray-500 dark:text-gray-400 text-center">
                            ...and <span x-text="insightsUploadParsed.postMetrics.length - 10"></span> more posts
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t dark:border-gray-700">
                    <button @click="insightsUploadParsed = null" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Back</button>
                    <button @click="submitInsightsUpload()" :disabled="insightsUploadLoading" class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 flex items-center gap-2">
                        <svg x-show="insightsUploadLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Upload & Apply
                    </button>
                </div>
            </div>

            <!-- Step 3: Results -->
            <div x-show="insightsUploadResults" class="space-y-4">
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 text-center">
                    <svg class="mx-auto w-12 h-12 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h4 class="text-lg font-semibold text-green-800 dark:text-green-300">Insights Uploaded!</h4>
                    <div class="mt-3 space-y-1 text-sm text-green-700 dark:text-green-400">
                        <p x-show="insightsUploadResults?.matched > 0" x-text="insightsUploadResults.matched + ' existing post(s) updated with metrics'"></p>
                        <p x-show="insightsUploadResults?.created > 0" x-text="insightsUploadResults.created + ' new post(s) created with metrics'"></p>
                        <p x-show="insightsUploadResults?.updatedPageMetrics">Page-level metrics distributed to posts</p>
                        <p x-show="!insightsUploadResults?.matched && !insightsUploadResults?.created">No changes were made</p>
                    </div>
                </div>
                <div class="flex justify-end pt-3 border-t dark:border-gray-700">
                    <button @click="showUploadInsightsModal = false; resetInsightsUpload(); reloadClientScopedData();" class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Done</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        const API_URL = typeof ESIROM_API_URL !== 'undefined' ? ESIROM_API_URL : 'https://esirom-hub-backend-production.up.railway.app/api';

        document.addEventListener('alpine:init', () => {
            Alpine.data('clientHub', () => ({
                isSidebarOpen: true,
                activeView: 'dashboard',
                viewMode: localStorage.getItem('viewMode') || 'admin',
                selectedViewClient: localStorage.getItem('selectedViewClient') || null,
                theme: localStorage.getItem('theme') || 'light',
                currentDate: new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }),
                user: {},
                dashboardData: {},
                reports: [],
                selectedReport: null,
                showReportModal: false,
                reportEditForm: null,
                savingReportEdit: false,
                reportPreview: null,
                reportPreviewLoading: false,
                reportBuilder: {
                    name: '',
                    status: 'draft',
                    startDate: '',
                    endDate: '',
                    platforms: [],
                    useSelectedPosts: false,
                    selectedPostIds: []
                },
                eventCoverageBookings: [],
                eventCoverageLoading: false,
                submittingEventCoverage: false,
                eventCoverageServiceOptions: [
                    { id: 'photography', label: 'Photography', icon: '📸' },
                    { id: 'videography', label: 'Videography', icon: '🎥' },
                    { id: 'live_coverage', label: 'Live Coverage', icon: '🎬' },
                    { id: 'live_streaming', label: 'Live Streaming', icon: '📡' }
                ],
                eventCoverageForm: {
                    eventName: '',
                    services: [],
                    eventDate: '',
                    location: '',
                    details: ''
                },
                loading: true,
                toasts: [],
                generatingReport: false,
                showPwModal: false,
                pwCurrent: '', pwNew: '', pwConfirm: '', pwLoading: false, pwError: '', pwSuccess: '',
                availableClients: [],
                selectedClient: null,
                showKPIUpdateModal: false,
                showPasswordChangeModal: false,
                passwordChangeForm: {
                    currentPassword: '',
                    newPassword: '',
                    confirmPassword: ''
                },

                async reloadClientScopedData() {
                    this.clientLoadToken = (this.clientLoadToken || 0) + 1;
                    const token = this.clientLoadToken;

                    this.platformFollowers = {};
                    this.pageLevelMetrics = {};
                    this.posts = [];
                    this.insightsUploadResults = null;
                    this.resetInsightsUpload();

                    await this.loadDashboardData(token);
                    await this.loadReports(token);
                    await this.loadPosts(token);
                    await this.loadEventCoverageBookings(token);
                    this.loadCampaigns();
                    this.loadPlatformFollowers();
                    this.loadPageLevelMetrics();
                    this.updateDashboardMetrics();
                    this.initializeReportBuilder();
                },
                
                // Platform filter for dashboard
                clientPlatforms: ['facebook', 'instagram'], // Will be loaded from client customization
                activePlatforms: ['facebook', 'instagram'],
                monthlyPostTarget: 0, // Will be loaded from client customization
                clientStartDate: null, // Will be loaded from client customization
                mirrorIGToFB: false,
                progressViewMode: 'month', // 'month' or 'year' for Monthly Post Progress widget
                
                // Date range filter
                dateRange: 'ytd', // ytd, current_month, last_month, last_3_months, custom
                showCustomDatePicker: false,
                customStartDate: '',
                customEndDate: '',
                currentPeriodStart: null,
                currentPeriodEnd: null,
                previousPeriodStart: null,
                previousPeriodEnd: null,
                currentPeriodLabel: '',
                
                // Widget customization
                showCustomizeWidgets: false,
                userWidgetPreferences: [
                    'reach', 'engagement_rate', 'total_engagement', 'ad_spend',
                    'impressions', 'views', 'likes', 'comments', 'shares', 'saves',
                    'watch_time', 'skip_rate', 'follower_views', 'non_follower_views', 'followers'
                ], // User's personal widget visibility preferences (default: all visible)
                adminEnabledWidgets: [], // Widgets enabled by admin for this client
                
                // Content Calendar
                posts: [],
                campaigns: [],
                selectedPosts: [],
                filterPlatform: '',
                filterMonth: '',
                filterYear: new Date().getFullYear().toString(),
                filterStatus: '',
                filterContentType: '',
                filterCampaign: '',
                searchQuery: '',
                showUploadInsightsModal: false,
                insightsUploadPlatform: 'instagram',
                insightsUploadFile: null,
                insightsUploadFolderFiles: null,
                insightsUploadParsed: null,
                insightsUploadLoading: false,
                insightsUploadResults: null,
                platformFollowers: {},
                pageLevelMetrics: {},
                clientLoadToken: 0,
                showCreatePostModal: false,
                showViewPostModal: false,
                showReviewPostModal: false,
                showPostKPIsModal: false,
                showCreateCampaignModal: false,
                currentPost: null,
                postForm: {
                    platforms: ['instagram'], // Changed to array for multi-platform
                    contentType: 'static',
                    scheduledDate: new Date().toISOString().split('T')[0],
                    caption: '',
                    conceptImage: null,
                    finalContent: null,
                    youtubeUrl: '',
                    campaignId: '',
                    skipConcept: false
                },

                showToast(message, type = 'info', duration = 3000) {
                    const id = Date.now();
                    this.toasts.push({ id, message, type });
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, duration);
                },

                async init() {
                    // Remove any stray checkboxes immediately
                    this.removeStrayCheckboxes();
                    
                    // Handle URL routing
                    this.handleRouting();
                    window.addEventListener('hashchange', () => this.handleRouting());
                    
                    // Check authentication
                    const token = localStorage.getItem('token');
                    const userStr = localStorage.getItem('user');
                    
                    if (!token || !userStr) {
                        window.location.href = '<?php echo esc_url(get_permalink(get_page_by_path('login'))); ?>';
                        return;
                    }

                    this.user = JSON.parse(userStr);
                    
                    // Check if password change is required
                    if (localStorage.getItem('requirePasswordChange') === 'true') {
                        this.showPasswordChangeModal = true;
                        return; // Don't load dashboard until password is changed
                    }

                    // Verify token and load data
                    await this.verifyAuth();

                    // Normalize viewMode for role
                    if (this.user?.role === 'client') {
                        this.viewMode = 'client';
                    } else if (this.user?.role === 'brand_rep' && this.viewMode === 'admin') {
                        this.viewMode = 'brand_rep';
                    } else if (this.user?.role === 'admin' && !['admin', 'brand_rep', 'client'].includes(this.viewMode)) {
                        this.viewMode = 'admin';
                    }
                    localStorage.setItem('viewMode', this.viewMode);
                    window.dispatchEvent(new CustomEvent('viewModeChanged', { detail: { viewMode: this.viewMode } }));
                    
                    // For clients, default to Published Posts view if no hash specified
                    if (this.user.role === 'client' && !window.location.hash) {
                        this.activeView = 'calendar';
                        window.location.hash = 'content-calendar';
                    }
                    
                    // Load available clients for admins/brand reps and multi-brand clients
                    if (this.user.role === 'admin' || this.user.role === 'brand_rep' || this.user.role === 'client') {
                        await this.loadAvailableClients();
                    }
                    
                    this.setDateRange('ytd');
                    await this.reloadClientScopedData();

                    // Watch for view changes
                    this.$watch('activeView', () => {
                        if (this.activeView === 'dashboard') {
                           setTimeout(() => this.initCharts(), 100);
                        }
                    });
                    
                    this.$watch('theme', () => {
                        if (this.activeView === 'dashboard') {
                            setTimeout(() => this.initCharts(), 100);
                        }
                    });
                },

                async verifyAuth() {
                    try {
                        const response = await fetch(`${API_URL}/auth/me`, {
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Authentication failed');
                        }

                        const data = await response.json();
                        this.user = data.user;
                        localStorage.setItem('user', JSON.stringify(data.user));
                    } catch (error) {
                        console.error('Auth error:', error);
                        this.logout();
                    }
                },

                async loadAvailableClients() {
                    try {
                        const response = await fetch(`${API_URL}/clients?serviceType=social_media`, {
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            }
                        });

                        if (response.ok) {
                            const data = await response.json();
                            this.availableClients = data.data || [];

                            if (this.user.role === 'client') {
                                // For real client users: allow "All Brands" when they have more than one brand.
                                const savedViewClient = localStorage.getItem('selectedViewClient') || '';
                                const hasSaved = this.availableClients.some(c => c._id === savedViewClient);
                                this.selectedViewClient = hasSaved ? savedViewClient : '';
                                if (this.selectedViewClient) {
                                    localStorage.setItem('selectedViewClient', this.selectedViewClient);
                                } else {
                                    localStorage.removeItem('selectedViewClient');
                                }
                            } else {
                                // Restore previously selected client or use first (admin/brand rep)
                                const savedClientId = localStorage.getItem('selectedClientId');
                                if (savedClientId && this.availableClients.length > 0) {
                                    const savedClient = this.availableClients.find(c => c._id === savedClientId);
                                    this.selectedClient = savedClient || this.availableClients[0];
                                } else if (this.availableClients.length > 0) {
                                    this.selectedClient = this.availableClients[0];
                                }
                            }
                        }
                    } catch (error) {
                        console.error('Error loading clients:', error);
                    }
                },

                async switchClient(client) {
                    this.selectedClient = client;
                    localStorage.setItem('selectedClientId', client._id);
                    await this.reloadClientScopedData();
                },

                async loadDashboardData(loadToken = null) {
                    try {
                        this.loading = true;
                        const token = loadToken ?? this.clientLoadToken;

                        const clientIds = this.getActiveClientIds();
                        if (!clientIds.length) {
                            throw new Error('No client selected');
                        }

                        // Client "All Brands" scope: aggregate data from each allowed brand dashboard.
                        if (clientIds.length > 1) {
                            const snapshots = await Promise.all(clientIds.map(async (id) => {
                                try {
                                    const res = await fetch(`${API_URL}/dashboard/${id}`, {
                                        headers: {
                                            'Authorization': `Bearer ${localStorage.getItem('token')}`
                                        }
                                    });
                                    if (!res.ok) return null;
                                    const data = await res.json();
                                    return data?.data || null;
                                } catch (error) {
                                    console.error(`Error loading dashboard for client ${id}:`, error);
                                    return null;
                                }
                            }));

                            if (token !== this.clientLoadToken) return;

                            const validSnapshots = snapshots.filter(Boolean);
                            this.dashboardData = this.mergeDashboardSnapshots(validSnapshots);
                            this.clientPlatforms = [...new Set(validSnapshots.flatMap(s => Object.keys(s?.platformBreakdown || {})))];
                            if (this.clientPlatforms.length === 0) {
                                this.clientPlatforms = ['facebook', 'instagram'];
                            }
                            this.activePlatforms = [...this.clientPlatforms];

                            const savedPreferences = localStorage.getItem('widgetPreferences_all_clients');
                            if (savedPreferences) {
                                this.userWidgetPreferences = JSON.parse(savedPreferences);
                            } else {
                                this.userWidgetPreferences = [
                                    'reach', 'engagement_rate', 'total_engagement', 'ad_spend',
                                    'impressions', 'views', 'likes', 'comments', 'shares', 'saves',
                                    'watch_time', 'skip_rate', 'follower_views', 'non_follower_views', 'followers'
                                ];
                            }

                            setTimeout(() => {
                                this.updateAutoTrackedKPIs();
                                this.initCharts();
                            }, 100);
                            return;
                        }

                        const clientId = clientIds[0];
                        
                        const response = await fetch(`${API_URL}/dashboard/${clientId}`, {
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            }
                        });

                        if (token !== this.clientLoadToken) return;

                        if (response.ok) {
                            const data = await response.json();
                            this.dashboardData = data.data;
                            
                            // Load client configuration from database
                            try {
                                const clientResponse = await fetch(`${API_URL}/clients/${clientId}`, {
                                    headers: {
                                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                                    }
                                });

                                if (token !== this.clientLoadToken) return;

                                if (clientResponse.ok) {
                                    const clientData = await clientResponse.json();
                                    const dashboardConfig = clientData.data.dashboardConfig || {};

                                    if (token !== this.clientLoadToken) return;
                                    
                                    console.log('Loading dashboard config from database:', dashboardConfig);
                                    
                                    // Load KPI Goals from database
                                    if (dashboardConfig.kpiGoals && dashboardConfig.kpiGoals.length > 0) {
                                        this.dashboardData.kpis = dashboardConfig.kpiGoals;
                                        
                                        // Migrate old KPIs to new format (add isAutoTracked flag if missing)
                                        this.dashboardData.kpis.forEach(kpi => {
                                            if (kpi.isAutoTracked === undefined) {
                                                kpi.isAutoTracked = ['reach', 'engagement', 'impressions', 'engagement_rate', 'platform_reach', 'platform_engagement'].includes(kpi.kpiType);
                                                console.log(`Migrated KPI "${kpi.name}" - isAutoTracked: ${kpi.isAutoTracked}`);
                                            }
                                        });
                                        
                                        console.log('KPIs loaded from database:', this.dashboardData.kpis);
                                        console.log('Number of KPIs:', this.dashboardData.kpis.length);
                                    } else {
                                        console.log('No KPI goals found in database');
                                    }
                                    
                                    // Load platforms from database
                                    if (dashboardConfig.platforms && dashboardConfig.platforms.length > 0) {
                            this.clientPlatforms = dashboardConfig.platforms;
                            this.activePlatforms = [...dashboardConfig.platforms];
                            console.log('Platforms loaded from database:', this.clientPlatforms);
                        }
                        
                        // Load monthly post target
                        if (dashboardConfig.monthlyPostTarget) {
                            this.monthlyPostTarget = dashboardConfig.monthlyPostTarget;
                        }
                        
                        // Load client start date
                        if (dashboardConfig.clientStartDate) {
                            this.clientStartDate = dashboardConfig.clientStartDate;
                        }
                        // Load IG->FB mirror flag
                        if (dashboardConfig.mirrorIGToFB !== undefined) {
                            this.mirrorIGToFB = dashboardConfig.mirrorIGToFB;
                        }
                                } else {
                                    console.log('Failed to load client config from database, falling back to localStorage');
                                    // Fallback to localStorage
                                    const customization = localStorage.getItem(`client_customize_${clientId}`);
                                    if (customization) {
                                        const customize = JSON.parse(customization);
                                        if (customize.kpiGoals) this.dashboardData.kpis = customize.kpiGoals;
                                        if (customize.platforms) {
                                            this.clientPlatforms = customize.platforms;
                                            this.activePlatforms = [...customize.platforms];
                                        }
                                        if (customize.monthlyPostTarget) this.monthlyPostTarget = customize.monthlyPostTarget;
                                        if (customize.clientStartDate) this.clientStartDate = customize.clientStartDate;
                                    }
                                }
                            } catch (error) {
                                console.error('Error loading client config:', error);
                            }
                            
                            // Load user widget preferences or set all as default
                            const savedPreferences = localStorage.getItem(`widgetPreferences_${clientId}`);
                            if (savedPreferences) {
                                this.userWidgetPreferences = JSON.parse(savedPreferences);
                            } else {
                                // Default: all widgets visible (set all widget IDs)
                                this.userWidgetPreferences = [
                                    'reach', 'engagement_rate', 'total_engagement', 'ad_spend',
                                    'impressions', 'views', 'likes', 'comments', 'shares', 'saves',
                                    'watch_time', 'skip_rate', 'follower_views', 'non_follower_views', 'followers'
                                ];
                            }
                            
                            // Optional extras from legacy localStorage exports (upload flow); KPIs/insights now come from API
                            const reportData = localStorage.getItem(`client_report_${clientId}`);
                            if (reportData) {
                                try {
                                    const report = JSON.parse(reportData);
                                    if (report.demographics) this.dashboardData.demographics = report.demographics;
                                    if (report.advertising) this.dashboardData.advertising = report.advertising;
                                } catch (e) { /* ignore */ }
                            }
                            
                            // Auto-update KPI values AFTER all data is loaded
                            setTimeout(() => {
                                this.updateAutoTrackedKPIs();
                                this.initCharts();
                            }, 100);
                        }
                    } catch (error) {
                        console.error('Error loading dashboard:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                mergeDashboardSnapshots(snapshots = []) {
                    const combined = {
                        client: { brandName: 'All Brands' },
                        metrics: {
                            reach: { current: 0, previous: 0, change: 0 },
                            impressions: { current: 0, previous: 0, change: 0 },
                            engagement: { current: 0, previous: 0, change: 0 },
                            engagementRate: { current: 0, previous: 0, change: 0 },
                            adSpend: { current: 0, previous: 0, change: 0 }
                        },
                        platformBreakdown: {},
                        recentReports: [],
                        upcomingPosts: [],
                        pendingPosts: [],
                        kpis: [],
                        insights: [],
                        demographics: null,
                        advertising: null
                    };

                    snapshots.forEach((snapshot) => {
                        if (!snapshot) return;

                        const addMetric = (key) => {
                            const current = Number(snapshot?.metrics?.[key]?.current || 0);
                            const previous = Number(snapshot?.metrics?.[key]?.previous || 0);
                            combined.metrics[key].current += current;
                            combined.metrics[key].previous += previous;
                        };

                        addMetric('reach');
                        addMetric('impressions');
                        addMetric('engagement');
                        addMetric('adSpend');

                        Object.entries(snapshot?.platformBreakdown || {}).forEach(([platform, values]) => {
                            if (!combined.platformBreakdown[platform]) {
                                combined.platformBreakdown[platform] = { posts: 0, reach: 0, engagement: 0 };
                            }
                            combined.platformBreakdown[platform].posts += Number(values?.posts || 0);
                            combined.platformBreakdown[platform].reach += Number(values?.reach || 0);
                            combined.platformBreakdown[platform].engagement += Number(values?.engagement || 0);
                        });

                        combined.recentReports.push(...(snapshot?.recentReports || []));
                        combined.upcomingPosts.push(...(snapshot?.upcomingPosts || []));
                        combined.pendingPosts.push(...(snapshot?.pendingPosts || []));
                    });

                    const engagementCurrent = combined.metrics.engagement.current;
                    const impressionsCurrent = combined.metrics.impressions.current;
                    const engagementPrevious = combined.metrics.engagement.previous;
                    const impressionsPrevious = combined.metrics.impressions.previous;
                    combined.metrics.engagementRate.current = impressionsCurrent > 0 ? (engagementCurrent / impressionsCurrent) * 100 : 0;
                    combined.metrics.engagementRate.previous = impressionsPrevious > 0 ? (engagementPrevious / impressionsPrevious) * 100 : 0;

                    Object.keys(combined.metrics).forEach((key) => {
                        const metric = combined.metrics[key];
                        metric.change = metric.previous > 0 ? ((metric.current - metric.previous) / metric.previous) * 100 : (metric.current > 0 ? 100 : 0);
                    });

                    combined.recentReports = combined.recentReports
                        .sort((a, b) => new Date(b.createdAt || 0) - new Date(a.createdAt || 0))
                        .slice(0, 5);

                    combined.upcomingPosts = combined.upcomingPosts
                        .sort((a, b) => new Date(a.scheduledDate || 0) - new Date(b.scheduledDate || 0))
                        .slice(0, 10);

                    combined.pendingPosts = combined.pendingPosts
                        .sort((a, b) => new Date(b.createdAt || 0) - new Date(a.createdAt || 0));

                    return combined;
                },

                updateAutoTrackedKPIs() {
                    console.log('=== updateAutoTrackedKPIs called ===');
                    console.log('KPIs:', this.dashboardData.kpis);
                    console.log('Metrics:', this.dashboardData.metrics);
                    console.log('Platform Breakdown:', this.dashboardData.platformBreakdown);
                    
                    if (!this.dashboardData.kpis) {
                        console.log('No KPIs found');
                        return;
                    }
                    
                    if (!this.dashboardData.metrics) {
                        console.log('No metrics found - data may not be loaded yet');
                        return;
                    }
                    
                    this.dashboardData.kpis.forEach(kpi => {
                        console.log(`Processing KPI: ${kpi.name}, Type: ${kpi.kpiType}, Auto-tracked: ${kpi.isAutoTracked}`);
                        
                        if (!kpi.isAutoTracked) {
                            console.log(`  Skipping ${kpi.name} - not auto-tracked`);
                            return;
                        }
                        
                        let currentValue = 0;
                        
                        switch(kpi.kpiType) {
                            case 'reach':
                                currentValue = this.dashboardData.metrics.reach?.current || 0;
                                console.log(`  Reach metric: ${currentValue}`);
                                break;
                            case 'engagement':
                                currentValue = this.dashboardData.metrics.engagement?.current || 0;
                                console.log(`  Engagement metric: ${currentValue}`);
                                break;
                            case 'impressions':
                                currentValue = this.dashboardData.metrics.impressions?.current || 0;
                                console.log(`  Impressions metric: ${currentValue}`);
                                break;
                            case 'engagement_rate':
                                currentValue = this.dashboardData.metrics.engagementRate?.current || 0;
                                console.log(`  Engagement rate metric: ${currentValue}`);
                                break;
                            case 'platform_reach':
                                if (kpi.platform && this.dashboardData.platformBreakdown) {
                                    currentValue = this.dashboardData.platformBreakdown[kpi.platform]?.reach || 0;
                                    console.log(`  Platform reach for ${kpi.platform}: ${currentValue}`);
                                }
                                break;
                            case 'platform_engagement':
                                if (kpi.platform && this.dashboardData.platformBreakdown) {
                                    currentValue = this.dashboardData.platformBreakdown[kpi.platform]?.engagement || 0;
                                    console.log(`  Platform engagement for ${kpi.platform}: ${currentValue}`);
                                }
                                break;
                        }
                        
                        kpi.currentValue = currentValue;
                        
                        // Calculate progress
                        if (kpi.targetValue > 0) {
                            kpi.progress = (currentValue / kpi.targetValue) * 100;
                        } else {
                            kpi.progress = 0;
                        }
                        
                        console.log(`✓ Updated KPI "${kpi.name}": ${currentValue} / ${kpi.targetValue} (${kpi.progress.toFixed(1)}%)`);
                    });
                },

                saveKPIUpdates() {
                    const clientId = this.getClientId();
                    if (!clientId) return;
                    
                    // Update auto-tracked KPIs before saving
                    this.updateAutoTrackedKPIs();
                    
                    // Get current customization
                    const customization = localStorage.getItem(`client_customize_${clientId}`);
                    if (customization) {
                        const customize = JSON.parse(customization);
                        customize.kpiGoals = this.dashboardData.kpis;
                        localStorage.setItem(`client_customize_${clientId}`, JSON.stringify(customize));
                        this.showToast('KPI progress updated successfully!', 'success');
                        this.showKPIUpdateModal = false;
                    }
                },

                async changeRequiredPassword() {
                    if (this.passwordChangeForm.newPassword !== this.passwordChangeForm.confirmPassword) {
                        this.showToast('New passwords do not match!', 'error');
                        return;
                    }
                    
                    if (this.passwordChangeForm.newPassword.length < 6) {
                        this.showToast('Password must be at least 6 characters long!', 'error');
                        return;
                    }
                    
                    try {
                        const response = await fetch(`${API_URL}/auth/update-password`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            },
                            body: JSON.stringify({
                                currentPassword: this.passwordChangeForm.currentPassword,
                                newPassword: this.passwordChangeForm.newPassword
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            // Clear the password change requirement
                            localStorage.removeItem('requirePasswordChange');
                            this.showPasswordChangeModal = false;
                            this.passwordChangeForm = { currentPassword: '', newPassword: '', confirmPassword: '' };
                            
                            // Update token if new one was provided
                            if (data.token) {
                                localStorage.setItem('token', data.token);
                            }
                            
                            this.showToast('Password changed successfully! Loading dashboard...', 'success');
                            
                            // Now load the dashboard
                            await this.verifyAuth();
                            if (this.user.role === 'admin' || this.user.role === 'brand_rep' || this.user.role === 'client') {
                                await this.loadAvailableClients();
                            }
                            this.setDateRange('ytd');
                            await this.reloadClientScopedData();
                        } else {
                            this.showToast(data.message || 'Failed to change password. Please check your current password.', 'error');
                        }
                    } catch (error) {
                        console.error('Password change error:', error);
                        this.showToast('Error changing password. Please try again.', 'error');
                    }
                },

                async changePassword() {
                    this.pwError = ''; this.pwSuccess = '';
                    if (!this.pwCurrent || !this.pwNew || !this.pwConfirm) { this.pwError = 'All fields are required.'; return; }
                    if (this.pwNew.length < 6) { this.pwError = 'New password must be at least 6 characters.'; return; }
                    if (this.pwNew !== this.pwConfirm) { this.pwError = 'New passwords do not match.'; return; }
                    this.pwLoading = true;
                    try {
                        const res = await fetch(`${API_URL}/auth/update-password`, {
                            method: 'PUT',
                            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}`, 'Content-Type': 'application/json' },
                            body: JSON.stringify({ currentPassword: this.pwCurrent, newPassword: this.pwNew })
                        });
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Failed to update password');
                        if (data.token) localStorage.setItem('token', data.token);
                        this.pwSuccess = 'Password updated successfully!';
                        this.pwCurrent = ''; this.pwNew = ''; this.pwConfirm = '';
                        setTimeout(() => { this.showPwModal = false; this.pwSuccess = ''; }, 2000);
                    } catch (e) { this.pwError = e.message; } finally { this.pwLoading = false; }
                },

                logout() {
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    localStorage.removeItem('requirePasswordChange');
                    window.location.href = '<?php echo esc_url(get_permalink(get_page_by_path('login'))); ?>';
                },

                switchViewMode(mode) {
                    this.viewMode = mode;
                    localStorage.setItem('viewMode', mode);
                    window.dispatchEvent(new CustomEvent('viewModeChanged', { detail: { viewMode: mode } }));
                    
                    // For client view, select first client if not already selected
                    if (mode === 'client' && !this.selectedViewClient && this.availableClients.length > 0) {
                        this.selectedViewClient = '';
                        localStorage.removeItem('selectedViewClient');
                    } else if (mode !== 'client') {
                        this.selectedViewClient = null;
                        localStorage.removeItem('selectedViewClient');
                    }
                    
                    // Reload all data with the new view perspective
                    this.reloadClientScopedData();
                },

                handleSelectedViewClientChange() {
                    if (this.selectedViewClient) {
                        localStorage.setItem('selectedViewClient', this.selectedViewClient);
                    } else {
                        localStorage.removeItem('selectedViewClient');
                    }
                    this.reloadClientScopedData();
                },

                setClientBrandScope(clientId) {
                    this.selectedViewClient = clientId || '';
                    this.handleSelectedViewClientChange();
                },

                getActiveClientIds() {
                    if (this.user.role === 'client') {
                        if (this.selectedViewClient) return [this.selectedViewClient];
                        const ids = (this.availableClients || []).map(c => c?._id).filter(Boolean);
                        if (ids.length > 0) return ids;
                        const fallbackId = this.user?.clientId?._id || this.user?.clientId;
                        return fallbackId ? [fallbackId] : [];
                    }

                    if (this.viewMode === 'client') {
                        if (this.selectedViewClient) return [this.selectedViewClient];
                        const allIds = (this.availableClients || []).map(c => c?._id).filter(Boolean);
                        if (allIds.length > 0) return allIds;
                    }

                    const selectedId = this.selectedClient?._id;
                    return selectedId ? [selectedId] : [];
                },

                toggleTheme() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    if (this.theme === 'dark') {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                },

                get pageTitle() {
                    switch (this.activeView) {
                        case 'dashboard':
                            return this.viewMode === 'client' ? 'Page Overview' : 'Dashboard Overview';
                        case 'calendar':
                            return this.viewMode === 'client' ? 'Published Posts' : (this.selectedClient?.brandName || this.selectedClient?.name || 'Dashboard');
                        case 'reports':
                            return 'Reports';
                        case 'eventCoverage':
                            return 'Event Coverage';
                        default:
                            return 'Dashboard';
                    }
                },

                async loadReports(loadToken = null) {
                    const clientId = this.getClientId();
                    if (!clientId && this.user.role !== 'client') {
                        this.reports = [];
                        return;
                    }

                    const token = loadToken ?? this.clientLoadToken;
                    try {
                        const reportsUrl = clientId
                            ? `${API_URL}/reports?clientId=${encodeURIComponent(clientId)}`
                            : `${API_URL}/reports`;
                        const response = await fetch(reportsUrl, {
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            }
                        });

                        if (token !== this.clientLoadToken) return;

                        if (response.ok) {
                            const data = await response.json();
                            this.reports = data.data || [];
                        } else {
                            this.reports = [];
                        }
                    } catch (error) {
                        console.error('Load reports error:', error);
                        this.reports = [];
                    }
                },

                async loadEventCoverageBookings(loadToken = null) {
                    const token = loadToken ?? this.clientLoadToken;
                    this.eventCoverageLoading = true;
                    try {
                        const clientId = this.getClientId();
                        const bookingsUrl = clientId
                            ? `${API_URL}/event-coverage?clientId=${encodeURIComponent(clientId)}`
                            : `${API_URL}/event-coverage`;
                        const response = await fetch(bookingsUrl, {
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            }
                        });

                        if (token !== this.clientLoadToken) return;

                        if (response.ok) {
                            const data = await response.json();
                            this.eventCoverageBookings = data.data || [];
                        } else {
                            this.eventCoverageBookings = [];
                        }
                    } catch (error) {
                        console.error('Load event coverage bookings error:', error);
                        this.eventCoverageBookings = [];
                    } finally {
                        this.eventCoverageLoading = false;
                    }
                },

                toggleEventCoverageService(serviceId) {
                    if (this.eventCoverageForm.services.includes(serviceId)) {
                        this.eventCoverageForm.services = this.eventCoverageForm.services.filter((id) => id !== serviceId);
                    } else {
                        this.eventCoverageForm.services = [...this.eventCoverageForm.services, serviceId];
                    }
                },

                async submitEventCoverageBooking() {
                    const clientId = this.getClientId();
                    if (!clientId) {
                        this.showToast('Please choose one brand scope before requesting a booking.', 'error');
                        return;
                    }
                    if (!this.eventCoverageForm.eventName || !this.eventCoverageForm.eventName.trim()) {
                        this.showToast('Please provide an event name or title.', 'error');
                        return;
                    }
                    if (!this.eventCoverageForm.services.length) {
                        this.showToast('Select at least one coverage service.', 'error');
                        return;
                    }
                    if (!this.eventCoverageForm.eventDate || !this.eventCoverageForm.location) {
                        this.showToast('Event date and location are required.', 'error');
                        return;
                    }

                    this.submittingEventCoverage = true;
                    try {
                        const response = await fetch(`${API_URL}/event-coverage`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            },
                            body: JSON.stringify({
                                clientId,
                                eventName: this.eventCoverageForm.eventName,
                                services: this.eventCoverageForm.services,
                                eventDate: this.eventCoverageForm.eventDate,
                                location: this.eventCoverageForm.location,
                                details: this.eventCoverageForm.details
                            })
                        });
                        const data = await response.json();
                        if (!response.ok || !data.success) {
                            throw new Error(data.message || 'Failed to submit event coverage booking');
                        }

                        this.eventCoverageForm = {
                            eventName: '',
                            services: [],
                            eventDate: '',
                            location: '',
                            details: ''
                        };
                        await this.loadEventCoverageBookings(this.clientLoadToken);
                        this.showToast('Event coverage request submitted.', 'success');
                    } catch (error) {
                        console.error('Submit event coverage booking error:', error);
                        this.showToast(error.message || 'Unable to submit booking request.', 'error');
                    } finally {
                        this.submittingEventCoverage = false;
                    }
                },

                async reviewEventCoverageBooking(booking, action) {
                    if (!booking?._id) return;
                    const payload = { action };

                    if (action === 'propose') {
                        const proposedDate = prompt('Proposed date/time (YYYY-MM-DDTHH:mm):', booking.eventDate ? booking.eventDate.slice(0, 16) : '');
                        if (proposedDate === null) return;
                        const proposedLocation = prompt('Proposed location:', booking.location || '');
                        if (proposedLocation === null) return;
                        const message = prompt('Message to client (optional):', '');
                        payload.proposedEventDate = proposedDate ? new Date(proposedDate).toISOString() : booking.eventDate;
                        payload.proposedLocation = proposedLocation || booking.location;
                        payload.proposedDetails = booking.details || '';
                        payload.message = message || '';
                    } else if (action === 'estimate') {
                        const amount = prompt('Estimate amount (numbers only):', booking.estimate?.amount != null ? String(booking.estimate.amount) : '');
                        if (amount === null) return;
                        const notes = prompt('Estimate notes (optional):', booking.estimate?.notes || '');
                        payload.estimateAmount = amount ? Number(amount) : null;
                        payload.estimateNotes = notes || '';
                    } else if (action === 'decline') {
                        const reason = prompt('Decline reason (optional):', '');
                        payload.message = reason || '';
                    } else if (action === 'accept') {
                        const note = prompt('Acceptance note (optional):', '');
                        payload.message = note || '';
                    }

                    try {
                        const response = await fetch(`${API_URL}/event-coverage/${booking._id}/review`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            },
                            body: JSON.stringify(payload)
                        });
                        const data = await response.json();
                        if (!response.ok || !data.success) {
                            throw new Error(data.message || 'Failed to update booking');
                        }
                        await this.loadEventCoverageBookings(this.clientLoadToken);
                        this.showToast('Booking updated successfully.', 'success');
                    } catch (error) {
                        console.error('Review booking error:', error);
                        this.showToast(error.message || 'Unable to update booking.', 'error');
                    }
                },

                async respondToEventCoverageProposal(booking, approve) {
                    if (!booking?._id) return;
                    const message = prompt(approve ? 'Add approval note (optional):' : 'Reason for rejecting proposed change (optional):', '');
                    if (message === null) return;
                    try {
                        const response = await fetch(`${API_URL}/event-coverage/${booking._id}/client-response`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            },
                            body: JSON.stringify({
                                approve,
                                message: message || ''
                            })
                        });
                        const data = await response.json();
                        if (!response.ok || !data.success) {
                            throw new Error(data.message || 'Failed to send response');
                        }
                        await this.loadEventCoverageBookings(this.clientLoadToken);
                        this.showToast('Response submitted.', 'success');
                    } catch (error) {
                        console.error('Client response error:', error);
                        this.showToast(error.message || 'Unable to submit response.', 'error');
                    }
                },

                async completeEventCoverageBooking(booking) {
                    if (!booking?._id) return;
                    const driveLink = prompt('Google Drive folder link:', booking.driveFolderUrl || '');
                    if (!driveLink) return;
                    const note = prompt('Completion note (optional):', '');
                    try {
                        const response = await fetch(`${API_URL}/event-coverage/${booking._id}/complete`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            },
                            body: JSON.stringify({
                                driveFolderUrl: driveLink,
                                message: note || ''
                            })
                        });
                        const data = await response.json();
                        if (!response.ok || !data.success) {
                            throw new Error(data.message || 'Failed to complete booking');
                        }
                        await this.loadEventCoverageBookings(this.clientLoadToken);
                        this.showToast('Event marked complete and Drive link saved.', 'success');
                    } catch (error) {
                        console.error('Complete booking error:', error);
                        this.showToast(error.message || 'Unable to complete booking.', 'error');
                    }
                },

                get reportCandidatePosts() {
                    return (this.posts || []).filter((post) => {
                        const status = (post.status || '').toLowerCase();
                        return status === 'published' || status === 'completed' || status === 'posted';
                    });
                },

                initializeReportBuilder() {
                    const today = new Date();
                    const end = this.currentPeriodEnd || today;
                    const start = this.currentPeriodStart || new Date(today.getFullYear(), today.getMonth(), 1);
                    const selectedClientName = this.dashboardData?.client?.brandName || this.dashboardData?.client?.name || 'Client';
                    this.reportBuilder = {
                        ...this.reportBuilder,
                        name: `${selectedClientName} Performance Report`,
                        status: this.reportBuilder.status || 'draft',
                        startDate: this.toDateInputValue(start),
                        endDate: this.toDateInputValue(end),
                        platforms: this.activePlatforms?.length ? [...this.activePlatforms] : [...this.clientPlatforms],
                        selectedPostIds: [],
                        useSelectedPosts: false
                    };
                    this.reportPreview = null;
                },

                toDateInputValue(dateValue) {
                    const date = new Date(dateValue);
                    if (Number.isNaN(date.getTime())) return '';
                    return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
                },

                toggleReportPlatform(platform) {
                    const exists = this.reportBuilder.platforms.includes(platform);
                    if (exists) {
                        this.reportBuilder.platforms = this.reportBuilder.platforms.filter((item) => item !== platform);
                    } else {
                        this.reportBuilder.platforms = [...this.reportBuilder.platforms, platform];
                    }
                },

                toggleReportPostSelection(postId) {
                    const exists = this.reportBuilder.selectedPostIds.includes(postId);
                    if (exists) {
                        this.reportBuilder.selectedPostIds = this.reportBuilder.selectedPostIds.filter((id) => id !== postId);
                    } else {
                        this.reportBuilder.selectedPostIds = [...this.reportBuilder.selectedPostIds, postId];
                    }
                },

                buildReportBuilderPayload() {
                    const clientId = this.getClientId();
                    const startDate = this.reportBuilder.startDate;
                    const endDate = this.reportBuilder.endDate;
                    const payload = {
                        clientId,
                        name: this.reportBuilder.name?.trim() || undefined,
                        status: this.reportBuilder.status || 'draft',
                        type: 'automated',
                        source: 'generated',
                        dateRange: {
                            start: startDate ? new Date(`${startDate}T00:00:00`).toISOString() : null,
                            end: endDate ? new Date(`${endDate}T23:59:59`).toISOString() : null
                        },
                        platforms: this.reportBuilder.platforms || [],
                        selectedPostIds: this.reportBuilder.useSelectedPosts ? (this.reportBuilder.selectedPostIds || []) : []
                    };
                    return payload;
                },

                validateReportBuilderPayload(payload) {
                    if (!payload.clientId) {
                        if (this.user.role === 'client' && (this.availableClients || []).length > 1) {
                            return 'Select a specific brand before generating a report.';
                        }
                        return 'No client selected for report generation.';
                    }
                    if (!payload.dateRange?.start || !payload.dateRange?.end) return 'Please select a valid start and end date.';
                    if (!Array.isArray(payload.platforms) || payload.platforms.length === 0) return 'Please select at least one platform.';
                    if (payload.selectedPostIds && payload.selectedPostIds.length > 0 && !this.reportBuilder.useSelectedPosts) {
                        return 'Invalid post selection state. Please try again.';
                    }
                    return null;
                },

                async previewReportBuilder() {
                    const payload = this.buildReportBuilderPayload();
                    const validationError = this.validateReportBuilderPayload(payload);
                    if (validationError) {
                        this.showToast(validationError, 'error');
                        return;
                    }

                    this.reportPreviewLoading = true;
                    try {
                        const params = new URLSearchParams({
                            clientId: payload.clientId,
                            startDate: payload.dateRange.start,
                            endDate: payload.dateRange.end,
                            platforms: (payload.platforms || []).join(','),
                            selectedPostIds: (payload.selectedPostIds || []).join(',')
                        });
                        const response = await fetch(`${API_URL}/reports/preview?${params.toString()}`, {
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            }
                        });
                        const data = await response.json();
                        if (!response.ok || !data.success) {
                            throw new Error(data.message || 'Failed to preview report');
                        }
                        this.reportPreview = data.data;
                        this.showToast('Report preview updated.', 'success');
                    } catch (error) {
                        console.error('Preview report error:', error);
                        this.showToast(error.message || 'Unable to preview report.', 'error');
                    } finally {
                        this.reportPreviewLoading = false;
                    }
                },

                // Published Posts Functions
                get filteredPosts() {
                    return this.posts.filter(post => {
                        const platformMatch = !this.filterPlatform || (post.platforms && post.platforms.includes(this.filterPlatform));
                        const statusMatch = !this.filterStatus || post.status === this.filterStatus;
                        const contentTypeMatch = !this.filterContentType || post.contentType === this.filterContentType;
                        const campaignMatch = !this.filterCampaign || post.campaignId === this.filterCampaign;
                        
                        // Month filter
                        let monthMatch = true;
                        if (this.filterMonth && post.scheduledDate) {
                            const postMonth = new Date(post.scheduledDate).getMonth() + 1; // 1-12
                            monthMatch = postMonth.toString().padStart(2, '0') === this.filterMonth;
                        }
                        // Year filter
                        let yearMatch = true;
                        if (this.filterYear && post.scheduledDate) {
                            const postYear = new Date(post.scheduledDate).getFullYear().toString();
                            yearMatch = postYear === this.filterYear;
                        }
                        
                        // Search filter
                        let searchMatch = true;
                        if (this.searchQuery) {
                            const query = this.searchQuery.toLowerCase();
                            searchMatch = (post.caption && post.caption.toLowerCase().includes(query)) ||
                                         (post.platforms && post.platforms.some(p => p.toLowerCase().includes(query))) ||
                                         (post.contentType && post.contentType.toLowerCase().includes(query)) ||
                                         (post.status && post.status.toLowerCase().includes(query));
                        }
                        
                        return platformMatch && statusMatch && monthMatch && yearMatch && contentTypeMatch && campaignMatch && searchMatch;
                    });
                },

                async loadPosts(loadToken = null) {
                    const clientId = this.getClientId();
                    const token = loadToken ?? this.clientLoadToken;
                    console.log(`[loadPosts] clientId=${clientId}, selectedClient=${this.selectedClient?.brandName || this.selectedClient?._id}, viewMode=${this.viewMode}`);
                    
                    try {
                        // Load posts from API
                        const postsUrl = clientId
                            ? `${API_URL}/posts?clientId=${encodeURIComponent(clientId)}`
                            : `${API_URL}/posts`;
                        const response = await fetch(postsUrl, {
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            }
                        });
                        
                        if (!response.ok) {
                            throw new Error('Failed to load posts');
                        }
                        
                        const result = await response.json();
                        const apiPosts = result.data || [];
                        const uniqueClientIds = [...new Set(apiPosts.map(p => p.clientId?._id || p.clientId))];
                        console.log(`[loadPosts] API returned ${apiPosts.length} posts, uniqueClientIds:`, uniqueClientIds);

                        if (token !== this.clientLoadToken) return;
                        
                        // Transform API posts to frontend schema
                        // DEBUG: Log first post's full performance object
                        if (apiPosts.length > 0) {
                            const firstPost = apiPosts[0];
                            console.log('=== DEBUG: First post performance data ===');
                            console.log('Post ID:', firstPost._id);
                            console.log('Platform:', firstPost.platform);
                            console.log('Status:', firstPost.status);
                            console.log('Performance object:', JSON.stringify(firstPost.performance, null, 2));
                            console.log('publishedDate:', firstPost.publishedDate);
                            console.log('scheduledDate:', firstPost.scheduledDate);
                        }
                        
                        const mapped = apiPosts.map(post => {
                            const platform = post.platform;
                            const perf = post.performance || {};
                            
                            // Build complete KPIs object with all fields
                            const likes = perf.likes || 0;
                            const comments = perf.comments || 0;
                            const shares = perf.shares || 0;
                            const saves = perf.saves || 0;
                            const engagement = perf.engagement || (likes + comments + shares + saves);
                            const kpis = {
                                [`${platform}_reach`]: perf.reach || 0,
                                [`${platform}_impressions`]: perf.impressions || 0,
                                [`${platform}_engagement`]: engagement,
                                [`${platform}_likes`]: likes,
                                [`${platform}_comments`]: comments,
                                [`${platform}_shares`]: shares,
                                [`${platform}_saves`]: saves,
                                [`${platform}_views`]: perf.views || 0,
                                [`${platform}_watch_time`]: perf.watch_time || 0,
                                [`${platform}_skip_rate`]: perf.skip_rate || 0,
                                [`${platform}_views_followers`]: perf.views_followers || 0,
                                [`${platform}_views_non_followers`]: perf.views_non_followers || 0,
                                [`${platform}_interactions`]: perf.interactions || 0
                            };
                            
                            const mediaUrlRaw = this.extractMediaUrl(post.content?.media);
                            const resolvedUrl = this.resolveMediaUrl(mediaUrlRaw);
                            const canonicalPostDate = post.publishedDate || post.scheduledDate;
                            return {
                                _id: post._id,
                                id: post._id,
                                clientId: post.clientId?._id || post.clientId,
                                platforms: [platform],
                                contentType: this.inferContentType(post),
                                scheduledDate: canonicalPostDate,
                                publishedDate: canonicalPostDate,
                                caption: post.content?.text || '',
                                status: post.status,
                                conceptImage: null,
                                finalContent: resolvedUrl,
                                campaignId: post.campaignId,
                                createdBy: post.createdBy,
                                createdAt: post.createdAt,
                                kpis: kpis,
                                mediaFile: this.extractFileName(resolvedUrl),
                                sourceIds: { [platform]: post._id }
                            };
                        });
                        
                        // Remove duplicates by key (clientId + date + platform + caption/media)
                        if (token !== this.clientLoadToken) return;

                        this.posts = this.dedupePosts(mapped);
                        if (this.mirrorIGToFB) {
                            this.posts = this.mergeMirroredPlatforms(this.posts);
                        }
                        
                        console.log(`Loaded ${this.posts.length} posts from API after dedupe`);
                        
                        // Debug: Show sample of mapped posts with their kpis
                        console.log('Mapped posts sample (first 2):', this.posts.slice(0, 2).map(p => ({
                            id: p._id,
                            platform: p.platforms,
                            status: p.status,
                            scheduledDate: p.scheduledDate,
                            kpis: p.kpis
                        })));
                        
                    } catch (error) {
                        console.error('Error loading posts from API:', error);
                        // Fallback to localStorage if API fails
                        const stored = localStorage.getItem(`client_posts_${clientId}`);
                        if (stored) {
                            try {
                                if (token !== this.clientLoadToken) return;
                                this.posts = JSON.parse(stored);
                            } catch (e) {
                                if (token !== this.clientLoadToken) return;
                                this.posts = [];
                            }
                        } else {
                            if (token !== this.clientLoadToken) return;
                            this.posts = [];
                        }
                    }
                },
                
                inferContentType(post) {
                    // Infer content type from post data
                    const first = Array.isArray(post.content?.media) ? post.content.media[0] : post.content?.media;
                    const src = typeof first === 'string' ? first : (first?.url || first?.src || '');
                    if (Array.isArray(post.content?.media) && post.content.media.length > 1) return 'carousel';
                    if (/\.(mp4|mov|webm)$/i.test(src)) return 'reel';
                    if (Array.isArray(post.content?.media) && post.content.media.length >= 1) return 'static';
                    return 'text';
                },

                savePosts() {
                    const clientId = this.getClientId();
                    
                    try {
                        // Clean up posts data to reduce size - remove large image data for storage
                        const postsToSave = this.posts.map(post => ({
                            ...post,
                            // Keep only essential image data, remove large base64 strings for storage
                            conceptImage: post.conceptImage && post.conceptImage.startsWith('data:') ? 'has_concept_image' : post.conceptImage,
                            finalContent: post.finalContent && post.finalContent.startsWith('data:') ? 'has_final_content' : post.finalContent
                        }));
                        
                        localStorage.setItem(`client_posts_${clientId}`, JSON.stringify(postsToSave));
                    } catch (error) {
                        console.error('Failed to save posts:', error);
                        this.showToast('Failed to save post data. Storage quota exceeded. Please contact support.', 'error');
                    }
                },

                getClientId() {
                    if (this.user.role === 'client') {
                        if (this.selectedViewClient) return this.selectedViewClient;
                        const clientCount = (this.availableClients || []).length;
                        if (clientCount > 1) return '';
                        return this.user?.clientId?._id || this.user?.clientId || '';
                    } else if (this.viewMode === 'client') {
                        return this.selectedViewClient || '';
                    } else if (this.selectedClient) {
                        return this.selectedClient._id;
                    } else {
                        return this.user.clientId;
                    }
                },

                createPost() {
                    // Validation
                    if (!this.postForm.platforms || this.postForm.platforms.length === 0) {
                        this.showToast('Please select at least one platform', 'error');
                        return;
                    }
                    
                    if (!this.postForm.scheduledDate) {
                        this.showToast('Please select a scheduled date', 'error');
                        return;
                    }
                    
                    // Check if editing existing post
                    if (this.postForm.id) {
                        const index = this.posts.findIndex(p => p.id === this.postForm.id);
                        if (index !== -1) {
                            // Update existing post
                            this.posts[index] = {
                                ...this.posts[index],
                                platforms: this.postForm.platforms,
                                contentType: this.postForm.contentType,
                                scheduledDate: this.postForm.scheduledDate,
                                caption: this.postForm.caption,
                                conceptImage: this.postForm.conceptImage,
                                finalContent: this.postForm.finalContent,
                                youtubeUrl: this.postForm.youtubeUrl,
                                campaignId: this.postForm.campaignId,
                                updatedAt: new Date().toISOString()
                            };
                            this.savePosts();
                            this.showCreatePostModal = false;
                            this.resetPostForm();
                            this.showToast('Post updated successfully!', 'success');
                            return;
                        }
                    }
                    
                    // Check for duplicates before creating
                    const dupFound = this.posts.some(p => {
                        const sameDay = p.scheduledDate && new Date(p.scheduledDate).toDateString() === new Date(this.postForm.scheduledDate).toDateString();
                        const platformOverlap = p.platforms?.some(pl => this.postForm.platforms.includes(pl));
                        const captionMatch = (p.caption || '').trim().toLowerCase() === (this.postForm.caption || '').trim().toLowerCase();
                        const mediaMatch = this.extractFileName(p.finalContent) && this.extractFileName(p.finalContent) === this.extractFileName(this.postForm.finalContent);
                        return sameDay && platformOverlap && (captionMatch || mediaMatch);
                    });
                    if (dupFound) {
                        this.showToast('Duplicate post detected for the same day/platform with same caption or media. Please adjust and try again.', 'error');
                        return;
                    }
                    
                    // Create new post
                    const newPost = {
                        id: 'post_' + Date.now(),
                        clientId: this.getClientId(),
                        createdBy: this.user._id,
                        createdAt: new Date().toISOString(),
                        platforms: this.postForm.platforms, // Array of platforms
                        contentType: this.postForm.contentType,
                        scheduledDate: this.postForm.scheduledDate,
                        caption: this.postForm.caption,
                        conceptImage: this.postForm.conceptImage,
                        finalContent: this.resolveMediaUrl(this.postForm.finalContent),
                        youtubeUrl: this.postForm.youtubeUrl,
                        campaignId: this.postForm.campaignId,
                        status: this.postForm.skipConcept ? 'pending_approval' : (this.postForm.conceptImage ? 'concept_review' : 'draft'),
                        clientFeedback: '',
                        captionSuggestion: '',
                        kpis: {}, // Will store KPIs per platform
                        mediaFile: this.extractFileName(this.postForm.finalContent)
                    };
                    if (this.mirrorIGToFB && newPost.platforms.includes('instagram') && !newPost.platforms.includes('facebook')) {
                        newPost.platforms.push('facebook');
                    }
                    
                    this.posts.unshift(newPost);
                    this.savePosts();
                    this.showCreatePostModal = false;
                    this.resetPostForm();
                    this.showToast('Post created successfully!', 'success');
                },

                resetPostForm() {
                    this.postForm = {
                        platforms: ['instagram'],
                        contentType: 'static',
                        scheduledDate: new Date().toISOString().split('T')[0],
                        caption: '',
                        conceptImage: null,
                        finalContent: null,
                        youtubeUrl: '',
                        campaignId: '',
                        skipConcept: false
                    };
                },

                // === Upload Insights Functions ===
                resetInsightsUpload() {
                    this.insightsUploadFile = null;
                    this.insightsUploadFolderFiles = null;
                    this.insightsUploadParsed = null;
                    this.insightsUploadLoading = false;
                    this.insightsUploadResults = null;
                    if (this.$refs.insightsFileInput) this.$refs.insightsFileInput.value = '';
                    if (this.$refs.insightsFolderInput) this.$refs.insightsFolderInput.value = '';
                },

                handleInsightsFileSelect(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.insightsUploadFile = file;
                        this.insightsUploadFolderFiles = null;
                    }
                },

                handleInsightsFolderSelect(event) {
                    const files = Array.from(event.target.files);
                    // Key insight files from IG export folder
                    const insightPaths = [
                        'past_instagram_insights/posts.json',
                        'past_instagram_insights/content_interactions.json',
                        'past_instagram_insights/profiles_reached.json',
                        'past_instagram_insights/audience_insights.json',
                        'past_instagram_insights/live_videos.json',
                    ];
                    
                    const insightFiles = files.filter(f => {
                        const path = f.webkitRelativePath || f.name;
                        return insightPaths.some(ip => path.includes(ip));
                    });
                    
                    if (insightFiles.length > 0) {
                        this.insightsUploadFolderFiles = insightFiles;
                        // Use the folder name as the display name
                        const folderName = (files[0]?.webkitRelativePath || '').split('/')[0] || 'Export folder';
                        this.insightsUploadFile = { name: folderName + ' (' + insightFiles.length + ' insight files)' };
                        this.showToast(`Found ${insightFiles.length} insight file(s) in export folder`, 'success');
                    } else {
                        // Fallback: look for any .json files
                        const jsonFiles = files.filter(f => f.name.endsWith('.json'));
                        if (jsonFiles.length > 0) {
                            this.insightsUploadFolderFiles = jsonFiles.slice(0, 10);
                            const folderName = (files[0]?.webkitRelativePath || '').split('/')[0] || 'Export folder';
                            this.insightsUploadFile = { name: folderName + ' (' + jsonFiles.length + ' JSON files)' };
                            this.showToast(`Found ${jsonFiles.length} JSON file(s) in folder`, 'success');
                        } else {
                            this.showToast('No insight files found in this folder. Make sure it\'s an Instagram/Facebook export.', 'error', 5000);
                        }
                    }
                },

                async parseInsightsFile() {
                    if (!this.insightsUploadFile) return;
                    
                    this.insightsUploadLoading = true;
                    
                    try {
                        let parsed;
                        
                        if (this.insightsUploadFolderFiles && this.insightsUploadFolderFiles.length > 0) {
                            // Parse multiple files from folder upload
                            parsed = await this.parseInsightsFolder(this.insightsUploadFolderFiles);
                        } else {
                            // Parse single file
                            const text = await this.insightsUploadFile.text();
                            let data;
                            if (this.insightsUploadFile.name.endsWith('.csv')) {
                                data = this.parseInsightsCSV(text);
                            } else {
                                data = JSON.parse(text);
                            }
                            parsed = this.normalizeInsightsData(data, this.insightsUploadPlatform);
                        }
                        
                        this.insightsUploadParsed = parsed;
                        
                        const totalMetrics = (parsed.postMetrics?.length || 0);
                        const hasPageMetrics = parsed.pageMetrics && Object.keys(parsed.pageMetrics).some(k => !['startDate','endDate'].includes(k) && parsed.pageMetrics[k] > 0);
                        
                        if (totalMetrics === 0 && !hasPageMetrics) {
                            this.showToast('No metrics found in file. Check the format.', 'warning', 5000);
                        } else {
                            this.showToast(`Found ${totalMetrics} post metrics${hasPageMetrics ? ' + page-level data' : ''}`, 'success');
                        }
                    } catch (error) {
                        console.error('Parse insights error:', error);
                        this.showToast('Error parsing file: ' + error.message, 'error', 5000);
                    } finally {
                        this.insightsUploadLoading = false;
                    }
                },

                async parseInsightsFolder(files) {
                    const result = { pageMetrics: null, postMetrics: [] };
                    
                    for (const file of files) {
                        try {
                            const text = await file.text();
                            const data = JSON.parse(text);
                            const path = file.webkitRelativePath || file.name;
                            
                            // posts.json - per-post metrics (the most important file)
                            if (path.includes('posts.json') && data.organic_insights_posts) {
                                result.postMetrics = this.parseIGNativePostInsights(data.organic_insights_posts);
                            }
                            
                            // profiles_reached.json - page-level reach & impressions
                            if (path.includes('profiles_reached.json') && data.organic_insights_reach) {
                                const reachData = data.organic_insights_reach[0]?.string_map_data || {};
                                const dateRange = this.igKey(reachData, 'Date Range')?.value || '';
                                if (!result.pageMetrics) result.pageMetrics = {};
                                result.pageMetrics.reach = this.parseIGMetricValue(this.igKey(reachData, 'Accounts Reached')?.value);
                                result.pageMetrics.impressions = this.parseIGMetricValue(this.igKey(reachData, 'Impressions')?.value);
                                if (dateRange) {
                                    const dates = this.parseIGDateRange(dateRange);
                                    result.pageMetrics.startDate = dates.startDate;
                                    result.pageMetrics.endDate = dates.endDate;
                                }
                            }
                            
                            // content_interactions.json - page-level engagement
                            if (path.includes('content_interactions.json') && data.organic_insights_interactions) {
                                const d = data.organic_insights_interactions[0]?.string_map_data || {};
                                if (!result.pageMetrics) result.pageMetrics = {};
                                result.pageMetrics.engagement = this.parseIGMetricValue(this.igKey(d, 'Content Interactions')?.value);
                                result.pageMetrics.likes = this.parseIGMetricValue(this.igKey(d, 'Post Likes')?.value) + this.parseIGMetricValue(this.igKey(d, 'Reels Likes')?.value) + this.parseIGMetricValue(this.igKey(d, 'Video Likes')?.value);
                                result.pageMetrics.comments = this.parseIGMetricValue(this.igKey(d, 'Post Comments')?.value) + this.parseIGMetricValue(this.igKey(d, 'Reels Comments')?.value) + this.parseIGMetricValue(this.igKey(d, 'Video Comments')?.value);
                                result.pageMetrics.shares = this.parseIGMetricValue(this.igKey(d, 'Post Shares')?.value) + this.parseIGMetricValue(this.igKey(d, 'Reels Shares')?.value) + this.parseIGMetricValue(this.igKey(d, 'Video Shares')?.value);
                                result.pageMetrics.saves = this.parseIGMetricValue(this.igKey(d, 'Post Saves')?.value) + this.parseIGMetricValue(this.igKey(d, 'Reels Saves')?.value) + this.parseIGMetricValue(this.igKey(d, 'Video Saves')?.value);
                                result.pageMetrics.storyInteractions = this.parseIGMetricValue(this.igKey(d, 'Story Interactions')?.value);
                                result.pageMetrics.storyReplies = this.parseIGMetricValue(this.igKey(d, 'Story Replies')?.value);
                                result.pageMetrics.accountsEngaged = this.parseIGMetricValue(this.igKey(d, 'Accounts Engaged')?.value);
                            }
                            
                            // audience_insights.json - follower data
                            if (path.includes('audience_insights.json') && data.organic_insights_audience) {
                                const audData = data.organic_insights_audience[0]?.string_map_data || {};
                                if (!result.pageMetrics) result.pageMetrics = {};
                                result.pageMetrics.followers = this.parseIGMetricValue(this.igKey(audData, 'Followers')?.value);
                            }
                            
                        } catch (e) {
                            console.warn('Skipping file:', file.name, e.message);
                        }
                    }
                    
                    return result;
                },

                igKey(obj, key) {
                    if (!obj) return undefined;
                    if (obj[key] !== undefined) return obj[key];
                    const lk = key.toLowerCase();
                    for (const k of Object.keys(obj)) {
                        if (k.toLowerCase() === lk) return obj[k];
                    }
                    return undefined;
                },

                parseIGNativePostInsights(posts) {
                    return posts.map(post => {
                        const media = this.igKey(post.media_map_data, 'Media Thumbnail') || {};
                        const metrics = post.string_map_data || {};
                        const caption = media.title || '';
                        const tsEntry = this.igKey(metrics, 'Creation Timestamp');
                        const timestamp = tsEntry?.timestamp || media.creation_timestamp;
                        const publishedDate = timestamp ? new Date(timestamp * 1000).toISOString() : null;
                        
                        const likes = this.parseIGMetricValue(this.igKey(metrics, 'Likes')?.value);
                        const comments = this.parseIGMetricValue(this.igKey(metrics, 'Comments')?.value);
                        const shares = this.parseIGMetricValue(this.igKey(metrics, 'Shares')?.value);
                        const saves = this.parseIGMetricValue(this.igKey(metrics, 'Saves')?.value);
                        
                        return {
                            publishedDate,
                            caption: this.decodeIGText(caption),
                            platformPostId: null,
                            permalink: '',
                            mediaUrl: media.uri || '',
                            reach: this.parseIGMetricValue(this.igKey(metrics, 'Accounts Reached')?.value),
                            impressions: this.parseIGMetricValue(this.igKey(metrics, 'Impressions')?.value),
                            engagement: likes + comments + shares + saves,
                            likes,
                            comments,
                            shares,
                            saves,
                            views: this.parseIGMetricValue(this.igKey(metrics, 'Views')?.value) || this.parseIGMetricValue(this.igKey(metrics, 'Plays')?.value),
                            watch_time: 0,
                        };
                    });
                },

                parseIGMetricValue(val) {
                    if (!val) return 0;
                    return parseInt(String(val).replace(/,/g, '')) || 0;
                },

                decodeIGText(text) {
                    if (!text) return '';
                    try {
                        // IG exports use UTF-8 byte sequences stored as latin1 chars
                        const bytes = new Uint8Array([...text].map(c => c.charCodeAt(0)));
                        return new TextDecoder('utf-8').decode(bytes);
                    } catch (e) {
                        return text;
                    }
                },

                parseIGDateRange(rangeStr) {
                    // Parses "Nov 11 - Feb 8" style date ranges
                    const currentYear = new Date().getFullYear();
                    const parts = rangeStr.split(' - ');
                    let startDate = null, endDate = null;
                    try {
                        if (parts.length === 2) {
                            endDate = new Date(parts[1].trim() + ', ' + currentYear).toISOString();
                            startDate = new Date(parts[0].trim() + ', ' + currentYear).toISOString();
                            // If start is after end, start was previous year
                            if (new Date(startDate) > new Date(endDate)) {
                                startDate = new Date(parts[0].trim() + ', ' + (currentYear - 1)).toISOString();
                            }
                        }
                    } catch (e) { }
                    return { startDate, endDate };
                },

                parseInsightsCSV(csvText) {
                    const lines = csvText.split('\n').filter(l => l.trim());
                    if (lines.length < 2) return [];
                    const headers = lines[0].split(',').map(h => h.trim().toLowerCase());
                    const rows = [];
                    for (let i = 1; i < lines.length; i++) {
                        const values = lines[i].split(',').map(v => v.trim());
                        const row = {};
                        headers.forEach((h, idx) => { row[h] = values[idx] || ''; });
                        rows.push(row);
                    }
                    return rows;
                },

                normalizeInsightsData(data, platform) {
                    const result = { pageMetrics: null, postMetrics: [] };
                    
                    // IG native export format: { organic_insights_posts: [...] }
                    if (data.organic_insights_posts && Array.isArray(data.organic_insights_posts)) {
                        result.postMetrics = this.parseIGNativePostInsights(data.organic_insights_posts);
                        // Compute aggregate page metrics from posts
                        if (result.postMetrics.length > 0) {
                            result.pageMetrics = this.computeAggregateMetrics(result.postMetrics);
                        }
                        return result;
                    }
                    
                    // IG native reach file: { organic_insights_reach: [...] }
                    if (data.organic_insights_reach) {
                        const reachData = data.organic_insights_reach[0]?.string_map_data || {};
                        result.pageMetrics = {
                            reach: this.parseIGMetricValue(reachData['Accounts reached']?.value),
                            impressions: this.parseIGMetricValue(reachData['Impressions']?.value),
                        };
                        const dateRange = reachData['Date range']?.value;
                        if (dateRange) {
                            const dates = this.parseIGDateRange(dateRange);
                            result.pageMetrics.startDate = dates.startDate;
                            result.pageMetrics.endDate = dates.endDate;
                        }
                        return result;
                    }
                    
                    // IG native interactions file: { organic_insights_interactions: [...] }
                    if (data.organic_insights_interactions) {
                        const interData = data.organic_insights_interactions[0]?.string_map_data || {};
                        result.pageMetrics = {
                            engagement: this.parseIGMetricValue(interData['Content interactions']?.value),
                            likes: this.parseIGMetricValue(interData['Post likes']?.value) + this.parseIGMetricValue(interData['Reels likes']?.value),
                            comments: this.parseIGMetricValue(interData['Post comments']?.value) + this.parseIGMetricValue(interData['Reels comments']?.value),
                            shares: this.parseIGMetricValue(interData['Post shares']?.value) + this.parseIGMetricValue(interData['Reels shares']?.value),
                            saves: this.parseIGMetricValue(interData['Post saves']?.value) + this.parseIGMetricValue(interData['Reels saves']?.value),
                        };
                        return result;
                    }
                    
                    // Format: Array of post objects (simple JSON/CSV export)
                    if (Array.isArray(data)) {
                        result.postMetrics = data.map(row => ({
                            publishedDate: row.date || row.published_date || row.publishedDate || row.timestamp || null,
                            caption: row.caption || row.text || row.content || row.description || '',
                            platformPostId: row.post_id || row.platformPostId || row.id || null,
                            permalink: row.permalink || row.url || row.link || '',
                            mediaUrl: row.media_url || row.mediaUrl || row.image || '',
                            reach: parseInt(row.reach) || 0,
                            impressions: parseInt(row.impressions) || 0,
                            engagement: parseInt(row.engagement) || parseInt(row.total_engagement) || 0,
                            likes: parseInt(row.likes) || parseInt(row.reactions) || 0,
                            comments: parseInt(row.comments) || parseInt(row.replies) || 0,
                            shares: parseInt(row.shares) || parseInt(row.reposts) || 0,
                            saves: parseInt(row.saves) || parseInt(row.bookmarks) || 0,
                            views: parseInt(row.views) || parseInt(row.video_views) || parseInt(row.plays) || 0,
                            watch_time: parseInt(row.watch_time) || parseInt(row.total_watch_time) || 0,
                        }));
                        if (result.postMetrics.length > 0) {
                            result.pageMetrics = this.computeAggregateMetrics(result.postMetrics);
                        }
                        return result;
                    }
                    
                    // Format: Object with nested page/post data
                    if (typeof data === 'object' && data !== null) {
                        if (data.page_metrics || data.pageMetrics || data.overview || data.summary) {
                            const pm = data.page_metrics || data.pageMetrics || data.overview || data.summary;
                            result.pageMetrics = {
                                reach: parseInt(pm.reach || pm.accounts_reached || 0),
                                impressions: parseInt(pm.impressions || 0),
                                engagement: parseInt(pm.engagement || pm.interactions || pm.total_engagement || 0),
                                likes: parseInt(pm.likes || pm.reactions || 0),
                                comments: parseInt(pm.comments || 0),
                                shares: parseInt(pm.shares || 0),
                                saves: parseInt(pm.saves || 0),
                                views: parseInt(pm.views || pm.video_views || 0),
                                startDate: pm.start_date || pm.startDate || pm.period_start || null,
                                endDate: pm.end_date || pm.endDate || pm.period_end || null,
                            };
                        }
                        
                        const postsArray = data.posts || data.post_metrics || data.postMetrics || data.content || data.media || data.data;
                        if (Array.isArray(postsArray)) {
                            result.postMetrics = postsArray.map(row => ({
                                publishedDate: row.date || row.published_date || row.publishedDate || row.timestamp || row.created_time || null,
                                caption: row.caption || row.text || row.message || row.content || row.description || '',
                                platformPostId: row.post_id || row.platformPostId || row.id || null,
                                permalink: row.permalink || row.permalink_url || row.url || '',
                                mediaUrl: row.media_url || row.mediaUrl || row.image || row.full_picture || '',
                                reach: parseInt(row.reach) || 0,
                                impressions: parseInt(row.impressions) || 0,
                                engagement: parseInt(row.engagement) || parseInt(row.total_engagement) || parseInt(row.post_engaged_users) || 0,
                                likes: parseInt(row.likes) || parseInt(row.like_count) || parseInt(row.reactions) || 0,
                                comments: parseInt(row.comments) || parseInt(row.comments_count) || 0,
                                shares: parseInt(row.shares) || parseInt(row.share_count) || 0,
                                saves: parseInt(row.saves) || parseInt(row.saved) || 0,
                                views: parseInt(row.views) || parseInt(row.video_views) || parseInt(row.plays) || 0,
                                watch_time: parseInt(row.watch_time) || 0,
                            }));
                        }
                        
                        if (result.postMetrics.length > 0 && !result.pageMetrics) {
                            result.pageMetrics = this.computeAggregateMetrics(result.postMetrics);
                        }
                        return result;
                    }
                    
                    return result;
                },

                computeAggregateMetrics(postMetrics) {
                    const agg = { reach: 0, impressions: 0, engagement: 0, likes: 0, comments: 0, shares: 0, saves: 0, views: 0 };
                    postMetrics.forEach(pm => {
                        agg.reach += pm.reach || 0;
                        agg.impressions += pm.impressions || 0;
                        agg.engagement += pm.engagement || ((pm.likes || 0) + (pm.comments || 0) + (pm.shares || 0) + (pm.saves || 0));
                        agg.likes += pm.likes || 0;
                        agg.comments += pm.comments || 0;
                        agg.shares += pm.shares || 0;
                        agg.saves += pm.saves || 0;
                        agg.views += pm.views || 0;
                    });
                    const dates = postMetrics.filter(pm => pm.publishedDate).map(pm => new Date(pm.publishedDate)).sort((a, b) => a - b);
                    if (dates.length > 0) {
                        agg.startDate = dates[0].toISOString();
                        agg.endDate = dates[dates.length - 1].toISOString();
                    }
                    return agg;
                },

                async submitInsightsUpload() {
                    if (!this.insightsUploadParsed) return;

                    const uploadClientId = this.getClientId();
                    const uploadToken = this.clientLoadToken;
                    console.log(`[submitInsightsUpload] uploadClientId=${uploadClientId}, selectedClient=${this.selectedClient?.brandName || this.selectedClient?._id}`);
                    if (!uploadClientId) {
                        this.showToast('Please select a client first', 'error');
                        return;
                    }
                    
                    this.insightsUploadLoading = true;
                    
                    try {
                        const response = await fetch(`${API_URL}/posts/upload-insights`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            },
                            body: JSON.stringify({
                                clientId: uploadClientId,
                                platform: this.insightsUploadPlatform,
                                pageMetrics: this.insightsUploadParsed.pageMetrics,
                                postMetrics: this.insightsUploadParsed.postMetrics
                            })
                        });
                        
                        const result = await response.json();
                        
                        // If the user switched brands while the upload was in-flight, don't apply results to the wrong client
                        if (uploadToken !== this.clientLoadToken || uploadClientId !== this.getClientId()) {
                            return;
                        }

                        if (result.success) {
                            this.insightsUploadResults = result.results;
                            // Save page-level metrics and followers from parsed data
                            const pm = this.insightsUploadParsed?.pageMetrics;
                            if (pm) {
                                if (pm.followers && pm.followers > 0) {
                                    this.savePlatformFollowers(this.insightsUploadPlatform, pm.followers, uploadClientId);
                                }
                                this.savePageLevelMetrics(this.insightsUploadPlatform, pm, uploadClientId);
                            }
                            this.showToast('Insights uploaded successfully!', 'success');
                        } else {
                            this.showToast('Upload failed: ' + (result.message || 'Unknown error'), 'error', 5000);
                        }
                    } catch (error) {
                        console.error('Submit insights error:', error);
                        this.showToast('Error uploading insights: ' + error.message, 'error', 5000);
                    } finally {
                        this.insightsUploadLoading = false;
                    }
                },
                async deleteAllClientPosts() {
                    const clientId = this.getClientId();
                    const clientName = this.selectedClient?.brandName || this.selectedClient?.companyName || clientId;
                    if (!confirm(`Delete ALL posts for "${clientName}"? This cannot be undone.`)) return;
                    try {
                        const response = await fetch(`${API_URL}/posts/client/${clientId}`, {
                            method: 'DELETE',
                            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
                        });
                        const result = await response.json();
                        if (result.success) {
                            this.showToast(`Deleted ${result.deletedCount} posts for ${clientName}`, 'success');
                            localStorage.removeItem(`client_posts_${clientId}`);
                            localStorage.removeItem(`page_level_metrics_${clientId}`);
                            localStorage.removeItem(`platform_followers_${clientId}`);
                            await this.reloadClientScopedData();
                        } else {
                            this.showToast('Delete failed: ' + (result.message || 'Unknown error'), 'error');
                        }
                    } catch (error) {
                        console.error('Delete posts error:', error);
                        this.showToast('Error deleting posts: ' + error.message, 'error');
                    }
                },
                // === End Upload Insights Functions ===

                editPost(post) {
                    // Load post data into form for editing
                    this.postForm = {
                        id: post.id,
                        platforms: [...post.platforms],
                        contentType: post.contentType,
                        scheduledDate: post.scheduledDate,
                        caption: post.caption || '',
                        conceptImage: post.conceptImage || null,
                        finalContent: post.finalContent || null,
                        youtubeUrl: post.youtubeUrl || '',
                        campaignId: post.campaignId || '',
                        skipConcept: post.skipConcept || false
                    };
                    this.showCreatePostModal = true;
                },

                togglePlatform(platform) {
                    const index = this.postForm.platforms.indexOf(platform);
                    if (index > -1) {
                        this.postForm.platforms.splice(index, 1);
                    } else {
                        this.postForm.platforms.push(platform);
                    }
                },

                async deletePost(post) {
                    if (confirm(`Are you sure you want to delete this post?\n\nCaption: ${post.caption || 'No caption'}\nScheduled: ${new Date(post.scheduledDate).toLocaleDateString()}\n\nThis action cannot be undone.`)) {
                        try {
                            // Delete from database via API
                            const response = await fetch(`${API_URL}/posts/${post._id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                                }
                            });
                            
                            if (response.ok) {
                                // Remove from local array
                                const index = this.posts.findIndex(p => p.id === post.id || p._id === post._id);
                                if (index !== -1) {
                                    this.posts.splice(index, 1);
                                }
                                this.showToast('Post deleted successfully from database!', 'success');
                                this.updateDashboardMetrics();
                            } else {
                                const error = await response.json();
                                this.showToast('Failed to delete post: ' + (error.message || 'Unknown error'), 'error');
                            }
                        } catch (error) {
                            console.error('Delete error:', error);
                            this.showToast('Error deleting post: ' + error.message, 'error');
                        }
                    }
                },

                removeStrayCheckboxes() {
                    // Aggressive removal of any stray checkboxes
                    setTimeout(() => {
                        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                            // Skip checkboxes that are part of the actual UI
                            if (checkbox.classList.contains('w-5') || 
                                checkbox.classList.contains('rounded') ||
                                checkbox.closest('.sidebar') ||
                                checkbox.closest('aside') ||
                                checkbox.closest('[x-data]')) {
                                return;
                            }
                            
                            // Remove any other checkboxes
                            const rect = checkbox.getBoundingClientRect();
                            if (rect.top < 150 && rect.left < 150) {
                                checkbox.remove();
                            }
                        });
                    }, 100);
                    
                    // Additional sweep after 500ms
                    setTimeout(() => {
                        document.querySelectorAll('body > input[type="checkbox"]').forEach(cb => cb.remove());
                    }, 500);
                },

                handleRouting() {
                    // Get hash from URL (e.g., #content-calendar)
                    const hash = window.location.hash.slice(1) || 'overview';
                    
                    // Map hash to view names
                    const routeMap = {
                        'overview': 'dashboard',
                        'dashboard': 'dashboard', // Support old links
                        'content-calendar': 'calendar',
                        'reports': 'reports',
                        'manage-reports': 'reports',
                        'event-coverage': 'eventCoverage',
                        'events': 'eventCoverage'
                    };
                    this.activeView = routeMap[hash] || 'dashboard';
                },

                togglePostSelection(postId, event) {
                    // Only toggle if user is admin or brand_rep
                    if (this.user.role !== 'admin' && this.user.role !== 'brand_rep') {
                        return;
                    }
                    
                    // Don't select if clicking on a button or interactive element
                    if (event && (event.target.tagName === 'BUTTON' || event.target.closest('button') || event.target.tagName === 'VIDEO')) {
                        return;
                    }
                    
                    const index = this.selectedPosts.indexOf(postId);
                    if (index > -1) {
                        this.selectedPosts.splice(index, 1);
                    } else {
                        this.selectedPosts.push(postId);
                    }
                },

                selectAllPosts() {
                    // Simply select all visible filtered posts
                    this.selectedPosts = this.filteredPosts.map(p => p.id);
                },

                async bulkDeletePosts() {
                    if (this.selectedPosts.length === 0) {
                        this.showToast('No posts selected', 'error');
                        return;
                    }

                    if (confirm(`Are you sure you want to delete ${this.selectedPosts.length} post(s) from the database?\n\nThis action cannot be undone.`)) {
                        const count = this.selectedPosts.length;
                        let deleted = 0;
                        let errors = 0;
                        
                        // Delete each post from database
                        for (const postId of this.selectedPosts) {
                            const post = this.posts.find(p => p.id === postId || p._id === postId);
                            if (!post || !post._id) continue;
                            
                            try {
                                const response = await fetch(`${API_URL}/posts/${post._id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                                    }
                                });
                                
                                if (response.ok) {
                                    deleted++;
                                    // Remove from local array
                                    const index = this.posts.findIndex(p => p._id === post._id);
                                    if (index !== -1) {
                                        this.posts.splice(index, 1);
                                    }
                                } else {
                                    errors++;
                                }
                            } catch (error) {
                                console.error('Delete error:', error);
                                errors++;
                            }
                        }
                        
                        this.selectedPosts = [];
                        this.updateDashboardMetrics();
                        this.showToast(`Deleted ${deleted} post(s) from database!${errors > 0 ? ` ${errors} failed to delete.` : ''}`, 'success');
                    }
                },

                viewPost(post) {
                    this.currentPost = {...post};
                    this.showViewPostModal = true;
                },

                updatePostStatus(newStatus) {
                    const index = this.posts.findIndex(p => p.id === this.currentPost.id);
                    if (index !== -1) {
                        this.posts[index].status = newStatus;
                        this.savePosts();
                        this.showViewPostModal = false;
                        this.showToast('Post status updated!', 'success');
                    }
                },

                reviewPost(post) {
                    this.currentPost = {
                        ...post,
                        clientFeedback: post.clientFeedback || '',
                        captionSuggestion: post.captionSuggestion || ''
                    };
                    this.showReviewPostModal = true;
                },

                approvePost() {
                    const index = this.posts.findIndex(p => p.id === this.currentPost.id);
                    if (index !== -1) {
                        this.posts[index].status = 'approved';
                        this.posts[index].approvedAt = new Date().toISOString();
                        this.posts[index].approvedBy = this.user._id;
                        this.savePosts();
                        this.showReviewPostModal = false;
                        this.showToast('Post approved!', 'success');
                    }
                },

                requestReview() {
                    const index = this.posts.findIndex(p => p.id === this.currentPost.id);
                    if (index !== -1) {
                        this.posts[index].status = 'needs_review';
                        this.posts[index].clientFeedback = this.currentPost.clientFeedback;
                        this.posts[index].captionSuggestion = this.currentPost.captionSuggestion;
                        this.savePosts();
                        this.showReviewPostModal = false;
                        this.showToast('Review requested!', 'success');
                    }
                },

                markAsPosted(post) {
                    const postUrl = prompt('Enter the post URL (optional):');
                    const index = this.posts.findIndex(p => p.id === post.id);
                    if (index !== -1) {
                        this.posts[index].status = 'posted';
                        this.posts[index].postedAt = new Date().toISOString();
                        this.posts[index].postedBy = this.user._id;
                        this.posts[index].postUrl = postUrl || '';
                        this.savePosts();
                        this.showToast('Post marked as posted!', 'success');
                    }
                },

                addPostKPIs(post) {
                    this.currentPost = {...post};
                    this.showPostKPIsModal = true;
                },

                savePostKPIs() {
                    const index = this.posts.findIndex(p => p.id === this.currentPost.id);
                    if (index !== -1) {
                        this.posts[index].kpis = this.currentPost.kpis;
                        this.posts[index].status = 'completed';
                        this.savePosts();
                        this.showPostKPIsModal = false;
                        
                        // Automatically update dashboard metrics
                        this.updateDashboardMetrics();
                        
                        this.showToast('KPIs saved successfully! Dashboard metrics updated.', 'success');
                    }
                },

                handleFileUpload(event, field) {
                    const file = event.target.files[0];
                    if (file) {
                        // Check file size (limit to 2MB to prevent localStorage issues)
                        if (file.size > 2 * 1024 * 1024) {
                            this.showToast('File size too large. Please choose a file smaller than 2MB.', 'error');
                            return;
                        }
                        
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            if (field === 'concept') {
                                this.postForm.conceptImage = e.target.result;
                            } else if (field === 'final') {
                                this.postForm.finalContent = e.target.result;
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                },

                calculateTotalKPI(metric) {
                    let total = 0;
                    this.posts.filter(p => p.status === 'completed').forEach(post => {
                        if (post.kpis && post.platforms) {
                            post.platforms.forEach(platform => {
                                const key = `${platform}_${metric}`;
                                total += parseInt(post.kpis[key] || 0);
                            });
                        }
                    });
                    return total;
                },

                // Campaign Functions
                loadCampaigns() {
                    const clientId = this.getClientId();
                    const stored = localStorage.getItem(`client_campaigns_${clientId}`);
                    this.campaigns = stored ? JSON.parse(stored) : [];
                },

                saveCampaigns() {
                    const clientId = this.getClientId();
                    localStorage.setItem(`client_campaigns_${clientId}`, JSON.stringify(this.campaigns));
                },

                getCampaignName(campaignId) {
                    const campaign = this.campaigns.find(c => c.id === campaignId);
                    return campaign ? campaign.name : '';
                },

                createCampaign() {
                    const name = prompt('Enter campaign name:');
                    if (name && name.trim()) {
                        const newCampaign = {
                            id: 'campaign_' + Date.now(),
                            name: name.trim(),
                            createdAt: new Date().toISOString(),
                            clientId: this.getClientId()
                        };
                        this.campaigns.push(newCampaign);
                        this.saveCampaigns();
                        this.showCreateCampaignModal = false;
                    }
                },

                // Monthly Post Tracker Functions
                getProgressMonthName() {
                    const now = new Date();
                    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                    
                    if (this.progressViewMode === 'month') {
                        return `${monthNames[now.getMonth()]} ${now.getFullYear()}`;
                    } else {
                        return `Year ${now.getFullYear()}`;
                    }
                },

                getFilteredMonthPostCount() {
                    const now = new Date();
                    const currentYear = now.getFullYear();
                    const currentMonth = now.getMonth();
                    
                    // Count COMPLETED posts (published posts are transformed to 'completed' status in frontend)
                    return this.posts.filter(post => {
                        if (post.status !== 'completed') return false;
                        
                        // Use scheduledDate as the post date (this is when it was published)
                        if (!post.scheduledDate) return false;
                        const postDate = new Date(post.scheduledDate);
                        
                        if (this.progressViewMode === 'month') {
                            // Count posts for current month only
                            return postDate.getMonth() === currentMonth && postDate.getFullYear() === currentYear;
                        } else {
                            // Count posts for current year
                            if (this.clientStartDate) {
                                const startDate = new Date(this.clientStartDate);
                                if (startDate.getFullYear() === currentYear) {
                                    return postDate >= startDate && postDate.getFullYear() === currentYear;
                                }
                            }
                            return postDate.getFullYear() === currentYear;
                        }
                    }).length;
                },

                // Progress helpers
                getProgressPostCount() {
                    return this.getFilteredMonthPostCount();
                },
                getProgressTargetCount() {
                    const now = new Date();
                    const currentYear = now.getFullYear();
                    
                    if (this.progressViewMode === 'month') {
                        // Return monthly target
                        return this.monthlyPostTarget || 0;
                    } else {
                        // Calculate full year target (12 months)
                        let months = 12;
                        
                        // Adjust for client start date if within current year
                        if (this.clientStartDate) {
                            const startDate = new Date(this.clientStartDate);
                            if (startDate.getFullYear() === currentYear) {
                                const startMonth = startDate.getMonth();
                                // Count from start month to end of year
                                months = 12 - startMonth;
                            }
                        }
                        
                        return (this.monthlyPostTarget || 0) * months;
                    }
                },
                getAheadBehindText() {
                    const diff = this.getProgressTargetCount() - this.getProgressPostCount();
                    if (diff > 0) return `${diff} posts remaining`;
                    if (diff < 0) return `${Math.abs(diff)} posts ahead`;
                    return 'On target';
                },
                getAvailableYears() {
                    const years = new Set();
                    this.posts.forEach(p => {
                        if (p.scheduledDate) years.add(new Date(p.scheduledDate).getFullYear());
                    });
                    years.add(new Date().getFullYear());
                    return Array.from(years).sort((a,b)=>b-a).map(String);
                },

                // Media helpers and dedupe
                resolveMediaUrl(url) {
                    if (!url) return null;
                    if (typeof url !== 'string') return null;
                    if (url.startsWith('data:')) return url;
                    if (/^https?:\/\//i.test(url)) return url;
                    if (url.startsWith('/')) return url; // absolute path already
                    const file = this.extractFileName(url);
                    const slug = this.getSelectedClientSlug();
                    return `${window.location.origin}/wp-content/media/${slug}/${file}`;
                },
                extractMediaUrl(media) {
                    if (!media) return null;
                    if (Array.isArray(media) && media.length) {
                        const first = media[0];
                        return typeof first === 'string' ? first : (first?.url || first?.src || first?.path || null);
                    }
                    return typeof media === 'string' ? media : (media?.url || null);
                },
                extractFileName(url) {
                    if (!url || typeof url !== 'string') return '';
                    const parts = url.split('?')[0].split('#')[0].split('/');
                    return parts[parts.length - 1] || '';
                },
                getSelectedClientSlug() {
                    const name = (this.selectedClient?.brandName || this.selectedClient?.companyName || this.user.clientId?.brandName || 'client').toLowerCase();
                    return name.replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
                },
                dedupePosts(list) {
                    const map = new Map();
                    list.forEach(p => {
                        const day = p.scheduledDate ? new Date(p.scheduledDate).toDateString() : '';
                        const key = [p.clientId, day, (p.platforms||[]).join(','), (p.caption||'').trim().toLowerCase(), this.extractFileName(p.finalContent)].join('|');
                        if (!map.has(key)) map.set(key, p);
                    });
                    return Array.from(map.values());
                },

                // Merge IG/FB posts for same content and optionally mirror IG-only posts to show FB icon
                mergeMirroredPlatforms(list) {
                    const map = new Map();
                    const keyFor = (p) => {
                        const day = p.scheduledDate ? new Date(p.scheduledDate).toDateString() : '';
                        const ident = ((p.caption || '').trim().toLowerCase()) || this.extractFileName(p.finalContent);
                        return [p.clientId, day, ident].join('|');
                    };
                    list.forEach(p => {
                        const key = keyFor(p);
                        const existing = map.get(key);
                        if (!existing) {
                            // Ensure arrays/objects present
                            p.platforms = Array.from(new Set(p.platforms || []));
                            p.kpis = p.kpis || {};
                            p.sourceIds = p.sourceIds || {};
                            map.set(key, p);
                        } else {
                            // Merge platforms
                            existing.platforms = Array.from(new Set([...(existing.platforms||[]), ...(p.platforms||[])]));
                            // Prefer IG media if existing has none
                            if (!existing.finalContent && p.finalContent) existing.finalContent = p.finalContent;
                            // Merge KPIs and source IDs
                            existing.kpis = { ...(existing.kpis || {}), ...(p.kpis || {}) };
                            existing.sourceIds = { ...(existing.sourceIds || {}), ...(p.sourceIds || {}) };
                            // Choose a stable id
                            if (!existing._id && p._id) { existing._id = p._id; existing.id = p._id; }
                        }
                    });
                    const merged = Array.from(map.values());
                    // Mirror IG to FB iconically (no media required) if enabled
                    if (this.mirrorIGToFB && this.clientPlatforms.includes('facebook')) {
                        merged.forEach(p => {
                            if (p.platforms.includes('instagram') && !p.platforms.includes('facebook')) {
                                p.platforms.push('facebook');
                            }
                        });
                    }
                    return merged;
                },

                downloadAsset(post) {
                    try {
                        const link = document.createElement('a');
                        link.href = post.finalContent;
                        const fname = this.extractFileName(post.finalContent) || `asset-${post.id}.jpg`;
                        link.download = fname;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    } catch (e) {
                        console.error('Download error', e);
                        this.showToast('Unable to download asset.', 'error');
                    }
                },

                calculateFilteredKPI(metric, usePreviousPeriod = false) {
                    let total = 0;
                    const filterFunc = usePreviousPeriod ? this.isPostInPreviousPeriod.bind(this) : this.isPostInDateRange.bind(this);
                    
                    this.posts.filter(p => (p.status === 'completed' || p.status === 'published') && filterFunc(p)).forEach(post => {
                        if (post.kpis && post.platforms) {
                            post.platforms.forEach(platform => {
                                // Only count if platform is active in filter
                                if (this.activePlatforms.includes(platform)) {
                                    const key = `${platform}_${metric}`;
                                    total += parseInt(post.kpis[key] || 0);
                                }
                            });
                        }
                    });
                    
                    // Use page-level metrics as a floor only when per-post sums are lower
                    // (page-level covers reels, stories, etc. that may not be in per-post data)
                    if (!usePreviousPeriod) {
                        const pageTotal = this.getPageLevelTotal(metric);
                        if (pageTotal > total) {
                            total = pageTotal;
                        }
                    }
                    return total;
                },
                
                calculatePercentageChange(currentValue, previousValue) {
                    if (previousValue === 0) {
                        return currentValue > 0 ? 100 : 0;
                    }
                    return ((currentValue - previousValue) / previousValue) * 100;
                },
                
                formatPercentageChange(change) {
                    const sign = change > 0 ? '+' : '';
                    return `${sign}${change.toFixed(1)}%`;
                },

                togglePlatformFilter(platform) {
                    const index = this.activePlatforms.indexOf(platform);
                    if (index > -1) {
                        // Don't allow removing all platforms
                        if (this.activePlatforms.length > 1) {
                            this.activePlatforms.splice(index, 1);
                        }
                    } else {
                        this.activePlatforms.push(platform);
                    }
                    // Recalculate metrics
                    this.updateDashboardMetrics();
                },

                // Date range filtering
                setDateRange(range) {
                    this.dateRange = range;
                    this.showCustomDatePicker = false;
                    
                    const now = new Date();
                    const currentYear = now.getFullYear();
                    const currentMonth = now.getMonth();
                    
                    switch(range) {
                        case 'ytd':
                            this.currentPeriodStart = new Date(currentYear, 0, 1);
                            this.currentPeriodEnd = now;
                            this.previousPeriodStart = new Date(currentYear - 1, 0, 1);
                            this.previousPeriodEnd = new Date(currentYear - 1, currentMonth, now.getDate());
                            this.currentPeriodLabel = `Jan 1, ${currentYear} - ${now.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}`;
                            break;
                            
                        case 'current_month':
                            this.currentPeriodStart = new Date(currentYear, currentMonth, 1);
                            this.currentPeriodEnd = now;
                            this.previousPeriodStart = new Date(currentYear, currentMonth - 1, 1);
                            this.previousPeriodEnd = new Date(currentYear, currentMonth, 0); // Last day of previous month
                            this.currentPeriodLabel = now.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                            break;
                            
                        case 'last_month':
                            const lastMonthStart = new Date(currentYear, currentMonth - 1, 1);
                            const lastMonthEnd = new Date(currentYear, currentMonth, 0);
                            this.currentPeriodStart = lastMonthStart;
                            this.currentPeriodEnd = lastMonthEnd;
                            this.previousPeriodStart = new Date(currentYear, currentMonth - 2, 1);
                            this.previousPeriodEnd = new Date(currentYear, currentMonth - 1, 0);
                            this.currentPeriodLabel = lastMonthStart.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                            break;
                            
                        case 'last_3_months':
                            this.currentPeriodStart = new Date(currentYear, currentMonth - 2, 1);
                            this.currentPeriodEnd = now;
                            this.previousPeriodStart = new Date(currentYear, currentMonth - 5, 1);
                            this.previousPeriodEnd = new Date(currentYear, currentMonth - 2, 0);
                            const threeMonthsAgo = new Date(currentYear, currentMonth - 2, 1);
                            this.currentPeriodLabel = `${threeMonthsAgo.toLocaleDateString('en-US', { month: 'short' })} - ${now.toLocaleDateString('en-US', { month: 'short', year: 'numeric' })}`;
                            break;
                            
                        case 'last_year':
                            this.currentPeriodStart = new Date(currentYear - 1, 0, 1);
                            this.currentPeriodEnd = new Date(currentYear - 1, 11, 31, 23, 59, 59);
                            this.previousPeriodStart = new Date(currentYear - 2, 0, 1);
                            this.previousPeriodEnd = new Date(currentYear - 2, 11, 31, 23, 59, 59);
                            this.currentPeriodLabel = `Jan 1 - Dec 31, ${currentYear - 1}`;
                            break;
                        
                        default:
                            // Handle full_year_XXXX
                            if (range.startsWith('full_year_')) {
                                const selectedYear = parseInt(range.replace('full_year_', ''));
                                this.currentPeriodStart = new Date(selectedYear, 0, 1);
                                this.currentPeriodEnd = selectedYear === currentYear ? now : new Date(selectedYear, 11, 31, 23, 59, 59);
                                this.previousPeriodStart = new Date(selectedYear - 1, 0, 1);
                                this.previousPeriodEnd = new Date(selectedYear - 1, 11, 31, 23, 59, 59);
                                this.currentPeriodLabel = selectedYear === currentYear 
                                    ? `Jan 1, ${selectedYear} - ${now.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}`
                                    : `Jan 1 - Dec 31, ${selectedYear}`;
                            }
                            break;
                    }
                    
                    this.updateDashboardMetrics();
                },
                
                applyCustomDateRange() {
                    if (!this.customStartDate || !this.customEndDate) return;
                    
                    this.dateRange = 'custom';
                    this.currentPeriodStart = new Date(this.customStartDate);
                    this.currentPeriodEnd = new Date(this.customEndDate);
                    
                    // Calculate previous period (same duration)
                    const duration = this.currentPeriodEnd - this.currentPeriodStart;
                    this.previousPeriodEnd = new Date(this.currentPeriodStart.getTime() - 1);
                    this.previousPeriodStart = new Date(this.previousPeriodEnd.getTime() - duration);
                    
                    this.currentPeriodLabel = `${this.currentPeriodStart.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${this.currentPeriodEnd.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
                    
                    this.updateDashboardMetrics();
                },
                
                isPostInDateRange(post) {
                    if (!post.scheduledDate || (post.status !== 'completed' && post.status !== 'published')) return false;
                    
                    const postDate = new Date(post.scheduledDate);
                    return postDate >= this.currentPeriodStart && postDate <= this.currentPeriodEnd;
                },
                
                isPostInPreviousPeriod(post) {
                    if (!post.scheduledDate || (post.status !== 'completed' && post.status !== 'published')) return false;
                    
                    const postDate = new Date(post.scheduledDate);
                    return postDate >= this.previousPeriodStart && postDate <= this.previousPeriodEnd;
                },

                // Widget preference management
                getAvailableWidgets() {
                    // Define all possible widgets
                    const allWidgets = [
                        { id: 'reach', name: 'Total Reach' },
                        { id: 'engagement_rate', name: 'Engagement Rate' },
                        { id: 'total_engagement', name: 'Total Engagement' },
                        { id: 'ad_spend', name: 'Ad Spend' },
                        { id: 'impressions', name: 'Total Impressions' },
                        { id: 'views', name: 'Total Views' },
                        { id: 'likes', name: 'Total Likes' },
                        { id: 'comments', name: 'Total Comments' },
                        { id: 'shares', name: 'Total Shares' },
                        { id: 'saves', name: 'Total Saves' },
                        { id: 'watch_time', name: 'Watch Time' },
                        { id: 'skip_rate', name: 'Skip Rate' },
                        { id: 'follower_views', name: 'Follower Views' },
                        { id: 'non_follower_views', name: 'Non-Follower Views' }
                    ];
                    
                    // Filter to only show widgets enabled by admin
                    // If no admin settings, show all widgets
                    if (this.adminEnabledWidgets.length === 0) {
                        return allWidgets;
                    }
                    
                    return allWidgets.filter(w => this.adminEnabledWidgets.includes(w.id));
                },

                toggleWidgetPreference(widgetId) {
                    const index = this.userWidgetPreferences.indexOf(widgetId);
                    if (index > -1) {
                        this.userWidgetPreferences.splice(index, 1);
                    } else {
                        this.userWidgetPreferences.push(widgetId);
                    }
                    // Save to localStorage
                    localStorage.setItem(`widgetPreferences_${this.selectedClient?._id}`, JSON.stringify(this.userWidgetPreferences));
                },

                resetWidgetPreferences() {
                    // Reset to all available widgets
                    this.userWidgetPreferences = this.getAvailableWidgets().map(w => w.id);
                    localStorage.setItem(`widgetPreferences_${this.selectedClient?._id}`, JSON.stringify(this.userWidgetPreferences));
                },

                isWidgetVisible(widgetId) {
                    // Widget is visible if it's in user preferences (or if no preferences set, show all)
                    if (this.userWidgetPreferences.length === 0) {
                        return true;
                    }
                    return this.userWidgetPreferences.includes(widgetId);
                },

                updateDashboardMetrics() {
                    // Update dashboard metrics from post KPIs
                    if (!this.dashboardData.metrics) {
                        this.dashboardData.metrics = {};
                    }
                    
                    // Calculate current and previous period metrics
                    const currentReach = this.calculateFilteredKPI('reach', false);
                    const previousReach = this.calculateFilteredKPI('reach', true);
                    
                    // Calculate engagement: use engagement field, or fallback to sum of likes+comments+shares+saves
                    let currentEngagement = this.calculateFilteredKPI('engagement', false);
                    if (currentEngagement === 0) {
                        currentEngagement = this.calculateFilteredKPI('likes', false) + this.calculateFilteredKPI('comments', false) + this.calculateFilteredKPI('shares', false) + this.calculateFilteredKPI('saves', false);
                    }
                    let previousEngagement = this.calculateFilteredKPI('engagement', true);
                    if (previousEngagement === 0) {
                        previousEngagement = this.calculateFilteredKPI('likes', true) + this.calculateFilteredKPI('comments', true) + this.calculateFilteredKPI('shares', true) + this.calculateFilteredKPI('saves', true);
                    }
                    
                    this.dashboardData.metrics.reach = {
                        current: currentReach,
                        change: this.calculatePercentageChange(currentReach, previousReach)
                    };
                    this.dashboardData.metrics.engagement = {
                        current: currentEngagement,
                        change: this.calculatePercentageChange(currentEngagement, previousEngagement)
                    };
                    
                    // Calculate engagement rate
                    const currentEngagementRate = currentReach > 0 ? (currentEngagement / currentReach) * 100 : 0;
                    const previousEngagementRate = previousReach > 0 ? (previousEngagement / previousReach) * 100 : 0;
                    this.dashboardData.metrics.engagementRate = {
                        current: currentEngagementRate,
                        change: this.calculatePercentageChange(currentEngagementRate, previousEngagementRate)
                    };
                    
                    // Calculate platform breakdown for charts
                    this.dashboardData.platformBreakdown = {};
                    this.activePlatforms.forEach(platform => {
                        this.dashboardData.platformBreakdown[platform] = {
                            reach: this.calculatePlatformKPI(platform, 'reach'),
                            engagement: this.calculatePlatformKPI(platform, 'engagement'),
                            impressions: this.calculatePlatformKPI(platform, 'impressions'),
                            followers: this.calculatePlatformFollowers(platform)
                        };
                    });
                    
                    // Extract and aggregate demographics from posts
                    this.updateDemographicsData();
                    
                    // Reinitialize charts with new data
                    this.$nextTick(() => {
                        this.initCharts();
                    });
                },

                getTotalFollowers() {
                    let total = 0;
                    Object.values(this.platformFollowers).forEach(v => { total += v || 0; });
                    return total;
                },

                loadPlatformFollowers() {
                    const clientId = this.getClientId();
                    if (!clientId) return;
                    const stored = localStorage.getItem(`platform_followers_${clientId}`);
                    this.platformFollowers = {};
                    if (stored) {
                        try {
                            const parsed = JSON.parse(stored);
                            if (parsed && typeof parsed === 'object' && parsed.data) {
                                if (parsed.clientId && parsed.clientId !== clientId) {
                                    this.platformFollowers = {};
                                } else {
                                    this.platformFollowers = parsed.data || {};
                                }
                            } else {
                                this.platformFollowers = parsed || {};
                            }
                        } catch(e) {
                            this.platformFollowers = {};
                        }
                    }
                },

                savePlatformFollowers(platform, count, clientIdOverride = null) {
                    const clientId = clientIdOverride || this.getClientId();
                    if (!clientId) return;
                    this.platformFollowers[platform] = count;
                    localStorage.setItem(`platform_followers_${clientId}`, JSON.stringify({ clientId, data: this.platformFollowers }));
                },

                savePageLevelMetrics(platform, metrics, clientIdOverride = null) {
                    const clientId = clientIdOverride || this.getClientId();
                    if (!clientId) return;
                    if (!this.pageLevelMetrics[platform]) this.pageLevelMetrics[platform] = {};
                    const keys = ['reach', 'impressions', 'engagement', 'likes', 'comments', 'shares', 'saves', 'views'];
                    keys.forEach(k => {
                        if (metrics[k] != null && metrics[k] > 0) {
                            this.pageLevelMetrics[platform][k] = metrics[k];
                        }
                    });
                    localStorage.setItem(`page_level_metrics_${clientId}`, JSON.stringify({ clientId, data: this.pageLevelMetrics }));
                },

                loadPageLevelMetrics() {
                    const clientId = this.getClientId();
                    if (!clientId) return;
                    const stored = localStorage.getItem(`page_level_metrics_${clientId}`);
                    this.pageLevelMetrics = {};
                    if (stored) {
                        try {
                            const parsed = JSON.parse(stored);
                            if (parsed && typeof parsed === 'object' && parsed.data) {
                                if (parsed.clientId && parsed.clientId !== clientId) {
                                    this.pageLevelMetrics = {};
                                } else {
                                    this.pageLevelMetrics = parsed.data || {};
                                }
                            } else {
                                this.pageLevelMetrics = parsed || {};
                            }
                        } catch(e) {
                            this.pageLevelMetrics = {};
                        }
                    }
                },

                getPageLevelTotal(metric) {
                    let total = 0;
                    this.activePlatforms.forEach(platform => {
                        if (this.pageLevelMetrics[platform] && this.pageLevelMetrics[platform][metric]) {
                            total += this.pageLevelMetrics[platform][metric];
                        }
                    });
                    return total;
                },

                calculatePlatformFollowers(platform) {
                    // Check localStorage followers first
                    if (this.platformFollowers[platform]) {
                        return this.platformFollowers[platform];
                    }
                    // Fallback: get from posts with demographics
                    const postsWithDemographics = this.posts
                        .filter(p => p.platforms && p.platforms.includes(platform) && p.kpis?.demographics?.totalFollowers)
                        .sort((a, b) => new Date(b.scheduledDate) - new Date(a.scheduledDate));
                    
                    if (postsWithDemographics.length > 0) {
                        return postsWithDemographics[0].kpis.demographics.totalFollowers;
                    }
                    
                    return 0;
                },

                updateDemographicsData() {
                    // Aggregate demographics from the most recent posts with demographic data
                    const postsWithDemographics = this.posts
                        .filter(p => p.kpis?.demographics)
                        .sort((a, b) => new Date(b.scheduledDate) - new Date(a.scheduledDate));
                    
                    if (postsWithDemographics.length === 0) {
                        return;
                    }
                    
                    // Use the most recent demographics data
                    const latestDemographics = postsWithDemographics[0].kpis.demographics;
                    
                    // Format for dashboard display
                    this.dashboardData.demographics = {
                        age: {},
                        gender: latestDemographics.gender || {},
                        cities: latestDemographics.topCities || [],
                        countries: latestDemographics.topCountries || []
                    };
                    
                    // Convert age groups to chart format
                    if (latestDemographics.ageGroups) {
                        latestDemographics.ageGroups.forEach(group => {
                            this.dashboardData.demographics.age[group.range] = group.percentage;
                        });
                    }
                    
                    console.log('📊 Updated demographics:', this.dashboardData.demographics);
                },

                calculatePlatformKPI(platform, metric) {
                    let total = 0;
                    this.posts.filter(p => p.status === 'completed').forEach(post => {
                        if (post.kpis && post.platforms && post.platforms.includes(platform)) {
                            const key = `${platform}_${metric}`;
                            total += post.kpis[key] || 0;
                        }
                    });
                    return total;
                },

                formatNumber(num) {
                    return new Intl.NumberFormat().format(num);
                },

                formatWatchTime(seconds) {
                    if (!seconds) return '0m';
                    const minutes = Math.floor(seconds / 60);
                    if (minutes >= 60) {
                        const hours = Math.floor(minutes / 60);
                        const mins = minutes % 60;
                        return `${hours}h ${mins}m`;
                    }
                    return `${minutes}m`;
                },

                calculateAvgSkipRate() {
                    const posts = this.posts.filter(p => p.status === 'completed' && p.kpis?.instagram_skip_rate);
                    if (posts.length === 0) return 0;
                    const total = posts.reduce((sum, p) => sum + (p.kpis.instagram_skip_rate || 0), 0);
                    return Math.round(total / posts.length);
                },

                formatChange(change) {
                    const sign = change >= 0 ? '+' : '';
                    return `${sign}${change.toFixed(1)}% vs last month`;
                },

                formatDate(dateStr) {
                    return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                },

                formatDateRange(range) {
                    if (!range) return '';
                    const start = new Date(range.start).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    const end = new Date(range.end).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    return `${start} - ${end}`;
                },

                async generateReportFromBuilder() {
                    const payload = this.buildReportBuilderPayload();
                    const validationError = this.validateReportBuilderPayload(payload);
                    if (validationError) {
                        this.showToast(validationError, 'error');
                        return;
                    }

                    this.generatingReport = true;
                    try {
                        const response = await fetch(`${API_URL}/reports/generate`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();
                        if (!response.ok || !data.success) {
                            throw new Error(data.message || 'Failed to generate report');
                        }

                        await this.loadReports(this.clientLoadToken);
                        this.reportPreview = null;
                        this.reportBuilder.selectedPostIds = [];
                        this.reportBuilder.useSelectedPosts = false;
                        this.showToast('Report generated successfully.', 'success');
                    } catch (error) {
                        console.error('Generate report error:', error);
                        this.showToast(error.message || 'Failed to generate report.', 'error');
                    } finally {
                        this.generatingReport = false;
                    }
                },

                async generateOverviewReport() {
                    // Backward-compatible action from legacy buttons.
                    await this.generateReportFromBuilder();
                },

                startReportEdit(report) {
                    this.selectedReport = report;
                    this.reportEditForm = {
                        _id: report._id,
                        name: report.name || '',
                        status: report.status || 'draft'
                    };
                    this.showReportModal = true;
                },

                async saveReportEdits() {
                    if (!this.reportEditForm?._id) return;
                    this.savingReportEdit = true;
                    try {
                        const response = await fetch(`${API_URL}/reports/${this.reportEditForm._id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            },
                            body: JSON.stringify({
                                name: this.reportEditForm.name,
                                status: this.reportEditForm.status
                            })
                        });
                        const data = await response.json();
                        if (!response.ok || !data.success) {
                            throw new Error(data.message || 'Failed to update report');
                        }
                        this.selectedReport = data.data;
                        await this.loadReports(this.clientLoadToken);
                        this.showToast('Report updated successfully.', 'success');
                    } catch (error) {
                        console.error('Save report error:', error);
                        this.showToast(error.message || 'Unable to update report.', 'error');
                    } finally {
                        this.savingReportEdit = false;
                    }
                },

                async deleteReport(report) {
                    if (!report?._id) return;
                    if (!confirm(`Delete report "${report.name || 'Untitled'}"? This action cannot be undone.`)) {
                        return;
                    }
                    try {
                        const response = await fetch(`${API_URL}/reports/${report._id}`, {
                            method: 'DELETE',
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            }
                        });
                        const data = await response.json();
                        if (!response.ok || !data.success) {
                            throw new Error(data.message || 'Failed to delete report');
                        }
                        if (this.selectedReport?._id === report._id) {
                            this.showReportModal = false;
                            this.selectedReport = null;
                            this.reportEditForm = null;
                        }
                        await this.loadReports(this.clientLoadToken);
                        this.showToast('Report deleted successfully.', 'success');
                    } catch (error) {
                        console.error('Delete report error:', error);
                        this.showToast(error.message || 'Unable to delete report.', 'error');
                    }
                },

                async downloadReport(report) {
                    if (!report) return;
                    if (report.pdfUrl) {
                        const a = document.createElement('a');
                        a.href = report.pdfUrl;
                        a.target = '_blank';
                        a.rel = 'noopener noreferrer';
                        a.download = `${(report.name || 'report').replace(/[^a-z0-9]+/gi, '_').toLowerCase()}.pdf`;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        return;
                    }

                    try {
                        const response = await fetch(`${API_URL}/reports/${report._id}`, {
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            }
                        });
                        const data = await response.json();
                        if (!response.ok || !data.success) {
                            throw new Error(data.message || 'Failed to download report');
                        }

                        this.downloadReportAsPdf(data.data || report);
                    } catch (error) {
                        console.error('Download report error:', error);
                        this.showToast(error.message || 'Unable to download report.', 'error');
                    }
                },

                async openReport(report) {
                    if (!report?._id) return;
                    try {
                        const response = await fetch(`${API_URL}/reports/${report._id}`, {
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            }
                        });
                        const data = await response.json();
                        if (!response.ok || !data.success) {
                            throw new Error(data.message || 'Failed to load report');
                        }
                        this.selectedReport = data.data;
                        this.reportEditForm = {
                            _id: data.data?._id,
                            name: data.data?.name || '',
                            status: data.data?.status || 'draft'
                        };
                        this.showReportModal = true;
                    } catch (error) {
                        console.error('Open report error:', error);
                        this.showToast(error.message || 'Unable to open report.', 'error');
                    }
                },

                downloadReportAsPdf(report) {
                    const jsPdfApi = window.jspdf && window.jspdf.jsPDF ? window.jspdf.jsPDF : null;
                    if (!jsPdfApi) {
                        this.showToast('PDF library failed to load. Please try again.', 'error');
                        return;
                    }

                    const doc = new jsPdfApi();
                    const lineGap = 7;
                    const left = 14;
                    let y = 20;

                    const safeText = (value) => (value == null ? '' : String(value));
                    const writeLine = (label, value) => {
                        const text = `${label}: ${safeText(value)}`;
                        const lines = doc.splitTextToSize(text, 180);
                        doc.text(lines, left, y);
                        y += lines.length * lineGap;
                    };

                    doc.setFontSize(16);
                    doc.text(safeText(report.name || 'Client Report'), left, y);
                    y += 10;
                    doc.setFontSize(11);

                    const start = report?.dateRange?.start ? new Date(report.dateRange.start).toLocaleDateString() : 'N/A';
                    const end = report?.dateRange?.end ? new Date(report.dateRange.end).toLocaleDateString() : 'N/A';
                    writeLine('Date range', `${start} - ${end}`);
                    writeLine('Status', report.status || 'draft');
                    writeLine('Type', report.type || 'automated');
                    writeLine('Platforms', (report.platforms || []).join(', ') || 'All');
                    writeLine('Selected posts', report?.customData?.selectedPostCount || report?.selectedPostIds?.length || 0);
                    y += 2;

                    const metrics = report.metrics || {};
                    writeLine('Total reach', metrics.totalReach || 0);
                    writeLine('Total impressions', metrics.totalImpressions || 0);
                    writeLine('Total engagement', metrics.totalEngagement || 0);
                    writeLine('Engagement rate', `${Number(metrics.engagementRate || 0).toFixed(2)}%`);
                    writeLine('Ad spend', `$${Number(metrics.totalAdSpend || 0).toFixed(2)}`);

                    const platformBreakdown = metrics.platformBreakdown || {};
                    const platforms = Object.keys(platformBreakdown);
                    if (platforms.length > 0) {
                        y += 4;
                        writeLine('Platform breakdown', '');
                        platforms.forEach((platform) => {
                            const row = platformBreakdown[platform] || {};
                            writeLine(`- ${platform}`, `Reach ${Number(row.reach || 0).toLocaleString()}, Engagement ${Number(row.engagement || 0).toLocaleString()}, Posts ${Number(row.posts || 0).toLocaleString()}`);
                        });
                    }

                    const filename = `${safeText(report.name || 'report').replace(/[^a-z0-9]+/gi, '_').replace(/^_+|_+$/g, '').toLowerCase() || 'report'}.pdf`;
                    doc.save(filename);
                },

                initCharts() {
                    if (window.platformChartInstance) window.platformChartInstance.destroy();
                    if (window.engagementChartInstance) window.engagementChartInstance.destroy();
                    if (window.demographicsChartInstance) window.demographicsChartInstance.destroy();
                    if (window.adSpendChartInstance) window.adSpendChartInstance.destroy();

                    const isDarkMode = this.theme === 'dark';
                    const chartFontColor = isDarkMode ? '#9ca3af' : '#6b7280';
                    const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)';

                    // Platform Breakdown Chart
                    const platformCtx = document.getElementById('platformChart')?.getContext('2d');
                    if (platformCtx && this.dashboardData.platformBreakdown) {
                        const platforms = Object.keys(this.dashboardData.platformBreakdown);
                        const reaches = platforms.map(p => this.dashboardData.platformBreakdown[p].reach);
                        
                        window.platformChartInstance = new Chart(platformCtx, {
                            type: 'bar',
                            data: {
                                labels: platforms.map(p => p.charAt(0).toUpperCase() + p.slice(1)),
                                datasets: [{
                                    label: 'Reach',
                                    data: reaches,
                                    backgroundColor: isDarkMode ? 'rgba(99, 102, 241, 0.8)' : 'rgba(129, 140, 248, 0.8)',
                                    borderColor: isDarkMode ? '#818cf8' : '#6366f1',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: { 
                                        beginAtZero: true,
                                        grid: { color: gridColor },
                                        ticks: { color: chartFontColor }
                                    },
                                    x: {
                                        grid: { display: false },
                                        ticks: { color: chartFontColor }
                                    }
                                },
                                plugins: {
                                    legend: { display: false }
                                }
                            }
                        });
                    }

                    // Engagement Chart
                    const engagementCtx = document.getElementById('engagementChart')?.getContext('2d');
                    if (engagementCtx && this.dashboardData.platformBreakdown) {
                        const platforms = Object.keys(this.dashboardData.platformBreakdown);
                        const engagements = platforms.map(p => this.dashboardData.platformBreakdown[p].engagement);
                        
                        const colors = isDarkMode 
                            ? ['#3b82f6', '#ec4899', '#0ea5e9', '#64748b', '#8b5cf6']
                            : ['#60a5fa', '#f472b6', '#38bdf8', '#94a3b8', '#a78bfa'];

                        window.engagementChartInstance = new Chart(engagementCtx, {
                            type: 'doughnut',
                            data: {
                                labels: platforms.map(p => p.charAt(0).toUpperCase() + p.slice(1)),
                                datasets: [{
                                    data: engagements,
                                    backgroundColor: colors,
                                    borderColor: isDarkMode ? '#1f2937' : '#ffffff',
                                    borderWidth: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: { 
                                            color: chartFontColor,
                                            padding: 15,
                                            font: { size: 11 }
                                        }
                                    }
                                }
                            }
                        });
                    }

                    // Demographics Chart (Age & Gender)
                    const demographicsCtx = document.getElementById('demographicsChart')?.getContext('2d');
                    if (demographicsCtx && this.dashboardData.demographics?.age) {
                        const ageLabels = Object.keys(this.dashboardData.demographics.age);
                        const ageData = Object.values(this.dashboardData.demographics.age);
                        
                        window.demographicsChartInstance = new Chart(demographicsCtx, {
                            type: 'bar',
                            data: {
                                labels: ageLabels,
                                datasets: [{
                                    label: 'Audience %',
                                    data: ageData,
                                    backgroundColor: isDarkMode ? 'rgba(139, 92, 246, 0.8)' : 'rgba(167, 139, 250, 0.8)',
                                    borderColor: isDarkMode ? '#a78bfa' : '#8b5cf6',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: { 
                                        beginAtZero: true,
                                        grid: { color: gridColor },
                                        ticks: { 
                                            color: chartFontColor,
                                            callback: function(value) { return value + '%'; }
                                        }
                                    },
                                    x: {
                                        grid: { display: false },
                                        ticks: { color: chartFontColor, font: { size: 10 } }
                                    }
                                },
                                plugins: {
                                    legend: { display: false }
                                }
                            }
                        });
                    }

                    // Ad Spend by Platform Chart
                    const adSpendCtx = document.getElementById('adSpendChart')?.getContext('2d');
                    if (adSpendCtx && this.dashboardData.advertising?.byPlatform) {
                        const platforms = Object.keys(this.dashboardData.advertising.byPlatform);
                        const spends = platforms.map(p => this.dashboardData.advertising.byPlatform[p].spend);
                        
                        const colors = isDarkMode 
                            ? ['#3b82f6', '#ec4899', '#0ea5e9', '#64748b', '#8b5cf6']
                            : ['#60a5fa', '#f472b6', '#38bdf8', '#94a3b8', '#a78bfa'];

                        window.adSpendChartInstance = new Chart(adSpendCtx, {
                            type: 'doughnut',
                            data: {
                                labels: platforms.map(p => p.charAt(0).toUpperCase() + p.slice(1)),
                                datasets: [{
                                    data: spends,
                                    backgroundColor: colors,
                                    borderColor: isDarkMode ? '#1f2937' : '#ffffff',
                                    borderWidth: 3
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: { 
                                            color: chartFontColor,
                                            padding: 10,
                                            font: { size: 10 }
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return context.label + ': $' + context.parsed.toFixed(2);
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
            }));
        });
    </script>

    <!-- Mobile Bottom Navigation -->
    <nav class="hub-mobile-bottom-nav md:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 z-40">
        <div class="flex justify-around items-center h-16">
            <!-- CLIENT VIEW MOBILE NAV -->
            <!-- Content Bank - Client View -->
            <a x-show="viewMode === 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('workflow'))); ?>?tab=contentBank" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 dark:text-gray-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span class="text-xs">Content Bank</span>
            </a>
            <!-- Published Posts - Client View -->
            <a x-show="viewMode === 'client'" href="#content-calendar" @click="activeView = 'calendar'" :class="activeView === 'calendar' ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400'" class="flex flex-col items-center justify-center flex-1 h-full space-y-1">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-xs" :class="activeView === 'calendar' ? 'font-medium' : ''">Posts</span>
            </a>
            <!-- Page Overview - Client View -->
            <a x-show="viewMode === 'client'" href="#overview" @click="activeView = 'dashboard'" :class="activeView === 'dashboard' ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400'" class="flex flex-col items-center justify-center flex-1 h-full space-y-1">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="text-xs" :class="activeView === 'dashboard' ? 'font-medium' : ''">Overview</span>
            </a>
            <!-- Reports - Client View -->
            <a x-show="viewMode === 'client'" href="#reports" @click="activeView = 'reports'" :class="activeView === 'reports' ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400'" class="flex flex-col items-center justify-center flex-1 h-full space-y-1">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-xs" :class="activeView === 'reports' ? 'font-medium' : ''">Reports</span>
            </a>
            <!-- Event Coverage - Client View -->
            <a x-show="viewMode === 'client'" href="#event-coverage" @click="activeView = 'eventCoverage'" :class="activeView === 'eventCoverage' ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400'" class="flex flex-col items-center justify-center flex-1 h-full space-y-1">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 6.75h7.5m-7.5 3h4.5m-6 7.5h10.5A2.25 2.25 0 0019.5 15V6A2.25 2.25 0 0017.25 3.75H6.75A2.25 2.25 0 004.5 6v9a2.25 2.25 0 002.25 2.25zm0 0v2.25m10.5-2.25v2.25"/>
                </svg>
                <span class="text-xs" :class="activeView === 'eventCoverage' ? 'font-medium' : ''">Events</span>
            </a>
            <!-- Content Calendar - Client View -->
            <a x-show="viewMode === 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('content-calendar'))); ?>" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 dark:text-gray-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-xs">Calendar</span>
            </a>
            <!-- ADMIN/BRAND REP VIEW MOBILE NAV -->
            <!-- Workflow - Admin/Brand Rep View -->
            <a x-show="viewMode !== 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('workflow'))); ?>" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 dark:text-gray-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-xs">Workflow</span>
            </a>
            <!-- Agency Overview - Admin/Brand Rep View -->
            <a x-show="viewMode !== 'client'" href="<?php echo esc_url(get_permalink(get_page_by_path('overview'))); ?>" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 dark:text-gray-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="text-xs">Overview</span>
            </a>
            <!-- Insights - Admin/Brand Rep View -->
            <a x-show="viewMode !== 'client'" href="#overview" @click="activeView = 'dashboard'" :class="activeView === 'dashboard' ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400'" class="flex flex-col items-center justify-center flex-1 h-full space-y-1">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="text-xs" :class="activeView === 'dashboard' ? 'font-medium' : ''">Insights</span>
            </a>
            <a x-show="viewMode !== 'client'" href="#reports" @click="activeView = 'reports'" :class="activeView === 'reports' ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400'" class="flex flex-col items-center justify-center flex-1 h-full space-y-1">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-xs" :class="activeView === 'reports' ? 'font-medium' : ''">Reports</span>
            </a>
            <a x-show="viewMode !== 'client'" href="#event-coverage" @click="activeView = 'eventCoverage'" :class="activeView === 'eventCoverage' ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400'" class="flex flex-col items-center justify-center flex-1 h-full space-y-1">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 6.75h7.5m-7.5 3h4.5m-6 7.5h10.5A2.25 2.25 0 0019.5 15V6A2.25 2.25 0 0017.25 3.75H6.75A2.25 2.25 0 004.5 6v9a2.25 2.25 0 002.25 2.25zm0 0v2.25m10.5-2.25v2.25"/>
                </svg>
                <span class="text-xs" :class="activeView === 'eventCoverage' ? 'font-medium' : ''">Events</span>
            </a>
            <!-- Published Posts - Admin/Brand Rep View -->
            <a x-show="viewMode !== 'client'" href="#content-calendar" @click="activeView = 'calendar'" :class="activeView === 'calendar' ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400'" class="flex flex-col items-center justify-center flex-1 h-full space-y-1">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-xs" :class="activeView === 'calendar' ? 'font-medium' : ''">Posts</span>
            </a>
            <!-- Inventory -->
            <a x-show="viewMode !== 'client'" href="<?php echo esc_url(esirom_hub_page_url('inventory')); ?>" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 dark:text-gray-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span class="text-xs">Inventory</span>
            </a>
            <!-- Admin link - only show for actual admins in admin view mode -->
            <a x-show="user.role === 'admin' && viewMode === 'admin'" href="<?php echo esc_url(get_permalink(get_page_by_path('admin'))); ?>" class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 dark:text-gray-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-xs">Admin</span>
            </a>
        </div>
    </nav>

    <!-- PWA Install Prompt -->
    <div id="pwa-install-prompt" style="display: none;" class="fixed bottom-20 md:bottom-4 left-4 right-4 md:left-auto md:right-4 md:w-96 bg-white dark:bg-gray-800 rounded-lg shadow-2xl p-4 z-50 border border-gray-200 dark:border-gray-700">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Install ESIROM Hub</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Install our app for quick access and offline functionality</p>
                <div class="flex gap-2">
                    <button id="pwa-install-btn" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                        Install
                    </button>
                    <button id="pwa-dismiss-btn" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600">
                        Not Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Worker Registration -->
    <script>
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?php echo get_template_directory_uri(); ?>/sw.js')
                    .then(registration => {
                        console.log('✅ Service Worker registered:', registration.scope);
                    })
                    .catch(error => {
                        console.log('❌ Service Worker registration failed:', error);
                    });
            });
        }

        // PWA Install Prompt
        let deferredPrompt;
        const installPrompt = document.getElementById('pwa-install-prompt');
        const installBtn = document.getElementById('pwa-install-btn');
        const dismissBtn = document.getElementById('pwa-dismiss-btn');

        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent the mini-infobar from appearing
            e.preventDefault();
            // Stash the event so it can be triggered later
            deferredPrompt = e;
            
            // Check if user has dismissed before
            const dismissed = localStorage.getItem('pwa-install-dismissed');
            if (!dismissed) {
                // Show install prompt
                installPrompt.style.display = 'block';
            }
        });

        installBtn.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            
            // Show the install prompt
            deferredPrompt.prompt();
            
            // Wait for the user to respond
            const { outcome } = await deferredPrompt.userChoice;
            console.log(`User response: ${outcome}`);
            
            // Clear the deferredPrompt
            deferredPrompt = null;
            installPrompt.style.display = 'none';
        });

        dismissBtn.addEventListener('click', () => {
            installPrompt.style.display = 'none';
            localStorage.setItem('pwa-install-dismissed', 'true');
        });

        // Detect if app is installed
        window.addEventListener('appinstalled', () => {
            console.log('✅ PWA installed successfully');
            installPrompt.style.display = 'none';
        });

        // Check if running as PWA
        if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
            console.log('✅ Running as PWA');
        }
    </script>

    <!-- Toast Notifications -->
    <div class="fixed top-20 right-4 md:top-4 z-[9999] space-y-2 w-full max-w-sm pr-4">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-full" x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="w-full shadow-lg rounded-lg pointer-events-auto overflow-hidden" :class="{'bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-700': toast.type === 'success', 'bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700': toast.type === 'error', 'bg-blue-50 dark:bg-blue-900/50 border border-blue-200 dark:border-blue-700': toast.type === 'info', 'bg-yellow-50 dark:bg-yellow-900/50 border border-yellow-200 dark:border-yellow-700': toast.type === 'warning'}">
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

    <?php include get_template_directory() . '/inc/change-password-modal.php'; ?>
    <?php wp_footer(); ?>
</body>
</html>
