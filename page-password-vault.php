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
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="isClientView ? 'Your brand login details — click to view and copy' : (isMultimediaBrandRep ? 'Shared company passwords — click a group to view logins' : 'Company-wide tools at top, brand social accounts below')"></p>
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
                <select x-model="vaultSectionFilter" @change="loadGrouped()" class="px-3 py-2 border rounded-xl text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <option value="">All Sections</option>
                    <option value="company_wide">Company Wide</option>
                    <option x-show="showSocialMediaSection" value="social_media">Social Media</option>
                </select>
                <select x-show="isAdmin" x-model="categoryFilter" @change="loadGrouped()" class="px-3 py-2 border rounded-xl text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
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

            <div x-show="!loading && !hasAnyGroups" x-cloak class="py-16 text-center text-gray-500">
                <p class="font-medium">No credentials found</p>
                <p class="text-sm mt-1" x-show="isAdmin">Import your CSV or add accounts manually.</p>
                <p class="text-sm mt-1" x-show="isClientView && !isAdmin">No active accounts are linked to your brand yet. Contact your account manager if you need access.</p>
            </div>

            <div class="space-y-8" x-show="!loading && hasAnyGroups" x-cloak>
                <template x-for="section in vaultSections" :key="section.key">
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 pb-1 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-base font-bold text-gray-900 dark:text-white" x-text="section.label"></h2>
                            <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300" x-text="sectionAccountCount(section) + ' accounts'"></span>
                        </div>
                        <p class="text-xs text-gray-500 -mt-1" x-show="section.key === 'company_wide'">Shared company passwords and tools</p>
                        <p class="text-xs text-gray-500 -mt-1" x-show="section.key === 'social_media'">Brand social media accounts grouped by client</p>

                        <div x-show="section.groups.length === 0" class="py-8 text-center text-sm text-gray-400">No accounts in this section</div>

                        <template x-for="group in section.groups" :key="section.key + ':' + group.key">
                            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
                                <div class="flex items-center gap-2 px-4 py-4">
                                    <button type="button" @click="toggleGroup(section.key, group.key)" class="flex-1 min-w-0 flex items-center gap-3 text-left hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors rounded-lg -m-1 p-1">
                                        <svg class="w-5 h-5 text-gray-400 shrink-0 transition-transform" :class="expanded[groupKey(section.key, group.key)] ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h3 class="font-semibold text-gray-900 dark:text-white truncate" x-text="group.label"></h3>
                                                <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300" x-text="group.total + ' accounts'"></span>
                                                <span x-show="group.active > 0" class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300" x-text="group.active + ' active'"></span>
                                                <span x-show="group.archived > 0" class="px-2 py-0.5 text-xs rounded-full bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300" x-text="group.archived + ' archived'"></span>
                                                <span x-show="group.overdue > 0" class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300" x-text="group.overdue + ' overdue'"></span>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-0.5" x-show="group.clientName" x-text="group.clientName"></p>
                                        </div>
                                    </button>
                                    <div x-show="isAdmin" class="flex flex-wrap gap-1 shrink-0">
                                        <button type="button" @click="bulkGroup(group, { status: 'active' })" class="px-2 py-1 text-[11px] bg-green-600 text-white rounded-lg hover:bg-green-700">Activate</button>
                                        <button type="button" @click="bulkGroup(group, { status: 'archived', visibleToBrandReps: false })" class="px-2 py-1 text-[11px] bg-gray-600 text-white rounded-lg hover:bg-gray-700">Archive</button>
                                        <button type="button" @click="openRenameGroup(group)" class="px-2 py-1 text-[11px] bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 rounded-lg hover:bg-indigo-200 dark:hover:bg-indigo-900/50">Rename</button>
                                        <button type="button" @click="openMoveGroup(group)" class="px-2 py-1 text-[11px] bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 rounded-lg hover:bg-indigo-200 dark:hover:bg-indigo-900/50">Move to…</button>
                                        <button type="button" @click="deleteGroup(group)" class="px-2 py-1 text-[11px] bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>
                                    </div>
                                </div>

                                <div x-show="expanded[groupKey(section.key, group.key)]" class="border-t border-gray-100 dark:border-gray-700 divide-y dark:divide-gray-700">
                                    <template x-for="account in group.accounts" :key="account._id">
                                        <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-3 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <p class="font-medium text-sm text-gray-900 dark:text-white" x-text="account.accountName"></p>
                                                    <span class="text-xs capitalize text-gray-500" x-text="account.platform || formatCategory(account.category)"></span>
                                                    <span :class="statusClass(account.status)" class="px-2 py-0.5 text-[10px] rounded-full font-medium" x-text="statusLabel(account.status)"></span>
                                                    <span :class="verificationClass(account)" class="px-2 py-0.5 text-[10px] rounded-full" x-text="verificationLabel(account)"></span>
                                                </div>
                                                <p class="text-xs font-mono text-gray-500 mt-1 truncate" x-text="account.username || 'No username'"></p>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <button type="button" @click="viewAccount(account)" class="px-3 py-1.5 text-xs bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">View</button>
                                                <button type="button" x-show="isStaff" @click="editAccount(account)" class="px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Edit</button>
                                                <button type="button" x-show="isAdmin" @click="openMoveAccount(account, group)" class="px-3 py-1.5 text-xs bg-indigo-50 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-300 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/40">Move</button>
                                                <button type="button" x-show="isAdmin" @click="deleteAccount(account)" class="px-3 py-1.5 text-xs text-red-600 border border-red-200 dark:border-red-900/40 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">Delete</button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
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
                <span x-show="selected?.status" :class="statusClass(selected?.status)" class="inline-block mt-1 px-2 py-0.5 text-[10px] rounded-full font-medium" x-text="statusLabel(selected?.status)"></span>
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
                        <select x-model="form.vaultSection" class="px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="company_wide">Company Wide</option>
                            <option value="social_media">Social Media</option>
                        </select>
                        <select x-model="form.category" class="px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="social_media">Social Media</option><option value="email">Email</option><option value="analytics">Analytics</option>
                            <option value="design_tools">Design Tools</option><option value="link_tools">Link Tools</option><option value="hosting">Hosting</option>
                            <option value="utilities">Utilities</option><option value="other">Other</option>
                        </select>
                    </div>
                    <input type="text" x-model="form.platform" placeholder="Platform" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
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

