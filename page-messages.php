<?php
/**
 * Template Name: Messages
 * Description: Facebook Messenger and Instagram DMs — staff only
 */
if (!defined('ABSPATH')) exit;

show_admin_bar(false);

$api_url = get_option('esirom_api_url', 'https://esirom-hub-backend-production.up.railway.app/api');
$login_url = esc_url(get_permalink(get_page_by_path('login')));
$dashboard_url = esc_url(get_permalink(get_page_by_path('dashboard')));
$admin_url = esc_url(get_permalink(get_page_by_path('admin')));
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages — Agency Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        const API_URL = '<?php echo esc_js($api_url); ?>';
        const LOGIN_URL = '<?php echo esc_js($login_url); ?>';
        const DASHBOARD_URL = '<?php echo esc_js($dashboard_url); ?>';
        const ADMIN_URL = '<?php echo esc_js($admin_url); ?>';
        tailwind.config = { darkMode: 'class', theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } } };
    </script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        <?php esirom_hub_layout_styles(); ?>
    </style>
</head>
<body class="hub-has-mobile-nav h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 pb-16 md:pb-0" x-data="messagesApp()" x-init="init()">

<div x-show="!authChecked" class="fixed inset-0 flex items-center justify-center bg-white dark:bg-gray-900 z-50">
    <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
</div>

<div x-show="authChecked && !user" x-cloak class="fixed inset-0 flex items-center justify-center">
    <div class="text-center">
        <p class="text-gray-500 mb-4">You must be logged in.</p>
        <a href="<?php echo $login_url; ?>" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Go to Login</a>
    </div>
</div>

<div x-show="authChecked && user" x-cloak class="hub-app-shell flex flex-col md:flex-row">
    <aside :class="isSidebarOpen ? 'sidebar-expanded' : 'sidebar-collapsed'" class="hub-app-sidebar sidebar hidden md:flex bg-white dark:bg-gray-900/70 dark:backdrop-blur-sm border-r border-gray-200 dark:border-gray-700/50 flex-col flex-shrink-0">
        <?php esirom_hub_staff_sidebar_header(false); ?>
        <nav class="flex-1 px-2 py-4 space-y-2 overflow-y-auto">
            <?php esirom_hub_staff_sidebar_nav('messages', 'site', false); ?>
        </nav>
        <div class="p-2 border-t border-gray-200 dark:border-gray-700/50">
            <?php esirom_hub_staff_sidebar_footer('site', false); ?>
        </div>
    </aside>

    <main class="hub-app-main">
        <header class="bg-white/80 dark:bg-gray-900/70 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700/50 px-4 sm:px-6 py-4 sticky top-0 z-10 shadow-sm">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="min-w-0">
                    <h1 class="text-lg font-bold text-gray-900 dark:text-white">Messages</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Facebook Messenger &amp; Instagram DMs</p>
                </div>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl border border-indigo-100 dark:border-indigo-800/50 text-xs font-semibold text-indigo-700 dark:text-indigo-300">
                        <span x-text="selectedClientName || 'Select Brand'"></span>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-lg border dark:border-gray-700 z-50 max-h-80 overflow-y-auto">
                        <template x-for="client in clients" :key="client._id">
                            <button @click="selectClient(client); open = false" class="w-full text-left px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700" :class="selectedClient === client._id ? 'bg-indigo-50 dark:bg-indigo-900/30' : ''">
                                <span class="font-medium" x-text="client.brandName || client.name"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
            <div class="flex gap-2 mt-3 flex-wrap">
                <button @click="platformFilter = 'all'; filterThreads()" class="px-3 py-1.5 text-xs font-semibold rounded-xl" :class="platformFilter === 'all' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'">All</button>
                <button @click="platformFilter = 'facebook'; filterThreads()" class="px-3 py-1.5 text-xs font-semibold rounded-xl" :class="platformFilter === 'facebook' ? 'bg-blue-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'">Messenger</button>
                <button @click="platformFilter = 'instagram'; filterThreads()" class="px-3 py-1.5 text-xs font-semibold rounded-xl" :class="platformFilter === 'instagram' ? 'bg-pink-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'">Instagram</button>
            </div>
        </header>

        <div class="hub-page-content p-4 sm:p-6 space-y-4">
            <div x-show="!selectedClient" class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-8 text-center text-sm text-gray-500">
                Select a brand to view inbox threads.
            </div>

            <template x-if="selectedClient && platforms">
                <div class="grid sm:grid-cols-2 gap-3">
                    <div class="rounded-xl border p-4" :class="platforms.facebook?.connected ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700'">
                        <p class="text-xs font-bold uppercase tracking-wide text-blue-700 dark:text-blue-300">Messenger</p>
                        <p class="text-sm mt-1" x-text="platforms.facebook?.connected ? ('Connected · ' + (platforms.facebook.username || platforms.facebook.pageId)) : 'Not connected'"></p>
                    </div>
                    <div class="rounded-xl border p-4" :class="platforms.instagram?.connected ? 'bg-pink-50 dark:bg-pink-900/20 border-pink-200 dark:border-pink-800' : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700'">
                        <p class="text-xs font-bold uppercase tracking-wide text-pink-700 dark:text-pink-300">Instagram DMs</p>
                        <p class="text-sm mt-1" x-text="platforms.instagram?.connected ? ('Connected · @' + (platforms.instagram.username || platforms.instagram.accountId)) : 'Not connected'"></p>
                    </div>
                </div>
            </template>

            <div x-show="errors && (errors.facebook || errors.instagram)" class="rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-4 text-sm text-amber-800 dark:text-amber-200">
                <p class="font-semibold mb-1">Some inboxes could not be loaded</p>
                <p x-show="errors?.facebook" class="text-xs mt-1"><strong>Messenger:</strong> <span x-text="errors.facebook"></span></p>
                <p x-show="errors?.instagram" class="text-xs mt-1"><strong>Instagram:</strong> <span x-text="errors.instagram"></span></p>
                <p class="text-xs mt-2 text-amber-700 dark:text-amber-300">Ensure the Meta app has Messenger and Instagram messaging permissions, then reconnect the brand in Admin.</p>
            </div>

            <div x-show="loading" class="flex justify-center py-16">
                <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
            </div>

            <div x-show="!loading && selectedClient && filteredThreads.length === 0" x-cloak class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-10 text-center">
                <p class="text-gray-600 dark:text-gray-400 text-sm">No conversations yet for this brand.</p>
                <p class="text-xs text-gray-500 mt-2">Threads appear here once Messenger or Instagram messaging is connected and active.</p>
            </div>

            <div x-show="!loading && filteredThreads.length > 0" x-cloak class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 overflow-hidden divide-y dark:divide-gray-700">
                <template x-for="thread in filteredThreads" :key="thread.id">
                    <div class="px-5 py-4 flex items-start gap-4 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                             :class="thread.platform === 'instagram' ? 'bg-gradient-to-br from-purple-500 to-pink-500' : 'bg-blue-600'"
                             x-text="(thread.name || '?').charAt(0).toUpperCase()"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <p class="font-semibold truncate" x-text="thread.name"></p>
                                <span class="text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full flex-shrink-0"
                                      :class="thread.platform === 'instagram' ? 'bg-pink-100 text-pink-700 dark:bg-pink-900/40 dark:text-pink-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'"
                                      x-text="thread.platform === 'instagram' ? 'IG' : 'FB'"></span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 truncate mt-0.5" x-text="thread.snippet || 'No preview'"></p>
                            <p class="text-xs text-gray-400 mt-1" x-text="fmtTime(thread.updatedTime) + (thread.messageCount ? ' · ' + thread.messageCount + ' messages' : '')"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </main>
