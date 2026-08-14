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
$admin_url = esc_url(get_permalink(get_page_by_path('admin')));
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
        const ADMIN_URL = '<?php echo esc_js($admin_url); ?>';
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
        <header class="hub-sticky-header bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700/50 px-4 sm:px-6 py-4 shadow-sm">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="min-w-0">
                    <h1 class="text-lg font-bold text-gray-900 dark:text-white">Ads</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Boost synced posts via Meta Marketing API</p>
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
            <div x-show="toast.show" x-cloak x-transition class="fixed bottom-20 md:bottom-6 right-4 z-[100] max-w-sm px-4 py-3 rounded-xl shadow-lg text-sm text-white" :class="toast.type === 'error' ? 'bg-red-600' : 'bg-emerald-600'" x-text="toast.message"></div>

            <div x-show="!selectedClient" class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-8 text-center text-sm text-gray-500">
                Select a brand to manage ads.
            </div>

            <template x-if="selectedClient">
                <div class="space-y-4">
                    <div x-show="!platforms.facebook?.connected" class="rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-4 text-sm text-amber-900 dark:text-amber-100">
                        Connect Facebook for this brand in <a href="<?php echo $admin_url; ?>" class="font-semibold underline">Admin</a> before boosting posts.
                    </div>

                    <div x-show="platforms.facebook?.connected && !platforms.instagram?.connected" class="rounded-xl border border-pink-200 dark:border-pink-800 bg-pink-50 dark:bg-pink-900/20 p-4 text-sm text-pink-900 dark:text-pink-100">
                        Instagram is not linked for this brand — only Facebook posts will appear. Link IG in <a href="<?php echo $admin_url; ?>" class="font-semibold underline">Admin</a>, then reopen Ads.
                    </div>

                    <!-- Ad account linking -->
                    <div x-show="platforms.facebook?.connected && (!adAccountId || editingAdAccount)" class="rounded-xl border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-900/20 p-4">
                        <p class="text-sm font-semibold text-indigo-900 dark:text-indigo-100 mb-2">Link a Meta ad account</p>
                        <p class="text-xs text-indigo-800/80 dark:text-indigo-200/80 mb-3">
                            Paste the Ad Account ID from Meta Ads Manager (e.g. <code class="text-[11px]">act_123456789</code>), or pick from the list if Facebook was reconnected with <code class="text-[11px]">ads_management</code>.
                        </p>
                        <div x-show="adsError" class="text-xs text-red-600 dark:text-red-400 mb-2" x-text="adsError"></div>

                        <div class="space-y-3">
                            <div class="flex flex-col sm:flex-row gap-2">
                                <input type="text"
                                       x-model="manualAdAccountId"
                                       placeholder="act_123456789"
                                       class="flex-1 rounded-lg border border-indigo-200 dark:border-indigo-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm font-mono">
                                <input type="text"
                                       x-model="manualAdAccountName"
                                       placeholder="Optional name"
                                       class="sm:w-48 rounded-lg border border-indigo-200 dark:border-indigo-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm">
                                <button @click="saveManualAdAccount()" :disabled="!manualAdAccountId || savingAdAccount" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold disabled:opacity-60">
                                    <span x-show="!savingAdAccount">Save ID</span>
                                    <span x-show="savingAdAccount">Saving…</span>
                                </button>
                            </div>

                            <div x-show="adAccounts.length > 0" class="flex flex-col sm:flex-row gap-2 pt-1 border-t border-indigo-200/60 dark:border-indigo-800/60">
                                <select x-model="selectedAdAccountId" class="flex-1 rounded-lg border border-indigo-200 dark:border-indigo-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm">
                                    <option value="">Select from linked accounts…</option>
                                    <template x-for="account in adAccounts" :key="account.id">
                                        <option :value="account.id" x-text="account.name + ' (' + account.id + ')'"></option>
                                    </template>
                                </select>
                                <button @click="saveAdAccount()" :disabled="!selectedAdAccountId || savingAdAccount" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold disabled:opacity-60">Save selection</button>
                                <button @click="loadAdAccounts()" class="px-4 py-2 rounded-lg border border-indigo-200 dark:border-indigo-700 text-sm font-semibold text-indigo-700 dark:text-indigo-300">Refresh list</button>
                            </div>
                            <div x-show="adAccounts.length === 0" class="flex gap-2">
                                <button @click="loadAdAccounts()" class="px-4 py-2 rounded-lg border border-indigo-200 dark:border-indigo-700 text-sm font-semibold text-indigo-700 dark:text-indigo-300">Try load account list</button>
                            </div>
                        </div>
                    </div>

                    <div x-show="adAccountId && !editingAdAccount" class="rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 px-4 py-3 flex flex-wrap items-center justify-between gap-2 text-sm">
                        <div>
                            <span class="font-semibold text-emerald-900 dark:text-emerald-100">Ad account:</span>
                            <span class="text-emerald-800 dark:text-emerald-200 ml-1" x-text="adAccountName || ('act_' + adAccountId)"></span>
                        </div>
                        <button @click="editingAdAccount = true; selectedAdAccountId = adAccountId; manualAdAccountId = adAccountId ? ('act_' + adAccountId) : ''; manualAdAccountName = adAccountName || ''" class="text-xs font-semibold text-emerald-700 dark:text-emerald-300 underline">Change</button>
                    </div>

                    <!-- Budget caps -->
                    <div x-show="platforms.facebook?.connected" class="rounded-xl border dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">Boost budget caps</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Month used: $<span x-text="fmtMoney(adsBudget.monthSpendUsd)"></span>
                                    <span x-show="adsBudget.maxMonthlyUsd != null"> / $<span x-text="fmtMoney(adsBudget.maxMonthlyUsd)"></span></span>
                                </p>
                            </div>
                            <button @click="saveBudgetCaps()" :disabled="savingBudget" class="px-3 py-1.5 rounded-lg bg-gray-900 dark:bg-indigo-600 text-white text-xs font-semibold disabled:opacity-60">
                                <span x-show="!savingBudget">Save caps</span>
                                <span x-show="savingBudget">Saving…</span>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Max budget / month (USD)</label>
                                <input type="number" min="0" step="1" x-model.number="budgetForm.maxMonthlyUsd" placeholder="e.g. 500" class="w-full rounded-lg border dark:border-gray-600 dark:bg-gray-700 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Max boost / day (USD)</label>
                                <input type="number" min="0" step="1" x-model.number="budgetForm.maxDailyBoostUsd" placeholder="e.g. 25" class="w-full rounded-lg border dark:border-gray-600 dark:bg-gray-700 px-3 py-2 text-sm">
                            </div>
                        </div>
                        <p class="text-[11px] text-gray-500 mt-2">Leave blank for no cap. Monthly check uses estimated boost spend (daily × days) for boosts created this month.</p>
                    </div>
                </div>
            </template>

            <!-- Boost tab -->
            <div x-show="selectedClient && tab === 'boost'" x-cloak class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex gap-1 bg-gray-100 dark:bg-gray-800 rounded-xl p-1">
                        <button @click="platformFilter = 'all'; loadAds()" class="px-3 py-1.5 text-xs font-semibold rounded-lg" :class="platformFilter === 'all' ? 'bg-white dark:bg-gray-700 shadow-sm' : 'text-gray-500'">All</button>
                        <button @click="platformFilter = 'facebook'; loadAds()" class="px-3 py-1.5 text-xs font-semibold rounded-lg" :class="platformFilter === 'facebook' ? 'bg-white dark:bg-gray-700 shadow-sm' : 'text-gray-500'">Facebook</button>
                        <button @click="platformFilter = 'instagram'; loadAds()" class="px-3 py-1.5 text-xs font-semibold rounded-lg" :class="platformFilter === 'instagram' ? 'bg-white dark:bg-gray-700 shadow-sm' : 'text-gray-500'">Instagram</button>
                    </div>
                    <p class="text-xs text-gray-500" x-text="postCountsLabel()"></p>
                </div>

                <div x-show="loading" class="flex justify-center py-16">
                    <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                </div>

                <div x-show="!loading && boostablePosts.length === 0" class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-10 text-center text-sm text-gray-500">
                    No boostable posts found for this filter. Sync published Facebook or Instagram posts from Admin, and ensure Instagram is linked.
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
                                          :class="post.platform === 'instagram' ? 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'"
                                          x-text="post.platform"></span>
                                    <span x-show="post.isPaid" class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">Boosted</span>
                                </div>
                                <p class="text-sm mt-2 line-clamp-2" x-text="post.text || '(No caption)'"></p>
                                <p class="text-xs text-gray-500 mt-1" x-text="fmtDate(post.publishedDate) + ' · Reach ' + fmtNum(post.reach) + ' · Engagement ' + fmtNum(post.engagement)"></p>
                                <p x-show="post.boostMeta?.boostedAt" class="text-xs text-emerald-600 dark:text-emerald-400 mt-1" x-text="'Last boost · $' + (post.boostMeta?.dailyBudget || 0) + '/day · ' + (post.boostMeta?.status || '')"></p>
                            </div>
                            <div class="flex sm:flex-col items-end justify-center gap-2 flex-shrink-0">
                                <button @click="openBoostModal(post)"
                                        :disabled="!adAccountId || boostingPostId === post._id"
                                        class="px-4 py-2 text-xs font-semibold rounded-xl text-white disabled:opacity-50 disabled:cursor-not-allowed"
                                        :class="adAccountId ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-400'"
                                        :title="adAccountId ? 'Boost this post' : 'Link an ad account first'">
                                    <span x-show="boostingPostId !== post._id">Boost Post</span>
                                    <span x-show="boostingPostId === post._id">Boosting…</span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Campaigns tab -->
            <div x-show="selectedClient && tab === 'campaigns'" x-cloak class="space-y-4">
                <div x-show="!adAccountId" class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-8 text-center text-sm text-gray-500">
                    Link a Meta ad account to view campaigns.
                </div>

                <div x-show="adAccountId && loading" class="flex justify-center py-16">
                    <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                </div>

                <div x-show="adAccountId && !loading && campaigns.length === 0" class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-10 text-center text-sm text-gray-500">
                    No campaigns in this ad account yet. Boost a post to create one.
                </div>

                <div x-show="adAccountId && !loading && campaigns.length > 0" class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 overflow-hidden">
                    <div class="divide-y dark:divide-gray-700">
                        <template x-for="campaign in campaigns" :key="campaign.id">
                            <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-semibold text-sm truncate" x-text="campaign.name"></p>
                                    <p class="text-xs text-gray-500 mt-1" x-text="campaign.objective + ' · Created ' + fmtDate(campaign.createdTime)"></p>
                                </div>
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <span class="text-xs font-bold uppercase px-2 py-1 rounded-full"
                                          :class="campaignStatusClass(campaign.status)"
                                          x-text="campaign.status"></span>
                                    <span x-show="campaign.dailyBudget" class="text-xs text-gray-600 dark:text-gray-400" x-text="'$' + campaign.dailyBudget + '/day'"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Boost modal -->
