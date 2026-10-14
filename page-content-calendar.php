<?php
/**
 * Template Name: Content Calendar Page
 * Description: Content Calendar for scheduling and viewing content
 */
if (!defined('ABSPATH')) {
    exit;
}

show_admin_bar(false);
$api_url = get_option('esirom_api_url', 'https://esirom-hub-backend-production.up.railway.app/api');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="h-full">
<head>
    <meta charset="UTF-8">
    <title>Content Calendar - Agency Hub</title>
    <?php esirom_hub_head_assets(array('pwa' => true)); ?>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        <?php esirom_hub_layout_styles(); ?>
    </style>
</head>
<body class="hub-has-mobile-nav h-full bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-white pb-16 md:pb-0" x-data="calendarApp()" x-init="init()">
    <div class="hub-app-shell flex flex-col md:flex-row">
        <!-- Mobile Header -->
        <div class="md:hidden hub-mobile-header">
            <div class="flex items-center justify-between gap-2 w-full">
                <a href="<?php echo esc_url(esirom_hub_page_url('workflow')); ?>" class="hub-sidebar-brand" aria-label="Agency Hub home">
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/agencyhub-icon.png" alt="" class="h-8 w-8">
                    <span class="flex flex-col">
                        <span class="hub-sidebar-brand__name">Content Calendar</span>
                        <span class="hub-sidebar-brand__sub" x-text="monthYearLabel"></span>
                    </span>
                </a>
                <div class="flex items-center gap-1">
                    <template x-if="viewMode !== 'client'">
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="hub-btn hub-btn--soft hub-btn--sm max-w-[8.5rem]" :aria-expanded="open" aria-label="Choose brand">
                                <span class="truncate" x-text="selectedClientName || 'Brand'"></span>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-64 hub-card hub-card--elevated z-50 max-h-80 overflow-y-auto py-1">
                                <template x-for="client in clients" :key="'m-' + client._id">
                                    <button type="button" @click="selectClient(client); open = false" class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700" :class="selectedClient === client._id ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-200 font-semibold' : 'text-gray-700 dark:text-gray-200'" x-text="client.brandName || client.name"></button>
                                </template>
                            </div>
                        </div>
                    </template>
                    <?php $hub_account_theme = true; include get_template_directory() . '/inc/hub-account-menu.php'; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <aside :class="isSidebarOpen ? 'sidebar-expanded' : 'sidebar-collapsed'" class="hub-app-sidebar sidebar hidden md:flex border-r border-gray-200 dark:border-gray-700/50 flex-col flex-shrink-0">
            <?php esirom_hub_staff_sidebar_header(true); ?>
            <nav class="flex-1 px-3 py-4 space-y-3 overflow-y-auto" aria-label="Sections">
                <?php esirom_hub_client_sidebar_nav('content_calendar', 'site', true); ?>
                <?php esirom_hub_staff_sidebar_nav('content_calendar', 'site', true); ?>
            </nav>
            <div class="p-3 border-t border-gray-200 dark:border-gray-700/50">
                <?php esirom_hub_staff_sidebar_footer('site', true); ?>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="hub-app-main mt-14 md:mt-0">
            <!-- Top Bar -->
            <header class="hub-sticky-header hidden md:flex items-center justify-between px-4 lg:px-6 py-3 min-h-16 gap-4 flex-wrap">
                <div class="flex items-center gap-4 flex-wrap min-w-0">
                    <div class="min-w-0">
                        <h1 class="hub-page-title">Content Calendar</h1>
                        <p class="hub-page-subtitle" x-text="monthYearLabel"></p>
                    </div>
                    <!-- Client Selector -->
                    <template x-if="viewMode !== 'client'">
                        <div class="relative" x-data="{ clientDropdownOpen: false }">
                            <button type="button" @click="clientDropdownOpen = !clientDropdownOpen" class="hub-btn hub-btn--soft hub-btn--sm" :aria-expanded="clientDropdownOpen">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span x-text="selectedClientName || 'Select brand'"></span>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="clientDropdownOpen" @click.away="clientDropdownOpen = false" x-cloak class="absolute left-0 mt-2 w-64 hub-card hub-card--elevated z-50 max-h-80 overflow-y-auto py-1">
                                <template x-for="client in clients" :key="client._id">
                                    <button type="button" @click="selectClient(client); clientDropdownOpen = false" class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center justify-between" :class="selectedClient === client._id ? 'bg-brand-50 dark:bg-brand-500/10' : ''">
                                        <span class="font-medium text-gray-900 dark:text-white" x-text="client.brandName || client.name"></span>
                                        <svg x-show="selectedClient === client._id" class="h-4 w-4 text-brand-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Month Navigation -->
                    <div class="flex items-center gap-1">
                        <button type="button" @click="prevMonth()" class="hub-btn hub-btn--ghost hub-btn--icon hub-btn--sm" aria-label="Previous month">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button type="button" @click="nextMonth()" class="hub-btn hub-btn--ghost hub-btn--icon hub-btn--sm" aria-label="Next month">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <button type="button" @click="goToToday()" class="hub-btn hub-btn--secondary hub-btn--sm">Today</button>
                    </div>
                    <?php $hub_account_theme = true; include get_template_directory() . '/inc/hub-account-menu.php'; ?>
                </div>
            </header>

        <!-- Month navigation (mobile) -->
        <div class="md:hidden flex items-center justify-between gap-2 px-4 pt-3">
            <button type="button" @click="prevMonth()" class="hub-btn hub-btn--secondary hub-btn--icon hub-btn--sm" aria-label="Previous month">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button type="button" @click="goToToday()" class="hub-btn hub-btn--secondary hub-btn--sm flex-1 justify-center" x-text="monthYearLabel"></button>
            <button type="button" @click="nextMonth()" class="hub-btn hub-btn--secondary hub-btn--icon hub-btn--sm" aria-label="Next month">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>

        <!-- Calendar Grid -->
        <div class="hub-page-content px-4 sm:px-6 lg:px-8 pt-3 sm:pt-4 pb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <!-- Day Headers -->
                <div class="grid grid-cols-7 bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                    <template x-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day">
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

    <?php include get_template_directory() . '/inc/hub-toasts.php'; ?>
    <?php include get_template_directory() . '/inc/change-password-modal.php'; ?>
    <?php esirom_hub_staff_mobile_nav('content_calendar'); ?>
