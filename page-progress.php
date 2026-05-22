<?php
/**
 * Template Name: My Progress
 */

if (!defined('ABSPATH')) exit;

$api_url = get_option('esirom_api_url', 'https://esirom-hub-backend-production.up.railway.app/api');
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Progress — Agency Hub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        const API_URL = '<?php echo esc_js($api_url); ?>';
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        };
    </script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .sidebar-collapsed .nav-text { display: none; }
        .ring-score { transition: stroke-dashoffset 0.8s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.3s ease forwards; }
    </style>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100" x-data="progressApp()" x-init="init()">

<!-- Auth guard -->
<div x-show="!authChecked" class="fixed inset-0 flex items-center justify-center bg-white dark:bg-gray-900 z-50">
    <div class="flex flex-col items-center gap-3">
        <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
        <p class="text-sm text-gray-500">Loading...</p>
    </div>
</div>

<div x-show="authChecked && !user" x-cloak class="fixed inset-0 flex items-center justify-center bg-white dark:bg-gray-900 z-50">
    <div class="text-center">
        <p class="text-gray-600 dark:text-gray-400 mb-4">You must be logged in to view this page.</p>
        <a href="<?php echo esc_url(get_permalink(get_page_by_path('login'))); ?>" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Go to Login</a>
    </div>
</div>

