<?php
/**
 * Template Name: My Progress
 */

if (!defined('ABSPATH')) exit;

show_admin_bar(false);

$api_url = get_option('esirom_api_url', 'https://esirom-hub-backend-production.up.railway.app/api');
$website_projects_url = esc_url(esirom_hub_page_url('website-projects'));
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <title>My Progress — Agency Hub</title>
    <?php esirom_hub_head_assets(array('pwa' => true)); ?>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        const API_URL = '<?php echo esc_js($api_url); ?>';
        const LOGIN_URL = '<?php echo esc_js(esirom_hub_page_url('login')); ?>';
    </script>
    <style>
        .ring-score { transition: stroke-dashoffset 0.8s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.3s ease forwards; }
        <?php esirom_hub_layout_styles(); ?>
    </style>
</head>
<body class="hub-has-mobile-nav h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 pb-16 md:pb-0" x-data="progressApp()" x-init="init()">

<!-- Auth guard -->
<div x-show="!authChecked" class="hub-boot" aria-busy="true">
    <span class="hub-spinner hub-spinner--lg"></span>
    <span>Loading…</span>
</div>

<div x-show="authChecked && !user" x-cloak class="hub-boot">
    <div class="hub-card hub-card--elevated p-8 text-center max-w-sm w-full mx-4">
        <p class="hub-empty__title">You need to sign in</p>
        <p class="hub-empty__text mt-1">Your session has ended or could not be verified.</p>
        <a href="<?php echo esc_url(esirom_hub_page_url('login')); ?>" class="hub-btn hub-btn--primary hub-btn--block mt-5">Go to sign in</a>
    </div>
</div>

