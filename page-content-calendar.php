<?php
/**
 * Template Name: Content Calendar Page
 * Description: Content Calendar for scheduling and viewing content
 */
if (!defined('ABSPATH')) {
    exit;
}

show_admin_bar(false);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Calendar - Agency Hub</title>
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

        #wpadminbar { display: none !important; }
        html { margin-top: 0 !important; }
        body { margin-top: 0 !important; }
    </style>
</head>
<body class="h-full bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-white pb-16 md:pb-0">
    <div x-data="calendarApp()" x-init="init()" class="hub-app-shell flex flex-col md:flex-row">
        <!-- Mobile Header -->
        <div class="md:hidden fixed top-0 left-0 right-0 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 z-40 px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png" alt="Agency Hub" class="h-8 w-8">
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-gray-800 dark:text-white leading-tight">Content Calendar</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Agency Hub</span>
                    </div>
                </div>
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

        <!-- Sidebar -->
        <aside :class="isSidebarOpen ? 'sidebar-expanded' : 'sidebar-collapsed'" class="hub-app-sidebar sidebar hidden md:flex bg-white dark:bg-gray-900/70 dark:backdrop-blur-sm border-r border-gray-200 dark:border-gray-700/50 flex-col">
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
                <?php esirom_hub_client_sidebar_nav('content_calendar', 'site', true); ?>
                <?php esirom_hub_staff_sidebar_nav('content_calendar', 'site', true); ?>
            </nav>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700/50">
                <?php esirom_hub_staff_sidebar_footer('site', true); ?>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="hub-app-main bg-gray-50 dark:bg-gray-900 mt-14 md:mt-0">
            <!-- Top Bar -->
            <header class="hidden md:flex items-center justify-between p-4 h-16 bg-white/80 dark:bg-gray-900/70 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700/50 sticky top-0 z-10 shadow-sm gap-4 flex-wrap">
                <div class="flex items-center gap-4 flex-wrap min-w-0">
                    <div class="min-w-0">
                        <h1 class="text-lg font-bold text-gray-900 dark:text-white">Content Calendar</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="monthYearLabel"></p>
                    </div>
                    <!-- Client Selector -->
                    <template x-if="viewMode !== 'client'">
                        <div class="relative" x-data="{ clientDropdownOpen: false }">
                            <button @click="clientDropdownOpen = !clientDropdownOpen" class="flex items-center gap-2 px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors border border-indigo-100 dark:border-indigo-800/50">
                                <svg class="h-4 w-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span class="text-xs font-semibold text-indigo-700 dark:text-indigo-300" x-text="selectedClientName || 'Select Brand'"></span>
                                <svg class="h-4 w-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="clientDropdownOpen" @click.away="clientDropdownOpen = false" x-cloak class="absolute left-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-lg border dark:border-gray-700 z-50 max-h-80 overflow-y-auto">
                                <template x-for="client in clients" :key="client._id">
                                    <button @click="selectClient(client); clientDropdownOpen = false" class="w-full text-left px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center justify-between" :class="{'bg-indigo-50 dark:bg-indigo-900/30': selectedClient === client._id}">
                                        <span class="font-medium text-gray-900 dark:text-white" x-text="client.brandName || client.name"></span>
                                        <svg x-show="selectedClient === client._id" class="h-5 w-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Month Navigation -->
                    <div class="flex items-center gap-1">
                        <button @click="prevMonth()" class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button @click="nextMonth()" class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <button @click="goToToday()" class="px-3 py-1.5 text-xs font-semibold bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">Today</button>
                    </div>
                    <!-- Theme Toggle -->
                    <button @click="toggleTheme()" class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg x-show="theme === 'light'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <svg x-show="theme === 'dark'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>
                    <!-- User Dropdown -->
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <div class="w-7 h-7 bg-indigo-500 rounded-full flex items-center justify-center text-white font-semibold text-xs" x-text="userName.charAt(0)"></div>
                            <span class="hidden sm:inline text-sm font-medium text-gray-700 dark:text-gray-300" x-text="userName"></span>
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-cloak
                             class="absolute right-0 mt-2 w-52 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 py-1 z-50">
                            <div class="px-4 py-2.5 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="userName"></p>
                                <p class="text-xs text-gray-500 mt-0.5" x-text="user?.email || ''"></p>
                                <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-0.5 capitalize font-medium" x-text="userRole?.replace('_', ' ')"></p>
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

        <!-- Calendar Grid -->
        <div class="hub-page-content px-4 sm:px-6 lg:px-8 pt-3 sm:pt-4 pb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <!-- Day Headers -->
                <div class="grid grid-cols-7 bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                    <template x-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']">
                        <div class="py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300" x-text="day"></div>
                    </template>
                </div>
                <!-- Calendar Days -->
                <div class="grid grid-cols-7">
                    <template x-for="(week, weekIdx) in calendarWeeks" :key="weekIdx">
                        <template x-for="(day, dayIdx) in week" :key="dayIdx">
                            <div 
                                @click="day.inMonth && openDayModal(day)"
                                :class="{
                                    'bg-gray-50 dark:bg-gray-900/50': !day.inMonth,
                                    'bg-white dark:bg-gray-800': day.inMonth,
                                    'ring-2 ring-indigo-500': day.isToday,
                                    'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700': day.inMonth
                                }"
                                class="min-h-[120px] border-b border-r dark:border-gray-700 p-2 relative">
                                <span 
                                    :class="{
                                        'text-gray-400 dark:text-gray-600': !day.inMonth,
                                        'text-gray-900 dark:text-white': day.inMonth && !day.isToday,
                                        'bg-indigo-600 text-white rounded-full w-7 h-7 flex items-center justify-center': day.isToday
                                    }"
                                    class="text-sm font-medium"
                                    x-text="day.date"></span>
                                <!-- Events for this day -->
                                <div class="mt-1 space-y-1 max-h-[80px] overflow-y-auto">
                                    <template x-for="event in getEventsForDay(day.fullDate)" :key="event.id">
                                        <div 
                                            @click.stop="openEventDetail(event)"
                                            :class="{
                                                'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border-l-2 border-green-500': event.color === 'green',
                                                'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border-l-2 border-blue-500': event.color === 'blue',
                                                'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300 border-l-2 border-orange-500': event.color === 'orange',
                                                'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 border-l-2 border-red-500': event.color === 'red'
                                            }"
                                            class="px-2 py-1 text-xs rounded cursor-pointer hover:opacity-80 truncate"
                                            x-text="event.title"></div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </template>
                </div>
            </div>
            <!-- Legend -->
            <div class="mt-4 flex flex-wrap gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-green-500 rounded"></div>
                    <span class="text-gray-600 dark:text-gray-400">Posted</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-blue-500 rounded"></div>
                    <span class="text-gray-600 dark:text-gray-400">Scheduled</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-orange-500 rounded"></div>
                    <span class="text-gray-600 dark:text-gray-400">High Priority</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-red-500 rounded"></div>
                    <span class="text-gray-600 dark:text-gray-400">Urgent</span>
                </div>
            </div>
        </div>

        <!-- Day Modal (click on day to add concept) -->
        <div x-show="showDayModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="showDayModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4" x-text="'Schedule for ' + formatDate(selectedDay)"></h3>
                    <div class="space-y-3 mb-6 max-h-60 overflow-y-auto">
                        <template x-for="event in getEventsForDay(selectedDay)" :key="event.id">
                            <div 
                                :class="{
                                    'border-l-4 border-green-500': event.color === 'green',
                                    'border-l-4 border-blue-500': event.color === 'blue',
                                    'border-l-4 border-orange-500': event.color === 'orange',
                                    'border-l-4 border-red-500': event.color === 'red'
                                }"
                                class="p-3 bg-gray-50 dark:bg-gray-700 rounded-r-lg">
                                <div class="font-medium text-gray-900 dark:text-white" x-text="event.title"></div>
                                <div class="text-sm text-gray-500 dark:text-gray-400" x-text="event.clientName + ' • ' + event.status"></div>
                            </div>
                        </template>
                        <div x-show="getEventsForDay(selectedDay).length === 0" class="text-gray-500 dark:text-gray-400 text-center py-4">No content scheduled for this day</div>
                    </div>
                    <template x-if="viewMode !== 'client'">
                        <button @click="openAddConceptModal()" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add New Concept
                        </button>
                    </template>
                    <button @click="showDayModal = false" class="mt-3 w-full px-4 py-2 border dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">Close</button>
                </div>
            </div>
        </div>

        <!-- Add Concept Modal -->
        <div x-show="showAddConceptModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="showAddConceptModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Add New Concept</h3>
                    <form @submit.prevent="createConcept()">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title *</label>
                                <input type="text" x-model="newConcept.title" required class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Brand *</label>
                                <select x-model="newConcept.clientId" required class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Brand</option>
                                    <template x-for="client in clients" :key="client._id">
                                        <option :value="client._id" x-text="client.brandName || client.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description *</label>
                                <textarea x-model="newConcept.description" required rows="3" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content Type *</label>
                                    <select x-model="newConcept.contentType" required class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">Select Type</option>
                                        <option value="graphic">Graphic</option>
                                        <option value="motion_graphic">Motion Graphic</option>
                                        <option value="video">Video</option>
                                        <option value="carousel">Carousel</option>
                                        <option value="story">Story</option>
                                        <option value="reel">Reel</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
                                    <select x-model="newConcept.priority" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Scheduled Post Date</label>
                                <input type="date" x-model="newConcept.scheduledPostDate" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="showAddConceptModal = false" class="px-4 py-2 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Create Concept</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>