<script>
    const API_URL = '<?php echo esc_js($api_url); ?>';
    const LOGIN_URL = '<?php echo esc_url(esirom_hub_page_url('login')); ?>';

    function calendarApp() {
        return {
            user: {},
            userRole: '',
            viewMode: window.HubAuth.resolveViewMode(window.HubAuth.readStoredViewMode(), (() => { try { return JSON.parse(localStorage.getItem('user') || '{}') || {}; } catch (e) { return {}; } })()), // provisional until /auth/me answers
            userName: '',
            showPwModal: false,
            pwCurrent: '', pwNew: '', pwConfirm: '', pwLoading: false, pwError: '', pwSuccess: '',
            clients: [],
            events: [],
            eventsLoading: false,
            selectedViewClient: localStorage.getItem('selectedViewClient') || '',
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
                    if (res.status === 401 || res.status === 403) { localStorage.removeItem('token'); localStorage.removeItem('user'); window.location.href = LOGIN_URL; return; }
                    if (!res.ok) throw new Error('server');
                    const data = await res.json();
                    this.user = data.user;
                    window.HubAuth.setUser(data.user);
                    localStorage.setItem('user', JSON.stringify(data.user));
                    this.userRole = data.user.role;
                    this.userName = data.user.firstName + ' ' + data.user.lastName;
                    // Derive the view mode from the verified role: only super admins keep a stored preview mode.
                    this.viewMode = window.HubAuth.applyViewMode();
                    await this.loadClients();
                    const savedClientId = localStorage.getItem('selectedClientId') || localStorage.getItem('hubSelectedClientId');
                    if (this.clients.length > 0 && this.viewMode !== 'client') {
                        const saved = savedClientId && this.clients.find(c => c._id === savedClientId);
                        this.selectClient(saved || this.clients[0]);
                    } else {
                        await this.loadEvents();
                    }
                } catch (e) {
                    this.showToast('We could not reach the server. Please check your connection and refresh.', 'error');
                }
            },

            selectClient(client) {
                this.selectedClient = client._id;
                this.selectedClientName = client.brandName || client.name;
                localStorage.setItem('selectedClientId', client._id);
                localStorage.setItem('hubSelectedClientId', client._id);
                this.loadEvents();
            },

            async loadClients() {
                const token = localStorage.getItem('token');
                try {
                    const res = await fetch(`${API_URL}/calendar/clients`, { headers: { 'Authorization': `Bearer ${token}` } });
                    if (res.ok) { const d = await res.json(); this.clients = d.data || []; }
                    else this.showToast('Brands could not be loaded.', 'error');
                } catch (e) { this.showToast('Brands could not be loaded. Check your connection.', 'error'); }
            },

            async loadEvents() {
                const token = localStorage.getItem('token');
                try {
                    let url = `${API_URL}/calendar/events?year=${this.currentYear}&month=${this.currentMonth}`;
                    if (this.selectedClient) url += `&clientId=${encodeURIComponent(this.selectedClient)}`;
                    else if (this.viewMode === 'client' && this.selectedViewClient) url += `&clientId=${encodeURIComponent(this.selectedViewClient)}&viewAs=client&viewAsClientId=${encodeURIComponent(this.selectedViewClient)}`;
                    this.eventsLoading = true;
                    const res = await fetch(url, { headers: { 'Authorization': `Bearer ${token}` } });
                    if (res.ok) { const d = await res.json(); this.events = d.data || []; }
                    else this.showToast('Calendar events could not be loaded.', 'error');
                } catch (e) { this.showToast('Calendar events could not be loaded. Check your connection.', 'error'); }
                finally { this.eventsLoading = false; }
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
                            week.push({ date: dayCounter, inMonth: true, fullDate: this.toLocalDateKey(fullDate), isToday });
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
                    const raw = String(e.date || '');
                    // Date-only strings are calendar dates; timestamps are converted to the local day.
                    const eventDate = /^\d{4}-\d{2}-\d{2}$/.test(raw) ? raw : this.toLocalDateKey(new Date(raw));
                    return eventDate === dateStr;
                });
            },

            toLocalDateKey(date) {
                const d = date instanceof Date ? date : new Date(date);
                if (Number.isNaN(d.getTime())) return '';
                const pad = (n) => String(n).padStart(2, '0');
                return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
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
                    window.location.href = '<?php echo esc_url(esirom_hub_page_url('workflow')); ?>?concept=' + event.id;
                }
            },

            formatDate(dateStr) {
                if (!dateStr) return '';
                const d = new Date(dateStr + 'T00:00:00');
                return d.toLocaleDateString('en-GB', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
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
                localStorage.removeItem('user');
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
</body>
</html>
