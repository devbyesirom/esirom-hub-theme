<?php
/**
 * Template Name: Admin Panel
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
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="h-full">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php bloginfo('name'); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png">
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png">
    
    <script>
        // Force light mode on admin panel
        document.documentElement.classList.remove('dark');
        
        // Suppress Tailwind CDN production warning for admin panel
        // Note: This is an internal admin tool, not a public-facing page
        window.tailwindCDNWarning = false;
    </script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
    <script>
        // Configure Tailwind to use class-based dark mode (not media query)
        tailwind.config = {
            darkMode: 'class',
            corePlugins: {
                preflight: true,
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
        <?php esirom_hub_layout_styles(); ?>
    </style>
    <?php wp_head(); ?>
    <?php esirom_hub_wp_head_reset(); ?>
</head>
<body class="h-full bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-white" x-data="adminPanel" x-init="init(); loadData()">
    <div class="hub-app-shell flex w-full">
        <!-- Sidebar -->
        <aside :class="isSidebarOpen ? 'sidebar-expanded' : 'sidebar-collapsed'" class="hub-app-sidebar sidebar flex-shrink-0 bg-white dark:bg-gray-900/70 dark:backdrop-blur-sm border-r border-gray-200 dark:border-gray-700/50 flex flex-col">
            <?php esirom_hub_staff_sidebar_header(false); ?>
            <nav class="flex-1 px-2 py-4 space-y-0.5 overflow-y-auto">
                <a @click.prevent="activeTab = 'users'" href="#" :class="activeTab === 'users' ? 'bg-indigo-500 text-white font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="flex items-center px-3 py-2 rounded-lg transition-colors duration-200 text-sm">
                    <svg class="h-5 w-5 flex-shrink-0 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                    <span class="nav-text">Users</span>
                </a>
                <a @click.prevent="activeTab = 'pending'" href="#" :class="activeTab === 'pending' ? 'bg-indigo-500 text-white font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="flex items-center px-3 py-2 rounded-lg transition-colors duration-200 text-sm relative">
                    <svg class="h-5 w-5 flex-shrink-0 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="nav-text">Pending Users</span>
                    <span x-show="pendingUsers.length > 0" class="ml-auto bg-red-500 text-white text-[10px] font-bold rounded-full h-4 min-w-[1rem] px-1 flex items-center justify-center" x-text="pendingUsers.length"></span>
                </a>
                <a @click.prevent="activeTab = 'clients'" href="#" :class="activeTab === 'clients' ? 'bg-indigo-500 text-white font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="flex items-center px-3 py-2 rounded-lg transition-colors duration-200 text-sm">
                    <svg class="h-5 w-5 flex-shrink-0 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" /></svg>
                    <span class="nav-text">Clients</span>
                </a>
                <a @click.prevent="activeTab = 'emails'; loadClientReminders()" href="#" :class="activeTab === 'emails' ? 'bg-indigo-500 text-white font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="flex items-center px-3 py-2 rounded-lg transition-colors duration-200 text-sm">
                    <svg class="h-5 w-5 flex-shrink-0 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                    <span class="nav-text">Emails</span>
                </a>
                <a @click.prevent="activeTab = 'import'" href="#" :class="activeTab === 'import' ? 'bg-indigo-500 text-white font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="flex items-center px-3 py-2 rounded-lg transition-colors duration-200 text-sm">
                    <svg class="h-5 w-5 flex-shrink-0 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                    <span class="nav-text">Import Posts</span>
                </a>
            </nav>
            <div class="p-2 border-t border-gray-200 dark:border-gray-700/50 space-y-0.5">
                <a href="<?php echo esc_url(esirom_hub_page_url('dashboard')); ?>" class="flex items-center px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 text-sm">
                    <svg class="h-5 w-5 flex-shrink-0 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg>
                    <span class="nav-text">Back to Dashboard</span>
                </a>
                <a @click.prevent="logout()" href="#" class="flex items-center px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 text-sm">
                    <svg class="h-5 w-5 flex-shrink-0 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                    <span class="nav-text">Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main content -->
        <main class="hub-app-main flex-1 min-w-0 w-full bg-gray-50 dark:bg-gray-900">
            <header class="flex items-center justify-between p-4 h-16 bg-white/80 dark:bg-gray-900/70 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700/50 sticky top-0 z-10 shadow-sm gap-4 flex-wrap">
                <div class="min-w-0">
                    <h1 class="text-lg font-bold text-gray-900 dark:text-white" x-text="pageTitle"></h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="pageSubtitle"></p>
                </div>
                <div class="flex items-center gap-2">
                    <!-- User Dropdown -->
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <img class="h-7 w-7 rounded-full object-cover" :src="'https://placehold.co/100x100/4a5568/ffffff?text=' + (user?.firstName?.[0] || 'A')" :alt="user?.fullName">
                            <span class="hidden sm:inline text-sm font-medium text-gray-700 dark:text-gray-300" x-text="user?.fullName"></span>
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-cloak
                             class="absolute right-0 mt-2 w-52 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 py-1 z-50">
                            <div class="px-4 py-2.5 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="user?.fullName"></p>
                                <p class="text-xs text-gray-500 mt-0.5" x-text="user?.email"></p>
                                <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-0.5 capitalize font-medium" x-text="user?.role?.replace('_', ' ')"></p>
                            </div>
                            <a @click.prevent="showPwModal = true; dropdownOpen = false" href="#"
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Change Password
                            </a>
                            <a @click.prevent="logout(); dropdownOpen = false" href="#"
                               class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-b-2xl transition-colors">Logout</a>
                        </div>
                    </div>
                </div>
            </header>

            <div class="hub-page-content px-4 sm:px-6 lg:px-8 pt-3 sm:pt-4 pb-4 sm:pb-6 lg:pb-8">
            
            <!-- Users Tab -->
            <div x-show="activeTab === 'users'" x-cloak class="w-full">
                <div class="flex justify-between items-center mb-4 gap-3 flex-wrap">
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">All Users</h2>
                    <button @click="showUserModal = true; editingUser = null" class="bg-indigo-600 text-white px-3 py-1.5 rounded-xl text-xs font-semibold hover:bg-indigo-700">
                        Add New User
                    </button>
                </div>

                <!-- Users Table -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="user in users" :key="user._id">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900" x-text="user.firstName + ' ' + user.lastName"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="user.email"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800" x-text="user.role.replace('_', ' ')"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="user.isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" x-text="user.isActive ? 'Active' : 'Inactive'"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button @click="editUser(user)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                        <button @click="deleteUser(user._id)" class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pending Users Tab -->
            <div x-show="activeTab === 'pending'" x-cloak class="w-full">
                <div class="flex justify-between items-center mb-4 gap-3 flex-wrap">
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">Registration Queue</h2>
                    <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-xs font-semibold" x-text="`${pendingUsers.length} Pending`"></span>
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-if="pendingUsers.length === 0">
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="mt-2 font-medium">No pending registrations</p>
                                        <p class="text-sm">All registration requests have been processed</p>
                                    </td>
                                </tr>
                            </template>
                            <template x-for="user in pendingUsers" :key="user._id">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                                <span class="text-indigo-600 font-semibold" x-text="user.firstName.charAt(0) + user.lastName.charAt(0)"></span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900" x-text="user.firstName + ' ' + user.lastName"></div>
                                                <div class="text-sm text-gray-500" x-text="user.email"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900" x-text="user.companyName || '-'"></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate" x-text="user.registrationNote || 'No note provided'" :title="user.registrationNote"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span x-text="new Date(user.createdAt).toLocaleDateString()"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button @click="approveUser(user)" class="text-green-600 hover:text-green-900">Approve</button>
                                        <button @click="rejectUser(user)" class="text-red-600 hover:text-red-900">Reject</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Clients Tab -->
            <div x-show="activeTab === 'clients'" x-cloak class="w-full">
                <div class="flex justify-between items-center mb-4 gap-3 flex-wrap">
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">All Clients &amp; Brands</h2>
                    <button @click="showClientModal = true; editingClient = null" class="bg-indigo-600 text-white px-3 py-1.5 rounded-xl text-xs font-semibold hover:bg-indigo-700">
                        Add New Client
                    </button>
                </div>

                <!-- Clients Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4">
                    <template x-for="client in clients" :key="client._id">
                        <div class="bg-white shadow rounded-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <img :src="client.logo || 'https://placehold.co/100x100/4a5568/ffffff?text=' + (client.brandName || client.companyName || client.name || 'C').charAt(0)" class="h-12 w-12 rounded-full" :alt="client.brandName || client.companyName || client.name">
                                <span :class="client.isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 text-xs font-semibold rounded-full" x-text="client.isActive ? 'Active' : 'Inactive'"></span>
                            </div>
                            <h3 class="text-lg font-semibold mb-2" x-text="client.brandName || client.companyName || client.name || 'Unnamed Client'"></h3>
                            <p class="text-sm text-gray-600 mb-2" x-text="client.contactEmail"></p>
                            <p class="text-xs text-gray-500 mb-1">Industry: <span x-text="client.industry || 'N/A'"></span></p>
                            <p class="text-xs mb-4">
                                <span x-show="!client.serviceType || client.serviceType === 'social_media'" class="inline-block px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-xs font-medium">Social Media</span>
                                <span x-show="client.serviceType === 'creative'" class="inline-block px-2 py-0.5 rounded-full bg-violet-100 text-violet-800 text-xs font-medium">Creative</span>
                                <span x-show="client.serviceType === 'multimedia'" class="inline-block px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs font-medium">Multimedia</span>
                            </p>
                            <div class="flex flex-col space-y-2">
                                <button @click="customizeClient(client)" class="w-full bg-green-600 text-white px-3 py-1.5 rounded-xl text-xs font-semibold hover:bg-green-700 flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                                    Customize Dashboard
                                </button>
                                <div class="flex space-x-2">
                                    <button @click="editClient(client)" class="flex-1 bg-indigo-600 text-white px-3 py-1.5 rounded-xl text-xs font-semibold hover:bg-indigo-700">Edit</button>
                                    <button @click="deleteClient(client._id)" class="flex-1 bg-red-600 text-white px-3 py-1.5 rounded-xl text-xs font-semibold hover:bg-red-700">Delete</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Emails Tab -->
            <div x-show="activeTab === 'emails'" x-cloak class="w-full space-y-6">
                <div class="bg-white shadow rounded-lg p-5 border border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Google Space — Workflow Digest</h3>
                            <p class="text-xs text-gray-500 mt-1">Daily overdue / due today / due soon summary for your team Space (Mon–Fri 8:30 AM).</p>
                        </div>
                        <button @click="testWorkflowDigest()" :disabled="workflowDigestLoading"
                                class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-xl hover:bg-indigo-700 disabled:opacity-50 whitespace-nowrap">
                            <span x-show="!workflowDigestLoading">Send Test Digest Now</span>
                            <span x-show="workflowDigestLoading">Sending…</span>
                        </button>
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg border border-gray-200 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Content Bank — Client Reminders</h3>
                            <p class="text-xs text-gray-500 mt-1">Email clients a summary of content waiting for their approval in the Hub.</p>
                            <p class="text-xs text-indigo-600 mt-2" x-show="reminderTotals.pendingItems > 0">
                                <span x-text="reminderTotals.pendingItems"></span> item(s) across
                                <span x-text="reminderTotals.brands"></span> brand(s) ·
                                <span x-text="reminderTotals.recipients"></span> client contact(s)
                            </p>
                            <p class="text-xs text-gray-400 mt-2" x-show="!reminderLoading && reminderBrands.length === 0">No content currently waiting for client approval.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button @click="loadClientReminders()" :disabled="reminderLoading"
                                    class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-50 disabled:opacity-50">
                                Refresh
                            </button>
                            <button @click="sendClientReminders()"
                                    :disabled="reminderSending || reminderLoading || selectedReminderClients.length === 0"
                                    class="px-4 py-2 bg-purple-600 text-white text-xs font-semibold rounded-xl hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!reminderSending">Send Client Reminder</span>
                                <span x-show="reminderSending">Sending…</span>
                            </button>
                        </div>
                    </div>

                    <div x-show="reminderLoading" class="p-8 text-center text-sm text-gray-500">Loading pending approvals…</div>

                    <div x-show="!reminderLoading && reminderBrands.length > 0" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left">
                                        <input type="checkbox" class="rounded border-gray-300"
                                               :checked="selectedReminderClients.length === reminderBrands.length && reminderBrands.length > 0"
                                               @change="toggleAllReminderClients($event.target.checked)">
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Brand</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pending</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client contacts</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="brand in reminderBrands" :key="brand.clientId">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" class="rounded border-gray-300"
                                                   :checked="selectedReminderClients.includes(brand.clientId)"
                                                   @change="toggleReminderClient(brand.clientId, $event.target.checked)">
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900" x-text="brand.brandName"></td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800" x-text="brand.pendingCount"></span>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-600">
                                            <template x-if="brand.clientUsers.length">
                                                <span x-text="brand.clientUsers.map(u => u.firstName + ' ' + u.lastName + ' (' + u.email + ')').join(', ')"></span>
                                            </template>
                                            <span x-show="!brand.clientUsers.length" class="text-red-500">No client users</span>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-500 max-w-md">
                                            <span x-text="brand.items.map(i => i.title).join(' · ')"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Import Posts Tab -->
            <div x-show="activeTab === 'import'" x-cloak class="w-full">
                <div class="mb-4">
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">Import Posts &amp; KPIs</h2>
                </div>

                <!-- Import Method Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Manual Import (CSV/JSON) -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <svg class="h-8 w-8 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <h3 class="text-lg font-semibold">Manual Import (CSV/JSON)</h3>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Upload a CSV or JSON file with post data and KPIs</p>
                        
                        <!-- Client Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Select Client</label>
                            <select x-model="importClientId" class="w-full border rounded px-3 py-2">
                                <option value="">Choose a client...</option>
                                <template x-for="client in clients" :key="client._id">
                                    <option :value="client._id" x-text="client.brandName || client.companyName || client.name"></option>
                                </template>
                            </select>
                        </div>

                        <!-- File Upload -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Upload File or Folder</label>
                            <input 
                                type="file" 
                                @change="handleImportFile($event)" 
                                accept=".csv,.json" 
                                multiple 
                                webkitdirectory 
                                directory 
                                :disabled="!importClientId"
                                class="w-full border rounded px-3 py-2 disabled:bg-gray-100 disabled:cursor-not-allowed"
                            >
                            <p class="text-xs text-gray-500 mt-1">Supported: CSV, JSON, Instagram Export, or Facebook Export</p>
                            <p x-show="!importClientId" class="text-xs text-red-600 mt-1">⚠️ Please select a client first</p>
                            <p x-show="importClientId" class="text-xs text-blue-600 mt-1">💡 Tip: Select the entire Instagram or Facebook export folder!</p>
                            <div x-show="importClientId" class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                <p class="text-xs text-amber-800 mb-2">
                                    <strong>Note:</strong> Media files (videos/images) are referenced but not uploaded to the server.
                                </p>
                                <p class="text-xs text-amber-800">
                                    To display media, copy the <code class="bg-amber-100 px-1 rounded">media</code> folder from your Instagram export to:
                                </p>
                                <code class="block bg-amber-100 px-2 py-1 rounded mt-1 text-xs" x-text="'/wp-content/media/' + ((clients.find(c => c._id === importClientId)?.brandName) || (clients.find(c => c._id === importClientId)?.companyName) || 'client-name').toLowerCase().replace(/\\s+/g, '-') + '/'"></code>
                                <p class="text-xs text-amber-700 mt-2">
                                    Example: <code class="bg-amber-100 px-1 rounded">cp -r media/ /wp-content/media/grabngoja/</code>
                                </p>
                            </div>
                        </div>

                        <button @click="processManualImport()" :disabled="!importClientId || !importFile" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed">
                            Import Posts
                        </button>

                        <!-- Download Template -->
                        <button @click="downloadTemplate()" class="w-full mt-2 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                            Download CSV Template
                        </button>
                    </div>

                    <!-- Meta API Import -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <svg class="h-8 w-8 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            <h3 class="text-lg font-semibold">Meta API Import</h3>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Automatically fetch posts from Facebook & Instagram</p>
                        
                        <!-- Client Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Select Client</label>
                            <select x-model="apiImportClientId" class="w-full border rounded px-3 py-2">
                                <option value="">Choose a client...</option>
                                <template x-for="client in clients" :key="client._id">
                                    <option :value="client._id" x-text="client.brandName || client.companyName || client.name"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Access Token -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Meta Access Token</label>
                            <input type="password" x-model="metaAccessToken" placeholder="Enter your Meta API access token" class="w-full border rounded px-3 py-2">
                            <p class="text-xs text-gray-500 mt-1">Get your token from <a href="https://developers.facebook.com/tools/explorer/" target="_blank" class="text-indigo-600 hover:underline">Meta Graph API Explorer</a></p>
                        </div>

                        <!-- Date Range -->
                        <div class="grid grid-cols-2 gap-2 mb-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">From Date</label>
                                <input type="date" x-model="apiImportFromDate" class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">To Date</label>
                                <input type="date" x-model="apiImportToDate" class="w-full border rounded px-3 py-2">
                            </div>
                        </div>

                        <button @click="processMetaAPIImport()" :disabled="!apiImportClientId || !metaAccessToken" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed">
                            Fetch from Meta API
                        </button>

                        <!-- Setup Guide -->
                        <button @click="showMetaAPIGuide = true" class="w-full mt-2 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                            Setup Guide
                        </button>
                    </div>
                </div>

                <!-- Import Preview/Results -->
                <!-- Media Upload Section (after import preview) -->
                <div x-show="importPreview.length > 0 && importClientId" class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-dashed border-indigo-300 rounded-lg p-6 mb-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">📹 Upload Media Files</h3>
                            <p class="text-sm text-gray-700 mb-4">
                                To display videos and images, upload the media files from your Instagram export.
                                Select the <code class="bg-indigo-100 px-2 py-0.5 rounded text-xs">media</code> folder from your Instagram export.
                            </p>
                            <div class="bg-white rounded-lg p-4 mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Media Folder</label>
                                <input 
                                    type="file" 
                                    @change="handleMediaUpload($event)" 
                                    webkitdirectory 
                                    directory 
                                    multiple
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700"
                                >
                                <p class="text-xs text-gray-500 mt-2">
                                    Client: <span class="font-semibold" x-text="(clients.find(c => c._id === importClientId)?.brandName) || (clients.find(c => c._id === importClientId)?.companyName)"></span>
                                </p>
                            </div>
                            <div x-show="mediaUploadProgress.total > 0" class="bg-white rounded-lg p-4">
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="font-medium">Uploading...</span>
                                    <span x-text="mediaUploadProgress.uploaded + ' / ' + mediaUploadProgress.total"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full transition-all" :style="'width: ' + (mediaUploadProgress.uploaded / mediaUploadProgress.total * 100) + '%'"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="importPreview.length > 0" class="bg-white shadow rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Import Preview (<span x-text="importPreview.length"></span> posts)</h3>
                    <div class="max-h-96 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Caption</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reach</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Impressions</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Views</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Watch Time</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Likes</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Comments</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Shares</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Saves</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Skip Rate</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Follower Views</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="post in importPreview" :key="post.id">
                                    <tr>
                                        <td class="px-4 py-2 text-sm" x-text="new Date(post.scheduledDate).toLocaleDateString()"></td>
                                        <td class="px-4 py-2 text-sm capitalize" x-text="post.contentType"></td>
                                        <td class="px-4 py-2 text-sm truncate max-w-xs" x-text="post.caption"></td>
                                        <td class="px-4 py-2 text-sm" x-text="post.kpis?.instagram_reach || 0"></td>
                                        <td class="px-4 py-2 text-sm" x-text="post.kpis?.instagram_impressions || 0"></td>
                                        <td class="px-4 py-2 text-sm" x-text="post.kpis?.instagram_views || 0"></td>
                                        <td class="px-4 py-2 text-sm" x-text="post.kpis?.instagram_watch_time ? Math.floor(post.kpis.instagram_watch_time / 60) + 'm' : '-'"></td>
                                        <td class="px-4 py-2 text-sm" x-text="post.kpis?.instagram_likes || 0"></td>
                                        <td class="px-4 py-2 text-sm" x-text="post.kpis?.instagram_comments || 0"></td>
                                        <td class="px-4 py-2 text-sm" x-text="post.kpis?.instagram_shares || 0"></td>
                                        <td class="px-4 py-2 text-sm" x-text="post.kpis?.instagram_saves || 0"></td>
                                        <td class="px-4 py-2 text-sm" x-text="post.kpis?.instagram_skip_rate ? post.kpis.instagram_skip_rate + '%' : '-'"></td>
                                        <td class="px-4 py-2 text-sm" x-text="post.kpis?.instagram_views_followers || 0"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button @click="importPreview = []" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                        <button @click="confirmImport()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Confirm Import</button>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Temporary Password Success Modal -->
    <div x-show="showTempPasswordModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showTempPasswordModal = false">
        <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-lg rounded-lg bg-white">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold mb-2 text-gray-900">User Created Successfully!</h3>
                <p class="text-sm text-gray-600 mb-4">Share this temporary password with the new user:</p>
                <div class="bg-indigo-50 border-2 border-indigo-200 rounded-lg p-4 mb-4">
                    <p class="text-xs text-gray-600 mb-1">Temporary Password</p>
                    <p class="text-2xl font-mono font-bold text-indigo-600 tracking-wider" x-text="temporaryPassword"></p>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                    <p class="text-xs text-yellow-800">
                        <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        The user will be required to change this password upon first login.
                    </p>
                </div>
                <button @click="copyTempPassword()" class="w-full mb-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Copy Password
                </button>
                <button @click="showTempPasswordModal = false" class="w-full px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- User Modal -->
    <div x-show="showUserModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showUserModal = false">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <h3 class="text-lg font-bold mb-4" x-text="editingUser ? 'Edit User' : 'Add New User'"></h3>
            <form @submit.prevent="saveUser()">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">First Name</label>
                        <input type="text" x-model="userForm.firstName" required class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Last Name</label>
                        <input type="text" x-model="userForm.lastName" required class="w-full border rounded px-3 py-2">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" x-model="userForm.email" required class="w-full border rounded px-3 py-2">
                    <p class="text-xs text-gray-500 mt-1" x-show="!editingUser">A temporary password will be generated automatically</p>
                </div>
                <div class="mb-4" x-show="editingUser">
                    <label class="block text-sm font-medium mb-1">New Password (Optional)</label>
                    <input type="password" x-model="userForm.newPassword" placeholder="Leave blank to keep current password" class="w-full border rounded px-3 py-2">
                    <p class="text-xs text-gray-500 mt-1">Enter a new password to reset this user's password (min 6 characters)</p>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Role</label>
                        <select x-model="userForm.role" required class="w-full border rounded px-3 py-2">
                            <option value="admin">Admin</option>
                            <option value="brand_rep">Brand Representative</option>
                            <option value="client">Client</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Status</label>
                        <div class="flex items-center h-10">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="userForm.isActive" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                <span class="ml-3 text-sm font-medium" x-text="userForm.isActive ? 'Active' : 'Inactive'"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mb-4" x-show="userForm.role === 'client'">
                    <label class="block text-sm font-medium mb-1">Assign to Clients (Multiple)</label>
                    <div class="border rounded px-3 py-2 max-h-40 overflow-y-auto space-y-2">
                        <div class="flex items-center justify-between mb-1">
                            <button type="button" @click="userForm.clientIds = clients.map(c => c._id)" class="text-xs text-indigo-600 hover:underline">Select all</button>
                            <button type="button" @click="userForm.clientIds = []" class="text-xs text-gray-500 hover:underline">Clear</button>
                        </div>
                        <template x-for="client in clients" :key="`client-user-${client._id}`">
                            <label class="flex items-center gap-2 text-sm py-0.5">
                                <input type="checkbox" :value="client._id" x-model="userForm.clientIds" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span x-text="client.brandName || client.companyName || client.name"></span>
                            </label>
                        </template>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Assign one or more brands to this client account</p>
                </div>
                <div class="mb-4" x-show="userForm.role === 'brand_rep'">
                    <label class="block text-sm font-medium mb-1">Assign Clients</label>
                    <div class="border rounded px-3 py-2 max-h-40 overflow-y-auto space-y-2">
                        <div class="flex items-center justify-between mb-1">
                            <button type="button" @click="userForm.assignedClients = clients.map(c => c._id)" class="text-xs text-indigo-600 hover:underline">Select all</button>
                            <button type="button" @click="userForm.assignedClients = []" class="text-xs text-gray-500 hover:underline">Clear</button>
                        </div>
                        <template x-for="client in clients" :key="`rep-client-${client._id}`">
                            <label class="flex items-center gap-2 text-sm py-0.5">
                                <input type="checkbox" :value="client._id" x-model="userForm.assignedClients" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span x-text="client.brandName || client.companyName || client.name"></span>
                            </label>
                        </template>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Assign one or more brands for this brand representative</p>
                </div>

                <!-- Department & Role (admin/brand_rep only) -->
                <div class="mb-4" x-show="userForm.role === 'brand_rep' || userForm.role === 'admin'">
                    <div class="border rounded-lg p-4 bg-gray-50 space-y-3">
                        <p class="text-sm font-semibold text-gray-700">Team Performance Settings</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Department</label>
                                <select x-model="userForm.department" class="w-full border rounded px-3 py-2 text-sm">
                                    <option value="">— No Department —</option>
                                    <option value="web_developer">Web Developer</option>
                                    <option value="graphic_designer">Graphic Designer</option>
                                    <option value="social_media_exec">Social Media Executive</option>
                                    <option value="multimedia">Multimedia</option>
                                </select>
                            </div>
                            <div x-show="userForm.department === 'multimedia'">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Multimedia Role</label>
                                <select x-model="userForm.multimediaRole" class="w-full border rounded px-3 py-2 text-sm">
                                    <option value="">— Select Role —</option>
                                    <option value="photographer">Photographer</option>
                                    <option value="videographer">Videographer</option>
                                    <option value="editor">Editor</option>
                                    <option value="all">All (Generalist)</option>
                                </select>
                            </div>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="userForm.isManager" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700">This person is a department manager</span>
                        </label>
                        <p class="text-xs text-gray-500">Managers can view their team's performance on the My Progress page.</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" @click="showUserModal = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Approve User Modal -->
    <div x-show="showApprovalModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showApprovalModal = false">
        <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-lg rounded-lg bg-white">
            <h3 class="text-xl font-bold mb-4 text-gray-900">Approve User Registration</h3>
            <div class="mb-6">
                <p class="text-gray-700 mb-2">Approve registration for:</p>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="font-semibold text-gray-900" x-text="approvingUser ? `${approvingUser.firstName} ${approvingUser.lastName}` : ''"></p>
                    <p class="text-sm text-gray-600" x-text="approvingUser?.email"></p>
                    <p class="text-sm text-gray-600 mt-1">
                        <span class="font-medium">Company:</span> 
                        <span x-text="approvingUser?.companyName"></span>
                    </p>
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Assign to Client (Optional)</label>
                <select x-model="approvalClientId" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Select Client --</option>
                    <template x-for="client in clients" :key="client._id">
                        <option :value="client._id" x-text="client.brandName || client.name"></option>
                    </template>
                </select>
                <p class="text-xs text-gray-500 mt-1">You can assign a client now or later</p>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" @click="showApprovalModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <button type="button" @click="confirmApproval()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Approve User
                </button>
            </div>
        </div>
    </div>

    <!-- Client Modal -->
    <div x-show="showClientModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showClientModal = false">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <h3 class="text-lg font-bold mb-4" x-text="editingClient ? 'Edit Client' : 'Add New Client'"></h3>
            <form @submit.prevent="saveClient()">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Client Name</label>
                        <input type="text" x-model="clientForm.name" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Brand Name</label>
                        <input type="text" x-model="clientForm.brandName" required class="w-full border rounded px-3 py-2">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Contact Email</label>
                        <input type="email" x-model="clientForm.contactEmail" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Contact Phone</label>
                        <input type="tel" x-model="clientForm.contactPhone" class="w-full border rounded px-3 py-2">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Industry</label>
                        <input type="text" x-model="clientForm.industry" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Service Type</label>
                        <select x-model="clientForm.serviceType" class="w-full border rounded px-3 py-2">
                            <option value="social_media">Social Media</option>
                            <option value="creative">Creative</option>
                            <option value="multimedia">Multimedia</option>
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Logo URL</label>
                    <input type="url" x-model="clientForm.logo" class="w-full border rounded px-3 py-2" placeholder="https://...">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="showClientModal = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Client Customization Modal -->
    <div x-show="showCustomizeModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showCustomizeModal = false">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white mb-10">
            <h3 class="text-lg font-bold mb-4">Customize Dashboard - <span x-text="customizingClient?.brandName || customizingClient?.companyName || customizingClient?.name"></span></h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left: Dashboard Widgets -->
                <div>
                    <h4 class="font-semibold mb-3 text-gray-700">Dashboard Widgets</h4>
                    <div class="space-y-2 bg-gray-50 p-4 rounded-lg">
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 p-2 rounded">
                            <input type="checkbox" x-model="customizeForm.widgets" value="metrics" class="rounded">
                            <span class="text-sm">Key Metrics Cards</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 p-2 rounded">
                            <input type="checkbox" x-model="customizeForm.widgets" value="charts" class="rounded">
                            <span class="text-sm">Platform Charts</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 p-2 rounded">
                            <input type="checkbox" x-model="customizeForm.widgets" value="kpis" class="rounded">
                            <span class="text-sm">KPI Progress Tracking</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 p-2 rounded">
                            <input type="checkbox" x-model="customizeForm.widgets" value="pending" class="rounded">
                            <span class="text-sm">Pending Approvals Alert</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 p-2 rounded">
                            <input type="checkbox" x-model="customizeForm.widgets" value="engagement_metrics" class="rounded">
                            <span class="text-sm">Engagement Metrics (Likes, Comments, Shares, Saves)</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 p-2 rounded">
                            <input type="checkbox" x-model="customizeForm.widgets" value="video_metrics" class="rounded">
                            <span class="text-sm">Video Metrics (Watch Time, Skip Rate, Views)</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 p-2 rounded">
                            <input type="checkbox" x-model="customizeForm.widgets" value="audience_breakdown" class="rounded">
                            <span class="text-sm">Audience Breakdown (Follower vs Non-Follower)</span>
                        </label>
                    </div>

                    <h4 class="font-semibold mb-3 mt-6 text-gray-700">Available Platforms</h4>
                    <div class="space-y-2 bg-gray-50 p-4 rounded-lg">
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 p-2 rounded">
                            <input type="checkbox" x-model="customizeForm.platforms" value="facebook" class="rounded">
                            <span class="text-sm">Facebook</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 p-2 rounded">
                            <input type="checkbox" x-model="customizeForm.platforms" value="instagram" class="rounded">
                            <span class="text-sm">Instagram</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 p-2 rounded">
                            <input type="checkbox" x-model="customizeForm.platforms" value="linkedin" class="rounded">
                            <span class="text-sm">LinkedIn</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 p-2 rounded">
                            <input type="checkbox" x-model="customizeForm.platforms" value="youtube" class="rounded">
                            <span class="text-sm">YouTube</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 p-2 rounded">
                            <input type="checkbox" x-model="customizeForm.platforms" value="x" class="rounded">
                            <span class="text-sm">X (Twitter)</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 p-2 rounded">
                            <input type="checkbox" x-model="customizeForm.platforms" value="tiktok" class="rounded">
                            <span class="text-sm">TikTok</span>
                        </label>
                    </div>

                    <div class="mt-3 p-3 rounded-lg border border-gray-200 bg-gray-50">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" x-model="customizeForm.mirrorIGToFB" class="rounded">
                            <span class="text-sm text-gray-700">Mirror Instagram posts to Facebook (show FB icon, KPIs separate)</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">When enabled, Instagram posts will also display Facebook in the calendar. Facebook KPIs can be imported via CSV or entered manually; media files are not required for Facebook.</p>
                    </div>
                </div>

                <!-- Right: KPI Goals by Year -->
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-semibold text-gray-700">KPI Goals by Year</h4>
                        <div class="flex gap-2">
                            <button @click="showBulkAddModal = true" class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">+ Bulk Add by Platform</button>
                            <button @click="addKPIGoal()" class="text-sm bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700">+ Add Single Goal</button>
                        </div>
                    </div>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        <template x-for="(goal, index) in customizeForm.kpiGoals" :key="index">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="flex justify-between items-start mb-3">
                                    <h5 class="font-semibold text-gray-700">Goal #<span x-text="index + 1"></span></h5>
                                    <button @click="removeKPIGoal(index)" class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                                
                                <!-- KPI Type Selection -->
                                <div class="mb-3">
                                    <label class="text-xs text-gray-600 font-medium">KPI Type</label>
                                    <select x-model="goal.kpiType" @change="updateGoalName(goal)" class="w-full border rounded px-2 py-1.5 text-sm">
                                        <option value="">Select KPI Type</option>
                                        <option value="reach">Total Reach</option>
                                        <option value="engagement">Total Engagement</option>
                                        <option value="impressions">Total Impressions</option>
                                        <option value="engagement_rate">Engagement Rate</option>
                                        <option value="platform_reach">Platform-Specific Reach</option>
                                        <option value="platform_engagement">Platform-Specific Engagement</option>
                                        <option value="followers">Followers (Manual)</option>
                                        <option value="custom">Custom (Manual)</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <span x-show="['reach', 'engagement', 'impressions', 'engagement_rate', 'platform_reach', 'platform_engagement'].includes(goal.kpiType)" class="text-green-600">✓ Auto-tracked from dashboard data</span>
                                        <span x-show="['followers', 'custom'].includes(goal.kpiType)" class="text-orange-600">⚠ Requires manual updates</span>
                                    </p>
                                </div>

                                <!-- Platform Selection (only for platform-specific KPIs) -->
                                <div x-show="goal.kpiType && goal.kpiType.startsWith('platform_')" class="mb-3">
                                    <label class="text-xs text-gray-600 font-medium">Platform</label>
                                    <select x-model="goal.platform" @change="updateGoalName(goal)" class="w-full border rounded px-2 py-1.5 text-sm">
                                        <option value="">Select Platform</option>
                                        <option value="facebook">Facebook</option>
                                        <option value="instagram">Instagram</option>
                                        <option value="linkedin">LinkedIn</option>
                                        <option value="youtube">YouTube</option>
                                        <option value="x">X (Twitter)</option>
                                        <option value="tiktok">TikTok</option>
                                    </select>
                                </div>

                                <!-- Goal Name (auto-generated or custom) -->
                                <div class="mb-3">
                                    <label class="text-xs text-gray-600 font-medium">Goal Name</label>
                                    <input type="text" x-model="goal.name" placeholder="e.g., Instagram Followers" class="w-full border rounded px-2 py-1.5 text-sm">
                                </div>

                                <!-- Year and Target -->
                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <label class="text-xs text-gray-600 font-medium">Year</label>
                                        <input type="number" x-model="goal.year" placeholder="2025" min="2020" max="2030" class="w-full border rounded px-2 py-1.5 text-sm">
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-600 font-medium">Target Value</label>
                                        <input type="number" x-model="goal.targetValue" placeholder="10000" class="w-full border rounded px-2 py-1.5 text-sm">
                                    </div>
                                </div>

                                <!-- Current Value (only for manual KPIs) -->
                                <div x-show="['followers', 'custom'].includes(goal.kpiType)" class="mb-3">
                                    <label class="text-xs text-gray-600 font-medium">Current Value</label>
                                    <input type="number" x-model="goal.currentValue" placeholder="Current value" class="w-full border rounded px-2 py-1.5 text-sm">
                                    <p class="text-xs text-gray-500 mt-1">This will need to be updated manually</p>
                                </div>

                                <!-- Auto-tracking indicator -->
                                <div x-show="['reach', 'engagement', 'impressions', 'engagement_rate', 'platform_reach', 'platform_engagement'].includes(goal.kpiType)" class="mb-3 p-2 bg-green-50 border border-green-200 rounded">
                                    <p class="text-xs text-green-700">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Current value will be automatically tracked from dashboard metrics
                                    </p>
                                </div>

                                <!-- Description -->
                                <div class="mb-2">
                                    <label class="text-xs text-gray-600 font-medium">Description (Optional)</label>
                                    <textarea x-model="goal.description" rows="2" placeholder="Optional notes..." class="w-full border rounded px-2 py-1.5 text-sm"></textarea>
                                </div>
                            </div>
                        </template>
                        <div x-show="customizeForm.kpiGoals.length === 0" class="text-center text-gray-500 text-sm py-8">
                            No KPI goals yet. Click "+ Add Goal" to create one.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Post Requirements -->
            <div class="mt-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
                <h4 class="font-semibold mb-3 text-gray-700">Monthly Post Requirements</h4>
                <p class="text-sm text-gray-600 mb-3">Set the required number of posts this client needs each month to track progress.</p>
                <div class="grid grid-cols-3 gap-4 mb-3">
                    <div>
                        <label class="text-xs text-gray-600">Required Posts Per Month</label>
                        <input type="number" x-model="customizeForm.monthlyPostTarget" placeholder="20" min="1" max="100" class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Client Start Date (Optional)</label>
                        <input type="date" x-model="customizeForm.clientStartDate" class="w-full border rounded px-3 py-2 text-sm">
                        <p class="text-xs text-gray-500 mt-1">Leave empty for year-round clients</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Post Types Breakdown (Optional)</label>
                        <textarea x-model="customizeForm.postTypeBreakdown" rows="2" placeholder="e.g., 10 static images, 5 videos, 5 reels" class="w-full border rounded px-3 py-2 text-sm"></textarea>
                    </div>
                </div>
            </div>

            <!-- Social Media Connections Section -->
            <div class="mt-6 pt-6 border-t">
                <h4 class="text-lg font-semibold mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    Social Media Connections
                </h4>
                <p class="text-sm text-gray-600 mb-4">Connect social media accounts to automatically sync posts and metrics.</p>
                
                <!-- Auto-Sync Info -->
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-blue-900">Automatic Sync Enabled</p>
                            <p class="text-xs text-blue-700 mt-1">Posts are automatically synced twice daily at 9 AM and 5 PM for all brands with connected accounts.</p>
                            <button 
                                @click="syncAllBrands()"
                                class="mt-2 px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors"
                            >
                                <svg class="inline w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Sync All Brands Now
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Facebook/Instagram Connection -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-lg border border-blue-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                <div>
                                    <h5 class="font-semibold text-gray-800">Facebook & Instagram</h5>
                                    <p class="text-xs text-gray-600" x-show="!socialMediaStatus.facebook?.connected">Not connected</p>
                                    <p class="text-xs text-green-600" x-show="socialMediaStatus.facebook?.connected">
                                        ✓ Connected: <span x-text="socialMediaStatus.facebook?.username"></span>
                                    </p>
                                    <p class="text-xs text-green-600 mt-1" x-show="socialMediaStatus.instagram?.connected">
                                        ✓ Instagram: @<span x-text="socialMediaStatus.instagram?.username"></span>
                                    </p>
                                    <p class="text-xs text-amber-700 mt-2" x-show="socialMediaStatus.facebook?.connected && !socialMediaStatus.instagram?.connected">
                                        Facebook is connected, but no Instagram Business account is stored yet. Run <strong>Sync Posts</strong> to auto-link from your Page, or enter the IG Business Account ID below (from Meta Business Suite / Graph API Explorer).
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button 
                                x-show="!socialMediaStatus.facebook?.connected"
                                @click="connectFacebook(customizingClient._id)" 
                                class="flex-1 bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700 transition-colors">
                                Connect Account
                            </button>
                            <button 
                                x-show="socialMediaStatus.facebook?.connected"
                                @click="syncSocialMedia(customizingClient._id)" 
                                class="flex-1 bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700 transition-colors">
                                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Sync Posts
                            </button>
                            <button 
                                x-show="socialMediaStatus.facebook?.connected"
                                @click="disconnectSocialMedia(customizingClient._id, 'facebook')" 
                                class="bg-red-600 text-white px-3 py-2 rounded text-sm hover:bg-red-700 transition-colors">
                                Disconnect
                            </button>
                        </div>
                        <div x-show="socialMediaStatus.facebook?.connected" class="mt-3 pt-3 border-t border-blue-200 space-y-2">
                            <p class="text-xs font-medium text-gray-700">Manual Instagram link (optional)</p>
                            <p class="text-xs text-gray-500">If auto-discovery fails, paste the numeric <strong>Instagram Business Account ID</strong> (not @handle). Page access token above must still be valid.</p>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <input type="text" x-model="manualIgAccountId" placeholder="e.g. 17841400008460056" class="flex-1 border rounded px-2 py-1.5 text-xs">
                                <input type="text" x-model="manualIgUsername" placeholder="@username (optional)" class="flex-1 border rounded px-2 py-1.5 text-xs">
                                <button type="button" @click="linkManualInstagram(customizingClient._id)" class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700 whitespace-nowrap">Link Instagram</button>
                            </div>
                        </div>
                    </div>

                    <!-- Coming Soon Platforms -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 opacity-60">
                        <div class="flex items-center mb-3">
                            <svg class="w-8 h-8 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                            <div>
                                <h5 class="font-semibold text-gray-600">LinkedIn, YouTube, X</h5>
                                <p class="text-xs text-gray-500">Coming soon</p>
                            </div>
                        </div>
                        <button disabled class="w-full bg-gray-300 text-gray-500 px-3 py-2 rounded text-sm cursor-not-allowed">
                            Not Available Yet
                        </button>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-xs text-blue-800">
                        <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <strong>Note:</strong> Connecting Facebook will also connect Instagram if your Facebook Page has an Instagram Business Account linked.
                    </p>
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-6 pt-4 border-t">
                <button type="button" @click="showCustomizeModal = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                <button @click="saveCustomization()" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Save Customization</button>
            </div>
        </div>
    </div>

    <!-- Bulk Add KPI Goals Modal -->
    <div x-show="showBulkAddModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showBulkAddModal = false">
        <div class="relative top-10 mx-auto p-6 border w-full max-w-3xl shadow-lg rounded-md bg-white mb-10">
            <h3 class="text-lg font-bold mb-4">Bulk Add Goals by Platform</h3>
            <p class="text-sm text-gray-600 mb-4">Quickly create goals for multiple platforms at once. Select the platforms and metric type, then set individual targets.</p>
            
            <!-- Metric Type Selection -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">What metric do you want to track?</label>
                <select x-model="bulkAddMetric" class="w-full border rounded px-3 py-2">
                    <option value="">Select Metric Type</option>
                    <option value="platform_reach">Reach (per platform)</option>
                    <option value="platform_engagement">Engagement (per platform)</option>
                    <option value="followers">Followers (manual, per platform)</option>
                </select>
            </div>

            <!-- Platform Selection with Targets -->
            <div x-show="bulkAddMetric" class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select platforms and set targets:</label>
                <div class="space-y-3">
                    <template x-for="platform in ['facebook', 'instagram', 'linkedin', 'youtube', 'x', 'tiktok']" :key="platform">
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded border">
                            <input type="checkbox" :id="'bulk_' + platform" x-model="bulkAddPlatforms[platform].enabled" class="rounded">
                            <label :for="'bulk_' + platform" class="flex-1 font-medium capitalize cursor-pointer" x-text="platform === 'x' ? 'X (Twitter)' : platform"></label>
                            <div x-show="bulkAddPlatforms[platform].enabled" class="flex items-center gap-2">
                                <label class="text-sm text-gray-600">Target:</label>
                                <input type="number" x-model="bulkAddPlatforms[platform].target" placeholder="e.g., 10000" class="w-32 border rounded px-2 py-1 text-sm">
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Year Selection -->
            <div x-show="bulkAddMetric" class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                <input type="number" x-model="bulkAddYear" :value="new Date().getFullYear()" min="2020" max="2030" class="w-full border rounded px-3 py-2">
            </div>

            <!-- Preview -->
            <div x-show="bulkAddMetric && Object.values(bulkAddPlatforms).some(p => p.enabled)" class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded">
                <h4 class="font-semibold text-blue-900 mb-2">Preview: Goals to be created</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <template x-for="platform in ['facebook', 'instagram', 'linkedin', 'youtube', 'x', 'tiktok']" :key="platform">
                        <li x-show="bulkAddPlatforms[platform].enabled">
                            <span class="font-medium capitalize" x-text="platform === 'x' ? 'X' : platform"></span>
                            <span x-text="bulkAddMetric === 'platform_reach' ? 'Reach' : bulkAddMetric === 'platform_engagement' ? 'Engagement' : 'Followers'"></span>
                            - Target: <span x-text="bulkAddPlatforms[platform].target || 0"></span>
                            <span x-show="['platform_reach', 'platform_engagement'].includes(bulkAddMetric)" class="text-green-600">(Auto-tracked)</span>
                        </li>
                    </template>
                </ul>
            </div>

            <div class="flex justify-end space-x-2 pt-4 border-t">
                <button type="button" @click="showBulkAddModal = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                <button @click="addBulkGoals()" :disabled="!bulkAddMetric || !Object.values(bulkAddPlatforms).some(p => p.enabled)" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">Add Goals</button>
            </div>
        </div>
    </div>

    <script src="<?php echo get_template_directory_uri(); ?>/instagram-import-parser.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/facebook-import-parser.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        const API_URL = typeof ESIROM_API_URL !== 'undefined' ? ESIROM_API_URL : 'https://esirom-hub-backend-production.up.railway.app/api';

        document.addEventListener('alpine:init', () => {
            Alpine.data('adminPanel', () => ({
                isSidebarOpen: true,
                activeTab: 'users',
                user: {},
                users: [],
                pendingUsers: [],
                clients: [],
                showPwModal: false,
                pwCurrent: '', pwNew: '', pwConfirm: '', pwLoading: false, pwError: '', pwSuccess: '',
                showUserModal: false,
                showClientModal: false,
                workflowDigestLoading: false,
                reminderBrands: [],
                reminderTotals: { brands: 0, pendingItems: 0, recipients: 0 },
                selectedReminderClients: [],
                reminderLoading: false,
                reminderSending: false,
                showCustomizeModal: false,
                showBulkAddModal: false,
                showApprovalModal: false,
                showTempPasswordModal: false,
                temporaryPassword: '',
                approvingUser: null,
                approvalClientId: '',
                editingUser: null,
                editingClient: null,
                editingUpdate: null,
                customizingClient: null,
                socialMediaStatus: {},
                manualIgAccountId: '',
                manualIgUsername: '',
                bulkAddMetric: '',
                bulkAddYear: new Date().getFullYear(),
                bulkAddPlatforms: {
                    facebook: { enabled: false, target: 0 },
                    instagram: { enabled: false, target: 0 },
                    linkedin: { enabled: false, target: 0 },
                    youtube: { enabled: false, target: 0 },
                    x: { enabled: false, target: 0 },
                    tiktok: { enabled: false, target: 0 }
                },
                editingUpdate: null,
                userForm: { firstName: '', lastName: '', email: '', role: 'client', clientIds: [], assignedClients: [], isActive: true, department: '', multimediaRole: '', isManager: false },
                clientForm: { name: '', brandName: '', contactEmail: '', contactPhone: '', industry: '', logo: '' },
                customizeForm: { 
                    widgets: ['metrics', 'charts', 'kpis', 'pending', 'reports'], 
                    platforms: ['facebook', 'instagram', 'linkedin', 'youtube', 'x'],
                    kpiGoals: [],
                    monthlyPostTarget: 20,
                    clientStartDate: '',
                    mirrorIGToFB: false,
                    postTypeBreakdown: ''
                },
                
                // Toast notifications
                toasts: [],
                
                showToast(message, type = 'info', duration = 3000) {
                    const id = Date.now();
                    this.toasts.push({ id, message, type });
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, duration);
                },
                
                // Import functionality
                importClientId: '',
                importFile: null,
                importPreview: [],
                mediaUploadProgress: { uploaded: 0, total: 0 },
                apiImportClientId: '',
                metaAccessToken: '',
                apiImportFromDate: '',
                apiImportToDate: '',
                showMetaAPIGuide: false,

                async init() {
                    // Check auth
                    const token = localStorage.getItem('token');
                    const userStr = localStorage.getItem('user');
                    
                    if (!token || !userStr) {
                        window.location.href = '<?php echo esc_url(get_permalink(get_page_by_path('login'))); ?>';
                        return;
                    }

                    this.user = JSON.parse(userStr);

                    // Check if admin
                    if (this.user.role !== 'admin') {
                        this.showToast('Access denied. Admin only.', 'error', 3000);
                        window.location.href = '<?php echo esc_url(get_permalink(get_page_by_path('dashboard'))); ?>';
                        return;
                    }
                },

                async loadData() {
                    await Promise.all([
                        this.loadUsers(),
                        this.loadPendingUsers(),
                        this.loadClients()
                    ]);
                },

                async loadUsers() {
                    try {
                        const response = await fetch(`${API_URL}/users`, {
                            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
                        });
                        const data = await response.json();
                        if (data.success) this.users = data.data;
                    } catch (error) {
                        console.error('Error loading users:', error);
                    }
                },

                async loadPendingUsers() {
                    try {
                        const response = await fetch(`${API_URL}/users/pending/list`, {
                            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
                        });
                        const data = await response.json();
                        if (data.success) this.pendingUsers = data.data;
                    } catch (error) {
                        console.error('Error loading pending users:', error);
                    }
                },

                approveUser(user) {
                    // Show custom approval modal
                    this.approvingUser = user;
                    this.approvalClientId = '';
                    this.showApprovalModal = true;
                },

                async confirmApproval() {
                    if (!this.approvingUser) return;
                    
                    try {
                        const response = await fetch(`${API_URL}/users/${this.approvingUser._id}/approve`, {
                            method: 'PUT',
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                clientId: this.approvalClientId || undefined
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.showToast('User approved successfully!', 'success');
                            this.showApprovalModal = false;
                            this.approvingUser = null;
                            this.approvalClientId = '';
                            await this.loadPendingUsers();
                            await this.loadUsers();
                        } else {
                            this.showToast(data.message || 'Error approving user', 'error');
                        }
                    } catch (error) {
                        console.error('Error approving user:', error);
                        this.showToast('Error approving user', 'error');
                    }
                },

                async rejectUser(user) {
                    if (!confirm(`Reject registration for ${user.firstName} ${user.lastName}? This action cannot be undone.`)) return;

                    try {
                        const response = await fetch(`${API_URL}/users/${user._id}/reject`, {
                            method: 'PUT',
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            }
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.showToast('User registration rejected', 'success');
                            await this.loadPendingUsers();
                        } else {
                            this.showToast(data.message || 'Error rejecting user', 'error');
                        }
                    } catch (error) {
                        console.error('Error rejecting user:', error);
                        this.showToast('Error rejecting user', 'error');
                    }
                },

                async loadClients() {
                    try {
                        const response = await fetch(`${API_URL}/clients?includeInactive=true`, {
                            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
                        });
                        const data = await response.json();
                        if (data.success) {
                            // Backfill brandName in case backend still uses companyName
                            this.clients = data.data.map(c => ({
                                ...c,
                                brandName: c.brandName || c.companyName || c.name || ''
                            }));
                        }
                    } catch (error) {
                        console.error('Error loading clients:', error);
                    }
                },

                editUser(user) {
                    this.editingUser = user;
                    const clientIdsFromUser = Array.isArray(user.clientIds)
                        ? user.clientIds.map(c => c?._id || c).filter(Boolean)
                        : [];
                    const primaryClientId = user.clientId?._id || user.clientId;
                    const mergedClientIds = [...new Set([...(primaryClientId ? [primaryClientId] : []), ...clientIdsFromUser])];
                    const assignedClientIds = Array.isArray(user.assignedClients)
                        ? user.assignedClients.map(c => c?._id || c).filter(Boolean)
                        : [];
                    this.userForm = {
                        firstName: user.firstName,
                        lastName: user.lastName,
                        email: user.email,
                        password: '',
                        role: user.role,
                        clientIds: mergedClientIds,
                        assignedClients: assignedClientIds,
                        isActive: user.isActive !== undefined ? user.isActive : true,
                        department: user.department || '',
                        multimediaRole: user.multimediaRole || '',
                        isManager: user.isManager || false
                    };
                    this.showUserModal = true;
                },

                async saveUser() {
                    try {
                        const url = this.editingUser ? `${API_URL}/users/${this.editingUser._id}` : `${API_URL}/auth/register`;
                        const method = this.editingUser ? 'PUT' : 'POST';
                        
                        // Prepare data based on role
                        const userData = {
                            firstName: this.userForm.firstName,
                            lastName: this.userForm.lastName,
                            email: this.userForm.email,
                            role: this.userForm.role,
                            isActive: this.userForm.isActive
                        };
                        
                        // Add new password if provided (for editing users)
                        if (this.editingUser && this.userForm.newPassword) {
                            userData.newPassword = this.userForm.newPassword;
                        }
                        
                        if (this.userForm.role === 'client') {
                            const normalizedClientIds = [...new Set((this.userForm.clientIds || []).filter(Boolean))];
                            userData.clientIds = normalizedClientIds;
                            userData.clientId = normalizedClientIds[0] || undefined;
                        } else if (this.userForm.role === 'brand_rep' || this.userForm.role === 'admin') {
                            userData.assignedClients = [...new Set((this.userForm.assignedClients || []).filter(Boolean))];
                            userData.department = this.userForm.department || null;
                            userData.multimediaRole = this.userForm.department === 'multimedia' ? (this.userForm.multimediaRole || null) : null;
                            userData.isManager = Boolean(this.userForm.isManager);
                        }

                        const response = await fetch(url, {
                            method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            },
                            body: JSON.stringify(userData)
                        });

                        const data = await response.json();
                        if (data.success || response.ok) {
                            this.showUserModal = false;
                            this.userForm = { firstName: '', lastName: '', email: '', role: 'client', clientIds: [], assignedClients: [], isActive: true, department: '', multimediaRole: '', isManager: false };
                            await this.loadUsers();
                            
                            // Show temporary password modal for new users
                            if (!this.editingUser && data.temporaryPassword) {
                                this.temporaryPassword = data.temporaryPassword;
                                this.showTempPasswordModal = true;
                            } else {
                                this.showToast(this.editingUser ? 'User updated!' : 'User created!', 'success', 3000);
                            }
                        } else {
                            this.showToast(data.message || 'Error saving user', 'error', 5000);
                        }
                    } catch (error) {
                        this.showToast('Error saving user', 'error', 5000);
                    }
                },

                copyTempPassword() {
                    navigator.clipboard.writeText(this.temporaryPassword).then(() => {
                        this.showToast('Password copied to clipboard!', 'success', 2000);
                    }).catch(() => {
                        this.showToast('Failed to copy password', 'error', 3000);
                    });
                },

                async deleteUser(id) {
                    if (!confirm('Are you sure you want to delete this user?')) return;
                    
                    try {
                        const response = await fetch(`${API_URL}/users/${id}`, {
                            method: 'DELETE',
                            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
                        });

                        if (response.ok) {
                            this.showToast('User deleted!', 'success', 3000);
                            await this.loadUsers();
                        }
                    } catch (error) {
                        console.error('Error deleting user:', error);
                    }
                },

                editClient(client) {
                    this.editingClient = client;
                    this.clientForm = {
                        name: client.name,
                        brandName: client.brandName || client.companyName || client.name || '',
                        contactEmail: client.contactEmail,
                        contactPhone: client.contactPhone || '',
                        industry: client.industry || '',
                        logo: client.logo || '',
                        serviceType: client.serviceType || 'social_media'
                    };
                    this.showClientModal = true;
                },

                async saveClient() {
                    try {
                        // Ensure brandName is set (use name as fallback if empty)
                        if (!this.clientForm.brandName && this.clientForm.name) {
                            this.clientForm.brandName = this.clientForm.name;
                        }
                        
                        // Validate that brandName exists
                        if (!this.clientForm.brandName) {
                            this.showToast('Brand Name is required', 'error', 3000);
                            return;
                        }
                        
                        const url = this.editingClient ? `${API_URL}/clients/${this.editingClient._id}` : `${API_URL}/clients`;
                        const method = this.editingClient ? 'PUT' : 'POST';

                        // Build payload compatible with both old and new backends
                        const payload = {
                            name: this.clientForm.name || this.clientForm.brandName || '',
                            brandName: this.clientForm.brandName || this.clientForm.name || '',
                            // Send companyName for older backends expecting this field
                            companyName: this.clientForm.brandName || this.clientForm.name || '',
                            contactEmail: this.clientForm.contactEmail || 'client@example.com',
                            contactPhone: this.clientForm.contactPhone || '',
                            industry: this.clientForm.industry || '',
                            logo: this.clientForm.logo || '',
                            serviceType: this.clientForm.serviceType || 'social_media'
                        };

                        const response = await fetch(url, {
                            method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.showToast(this.editingClient ? 'Client updated!' : 'Client created!', 'success', 3000);
                            this.showClientModal = false;
                            this.clientForm = { name: '', brandName: '', contactEmail: '', contactPhone: '', industry: '', logo: '', serviceType: 'social_media' };
                            await this.loadClients();
                        } else {
                            console.error('API Error:', data);
                            this.showToast(data.message || 'Error saving client', 'error', 5000);
                        }
                    } catch (error) {
                        console.error('Error saving client:', error);
                        this.showToast('Error saving client. Check console for details.', 'error', 5000);
                    }
                },

                async deleteClient(id) {
                    if (!confirm('Are you sure you want to delete this client?')) return;
                    
                    try {
                        const response = await fetch(`${API_URL}/clients/${id}`, {
                            method: 'DELETE',
                            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
                        });

                        if (response.ok) {
                            this.showToast('Client deleted!', 'success', 3000);
                            await this.loadClients();
                        }
                    } catch (error) {
                        console.error('Error deleting client:', error);
                    }
                },

                async customizeClient(client) {
                    this.customizingClient = client;
                    
                    // Load social media connection status
                    await this.loadSocialMediaStatus(client._id);
                    
                    try {
                        // Load from database first
                        const response = await fetch(`${API_URL}/clients/${client._id}`, {
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            }
                        });

                        if (response.ok) {
                            const data = await response.json();
                            const dashboardConfig = data.data.dashboardConfig || {};
                            
                            // Load from database or use defaults
                            this.customizeForm = {
                                widgets: dashboardConfig.enabledWidgets || ['metrics', 'charts', 'kpis', 'pending', 'reports'],
                                platforms: dashboardConfig.platforms || ['facebook', 'instagram', 'linkedin', 'youtube', 'x'],
                                kpiGoals: dashboardConfig.kpiGoals || [],
                                monthlyPostTarget: dashboardConfig.monthlyPostTarget || 20,
                                clientStartDate: dashboardConfig.clientStartDate || '',
                                mirrorIGToFB: dashboardConfig.mirrorIGToFB !== undefined ? dashboardConfig.mirrorIGToFB : false
                            };
                        } else {
                            // Fallback to localStorage if API fails
                            const stored = localStorage.getItem(`client_customize_${client._id}`);
                            if (stored) {
                                this.customizeForm = JSON.parse(stored);
                            } else {
                                // Default: all enabled
                                this.customizeForm = {
                                    widgets: ['metrics', 'charts', 'kpis', 'pending', 'reports'],
                                    platforms: ['facebook', 'instagram', 'linkedin', 'youtube', 'x'],
                                    kpiGoals: [],
                                    monthlyPostTarget: 20,
                                    clientStartDate: '',
                                    mirrorIGToFB: false
                                };
                            }
                        }
                    } catch (error) {
                        console.error('Error loading client config:', error);
                        // Fallback to defaults
                        this.customizeForm = {
                            widgets: ['metrics', 'charts', 'kpis', 'pending', 'reports'],
                            platforms: ['facebook', 'instagram', 'linkedin', 'youtube', 'x'],
                            kpiGoals: [],
                            monthlyPostTarget: 20,
                            clientStartDate: '',
                            mirrorIGToFB: false
                        };
                    }
                    
                    this.showCustomizeModal = true;
                },

                async loadSocialMediaStatus(clientId) {
                    try {
                        const response = await fetch(`${API_URL}/social-media/status/${clientId}`, {
                            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.socialMediaStatus = data.data;
                        }
                    } catch (error) {
                        console.error('Error loading social media status:', error);
                    }
                },

                async connectFacebook(clientId) {
                    try {
                        const response = await fetch(`${API_URL}/social-media/auth/facebook?clientId=${clientId}`, {
                            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
                        });
                        const data = await response.json();
                        
                        if (data.success && data.authUrl) {
                            // Open OAuth popup
                            const width = 600;
                            const height = 700;
                            const left = (screen.width - width) / 2;
                            const top = (screen.height - height) / 2;
                            
                            const popup = window.open(
                                data.authUrl,
                                'Facebook OAuth',
                                `width=${width},height=${height},left=${left},top=${top}`
                            );
                            
                            // Check for popup close and reload status
                            const checkPopup = setInterval(() => {
                                if (popup.closed) {
                                    clearInterval(checkPopup);
                                    this.loadSocialMediaStatus(clientId);
                                    this.showToast('Checking connection status...', 'info', 2000);
                                }
                            }, 1000);
                        } else {
                            this.showToast(data.message || 'Failed to get authorization URL', 'error', 5000);
                        }
                    } catch (error) {
                        console.error('Error connecting Facebook:', error);
                        this.showToast('Error connecting Facebook account', 'error', 5000);
                    }
                },

                async syncSocialMedia(clientId) {
                    try {
                        this.showToast('Syncing posts from social media...', 'info', 3000);
                        
                        const response = await fetch(`${API_URL}/social-media/sync/${clientId}`, {
                            method: 'POST',
                            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            const total = data.data.total;
                            const ig = data.data.instagram;
                            const fb = data.data.facebook;
                            const totalUpdated = data.data.totalUpdated || 0;
                            const igUpdated = data.data.instagramUpdated || 0;
                            const fbUpdated = data.data.facebookUpdated || 0;
                            const errors = data.data.errors || {};
                            
                            if (total > 0 || totalUpdated > 0) {
                                this.showToast(`✅ Synced ${total} new posts (IG: ${ig}, FB: ${fb}) and updated insights for ${totalUpdated} posts (IG: ${igUpdated}, FB: ${fbUpdated})`, 'success', 6000);
                            } else {
                                this.showToast('No new posts to sync and no insights to update. All posts are up to date.', 'info', 4000);
                            }

                            if (Object.keys(errors).length) {
                                const msg = Object.entries(errors).map(([k, v]) => `${k.toUpperCase()}: ${v}`).join(' | ');
                                this.showToast(`Sync completed with warnings: ${msg}`, 'error', 7000);
                            }
                            await this.loadSocialMediaStatus(clientId);
                        } else {
                            this.showToast(data.message || 'Failed to sync posts', 'error', 5000);
                        }
                    } catch (error) {
                        console.error('Error syncing social media:', error);
                        this.showToast('Error syncing posts', 'error', 5000);
                    }
                },

                async linkManualInstagram(clientId) {
                    const id = (this.manualIgAccountId || '').trim();
                    if (!id) {
                        this.showToast('Enter the Instagram Business Account ID (numeric)', 'error', 4000);
                        return;
                    }
                    try {
                        const response = await fetch(`${API_URL}/social-media/link-instagram/${clientId}`, {
                            method: 'POST',
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                igAccountId: id,
                                igUsername: (this.manualIgUsername || '').trim()
                            })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.showToast(data.message || 'Instagram linked', 'success', 5000);
                            this.manualIgAccountId = '';
                            this.manualIgUsername = '';
                            await this.loadSocialMediaStatus(clientId);
                        } else {
                            this.showToast(data.message || 'Failed to link Instagram', 'error', 6000);
                        }
                    } catch (error) {
                        console.error('linkManualInstagram:', error);
                        this.showToast('Error linking Instagram', 'error', 5000);
                    }
                },

                async syncAllBrands() {
                    try {
                        this.showToast('Syncing posts for all brands...', 'info', 3000);
                        
                        const response = await fetch(`${API_URL}/social-media/sync-all`, {
                            method: 'POST',
                            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            const total = data.totalNewPosts;
                            const totalUpdated = data.totalUpdatedPosts || 0;
                            const clients = data.totalClients;
                            const results = Array.isArray(data.results) ? data.results : [];
                            const errorBrands = results.filter(r => r.error);
                            
                            if (total > 0 || totalUpdated > 0) {
                                this.showToast(`✅ Synced ${total} new posts and updated insights for ${totalUpdated} posts across ${clients} brands`, 'success', 7000);
                            } else {
                                this.showToast(`All ${clients} brands are up to date. No new posts or insights updates.`, 'info', 4000);
                            }

                            if (errorBrands.length) {
                                const msg = errorBrands.slice(0, 3).map(r => `${r.clientName}: ${r.error}`).join(' | ');
                                this.showToast(`Some brands had sync issues: ${msg}${errorBrands.length > 3 ? ' | ...' : ''}`, 'error', 8000);
                            }
                        } else {
                            this.showToast(data.message || 'Failed to sync all brands', 'error', 5000);
                        }
                    } catch (error) {
                        console.error('Error syncing all brands:', error);
                        this.showToast('Error syncing all brands', 'error', 5000);
                    }
                },

                async loadClientReminders() {
                    this.reminderLoading = true;
                    try {
                        const response = await fetch(`${API_URL}/admin/client-reminders/preview`, {
                            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.reminderBrands = data.brands || [];
                            this.reminderTotals = data.totals || { brands: 0, pendingItems: 0, recipients: 0 };
                            this.selectedReminderClients = this.reminderBrands
                                .filter(b => b.clientUsers && b.clientUsers.length)
                                .map(b => b.clientId);
                        } else {
                            this.showToast(data.message || 'Failed to load reminders', 'error', 5000);
                        }
                    } catch (error) {
                        console.error('loadClientReminders:', error);
                        this.showToast('Could not load client reminders', 'error', 5000);
                    } finally {
                        this.reminderLoading = false;
                    }
                },

                toggleReminderClient(clientId, checked) {
                    if (checked) {
                        if (!this.selectedReminderClients.includes(clientId)) {
                            this.selectedReminderClients.push(clientId);
                        }
                    } else {
                        this.selectedReminderClients = this.selectedReminderClients.filter(id => id !== clientId);
                    }
                },

                toggleAllReminderClients(checked) {
                    if (checked) {
                        this.selectedReminderClients = this.reminderBrands
                            .filter(b => b.clientUsers && b.clientUsers.length)
                            .map(b => b.clientId);
                    } else {
                        this.selectedReminderClients = [];
                    }
                },

                async sendClientReminders() {
                    if (!this.selectedReminderClients.length) return;
                    const brands = this.reminderBrands.filter(b => this.selectedReminderClients.includes(b.clientId));
                    const itemCount = brands.reduce((n, b) => n + (b.pendingCount || 0), 0);
                    const emails = new Set();
                    brands.forEach(b => (b.clientUsers || []).forEach(u => emails.add(u.email)));
                    if (!confirm(`Send reminder emails to ${emails.size} client contact(s) covering ${itemCount} pending item(s) across ${brands.length} brand(s)?`)) return;

                    this.reminderSending = true;
                    try {
                        const response = await fetch(`${API_URL}/admin/client-reminders/send`, {
                            method: 'POST',
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ clientIds: this.selectedReminderClients })
                        });
                        const data = await response.json();
                        if (response.ok && data.emailsSent > 0) {
                            this.showToast(data.message || 'Client reminders sent', 'success', 8000);
                        } else {
                            this.showToast(data.message || 'No reminders were sent', data.emailsSent === 0 ? 'info' : 'error', 8000);
                        }
                    } catch (error) {
                        console.error('sendClientReminders:', error);
                        this.showToast('Could not send client reminders', 'error', 5000);
                    } finally {
                        this.reminderSending = false;
                    }
                },

                async testWorkflowDigest() {
                    if (!confirm('Send the workflow digest to the Google Space now? (Overdue, due today, due in 3 days)')) return;
                    this.workflowDigestLoading = true;
                    try {
                        const response = await fetch(`${API_URL}/admin/test-workflow-digest`, {
                            method: 'POST',
                            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
                        });
                        const data = await response.json().catch(() => ({}));
                        if (response.ok && data.success) {
                            const counts = data.result?.counts;
                            const sample = data.result?.samplePosted;
                            const summary = counts ? ` (${counts.overdue} overdue, ${counts.dueToday} due today, ${counts.dueSoon} due soon)` : '';
                            this.showToast(`${sample ? 'Test card' : 'Workflow digest'} sent to Google Space${summary} — check AgencyHUB: Workflow Notifications`, 'success', 9000);
                        } else if (response.status === 401) {
                            this.showToast('Session expired — log in again as admin', 'error', 5000);
                        } else {
                            this.showToast(data.message || `Failed (${response.status})`, 'error', 6000);
                        }
                    } catch (error) {
                        console.error('testWorkflowDigest:', error);
                        this.showToast('Could not reach the API — check Admin → API URL settings', 'error', 6000);
                    } finally {
                        this.workflowDigestLoading = false;
                    }
                },

                async disconnectSocialMedia(clientId, platform) {
                    if (!confirm(`Are you sure you want to disconnect ${platform}? This will remove the connection but keep existing posts.`)) {
                        return;
                    }
                    
                    try {
                        const response = await fetch(`${API_URL}/social-media/disconnect/${clientId}/${platform}`, {
                            method: 'DELETE',
                            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.showToast(`${platform} disconnected successfully`, 'success', 3000);
                            await this.loadSocialMediaStatus(clientId);
                        } else {
                            this.showToast(data.message || 'Failed to disconnect', 'error', 5000);
                        }
                    } catch (error) {
                        console.error('Error disconnecting:', error);
                        this.showToast('Error disconnecting account', 'error', 5000);
                    }
                },

                addKPIGoal() {
                    this.customizeForm.kpiGoals.push({
                        kpiType: '',
                        platform: '',
                        name: '',
                        year: new Date().getFullYear(),
                        targetValue: 0,
                        currentValue: 0,
                        unit: 'number',
                        description: '',
                        progress: 0,
                        isAutoTracked: false
                    });
                },

                updateGoalName(goal) {
                    // Auto-generate goal name based on KPI type and platform
                    if (!goal.kpiType) return;
                    
                    const kpiNames = {
                        'reach': 'Total Reach',
                        'engagement': 'Total Engagement',
                        'impressions': 'Total Impressions',
                        'engagement_rate': 'Engagement Rate',
                        'platform_reach': 'Reach',
                        'platform_engagement': 'Engagement',
                        'followers': 'Followers',
                        'custom': 'Custom Goal'
                    };
                    
                    let name = kpiNames[goal.kpiType] || goal.kpiType;
                    
                    // Add platform name for platform-specific goals
                    if (goal.kpiType.startsWith('platform_') && goal.platform) {
                        const platformName = goal.platform.charAt(0).toUpperCase() + goal.platform.slice(1);
                        name = `${platformName} ${name}`;
                    }
                    
                    goal.name = name;
                    goal.isAutoTracked = ['reach', 'engagement', 'impressions', 'engagement_rate', 'platform_reach', 'platform_engagement'].includes(goal.kpiType);
                },

                addBulkGoals() {
                    if (!this.bulkAddMetric) return;
                    
                    const kpiNames = {
                        'platform_reach': 'Reach',
                        'platform_engagement': 'Engagement',
                        'followers': 'Followers'
                    };
                    
                    let addedCount = 0;
                    
                    // Loop through all platforms
                    Object.keys(this.bulkAddPlatforms).forEach(platform => {
                        const platformData = this.bulkAddPlatforms[platform];
                        
                        if (platformData.enabled && platformData.target > 0) {
                            const platformName = platform === 'x' ? 'X' : platform.charAt(0).toUpperCase() + platform.slice(1);
                            const metricName = kpiNames[this.bulkAddMetric];
                            
                            this.customizeForm.kpiGoals.push({
                                kpiType: this.bulkAddMetric,
                                platform: platform,
                                name: `${platformName} ${metricName}`,
                                year: this.bulkAddYear,
                                targetValue: platformData.target,
                                currentValue: 0,
                                unit: 'number',
                                description: '',
                                progress: 0,
                                isAutoTracked: ['platform_reach', 'platform_engagement'].includes(this.bulkAddMetric)
                            });
                            
                            addedCount++;
                        }
                    });
                    
                    // Reset bulk add form
                    this.bulkAddMetric = '';
                    this.bulkAddYear = new Date().getFullYear();
                    Object.keys(this.bulkAddPlatforms).forEach(platform => {
                        this.bulkAddPlatforms[platform] = { enabled: false, target: 0 };
                    });
                    
                    this.showBulkAddModal = false;
                    this.showToast(`Added ${addedCount} goal(s) successfully!`, 'success', 3000);
                },

                removeKPIGoal(index) {
                    this.customizeForm.kpiGoals.splice(index, 1);
                },

                async saveCustomization() {
                    if (!this.customizingClient) return;
                    
                    // Calculate progress for each KPI
                    this.customizeForm.kpiGoals.forEach(goal => {
                        if (goal.targetValue > 0) {
                            goal.progress = (goal.currentValue / goal.targetValue) * 100;
                        } else {
                            goal.progress = 0;
                        }
                    });
                    
                    try {
                        // Save to database via API
                        const response = await fetch(`${API_URL}/clients/${this.customizingClient._id}/dashboard-config`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                            },
                            body: JSON.stringify({
                                platforms: this.customizeForm.platforms,
                                kpiGoals: this.customizeForm.kpiGoals,
                                monthlyPostTarget: this.customizeForm.monthlyPostTarget,
                                clientStartDate: this.customizeForm.clientStartDate,
                                mirrorIGToFB: this.customizeForm.mirrorIGToFB
                            })
                        });

                        if (response.ok) {
                            // Also save to localStorage as backup/cache
                            localStorage.setItem(
                                `client_customize_${this.customizingClient._id}`,
                                JSON.stringify(this.customizeForm)
                            );
                            
                            this.showToast('Dashboard customization saved to database!', 'success', 4000);
                            this.showCustomizeModal = false;
                            this.customizingClient = null;
                        } else {
                            const error = await response.json();
                            this.showToast(`Error: ${error.message || 'Failed to save'}`, 'error', 4000);
                        }
                    } catch (error) {
                        console.error('Error saving customization:', error);
                        this.showToast('Error saving customization. Please try again.', 'error', 4000);
                    }
                },

                get pageTitle() {
                    switch (this.activeTab) {
                        case 'users':
                            return 'User Management';
                        case 'pending':
                            return 'Pending Registrations';
                        case 'clients':
                            return 'Client Management';
                        case 'emails':
                            return 'Emails & Notifications';
                        case 'import':
                            return 'Import Posts';
                        default:
                            return 'Admin Panel';
                    }
                },

                get pageSubtitle() {
                    switch (this.activeTab) {
                        case 'users':
                            return 'Manage staff accounts and access levels';
                        case 'pending':
                            return 'Review and approve new user registration requests';
                        case 'clients':
                            return 'Manage brands, dashboards, and client settings';
                        case 'emails':
                            return 'Client Content Bank reminders and team workflow digest';
                        case 'import':
                            return 'Import historical posts and performance data';
                        default:
                            return 'Agency Hub administration';
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
                    window.location.href = '<?php echo esc_url(get_permalink(get_page_by_path('login'))); ?>';
                },

                // Import Functions
                handleImportFile(event) {
                    this.importFile = event.target.files;
                },

                downloadTemplate() {
                    const csvContent = `date,platform,content_type,caption,reach,engagement,impressions,clicks,shares,comments,saves
2025-01-15,instagram,static,"Check out our new product!",5000,450,6000,120,25,30,40
2025-01-16,facebook,video,"Behind the scenes video",8000,720,9500,200,45,60,0
2025-01-17,instagram,reel,"Quick tips for success",12000,1200,15000,300,80,100,150`;
                    
                    const blob = new Blob([csvContent], { type: 'text/csv' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'post_import_template.csv';
                    a.click();
                    window.URL.revokeObjectURL(url);
                },

                async processManualImport() {
                    if (!this.importFile || !this.importClientId) {
                        this.showToast('Please select a client and upload a file', 'warning', 3000);
                        return;
                    }

                    try {
                        // Check if it's an Instagram folder export (multiple files)
                        if (this.importFile.length > 1) {
                            await this.processInstagramFolderImport();
                        } else {
                            // Single file import (CSV/JSON)
                            await this.processSingleFileImport();
                        }
                    } catch (error) {
                        console.error('Import error:', error);
                        this.showToast('Error processing import: ' + error.message, 'error', 5000);
                    }
                },

                async processInstagramFolderImport() {
                    // Detect if it's Instagram or Facebook export
                    const fileList = Array.from(this.importFile);
                    const isFacebook = fileList.some(f => f.webkitRelativePath.includes("this_profile's_activity_across_facebook"));
                    const isInstagram = fileList.some(f => f.webkitRelativePath.includes('your_instagram_activity'));
                    
                    if (isInstagram) {
                        this.showToast('Processing Instagram export folder...', 'info', 5000);
                        
                        try {
                            const parser = new InstagramDataParser();
                            const parsedData = await parser.parseExport(fileList);
                            
                            if (!parsedData || !parsedData.posts) {
                                throw new Error('Invalid Instagram export data - no posts found');
                            }
                            
                            // Get client name
                            const client = this.clients.find(c => c._id === this.importClientId);
                            const clientName = client ? client.brandName : this.importClientId;
                            
                            // Generate posts with KPIs
                            this.importPreview = parser.generatePostsWithKPIs(this.importClientId, clientName);
                            
                            const postsCount = parsedData.posts ? parsedData.posts.length : 0;
                            const reelsCount = parsedData.reels ? parsedData.reels.length : 0;
                            const storiesCount = parsedData.stories ? parsedData.stories.length : 0;
                            
                            this.showToast(`Instagram import complete! Found ${postsCount} posts, ${reelsCount} reels, ${storiesCount} stories`, 'success', 5000);
                        } catch (error) {
                            console.error('Instagram import error:', error);
                            this.showToast(`Instagram import failed: ${error.message}`, 'error', 5000);
                        }
                    } else {
                        this.showToast('Could not detect export type. Please select the entire Instagram or Facebook export folder.', 'error', 5000);
                    }
                },
                
                async processFacebookFolderImport() {
                    this.showToast('Processing Facebook export folder...', 'info', 5000);
                    
                    try {
                        const parser = new FacebookDataParser();
                        const fileList = Array.from(this.importFile);
                        const parsedData = await parser.parseExport(fileList);
                        
                        if (!parsedData || !parsedData.posts) {
                            throw new Error('Invalid Facebook export data - no posts found');
                        }
                        
                        // Get client name
                        const client = this.clients.find(c => c._id === this.importClientId);
                        const clientName = client ? client.companyName : this.importClientId;
                        
                        // Generate posts with estimated KPIs
                        this.importPreview = parser.generatePostsWithKPIs(this.importClientId, clientName);
                        
                        const postsCount = parsedData.posts ? parsedData.posts.length : 0;
                        const videosCount = parsedData.videos ? parsedData.videos.length : 0;
                        
                        this.showToast(`Facebook import complete! Found ${postsCount} posts, ${videosCount} videos (KPIs set to 0)`, 'success', 5000);
                    } catch (error) {
                        console.error('Facebook import error:', error);
                        this.showToast(`Facebook import failed: ${error.message}`, 'error', 5000);
                    }
                },

                async processSingleFileImport() {
                    const file = this.importFile[0];
                    const reader = new FileReader();
                    
                    reader.onload = async (e) => {
                        try {
                            const content = e.target.result;
                            let posts = [];

                            if (file.name.endsWith('.json')) {
                                posts = JSON.parse(content);
                            } else if (file.name.endsWith('.csv')) {
                                posts = this.parseCSV(content);
                            }

                            // Transform to our post format
                            this.importPreview = posts.map(row => ({
                                id: 'post_' + Date.now() + '_' + Math.random(),
                                clientId: this.importClientId,
                                scheduledDate: row.date || row.scheduledDate,
                                platforms: Array.isArray(row.platform) ? row.platform : [row.platform],
                                contentType: row.content_type || row.contentType || 'static',
                                caption: row.caption || '',
                                status: 'completed',
                                kpis: {
                                    [`${row.platform}_reach`]: parseInt(row.reach) || 0,
                                    [`${row.platform}_engagement`]: parseInt(row.engagement) || 0,
                                    [`${row.platform}_impressions`]: parseInt(row.impressions) || 0,
                                    [`${row.platform}_clicks`]: parseInt(row.clicks) || 0,
                                    [`${row.platform}_shares`]: parseInt(row.shares) || 0,
                                    [`${row.platform}_comments`]: parseInt(row.comments) || 0,
                                    [`${row.platform}_saves`]: parseInt(row.saves) || 0
                                }
                            }));

                            this.showToast(`Preview ready: ${this.importPreview.length} posts found`, 'success', 3000);
                        } catch (error) {
                            console.error('Import error:', error);
                            this.showToast('Error parsing file. Please check the format.', 'error', 5000);
                        }
                    };
                    reader.readAsText(file);
                },

                parseCSV(csv) {
                    const lines = csv.split('\n');
                    const headers = lines[0].split(',').map(h => h.trim());
                    const posts = [];

                    for (let i = 1; i < lines.length; i++) {
                        if (!lines[i].trim()) continue;
                        const values = lines[i].split(',');
                        const post = {};
                        headers.forEach((header, index) => {
                            post[header] = values[index]?.trim() || '';
                        });
                        posts.push(post);
                    }
                    return posts;
                },

                async processMetaAPIImport() {
                    if (!this.apiImportClientId || !this.metaAccessToken) {
                        this.showToast('Please select a client and provide an access token', 'warning', 3000);
                        return;
                    }

                    try {
                        this.showToast('Fetching posts from Meta API...', 'info', 5000);
                        
                        // Fetch Instagram posts
                        const instagramPosts = await this.fetchInstagramPosts();
                        // Fetch Facebook posts
                        const facebookPosts = await this.fetchFacebookPosts();

                        this.importPreview = [...instagramPosts, ...facebookPosts];
                        this.showToast(`Found ${this.importPreview.length} posts from Meta API`, 'success', 3000);
                    } catch (error) {
                        console.error('Meta API error:', error);
                        this.showToast('Error fetching from Meta API: ' + error.message, 'error');
                    }
                },

                async fetchInstagramPosts() {
                    // This would call the Meta Graph API for Instagram
                    // For now, returning empty array - needs actual API implementation
                    const url = `https://graph.facebook.com/v18.0/me/media?fields=id,caption,media_type,media_url,timestamp,insights.metric(reach,engagement,impressions)&access_token=${this.metaAccessToken}`;
                    
                    try {
                        const response = await fetch(url);
                        const data = await response.json();
                        
                        if (data.error) {
                            throw new Error(data.error.message);
                        }

                        return (data.data || []).map(post => ({
                            id: 'post_' + post.id,
                            clientId: this.apiImportClientId,
                            scheduledDate: post.timestamp,
                            platforms: ['instagram'],
                            contentType: post.media_type === 'VIDEO' ? 'video' : 'static',
                            caption: post.caption || '',
                            status: 'completed',
                            kpis: {
                                instagram_reach: post.insights?.data?.find(i => i.name === 'reach')?.values[0]?.value || 0,
                                instagram_engagement: post.insights?.data?.find(i => i.name === 'engagement')?.values[0]?.value || 0,
                                instagram_impressions: post.insights?.data?.find(i => i.name === 'impressions')?.values[0]?.value || 0
                            }
                        }));
                    } catch (error) {
                        console.error('Instagram API error:', error);
                        return [];
                    }
                },

                async fetchFacebookPosts() {
                    // Similar to Instagram but for Facebook pages
                    // Needs page ID and proper permissions
                    return [];
                },

                async confirmImport() {
                    if (this.importPreview.length === 0) {
                        this.showToast('No posts to import', 'error');
                        return;
                    }

                    const clientId = this.importPreview[0].clientId;
                    
                    try {
                        // Show progress
                        const progressMsg = `Saving ${this.importPreview.length} posts to database...`;
                        this.showToast(progressMsg, 'info');
                        
                        let newPosts = 0;
                        let updatedPosts = 0;
                        let errors = 0;
                        
                        // Import posts in batches to avoid overwhelming the server
                        const batchSize = 10;
                        for (let i = 0; i < this.importPreview.length; i += batchSize) {
                            const batch = this.importPreview.slice(i, i + batchSize);
                            
                            // Process each post in the batch
                            const batchPromises = batch.map(async (post) => {
                                try {
                                    // Transform imported post to match backend schema
                                    const platform = post.platforms[0];
                                    const kpis = post.kpis || {};
                                    
                                    const transformedPost = {
                                        clientId: post.clientId,
                                        platform: platform,
                                        content: {
                                            text: post.caption || '',
                                            media: post.finalContent ? [post.finalContent] : [],
                                            link: ''
                                        },
                                        status: post.status === 'completed' ? 'published' : 'draft',
                                        scheduledDate: post.scheduledDate,
                                        publishedDate: post.status === 'completed' ? post.scheduledDate : null,
                                        performance: {
                                            reach: kpis[`${platform}_reach`] || 0,
                                            impressions: kpis[`${platform}_impressions`] || 0,
                                            engagement: kpis[`${platform}_engagement`] || 0,
                                            likes: kpis[`${platform}_likes`] || 0,
                                            comments: kpis[`${platform}_comments`] || 0,
                                            shares: kpis[`${platform}_shares`] || 0,
                                            saves: kpis[`${platform}_saves`] || 0,
                                            views: kpis[`${platform}_views`] || 0,
                                            watch_time: kpis[`${platform}_watch_time`] || 0,
                                            skip_rate: kpis[`${platform}_skip_rate`] || 0,
                                            views_followers: kpis[`${platform}_views_followers`] || 0,
                                            views_non_followers: kpis[`${platform}_views_non_followers`] || 0,
                                            interactions: kpis[`${platform}_interactions`] || 0,
                                            lastUpdated: new Date()
                                        }
                                    };
                                    
                                    // Check if post already exists
                                    const checkResponse = await fetch(`${API_URL}/posts?clientId=${clientId}&scheduledDate=${post.scheduledDate}`, {
                                        headers: {
                                            'Authorization': `Bearer ${localStorage.getItem('token')}`
                                        }
                                    });
                                    
                                    const existingPosts = await checkResponse.json();
                                    const existingPost = existingPosts.data?.find(p => 
                                        p.scheduledDate === post.scheduledDate && 
                                        p.content?.text === transformedPost.content.text
                                    );
                                    
                                    if (existingPost) {
                                        // Update existing post
                                        const updateResponse = await fetch(`${API_URL}/posts/${existingPost._id}`, {
                                            method: 'PUT',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                                            },
                                            body: JSON.stringify(transformedPost)
                                        });
                                        
                                        if (updateResponse.ok) {
                                            updatedPosts++;
                                        } else {
                                            const errorData = await updateResponse.json();
                                            console.error('Update error:', errorData);
                                            errors++;
                                        }
                                    } else {
                                        // Create new post
                                        const createResponse = await fetch(`${API_URL}/posts`, {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'Authorization': `Bearer ${localStorage.getItem('token')}`
                                            },
                                            body: JSON.stringify(transformedPost)
                                        });
                                        
                                        if (createResponse.ok) {
                                            newPosts++;
                                        } else {
                                            const errorData = await createResponse.json();
                                            console.error('Create error:', errorData);
                                            errors++;
                                        }
                                    }
                                } catch (error) {
                                    console.error('Error saving post:', error);
                                    errors++;
                                }
                            });
                            
                            // Wait for batch to complete
                            await Promise.all(batchPromises);
                            
                            // Update progress
                        }
                        
                        // Clear import state
                        this.importPreview = [];
                        this.importFile = null;
                        this.importClientId = '';
                        this.apiImportClientId = '';
                        this.metaAccessToken = '';
                        
                        // Show results
                        let resultMessage = `Import complete! ${newPosts} new, ${updatedPosts} updated`;
                        if (errors > 0) {
                            resultMessage += `, ${errors} failed`;
                        }
                        this.showToast(resultMessage, errors > 0 ? 'warning' : 'success', 5000);
                        
                    } catch (error) {
                        this.showToast('Error importing posts: ' + error.message, 'error', 5000);
                    }
                },

                async handleMediaUpload(event) {
                    try {
                        const files = Array.from(event.target.files);
                        if (files.length === 0) return;

                        const client = this.clients.find(c => c._id === this.importClientId);
                        if (!client) {
                            this.showToast('Please select a client first', 'warning', 3000);
                            return;
                        }

                    this.mediaUploadProgress = { uploaded: 0, total: files.length };

                    // Group files by folder structure
                    const filesByFolder = {};
                    files.forEach(file => {
                        const pathParts = file.webkitRelativePath.split('/');
                        // Extract media type and month from path
                        // e.g., "media/reels/202509/video.mp4"
                        if (pathParts.length >= 3 && pathParts[0] === 'media') {
                            const mediaType = pathParts[1]; // reels, stories, profile
                            const monthFolder = pathParts.length > 3 ? pathParts[2] : '';
                            const key = `${mediaType}/${monthFolder}`;
                            
                            if (!filesByFolder[key]) {
                                filesByFolder[key] = [];
                            }
                            filesByFolder[key].push(file);
                        }
                    });

                    // Upload files in batches by folder
                    for (const [folderKey, folderFiles] of Object.entries(filesByFolder)) {
                        const [mediaType, monthFolder] = folderKey.split('/');
                        
                        // Upload in chunks of 10 files
                        for (let i = 0; i < folderFiles.length; i += 10) {
                            const chunk = folderFiles.slice(i, i + 10);
                            const formData = new FormData();
                            
                            chunk.forEach(file => {
                                formData.append('mediaFiles[]', file);
                            });
                            
                            formData.append('clientName', client.companyName);
                            formData.append('mediaType', mediaType);
                            formData.append('monthFolder', monthFolder);

                            try {
                                // Add auth token to FormData
                                const token = localStorage.getItem('token');
                                if (token) {
                                    formData.append('auth_token', token);
                                }
                                
                                const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=upload_media', {
                                    method: 'POST',
                                    body: formData,
                                    credentials: 'same-origin'
                                });

                                const result = await response.json();
                                
                                if (result.success) {
                                    this.mediaUploadProgress.uploaded += chunk.length;
                                } else {
                                    const errorMessage = result.data?.message || result.message || 'Unknown upload error';
                                    console.error('Upload error:', errorMessage);
                                }
                            } catch (error) {
                                console.error('Upload failed:', error);
                            }
                        }
                    }

                    this.showToast(`Successfully uploaded ${this.mediaUploadProgress.uploaded} media files!`, 'success');
                    this.mediaUploadProgress = { uploaded: 0, total: 0 };
                    } catch (error) {
                        console.error('Media upload error:', error);
                        this.showToast(`Media upload failed: ${error.message}`, 'error', 5000);
                    }
                }
            }));
        });
    </script>

            </div>
        </main>
    </div>

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
                     'bg-green-50 border border-green-200': toast.type === 'success',
                     'bg-red-50 border border-red-200': toast.type === 'error',
                     'bg-blue-50 border border-blue-200': toast.type === 'info',
                     'bg-yellow-50 border border-yellow-200': toast.type === 'warning'
                 }">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <!-- Success Icon -->
                            <svg x-show="toast.type === 'success'" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <!-- Error Icon -->
                            <svg x-show="toast.type === 'error'" class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <!-- Info Icon -->
                            <svg x-show="toast.type === 'info'" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <!-- Warning Icon -->
                            <svg x-show="toast.type === 'warning'" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium break-words" 
                               :class="{
                                   'text-green-800': toast.type === 'success',
                                   'text-red-800': toast.type === 'error',
                                   'text-blue-800': toast.type === 'info',
                                   'text-yellow-800': toast.type === 'warning'
                               }"
                               x-text="toast.message"></p>
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
