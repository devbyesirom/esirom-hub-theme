<?php
/**
 * Template Name: Ads
 * Description: Boost posts and manage ad campaigns — staff only
 */
if (!defined('ABSPATH')) exit;

show_admin_bar(false);

$api_url = get_option('esirom_api_url', 'https://esirom-hub-backend-production.up.railway.app/api');
$login_url = esc_url(get_permalink(get_page_by_path('login')));
$dashboard_url = esc_url(get_permalink(get_page_by_path('dashboard')));
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ads — Agency Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        const API_URL = '<?php echo esc_js($api_url); ?>';
        const LOGIN_URL = '<?php echo esc_js($login_url); ?>';
        const DASHBOARD_URL = '<?php echo esc_js($dashboard_url); ?>';
        tailwind.config = { darkMode: 'class', theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } } };
    </script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        <?php esirom_hub_layout_styles(); ?>
    </style>
</head>
<body class="hub-has-mobile-nav h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 pb-16 md:pb-0" x-data="adsApp()" x-init="init()">

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
            <?php esirom_hub_staff_sidebar_nav('ads', 'site', false); ?>
        </nav>
        <div class="p-2 border-t border-gray-200 dark:border-gray-700/50">
            <?php esirom_hub_staff_sidebar_footer('site', false); ?>
        </div>
    </aside>

    <main class="hub-app-main">
        <header class="bg-white/80 dark:bg-gray-900/70 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700/50 px-4 sm:px-6 py-4 sticky top-0 z-10 shadow-sm">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="min-w-0">
                    <h1 class="text-lg font-bold text-gray-900 dark:text-white">Ads</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Boost published posts via Meta Marketing API</p>
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
            <div class="flex gap-2 mt-3">
                <button @click="tab = 'boost'" class="px-3 py-1.5 text-xs font-semibold rounded-xl" :class="tab === 'boost' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'">Boost Posts</button>
                <button @click="tab = 'campaigns'" class="px-3 py-1.5 text-xs font-semibold rounded-xl" :class="tab === 'campaigns' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'">Campaigns</button>
            </div>
        </header>

        <div class="hub-page-content p-4 sm:p-6 space-y-4">
            <div x-show="!selectedClient" class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-8 text-center text-sm text-gray-500">
                Select a brand to manage ads.
            </div>

            <div x-show="selectedClient && tab === 'boost'" x-cloak class="space-y-4">
                <div class="rounded-xl border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-900/20 p-4 text-sm text-indigo-900 dark:text-indigo-100">
                    Choose a synced published post to boost. Full campaign creation via Marketing API is coming next — for now this lists eligible posts with platform IDs.
                </div>

                <div x-show="loading" class="flex justify-center py-16">
                    <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                </div>

                <div x-show="!loading && boostablePosts.length === 0" class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-10 text-center text-sm text-gray-500">
                    No boostable posts found. Sync published Facebook or Instagram posts from Insights first.
                </div>

                <div x-show="!loading && boostablePosts.length > 0" class="grid gap-4">
                    <template x-for="post in boostablePosts" :key="post._id">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-4 flex flex-col sm:flex-row gap-4">
                            <div class="w-full sm:w-24 h-24 rounded-lg bg-gray-100 dark:bg-gray-700 overflow-hidden flex-shrink-0">
                                <img x-show="post.mediaUrl" :src="post.mediaUrl" alt="" class="w-full h-full object-cover">
                                <div x-show="!post.mediaUrl" class="w-full h-full flex items-center justify-center text-xs text-gray-400">No image</div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full"
                                          :class="post.platform === 'instagram' ? 'bg-pink-100 text-pink-700' : 'bg-blue-100 text-blue-700'"
                                          x-text="post.platform"></span>
                                    <span x-show="post.isPaid" class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Boosted</span>
                                </div>
                                <p class="text-sm mt-2 line-clamp-2" x-text="post.text || '(No caption)'"></p>
                                <p class="text-xs text-gray-500 mt-1" x-text="fmtDate(post.publishedDate) + ' · Reach ' + (post.reach || 0) + ' · Engagement ' + (post.engagement || 0)"></p>
                            </div>
                            <div class="flex sm:flex-col items-end justify-center gap-2 flex-shrink-0">
                                <button disabled class="px-3 py-1.5 text-xs font-semibold rounded-xl bg-gray-200 dark:bg-gray-700 text-gray-500 cursor-not-allowed" title="Coming soon">Boost Post</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div x-show="selectedClient && tab === 'campaigns'" x-cloak class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-10 text-center">
                <p class="text-gray-600 dark:text-gray-400 text-sm">No active campaigns yet.</p>
                <p class="text-xs text-gray-500 mt-2">Campaign management via Meta Marketing API will appear here once ad account linking is configured.</p>
            </div>
        </div>
    </main>
</div>

<script>
function adsApp() {
    return {
        authChecked: false,
        user: null,
        isSidebarOpen: true,
        loading: false,
        tab: 'boost',
        clients: [],
        selectedClient: null,
        selectedClientName: '',
        boostablePosts: [],
        campaigns: [],

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
                    await this.selectClient(this.clients.find(c => c._id === saved));
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
            await this.loadAds();
        },

        async loadAds() {
            if (!this.selectedClient) return;
            this.loading = true;
            try {
                const res = await fetch(`${API_URL}/social-media/ads/${this.selectedClient}`, { headers: this.headers() });
                const data = await res.json();
                if (data.success) {
                    this.boostablePosts = data.data.boostablePosts || [];
                    this.campaigns = data.data.campaigns || [];
                }
            } finally {
                this.loading = false;
            }
        },

        fmtDate(value) {
            if (!value) return '—';
            return new Date(value).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
        }
    };
}
</script>

<?php esirom_hub_staff_mobile_nav('ads'); ?>
</body>
</html>