<script>
    const API_URL = typeof ESIROM_API_URL !== 'undefined' ? ESIROM_API_URL : 'https://esirom-hub-backend-production.up.railway.app/api';
    const LOGIN_URL = '<?php echo esc_url(get_permalink(get_page_by_path('login'))); ?>';

    function calendarApp() {
        return {
            user: {},
            userRole: '',
            viewMode: localStorage.getItem('viewMode') || 'admin',
            userName: '',
            showPwModal: false,
            pwCurrent: '', pwNew: '', pwConfirm: '', pwLoading: false, pwError: '', pwSuccess: '',
            clients: [],
            events: [],
            selectedClient: '',
            selectedClientName: '',
            currentYear: new Date().getFullYear(),
            currentMonth: new Date().getMonth() + 1,
            showDayModal: false,
            showAddConceptModal: false,
            selectedDay: null,
            isSidebarOpen: true,
            theme: localStorage.getItem('theme') || 'light',
            toasts: [],
            newConcept: {
                title: '',
                clientId: '',
                description: '',
                contentType: '',
                priority: 'medium',
                scheduledPostDate: ''
            },

            showToast(message, type = 'info', duration = 3000) {
                const id = Date.now();
                this.toasts.push({ id, message, type });
                setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, duration);
            },

            async init() {
                const token = localStorage.getItem('token');
                if (!token) { window.location.href = LOGIN_URL; return; }
                try {
                    const res = await fetch(`${API_URL}/auth/me`, { headers: { 'Authorization': `Bearer ${token}` } });
                    if (!res.ok) { localStorage.removeItem('token'); window.location.href = LOGIN_URL; return; }
                    const data = await res.json();
                    this.user = data.user;
                    this.userRole = data.user.role;
                    this.userName = data.user.firstName + ' ' + data.user.lastName;
                    // Ensure true client accounts always use client viewMode
                    if (this.userRole === 'client') {
                        this.viewMode = 'client';
                        localStorage.setItem('viewMode', 'client');
                    } else if (this.userRole === 'brand_rep' && this.viewMode === 'admin') {
                        this.viewMode = 'brand_rep';
                        localStorage.setItem('viewMode', 'brand_rep');
                    } else if (this.userRole === 'admin' && !['admin', 'brand_rep', 'client'].includes(this.viewMode)) {
                        this.viewMode = 'admin';
                        localStorage.setItem('viewMode', 'admin');
                    }
                    await this.loadClients();
                    // Auto-select first client for admins/brand_reps
                    if (this.clients.length > 0 && this.viewMode !== 'client') {
                        this.selectClient(this.clients[0]);
                    } else {
                        await this.loadEvents();
                    }
                } catch (e) { console.error(e); window.location.href = LOGIN_URL; }
            },

            selectClient(client) {
                this.selectedClient = client._id;
                this.selectedClientName = client.brandName || client.name;
                this.loadEvents();
            },

            async loadClients() {
                const token = localStorage.getItem('token');
                try {
                    const res = await fetch(`${API_URL}/calendar/clients`, { headers: { 'Authorization': `Bearer ${token}` } });
                    if (res.ok) { const d = await res.json(); this.clients = d.data || []; }
                } catch (e) { console.error('Load clients error:', e); }
            },

            async loadEvents() {
                const token = localStorage.getItem('token');
                try {
                    let url = `${API_URL}/calendar/events?year=${this.currentYear}&month=${this.currentMonth}`;
                    if (this.selectedClient) url += `&clientId=${this.selectedClient}`;
                    const res = await fetch(url, { headers: { 'Authorization': `Bearer ${token}` } });
                    if (res.ok) { const d = await res.json(); this.events = d.data || []; }
                } catch (e) { console.error('Load events error:', e); }
            },

            get monthYearLabel() {
                const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                return months[this.currentMonth - 1] + ' ' + this.currentYear;
            },

            get calendarWeeks() {
                const weeks = [];
                const firstDay = new Date(this.currentYear, this.currentMonth - 1, 1);
                const lastDay = new Date(this.currentYear, this.currentMonth, 0);
                const startDayOfWeek = firstDay.getDay();
                const daysInMonth = lastDay.getDate();
                const prevMonthLastDay = new Date(this.currentYear, this.currentMonth - 1, 0).getDate();
                const today = new Date();
                let dayCounter = 1;
                let nextMonthDay = 1;

                for (let w = 0; w < 6; w++) {
                    const week = [];
                    for (let d = 0; d < 7; d++) {
                        if (w === 0 && d < startDayOfWeek) {
                            const date = prevMonthLastDay - startDayOfWeek + d + 1;
                            week.push({ date, inMonth: false, fullDate: null, isToday: false });
                        } else if (dayCounter > daysInMonth) {
                            week.push({ date: nextMonthDay++, inMonth: false, fullDate: null, isToday: false });
                        } else {
                            const fullDate = new Date(this.currentYear, this.currentMonth - 1, dayCounter);
                            const isToday = fullDate.toDateString() === today.toDateString();
                            week.push({ date: dayCounter, inMonth: true, fullDate: fullDate.toISOString().split('T')[0], isToday });
                            dayCounter++;
                        }
                    }
                    weeks.push(week);
                    if (dayCounter > daysInMonth && w >= 3) break;
                }
                return weeks;
            },

            getEventsForDay(dateStr) {
                if (!dateStr) return [];
                return this.events.filter(e => {
                    const eventDate = new Date(e.date).toISOString().split('T')[0];
                    return eventDate === dateStr;
                });
            },

            prevMonth() {
                if (this.currentMonth === 1) { this.currentMonth = 12; this.currentYear--; }
                else { this.currentMonth--; }
                this.loadEvents();
            },

            nextMonth() {
                if (this.currentMonth === 12) { this.currentMonth = 1; this.currentYear++; }
                else { this.currentMonth++; }
                this.loadEvents();
            },

            goToToday() {
                const today = new Date();
                this.currentYear = today.getFullYear();
                this.currentMonth = today.getMonth() + 1;
                this.loadEvents();
            },

            openDayModal(day) {
                this.selectedDay = day.fullDate;
                this.showDayModal = true;
            },

            openAddConceptModal() {
                this.newConcept = {
                    title: '',
                    clientId: this.selectedClient || '',
                    description: '',
                    contentType: '',
                    priority: 'medium',
                    scheduledPostDate: this.selectedDay || ''
                };
                this.showDayModal = false;
                this.showAddConceptModal = true;
            },

            openEventDetail(event) {
                if (event.type === 'concept') {
                    window.location.href = '<?php echo esc_url(get_permalink(get_page_by_path('workflow'))); ?>?conceptId=' + event.id;
                }
            },

            formatDate(dateStr) {
                if (!dateStr) return '';
                const d = new Date(dateStr + 'T00:00:00');
                return d.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
            },

            async createConcept() {
                const token = localStorage.getItem('token');
                try {
                    const payload = {
                        title: this.newConcept.title,
                        clientId: this.newConcept.clientId,
                        description: this.newConcept.description,
                        contentType: this.newConcept.contentType,
                        priority: this.newConcept.priority,
                        platform: ['instagram'],
                        dueDate: this.newConcept.scheduledPostDate || new Date().toISOString(),
                        scheduledPostDate: this.newConcept.scheduledPostDate || null
                    };
                    const res = await fetch(`${API_URL}/workflow/concepts`, {
                        method: 'POST',
                        headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    if (res.ok) {
                        this.showAddConceptModal = false;
                        await this.loadEvents();
                        this.showToast('Concept created successfully!', 'success');
                    } else {
                        const err = await res.json();
                        this.showToast('Error: ' + (err.message || 'Failed to create concept'), 'error');
                    }
                } catch (e) { console.error('Create concept error:', e); this.showToast('Failed to create concept', 'error'); }
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
                window.location.href = LOGIN_URL;
            },

            toggleTheme() {
                this.theme = this.theme === 'light' ? 'dark' : 'light';
                localStorage.setItem('theme', this.theme);
                document.documentElement.classList.toggle('dark');
            }
        };
    }
</script>

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

    <?php include get_template_directory() . '/inc/change-password-modal.php'; ?>
    <?php esirom_hub_staff_mobile_nav('content_calendar'); ?>
</div>
</body>
</html>