<div x-show="authChecked && user" x-cloak class="hub-app-shell flex flex-col md:flex-row">

    <!-- Sidebar -->
    <aside :class="isSidebarOpen ? 'sidebar-expanded' : 'sidebar-collapsed'" class="hub-app-sidebar sidebar hidden md:flex border-r border-gray-200 dark:border-gray-700/50 flex-col flex-shrink-0">
        <?php esirom_hub_staff_sidebar_header(false); ?>

        <nav class="flex-1 px-3 py-4 space-y-3 overflow-y-auto" aria-label="Sections">
            <?php esirom_hub_staff_sidebar_nav('progress', 'site', false); ?>
        </nav>

        <div class="p-3 border-t border-gray-200 dark:border-gray-700/50">
            <?php esirom_hub_staff_sidebar_footer('site', false); ?>
        </div>
    </aside>

    <!-- Main -->
    <main class="hub-app-main">

        <!-- Top bar -->
        <header class="hub-sticky-header px-4 sm:px-6 py-3 flex items-center justify-between gap-4 flex-wrap">
            <div class="min-w-0">
                <h1 class="hub-page-title">My Progress</h1>
                <p class="hub-page-subtitle" x-text="periodLabel"></p>
            </div>

            <!-- Controls -->
            <div class="flex items-center gap-3 flex-wrap">
                <!-- Period Type -->
                <div class="hub-segmented" role="tablist" aria-label="Period type">
                    <template x-for="pt in ['month','quarter','year']" :key="pt">
                        <button type="button" role="tab" @click="setPeriodType(pt)" :aria-selected="periodType === pt"
                                class="hub-segmented__item" :class="periodType === pt ? 'is-active' : ''"
                                x-text="pt === 'month' ? 'Month' : pt === 'quarter' ? 'Quarter' : 'Year'"></button>
                    </template>
                </div>

                <!-- Year picker -->
                <select x-model="selectedYear" @change="loadData()" class="hub-select !w-auto !py-1.5 text-xs" aria-label="Year">
                    <template x-for="y in yearOptions" :key="y">
                        <option :value="y" x-text="y"></option>
                    </template>
                </select>

                <!-- Month picker (month mode only) -->
                <select x-show="periodType === 'month'" x-model.number="selectedMonth" @change="loadData()" class="hub-select !w-auto !py-1.5 text-xs" aria-label="Month">
                    <template x-for="(name, idx) in monthNames" :key="idx">
                        <option :value="idx" x-text="name"></option>
                    </template>
                </select>

                <!-- View toggle for managers/admins -->
                <div x-show="canViewTeam" class="hub-segmented" role="tablist" aria-label="View">
                    <button type="button" role="tab" @click="view = 'personal'; loadData()" :aria-selected="view === 'personal'" class="hub-segmented__item" :class="view === 'personal' ? 'is-active' : ''">My stats</button>
                    <button type="button" role="tab" @click="view = 'team'; loadData()" :aria-selected="view === 'team'" class="hub-segmented__item" :class="view === 'team' ? 'is-active' : ''">Team view</button>
                </div>

                <?php $hub_account_theme = true; include get_template_directory() . '/inc/hub-account-menu.php'; ?>
            </div>
        </header>

        <div class="hub-page-content p-4 sm:p-6 space-y-6">

            <!-- Loading state -->
            <div x-show="loading" class="flex items-center justify-center py-24">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-10 h-10 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Loading performance data...</p>
                </div>
            </div>

            <!-- No department assigned -->
            <div x-show="!loading && view === 'personal' && myData && !myData.department" class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-xl p-6 text-center">
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/40 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="font-semibold text-amber-800 dark:text-amber-300 mb-1" x-text="user?.role === 'admin' ? 'Agency oversight' : 'Department not set'"></h3>
                <p class="text-sm text-amber-700 dark:text-amber-400" x-show="user?.role === 'admin'">
                    You are not assigned to a department. Use <button type="button" @click="view = 'team'; loadData()" class="font-semibold underline text-amber-800 dark:text-amber-200">Team View</button> to see performance across all divisions and team members.
                </p>
                <p class="text-sm text-amber-700 dark:text-amber-400" x-show="user?.role !== 'admin'">
                    Your department hasn't been assigned yet. Ask an admin to set it in the Admin Panel so your progress goals can be tracked.
                </p>
            </div>

            <!-- PERSONAL VIEW -->
            <template x-if="!loading && view === 'personal' && myData && myData.department">
            <div class="space-y-6 animate-fade-in">

                <!-- Hero score card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                        <!-- Score ring -->
                        <div class="relative flex-shrink-0">
                            <svg class="w-32 h-32 -rotate-90" viewBox="0 0 120 120">
                                <circle cx="60" cy="60" r="50" fill="none" stroke="currentColor" class="text-gray-100 dark:text-gray-700" stroke-width="12"/>
                                <circle cx="60" cy="60" r="50" fill="none"
                                        :stroke="scoreRingColor"
                                        stroke-width="12"
                                        stroke-linecap="round"
                                        :stroke-dasharray="314"
                                        :stroke-dashoffset="myData.score !== null ? 314 - (314 * Math.min(myData.score, 100) / 100) : 314"
                                        class="ring-score"/>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-3xl font-extrabold text-gray-900 dark:text-white" x-text="myData.score !== null ? myData.score + '%' : '—'"></span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Score</span>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                      :class="{
                                          'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300': myData.department === 'web_developer',
                                          'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300': myData.department === 'graphic_designer',
                                          'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300': myData.department === 'social_media_exec',
                                          'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300': myData.department === 'multimedia'
                                      }"
                                      x-text="myData.departmentLabel"></span>
                                <span x-show="myData.isManager" class="text-xs font-semibold px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">Manager</span>
                                <span x-show="myData.multimediaRole" class="text-xs font-medium px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 capitalize" x-text="(myData.multimediaRole || '').replace('_', ' ')"></span>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white" x-text="(user?.firstName || '') + ' ' + (user?.lastName || '')"></h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="periodLabel"></p>

                            <a x-show="myData.department === 'web_developer'" href="<?php echo $website_projects_url; ?>"
                               class="inline-flex items-center gap-1.5 mt-3 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                View Website Projects & task queue →
                            </a>

                            <!-- Score description -->
                            <div class="mt-3 flex items-center gap-2">
                                <div class="h-2 flex-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-2 rounded-full transition-all duration-700"
                                         :class="{
                                             'bg-green-500': myData.score >= 90,
                                             'bg-blue-500': myData.score >= 70 && myData.score < 90,
                                             'bg-amber-400': myData.score >= 50 && myData.score < 70,
                                             'bg-red-500': myData.score < 50 && myData.score !== null,
                                             'bg-gray-300': myData.score === null
                                         }"
                                         :style="'width: ' + (myData.score || 0) + '%'"></div>
                                </div>
                                <span class="text-xs font-medium"
                                      :class="{
                                          'text-green-600 dark:text-green-400': myData.score >= 90,
                                          'text-blue-600 dark:text-blue-400': myData.score >= 70 && myData.score < 90,
                                          'text-amber-600 dark:text-amber-400': myData.score >= 50 && myData.score < 70,
                                          'text-red-600 dark:text-red-400': myData.score !== null && myData.score < 50,
                                          'text-gray-500': myData.score === null
                                      }"
                                      x-text="myData.score >= 90 ? 'Excellent' : myData.score >= 70 ? 'Good' : myData.score >= 50 ? 'Needs improvement' : myData.score !== null ? 'Behind' : 'Pending'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scoring explanation (context-aware) -->
                <div x-show="myData.goals && myData.goals.length > 0" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/50 rounded-xl p-4 flex gap-3 items-start">
                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <div>
                        <p class="text-xs font-semibold text-blue-800 dark:text-blue-200">How is my score calculated?</p>
                        <p class="text-xs text-blue-700 dark:text-blue-300 mt-0.5"
                           x-text="myData.department === 'multimedia' ? 'Your score is the average of all your active delivery goals this month. Goals are only counted when you have projects assigned for that type.' : myData.department === 'graphic_designer' ? 'You are scored when artwork is submitted for review — not when the client approves. On-time handoffs use your first submission date; revisions after review do not count against you. Score = (Handoffs% × 2 + On-Time%) ÷ 3.' : myData.department === 'social_media_exec' ? 'Owned brands (set in Admin) drive your targets. Upload next month\'s concepts by the 2nd Friday, publish this month\'s posts, and submit last month\'s report for each brand in the first week.' : myData.department === 'web_developer' ? 'Web developer goals pull from Website Projects: projects started, tasks completed, and sites marked live. Manage work in Website Projects under Workflow.' : 'Your score is a weighted average of all active goals for this period.'">
                        </p>
                    </div>
                </div>

                <!-- Goals breakdown -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <template x-for="goal in myData.goals" :key="goal.id">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white" x-text="goal.label"></h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="goal.description"></p>
                                </div>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full flex-shrink-0"
                                      :class="{
                                          'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300': goal.status === 'achieved',
                                          'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300': goal.status === 'on_track',
                                          'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300': goal.status === 'pending',
                                          'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300': goal.status === 'behind',
                                          'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300': goal.status === 'no_data'
                                      }"
                                      x-text="goal.status === 'no_data' ? 'No Data' : goal.status === 'pending' ? 'Pending' : goal.status === 'achieved' ? '✓ Achieved' : goal.status === 'on_track' ? 'On Track' : 'Behind'"></span>
                            </div>

                            <!-- Stats row -->
                            <div class="flex items-end gap-1 mb-3">
                                <span class="text-3xl font-extrabold leading-none"
                                      :class="{
                                          'text-green-600 dark:text-green-400': goal.status === 'achieved',
                                          'text-blue-600 dark:text-blue-400': goal.status === 'on_track',
                                          'text-amber-500': goal.status === 'pending',
                                          'text-red-500': goal.status === 'behind',
                                          'text-gray-400': goal.status === 'no_data'
                                      }"
                                      x-text="goal.current"></span>
                                <span class="text-base text-gray-400 dark:text-gray-500 mb-0.5" x-text="' / ' + goal.target + ' ' + goal.unit"></span>
                            </div>

                            <!-- Progress bar -->
                            <div x-show="goal.percentage !== null" class="h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden mb-2">
                                <div class="h-2 rounded-full transition-all duration-700"
                                     :class="{
                                         'bg-green-500': goal.status === 'achieved',
                                         'bg-blue-500': goal.status === 'on_track',
                                         'bg-red-400': goal.status === 'behind',
                                         'bg-gray-300 dark:bg-gray-600': goal.status === 'no_data' || goal.status === 'pending'
                                     }"
                                     :style="'width: ' + Math.min(goal.percentage || 0, 100) + '%'"></div>
                            </div>
                            <div x-show="goal.percentage === null" class="text-xs text-amber-600 dark:text-amber-400 mb-2"
                                 x-text="goal.id === 'monthly_reports' ? 'Report deadline has not passed yet — due by end of the first week of this month.' : goal.id === 'concept_deadline' ? 'Concept deadline has not passed yet — due by the 2nd Friday of this month.' : 'Deadline has not passed yet — tracking will begin after the due date.'"></div>

                            <p x-show="goal.percentage !== null" class="text-xs text-gray-500 dark:text-gray-400" x-text="(goal.percentage || 0) + '% complete'"></p>

                            <!-- Late details for graphic designer -->
                            <div x-show="(goal.id === 'on_time_handoffs' || goal.id === 'on_time_delivery') && goal.details && goal.details.length > 0" class="mt-3 border-t border-gray-100 dark:border-gray-700 pt-3">
                                <p class="text-xs font-semibold text-red-600 dark:text-red-400 mb-2">Late first submissions:</p>
                                <div class="space-y-1.5 max-h-32 overflow-y-auto">
                                    <template x-for="item in (goal.details || [])" :key="item.title">
                                        <div class="flex items-start justify-between gap-2 text-xs">
                                            <span class="text-gray-700 dark:text-gray-300 truncate" x-text="item.title"></span>
                                            <span class="text-red-500 font-medium whitespace-nowrap flex-shrink-0" x-text="'+' + item.daysLate + 'd late'"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Pending items for graphic designer -->
                            <div x-show="(goal.id === 'handoffs_completed' || goal.id === 'graphics_completed') && goal.details && goal.details.length > 0" class="mt-3 border-t border-gray-100 dark:border-gray-700 pt-3">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">Still in progress (<span x-text="(goal.details || []).length"></span>):</p>
                                <div class="space-y-1.5 max-h-32 overflow-y-auto">
                                    <template x-for="item in (goal.details || [])" :key="item.title">
                                        <div class="flex items-start justify-between gap-2 text-xs">
                                            <span class="text-gray-700 dark:text-gray-300 truncate" x-text="item.title"></span>
                                            <span :class="item.overdueDays > 0 ? 'text-red-500 font-medium' : 'text-gray-400'"
                                                  x-text="item.overdueDays > 0 ? item.overdueDays + 'd overdue' : 'Due ' + new Date(item.dueDate).toLocaleDateString('en-GB', { month: 'short', day: 'numeric' })"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Revisions in progress (informational, no penalty) -->
                            <div x-show="goal.revisionItems && goal.revisionItems.length > 0" class="mt-3 border-t border-gray-100 dark:border-gray-700 pt-3">
                                <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 mb-2">Awaiting revision (<span x-text="(goal.revisionItems || []).length"></span>) — already handed off:</p>
                                <div class="space-y-1 max-h-24 overflow-y-auto">
                                    <template x-for="item in (goal.revisionItems || [])" :key="item.title + '-rev'">
                                        <p class="text-xs text-gray-600 dark:text-gray-400 truncate" x-text="item.title"></p>
                                    </template>
                                </div>
                            </div>

                            <!-- Brand breakdown for social media exec -->
                            <div x-show="goal.brandBreakdown && goal.brandBreakdown.length > 0 && ['next_month_concepts','concept_deadline','posts_published','monthly_reports','deadline_compliance','concepts_created'].includes(goal.id)" class="mt-3 border-t border-gray-100 dark:border-gray-700 pt-3">
                                <div class="flex items-center justify-between mb-1.5">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Owned brands</p>
                                    <p x-show="goal.secondFriday" class="text-xs text-indigo-600 dark:text-indigo-400">2nd Fri: <span x-text="new Date(goal.secondFriday).toLocaleDateString('en-GB', { month: 'short', day: 'numeric' })"></span></p>
                                    <p x-show="goal.reportDeadline && goal.id === 'monthly_reports'" class="text-xs text-indigo-600 dark:text-indigo-400">Due: <span x-text="new Date(goal.reportDeadline).toLocaleDateString('en-GB', { month: 'short', day: 'numeric' })"></span></p>
                                </div>
                                <div class="space-y-2">
                                    <template x-for="brand in (goal.brandBreakdown || [])" :key="brand.brandId + goal.id">
                                        <div class="text-xs">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="font-medium text-gray-700 dark:text-gray-300 truncate" x-text="brand.brandName"></span>
                                                <span class="text-gray-500 whitespace-nowrap" x-show="goal.id === 'next_month_concepts' || goal.id === 'concepts_created'" x-text="(brand.conceptsCreated ?? 0) + ' / ' + (brand.conceptsTarget ?? brand.target) + ' concepts'"></span>
                                                <span class="text-gray-500 whitespace-nowrap" x-show="goal.id === 'concept_deadline' || goal.id === 'deadline_compliance'" x-text="(brand.conceptsOnTime ?? 0) + ' / ' + (brand.conceptsTarget ?? brand.target) + ' on time'"></span>
                                                <span class="text-gray-500 whitespace-nowrap" x-show="goal.id === 'posts_published'" x-text="(brand.postsPublished ?? 0) + ' / ' + (brand.target ?? 0) + ' posts' + (brand.carousels ? ' · ' + brand.carousels + (brand.carousels === 1 ? ' carousel' : ' carousels') + (brand.carouselSlides ? ' (' + brand.carouselSlides + ' slides)' : '') : '')"></span>
                                                <span class="whitespace-nowrap font-medium" x-show="goal.id === 'monthly_reports'" :class="brand.reportSubmitted ? 'text-green-600' : 'text-amber-600'" x-text="brand.reportSubmitted ? 'Report ✓' : 'Report missing'"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- No goals state -->
                <div x-show="!myData.goals || myData.goals.length === 0" class="bg-gray-50 dark:bg-gray-800/50 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl p-8 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">No performance goals found for this period.</p>
                </div>
            </div>
            </template>

            <!-- TEAM VIEW -->
            <template x-if="!loading && view === 'team'">
            <div class="space-y-6 animate-fade-in">

                <!-- Admin department filter -->
                <div x-show="user?.role === 'admin'" class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter department:</span>
                    <template x-for="dept in ['all', 'web_developer', 'graphic_designer', 'social_media_exec', 'multimedia']" :key="dept">
                        <button @click="teamDeptFilter = dept; loadData()"
                                class="text-xs px-3 py-1.5 rounded-full font-medium transition-all"
                                :class="teamDeptFilter === dept ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:border-indigo-300'"
                                x-text="dept === 'all' ? 'All Departments' : dept === 'web_developer' ? 'Web Dev' : dept === 'graphic_designer' ? 'Graphic Design' : dept === 'social_media_exec' ? 'Social Media' : 'Multimedia'"></button>
                    </template>
                </div>

                <!-- My stats in team context -->
                <div x-show="teamData?.me?.department" class="bg-white dark:bg-gray-800 rounded-xl border-2 border-indigo-200 dark:border-indigo-700 p-5 shadow-sm">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wide">My Performance</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 relative flex-shrink-0">
                            <svg class="w-16 h-16 -rotate-90" viewBox="0 0 64 64">
                                <circle cx="32" cy="32" r="26" fill="none" stroke="currentColor" class="text-gray-100 dark:text-gray-700" stroke-width="6"/>
                                <circle cx="32" cy="32" r="26" fill="none"
                                        :stroke="getScoreStroke(teamData?.me?.score)"
                                        stroke-width="6" stroke-linecap="round"
                                        :stroke-dasharray="163"
                                        :stroke-dashoffset="teamData?.me?.score !== null ? 163 - (163 * Math.min(teamData?.me?.score, 100) / 100) : 163"
                                        class="ring-score"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-sm font-extrabold text-gray-900 dark:text-white" x-text="teamData?.me?.score !== null ? teamData?.me?.score + '%' : '—'"></span>
                            </div>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white" x-text="(user?.firstName || '') + ' ' + (user?.lastName || '') + ' (You)'"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="teamData?.me?.departmentLabel || ''"></p>
                        </div>
                    </div>
                </div>

                <!-- Department groups -->
                <template x-for="dept in (teamData?.departments || [])" :key="dept.department">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                        <!-- Dept header -->
                        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between"
                             :class="{
                                 'bg-blue-50 dark:bg-blue-900/20': dept.department === 'web_developer',
                                 'bg-purple-50 dark:bg-purple-900/20': dept.department === 'graphic_designer',
                                 'bg-green-50 dark:bg-green-900/20': dept.department === 'social_media_exec',
                                 'bg-orange-50 dark:bg-orange-900/20': dept.department === 'multimedia'
                             }">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white" x-text="dept.departmentLabel"></h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="(dept.members || []).length + ' member' + ((dept.members || []).length !== 1 ? 's' : '')"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Team Average</p>
                                <p class="text-xl font-extrabold"
                                   :class="{
                                       'text-green-600': dept.avgScore >= 90,
                                       'text-blue-600': dept.avgScore >= 70 && dept.avgScore < 90,
                                       'text-amber-500': dept.avgScore >= 50 && dept.avgScore < 70,
                                       'text-red-500': dept.avgScore !== null && dept.avgScore < 50,
                                       'text-gray-400': dept.avgScore === null
                                   }"
                                   x-text="dept.avgScore !== null ? dept.avgScore + '%' : '—'"></p>
                            </div>
                        </div>

                        <!-- Members grid -->
                        <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            <template x-for="member in dept.members" :key="member.user._id">
                                <div class="px-5 py-4 flex items-center gap-4">
                                    <!-- Avatar -->
                                    <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center text-white text-xs font-bold"
                                         :class="{
                                             'bg-blue-500': dept.department === 'web_developer',
                                             'bg-purple-500': dept.department === 'graphic_designer',
                                             'bg-green-500': dept.department === 'social_media_exec',
                                             'bg-orange-500': dept.department === 'multimedia'
                                         }"
                                         x-text="((member.user.firstName || '?')[0] + (member.user.lastName || '')[0]).toUpperCase()"></div>

                                    <!-- Info -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap mb-2">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="member.user.firstName + ' ' + member.user.lastName"></p>
                                            <span x-show="member.user.isManager" class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400">MANAGER</span>
                                            <span x-show="member.user.multimediaRole" class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-300 capitalize" x-text="(member.user.multimediaRole || '').replace('_', ' ')"></span>
                                        </div>

                                        <!-- Per-goal rows — much clearer than anonymous bars -->
                                        <div class="space-y-1.5">
                                            <template x-for="goal in member.goals" :key="goal.id">
                                                <div class="flex items-center gap-2">
                                                    <!-- Label -->
                                                    <span class="text-[11px] text-gray-500 dark:text-gray-400 w-32 flex-shrink-0 leading-tight" :title="goal.label" x-text="goal.shortLabel || goal.label"></span>
                                                    <!-- Bar -->
                                                    <div class="flex-1 max-w-[80px] h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                                        <div class="h-1.5 rounded-full transition-all"
                                                             :class="{
                                                                 'bg-green-500': goal.status === 'achieved',
                                                                 'bg-blue-400': goal.status === 'on_track',
                                                                 'bg-red-400': goal.status === 'behind',
                                                                 'bg-gray-300 dark:bg-gray-600': goal.status === 'no_data' || goal.status === 'pending'
                                                             }"
                                                             :style="'width: ' + Math.min(goal.percentage || 0, 100) + '%'"></div>
                                                    </div>
                                                    <!-- Current / target -->
                                                    <span class="text-[11px] font-medium leading-none whitespace-nowrap"
                                                          :class="{
                                                              'text-green-600 dark:text-green-400': goal.status === 'achieved',
                                                              'text-blue-500 dark:text-blue-400': goal.status === 'on_track',
                                                              'text-red-500 dark:text-red-400': goal.status === 'behind',
                                                              'text-gray-400': goal.status === 'no_data' || goal.status === 'pending'
                                                          }"
                                                          x-text="goal.status === 'no_data' ? 'No data' : goal.status === 'pending' ? 'Pending' : goal.current + ' / ' + goal.target + ' ' + goal.unit"></span>
                                                    <!-- Status dot -->
                                                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                                          :class="{
                                                              'bg-green-500': goal.status === 'achieved',
                                                              'bg-blue-400': goal.status === 'on_track',
                                                              'bg-red-400': goal.status === 'behind',
                                                              'bg-gray-300 dark:bg-gray-600': goal.status === 'no_data' || goal.status === 'pending'
                                                          }"></span>
                                                </div>
                                            </template>

                                            <!-- Hint for SM exec / web dev with no data -->
                                            <div x-show="member.score === null && (dept.department === 'social_media_exec')" class="text-[11px] text-amber-600 dark:text-amber-400 flex items-center gap-1 mt-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                                Set monthly concept target in Admin → Clients
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Score badge -->
                                    <div class="text-right flex-shrink-0 ml-2">
                                        <span class="text-lg font-extrabold leading-none"
                                              :class="{
                                                  'text-green-600 dark:text-green-400': member.score >= 90,
                                                  'text-blue-600 dark:text-blue-400': member.score >= 70 && member.score < 90,
                                                  'text-amber-500': member.score >= 50 && member.score < 70,
                                                  'text-red-500': member.score !== null && member.score < 50,
                                                  'text-gray-400': member.score === null
                                              }"
                                              x-text="member.score !== null ? member.score + '%' : '—'"></span>
                                        <p class="text-[10px] mt-0.5"
                                           :class="{
                                               'text-green-600 dark:text-green-400': member.score >= 90,
                                               'text-blue-500 dark:text-blue-400': member.score >= 70 && member.score < 90,
                                               'text-amber-500': member.score >= 50 && member.score < 70,
                                               'text-red-500': member.score !== null && member.score < 50,
                                               'text-gray-400': member.score === null
                                           }"
                                           x-text="member.score >= 90 ? 'Excellent' : member.score >= 70 ? 'Good' : member.score >= 50 ? 'Needs improvement' : member.score !== null ? 'Behind' : 'No Data'"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Empty team -->
                <div x-show="!loading && teamData && (!teamData?.departments || teamData?.departments.length === 0)" class="bg-gray-50 dark:bg-gray-800/50 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl p-8 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">No team members with departments assigned. Set departments in the Admin Panel.</p>
                </div>
            </div>
            </template>

            <!-- Access denied for non-managers in team view -->
            <div x-show="!loading && view === 'team' && !canViewTeam" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/50 rounded-xl p-8 text-center">
                <p class="text-sm text-red-600 dark:text-red-400">You need manager or admin access to view team performance.</p>
            </div>
        </div>
    </main>