<div x-show="showBoostModal" x-cloak class="fixed inset-0 z-[120] flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="absolute inset-0 bg-black/50" @click="closeBoostModal()"></div>
    <div class="relative w-full sm:max-w-lg bg-white dark:bg-gray-800 rounded-t-2xl sm:rounded-2xl shadow-2xl max-h-[92vh] overflow-y-auto" @click.stop>
        <div class="p-5 border-b dark:border-gray-700">
            <h3 class="text-lg font-bold">Boost post</h3>
            <p class="text-xs text-gray-500 mt-1 line-clamp-2" x-text="boostForm.post?.text || ''"></p>
        </div>
        <div class="p-5 space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Daily budget (USD)</label>
                    <input type="number" min="1" step="1" x-model.number="boostForm.dailyBudget" class="w-full rounded-lg border dark:border-gray-600 dark:bg-gray-700 px-3 py-2 text-sm">
                    <p x-show="adsBudget.maxDailyBoostUsd != null" class="text-[11px] text-gray-500 mt-1" x-text="'Max $' + adsBudget.maxDailyBoostUsd + '/day for this brand'"></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Duration (days)</label>
                    <input type="number" min="1" max="30" step="1" x-model.number="boostForm.durationDays" class="w-full rounded-lg border dark:border-gray-600 dark:bg-gray-700 px-3 py-2 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Objective</label>
                <select x-model="boostForm.objective" class="w-full rounded-lg border dark:border-gray-600 dark:bg-gray-700 px-3 py-2 text-sm">
                    <option value="ENGAGEMENT">Engagement</option>
                    <option value="REACH">Reach</option>
                    <option value="TRAFFIC">Traffic</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Countries (ISO codes, comma-separated)</label>
                <input type="text" x-model="boostForm.countries" placeholder="JM, US, BB" class="w-full rounded-lg border dark:border-gray-600 dark:bg-gray-700 px-3 py-2 text-sm">
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" x-model="boostForm.launchActive" class="rounded text-indigo-600">
                <span>Launch immediately (Active in Ads Manager)</span>
            </label>
            <p class="text-xs text-gray-500">Boosts are billed by Meta to the linked ad account. Payment method must be set up in Meta Business Suite.</p>
        </div>
        <div class="p-5 border-t dark:border-gray-700 flex justify-end gap-2">
            <button @click="closeBoostModal()" class="px-4 py-2 rounded-lg text-sm font-semibold bg-gray-100 dark:bg-gray-700">Cancel</button>
            <button @click="submitBoost()" :disabled="boosting" class="px-4 py-2 rounded-lg text-sm font-semibold bg-indigo-600 text-white disabled:opacity-60">
                <span x-show="!boosting">Create boost</span>
                <span x-show="boosting">Creating…</span>
            </button>
        </div>
    </div>
