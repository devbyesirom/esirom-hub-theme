<?php
/**
 * Template Name: Website Projects
 * Description: Website project hub — bugs, requests, content updates, and client notifications
 */
if (!defined('ABSPATH')) exit;

show_admin_bar(false);

$api_url = get_option('esirom_api_url', 'https://esirom-hub-backend-production.up.railway.app/api');
$login_url = esc_url(get_permalink(get_page_by_path('login')));
$dashboard_url = esc_url(get_permalink(get_page_by_path('dashboard')));
$website_projects_url = esc_url(get_permalink(get_page_by_path('website-projects')));
$progress_url = esc_url(get_permalink(get_page_by_path('progress')));
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Projects — Agency Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        const API_URL = '<?php echo esc_js($api_url); ?>';
        const LOGIN_URL = '<?php echo esc_js($login_url); ?>';
        const DASHBOARD_URL = '<?php echo esc_js($dashboard_url); ?>';
        const WEBSITE_PROJECTS_URL = '<?php echo esc_js($website_projects_url); ?>';
        const PROGRESS_URL = '<?php echo esc_js($progress_url); ?>';
        tailwind.config = { darkMode: 'class', theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } } };
    </script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        <?php esirom_hub_layout_styles(); ?>
    </style>
</head>
<body class="hub-has-mobile-nav h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 pb-16 md:pb-0" x-data="websiteProjectsApp()" x-init="init()">

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

    <aside :class="isSidebarOpen ? 'sidebar-expanded' : 'sidebar-collapsed'" class="hub-app-sidebar sidebar hidden md:flex bg-white dark:bg-gray-900/70 border-r border-gray-200 dark:border-gray-700/50 flex-col flex-shrink-0">
        <?php esirom_hub_staff_sidebar_header(true); ?>
        <nav class="flex-1 px-2 py-4 space-y-2 overflow-y-auto">
            <?php $hub_nav_active = 'website_projects'; $hub_nav_context = 'site'; $hub_nav_alpine_labels = true; esirom_hub_staff_sidebar_nav('website_projects', 'site', true); ?>
            <?php $hub_client_nav_active = 'website_projects'; $hub_client_nav_context = 'site'; esirom_hub_client_sidebar_nav('website_projects', 'site', true); ?>
        </nav>
        <div class="p-2 border-t border-gray-200 dark:border-gray-700/50">
            <?php esirom_hub_staff_sidebar_footer('site', true); ?>
        </div>
    </aside>

    <main class="hub-app-main flex-1 min-w-0">
        <header class="bg-white/80 dark:bg-gray-900/70 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700/50 px-4 sm:px-6 py-4 sticky top-0 z-10">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-lg font-bold text-gray-900 dark:text-white">Website Projects</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="isClient ? 'Submit bugs, requests, and content updates for your website' : (user?.department === 'web_developer' ? 'Manage websites — tracked in My Progress' : 'Report bugs, suggest features, or request updates for client & internal sites')"></p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a x-show="canManageTasks" href="<?php echo esc_url($progress_url); ?>" class="px-3 py-1.5 border border-indigo-200 dark:border-indigo-700 text-indigo-700 dark:text-indigo-300 text-xs font-semibold rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-900/30">My Progress →</a>
                    <button x-show="canManageProjects" @click="openProjectModal()" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-xl hover:bg-indigo-700">+ New Project</button>
                    <button @click="openTaskModal()" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-semibold rounded-xl hover:bg-emerald-700" x-text="isClient ? '+ New Request' : '+ New Task'"></button>
                </div>
            </div>
            <div class="flex gap-1 mt-3 flex-wrap">
                <template x-for="t in visibleTabs" :key="t.id">
                    <button @click="tab = t.id; loadTab()" class="px-3 py-1.5 text-xs font-semibold rounded-xl transition-all"
                            :class="tab === t.id ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                            x-text="t.label"></button>
                </template>
            </div>
        </header>

        <div class="hub-page-content p-4 sm:p-6 space-y-6">
            <div x-show="loading" class="flex justify-center py-20">
                <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
            </div>

            <!-- Dashboard -->
            <div x-show="!loading && tab === 'dashboard'" x-cloak class="space-y-6">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-4">
                        <p class="text-2xl font-bold text-indigo-600" x-text="stats.projectCount ?? 0"></p>
                        <p class="text-xs text-gray-500 mt-1">Active Projects</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-4">
                        <p class="text-2xl font-bold text-amber-600" x-text="stats.openTasks ?? 0"></p>
                        <p class="text-xs text-gray-500 mt-1">Open Tasks</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-4">
                        <p class="text-2xl font-bold text-red-600" x-text="stats.urgentTasks ?? 0"></p>
                        <p class="text-xs text-gray-500 mt-1">Urgent</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-4">
                        <p class="text-2xl font-bold text-green-600" x-text="stats.completedThisWeek ?? 0"></p>
                        <p class="text-xs text-gray-500 mt-1">Completed (7d)</p>
                    </div>
                </div>

                <!-- Transparency counts -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div>
                            <h2 class="font-semibold text-gray-900 dark:text-white">Development transparency</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Counts only — no task details exposed</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/40">
                            <p class="text-3xl font-extrabold text-amber-600 tabular-nums" x-text="stats.pending ?? 0"></p>
                            <p class="text-xs font-medium text-amber-800 dark:text-amber-300 mt-1">Pending</p>
                            <p class="text-[10px] text-gray-500 mt-0.5">Submitted & acknowledged</p>
                        </div>
                        <div class="text-center p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/40">
                            <p class="text-3xl font-extrabold text-blue-600 tabular-nums" x-text="stats.outstanding ?? 0"></p>
                            <p class="text-xs font-medium text-blue-800 dark:text-blue-300 mt-1">Outstanding</p>
                            <p class="text-[10px] text-gray-500 mt-0.5">In progress & review</p>
                        </div>
                        <div class="text-center p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800/40">
                            <p class="text-3xl font-extrabold text-green-600 tabular-nums" x-text="stats.completed ?? 0"></p>
                            <p class="text-xs font-medium text-green-800 dark:text-green-300 mt-1">Completed</p>
                            <p class="text-[10px] text-gray-500 mt-0.5">Delivered work</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b dark:border-gray-700 font-semibold">Recent Activity</div>
                    <div class="divide-y dark:divide-gray-700">
                        <template x-for="task in recentTasks" :key="task._id">
                            <button @click="openTaskDetail(task._id)" class="w-full px-5 py-3 text-left hover:bg-gray-50 dark:hover:bg-gray-700/40 flex items-center gap-3">
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium" :class="typeBadge(task.type)" x-text="typeLabel(task.type)"></span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate" x-text="task.title"></p>
                                    <p class="text-xs text-gray-500" x-text="(task.clientId?.brandName || '') + (task.projectId?.title ? ' · ' + task.projectId.title : '')"></p>
                                </div>
                                <span class="text-xs px-2 py-0.5 rounded-full" :class="statusBadge(task.status)" x-text="statusLabel(task.status)"></span>
                            </button>
                        </template>
                        <p x-show="!recentTasks.length" class="px-5 py-8 text-sm text-gray-500 text-center">No tasks yet. Create a project or submit a request to get started.</p>
                    </div>
                </div>
            </div>

            <!-- Projects -->
            <div x-show="!loading && tab === 'projects'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <template x-for="project in projects" :key="project._id">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-5 space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white" x-text="project.title"></h3>
                                <p class="text-xs text-gray-500">
                                    <span x-text="project.clientId?.brandName || project.clientId?.name"></span>
                                    <span x-show="project.isInternal" class="ml-1 px-1.5 py-0.5 rounded bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300">Internal</span>
                                </p>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300" x-text="project.status.replace(/_/g, ' ')"></span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2" x-text="project.description || 'No description'"></p>
                        <div class="flex flex-wrap gap-2 text-xs">
                            <a x-show="project.productionUrl" :href="project.productionUrl" target="_blank" class="text-indigo-600 hover:underline">Live site ↗</a>
                            <a x-show="project.adminUrl" :href="project.adminUrl" target="_blank" class="text-gray-600 hover:underline">Admin ↗</a>
                            <span x-show="project.cmsType" class="text-gray-500" x-text="(project.cmsType === 'custom' ? 'Custom' : 'WordPress') + (project.hostingProvider ? ' · ' + hostingLabel(project.hostingProvider) : '')"></span>
                        </div>
                        <div class="flex gap-3 text-center">
                            <div class="flex-1 py-2 rounded-lg bg-amber-50 dark:bg-amber-900/20">
                                <p class="text-lg font-bold text-amber-600 tabular-nums" x-text="project.taskCounts?.pending ?? 0"></p>
                                <p class="text-[10px] text-gray-500">Pending</p>
                            </div>
                            <div class="flex-1 py-2 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                                <p class="text-lg font-bold text-blue-600 tabular-nums" x-text="project.taskCounts?.outstanding ?? 0"></p>
                                <p class="text-[10px] text-gray-500">Outstanding</p>
                            </div>
                            <div class="flex-1 py-2 rounded-lg bg-green-50 dark:bg-green-900/20">
                                <p class="text-lg font-bold text-green-600 tabular-nums" x-text="project.taskCounts?.completed ?? 0"></p>
                                <p class="text-[10px] text-gray-500">Done</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t dark:border-gray-700">
                            <button @click="openTaskModal(project._id)" class="text-xs text-emerald-600 hover:underline">+ Submit request</button>
                            <div class="flex gap-2">
                                <button @click="selectProject(project); tab='tasks'; loadTasks()" class="text-xs text-indigo-600 hover:underline">View tasks</button>
                                <button x-show="canManageProjects" @click="openProjectModal(project)" class="text-xs text-gray-600 hover:underline">Edit</button>
                            </div>
                        </div>
                    </div>
                </template>
                <p x-show="!projects.length" class="col-span-full text-center text-gray-500 py-12">No website projects yet.</p>
            </div>

            <!-- Tasks -->
            <div x-show="!loading && tab === 'tasks'" x-cloak class="space-y-4">
                <div class="flex flex-wrap gap-2">
                    <select x-model="taskFilter.status" @change="loadTasks()" class="px-3 py-2 border rounded-xl text-xs dark:bg-gray-800 dark:border-gray-600">
                        <option value="">All statuses</option>
                        <template x-for="s in statusOptions" :key="s.value">
                            <option :value="s.value" x-text="s.label"></option>
                        </template>
                    </select>
                    <select x-model="taskFilter.type" @change="loadTasks()" class="px-3 py-2 border rounded-xl text-xs dark:bg-gray-800 dark:border-gray-600">
                        <option value="">All types</option>
                        <template x-for="t in typeOptions" :key="t.value">
                            <option :value="t.value" x-text="t.label"></option>
                        </template>
                    </select>
                    <select x-show="projects.length > 1" x-model="taskFilter.projectId" @change="loadTasks()" class="px-3 py-2 border rounded-xl text-xs dark:bg-gray-800 dark:border-gray-600">
                        <option value="">All projects</option>
                        <template x-for="p in projects" :key="p._id">
                            <option :value="p._id" x-text="p.title"></option>
                        </template>
                    </select>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 overflow-hidden divide-y dark:divide-gray-700">
                    <template x-for="task in tasks" :key="task._id">
                        <button @click="openTaskDetail(task._id)" class="w-full px-5 py-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700/40">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium" :class="typeBadge(task.type)" x-text="typeLabel(task.type)"></span>
                                <span class="text-xs px-2 py-0.5 rounded-full" :class="priorityBadge(task.priority)" x-text="task.priority"></span>
                                <span class="text-xs px-2 py-0.5 rounded-full" :class="statusBadge(task.status)" x-text="statusLabel(task.status)"></span>
                            </div>
                            <p class="font-medium" x-text="task.title"></p>
                            <p class="text-xs text-gray-500 mt-1" x-text="(task.clientId?.brandName || '') + ' · ' + (task.projectId?.title || '')"></p>
                        </button>
                    </template>
                    <p x-show="!tasks.length" class="px-5 py-10 text-sm text-gray-500 text-center">No tasks match your filters.</p>
                </div>
            </div>

            <!-- My Queue (web team) -->
            <div x-show="!loading && tab === 'queue'" x-cloak class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 overflow-hidden divide-y dark:divide-gray-700">
                    <template x-for="task in myQueue" :key="task._id">
                        <button @click="openTaskDetail(task._id)" class="w-full px-5 py-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700/40">
                            <p class="font-medium" x-text="task.title"></p>
                            <p class="text-xs text-gray-500 mt-1" x-text="typeLabel(task.type) + ' · ' + statusLabel(task.status)"></p>
                        </button>
                    </template>
                    <p x-show="!myQueue.length" class="px-5 py-10 text-sm text-gray-500 text-center">Nothing assigned to you right now.</p>
                </div>
            </div>
        </div>
    </main>
