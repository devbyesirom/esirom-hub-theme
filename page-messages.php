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
        .msg-thread-active { background: rgb(238 242 255); }
        .dark .msg-thread-active { background: rgb(49 46 129 / 0.25); }
        .msg-bubble-page { background: rgb(79 70 229); color: white; margin-left: auto; }
        .msg-bubble-user { background: rgb(243 244 246); color: rgb(17 24 39); }
        .dark .msg-bubble-user { background: rgb(55 65 81); color: rgb(243 244 246); }
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
                <p class="text-xs mt-2 text-amber-700 dark:text-amber-300">Reconnect the brand in <a href="<?php echo esc_url($admin_url); ?>" class="underline font-medium">Admin → Social Media Connections</a> so the Page token includes Messenger and Instagram messaging permissions.</p>
            </div>

            <div x-show="loading" class="flex justify-center py-16">
                <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
            </div>

            <div x-show="!loading && selectedClient && filteredThreads.length === 0" x-cloak class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-10 text-center">
                <p class="text-gray-600 dark:text-gray-400 text-sm">No conversations yet for this brand.</p>
                <p class="text-xs text-gray-500 mt-2">Threads appear here once Messenger or Instagram messaging is connected and active.</p>
            </div>

            <div x-show="!loading && filteredThreads.length > 0" x-cloak class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 overflow-hidden flex flex-col md:flex-row min-h-[520px] max-h-[calc(100dvh-220px)]">
                <!-- Thread list -->
                <div class="md:w-80 lg:w-96 border-b md:border-b-0 md:border-r dark:border-gray-700 flex flex-col min-h-0" :class="activeThread && 'hidden md:flex'">
                    <div class="px-4 py-3 border-b dark:border-gray-700 text-xs font-semibold text-gray-500 uppercase tracking-wide">Inbox</div>
                    <div class="flex-1 overflow-y-auto divide-y dark:divide-gray-700">
                        <template x-for="thread in filteredThreads" :key="thread.id">
                            <button type="button" @click="openThread(thread)"
                                    class="w-full text-left px-4 py-3 flex items-start gap-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
                                    :class="activeThread?.id === thread.id ? 'msg-thread-active' : ''">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                     :class="thread.platform === 'instagram' ? 'bg-gradient-to-br from-purple-500 to-pink-500' : 'bg-blue-600'"
                                     x-text="(thread.name || '?').charAt(0).toUpperCase()"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="font-semibold text-sm truncate" x-text="thread.name"></p>
                                        <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded-full flex-shrink-0"
                                              :class="thread.platform === 'instagram' ? 'bg-pink-100 text-pink-700' : 'bg-blue-100 text-blue-700'"
                                              x-text="thread.platform === 'instagram' ? 'IG' : 'FB'"></span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5" x-text="thread.snippet || 'No preview'"></p>
                                    <p class="text-[10px] text-gray-400 mt-1" x-text="fmtTime(thread.updatedTime)"></p>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Conversation panel -->
                <div class="flex-1 flex flex-col min-h-0 min-w-0" :class="!activeThread && 'hidden md:flex'">
                    <div x-show="!activeThread" class="flex-1 flex items-center justify-center text-sm text-gray-500 p-8 text-center">
                        Select a conversation to read and reply
                    </div>

                    <template x-if="activeThread">
                        <div class="flex flex-col flex-1 min-h-0">
                            <div class="px-4 py-3 border-b dark:border-gray-700 flex items-center gap-3 flex-shrink-0">
                                <button type="button" @click="closeThread()" class="md:hidden p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                     :class="activeThread.platform === 'instagram' ? 'bg-gradient-to-br from-purple-500 to-pink-500' : 'bg-blue-600'"
                                     x-text="(activeThread.name || '?').charAt(0).toUpperCase()"></div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold truncate" x-text="activeThread.name"></p>
                                    <p class="text-xs text-gray-500" x-text="activeThread.platform === 'instagram' ? 'Instagram DM' : 'Messenger'"></p>
                                </div>
                                <button type="button" @click="loadThreadMessages()" class="text-xs text-indigo-600 hover:underline" :disabled="threadLoading">Refresh</button>
                            </div>

                            <div x-show="threadLoading" class="flex-1 flex items-center justify-center">
                                <div class="w-7 h-7 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                            </div>

                            <div x-show="threadError && !threadLoading" class="mx-4 mt-4 rounded-lg border border-amber-200 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-800 p-3 text-xs text-amber-800 dark:text-amber-200" x-text="threadError"></div>

                            <div x-show="!threadLoading" class="flex-1 overflow-y-auto p-4 space-y-3" id="msg-scroll-area">
                                <template x-for="msg in threadMessages" :key="msg.id">
                                    <div class="flex" :class="msg.isPage ? 'justify-end' : 'justify-start'">
                                        <div class="max-w-[85%] rounded-2xl px-4 py-2 text-sm shadow-sm"
                                             :class="msg.isPage ? 'msg-bubble-page rounded-br-md' : 'msg-bubble-user rounded-bl-md dark:bg-gray-700'">
                                            <p class="whitespace-pre-wrap break-words" x-text="msg.text"></p>
                                            <p class="text-[10px] mt-1 opacity-70" x-text="fmtTime(msg.createdTime) + (msg.isPage ? ' · You' : '')"></p>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="!threadLoading && threadMessages.length === 0" class="text-center text-sm text-gray-500 py-8">No messages loaded for this thread.</div>
                            </div>

                            <div class="border-t dark:border-gray-700 p-3 flex-shrink-0 bg-gray-50 dark:bg-gray-900/50">
                                <div class="flex gap-2 items-end">
                                    <textarea x-model="replyText" rows="2" placeholder="Type a reply…"
                                              @keydown.meta.enter="sendReply()" @keydown.ctrl.enter="sendReply()"
                                              class="flex-1 resize-none rounded-xl border dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                                    <button type="button" @click="sendReply()" :disabled="sending || !replyText.trim()"
                                            class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0">
                                        <span x-show="!sending">Send</span>
                                        <span x-show="sending">…</span>
                                    </button>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-2">Replies must be sent within Meta’s 24-hour messaging window. Cmd/Ctrl+Enter to send.</p>
                                <p x-show="sendError" class="text-xs text-red-600 mt-1" x-text="sendError"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function messagesApp() {
    return {
        authChecked: false,
        user: null,
        viewMode: localStorage.getItem('viewMode') || 'admin',
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
        activeThread: null,
        threadMessages: [],
        threadLoading: false,
        threadError: null,
        replyText: '',
        sending: false,
        sendError: null,

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
                if (this.user.role === 'brand_rep') {
                    this.viewMode = 'brand_rep';
                } else if (this.user.role === 'admin' && this.viewMode === 'client') {
                    this.viewMode = 'admin';
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
            this.activeThread = null;
            this.threadMessages = [];
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
            if (this.activeThread && !this.filteredThreads.some(t => t.id === this.activeThread.id)) {
                this.closeThread();
            }
        },

        openThread(thread) {
            this.activeThread = thread;
            this.replyText = '';
            this.sendError = null;
            this.threadError = null;
            this.loadThreadMessages();
        },

        closeThread() {
            this.activeThread = null;
            this.threadMessages = [];
            this.replyText = '';
            this.threadError = null;
            this.sendError = null;
        },

        async loadThreadMessages() {
            if (!this.selectedClient || !this.activeThread) return;
            this.threadLoading = true;
            this.threadError = null;
            try {
                const params = new URLSearchParams();
                if (this.activeThread.recipientId) params.set('recipientId', this.activeThread.recipientId);
                const res = await fetch(
                    `${API_URL}/social-media/messages/${this.selectedClient}/threads/${encodeURIComponent(this.activeThread.id)}?${params}`,
                    { headers: this.headers() }
                );
                const data = await res.json();
                if (data.success) {
                    this.threadMessages = data.data.messages || [];
                    if (data.data.error) this.threadError = data.data.error;
                    this.$nextTick(() => {
                        const el = document.getElementById('msg-scroll-area');
                        if (el) el.scrollTop = el.scrollHeight;
                    });
                } else {
                    this.threadError = data.message || 'Could not load messages';
                    this.threadMessages = [];
                }
            } catch (e) {
                this.threadError = 'Failed to load conversation';
                this.threadMessages = [];
            } finally {
                this.threadLoading = false;
            }
        },

        async sendReply() {
            if (!this.activeThread || !this.replyText.trim() || this.sending) return;
            if (!this.activeThread.recipientId) {
                this.sendError = 'Cannot reply — missing recipient ID. Refresh the inbox and try again.';
                return;
            }
            this.sending = true;
            this.sendError = null;
            try {
                const res = await fetch(`${API_URL}/social-media/messages/${this.selectedClient}/reply`, {
                    method: 'POST',
                    headers: this.headers(),
                    body: JSON.stringify({
                        recipientId: this.activeThread.recipientId,
                        text: this.replyText.trim(),
                        platform: this.activeThread.platform
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.replyText = '';
                    await this.loadThreadMessages();
                    await this.loadMessages();
                } else {
                    this.sendError = data.message || 'Failed to send reply';
                }
            } catch (e) {
                this.sendError = 'Failed to send reply';
            } finally {
                this.sending = false;
            }
        },

        fmtTime(value) {
            if (!value) return '';
            return new Date(value).toLocaleString('en-GB', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
        },

        logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = LOGIN_URL;
        }
    };
}
</script>

<?php esirom_hub_staff_mobile_nav('messages'); ?>
</body>
</html>