</div>

<script>
function adsApp() {
    return {
        authChecked: false,
        user: null,
        viewMode: localStorage.getItem('viewMode') || 'admin',
        isSidebarOpen: true,
        loading: false,
        tab: 'boost',
        platformFilter: 'all',
        clients: [],
        selectedClient: null,
        selectedClientName: '',
        boostablePosts: [],
        campaigns: [],
        platforms: {},
        adAccounts: [],
        adAccountId: null,
        adAccountName: '',
        selectedAdAccountId: '',
        manualAdAccountId: '',
        manualAdAccountName: '',
        editingAdAccount: false,
        savingAdAccount: false,
        savingBudget: false,
        adsError: null,
        adsBudget: { maxMonthlyUsd: null, maxDailyBoostUsd: null, monthSpendUsd: 0 },
        budgetForm: { maxMonthlyUsd: null, maxDailyBoostUsd: null },
        showBoostModal: false,
        boosting: false,
        boostingPostId: null,
        boostForm: {
            post: null,
            dailyBudget: 10,
            durationDays: 7,
            objective: 'ENGAGEMENT',
            countries: 'JM',
            launchActive: true
        },
        toast: { show: false, message: '', type: 'success' },

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
                if (this.user.role === 'brand_rep') this.viewMode = 'brand_rep';
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

        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => { this.toast.show = false; }, 5000);
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
            this.platformFilter = 'all';
            await this.loadAds();
        },

        async loadAds() {
            if (!this.selectedClient) return;
            this.loading = true;
            try {
                const qs = this.platformFilter && this.platformFilter !== 'all' ? `?platform=${this.platformFilter}` : '';
                const res = await fetch(`${API_URL}/social-media/ads/${this.selectedClient}${qs}`, { headers: this.headers() });
                const data = await res.json();
                if (data.success) {
                    this.boostablePosts = data.data.boostablePosts || [];
                    this.campaigns = data.data.campaigns || [];
                    this.platforms = data.data.platforms || {};
                    this.adAccounts = data.data.adAccounts || [];
                    this.adsError = data.data.adsError || null;
                    this.adAccountId = data.data.platforms?.facebook?.adAccountId || null;
                    this.adAccountName = data.data.platforms?.facebook?.adAccountName || '';
                    this.selectedAdAccountId = this.adAccountId || '';
                    this.adsBudget = data.data.adsBudget || { maxMonthlyUsd: null, maxDailyBoostUsd: null, monthSpendUsd: 0 };
                    this.budgetForm = {
                        maxMonthlyUsd: this.adsBudget.maxMonthlyUsd,
                        maxDailyBoostUsd: this.adsBudget.maxDailyBoostUsd
                    };
                    if (!this.manualAdAccountId && this.adAccountId) {
                        this.manualAdAccountId = 'act_' + this.adAccountId;
                        this.manualAdAccountName = this.adAccountName || '';
                    }
                }
            } finally {
                this.loading = false;
            }
        },

        postCountsLabel() {
            const fb = this.boostablePosts.filter(p => p.platform === 'facebook').length;
            const ig = this.boostablePosts.filter(p => p.platform === 'instagram').length;
            if (this.platformFilter === 'facebook') return `${fb} Facebook post${fb === 1 ? '' : 's'}`;
            if (this.platformFilter === 'instagram') return `${ig} Instagram post${ig === 1 ? '' : 's'}`;
            return `${this.boostablePosts.length} posts · ${fb} FB · ${ig} IG`;
        },

        async loadAdAccounts() {
            if (!this.selectedClient) return;
            try {
                const res = await fetch(`${API_URL}/social-media/ads/${this.selectedClient}/ad-accounts`, { headers: this.headers() });
                const data = await res.json();
                if (data.success) {
                    this.adAccounts = data.data || [];
                    this.adsError = null;
                    if (!this.adAccounts.length) {
                        this.showToast('No ad accounts returned — paste your act_ ID instead', 'error');
                    }
                } else {
                    this.adsError = data.message;
                    this.showToast(data.message, 'error');
                }
            } catch (e) {
                this.showToast('Could not load ad accounts', 'error');
            }
        },

        async saveAdAccount() {
            if (!this.selectedClient || !this.selectedAdAccountId) return;
            this.savingAdAccount = true;
            try {
                const account = this.adAccounts.find(a => a.id === this.selectedAdAccountId);
                const res = await fetch(`${API_URL}/social-media/ads/${this.selectedClient}/ad-account`, {
                    method: 'PUT',
                    headers: this.headers(),
                    body: JSON.stringify({
                        adAccountId: this.selectedAdAccountId,
                        adAccountName: account?.name || ''
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.adAccountId = data.data.adAccountId;
                    this.adAccountName = data.data.adAccountName;
                    this.editingAdAccount = false;
                    this.showToast('Ad account linked');
                    await this.loadAds();
                } else {
                    this.showToast(data.message || 'Failed to save ad account', 'error');
                }
            } finally {
                this.savingAdAccount = false;
            }
        },

        async saveManualAdAccount() {
            if (!this.selectedClient || !this.manualAdAccountId) return;
            this.savingAdAccount = true;
            try {
                const res = await fetch(`${API_URL}/social-media/ads/${this.selectedClient}/ad-account`, {
                    method: 'PUT',
                    headers: this.headers(),
                    body: JSON.stringify({
                        adAccountId: this.manualAdAccountId.trim(),
                        adAccountName: (this.manualAdAccountName || '').trim()
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.adAccountId = data.data.adAccountId;
                    this.adAccountName = data.data.adAccountName;
                    this.editingAdAccount = false;
                    this.showToast('Ad account linked');
                    await this.loadAds();
                } else {
                    this.showToast(data.message || 'Failed to save ad account', 'error');
                }
            } finally {
                this.savingAdAccount = false;
            }
        },

        async saveBudgetCaps() {
            if (!this.selectedClient) return;
            this.savingBudget = true;
            try {
                const res = await fetch(`${API_URL}/social-media/ads/${this.selectedClient}/budget`, {
                    method: 'PUT',
                    headers: this.headers(),
                    body: JSON.stringify({
                        maxMonthlyUsd: this.budgetForm.maxMonthlyUsd === '' || this.budgetForm.maxMonthlyUsd == null
                            ? null
                            : this.budgetForm.maxMonthlyUsd,
                        maxDailyBoostUsd: this.budgetForm.maxDailyBoostUsd === '' || this.budgetForm.maxDailyBoostUsd == null
                            ? null
                            : this.budgetForm.maxDailyBoostUsd
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.adsBudget = { ...this.adsBudget, ...data.data };
                    this.budgetForm = {
                        maxMonthlyUsd: data.data.maxMonthlyUsd,
                        maxDailyBoostUsd: data.data.maxDailyBoostUsd
                    };
                    this.showToast('Budget caps saved');
                } else {
                    this.showToast(data.message || 'Failed to save budget caps', 'error');
                }
            } catch (e) {
                this.showToast('Failed to save budget caps', 'error');
            } finally {
                this.savingBudget = false;
            }
        },

        openBoostModal(post) {
            if (!this.adAccountId) return;
            const maxDaily = this.adsBudget.maxDailyBoostUsd;
            let daily = 10;
            if (maxDaily != null && maxDaily > 0) daily = Math.min(daily, Number(maxDaily));
            this.boostForm.post = post;
            this.boostForm.dailyBudget = daily;
            this.boostForm.durationDays = 7;
            this.boostForm.objective = 'ENGAGEMENT';
            this.boostForm.countries = 'JM';
            this.boostForm.launchActive = true;
            this.showBoostModal = true;
        },

        closeBoostModal() {
            this.showBoostModal = false;
            this.boostForm.post = null;
        },

        async submitBoost() {
            if (!this.selectedClient || !this.boostForm.post) return;
            if (this.adsBudget.maxDailyBoostUsd != null && this.boostForm.dailyBudget > this.adsBudget.maxDailyBoostUsd) {
                this.showToast(`Daily budget cannot exceed $${this.adsBudget.maxDailyBoostUsd}`, 'error');
                return;
            }
            this.boosting = true;
            this.boostingPostId = this.boostForm.post._id;
            try {
                const res = await fetch(`${API_URL}/social-media/ads/${this.selectedClient}/boost`, {
                    method: 'POST',
                    headers: this.headers(),
                    body: JSON.stringify({
                        postId: this.boostForm.post._id,
                        dailyBudget: this.boostForm.dailyBudget,
                        durationDays: this.boostForm.durationDays,
                        objective: this.boostForm.objective,
                        countries: this.boostForm.countries,
                        launchActive: this.boostForm.launchActive
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.showToast('Boost created in Meta Ads Manager');
                    this.closeBoostModal();
                    await this.loadAds();
                } else {
                    this.showToast(data.message || 'Boost failed', 'error');
                }
            } catch (e) {
                this.showToast('Boost request failed', 'error');
            } finally {
                this.boosting = false;
                this.boostingPostId = null;
            }
        },

        campaignStatusClass(status) {
            const s = String(status || '').toUpperCase();
            if (s === 'ACTIVE') return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300';
            if (s === 'PAUSED') return 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';
            return 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300';
        },

        fmtDate(value) {
            if (!value) return '—';
            return new Date(value).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
        },

        fmtNum(value) {
            const n = Number(value) || 0;
            if (n >= 1000) return (n / 1000).toFixed(1) + 'K';
            return n.toString();
        },

        fmtMoney(value) {
            const n = Number(value);
            if (!Number.isFinite(n)) return '0';
            return n % 1 === 0 ? String(n) : n.toFixed(2);
        },

        logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = LOGIN_URL;
        }
    };
}
</script>

<?php esirom_hub_staff_mobile_nav('ads'); ?>
</body>
</html>