</div>

<?php esirom_hub_staff_mobile_nav('website_projects'); ?>
<?php $hub_client_nav_context = 'site'; esirom_hub_client_mobile_nav(); ?>

<!-- Project Modal -->
<div x-show="showProjectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @keydown.escape.window="showProjectModal = false">
    <div @click.outside="showProjectModal = false" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 space-y-4">
        <h2 class="text-lg font-bold" x-text="projectForm._id ? 'Edit Project' : 'New Website Project'"></h2>
        <div class="space-y-3">
            <div>
                <label class="text-xs font-medium text-gray-600">Client *</label>
                <select x-model="projectForm.clientId" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600">
                    <option value="">Select client</option>
                    <template x-for="c in clients" :key="c._id">
                        <option :value="c._id" x-text="c.brandName || c.name"></option>
                    </template>
                </select>
            </div>
            <div><label class="text-xs font-medium">Project title *</label><input x-model="projectForm.title" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600"></div>
            <div><label class="text-xs font-medium">Description</label><textarea x-model="projectForm.description" rows="2" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600"></textarea></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="text-xs font-medium">Live URL</label><input x-model="projectForm.productionUrl" type="url" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600"></div>
                <div><label class="text-xs font-medium">Admin / CMS URL</label><input x-model="projectForm.adminUrl" type="url" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-medium">CMS</label>
                    <select x-model="projectForm.cmsType" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600">
                        <option value="wordpress">WordPress</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium">Hosting</label>
                    <select x-model="projectForm.hostingProvider" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600">
                        <option value="">Select hosting</option>
                        <option value="netlify">Netlify</option>
                        <option value="railway">Railway</option>
                        <option value="hostgator">HostGator</option>
                        <option value="siteground">SiteGround</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-medium">Lead developer</label>
                    <select x-model="projectForm.leadDeveloper" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600">
                        <option value="">Unassigned</option>
                        <template x-for="d in developers" :key="d._id">
                            <option :value="d._id" x-text="d.firstName + ' ' + d.lastName"></option>
                        </template>
                    </select>
                </div>
                <div class="flex items-end pb-2">
                    <label class="flex items-center gap-2 text-xs font-medium cursor-pointer">
                        <input type="checkbox" x-model="projectForm.isInternal" class="rounded border-gray-300 text-indigo-600">
                        Internal site (all brand reps can submit tasks)
                    </label>
                </div>
            </div>
            <div><label class="text-xs font-medium">Tech stack / notes</label><textarea x-model="projectForm.notes" rows="2" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600"></textarea></div>
        </div>
        <div class="flex justify-end gap-2 pt-2">
            <button @click="showProjectModal = false" class="px-4 py-2 text-sm rounded-lg border">Cancel</button>
            <button @click="saveProject()" class="px-4 py-2 text-sm rounded-lg bg-indigo-600 text-white">Save Project</button>
        </div>
    </div>