<!-- Group action modal (rename / move) -->
<div x-show="showGroupModal" x-cloak class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50" @keydown.escape.window="closeGroupModal()">
    <div class="bg-white dark:bg-gray-800 w-full sm:max-w-md sm:rounded-xl rounded-t-xl shadow-2xl" @click.stop>
        <div class="px-5 py-4 border-b dark:border-gray-700">
            <h3 class="font-bold text-gray-900 dark:text-white" x-text="groupModalTitle"></h3>
            <p class="text-xs text-gray-500 mt-1" x-text="groupModalSubtitle"></p>
        </div>
        <div class="p-5 space-y-3">
            <template x-if="groupModalMode === 'rename'">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">New brand / group name</label>
                    <input type="text" x-model="groupModalForm.newGroupName" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Brand name">
                </div>
            </template>
            <template x-if="groupModalMode === 'move'">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Move to group</label>
                    <input type="text" x-model="groupModalForm.targetGroupName" list="vault-group-names" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Existing or new group name">
                    <p class="text-[11px] text-gray-400 mt-1">Pick an existing brand or type a new group name.</p>
                </div>
            </template>
            <datalist id="vault-group-names">
                <option x-for="name in groupNameOptions" :key="name" :value="name"></option>
            </datalist>
        </div>
        <div class="px-5 py-4 border-t dark:border-gray-700 flex justify-end gap-2">
            <button type="button" @click="closeGroupModal()" class="px-3 py-2 text-sm text-gray-500">Cancel</button>
            <button type="button" @click="submitGroupModal()" class="px-3 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700" x-text="groupModalMode === 'rename' ? 'Rename' : 'Move'"></button>
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
        vaultSections: [],
        groups: [],
        expanded: {},
        search: '',
        statusFilter: 'active',
        vaultSectionFilter: '',
        categoryFilter: '',
        overdueCount: 0,
        selectedViewClient: localStorage.getItem('selectedViewClient') || '',
        showModal: false,
        showGroupModal: false,
        groupModalMode: 'rename',
        groupModalTitle: '',
        groupModalSubtitle: '',
        groupModalTarget: null,
        groupModalAccount: null,
        groupModalForm: { newGroupName: '', targetGroupName: '' },
        groupNameOptions: [],
        viewMode: localStorage.getItem('viewMode') || 'admin',
        accountModalView: true,
        showPassword: false,
        selected: null,
        form: {},
        toast: { show: false, message: '', type: 'success' },

        get isAdmin() { return this.user?.role === 'admin' && this.viewMode !== 'client'; },
        get isStaff() { return this.user?.role === 'admin' || (this.user?.role === 'brand_rep' && this.viewMode !== 'client'); },
        get isClientView() { return this.user?.role === 'client' || this.viewMode === 'client'; },
        get isMultimediaBrandRep() { return this.user?.role === 'brand_rep' && this.user?.department === 'multimedia'; },
        get showSocialMediaSection() {
            if (this.isClientView) return true;
            if (this.user?.role === 'admin') return true;
            if (this.user?.role === 'brand_rep') return this.user?.department === 'social_media_exec';
            return false;
        },
        get hasAnyGroups() {
            return (this.vaultSections || []).some((section) => (section.groups || []).length > 0);
        },

        groupKey(sectionKey, groupKey) {
            return `${sectionKey}:${groupKey}`;
        },

        sectionAccountCount(section) {
            return (section.groups || []).reduce((sum, group) => sum + (group.total || 0), 0);
        },

        userClientIds() {
            const ids = [];
            const add = (ref) => {
                if (!ref) return;
                const id = ref?._id || ref;
                if (id) ids.push(String(id));
            };
            add(this.user?.clientId);
            (this.user?.clientIds || []).forEach(add);
            return [...new Set(ids)];
        },

        get scopedClientIds() {
            if (this.user?.role === 'admin' && this.viewMode === 'client' && this.selectedViewClient) {
                return [String(this.selectedViewClient)];
            }
            if (this.user?.role === 'client') return this.userClientIds();
            return [];
        },

        queryParams() {
            const params = new URLSearchParams();
            if (this.search) params.append('search', this.search);
            if (this.statusFilter) params.append('status', this.statusFilter);
            if (this.vaultSectionFilter) params.append('vaultSection', this.vaultSectionFilter);
            if (this.categoryFilter) params.append('category', this.categoryFilter);
            if (this.user?.role === 'admin' && this.viewMode !== 'admin') {
                params.append('viewAs', this.viewMode);
                if (this.viewMode === 'client' && this.selectedViewClient) {
                    params.append('viewAsClientId', this.selectedViewClient);
                }
            } else if (this.user?.role === 'brand_rep') {
                // brand reps always load active-only; enforced server-side
            }
            return params;
        },

        filterGroupsByStatus(groups = []) {
            if (!this.statusFilter) return groups;
            const want = this.statusFilter;
            return groups
                .map((group) => {
                    const accounts = (group.accounts || []).filter((a) => a.status === want);
                    if (!accounts.length) return null;
                    return {
                        ...group,
                        accounts,
                        total: accounts.length,
                        active: accounts.filter((a) => a.status === 'active').length,
                        archived: accounts.filter((a) => a.status === 'archived').length,
                        overdue: accounts.filter((a) => a.verification?.overdue).length
                    };
                })
                .filter(Boolean);
        },

        applyClientScope(groups = []) {
            if (!this.isClientView) return groups;
            const allowed = this.scopedClientIds.length ? this.scopedClientIds : this.userClientIds();
            if (!allowed.length) return [];
            return groups
                .map((group) => {
                    const accounts = (group.accounts || []).filter((account) => {
                        const accountClientId = account.clientId?._id || account.clientId;
                        return accountClientId && allowed.includes(String(accountClientId));
                    });
                    if (!accounts.length) return null;
                    const groupClientId = group.clientId ? String(group.clientId) : (group.key?.startsWith('client:') ? group.key.slice(7) : null);
                    if (groupClientId && !allowed.includes(groupClientId)) return null;
                    return {
                        ...group,
                        accounts,
                        total: accounts.length,
                        active: accounts.filter((a) => a.status === 'active').length,
                        archived: accounts.filter((a) => a.status === 'archived').length,
                        overdue: accounts.filter((a) => a.verification?.overdue).length
                    };
                })
                .filter(Boolean);
        },

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
                } else if (this.user?.role === 'brand_rep') {
                    this.viewMode = 'admin';
                    this.statusFilter = 'active';
                } else if (this.user?.role === 'admin') {
                    this.viewMode = localStorage.getItem('viewMode') || 'admin';
                } else {
                    this.viewMode = 'admin';
                }
                if (this.allowed) {
                    if (this.isAdmin) {
                        await this.normalizeGroups(true);
                        await this.loadGroupNameOptions();
                    }
                    await this.loadGrouped();
                }
            } catch (e) {
                window.location.href = LOGIN_URL;
            } finally {
                this.authChecked = true;
            }
        },

        toggleGroup(sectionKey, groupKey) {
            const key = this.groupKey(sectionKey, groupKey);
            this.expanded = { ...this.expanded, [key]: !this.expanded[key] };
        },

        resolveVaultSection(item) {
            if (item?.vaultSection === 'company_wide' || item?.vaultSection === 'social_media') return item.vaultSection;
            return item?.category === 'social_media' ? 'social_media' : 'company_wide';
        },

        buildVaultSectionsFromGroups(groups = []) {
            const sectionDefs = [
                { key: 'company_wide', label: 'Company Wide' },
                { key: 'social_media', label: 'Social Media' }
            ].filter((section) => this.showSocialMediaSection || section.key !== 'social_media');

            return sectionDefs.map((section) => ({
                ...section,
                groups: groups.filter((group) => {
                    const accounts = (group.accounts || []).filter((account) => this.resolveVaultSection(account) === section.key);
                    return accounts.length > 0;
                }).map((group) => {
                    const accounts = (group.accounts || []).filter((account) => this.resolveVaultSection(account) === section.key);
                    return {
                        ...group,
                        accounts,
                        total: accounts.length,
                        active: accounts.filter((a) => a.status === 'active').length,
                        archived: accounts.filter((a) => a.status === 'archived').length,
                        overdue: accounts.filter((a) => a.verification?.overdue).length
                    };
                })
            }));
        },

        applySectionFilters(sections = []) {
            let filtered = sections;
            if (!this.showSocialMediaSection) {
                filtered = filtered.filter((section) => section.key !== 'social_media');
            }
            if (this.vaultSectionFilter) {
                filtered = filtered.filter((section) => section.key === this.vaultSectionFilter);
            }
            return filtered.map((section) => ({
                ...section,
                groups: this.filterGroupsByStatus(section.groups || [])
            }));
        },

        async loadGrouped() {
            this.loading = true;
            const params = this.queryParams();
            try {
                const res = await fetch(`${API_URL}/credentials/grouped?${params}`, { headers: this.headers() });
                let data = {};
                try { data = await res.json(); } catch (e) { /* ignore */ }

                if (res.ok && data.success) {
                    let sections = (data.sections || []).map((section) => ({
                        ...section,
                        groups: this.applyClientScope(section.groups || [])
                    }));
                    if (!sections.length && (data.groups || []).length) {
                        sections = this.buildVaultSectionsFromGroups(this.applyClientScope(data.groups || []));
                    }
                    sections = this.applySectionFilters(sections);
                    this.vaultSections = sections;
                    this.groups = sections.flatMap((section) => section.groups || []);
                    this.overdueCount = this.groups.reduce((sum, group) => sum + (group.overdue || 0), 0);
                    return;
                }

                const fallback = await fetch(`${API_URL}/credentials?${params}`, { headers: this.headers() });
                const flat = await fallback.json();
                if (flat.success) {
                    let items = (flat.data || []).filter((item) => {
                        if (!this.showSocialMediaSection && this.resolveVaultSection(item) === 'social_media') return false;
                        return true;
                    });
                    let groups = this.applyClientScope(this.groupCredentialsClient(items));
                    groups = this.filterGroupsByStatus(groups);
                    const sections = this.applySectionFilters(this.buildVaultSectionsFromGroups(groups));
                    this.vaultSections = sections;
                    this.groups = sections.flatMap((section) => section.groups || []);
                    this.overdueCount = this.groups.reduce((sum, group) => sum + (group.overdue || 0), 0);
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
                if (data.success) { this.notify(data.message, 'success'); await this.loadGrouped(); await this.loadGroupNameOptions(); }
                else this.notify(data.message || 'Update failed', 'error');
            } catch (e) {
                this.notify('Update failed', 'error');
            }
        },

        async loadGroupNameOptions() {
            try {
                const res = await fetch(`${API_URL}/credentials/groups`, { headers: this.headers() });
                const data = await res.json();
                if (data.success) {
                    this.groupNameOptions = [...new Set((data.data || []).map((g) => g.groupName || g.label).filter(Boolean))].sort();
                }
            } catch (e) { /* ignore */ }
        },

        openRenameGroup(group) {
            this.groupModalMode = 'rename';
            this.groupModalTitle = 'Rename brand';
            this.groupModalSubtitle = `Rename all accounts in "${group.label}"`;
            this.groupModalTarget = group;
            this.groupModalAccount = null;
            this.groupModalForm = { newGroupName: group.label || '', targetGroupName: '' };
            this.showGroupModal = true;
        },

        openMoveGroup(group) {
            this.groupModalMode = 'move';
            this.groupModalTitle = 'Move brand to group';
            this.groupModalSubtitle = `Move all ${group.total} account${group.total === 1 ? '' : 's'} from "${group.label}"`;
            this.groupModalTarget = group;
            this.groupModalAccount = null;
            this.groupModalForm = { newGroupName: '', targetGroupName: '' };
            this.showGroupModal = true;
        },

        openMoveAccount(account, group) {
            this.groupModalMode = 'move';
            this.groupModalTitle = 'Move account to group';
            this.groupModalSubtitle = account.accountName;
            this.groupModalTarget = group;
            this.groupModalAccount = account;
            this.groupModalForm = { newGroupName: '', targetGroupName: group?.label || '' };
            this.showGroupModal = true;
        },

        closeGroupModal() {
            this.showGroupModal = false;
            this.groupModalTarget = null;
            this.groupModalAccount = null;
        },

        async submitGroupModal() {
            try {
                if (this.groupModalMode === 'rename') {
                    const newGroupName = this.groupModalForm.newGroupName?.trim();
                    if (!newGroupName) { this.notify('Enter a group name', 'error'); return; }
                    const group = this.groupModalTarget;
                    const res = await fetch(`${API_URL}/credentials/bulk/rename`, {
                        method: 'POST', headers: this.headers(),
                        body: JSON.stringify({ groupKey: group.key, groupName: group.groupName || group.label, newGroupName })
                    });
                    const data = await res.json();
                    if (data.success) { this.notify(data.message, 'success'); this.closeGroupModal(); await this.loadGrouped(); await this.loadGroupNameOptions(); }
                    else this.notify(data.message || 'Rename failed', 'error');
                    return;
                }

                const targetGroupName = this.groupModalForm.targetGroupName?.trim();
                if (!targetGroupName) { this.notify('Enter a target group', 'error'); return; }
                const group = this.groupModalTarget;
                const payload = { targetGroupName };
                if (this.groupModalAccount?._id) {
                    payload.accountIds = [this.groupModalAccount._id];
                } else if (group) {
                    payload.groupKey = group.key;
                    payload.groupName = group.groupName || group.label;
                }
                const res = await fetch(`${API_URL}/credentials/bulk/move`, {
                    method: 'POST', headers: this.headers(), body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) { this.notify(data.message, 'success'); this.closeGroupModal(); await this.loadGrouped(); await this.loadGroupNameOptions(); }
                else this.notify(data.message || 'Move failed', 'error');
            } catch (e) {
                this.notify('Action failed', 'error');
            }
        },

        async deleteGroup(group) {
            if (!confirm(`Permanently delete "${group.label}" and all ${group.total} account${group.total === 1 ? '' : 's'}? This cannot be undone.`)) return;
            try {
                const res = await fetch(`${API_URL}/credentials/bulk/delete`, {
                    method: 'POST', headers: this.headers(),
                    body: JSON.stringify({ groupKey: group.key, groupName: group.groupName || group.label })
                });
                const data = await res.json();
                if (data.success) { this.notify(data.message, 'success'); await this.loadGrouped(); await this.loadGroupNameOptions(); }
                else this.notify(data.message || 'Delete failed', 'error');
            } catch (e) {
                this.notify('Delete failed', 'error');
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

        openModal(vaultSection = null) {
            const defaultSection = vaultSection || (this.isMultimediaBrandRep || !this.showSocialMediaSection ? 'company_wide' : 'social_media');
            this.form = {
                groupName: '',
                accountName: '',
                vaultSection: defaultSection,
                category: defaultSection === 'social_media' ? 'social_media' : 'other',
                platform: '',
                username: '',
                password: '',
                twoFactorNotes: '',
                recoveryEmail: '',
                recoveryPhone: '',
                url: '',
                notes: '',
                status: 'archived',
                visibleToBrandReps: false
            };
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
            if (!confirm(`Delete "${item.accountName}" permanently?`)) return;
            const res = await fetch(`${API_URL}/credentials/${item._id}`, { method: 'DELETE', headers: this.headers() });
            const data = await res.json();
            if (data.success) { this.notify('Deleted', 'success'); this.closeModal(); await this.loadGrouped(); await this.loadGroupNameOptions(); }
            else this.notify(data.message || 'Delete failed', 'error');
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
        statusClass(status) {
            return status === 'archived'
                ? 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
                : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300';
        },
        statusLabel(status) { return status === 'archived' ? 'Archived' : 'Active'; },
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

<?php esirom_hub_mobile_nav('password_vault', 'site'); ?>
</body>
</html>
