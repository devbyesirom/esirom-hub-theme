<?php
/**
 * Template Name: Password Vault
 * Description: Brand password manager — admin, brand reps, and clients
 */
if (!defined('ABSPATH')) exit;

show_admin_bar(false);

$api_url = get_option('esirom_api_url', 'https://esirom-hub-backend-production.up.railway.app/api');
$login_url = esc_url(get_permalink(get_page_by_path('login')));
$dashboard_url = esc_url(get_permalink(get_page_by_path('dashboard')));
$vault_url = esc_url(get_permalink(get_page_by_path('password-vault')));
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Vault — Agency Hub</title>
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
<body class="hub-has-mobile-nav h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 pb-16 md:pb-0" x-data="passwordVaultApp()" x-init="init()">

<div x-show="!authChecked" class="fixed inset-0 flex items-center justify-center bg-white dark:bg-gray-900 z-50">
    <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
</div>

<div x-show="authChecked && !allowed" x-cloak class="fixed inset-0 flex items-center justify-center">
    <div class="text-center">
        <p class="text-gray-500 mb-4">You do not have access to Password Vault.</p>
        <a href="<?php echo $dashboard_url; ?>" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Back to Dashboard</a>
    </div>
</div>

<div x-show="authChecked && allowed" x-cloak class="hub-app-shell flex flex-col md:flex-row">
    <aside :class="isSidebarOpen ? 'sidebar-expanded' : 'sidebar-collapsed'" class="hub-app-sidebar sidebar hidden md:flex bg-white dark:bg-gray-900/70 dark:backdrop-blur-sm border-r border-gray-200 dark:border-gray-700/50 flex-col flex-shrink-0">
        <?php esirom_hub_staff_sidebar_header(false); ?>
        <nav class="flex-1 px-2 py-4 space-y-2 overflow-y-auto">
            <?php esirom_hub_client_sidebar_nav('password_vault', 'site', false); ?>
            <?php esirom_hub_staff_sidebar_nav('password_vault', 'site', false); ?>
        </nav>
        <div class="p-2 border-t border-gray-200 dark:border-gray-700/50">
            <?php esirom_hub_staff_sidebar_footer('site', false); ?>
        </div>
    </aside>

    <main class="hub-app-main">
        <header class="bg-white/80 dark:bg-gray-900/70 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700/50 px-4 sm:px-6 py-4 sticky top-0 z-10">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-lg font-bold text-gray-900 dark:text-white">Password Vault</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Brand accounts grouped by client — click a brand to view all logins</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span x-show="overdueCount > 0 && user?.role !== 'client'" class="px-2.5 py-1 text-xs rounded-lg bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300" x-text="overdueCount + ' need verification'"></span>
                    <label x-show="isAdmin" class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl text-xs font-semibold cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600">
                        Import CSV
                        <input type="file" accept=".csv,text/csv" @change="importCsv($event)" class="hidden">
                    </label>
                    <button x-show="isAdmin" @click="normalizeGroups()" class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl text-xs font-semibold hover:bg-gray-200 dark:hover:bg-gray-600">Fix Groups</button>
                    <button x-show="isStaff" @click="openModal()" class="px-3 py-1.5 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-700">+ Add Account</button>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 mt-3">
                <input type="search" x-model="search" @input.debounce.300ms="loadGrouped()" placeholder="Search brands or accounts…" class="flex-1 min-w-[180px] px-3 py-2 border rounded-xl text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                <select x-show="isAdmin" x-model="statusFilter" @change="loadGrouped()" class="px-3 py-2 border rounded-xl text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="archived">Archived</option>
                </select>
                <select x-model="categoryFilter" @change="loadGrouped()" class="px-3 py-2 border rounded-xl text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <option value="">All Categories</option>
                    <option value="social_media">Social Media</option>
                    <option value="email">Email</option>
                    <option value="analytics">Analytics</option>
                    <option value="design_tools">Design Tools</option>
                    <option value="link_tools">Link Tools</option>
                    <option value="hosting">Hosting</option>
                    <option value="utilities">Utilities</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </header>

        <div class="p-4 sm:p-6 max-w-5xl mx-auto w-full">
            <div x-show="loading" class="py-16 text-center text-gray-500">Loading credentials…</div>

            <div x-show="!loading && groups.length === 0" x-cloak class="py-16 text-center text-gray-500">
                <p class="font-medium">No credentials found</p>
                <p class="text-sm mt-1" x-show="isAdmin">Import your CSV or add accounts manually.</p>
            </div>

            <div class="space-y-3" x-show="!loading && groups.length > 0" x-cloak>
                <template x-for="group in groups" :key="group.key">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
                        <div class="flex items-center gap-2 px-4 py-4">
                            <button type="button" @click="toggleGroup(group.key)" class="flex-1 min-w-0 flex items-center gap-3 text-left hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors rounded-lg -m-1 p-1">
                                <svg class="w-5 h-5 text-gray-400 shrink-0 transition-transform" :class="expanded[group.key] ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="font-semibold text-gray-900 dark:text-white truncate" x-text="group.label"></h2>
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300" x-text="group.total + ' accounts'"></span>
                                        <span x-show="group.active > 0" class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300" x-text="group.active + ' active'"></span>
                                        <span x-show="group.overdue > 0" class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300" x-text="group.overdue + ' overdue'"></span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5" x-show="group.clientName" x-text="group.clientName"></p>
                                </div>
                            </button>
                            <div x-show="isAdmin" class="flex flex-wrap gap-1 shrink-0">
                                <button type="button" @click="bulkGroup(group, { status: 'active' })" class="px-2 py-1 text-[11px] bg-green-600 text-white rounded-lg hover:bg-green-700">Activate</button>
                                <button type="button" @click="bulkGroup(group, { status: 'archived', visibleToBrandReps: false })" class="px-2 py-1 text-[11px] bg-gray-600 text-white rounded-lg hover:bg-gray-700">Archive</button>
                            </div>
                        </div>

                        <div x-show="expanded[group.key]" class="border-t border-gray-100 dark:border-gray-700 divide-y dark:divide-gray-700">
                            <template x-for="account in group.accounts" :key="account._id">
                                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-3 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="font-medium text-sm text-gray-900 dark:text-white" x-text="account.accountName"></p>
                                            <span class="text-xs capitalize text-gray-500" x-text="account.platform || formatCategory(account.category)"></span>
                                            <span :class="verificationClass(account)" class="px-2 py-0.5 text-[10px] rounded-full" x-text="verificationLabel(account)"></span>
                                        </div>
                                        <p class="text-xs font-mono text-gray-500 mt-1 truncate" x-text="account.username || 'No username'"></p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <button type="button" @click="viewAccount(account)" class="px-3 py-1.5 text-xs bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">View</button>
                                        <button type="button" x-show="isStaff" @click="editAccount(account)" class="px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Edit</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </main>