</div>

<!-- Task Modal (create) -->
<div x-show="showTaskModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
    <div @click.outside="showTaskModal = false" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-4">
        <h2 class="text-lg font-bold" x-text="isClient ? 'Submit a Request' : (user?.role === 'brand_rep' && user?.department !== 'web_developer' ? 'Report Bug or Suggest Feature' : 'New Task')"></h2>
        <p x-show="user?.role === 'brand_rep' && user?.department !== 'web_developer'" class="text-xs text-gray-500">Your submission goes to the web team for review. Internal sites are open to all brand reps.</p>
        <div class="space-y-3">
            <div>
                <label class="text-xs font-medium">Website project *</label>
                <select x-model="taskForm.projectId" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600">
                    <option value="">Select project</option>
                    <template x-for="p in projects" :key="p._id">
                        <option :value="p._id" x-text="(p.clientId?.brandName ? p.clientId.brandName + ' — ' : '') + p.title"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="text-xs font-medium">Type *</label>
                <select x-model="taskForm.type" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600">
                    <template x-for="t in typeOptions" :key="t.value">
                        <option :value="t.value" x-text="t.label"></option>
                    </template>
                </select>
            </div>
            <div><label class="text-xs font-medium">Title *</label><input x-model="taskForm.title" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600"></div>
            <div><label class="text-xs font-medium">Description</label><textarea x-model="taskForm.description" rows="3" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600" placeholder="Steps to reproduce, desired changes, content to add…"></textarea></div>
            <div><label class="text-xs font-medium">Page URL (where on the site)</label><input x-model="taskForm.pageUrl" type="url" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600" placeholder="https://…"></div>
            <div x-show="!isClient" class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-medium">Priority</label>
                    <select x-model="taskForm.priority" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium">Assign to</label>
                    <select x-model="taskForm.assignedTo" class="w-full mt-1 px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600">
                        <option value="">Auto (lead dev)</option>
                        <template x-for="d in developers" :key="d._id">
                            <option :value="d._id" x-text="d.firstName + ' ' + d.lastName"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-2">
            <button @click="showTaskModal = false" class="px-4 py-2 text-sm rounded-lg border">Cancel</button>
            <button @click="saveTask()" class="px-4 py-2 text-sm rounded-lg bg-emerald-600 text-white">Submit</button>
        </div>
    </div>