<div x-show="authChecked && user" x-cloak class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="hidden md:flex flex-col w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex-shrink-0">
        <div class="flex items-center p-5 border-b border-gray-200 dark:border-gray-700">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-900 dark:text-white">Agency Hub</p>
                <p class="text-[10px] text-gray-500">by esirom</p>
            </div>
        </div>

        <nav class="flex-1 px-2 py-4 space-y-2 overflow-y-auto">
            <?php esirom_hub_staff_sidebar_nav('progress', 'site', false); ?>
        </nav>

        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <?php esirom_hub_staff_sidebar_footer('site', false); ?>
            <!-- User dropdown -->
            <div x-data="{ dropdownOpen: false }" class="relative">
                <button @click="dropdownOpen = !dropdownOpen"
                        class="w-full flex items-center gap-2 p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-left">
                    <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold shrink-0"
                         x-text="((user?.firstName || '?')[0] + (user?.lastName || '')[0]).toUpperCase()"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate"
                           x-text="(user?.firstName || '') + ' ' + (user?.lastName || '')"></p>
                        <p class="text-xs text-gray-500 capitalize"
                           x-text="user?.role?.replace('_', ' ') || ''"></p>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-cloak
                     class="absolute bottom-full left-0 right-0 mb-1 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 py-1 z-50">
                    <a @click.prevent="showPwModal = true; dropdownOpen = false" href="#"
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Change Password
                    </a>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main -->
    <main class="flex-1 overflow-y-auto">

        <!-- Top bar -->
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between gap-4 flex-wrap sticky top-0 z-10">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    My Progress
                </h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="periodLabel"></p>
            </div>

            <!-- Controls -->
            <div class="flex items-center gap-3 flex-wrap">
                <!-- Period Type -->
                <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-0.5 gap-0.5">
                    <template x-for="pt in ['month','quarter','year']" :key="pt">
                        <button @click="setPeriodType(pt)"
                                class="px-3 py-1.5 text-xs font-medium rounded-md transition-all capitalize"
                                :class="periodType === pt ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                                x-text="pt === 'month' ? 'Month' : pt === 'quarter' ? 'Quarter' : 'Year'"></button>
                    </template>
                </div>

                <!-- Year picker -->
                <select x-model="selectedYear" @change="loadData()" class="text-sm border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <template x-for="y in yearOptions" :key="y">
                        <option :value="y" x-text="y"></option>
                    </template>
                </select>

                <!-- Month picker (month mode only) -->
                <select x-show="periodType === 'month'" x-model.number="selectedMonth" @change="loadData()" class="text-sm border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <template x-for="(name, idx) in monthNames" :key="idx">
                        <option :value="idx" x-text="name"></option>
                    </template>
                </select>

                <!-- View toggle for managers/admins -->
                <div x-show="canViewTeam" class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-0.5 gap-0.5">
                    <button @click="view = 'personal'; loadData()"
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition-all"
                            :class="view === 'personal' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400'">My Stats</button>
                    <button @click="view = 'team'; loadData()"
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition-all"
                            :class="view === 'team' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400'">Team View</button>
                </div>

                <!-- Dark mode -->
                <button @click="toggleDark()" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg x-show="!isDark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="isDark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
            </div>
        </div>

        <div class="p-6 space-y-6">

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
            <div x-show="!loading && view === 'personal' && myData && myData.department" class="space-y-6 animate-fade-in">

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
                                      x-text="myData.score >= 90 ? 'Excellent' : myData.score >= 70 ? 'Good' : myData.score >= 50 ? 'Needs Improvement' : myData.score !== null ? 'Behind' : 'Pending'"></span>
                            </div>
                        </div>
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
                            <div x-show="goal.percentage === null" class="text-xs text-amber-600 dark:text-amber-400 mb-2">Deadline has not passed yet — tracking will begin after the 2nd Friday.</div>

                            <p x-show="goal.percentage !== null" class="text-xs text-gray-500 dark:text-gray-400" x-text="(goal.percentage || 0) + '% complete'"></p>

                            <!-- Late details for graphic designer -->
                            <div x-show="goal.id === 'on_time_delivery' && goal.details && goal.details.length > 0" class="mt-3 border-t border-gray-100 dark:border-gray-700 pt-3">
                                <p class="text-xs font-semibold text-red-600 dark:text-red-400 mb-2">Late Submissions:</p>
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
                            <div x-show="goal.id === 'graphics_completed' && goal.details && goal.details.length > 0" class="mt-3 border-t border-gray-100 dark:border-gray-700 pt-3">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">Pending (<span x-text="goal.details.length"></span>):</p>
                                <div class="space-y-1.5 max-h-32 overflow-y-auto">
                                    <template x-for="item in (goal.details || [])" :key="item.title">
                                        <div class="flex items-start justify-between gap-2 text-xs">
                                            <span class="text-gray-700 dark:text-gray-300 truncate" x-text="item.title"></span>
                                            <span :class="item.overdueDays > 0 ? 'text-red-500 font-medium' : 'text-gray-400'"
                                                  x-text="item.overdueDays > 0 ? item.overdueDays + 'd overdue' : 'Due ' + new Date(item.dueDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Brand breakdown for social media exec -->
                            <div x-show="goal.id === 'deadline_compliance' && goal.brandBreakdown && goal.brandBreakdown.length > 0" class="mt-3 border-t border-gray-100 dark:border-gray-700 pt-3">
                                <div class="flex items-center justify-between mb-1.5">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Brand Targets</p>
                                    <p x-show="goal.secondFriday" class="text-xs text-indigo-600 dark:text-indigo-400">Deadline: <span x-text="goal.secondFriday ? new Date(goal.secondFriday).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) : ''"></span></p>
                                </div>
                                <div class="space-y-1">
                                    <template x-for="brand in (goal.brandBreakdown || [])" :key="brand.brandId">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-700 dark:text-gray-300 truncate" x-text="brand.brandName"></span>
                                            <span class="text-gray-500 font-medium" x-text="brand.target + ' concepts'"></span>
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

            <!-- TEAM VIEW -->
            <div x-show="!loading && view === 'team'" class="space-y-6 animate-fade-in">

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
                <div x-show="teamData && teamData.me && teamData.me.department" class="bg-white dark:bg-gray-800 rounded-xl border-2 border-indigo-200 dark:border-indigo-700 p-5 shadow-sm">
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
                                        :stroke-dashoffset="teamData?.me?.score !== null ? 163 - (163 * Math.min(teamData.me.score, 100) / 100) : 163"
                                        class="ring-score"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-sm font-extrabold text-gray-900 dark:text-white" x-text="teamData?.me?.score !== null ? teamData.me.score + '%' : '—'"></span>
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
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="dept.members.length + ' member' + (dept.members.length !== 1 ? 's' : '')"></p>
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
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="member.user.firstName + ' ' + member.user.lastName"></p>
                                            <span x-show="member.user.isManager" class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400">MANAGER</span>
                                            <span x-show="member.user.multimediaRole" class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 capitalize" x-text="(member.user.multimediaRole || '').replace('_', ' ')"></span>
                                        </div>

                                        <!-- Mini goal bars -->
                                        <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                            <template x-for="goal in member.goals" :key="goal.id">
                                                <div class="flex items-center gap-1 group relative">
                                                    <div class="w-16 h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                                        <div class="h-1.5 rounded-full"
                                                             :class="{
                                                                 'bg-green-500': goal.status === 'achieved',
                                                                 'bg-blue-400': goal.status === 'on_track',
                                                                 'bg-red-400': goal.status === 'behind',
                                                                 'bg-gray-300 dark:bg-gray-600': goal.status === 'no_data' || goal.status === 'pending'
                                                             }"
                                                             :style="'width: ' + Math.min(goal.percentage || 0, 100) + '%'"></div>
                                                    </div>
                                                    <span class="text-[10px] text-gray-400 leading-none" x-text="goal.percentage !== null ? goal.percentage + '%' : '?'"></span>
                                                </div>
                                            </template>
                                        </div>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5" x-text="member.goals.map(g => g.label).join(' · ')"></p>
                                    </div>

                                    <!-- Score badge -->
                                    <div class="text-right flex-shrink-0">
                                        <span class="text-lg font-extrabold"
                                              :class="{
                                                  'text-green-600 dark:text-green-400': member.score >= 90,
                                                  'text-blue-600 dark:text-blue-400': member.score >= 70 && member.score < 90,
                                                  'text-amber-500': member.score >= 50 && member.score < 70,
                                                  'text-red-500': member.score !== null && member.score < 50,
                                                  'text-gray-400': member.score === null
                                              }"
                                              x-text="member.score !== null ? member.score + '%' : '—'"></span>
                                        <p class="text-[10px] text-gray-400 mt-0.5"
                                           x-text="member.score >= 90 ? 'Excellent' : member.score >= 70 ? 'Good' : member.score >= 50 ? 'Needs Work' : member.score !== null ? 'Behind' : 'No Data'"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Empty team -->
                <div x-show="!loading && teamData && (!teamData.departments || teamData.departments.length === 0)" class="bg-gray-50 dark:bg-gray-800/50 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl p-8 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">No team members with departments assigned. Set departments in the Admin Panel.</p>
                </div>
            </div>

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
        viewMode: localStorage.getItem('viewMode') || 'admin',
        showPwModal: false,
        pwCurrent: '', pwNew: '', pwConfirm: '', pwLoading: false, pwError: '', pwSuccess: '',
        isDark: localStorage.getItem('darkMode') === 'true',
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
            localStorage.setItem('darkMode', this.isDark);
            document.documentElement.classList.toggle('dark', this.isDark);
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
                    localStorage.setItem('user', JSON.stringify(this.user));

                    if (this.user.role === 'client') {
                        window.location.href = '<?php echo esc_js(get_permalink(get_page_by_path('dashboard'))); ?>';
                        return;
                    }

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
</body>
</html>