</div>

<script>
function messagesApp() {
    return {
        authChecked: false,
        user: null,
        isSidebarOpen: true,
        loading: false,
        clients: [],
        selectedClient: null,
        selectedClientName: '',
        threads: [],
        filteredThreads: [],
        platforms: null,
        errors: null,
        platformFilter: 'all',

        async init() {
            const token = localStorage.getItem('token');
            if (!token) { this.authChecked = true; return; }
            try {
                const res = await fetch(`${API_URL}/auth/me`, { headers: { Authorization: `Bearer ${token}` } });
                if (!res.ok) throw new Error('auth');
                const data = await res.json();
                this.user = data.user;
                if (this.user.role === 'client') {
                    window.location.href = DASHBOARD_URL;
                    return;
                }
                await this.loadClients();
                const saved = localStorage.getItem('hubSelectedClientId');
                if (saved && this.clients.some(c => c._id === saved)) {
                    const client = this.clients.find(c => c._id === saved);
                    await this.selectClient(client);
                } else if (this.clients.length === 1) {
                    await this.selectClient(this.clients[0]);
                }
            } catch (e) {
                this.user = null;
            } finally {
                this.authChecked = true;
            }
        },

        headers() {
            return { Authorization: `Bearer ${localStorage.getItem('token')}`, 'Content-Type': 'application/json' };
        },

        async loadClients() {
            const res = await fetch(`${API_URL}/calendar/clients`, { headers: this.headers() });
            const data = await res.json();
            if (data.success) this.clients = data.data || [];
        },

        async selectClient(client) {
            this.selectedClient = client._id;
            this.selectedClientName = client.brandName || client.name;
            localStorage.setItem('hubSelectedClientId', client._id);
            await this.loadMessages();
        },

        async loadMessages() {
            if (!this.selectedClient) return;
            this.loading = true;
            this.errors = null;
            try {
                const res = await fetch(`${API_URL}/social-media/messages/${this.selectedClient}`, { headers: this.headers() });
                const data = await res.json();
                if (data.success) {
                    this.platforms = data.data.platforms;
                    this.threads = data.data.threads || [];
                    this.errors = data.data.errors;
                    this.filterThreads();
                }
            } finally {
                this.loading = false;
            }
        },

        filterThreads() {
            this.filteredThreads = this.platformFilter === 'all'
                ? this.threads
                : this.threads.filter(t => t.platform === this.platformFilter);
        },

        fmtTime(value) {
            if (!value) return '';
            return new Date(value).toLocaleString('en-GB', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
        }
    };
}
</script>

<?php esirom_hub_staff_mobile_nav('messages'); ?>
</body>
</html>