</div>

<!-- Task Detail Modal -->
<div x-show="showTaskDetail" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6 space-y-4">
        <template x-if="selectedTask">
            <div class="space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="flex flex-wrap gap-2 mb-2">
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium" :class="typeBadge(selectedTask.type)" x-text="typeLabel(selectedTask.type)"></span>
                            <span class="text-xs px-2 py-0.5 rounded-full" :class="statusBadge(selectedTask.status)" x-text="statusLabel(selectedTask.status)"></span>
                        </div>
                        <h2 class="text-lg font-bold" x-text="selectedTask.title"></h2>
                        <p class="text-xs text-gray-500" x-text="selectedTask.projectId?.title"></p>
                    </div>
                    <button @click="showTaskDetail = false" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <p class="text-sm whitespace-pre-wrap" x-text="selectedTask.description || 'No description'"></p>
                <a x-show="selectedTask.pageUrl" :href="selectedTask.pageUrl" target="_blank" class="text-sm text-indigo-600 hover:underline block">View page ↗</a>

                <div x-show="canManageTasks" class="grid grid-cols-2 gap-3 p-3 bg-gray-50 dark:bg-gray-900/40 rounded-xl">
                    <div>
                        <label class="text-xs font-medium">Status</label>
                        <select x-model="taskUpdate.status" @change="updateTask()" class="w-full mt-1 px-2 py-1.5 border rounded-lg text-xs dark:bg-gray-700">
                            <template x-for="s in statusOptions" :key="s.value">
                                <option :value="s.value" x-text="s.label"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium">Assign to</label>
                        <select x-model="taskUpdate.assignedTo" @change="updateTask()" class="w-full mt-1 px-2 py-1.5 border rounded-lg text-xs dark:bg-gray-700">
                            <option value="">Unassigned</option>
                            <template x-for="d in developers" :key="d._id">
                                <option :value="d._id" x-text="d.firstName + ' ' + d.lastName"></option>
                            </template>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs font-medium">Resolution notes</label>
                        <textarea x-model="taskUpdate.resolutionNotes" @blur="updateTask()" rows="2" class="w-full mt-1 px-2 py-1.5 border rounded-lg text-xs dark:bg-gray-700" placeholder="What was fixed or delivered…"></textarea>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold mb-2">Comments</h3>
                    <div class="space-y-2 max-h-40 overflow-y-auto mb-3">
                        <template x-for="c in (selectedTask.comments || [])" :key="c._id">
                            <div class="text-sm p-2 rounded-lg bg-gray-50 dark:bg-gray-900/40">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <p class="text-xs text-gray-500" x-text="(c.user?.firstName || 'User') + ' · ' + formatDate(c.createdAt)"></p>
                                    <template x-if="canEditComment(c) && !(editingComment?.id === c._id)">
                                        <div class="flex items-center gap-1">
                                            <button type="button" @click="startEditComment(c)" class="text-xs text-indigo-600 hover:text-indigo-800">Edit</button>
                                            <button type="button" @click="deleteComment(c)" class="text-xs text-red-600 hover:text-red-800">Delete</button>
                                        </div>
                                    </template>
                                </div>
                                <div x-show="editingComment?.id !== c._id">
                                    <p x-text="c.text"></p>
                                </div>
                                <div x-show="editingComment?.id === c._id" class="space-y-2">
                                    <input x-model="editingComment.text" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700">
                                    <div class="flex gap-2 justify-end">
                                        <button type="button" @click="cancelEditComment()" class="px-2 py-1 text-xs border rounded-lg">Cancel</button>
                                        <button type="button" @click="saveEditComment()" class="px-2 py-1 text-xs bg-indigo-600 text-white rounded-lg">Save</button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="flex gap-2">
                        <input x-model="newComment" @keydown.enter.prevent="addComment()" class="flex-1 px-3 py-2 border rounded-lg text-sm dark:bg-gray-700" placeholder="Add a comment…">
                        <button @click="addComment()" class="px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg">Send</button>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold mb-2">Attachments</h3>
                    <div class="flex flex-wrap gap-2 mb-2">
                        <template x-for="file in (selectedTask.attachments || [])" :key="file._id">
                            <a :href="file.url" target="_blank" class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-lg hover:underline" x-text="file.originalName || 'File'"></a>
                        </template>
                    </div>
                    <input type="file" @change="uploadTaskFile($event)" class="text-xs">
                </div>
            </div>
        </template>
    </div>