</div>

<!-- Account modal -->
<div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50" @keydown.escape.window="closeModal()">
    <div class="bg-white dark:bg-gray-800 w-full sm:max-w-lg sm:rounded-xl rounded-t-xl shadow-2xl max-h-[92vh] overflow-hidden" @click.stop>
        <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-gray-900 dark:text-white" x-text="accountModalView ? 'Account Details' : (form._id ? 'Edit Account' : 'Add Account')"></h3>
                <p class="text-xs text-gray-500" x-text="selected?.accountName || ''"></p>
            </div>
            <button @click="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
        <div class="p-5 overflow-y-auto max-h-[calc(92vh-130px)] space-y-4">
            <template x-if="accountModalView && selected">
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Username</p>
                        <div class="flex gap-2">
                            <code class="flex-1 px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm break-all" x-text="selected.username || '—'"></code>
                            <button x-show="selected.username" @click="copy(selected.username)" class="px-3 py-2 text-xs bg-gray-100 dark:bg-gray-700 rounded-lg">Copy</button>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Password</p>
                        <div class="flex gap-2">
                            <code class="flex-1 px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm font-mono break-all" x-text="showPassword ? selected.password : '••••••••••••'"></code>
                            <button @click="showPassword = !showPassword" class="px-3 py-2 text-xs bg-gray-100 dark:bg-gray-700 rounded-lg" x-text="showPassword ? 'Hide' : 'Reveal'"></button>
                            <button x-show="selected.password" @click="copy(selected.password)" class="px-3 py-2 text-xs bg-indigo-600 text-white rounded-lg">Copy</button>
                        </div>
                    </div>
                    <div x-show="selected.twoFactorNotes">
                        <p class="text-xs text-gray-500 mb-1">2FA / Recovery Codes</p>
                        <div class="flex gap-2">
                            <pre class="flex-1 px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm whitespace-pre-wrap" x-text="selected.twoFactorNotes"></pre>
                            <button @click="copy(selected.twoFactorNotes)" class="px-3 py-2 text-xs bg-gray-100 dark:bg-gray-700 rounded-lg shrink-0">Copy</button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        <div><p class="text-xs text-gray-500">Recovery Email</p><p x-text="selected.recoveryEmail || '—'"></p></div>
                        <div><p class="text-xs text-gray-500">Recovery Phone</p><p x-text="selected.recoveryPhone || '—'"></p></div>
                    </div>
                    <div x-show="selected.url"><p class="text-xs text-gray-500">Login URL</p><a :href="selected.url" target="_blank" class="text-indigo-600 text-sm break-all" x-text="selected.url"></a></div>
                    <div x-show="selected.notes"><p class="text-xs text-gray-500">Notes</p><p class="text-sm whitespace-pre-wrap" x-text="selected.notes"></p></div>
                </div>
            </template>
            <template x-if="!accountModalView">
                <div class="space-y-3">
                    <input type="text" x-model="form.groupName" placeholder="Brand / Group" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <input type="text" x-model="form.accountName" placeholder="Account name *" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <div class="grid grid-cols-2 gap-3">
                        <select x-model="form.category" class="px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="social_media">Social Media</option><option value="email">Email</option><option value="analytics">Analytics</option>
                            <option value="design_tools">Design Tools</option><option value="link_tools">Link Tools</option><option value="hosting">Hosting</option>
                            <option value="utilities">Utilities</option><option value="other">Other</option>
                        </select>
                        <input type="text" x-model="form.platform" placeholder="Platform" class="px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <input type="text" x-model="form.username" placeholder="Username" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <input type="text" x-model="form.password" placeholder="Password" class="w-full px-3 py-2 border rounded-lg text-sm font-mono dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <textarea x-model="form.twoFactorNotes" rows="2" placeholder="2FA / recovery codes" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <input type="email" x-model="form.recoveryEmail" placeholder="Recovery email" class="px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <input type="text" x-model="form.recoveryPhone" placeholder="Recovery phone" class="px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div x-show="isAdmin" class="grid grid-cols-2 gap-3">
                        <select x-model="form.status" class="px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="active">Active</option><option value="archived">Archived</option>
                        </select>
                        <label class="flex items-center gap-2 text-sm px-1"><input type="checkbox" x-model="form.visibleToBrandReps" class="rounded"> Visible to brand reps</label>
                    </div>
                </div>
            </template>
        </div>
        <div class="px-5 py-4 border-t dark:border-gray-700 flex justify-between gap-2">
            <div class="flex gap-2">
                <button x-show="accountModalView && isStaff" @click="verifyAccount()" class="px-3 py-2 text-sm bg-green-600 text-white rounded-lg">Mark Verified</button>
                <button x-show="accountModalView && isStaff" @click="accountModalView = false" class="px-3 py-2 text-sm border rounded-lg dark:border-gray-600">Edit</button>
                <button x-show="isAdmin && selected?._id" @click="deleteAccount(selected)" class="px-3 py-2 text-sm text-red-600">Delete</button>
            </div>
            <div class="flex gap-2 ml-auto">
                <button @click="closeModal()" class="px-3 py-2 text-sm text-gray-500">Close</button>
                <button x-show="!accountModalView" @click="saveAccount()" class="px-3 py-2 text-sm bg-indigo-600 text-white rounded-lg">Save</button>
            </div>
        </div>
    </div>
