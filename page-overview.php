<?php
/**
 * Template Name: Agency Overview
 * Template Post Type: page
 *
 * @package Esirom_Client_Hub
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Hide WordPress admin bar on this page
$esirom_overview_page_path = '/overview';
$esirom_ov_id = (int) get_queried_object_id();
if ($esirom_ov_id < 1 && function_exists('get_page_by_path')) {
    $p = get_page_by_path('overview');
    if ($p && ! is_wp_error($p) && ! empty($p->ID)) {
        $esirom_ov_id = (int) $p->ID;
    }
}
if ($esirom_ov_id > 0) {
    $ov_link = get_permalink($esirom_ov_id);
    if ($ov_link) {
        $ov_path = parse_url($ov_link, PHP_URL_PATH);
        if (is_string($ov_path) && $ov_path !== '') {
            $esirom_overview_page_path = rtrim($ov_path, '/');
        }
    }
}

show_admin_bar(false);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="h-full">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <title>Agency Overview | <?php bloginfo('name'); ?></title>
    <?php esirom_hub_head_assets(array('pwa' => true)); ?>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .ov-focus {
            -webkit-tap-highlight-color: transparent;
        }
        .ov-focus:focus-visible {
            outline: 2px solid rgba(99, 102, 241, 0.5);
            outline-offset: 2px;
        }
        <?php esirom_hub_layout_styles(); ?>

        /* ── Same modern design system as Workflow ── */
        @keyframes ov-fade-up {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .ov-card-appear { animation: ov-fade-up 0.25s ease forwards; }
        .ov-stat-card { position: relative; overflow: hidden; }
        .ov-stat-card::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 0.2s;
            background: radial-gradient(circle at top right, rgba(99,102,241,0.04), transparent 70%);
        }
        .ov-stat-card:hover::before { opacity: 1; }
        .ov-accentbar {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 3px;
            border-radius: 0 0 1rem 1rem;
            transition: height 0.15s ease;
        }
        .ov-stat-card:hover .ov-accentbar { height: 5px; }
        .ov-brand-card {
            position: relative;
            overflow: hidden;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .ov-brand-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px -4px rgba(0,0,0,0.08), 0 4px 8px -2px rgba(0,0,0,0.04);
        }
        .ov-brand-card::after {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 0.2s;
            background: radial-gradient(circle at top left, rgba(99,102,241,0.03), transparent 60%);
            pointer-events: none;
        }
        .ov-brand-card:hover::after { opacity: 1; }
        .ov-progress-bar {
            height: 6px;
            border-radius: 9999px;
            overflow: hidden;
            background: #f3f4f6;
        }
        .dark .ov-progress-bar { background: #374151; }
        .ov-progress-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
    <?php wp_head(); ?>
    <?php esirom_hub_wp_head_reset(); ?>
</head>
<body class="hub-has-mobile-nav h-full bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-white pb-16 md:pb-0" x-data="agencyOverview()" x-init="init()">
    <div class="hub-app-shell flex flex-col md:flex-row">
        <!-- Mobile Header -->
        <div class="md:hidden hub-mobile-header">
            <div class="flex items-center justify-between gap-2 w-full">
                <a href="<?php echo esc_url(esirom_hub_page_url('workflow')); ?>" class="hub-sidebar-brand" aria-label="Agency Hub home">
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/agencyhub-icon.png" alt="" class="h-8 w-8">
                    <span class="flex flex-col">
                        <span class="hub-sidebar-brand__name">Agency Overview</span>
                        <span class="hub-sidebar-brand__sub">Agency Hub</span>
                    </span>
                </a>
                <div class="flex items-center gap-1">
                    <!-- "Viewing as" switcher (mobile) — super admins only -->
                    <div x-show="isSuperAdmin" x-cloak class="relative" x-data="{ showRoleSwitcher: false }">
                        <button type="button" @click="showRoleSwitcher = !showRoleSwitcher" class="hub-btn hub-btn--ghost hub-btn--icon hub-btn--sm" :aria-expanded="showRoleSwitcher" aria-label="Switch view mode">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                        <div x-show="showRoleSwitcher" @click.away="showRoleSwitcher = false" x-cloak class="absolute right-0 mt-2 w-60 hub-card hub-card--elevated z-50 p-2">
                            <p class="hub-eyebrow px-2 pt-1 pb-2">Viewing as</p>
                            <button type="button" @click="switchViewMode('admin'); showRoleSwitcher = false" :class="viewMode === 'admin' ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">Admin view</button>
                            <button type="button" @click="switchViewMode('brand_rep'); showRoleSwitcher = false" :class="viewMode === 'brand_rep' ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">Brand rep view</button>
                            <button type="button" @click="switchViewMode('client'); showRoleSwitcher = false" :class="viewMode === 'client' ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">Client view</button>
                        </div>
                    </div>
                    <?php $hub_account_theme = true; include get_template_directory() . '/inc/hub-account-menu.php'; ?>
                </div>
            </div>
        </div>

        <aside :class="isSidebarOpen ? 'sidebar-expanded' : 'sidebar-collapsed'" class="hub-app-sidebar sidebar hidden md:flex border-r border-gray-200 dark:border-gray-700/50 flex-col flex-shrink-0">
            <?php esirom_hub_staff_sidebar_header(false); ?>
            <nav class="flex-1 px-3 py-4 space-y-3 overflow-y-auto" aria-label="Sections">
                <?php esirom_hub_client_sidebar_nav('', 'site', false); ?>
                <?php esirom_hub_staff_sidebar_nav('overview', 'site', false); ?>
            </nav>
            <div class="p-3 border-t border-gray-200 dark:border-gray-700/50">
                <?php esirom_hub_staff_sidebar_footer('site', false); ?>
            </div>
        </aside>

        <main class="hub-app-main mt-14 md:mt-0">
            <!-- Header / Command Bar -->
            <header class="hub-sticky-header hidden md:flex items-center justify-between px-4 lg:px-6 py-3 min-h-16 gap-4 flex-wrap">
                <!-- Left: title + period picker -->
                <div class="flex items-center gap-5 flex-wrap min-w-0">
                    <div class="min-w-0">
                        <h1 class="hub-page-title">Agency Overview</h1>
                        <p x-show="periodInfo && periodInfo.label" x-text="periodInfo.label" class="hub-page-subtitle"></p>
                    </div>
                    <!-- Period controls -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <input type="month" id="overview-month" x-model="selectedYearMonth" @change="onMonthRangeChange()" x-show="!rangeAll" x-cloak aria-label="Month"
                            class="hub-input !w-auto !py-1.5 text-xs" />
                        <div class="hub-segmented">
                            <button type="button" @click="setOverviewThisMonth()" class="hub-segmented__item">This month</button>
                            <button type="button" @click="setOverviewLastMonth()" class="hub-segmented__item">Last month</button>
                            <label class="hub-segmented__item cursor-pointer select-none" :class="rangeAll ? 'is-active' : ''">
                                <input type="checkbox" x-model="rangeAll" @change="onAllTimeChange()" class="sr-only" />
                                All time
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Right: view switcher + theme + user -->
                <div class="flex items-center gap-2">
                    <!-- "Viewing as" switcher — super admins only -->
                    <div x-show="isSuperAdmin" x-cloak class="relative" x-data="{ showRoleSwitcher: false }">
                        <button type="button" @click="showRoleSwitcher = !showRoleSwitcher" class="hub-btn hub-btn--soft hub-btn--sm" :aria-expanded="showRoleSwitcher">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span x-text="viewMode === 'admin' ? 'Admin view' : viewMode === 'brand_rep' ? 'Brand rep view' : 'Client view'"></span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="showRoleSwitcher" @click.away="showRoleSwitcher = false" x-cloak class="absolute right-0 mt-2 w-60 hub-card hub-card--elevated z-50 p-2">
                            <p class="hub-eyebrow px-2 pt-1 pb-2">Viewing as</p>
                            <button type="button" @click="switchViewMode('admin'); showRoleSwitcher = false" :class="viewMode === 'admin' ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">Admin view</button>
                            <button type="button" @click="switchViewMode('brand_rep'); showRoleSwitcher = false" :class="viewMode === 'brand_rep' ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">Brand rep view</button>
                            <button type="button" @click="switchViewMode('client'); showRoleSwitcher = false" :class="viewMode === 'client' ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">Client view</button>
                            <div x-show="viewMode === 'client'" class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                                <label for="overviewViewClient" class="hub-label px-2 !mb-1 !text-xs">View as brand</label>
                                <select id="overviewViewClient" x-model="selectedViewClient" @change="localStorage.setItem('selectedViewClient', selectedViewClient); loadData();" class="hub-select !py-1.5 text-sm">
                                    <option value="">All brands</option>
                                    <template x-for="brand in brands" :key="brand._id">
                                        <option :value="brand._id" x-text="brand.brandName || brand.name"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php $hub_account_theme = true; include get_template_directory() . '/inc/hub-account-menu.php'; ?>
                </div>
            </header>

            <!-- Loading State -->
            <div x-show="loading" class="flex items-center justify-center min-h-[16rem]">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-10 h-10 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-sm text-gray-400">Loading agency data…</p>
                </div>
            </div>

            <!-- Main Content -->
            <div x-show="!loading" x-cloak class="hub-page-content px-4 sm:px-6 lg:px-8 pt-5 pb-8 space-y-5">

                <!-- Period controls (mobile) -->
                <div class="md:hidden flex items-center gap-2 flex-wrap">
                    <input type="month" x-model="selectedYearMonth" @change="onMonthRangeChange()" x-show="!rangeAll" x-cloak aria-label="Month" class="hub-input !w-auto !py-1.5 text-xs" />
                    <div class="hub-segmented">
                        <button type="button" @click="setOverviewThisMonth()" class="hub-segmented__item">This month</button>
                        <button type="button" @click="setOverviewLastMonth()" class="hub-segmented__item">Last month</button>
                        <label class="hub-segmented__item cursor-pointer select-none" :class="rangeAll ? 'is-active' : ''">
                            <input type="checkbox" x-model="rangeAll" @change="onAllTimeChange()" class="sr-only" />
                            All time
                        </label>
                    </div>
                </div>

                <!-- ── Summary Stats ── -->
                <div class="ov-card-appear">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">At a Glance</span>
                        <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700"></div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">

                        <!-- Total Brands -->
                        <div class="ov-stat-card bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-100 dark:border-gray-700">
                            <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center mb-3">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <p class="text-2xl font-extrabold text-gray-900 dark:text-white tabular-nums leading-none" x-text="stats.totalBrands"></p>
                            <p class="text-xs text-gray-500 mt-1.5 font-medium">Total Brands</p>
                            <div class="ov-accentbar bg-indigo-400"></div>
                        </div>

                        <!-- Total Posts -->
                        <div class="ov-stat-card bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-100 dark:border-gray-700">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center mb-3">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="text-2xl font-extrabold text-emerald-600 tabular-nums leading-none" x-text="stats.totalPosts"></p>
                            <p class="text-xs text-gray-500 mt-1.5 font-medium">Total Posts</p>
                            <div class="ov-accentbar bg-emerald-400"></div>
                        </div>

                        <!-- Avg Posts/Brand -->
                        <div class="ov-stat-card bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-100 dark:border-gray-700">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center mb-3">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <p class="text-2xl font-extrabold text-blue-600 tabular-nums leading-none" x-text="stats.avgPostsPerBrand"></p>
                            <p class="text-xs text-gray-500 mt-1.5 font-medium">Avg / Brand</p>
                            <div class="ov-accentbar bg-blue-400"></div>
                        </div>

                        <!-- Posts Remaining + progress -->
                        <div class="ov-stat-card bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-100 dark:border-gray-700">
                            <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center mb-3">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10M4 18h10"/></svg>
                            </div>
                            <p class="text-2xl font-extrabold text-purple-600 tabular-nums leading-none" x-text="stats.totalRemainingPosts"></p>
                            <p class="text-xs text-gray-500 mt-1.5 font-medium">Posts Remaining</p>
                            <div class="mt-2 ov-progress-bar">
                                <div class="ov-progress-fill bg-purple-500" :style="`width:${Math.min(stats.overallProgress || 0, 100)}%`"></div>
                            </div>
                            <p class="text-xs text-gray-400 mt-1" x-text="`${stats.overallProgress}% of ${stats.totalRequiredPosts} target`"></p>
                            <div class="ov-accentbar bg-purple-400"></div>
                        </div>

                        <!-- Below Target -->
                        <div class="ov-stat-card bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-100 dark:border-gray-700">
                            <div class="w-8 h-8 rounded-xl bg-red-50 dark:bg-red-900/30 flex items-center justify-center mb-3 relative">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span x-show="stats.belowTarget > 0" class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-red-500 rounded-full animate-ping opacity-75"></span>
                                <span x-show="stats.belowTarget > 0" class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-red-500 rounded-full"></span>
                            </div>
                            <p class="text-2xl font-extrabold text-red-600 tabular-nums leading-none" x-text="stats.belowTarget"></p>
                            <p class="text-xs text-gray-500 mt-1.5 font-medium">Below Target</p>
                            <div class="ov-accentbar bg-red-400"></div>
                        </div>
                    </div>
                </div>

                <!-- ── Website Development ── -->
                <div x-show="websiteDevelopment && websiteDevelopment.projectCount > 0" class="ov-card-appear">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Website Development</span>
                            <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700 min-w-[2rem]"></div>
                        </div>
                        <a href="<?php echo esc_url(esirom_hub_page_url('website-projects')); ?>"
                           class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">Open Website Projects →</a>
                    </div>
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="ov-stat-card bg-white dark:bg-gray-800 rounded-2xl p-4 border border-amber-100 dark:border-amber-900/40 text-center">
                            <p class="text-2xl font-extrabold text-amber-600 tabular-nums" x-text="websiteDevelopment.totals?.pending ?? 0"></p>
                            <p class="text-xs text-gray-500 mt-1 font-medium">Pending</p>
                        </div>
                        <div class="ov-stat-card bg-white dark:bg-gray-800 rounded-2xl p-4 border border-blue-100 dark:border-blue-900/40 text-center">
                            <p class="text-2xl font-extrabold text-blue-600 tabular-nums" x-text="websiteDevelopment.totals?.outstanding ?? 0"></p>
                            <p class="text-xs text-gray-500 mt-1 font-medium">Outstanding</p>
                        </div>
                        <div class="ov-stat-card bg-white dark:bg-gray-800 rounded-2xl p-4 border border-green-100 dark:border-green-900/40 text-center">
                            <p class="text-2xl font-extrabold text-green-600 tabular-nums" x-text="websiteDevelopment.totals?.completed ?? 0"></p>
                            <p class="text-xs text-gray-500 mt-1 font-medium">Completed</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <template x-for="wp in (websiteDevelopment.projects || [])" :key="wp._id">
                            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4">
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <div class="min-w-0">
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white truncate" x-text="wp.title"></h3>
                                        <p class="text-xs text-gray-500 truncate">
                                            <span x-text="wp.clientName"></span>
                                            <span x-show="wp.isInternal" class="ml-1 text-purple-600 dark:text-purple-400">· Internal</span>
                                        </p>
                                    </div>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 capitalize shrink-0" x-text="(wp.status || '').replace(/_/g, ' ')"></span>
                                </div>
                                <div class="grid grid-cols-3 gap-2 text-center">
                                    <div><p class="text-lg font-bold text-amber-600 tabular-nums" x-text="wp.counts?.pending ?? 0"></p><p class="text-[10px] text-gray-400">Pending</p></div>
                                    <div><p class="text-lg font-bold text-blue-600 tabular-nums" x-text="wp.counts?.outstanding ?? 0"></p><p class="text-[10px] text-gray-400">Active</p></div>
                                    <div><p class="text-lg font-bold text-green-600 tabular-nums" x-text="wp.counts?.completed ?? 0"></p><p class="text-[10px] text-gray-400">Done</p></div>
                                </div>
                                <p x-show="wp.leadDeveloper" class="text-[10px] text-gray-500 dark:text-gray-400 mt-2" x-text="'Lead: ' + (typeof wp.leadDeveloper === 'object' ? ((wp.leadDeveloper?.fullName || ((wp.leadDeveloper?.firstName || '') + ' ' + (wp.leadDeveloper?.lastName || '')).trim()) || 'Unassigned') : wp.leadDeveloper)"></p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- ── Team Efficiency + Workload ── -->
                <div class="ov-card-appear">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Creative Team</span>
                        <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700"></div>
                        <a :href="WORKFLOW_URL" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">Open Workflow →</a>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <!-- Team Efficiency -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden flex flex-col">
                            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between gap-3 flex-wrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/40 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Team Efficiency</h3>
                                        <p class="text-xs text-gray-400">Completion rate per creative</p>
                                    </div>
                                </div>
                                <input type="month" :value="`${efficiencyYear}-${String(efficiencyMonth).padStart(2, '0')}`" @change="updateEfficiencyMonth($event.target.value)" class="px-2 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            </div>
                            <div class="p-4 space-y-2.5 overflow-y-auto max-h-80">
                                <template x-for="w in teamMonthlyEfficiency" :key="w.assignee?._id">
                                    <div @click="openWorkflowForAssignee(w.assignee?._id, true)" class="p-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-indigo-200 dark:hover:border-indigo-700/60 hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 cursor-pointer transition-all">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-xs font-bold text-indigo-700 dark:text-indigo-400 shrink-0" x-text="((w.assignee?.firstName || '?')[0] + (w.assignee?.lastName || '?')[0]).toUpperCase()"></div>
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="(w.assignee?.firstName || 'Unknown') + ' ' + (w.assignee?.lastName || '')"></p>
                                            </div>
                                            <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                                                  :class="w.completionRate >= 70 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' : (w.completionRate >= 40 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400')"
                                                  x-text="w.completionRate + '%'"></span>
                                        </div>
                                        <div class="h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-500"
                                                 :style="'width:' + w.completionRate + '%'"
                                                 :class="w.completionRate >= 70 ? 'bg-emerald-500' : (w.completionRate >= 40 ? 'bg-amber-500' : 'bg-red-500')"></div>
                                        </div>
                                        <div class="mt-1.5 flex items-center gap-3 text-xs text-gray-500">
                                            <span>Done <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="w.completedCount"></span></span>
                                            <span class="text-gray-300 dark:text-gray-600">·</span>
                                            <span>Assigned <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="w.assignedCount"></span></span>
                                            <span class="text-gray-300 dark:text-gray-600">·</span>
                                            <span class="text-blue-500">WIP <span class="font-semibold" x-text="w.inProgressCount"></span></span>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="teamMonthlyEfficiency.length === 0" class="py-10 text-center">
                                    <p class="text-sm text-gray-400">No assignments found for this month</p>
                                </div>
                            </div>
                        </div>

                        <!-- Workload Distribution -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden flex flex-col">
                            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-violet-50 dark:bg-violet-900/40 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Workload Distribution</h3>
                                        <p class="text-xs text-gray-400">Pending assigned concepts per creative</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 space-y-2 overflow-y-auto max-h-96">
                                <template x-for="w in workloadByAssignee" :key="w.assignee?._id">
                                    <div @click="openWorkflowForAssignee(w.assignee?._id, false)" class="cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/40 px-3 py-2.5 -mx-2 rounded-xl transition-colors">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-violet-100 dark:bg-violet-900/40 flex items-center justify-center text-xs font-bold text-violet-700 dark:text-violet-400 shrink-0" x-text="((w.assignee?.firstName || '?')[0] + (w.assignee?.lastName || '?')[0]).toUpperCase()"></div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="(w.assignee?.firstName || 'Unknown') + ' ' + (w.assignee?.lastName || '')"></p>
                                            </div>
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300 tabular-nums" x-text="w.pendingCount"></span>
                                        </div>
                                        <div class="h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden flex">
                                            <div class="h-full bg-gray-400"   :style="'width:' + (w.pendingCount ? (w.statusBreakdown?.draft || 0) / w.pendingCount * 100 : 0) + '%'"></div>
                                            <div class="h-full bg-blue-400"   :style="'width:' + (w.pendingCount ? (w.statusBreakdown?.in_progress || 0) / w.pendingCount * 100 : 0) + '%'"></div>
                                            <div class="h-full bg-amber-400"  :style="'width:' + (w.pendingCount ? (w.statusBreakdown?.pending_review || 0) / w.pendingCount * 100 : 0) + '%'"></div>
                                            <div class="h-full bg-purple-400" :style="'width:' + (w.pendingCount ? (w.statusBreakdown?.in_content_bank || 0) / w.pendingCount * 100 : 0) + '%'"></div>
                                            <div class="h-full bg-teal-400"   :style="'width:' + ((w.statusBreakdown?.client_approved || 0) / w.pendingCount * 100) + '%'"></div>
                                        </div>
                                        <div class="mt-1.5 flex flex-wrap gap-x-3 gap-y-0.5">
                                            <span x-show="w.statusBreakdown?.draft > 0" class="text-xs text-gray-400 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-gray-400 shrink-0"></span>Draft <span class="font-medium text-gray-600 dark:text-gray-300" x-text="w.statusBreakdown?.draft"></span></span>
                                            <span x-show="w.statusBreakdown?.in_progress > 0" class="text-xs text-gray-400 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-blue-400 shrink-0"></span>WIP <span class="font-medium text-gray-600 dark:text-gray-300" x-text="w.statusBreakdown?.in_progress"></span></span>
                                            <span x-show="w.statusBreakdown?.pending_review > 0" class="text-xs text-gray-400 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>Review <span class="font-medium text-gray-600 dark:text-gray-300" x-text="w.statusBreakdown?.pending_review"></span></span>
                                            <span x-show="w.statusBreakdown?.in_content_bank > 0" class="text-xs text-gray-400 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-purple-400 shrink-0"></span>Approved <span class="font-medium text-gray-600 dark:text-gray-300" x-text="w.statusBreakdown?.in_content_bank"></span></span>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="workloadByAssignee.length === 0" class="py-10 text-center">
                                    <p class="text-sm text-gray-400">No workload data yet</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── Search & Filter ── -->
                <div class="ov-card-appear flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                    <div class="relative flex-1 min-w-0">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <label class="sr-only" for="overview-brand-search">Search brands</label>
                        <input
                            id="overview-brand-search"
                            type="search"
                            x-model="searchQuery"
                            @input="filterBrands()"
                            placeholder="Search brands…"
                            autocomplete="off"
                            class="ov-focus w-full pl-9 pr-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition"
                        >
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <template x-for="opt in [{v:'all',l:'All'},{v:'on-track',l:'On Track'},{v:'below-target',l:'Below Target'},{v:'no-posts',l:'No Posts'}]" :key="opt.v">
                            <button type="button" @click="filterStatus = opt.v; filterBrands()"
                                    :class="filterStatus === opt.v
                                        ? 'bg-indigo-600 text-white shadow-sm'
                                        : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-indigo-300 dark:hover:border-indigo-600'"
                                    class="ov-focus px-3.5 py-2 rounded-xl text-xs font-semibold transition-all"
                                    x-text="opt.l">
                            </button>
                        </template>
                    </div>
                </div>

                <!-- ── Brand Cards Grid ── -->
                <div class="ov-card-appear grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <template x-for="brand in filteredBrands" :key="brand._id">
                        <div @click="navigateToBrand(brand._id)"
                             class="ov-brand-card bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 cursor-pointer overflow-hidden">

                            <!-- Top accent bar keyed to status -->
                            <div class="h-1"
                                 :class="{
                                     'bg-emerald-400': brand.status === 'on-track',
                                     'bg-amber-400':   brand.status === 'below-target',
                                     'bg-red-400':     brand.status === 'no-posts'
                                 }"></div>

                            <div class="p-5">
                                <!-- Brand Header -->
                                <div class="flex items-start justify-between gap-3 mb-4">
                                    <div class="min-w-0">
                                        <h3 class="text-base font-bold text-gray-900 dark:text-white truncate" x-text="brand.name"></h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate" x-text="brand.industry || '—'"></p>
                                    </div>
                                    <span class="shrink-0 px-2.5 py-1 text-xs font-semibold rounded-full"
                                          :class="{
                                              'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400': brand.status === 'on-track',
                                              'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400':         brand.status === 'below-target',
                                              'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400':                 brand.status === 'no-posts'
                                          }"
                                          x-text="brand.statusLabel"></span>
                                </div>

                                <!-- Monthly Post Progress -->
                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Monthly Posts</span>
                                        <span class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
                                            <span x-text="brand.postsThisMonth"></span><span class="text-gray-400 font-normal"> / </span><span x-text="brand.requiredPosts"></span>
                                        </span>
                                    </div>
                                    <div class="ov-progress-bar">
                                        <div class="ov-progress-fill"
                                             :class="{
                                                 'bg-emerald-500': brand.progress >= 100,
                                                 'bg-amber-500':   brand.progress >= 50 && brand.progress < 100,
                                                 'bg-red-500':     brand.progress < 50
                                             }"
                                             :style="`width:${Math.min(brand.progress, 100)}%`"></div>
                                    </div>
                                    <div class="flex items-center justify-between mt-1.5">
                                        <span class="text-xs text-gray-400" x-text="`${brand.progress}% complete`"></span>
                                        <span class="text-xs font-semibold"
                                              :class="brand.remainingPosts > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'"
                                              x-text="brand.remainingPosts > 0 ? brand.remainingPosts + ' remaining' : 'Target met ✓'"></span>
                                    </div>
                                </div>

                                <!-- Platform Breakdown -->
                                <div class="flex items-center gap-4 pt-3.5 border-t border-gray-100 dark:border-gray-700/60">
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-5 h-5 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                                            <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 tabular-nums" x-text="brand.platforms.facebook"></span>
                                    </div>
                                    <div class="flex items-center gap-1.5" :title="brand.igNeedsSync ? 'Instagram is linked but posts have not synced yet — run Sync Posts in Admin' : ''">
                                        <div class="w-5 h-5 rounded-full bg-pink-50 dark:bg-pink-900/30 flex items-center justify-center shrink-0">
                                            <svg class="w-3 h-3 text-pink-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                        </div>
                                        <span class="text-xs font-semibold tabular-nums"
                                              :class="brand.igNeedsSync ? 'text-amber-600 dark:text-amber-400' : 'text-gray-700 dark:text-gray-300'"
                                              x-text="brand.platforms.instagram"></span>
                                    </div>
                                    <div class="ml-auto flex items-center gap-1 text-indigo-600 dark:text-indigo-400">
                                        <span class="text-xs font-semibold">View</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </div>
                                </div>
                                <p x-show="brand.igNeedsSync" class="text-[10px] text-amber-600 dark:text-amber-400 mt-2">IG linked — run Sync Posts in Admin to import</p>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="!loading && filteredBrands.length === 0" class="ov-card-appear text-center py-16">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No brands found</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filter.</p>
                </div>

            </div><!-- /main content -->
        </main>
    </div>

    <script>
        const API_URL = typeof ESIROM_API_URL !== 'undefined' ? ESIROM_API_URL : <?php echo wp_json_encode(get_option('esirom_api_url', 'https://esirom-hub-backend-production.up.railway.app/api')); ?>;
        const DASHBOARD_URL = '<?php echo esc_url(esirom_hub_page_url('dashboard')); ?>';
        const LOGIN_URL = '<?php echo esc_url(esirom_hub_page_url('login')); ?>';
        const WORKFLOW_URL = '<?php echo esc_url(esirom_hub_page_url('workflow')); ?>';
        const OVERVIEW_PAGE_PATH = <?php echo wp_json_encode($esirom_overview_page_path, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;

        function agencyOverview() {
            return {
                loading: true,
                user: {},
                WORKFLOW_URL,
                // Provisional until /auth/me answers; HubAuth re-derives it from the real role in init().
                viewMode: (() => {
                    try {
                        const cachedUser = JSON.parse(localStorage.getItem('user') || '{}');
                        return window.HubAuth.resolveViewMode(window.HubAuth.readStoredViewMode(), cachedUser);
                    } catch (_err) {}
                    return 'admin';
                })(),
                get isSuperAdmin() { void this.user?.role; return window.HubAuth.isSuperAdmin(window.HubAuth.getUser() || this.user); }, // reads this.user for reactivity, decides on the verified /auth/me payload
                showPwModal: false,
                pwCurrent: '', pwNew: '', pwConfirm: '', pwLoading: false, pwError: '', pwSuccess: '',
                selectedViewClient: localStorage.getItem('selectedViewClient') || null,
                theme: localStorage.getItem('theme') || 'light',
                isSidebarOpen: true,
                toasts: [],
                currentDate: '',
                selectedYearMonth: '',
                rangeAll: false,
                periodInfo: { label: '' },
                searchQuery: '',
                filterStatus: 'all',
                brands: [],
                filteredBrands: [],
                stats: {
                    totalBrands: 0,
                    totalPosts: 0,
                    avgPostsPerBrand: 0,
                    belowTarget: 0,
                    totalRequiredPosts: 0,
                    totalRemainingPosts: 0,
                    overallProgress: 0
                },
                websiteDevelopment: {
                    projectCount: 0,
                    totals: { pending: 0, outstanding: 0, completed: 0, total: 0 },
                    projects: []
                },
                workloadByAssignee: [],
                teamMonthlyEfficiency: [],
                efficiencyYear: new Date().getFullYear(),
                efficiencyMonth: new Date().getMonth() + 1,

                showToast(message, type = 'info', duration = 3000) {
                    const id = Date.now();
                    this.toasts.push({ id, message, type });
                    setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, duration);
                },

                defaultYearMonth() {
                    const d = new Date();
                    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
                },

                async init() {
                    const authed = await this.checkAuth();
                    if (!authed) return;
                    this.applyTheme();
                    this.setCurrentDate();
                    if (!this.selectedYearMonth) this.selectedYearMonth = this.defaultYearMonth();
                    this.syncPeriodFromUrl();
                    window.addEventListener('popstate', () => {
                        this.syncPeriodFromUrl();
                        this.loadData();
                    });
                    await this.loadData();
                    this.replaceOverviewUrl();
                },

                async checkAuth() {
                    const token = localStorage.getItem('token');
                    if (!token) {
                        window.location.href = LOGIN_URL;
                        return;
                    }
                    try {
                        const res = await fetch(`${API_URL}/auth/me`, {
                            headers: { 'Authorization': `Bearer ${token}` }
                        });
                        if (res.status === 401 || res.status === 403) throw new Error('Auth failed');
                        if (!res.ok) {
                            // Server hiccup: keep the cached session rather than signing the user out.
                            this.user = JSON.parse(localStorage.getItem('user') || '{}') || {};
                            this.showToast('We could not verify your session. Showing your last known data.', 'error');
                        } else {
                            const data = await res.json();
                            this.user = data.user || {};
                            window.HubAuth.setUser(data.user);
                        }
                        if (this.user && Object.keys(this.user).length) {
                            localStorage.setItem('user', JSON.stringify(this.user));
                        }
                        // Derive the view mode from the verified role (cached session only if the backend hiccupped):
                        // only super admins keep a stored preview mode.
                        this.viewMode = window.HubAuth.applyViewMode(undefined, window.HubAuth.getUser() || this.user);
                    } catch (e) {
                        localStorage.removeItem('token');
                        localStorage.removeItem('user');
                        window.location.href = LOGIN_URL;
                        return false;
                    }
                    if (this.user?.role !== 'admin' && this.user?.role !== 'brand_rep') {
                        window.location.href = DASHBOARD_URL;
                        return false;
                    }
                    return true;
                },

                setCurrentDate() {
                    try {
                        const now = new Date();
                        this.currentDate = now.toLocaleDateString('en-US', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                    } catch (e) {
                        this.currentDate = '';
                    }
                },

                applyTheme() {
                    localStorage.setItem('theme', this.theme);
                    if (this.theme === 'dark') {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                },

                switchViewMode(mode) {
                    // Only verified super admins (per /auth/me) may preview another role.
                    if (!window.HubAuth.isSuperAdmin()) {
                        this.viewMode = window.HubAuth.applyViewMode(this.viewMode);
                        this.showToast('Only super admins can switch views.', 'error');
                        return;
                    }
                    mode = window.HubAuth.resolveViewMode(mode);
                    this.viewMode = window.HubAuth.persistViewMode(mode);
                    
                    // For client view, select first client if not already selected
                    if (mode === 'client' && !this.selectedViewClient && this.brands.length > 0) {
                        this.selectedViewClient = this.brands[0]._id;
                        localStorage.setItem('selectedViewClient', this.selectedViewClient);
                    } else if (mode !== 'client') {
                        this.selectedViewClient = null;
                        localStorage.removeItem('selectedViewClient');
                    }
                    
                    this.loadData();
                },

                toggleTheme() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    this.applyTheme();
                },

                syncPeriodFromUrl() {
                    try {
                        const sp = new URLSearchParams(window.location.search);
                        if (sp.get('range') === 'all' || sp.get('period') === 'all') {
                            this.rangeAll = true;
                            if (!this.selectedYearMonth) this.selectedYearMonth = this.defaultYearMonth();
                            return;
                        }
                        this.rangeAll = false;
                        const m = sp.get('month');
                        if (m && /^\d{4}-\d{2}$/.test(m)) {
                            this.selectedYearMonth = m;
                            return;
                        }
                        const p = sp.get('period');
                        if (p === 'last') {
                            const d = new Date();
                            d.setMonth(d.getMonth() - 1);
                            this.selectedYearMonth = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
                        } else {
                            this.selectedYearMonth = this.defaultYearMonth();
                        }
                    } catch (e) { /* ignore */ }
                },

                replaceOverviewUrl() {
                    const base = (typeof OVERVIEW_PAGE_PATH !== 'undefined' && OVERVIEW_PAGE_PATH)
                        ? String(OVERVIEW_PAGE_PATH).replace(/\/$/, '')
                        : '/overview';
                    const u = new URL(base, window.location.origin);
                    u.search = '';
                    if (this.rangeAll) {
                        u.searchParams.set('range', 'all');
                    } else {
                        u.searchParams.set('month', this.selectedYearMonth || this.defaultYearMonth());
                    }
                    history.replaceState({}, '', u.pathname + u.search);
                },

                onMonthRangeChange() {
                    this.rangeAll = false;
                    this.replaceOverviewUrl();
                    this.loadData();
                },

                onAllTimeChange() {
                    this.replaceOverviewUrl();
                    this.loadData();
                },

                setOverviewThisMonth() {
                    this.rangeAll = false;
                    this.selectedYearMonth = this.defaultYearMonth();
                    this.replaceOverviewUrl();
                    this.loadData();
                },

                setOverviewLastMonth() {
                    this.rangeAll = false;
                    const d = new Date();
                    d.setMonth(d.getMonth() - 1);
                    this.selectedYearMonth = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
                    this.replaceOverviewUrl();
                    this.loadData();
                },

                async loadData() {
                    this.loading = true;
                    try {
                        const token = localStorage.getItem('token');
                        const q = new URLSearchParams();
                        if (this.rangeAll) {
                            q.set('range', 'all');
                        } else {
                            q.set('month', this.selectedYearMonth || this.defaultYearMonth());
                        }
                        // Scope to one brand when an admin is previewing the client view
                        if (this.viewMode === 'client' && this.selectedViewClient) {
                            q.set('clientId', this.selectedViewClient);
                            q.set('viewAs', 'client');
                            q.set('viewAsClientId', this.selectedViewClient);
                        }

                        // Keep efficiency month aligned with overview month when not in All Time
                        if (!this.rangeAll && this.selectedYearMonth && this.selectedYearMonth.includes('-')) {
                            const [y, m] = this.selectedYearMonth.split('-').map(Number);
                            if (!Number.isNaN(y) && !Number.isNaN(m)) {
                                this.efficiencyYear = y;
                                this.efficiencyMonth = m;
                            }
                        }

                        const [overviewRes, workflowRes] = await Promise.all([
                            fetch(`${API_URL}/analytics/agency-overview?${q}`, {
                                headers: { 'Authorization': `Bearer ${token}` }
                            }),
                            fetch(`${API_URL}/workflow/dashboard?efficiencyYear=${this.efficiencyYear}&efficiencyMonth=${this.efficiencyMonth}`, {
                                headers: { 'Authorization': `Bearer ${token}` }
                            })
                        ]);

                        if (!overviewRes.ok) {
                            throw new Error('Failed to load data');
                        }

                        const data = await overviewRes.json();
                        this.brands = Array.isArray(data.brands) ? data.brands : [];
                        this.stats = {
                            totalBrands: 0,
                            totalPosts: 0,
                            avgPostsPerBrand: 0,
                            belowTarget: 0,
                            totalRequiredPosts: 0,
                            totalRemainingPosts: 0,
                            overallProgress: 0,
                            ...((data && data.stats) || {})
                        };
                        this.periodInfo = (data && data.period) ? data.period : { label: '' };
                        this.websiteDevelopment = data.websiteDevelopment || {
                            projectCount: 0,
                            totals: { pending: 0, outstanding: 0, completed: 0, total: 0 },
                            projects: []
                        };

                        if (workflowRes.ok) {
                            const wf = await workflowRes.json();
                            this.workloadByAssignee = wf.workloadByAssignee || [];
                            this.teamMonthlyEfficiency = wf.teamMonthlyEfficiency || [];
                        }

                        this.filterBrands();
                    } catch (error) {
                        console.error('Error loading data:', error);
                        this.showToast('Failed to load agency overview data', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                async updateEfficiencyMonth(monthValue) {
                    if (!monthValue || !monthValue.includes('-')) return;
                    const [year, month] = monthValue.split('-');
                    const parsedYear = parseInt(year, 10);
                    const parsedMonth = parseInt(month, 10);
                    if (Number.isNaN(parsedYear) || Number.isNaN(parsedMonth)) return;
                    this.efficiencyYear = parsedYear;
                    this.efficiencyMonth = parsedMonth;
                    try {
                        const token = localStorage.getItem('token');
                        const res = await fetch(`${API_URL}/workflow/dashboard?efficiencyYear=${this.efficiencyYear}&efficiencyMonth=${this.efficiencyMonth}`, {
                            headers: { 'Authorization': `Bearer ${token}` }
                        });
                        if (res.ok) {
                            const wf = await res.json();
                            this.teamMonthlyEfficiency = wf.teamMonthlyEfficiency || [];
                            this.workloadByAssignee = wf.workloadByAssignee || [];
                        }
                    } catch (e) {
                        console.error('Efficiency reload error:', e);
                    }
                },

                openWorkflowForAssignee(assigneeId, withMonthFilter = false) {
                    if (!assigneeId) return;
                    const params = new URLSearchParams({ tab: 'concepts', assignedTo: assigneeId });
                    if (withMonthFilter) {
                        params.set('assignedYear', String(this.efficiencyYear));
                        params.set('assignedMonth', String(this.efficiencyMonth));
                    }
                    window.location.href = `${WORKFLOW_URL}?${params.toString()}`;
                },

                filterBrands() {
                    const list = Array.isArray(this.brands) ? this.brands : [];
                    let filtered = list;

                    // Search filter
                    if (this.searchQuery) {
                        const query = this.searchQuery.toLowerCase();
                        filtered = filtered.filter((brand) => {
                            const n = (brand && brand.name) ? String(brand.name) : '';
                            const ind = (brand && brand.industry) ? String(brand.industry) : '';
                            return n.toLowerCase().includes(query) || ind.toLowerCase().includes(query);
                        });
                    }

                    // Status filter
                    if (this.filterStatus !== 'all') {
                        filtered = filtered.filter(brand => brand.status === this.filterStatus);
                    }

                    this.filteredBrands = filtered;
                },

                navigateToBrand(brandId) {
                    localStorage.setItem('selectedClientId', brandId);
                    localStorage.setItem('hubSelectedClientId', brandId);
                    window.location.href = DASHBOARD_URL;
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
                }
            }
        }
    </script>

    <!-- Mobile Bottom Navigation -->
    <?php esirom_hub_mobile_nav('overview', 'site'); ?>

    <?php include get_template_directory() . '/inc/hub-toasts.php'; ?>

    <?php include get_template_directory() . '/inc/change-password-modal.php'; ?>
</body>
</html>