</div>

<div x-show="toast.show" x-transition class="fixed bottom-20 md:bottom-6 right-4 z-[60] px-4 py-3 rounded-xl shadow-lg text-sm text-white max-w-sm"
     :class="{ 'bg-green-600': toast.type === 'success', 'bg-red-600': toast.type === 'error', 'bg-gray-800': toast.type === 'info' }"
     x-text="toast.message"></div>

<script>
function websiteProjectsApp() {
    return {
        authChecked: false,
        user: null,
        viewMode: localStorage.getItem('viewMode') || 'admin',
        isSidebarOpen: true,
        loading: true,
        tab: 'dashboard',
        stats: {},
        recentTasks: [],
        projects: [],
        tasks: [],
        myQueue: [],
        clients: [],
        developers: [],
        taskFilter: { status: '', type: '', projectId: '' },
        selectedProject: null,
        showProjectModal: false,
        showTaskModal: false,
        showTaskDetail: false,
        projectForm: {},
        taskForm: {},
        selectedTask: null,
        taskUpdate: {},
        newComment: '',
        editingComment: null,
        toast: { show: false, message: '', type: 'info' },

        typeOptions: [
            { value: 'bug', label: 'Bug report' },
            { value: 'change_request', label: 'Change request' },
            { value: 'content_update', label: 'Content update' },
            { value: 'design_feedback', label: 'Design feedback' },
            { value: 'maintenance', label: 'Maintenance' },
            { value: 'other', label: 'Other' }
        ],
        statusOptions: [
            { value: 'submitted', label: 'Submitted' },
            { value: 'acknowledged', label: 'Acknowledged' },
            { value: 'in_progress', label: 'In progress' },
            { value: 'waiting_on_client', label: 'Waiting on client' },
            { value: 'ready_for_review', label: 'Ready for review' },
            { value: 'completed', label: 'Completed' },
            { value: 'wont_fix', label: "Won't fix" },
            { value: 'cancelled', label: 'Cancelled' }
        ],

        get isClient() { return this.viewMode === 'client' || this.user?.role === 'client'; },
        get isWebsiteOnlyClient() {
            if (!this.isClient) return false;
            const brands = [this.user?.clientId, ...(this.user?.clientIds || [])].filter(Boolean);
            return brands.length > 0 && brands.every((c) => (c.serviceType || 'social_media') === 'website');
        },
        get canManageProjects() {
            return !this.isClient && (this.user?.role === 'admin' || this.user?.department === 'web_developer');
        },
        get canManageTasks() {
            return !this.isClient && (this.user?.role === 'admin' || this.user?.department === 'web_developer');
        },
        get canSubmitTasks() {
            return this.isClient || this.user?.role === 'admin' || this.user?.role === 'brand_rep';
        },
        get visibleTabs() {
            const tabs = [
                { id: 'dashboard', label: 'Dashboard' }
            ];
            if (!this.isClient || this.projects.length > 0) {
                tabs.push({ id: 'projects', label: this.isClient ? 'My Websites' : 'Projects' });
            }
            tabs.push({ id: 'tasks', label: this.isClient ? 'My Requests' : 'All Tasks' });
            if (this.canManageTasks) tabs.push({ id: 'queue', label: 'My Queue' });
            return tabs;
        },

        headers(json = true) {
            const h = { Authorization: `Bearer ${localStorage.getItem('token')}` };
            if (json) h['Content-Type'] = 'application/json';
            return h;
        },

        showToast(message, type = 'info') {
            this.toast = { show: true, message, type };
            setTimeout(() => { this.toast.show = false; }, 4000);
        },

        typeLabel(v) { return this.typeOptions.find(t => t.value === v)?.label || v; },
        statusLabel(v) { return this.statusOptions.find(s => s.value === v)?.label || v; },
        typeBadge(v) {
            const map = { bug: 'bg-red-100 text-red-800', change_request: 'bg-blue-100 text-blue-800', content_update: 'bg-green-100 text-green-800', design_feedback: 'bg-purple-100 text-purple-800', maintenance: 'bg-gray-100 text-gray-800', other: 'bg-gray-100 text-gray-700' };
            return map[v] || 'bg-gray-100 text-gray-700';
        },
        statusBadge(v) {
            if (v === 'completed') return 'bg-green-100 text-green-800';
            if (v === 'in_progress') return 'bg-blue-100 text-blue-800';
            if (v === 'waiting_on_client') return 'bg-amber-100 text-amber-800';
            if (v === 'ready_for_review') return 'bg-indigo-100 text-indigo-800';
            return 'bg-gray-100 text-gray-700';
        },
        priorityBadge(v) {
            if (v === 'urgent') return 'bg-red-100 text-red-800';
            if (v === 'high') return 'bg-orange-100 text-orange-800';
            return 'bg-gray-100 text-gray-600';
        },
        formatDate(d) { return d ? new Date(d).toLocaleString() : ''; },
        hostingLabel(v) {
            const map = { netlify: 'Netlify', railway: 'Railway', hostgator: 'HostGator', siteground: 'SiteGround' };
            return map[v] || v || '';
        },

        async init() {
            const token = localStorage.getItem('token');
            if (!token) { this.authChecked = true; return; }
            try {
                const res = await fetch(`${API_URL}/auth/me`, { headers: this.headers() });
                const data = await res.json();
                if (!data.success) { localStorage.removeItem('token'); this.authChecked = true; return; }
                this.user = data.user;
                this.viewMode = localStorage.getItem('viewMode') || (this.user.role === 'client' ? 'client' : 'admin');
                await this.loadAll();
                const params = new URLSearchParams(window.location.search);
                const taskId = params.get('task');
                if (taskId) await this.openTaskDetail(taskId);
            } catch (e) {
                console.error(e);
            } finally {
                this.authChecked = true;
            }
        },

        async loadAll() {
            this.loading = true;
            try {
                await Promise.all([
                    this.loadDashboard(),
                    this.loadProjects(),
                    this.canManageProjects ? this.loadClients() : Promise.resolve(),
                    !this.isClient ? this.loadDevelopers() : Promise.resolve()
                ]);
                if (this.tab === 'tasks') await this.loadTasks();
                if (this.tab === 'queue') await this.loadQueue();
            } finally {
                this.loading = false;
            }
        },

        async loadTab() {
            this.loading = true;
            try {
                if (this.tab === 'dashboard') await this.loadDashboard();
                if (this.tab === 'projects') await this.loadProjects();
                if (this.tab === 'tasks') await this.loadTasks();
                if (this.tab === 'queue') await this.loadQueue();
            } finally { this.loading = false; }
        },

        async loadDashboard() {
            const res = await fetch(`${API_URL}/website-projects/dashboard`, { headers: this.headers() });
            const data = await res.json();
            if (data.success) {
                this.stats = data.stats;
                this.recentTasks = data.recentTasks || [];
            }
        },

        async loadProjects() {
            const res = await fetch(`${API_URL}/website-projects/projects`, { headers: this.headers() });
            const data = await res.json();
            if (data.success) {
                this.projects = data.projects || [];
                this.syncClientNavStore();
            }
        },

        syncClientNavStore() {
            if (!this.isClient || typeof Alpine === 'undefined' || !Alpine.store('clientNav')) return;
            const store = Alpine.store('clientNav');
            store.hasWebsiteProjects = this.projects.length > 0;
            store.websiteProjectCount = this.projects.length;
            store.loaded = true;
        },

        async loadTasks() {
            const params = new URLSearchParams();
            if (this.taskFilter.status) params.set('status', this.taskFilter.status);
            if (this.taskFilter.type) params.set('type', this.taskFilter.type);
            if (this.taskFilter.projectId) params.set('projectId', this.taskFilter.projectId);
            const res = await fetch(`${API_URL}/website-projects/tasks?${params}`, { headers: this.headers() });
            const data = await res.json();
            if (data.success) this.tasks = data.tasks || [];
        },

        async loadQueue() {
            const res = await fetch(`${API_URL}/website-projects/tasks?mine=true`, { headers: this.headers() });
            const data = await res.json();
            if (data.success) this.myQueue = data.tasks || [];
        },

        async loadClients() {
            const res = await fetch(`${API_URL}/website-projects/clients`, { headers: this.headers() });
            const data = await res.json();
            if (data.success) this.clients = data.clients || [];
        },

        async loadDevelopers() {
            const res = await fetch(`${API_URL}/website-projects/developers`, { headers: this.headers() });
            const data = await res.json();
            if (data.success) this.developers = data.developers || [];
        },

        selectProject(project) {
            this.selectedProject = project;
            this.taskFilter.projectId = project._id;
        },

        openProjectModal(project = null) {
            this.projectForm = project ? { ...project, clientId: project.clientId?._id || project.clientId, leadDeveloper: project.leadDeveloper?._id || project.leadDeveloper || '', isInternal: !!project.isInternal } : { clientId: '', title: '', description: '', productionUrl: '', adminUrl: '', cmsType: 'wordpress', hostingProvider: '', leadDeveloper: '', isInternal: false, notes: '' };
            this.showProjectModal = true;
        },

        async saveProject() {
            if (!this.projectForm.clientId || !this.projectForm.title?.trim()) {
                this.showToast('Client and title are required', 'error'); return;
            }
            const isEdit = !!this.projectForm._id;
            const url = isEdit ? `${API_URL}/website-projects/projects/${this.projectForm._id}` : `${API_URL}/website-projects/projects`;
            const res = await fetch(url, { method: isEdit ? 'PUT' : 'POST', headers: this.headers(), body: JSON.stringify(this.projectForm) });
            const data = await res.json();
            if (data.success) {
                this.showProjectModal = false;
                this.showToast(isEdit ? 'Project updated' : 'Project created', 'success');
                await this.loadAll();
            } else {
                this.showToast(data.message || 'Failed to save project', 'error');
            }
        },

        openTaskModal(projectId = '') {
            if (!this.projects.length) {
                this.showToast(this.isClient ? 'No website project linked to your account yet. Contact your account manager.' : 'Create a website project first', 'error');
                return;
            }
            this.taskForm = {
                projectId: projectId || this.taskFilter.projectId || this.projects[0]?._id || '',
                type: 'bug',
                title: '',
                description: '',
                pageUrl: '',
                priority: 'medium',
                assignedTo: ''
            };
            this.showTaskModal = true;
        },

        async saveTask() {
            if (!this.taskForm.projectId || !this.taskForm.title?.trim()) {
                this.showToast('Project and title are required', 'error'); return;
            }
            const res = await fetch(`${API_URL}/website-projects/tasks`, { method: 'POST', headers: this.headers(), body: JSON.stringify(this.taskForm) });
            const data = await res.json();
            if (data.success) {
                this.showTaskModal = false;
                this.showToast(this.isClient ? 'Request submitted — you will be notified when it is completed' : 'Task created', 'success');
                await this.loadAll();
                this.tab = 'tasks';
                await this.loadTasks();
            } else {
                this.showToast(data.message || 'Failed to submit', 'error');
            }
        },

        async openTaskDetail(id) {
            const res = await fetch(`${API_URL}/website-projects/tasks/${id}`, { headers: this.headers() });
            const data = await res.json();
            if (!data.success) { this.showToast(data.message || 'Task not found', 'error'); return; }
            this.selectedTask = data.task;
            this.taskUpdate = {
                status: data.task.status,
                assignedTo: data.task.assignedTo?._id || data.task.assignedTo || '',
                resolutionNotes: data.task.resolutionNotes || ''
            };
            this.showTaskDetail = true;
        },

        async updateTask() {
            if (!this.selectedTask) return;
            const res = await fetch(`${API_URL}/website-projects/tasks/${this.selectedTask._id}`, {
                method: 'PUT', headers: this.headers(),
                body: JSON.stringify(this.taskUpdate)
            });
            const data = await res.json();
            if (data.success) {
                this.selectedTask = data.task;
                this.showToast('Task updated', 'success');
                await this.loadDashboard();
            } else {
                this.showToast(data.message || 'Update failed', 'error');
            }
        },

        async addComment() {
            if (!this.newComment.trim() || !this.selectedTask) return;
            const res = await fetch(`${API_URL}/website-projects/tasks/${this.selectedTask._id}/comments`, {
                method: 'POST', headers: this.headers(), body: JSON.stringify({ text: this.newComment })
            });
            const data = await res.json();
            if (data.success) {
                this.selectedTask = data.task;
                this.newComment = '';
            } else {
                this.showToast(data.message || 'Comment failed', 'error');
            }
        },

        canEditComment(item) {
            if (!this.user || !item?.user) return false;
            const authorId = item.user._id || item.user;
            const uid = String(this.user._id || this.user.id);
            return this.user.role === 'admin' || uid === String(authorId);
        },

        startEditComment(item) {
            this.editingComment = { id: item._id, text: item.text || '' };
        },

        cancelEditComment() {
            this.editingComment = null;
        },

        async saveEditComment() {
            if (!this.editingComment?.text?.trim() || !this.selectedTask) return;
            const res = await fetch(`${API_URL}/website-projects/tasks/${this.selectedTask._id}/comments/${this.editingComment.id}`, {
                method: 'PUT', headers: this.headers(), body: JSON.stringify({ text: this.editingComment.text.trim() })
            });
            const data = await res.json();
            if (data.success) {
                this.selectedTask = data.task;
                this.editingComment = null;
                this.showToast('Comment updated', 'success');
            } else {
                this.showToast(data.message || 'Update failed', 'error');
            }
        },

        async deleteComment(item) {
            if (!confirm('Delete this comment?') || !this.selectedTask) return;
            const res = await fetch(`${API_URL}/website-projects/tasks/${this.selectedTask._id}/comments/${item._id}`, {
                method: 'DELETE', headers: this.headers()
            });
            const data = await res.json();
            if (data.success) {
                this.selectedTask = data.task;
                if (this.editingComment?.id === item._id) this.editingComment = null;
                this.showToast('Comment deleted', 'success');
            } else {
                this.showToast(data.message || 'Delete failed', 'error');
            }
        },

        async uploadTaskFile(event) {
            const file = event.target.files?.[0];
            if (!file || !this.selectedTask) return;
            const form = new FormData();
            form.append('file', file);
            const res = await fetch(`${API_URL}/website-projects/tasks/${this.selectedTask._id}/attachments`, {
                method: 'POST', headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }, body: form
            });
            const data = await res.json();
            if (data.success) {
                this.selectedTask = data.task;
                this.showToast('File uploaded', 'success');
            } else {
                this.showToast(data.message || 'Upload failed', 'error');
            }
            event.target.value = '';
        }
    };
}
</script>
</body>
</html>