</div>

<script>
function progressApp() {
    return {
        authChecked: false,
        user: null,
        isSidebarOpen: true,
        viewMode: window.HubAuth.resolveViewMode(window.HubAuth.readStoredViewMode(), (() => { try { return JSON.parse(localStorage.getItem('user') || '{}') || {}; } catch (e) { return {}; } })()), // provisional until /auth/me answers
        showPwModal: false,
        pwCurrent: '', pwNew: '', pwConfirm: '', pwLoading: false, pwError: '', pwSuccess: '',
        isDark: localStorage.getItem('theme') === 'dark',
        get theme() { return this.isDark ? 'dark' : 'light'; },
        loading: false,
        view: 'personal',
        periodType: 'month',
        selectedYear: new Date().getFullYear(),
        selectedMonth: new Date().getMonth(),
        myData: null,
        teamData: null,
        teamDeptFilter: 'all',
        monthNames: ['January','February','March','April','May','June','July','August','September','October','November','December'],

        get yearOptions() {
            const y = new Date().getFullYear();
            return [y, y - 1, y - 2];
        },

        get periodLabel() {
            if (this.periodType === 'month') return this.monthNames[this.selectedMonth] + ' ' + this.selectedYear;
            if (this.periodType === 'quarter') {
                const q = Math.floor(this.selectedMonth / 3) + 1;
                return `Q${q} ${this.selectedYear}`;
            }
            return String(this.selectedYear);
        },

        get canViewTeam() {
            return this.user?.role === 'admin' || this.user?.isManager;
        },

        get scoreRingColor() {
            const s = this.myData?.score;
            if (s === null || s === undefined) return '#d1d5db';
            if (s >= 90) return '#22c55e';
            if (s >= 70) return '#3b82f6';
            if (s >= 50) return '#f59e0b';
            return '#ef4444';
        },

        getScoreStroke(score) {
            if (score === null || score === undefined) return '#d1d5db';
            if (score >= 90) return '#22c55e';
            if (score >= 70) return '#3b82f6';
            if (score >= 50) return '#f59e0b';
            return '#ef4444';
        },

        toggleDark() {
            this.isDark = !this.isDark;
            localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', this.isDark);
        },
        toggleTheme() { this.toggleDark(); },

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

        setPeriodType(type) {
            this.periodType = type;
            this.loadData();
        },

        async init() {
            document.documentElement.classList.toggle('dark', this.isDark);

            const token = localStorage.getItem('token');
            if (!token) { this.authChecked = true; return; }

            try {
                const res = await fetch(`${API_URL}/auth/me`, {
                    headers: { Authorization: `Bearer ${token}` }
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Auth failed');

                if (data.success && data.user) {
                    this.user = {
                        ...data.user,
                        _id: data.user._id || data.user.id
                    };
                    window.HubAuth.setUser(this.user);
                    localStorage.setItem('user', JSON.stringify(this.user));

                    if (this.user.role === 'client') {
                        window.location.href = '<?php echo esc_js(esirom_hub_page_url('dashboard')); ?>';
                        return;
                    }
                    // Staff-only page: derive the view mode from the verified role; a super admin previewing as client falls back to admin.
                    this.viewMode = window.HubAuth.applyViewMode(window.HubAuth.readStoredViewMode() === 'client' ? 'admin' : undefined);

                    if (this.user.role === 'admin') {
                        this.view = 'team';
                    } else if (this.user.isManager) {
                        this.view = 'team';
                    }

                    await this.loadData();
                }
            } catch (e) {
                console.error('Auth error', e);
            } finally {
                this.authChecked = true;
            }
        },

        async loadData() {
            if (!this.user) return;
            this.loading = true;
            this.myData = null;
            this.teamData = null;

            const params = new URLSearchParams({
                period: this.periodType,
                year: this.selectedYear,
                month: this.selectedMonth
            });

            try {
                if (this.view === 'personal' || this.canViewTeam) {
                    const meRes = await fetch(`${API_URL}/team-progress/me?${params}`, {
                        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
                    });
                    const meData = await meRes.json();
                    if (meData.success) this.myData = meData.data;
                }

                if (this.view === 'team' && this.canViewTeam) {
                    const teamParams = new URLSearchParams(params);
                    if (this.teamDeptFilter !== 'all') teamParams.set('department', this.teamDeptFilter);
                    const teamRes = await fetch(`${API_URL}/team-progress/team?${teamParams}`, {
                        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
                    });
                    const td = await teamRes.json();
                    if (td.success) this.teamData = td.data;
                }
            } catch (e) {
                console.error('Progress load error:', e);
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>

<?php include get_template_directory() . '/inc/change-password-modal.php'; ?>
<?php esirom_hub_staff_mobile_nav('progress'); ?>
</body>
</html>