</div>

<div x-show="toast.show" x-cloak class="fixed bottom-20 md:bottom-6 right-4 z-[60] px-4 py-3 rounded-xl shadow-lg text-sm text-white" :class="toast.type === 'error' ? 'bg-red-600' : 'bg-gray-900'" x-text="toast.message"></div>

<script>
function passwordVaultApp() {
    return {
        authChecked: false,
        allowed: false,
        user: null,
        isSidebarOpen: true,
        loading: true,
        groups: [],
        expanded: {},
        search: '',
        statusFilter: '',
        categoryFilter: '',
        overdueCount: 0,
        showModal: false,
        viewMode: localStorage.getItem('viewMode') || 'admin',
        accountModalView: true,
        showPassword: false,
        selected: null,
        form: {},
        toast: { show: false, message: '', type: 'success' },

        get isAdmin() { return this.user?.role === 'admin'; },
        get isStaff() { return ['admin', 'brand_rep'].includes(this.user?.role); },

        headers() {
            return { 'Authorization': `Bearer ${localStorage.getItem('token')}`, 'Content-Type': 'application/json' };
        },

        async init() {
            const token = localStorage.getItem('token');
            if (!token) { window.location.href = LOGIN_URL; return; }
            try {
                const res = await fetch(`${API_URL}/auth/me`, { headers: { Authorization: `Bearer ${token}` } });
                if (!res.ok) throw new Error('auth');
                const data = await res.json();
                this.user = data.user;
                this.allowed = ['admin', 'brand_rep', 'client'].includes(this.user?.role);
                if (this.user?.role === 'client') {
                    this.viewMode = 'client';
                } else if (this.user?.role === 'admin') {
                    this.viewMode = localStorage.getItem('viewMode') || 'admin';
                } else {
                    this.viewMode = 'admin';
                }
                if (this.allowed) {
                    if (this.isAdmin) await this.normalizeGroups(true);
                    await this.loadGrouped();
                }
            } catch (e) {
                window.location.href = LOGIN_URL;
            } finally {
                this.authChecked = true;
            }
        },

        toggleGroup(key) {
            this.expanded = { ...this.expanded, [key]: !this.expanded[key] };
        },

        async loadGrouped() {
            this.loading = true;
            const params = new URLSearchParams();
            if (this.search) params.append('search', this.search);
            if (this.statusFilter) params.append('status', this.statusFilter);
            if (this.categoryFilter) params.append('category', this.categoryFilter);
            try {
                const res = await fetch(`${API_URL}/credentials/grouped?${params}`, { headers: this.headers() });
                let data = {};
                try { data = await res.json(); } catch (e) { /* ignore */ }

                if (res.ok && data.success) {
                    this.groups = data.groups || [];
                    this.overdueCount = data.counts?.overdueVerification || 0;
                    return;
                }

                const fallback = await fetch(`${API_URL}/credentials?${params}`, { headers: this.headers() });
                const flat = await fallback.json();
                if (flat.success) {
                    this.groups = this.groupCredentialsClient(flat.data || []);
                    this.overdueCount = flat.counts?.overdueVerification || 0;
                    return;
                }

                this.notify(flat.message || data.message || 'Failed to load credentials', 'error');
            } catch (e) {
                this.notify('Failed to load credentials', 'error');
            } finally {
                this.loading = false;
            }
        },

        groupCredentialsClient(items = []) {
            const normalize = (value) => String(value || '').toLowerCase().replace(/[^a-z0-9]+/g, ' ').trim();
            const inferPrefix = (accountName = '') => {
                const trimmed = String(accountName || '').trim();
                const paren = trimmed.match(/^(.+?)\s*\([^)]+\)\s*$/i);
                if (paren) return paren[1].trim();
                const dash = trimmed.match(/^(.+?)\s*[-–—]\s+/);
                if (dash) return dash[1].trim();
                return trimmed;
            };
            const grouped = new Map();

            items.forEach((item) => {
                const clientRef = item.clientId;
                const clientId = clientRef?._id || clientRef || null;
                const clientName = clientRef?.brandName || clientRef?.name || null;
                let key, label, groupName;

                if (clientId && clientName) {
                    key = `client:${clientId}`;
                    label = clientName;
                    groupName = item.groupName?.trim() || clientName;
                } else if (item.groupName?.trim()) {
                    label = item.groupName.trim();
                    key = `group:${normalize(label)}`;
                    groupName = label;
                } else {
                    label = inferPrefix(item.accountName) || 'Other Accounts';
                    key = label === 'Other Accounts' ? 'ungrouped' : `group:${normalize(label)}`;
                    groupName = label === 'Other Accounts' ? '' : label;
                }

                if (!grouped.has(key)) {
                    grouped.set(key, {
                        key,
                        label,
                        groupName,
                        clientId,
                        clientName,
                        total: 0,
                        active: 0,
                        archived: 0,
                        visibleToBrandReps: 0,
                        overdue: 0,
                        accounts: []
                    });
                }

                const bucket = grouped.get(key);
                bucket.total += 1;
                if (item.status === 'active') bucket.active += 1;
                if (item.status === 'archived') bucket.archived += 1;
                if (item.visibleToBrandReps) bucket.visibleToBrandReps += 1;
                if (item.verification?.overdue) bucket.overdue += 1;
                bucket.accounts.push(item);
            });

            return [...grouped.values()]
                .sort((a, b) => a.label.localeCompare(b.label))
                .map((group) => ({
                    ...group,
                    accounts: group.accounts.sort((a, b) => (a.accountName || '').localeCompare(b.accountName || ''))
                }));
        },

        async normalizeGroups(silent = false) {
            try {
                const res = await fetch(`${API_URL}/credentials/normalize-groups`, { method: 'POST', headers: this.headers(), body: '{}' });
                if (res.status === 404) return;
                const data = await res.json();
                if (data.success && !silent && data.updated > 0) this.notify(data.message, 'success');
            } catch (e) { /* ignore */ }
        },

        async bulkGroup(group, payload) {
            const action = payload.status === 'archived' ? 'archive' : 'activate';
            if (!confirm(`${action.charAt(0).toUpperCase() + action.slice(1)} all ${group.total} account${group.total === 1 ? '' : 's'} in "${group.label}"?`)) return;
            try {
                const res = await fetch(`${API_URL}/credentials/bulk/status`, {
                    method: 'POST', headers: this.headers(),
                    body: JSON.stringify({ groupKey: group.key, groupName: group.groupName || group.label, ...payload })
                });
                const data = await res.json();
                if (data.success) { this.notify(data.message, 'success'); await this.loadGrouped(); }
                else this.notify(data.message || 'Update failed', 'error');
            } catch (e) {
                this.notify('Update failed', 'error');
            }
        },

        async viewAccount(account) {
            const res = await fetch(`${API_URL}/credentials/${account._id}`, { headers: this.headers() });
            const data = await res.json();
            if (data.success) {
                this.selected = data.data;
                this.accountModalView = true;
                this.showPassword = false;
                this.showModal = true;
            }
        },

        editAccount(account) { this.viewAccount(account).then(() => { this.accountModalView = false; this.form = { ...this.selected, password: '' }; }); },

        openModal() {
            this.form = { groupName: '', accountName: '', category: 'social_media', platform: '', username: '', password: '', twoFactorNotes: '', recoveryEmail: '', recoveryPhone: '', url: '', notes: '', status: 'archived', visibleToBrandReps: false };
            this.selected = null;
            this.accountModalView = false;
            this.showModal = true;
        },

        closeModal() { this.showModal = false; this.selected = null; this.showPassword = false; },

        async saveAccount() {
            if (!this.form.accountName?.trim()) { this.notify('Account name is required', 'error'); return; }
            const isEdit = Boolean(this.form._id);
            const payload = { ...this.form }; delete payload._id;
            const res = await fetch(`${API_URL}/credentials${isEdit ? '/' + this.form._id : ''}`, {
                method: isEdit ? 'PUT' : 'POST', headers: this.headers(), body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) { this.notify('Saved', 'success'); this.closeModal(); await this.loadGrouped(); }
            else this.notify(data.message || 'Save failed', 'error');
        },

        async deleteAccount(item) {
            if (!confirm(`Delete "${item.accountName}"?`)) return;
            const res = await fetch(`${API_URL}/credentials/${item._id}`, { method: 'DELETE', headers: this.headers() });
            const data = await res.json();
            if (data.success) { this.notify('Deleted', 'success'); this.closeModal(); await this.loadGrouped(); }
        },

        async verifyAccount() {
            const res = await fetch(`${API_URL}/credentials/${this.selected._id}/verify`, {
                method: 'POST', headers: this.headers(),
                body: JSON.stringify({ recoveryEmail: this.selected.recoveryEmail, recoveryPhone: this.selected.recoveryPhone, twoFactorNotes: this.selected.twoFactorNotes })
            });
            const data = await res.json();
            if (data.success) { this.selected = data.data; this.notify('Marked as verified', 'success'); await this.loadGrouped(); }
        },

        async importCsv(event) {
            const file = event.target.files?.[0];
            if (!file) return;
            const csv = await file.text();
            const res = await fetch(`${API_URL}/credentials/import/csv`, { method: 'POST', headers: this.headers(), body: JSON.stringify({ csv }) });
            const data = await res.json();
            event.target.value = '';
            if (data.success) { this.notify(data.message, 'success'); await this.normalizeGroups(true); await this.loadGrouped(); }
            else this.notify(data.message || 'Import failed', 'error');
        },

        async copy(value) {
            try { await navigator.clipboard.writeText(String(value)); this.notify('Copied', 'success'); }
            catch (e) { this.notify('Copy failed', 'error'); }
        },

        formatCategory(c) { return (c || 'other').replace(/_/g, ' '); },
        verificationClass(item) { return item?.verification?.overdue ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'; },
        verificationLabel(item) { return item?.verification?.overdue ? 'Overdue' : 'Current'; },
        notify(message, type = 'success') { this.toast = { show: true, message, type }; setTimeout(() => { this.toast.show = false; }, 3500); },

        logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = LOGIN_URL;
        }
    };
}
</script>

<nav class="hub-mobile-bottom-nav md:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 z-40">
    <div class="flex justify-around items-center h-16 overflow-x-auto">
        <?php esirom_hub_client_mobile_nav('site'); ?>
    </div>
</nav>
</body>
</html>
