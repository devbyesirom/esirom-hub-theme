<?php
/**
 * Template Name: AI Studio
 * Description: Product-safe AI images and on-voice captions — staff only (feature-flagged)
 */
if (!defined('ABSPATH')) exit;
require_once get_template_directory() . '/inc/ai-knowledge-editor.php';

show_admin_bar(false);

$api_url = get_option('esirom_api_url', 'https://esirom-hub-backend-production.up.railway.app/api');
$login_url = esc_url(esirom_hub_page_url('login'));
$dashboard_url = esc_url(esirom_hub_page_url('dashboard'));
$admin_url = esc_url(esirom_hub_page_url('admin'));
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <title>AI Studio — Agency Hub</title>
    <?php esirom_hub_head_assets(array('pwa' => true)); ?>
    <script src="<?php echo esc_url(get_template_directory_uri() . '/js/ai-knowledge.js?ver=' . rawurlencode(wp_get_theme()->get('Version'))); ?>"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        const API_URL = '<?php echo esc_js($api_url); ?>';
        const LOGIN_URL = '<?php echo esc_js($login_url); ?>';
        const DASHBOARD_URL = '<?php echo esc_js($dashboard_url); ?>';
        const ADMIN_URL = '<?php echo esc_js($admin_url); ?>';
    </script>
    <style>
        <?php esirom_hub_layout_styles(); ?>
    </style>
</head>
<body class="hub-has-mobile-nav h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 pb-16 md:pb-0" x-data="Object.assign(aiKnowledgeEditor('staff'), aiStudioApp())" x-init="init()">

<div x-show="!authChecked" class="hub-boot" aria-busy="true">
    <span class="hub-spinner hub-spinner--lg"></span>
    <span>Loading…</span>
</div>

<div x-show="authChecked && !user" x-cloak class="hub-boot">
    <div class="hub-card hub-card--elevated p-8 text-center max-w-sm w-full mx-4">
        <p class="hub-empty__title">You need to sign in</p>
        <p class="hub-empty__text mt-1" x-text="loadError || 'Your session has ended or could not be verified.'"></p>
        <a href="<?php echo $login_url; ?>" class="hub-btn hub-btn--primary hub-btn--block mt-5">Go to sign in</a>
    </div>
</div>

<div x-show="authChecked && user" x-cloak class="hub-app-shell flex flex-col md:flex-row">
    <aside :class="isSidebarOpen ? 'sidebar-expanded' : 'sidebar-collapsed'" class="hub-app-sidebar sidebar hidden md:flex border-r border-gray-200 dark:border-gray-700/50 flex-col flex-shrink-0">
        <?php esirom_hub_staff_sidebar_header(false); ?>
        <nav class="flex-1 px-3 py-4 space-y-3 overflow-y-auto" aria-label="Sections">
            <?php esirom_hub_staff_sidebar_nav('ai_studio', 'site', false); ?>
        </nav>
        <div class="p-3 border-t border-gray-200 dark:border-gray-700/50">
            <?php esirom_hub_staff_sidebar_footer('site', false); ?>
        </div>
    </aside>

    <main class="hub-app-main">
        <header class="hub-sticky-header px-4 sm:px-6 py-3">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="min-w-0 flex-1">
                    <h1 class="hub-page-title">AI Studio <span class="hub-pill ml-1 text-[11px] align-middle px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-200">Beta</span></h1>
                    <p class="hub-page-subtitle hidden sm:block">Scenes around real product photos, and captions in each brand's voice. Nothing is posted automatically.</p>
                </div>
                <div class="flex items-center gap-2 sm:gap-3 ml-auto">
                    <!-- Compact metrics -->
                    <div x-show="status.enabled && brand" x-cloak class="flex items-center gap-3 sm:gap-4 rounded-full border border-gray-200 dark:border-gray-700 bg-white/70 dark:bg-gray-800/70 px-3 py-1.5" data-metrics>
                        <div class="flex items-center gap-1.5" :title="'Images this month: ' + metricImages() + '. ' + allowanceLabel()">
                            <svg class="w-7 h-7 -rotate-90" viewBox="0 0 36 36" role="img" :aria-label="'Images this month ' + metricImages()">
                                <circle cx="18" cy="18" r="15" fill="none" stroke-width="4" class="stroke-gray-200 dark:stroke-gray-700"></circle>
                                <circle cx="18" cy="18" r="15" fill="none" stroke-width="4" stroke-linecap="round" :class="ringColour(budget.usage.finished, budget.allowance.total)" :stroke-dasharray="ringDash(budget.usage.finished, budget.allowance.total)"></circle>
                            </svg>
                            <div class="leading-tight"><p class="text-xs font-semibold tabular-nums" x-text="metricImages()"></p><p class="text-[10px] text-gray-500">images</p></div>
                        </div>
                        <div class="flex items-center gap-1.5" :title="'Your requests today: ' + budget.userToday + ' of ' + budget.staffDailyCap + '. Resets at midnight (Jamaica).'">
                            <svg class="w-7 h-7 -rotate-90" viewBox="0 0 36 36" role="img" :aria-label="'Your requests today ' + budget.userToday + ' of ' + budget.staffDailyCap">
                                <circle cx="18" cy="18" r="15" fill="none" stroke-width="4" class="stroke-gray-200 dark:stroke-gray-700"></circle>
                                <circle cx="18" cy="18" r="15" fill="none" stroke-width="4" stroke-linecap="round" :class="ringColour(budget.userToday, budget.staffDailyCap)" :stroke-dasharray="ringDash(budget.userToday, budget.staffDailyCap)"></circle>
                            </svg>
                            <div class="leading-tight"><p class="text-xs font-semibold tabular-nums" x-text="(budget.userToday || 0) + '/' + (budget.staffDailyCap || 0)"></p><p class="text-[10px] text-gray-500">today</p></div>
                        </div>
                        <div class="hidden sm:block leading-tight" :title="'Previews left this month: ' + budget.previewsLeft + ' of ' + budget.previewCap">
                            <p class="text-xs font-semibold tabular-nums" x-text="(budget.previewsLeft ?? 0)"></p><p class="text-[10px] text-gray-500">previews left</p>
                        </div>
                        <div class="leading-tight" :title="budget.paused ? 'Paused by an admin' : 'Estimated cost this month, this brand'">
                            <p class="text-xs font-semibold tabular-nums" x-text="'US$' + ((budget.usage && budget.usage.costUsd) || 0).toFixed(2)"></p><p class="text-[10px]" :class="budget.paused ? 'text-amber-600' : 'text-gray-500'" x-text="budget.paused ? 'paused' : 'est. cost'"></p>
                        </div>
                    </div>
                    <div class="relative hidden sm:block" x-data="{ open: false }" x-show="status.enabled">
                        <button type="button" @click="open = !open" class="hub-btn hub-btn--soft hub-btn--sm" :aria-expanded="open">
                            <span x-text="brand ? brand.name : 'Select company'"></span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-72 hub-card hub-card--elevated z-50 max-h-80 overflow-y-auto py-1">
                            <template x-for="b in brands" :key="b._id">
                                <button type="button" @click="selectBrand(b._id); open = false" class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 flex justify-between gap-2" :class="brand && brand._id === b._id ? 'font-semibold text-brand-700 dark:text-brand-200' : 'text-gray-700 dark:text-gray-200'">
                                    <span x-text="b.name"></span>
                                    <span class="text-xs text-gray-400" x-text="b.paused ? 'Paused' : (b.finished + '/' + b.allowance)"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <?php esirom_hub_account_menu(); ?>
                </div>
            </div>
            <div class="hub-segmented mt-3" role="tablist" x-show="status.enabled">
                <template x-for="t in tabs()" :key="t.id">
                    <button type="button" role="tab" @click="tab = t.id; if (t.id === 'usage') loadUsage()" :aria-selected="tab === t.id" class="hub-segmented__item" :class="tab === t.id ? 'is-active' : ''" x-text="t.label"></button>
                </template>
            </div>
        </header>

        <div class="hub-page-content p-4 sm:p-6 space-y-4">
            <div x-show="toast.show" x-cloak x-transition class="fixed bottom-20 md:bottom-6 right-4 z-[100] max-w-sm px-4 py-3 rounded-xl shadow-lg text-sm text-white" :class="toast.type === 'error' ? 'bg-red-600' : 'bg-emerald-600'" x-text="toast.message"></div>

            <!-- Not available / not configured -->
            <div x-show="statusLoaded && !status.enabled" x-cloak class="hub-card p-8 text-center max-w-lg mx-auto">
                <p class="hub-empty__title">AI Studio isn't available for your account yet</p>
                <p class="hub-empty__text mt-1">It's being trialled by admins first. Ask an admin if you need access.</p>
            </div>
            <div x-show="status.enabled && !status.configured && tab !== 'create'" x-cloak class="rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-4 text-sm text-amber-900 dark:text-amber-100" data-testid="not-configured-banner">
                <p class="font-semibold">AI Studio is not configured yet</p>
                <p class="mt-1">The Gemini API key hasn't been added to the Hub backend, so images and captions can't be generated. You can still set up brand profiles and product libraries.</p>
            </div>

            <div x-show="status.enabled && !brand && tab !== 'create'" x-cloak class="hub-card p-8 text-center text-sm text-gray-500">Select a company to get started.</div>

            <!-- CREATE: conversational canvas -->
            <section x-show="status.enabled && tab === 'create'" x-cloak class="ais-create flex flex-col" aria-label="Create">
                <div id="ais-feed" class="flex-1 space-y-6 pb-4" role="log" aria-live="polite" aria-relevant="additions">
                    <!-- Empty feed -->
                    <div x-show="!generations.length" class="text-center py-16 max-w-md mx-auto">
                        <div class="mx-auto w-12 h-12 rounded-2xl bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-300 flex items-center justify-center" aria-hidden="true">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l1.9 5.6L19.5 9.5l-5.6 1.9L12 17l-1.9-5.6L4.5 9.5l5.6-1.9L12 2zm7 11l.9 2.6 2.6.9-2.6.9L19 20l-.9-2.6-2.6-.9 2.6-.9L19 13z"/></svg>
                        </div>
                        <p class="mt-3 font-semibold" x-text="readyToGenerate() ? 'What scene should we put ' + productName() + ' in?' : 'Pick a client-approved brand and product to begin'"></p>
                        <p class="text-sm text-gray-500 mt-1">Describe a background below. Choose a preview and three captions in the brand's voice appear underneath. Nothing is posted automatically.</p>
                        <div class="mt-4 flex flex-wrap justify-center gap-2" x-show="readyToGenerate() && brandTemplates().length">
                            <template x-for="(t, i) in brandTemplates()" :key="i">
                                <button type="button" class="text-xs px-3 py-1.5 rounded-full border border-gray-200 dark:border-gray-700 hover:border-indigo-400" @click="useTemplate(i)" x-text="t.name"></button>
                            </template>
                        </div>
                    </div>

                    <template x-for="(g, gi) in generations" :key="g.key">
                        <article class="space-y-3 scroll-mb-48 scroll-mt-40" :aria-label="'Generation ' + (gi + 1)">
                            <!-- Prompt (user message) -->
                            <div class="flex justify-end">
                                <div class="max-w-[85%] sm:max-w-[70%] rounded-2xl rounded-br-md bg-indigo-600 text-white px-4 py-2.5 shadow-sm">
                                    <p class="text-sm whitespace-pre-wrap break-words" x-text="g.prompt"></p>
                                    <p class="text-[11px] opacity-80 mt-1" x-text="[g.productName, formatLabel(g.aspectRatio), placementLabel(g.placement), g.previews + (g.previews === 1 ? ' preview' : ' previews')].filter(Boolean).join(' · ')"></p>
                                </div>
                            </div>

                            <!-- Result (assistant message) -->
                            <div class="flex gap-3">
                                <div class="hidden sm:flex w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-300 items-center justify-center flex-shrink-0" aria-hidden="true">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l1.9 5.6L19.5 9.5l-5.6 1.9L12 17l-1.9-5.6L4.5 9.5l5.6-1.9L12 2z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0 space-y-3">
                                    <p x-show="g.loading" class="text-xs text-gray-500" role="status">Creating scenes and placing the real product photo… this can take up to a minute.</p>
                                    <p x-show="g.error" class="text-sm text-red-600 dark:text-red-400" x-text="g.error"></p>

                                    <!-- Gallery -->
                                    <div class="grid gap-3" :class="galleryCols(g)">
                                        <template x-if="g.loading">
                                            <template x-for="n in g.previews" :key="n">
                                                <div class="ais-shimmer rounded-xl" :class="aspectClass(g.aspectRatio)" aria-hidden="true"></div>
                                            </template>
                                        </template>
                                        <template x-for="(p, i) in (g.gen ? g.gen.previews : [])" :key="i">
                                            <figure class="rounded-xl overflow-hidden border bg-white dark:bg-gray-800 transition" :class="g.gen.chosenIndex === i ? 'border-indigo-500 ring-2 ring-indigo-500/40' : 'border-gray-200 dark:border-gray-700'">
                                                <template x-if="!p.blocked">
                                                    <button type="button" class="block w-full focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500" @click="choose(g, i)" :disabled="g.choosing || g.sent" :aria-pressed="g.gen.chosenIndex === i" :aria-label="'Choose preview ' + (i + 1)">
                                                        <img :src="p.url" :alt="'Preview ' + (i + 1) + ' of ' + g.productName" class="w-full object-cover" :class="aspectClass(g.aspectRatio)" loading="lazy">
                                                    </button>
                                                </template>
                                                <template x-if="p.blocked">
                                                    <div class="flex items-center justify-center p-3 text-xs text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/20" :class="aspectClass(g.aspectRatio)" x-text="'Blocked: ' + (p.blockReason || 'product check failed')"></div>
                                                </template>
                                                <figcaption class="px-2.5 py-2 flex items-center justify-between gap-2">
                                                    <span class="text-[11px]" :class="p.productCheck && p.productCheck.passed ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600'" x-text="p.productCheck && p.productCheck.passed ? '✓ Product unaltered' : 'Product check failed'"></span>
                                                    <button x-show="!p.blocked" type="button" class="text-xs font-semibold px-2.5 py-1 rounded-full" :class="g.gen.chosenIndex === i ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 hover:bg-indigo-50 dark:hover:bg-indigo-500/20'" @click="choose(g, i)" :disabled="g.choosing || g.sent" x-text="g.gen.chosenIndex === i ? 'Chosen' : 'Choose'"></button>
                                                </figcaption>
                                            </figure>
                                        </template>
                                    </div>
                                    <p x-show="g.gen && g.gen.chosenIndex === null && !g.loading" class="text-xs text-gray-500">Choose a preview to write captions. Only the one you choose counts towards the brand's monthly images.</p>

                                    <!-- Captions, revealed after choosing -->
                                    <div x-show="g.cap.loading || g.cap.gen" x-transition class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3 sm:p-4 space-y-3">
                                        <div class="flex items-center justify-between gap-2">
                                            <h3 class="text-sm font-semibold">Captions</h3>
                                            <button type="button" x-show="g.cap.gen && !g.sent" class="text-xs text-indigo-600 dark:text-indigo-300 hover:underline" @click="writeCaptions(g)" :disabled="g.cap.loading">Write 3 more</button>
                                        </div>
                                        <template x-if="g.cap.loading">
                                            <div class="space-y-3" aria-hidden="true">
                                                <template x-for="n in 3" :key="n"><div class="space-y-2"><div class="ais-shimmer h-3 rounded w-11/12"></div><div class="ais-shimmer h-3 rounded w-9/12"></div><div class="ais-shimmer h-3 rounded w-6/12"></div></div></template>
                                            </div>
                                        </template>
                                        <p x-show="g.cap.error" class="text-xs text-red-600" x-text="g.cap.error"></p>
                                        <template x-for="(opt, ci) in (g.cap.gen && !g.cap.loading ? g.cap.edits : [])" :key="ci">
                                            <div class="rounded-xl border p-2.5 space-y-2" :class="g.cap.approvedIndex === ci ? 'border-indigo-500 bg-indigo-50/60 dark:bg-indigo-500/10' : 'border-gray-200 dark:border-gray-700'">
                                                <label class="sr-only" :for="'cap-' + g.key + '-' + ci" x-text="'Caption option ' + (ci + 1)"></label>
                                                <textarea :id="'cap-' + g.key + '-' + ci" x-model="g.cap.edits[ci]" rows="3" class="w-full bg-transparent text-sm resize-y focus:outline-none" :disabled="g.sent" @input="if (g.cap.approvedIndex === ci) g.cap.approvedIndex = null"></textarea>
                                                <p x-show="(g.cap.flags[ci] || []).length" class="text-xs text-red-600" x-text="'Contains words to avoid: ' + (g.cap.flags[ci] || []).join(', ')"></p>
                                                <div class="flex justify-end">
                                                    <button type="button" class="text-xs font-semibold px-3 py-1 rounded-full" :class="g.cap.approvedIndex === ci ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 hover:bg-indigo-50 dark:hover:bg-indigo-500/20'" :disabled="g.sent" @click="approveCaption(g, ci)" x-text="g.cap.approvedIndex === ci ? '✓ Using this caption' : 'Use this caption'"></button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Final pair + send -->
                                    <div x-show="g.gen && g.gen.chosenIndex !== null && g.cap.approvedIndex !== null" x-transition class="rounded-2xl border border-indigo-200 dark:border-indigo-500/30 bg-indigo-50/70 dark:bg-indigo-500/10 p-3 flex gap-3 items-center">
                                        <img :src="chosenUrl(g)" alt="Chosen image" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                                        <p class="text-xs text-gray-700 dark:text-gray-200 line-clamp-3 flex-1 min-w-0" x-text="g.cap.approvedIndex !== null ? g.cap.edits[g.cap.approvedIndex] : ''"></p>
                                        <button type="button" x-show="!g.sent" class="hub-btn hub-btn--primary hub-btn--sm flex-shrink-0" :disabled="g.sending" @click="sendToReview(g)"><span x-text="g.sending ? 'Sending…' : 'Send to review'"></span></button>
                                        <span x-show="g.sent" class="text-xs font-semibold text-emerald-700 dark:text-emerald-300 flex-shrink-0">✓ In Workflow for review</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </template>
                </div>

                <!-- Sticky prompt bar -->
                <div class="ais-bar sticky z-30 -mx-4 sm:-mx-6 px-4 sm:px-6 pt-2 pb-3 bg-gradient-to-t from-gray-50 via-gray-50/95 to-gray-50/0 dark:from-gray-900 dark:via-gray-900/95 dark:to-gray-900/0">
                    <div class="max-w-4xl mx-auto">
                        <p x-show="brand && !budget.canGenerate && (budget.reasons || []).length" class="mb-2 text-xs text-amber-800 dark:text-amber-200 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-lg px-3 py-1.5" x-text="(budget.reasons || [])[0] ? budget.reasons[0].message : ''" role="status"></p>
                        <p x-show="!status.configured" class="mb-2 text-xs text-amber-800 dark:text-amber-200 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-lg px-3 py-1.5" data-testid="not-configured">Generation is off until the Gemini API key and credits are in place. You can still set up brand knowledge.</p>
                        <form @submit.prevent="generateImages()" class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-500/20">
                            <!-- Pills -->
                            <div class="flex flex-wrap items-center gap-1.5 px-3 pt-2.5">
                                <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                                    <button type="button" class="ais-pill" :class="readyToGenerate() ? '' : 'ais-pill--attention'" @click="open = !open" :aria-expanded="open" aria-haspopup="true" data-pill="brand">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5a1.99 1.99 0 011.41.59l7 7a2 2 0 010 2.82l-7 7a2 2 0 01-2.82 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                                        <span class="truncate max-w-[14rem]" x-text="brandPillLabel()"></span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="open" x-cloak @click.away="open = false" class="absolute bottom-full mb-2 left-0 w-[min(22rem,calc(100vw-2rem))] hub-card hub-card--elevated z-50 max-h-[60vh] overflow-y-auto p-2 text-sm" role="dialog" aria-label="Choose company, brand and product">
                                        <p class="px-2 pt-1 pb-1 text-[11px] font-semibold uppercase tracking-wide text-gray-400">Company</p>
                                        <select class="hub-input w-full" :value="brand ? brand._id : ''" @change="selectBrand($event.target.value)" aria-label="Company">
                                            <option value="" disabled>Select company…</option>
                                            <template x-for="b in brands" :key="b._id"><option :value="b._id" :selected="brand && brand._id === b._id" x-text="b.name"></option></template>
                                        </select>
                                        <p class="px-2 pt-3 pb-1 text-[11px] font-semibold uppercase tracking-wide text-gray-400">Brand</p>
                                        <div class="space-y-0.5">
                                            <template x-for="b in kTree.brands" :key="b._id">
                                                <button type="button" class="w-full text-left px-2 py-1.5 rounded-lg flex justify-between gap-2" :class="form.brandId === b._id ? 'bg-indigo-50 dark:bg-indigo-500/10 font-semibold' : 'hover:bg-gray-50 dark:hover:bg-gray-700'" :disabled="!b.approved" @click="form.brandId = b._id; form.templateIndex = ''; pickFirstProduct()">
                                                    <span x-text="b.approved ? b.approved.name : kData(b).name"></span>
                                                    <span x-show="!b.approved" class="text-[10px] text-amber-700 dark:text-amber-300">Awaiting client approval</span>
                                                </button>
                                            </template>
                                            <p x-show="brand && !kTree.brands.length" class="px-2 text-xs text-gray-500">No brands yet. Add them in <button type="button" class="underline" @click="tab = 'knowledge'; open = false">Brand knowledge</button>.</p>
                                        </div>
                                        <div x-show="form.brandId">
                                            <p class="px-2 pt-3 pb-1 text-[11px] font-semibold uppercase tracking-wide text-gray-400">Product</p>
                                            <div class="grid grid-cols-3 gap-1.5">
                                                <template x-for="p in brandProducts()" :key="p._id">
                                                    <button type="button" class="rounded-lg border p-1 text-[11px] text-left disabled:opacity-50" :class="form.productId === p._id ? 'border-indigo-500 ring-2 ring-indigo-500/30' : 'border-gray-200 dark:border-gray-700'" :disabled="!(p.approved && (p.approved.images || []).length)" @click="form.productId = p._id; form.imageIndex = 0; open = false" :title="!p.approved ? 'Awaiting client approval' : (!(p.approved.images || []).length ? 'No approved cut-out' : '')">
                                                        <img x-show="((p.approved || {}).images || []).length" :src="(((p.approved || {}).images || [])[0] || {}).url" alt="" class="h-12 w-full object-contain checker rounded">
                                                        <div x-show="!((p.approved || {}).images || []).length" class="h-12 flex items-center justify-center text-[10px] text-gray-400 text-center" x-text="p.approved ? 'No cut-out' : 'Awaiting approval'"></div>
                                                        <span class="block truncate mt-1" x-text="kData(p).name"></span>
                                                    </button>
                                                </template>
                                            </div>
                                            <p x-show="!brandProducts().length" class="px-2 text-xs text-gray-500">No products for this brand yet.</p>
                                        </div>
                                    </div>
                                </div>
                                <label class="sr-only" for="ais-format">Format</label>
                                <select id="ais-format" x-model="form.aspectRatio" class="ais-pill ais-pill--select" data-pill="format"><option value="1:1">Square 1:1</option><option value="4:5">Portrait 4:5</option><option value="9:16">Story 9:16</option><option value="16:9">Wide 16:9</option></select>
                                <label class="sr-only" for="ais-pos">Position</label>
                                <select id="ais-pos" x-model="form.placement" class="ais-pill ais-pill--select" data-pill="position"><option value="centre">Centre</option><option value="left">Left</option><option value="right">Right</option></select>
                                <label class="sr-only" for="ais-prev">Previews</label>
                                <select id="ais-prev" x-model.number="form.previews" class="ais-pill ais-pill--select" data-pill="previews"><option :value="1">1 preview</option><option :value="2">2 previews</option><option :value="3">3 previews</option></select>
                                <template x-if="brandTemplates().length">
                                    <select x-model="form.templateIndex" class="ais-pill ais-pill--select" aria-label="Scene template"><option value="">No template</option><template x-for="(t, i) in brandTemplates()" :key="i"><option :value="i" x-text="t.name"></option></template></select>
                                </template>
                            </div>
                            <!-- Input -->
                            <div class="relative px-3 pb-2.5 pt-1.5">
                                <label for="ais-scene" class="sr-only">Scene description</label>
                                <textarea id="ais-scene" x-ref="scene" x-model="form.scene" rows="1" maxlength="800"
                                          :disabled="!readyToGenerate()"
                                          :placeholder="readyToGenerate() ? 'Describe the background scene...' : 'Select a client-approved brand to begin'"
                                          @input="autosize($event.target)"
                                          @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); generateImages(); }"
                                          class="block w-full resize-none bg-transparent pr-14 py-2 text-[15px] leading-6 max-h-48 focus:outline-none disabled:cursor-not-allowed placeholder:text-gray-400"></textarea>
                                <button type="submit" class="absolute right-3 bottom-3 w-10 h-10 rounded-full flex items-center justify-center text-white bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 dark:disabled:bg-gray-600 shadow focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2" :disabled="!canSubmit()" :aria-label="busy.image ? 'Generating' : 'Generate previews'" data-generate>
                                    <svg x-show="!busy.image" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l1.9 5.6L19.5 9.5l-5.6 1.9L12 17l-1.9-5.6L4.5 9.5l5.6-1.9L12 2zm7 11l.9 2.6 2.6.9-2.6.9L19 20l-.9-2.6-2.6-.9 2.6-.9L19 13z"/></svg>
                                    <span x-show="busy.image" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                        <p class="mt-1.5 text-center text-[11px] text-gray-500 dark:text-gray-400">The AI only creates the background. Your real product photo is placed on top and checked pixel by pixel, so packaging, colours and text never change.</p>
                    </div>
                </div>
            </section>


            <template x-if="status.enabled && brand">
            <div class="space-y-4">
                <!-- KNOWLEDGE -->
                <div x-show="tab === 'knowledge'">
                    <?php esirom_hub_ai_knowledge_editor(); ?>
                </div>

                <!-- USAGE (admin) -->
                <div x-show="tab === 'usage'" class="space-y-4">
                    <div class="hub-card p-4 space-y-3" x-show="status.isAdmin">
                        <h2 class="font-semibold" x-text="'Image budget for ' + brand.name + ' (shared by all its brands)'"></h2>
                        <div class="flex flex-wrap gap-3 items-end">
                            <div><label class="text-xs text-gray-500">Monthly images (blank = client allocation)</label><input type="number" min="0" x-model="budgetForm.monthlyImageAllowance" class="hub-input mt-1 w-40"></div>
                            <button class="hub-btn hub-btn--soft hub-btn--sm" @click="saveBudget({ monthlyImageAllowance: budgetForm.monthlyImageAllowance === '' ? null : budgetForm.monthlyImageAllowance })">Save</button>
                            <button class="hub-btn hub-btn--soft hub-btn--sm" @click="saveBudget({ topUp: 5 })">Top up +5 this month</button>
                            <button class="hub-btn hub-btn--sm" :class="budget.paused ? 'hub-btn--primary' : 'hub-btn--soft'" @click="saveBudget({ paused: !budget.paused })" x-text="budget.paused ? 'Resume brand' : 'Pause brand'"></button>
                        </div>
                    </div>
                    <div class="hub-card p-4 overflow-x-auto" x-show="usage">
                        <div class="flex justify-between items-baseline"><h2 class="font-semibold" x-text="'Usage, ' + (usage ? usage.monthKey : '')"></h2><p class="text-xs text-gray-500" x-text="usage ? ('Hub total: ' + usage.totals.previews + ' / ' + usage.monthlyHardStop + ' generations · US$' + usage.totals.costUsd.toFixed(2)) : ''"></p></div>
                        <h3 class="text-sm font-medium mt-3">By brand</h3>
                        <table class="w-full text-sm mt-1"><thead><tr class="text-left text-xs text-gray-500"><th>Brand</th><th>Images</th><th>Previews</th><th>Captions</th><th title="Inbox reply suggestions — separate from the image allowance">Reply suggestions (sent / flagged)</th><th>Blocked</th><th>Est. cost</th></tr></thead>
                            <tbody><template x-for="r in usage ? usage.byBrand : []" :key="r.clientId"><tr class="border-t border-gray-100 dark:border-gray-700"><td x-text="r.name + (r.paused ? ' (paused)' : '')"></td><td x-text="r.finished + ' / ' + r.allowance"></td><td x-text="r.previews"></td><td x-text="r.captionRequests"></td><td x-text="(r.replyRequests || 0) + ' (' + (r.repliesSent || 0) + ' / ' + (r.sensitiveFlagged || 0) + ')'"></td><td x-text="r.blocked"></td><td x-text="'US$' + r.costUsd.toFixed(2)"></td></tr></template></tbody></table>
                        <h3 class="text-sm font-medium mt-4">By person</h3>
                        <table class="w-full text-sm mt-1"><thead><tr class="text-left text-xs text-gray-500"><th>Person</th><th>Image requests</th><th>Previews</th><th>Chosen</th><th>Captions</th><th>Reply suggestions</th><th>Est. cost</th></tr></thead>
                            <tbody><template x-for="r in usage ? usage.byPerson : []" :key="r.userId"><tr class="border-t border-gray-100 dark:border-gray-700"><td x-text="r.name"></td><td x-text="r.imageRequests"></td><td x-text="r.previews"></td><td x-text="r.finished"></td><td x-text="r.captionRequests"></td><td x-text="r.replyRequests || 0"></td><td x-text="'US$' + r.costUsd.toFixed(2)"></td></tr></template></tbody></table>
                        <p class="text-[11px] text-gray-400 mt-3" x-text="'Models: ' + status.models.image + ' (2K images), ' + status.models.caption + ' (captions). Costs are estimates from Google list prices.'"></p>
                    </div>
                </div>
            </div>
            </template>
        </div>
    </main>
</div>
<style>
.ais-pill{display:inline-flex;align-items:center;gap:.35rem;height:1.875rem;padding:0 .75rem;border-radius:9999px;font-size:.75rem;font-weight:600;border:1px solid rgb(229 231 235);background:rgb(249 250 251);color:rgb(55 65 81);cursor:pointer}
.ais-pill:hover{border-color:rgb(165 180 252)}
.ais-pill:focus-visible{outline:2px solid #6366f1;outline-offset:2px}
.ais-pill--select{appearance:none;-webkit-appearance:none;padding-right:1.75rem;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right .5rem center;background-size:.75rem}
.ais-pill--attention{border-color:rgb(165 180 252);background:rgb(238 242 255);color:rgb(67 56 202)}
.dark .ais-pill{border-color:rgb(55 65 81);background:rgb(31 41 55);color:rgb(229 231 235)}
.dark .ais-pill--attention{border-color:rgb(99 102 241 / .5);background:rgb(99 102 241 / .15);color:rgb(199 210 254)}
.ais-shimmer{background:linear-gradient(90deg,rgb(229 231 235) 0%,rgb(243 244 246) 40%,rgb(229 231 235) 80%);background-size:200% 100%;animation:ais-shimmer 1.4s ease-in-out infinite}
.dark .ais-shimmer{background:linear-gradient(90deg,rgb(31 41 55) 0%,rgb(55 65 81) 40%,rgb(31 41 55) 80%);background-size:200% 100%}
@keyframes ais-shimmer{0%{background-position:100% 0}100%{background-position:-100% 0}}
@media (prefers-reduced-motion:reduce){.ais-shimmer{animation:none}}
.ais-create{min-height:calc(100dvh - 9.5rem)}
.ais-bar{bottom:4rem}
@media (min-width:768px){.ais-bar{bottom:0}}
.checker{background-image:linear-gradient(45deg,#e5e7eb 25%,transparent 25%),linear-gradient(-45deg,#e5e7eb 25%,transparent 25%),linear-gradient(45deg,transparent 75%,#e5e7eb 75%),linear-gradient(-45deg,transparent 75%,#e5e7eb 75%);background-size:12px 12px;background-position:0 0,0 6px,6px -6px,-6px 0}</style>

<script>
function aiStudioApp() {
    return {
        authChecked: false, loadError: '', user: null,
        showPwModal: false, pwCurrent: '', pwNew: '', pwConfirm: '', pwLoading: false, pwError: '', pwSuccess: '',
        async changePassword() {
            this.pwError = ''; this.pwSuccess = '';
            if (!this.pwCurrent || !this.pwNew || !this.pwConfirm) { this.pwError = 'All fields are required.'; return; }
            if (this.pwNew.length < 6) { this.pwError = 'Your new password must be at least 6 characters.'; return; }
            if (this.pwNew !== this.pwConfirm) { this.pwError = 'The new passwords do not match.'; return; }
            this.pwLoading = true;
            try {
                const res = await fetch(`${API_URL}/auth/update-password`, {
                    method: 'PUT',
                    headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}`, 'Content-Type': 'application/json' },
                    body: JSON.stringify({ currentPassword: this.pwCurrent, newPassword: this.pwNew })
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) throw new Error(data.message || 'Failed to update password');
                if (data.token) localStorage.setItem('token', data.token);
                this.pwSuccess = 'Password updated.';
                this.pwCurrent = ''; this.pwNew = ''; this.pwConfirm = '';
                setTimeout(() => { this.showPwModal = false; this.pwSuccess = ''; }, 1500);
            } catch (e) { this.pwError = e.message; } finally { this.pwLoading = false; }
        },
        viewMode: 'admin', isSidebarOpen: true,
        statusLoaded: false,
        status: { enabled: false, configured: false, models: {}, isAdmin: false },
        tab: 'create', brands: [], brand: null, profile: { sceneTemplates: [], products: [] },
        profileText: { doWords: '', dontWords: '', hashtags: '' },
        budget: { usage: { finished: 0, costUsd: 0 }, allowance: { total: 0 }, reasons: [], canGenerate: false },
        budgetForm: { monthlyImageAllowance: '' },
        examples: [], usage: null,
        form: { brandId: '', productId: '', imageIndex: 0, templateIndex: '', scene: '', aspectRatio: '1:1', placement: 'centre', previews: 3 },
        generations: [], historyLoaded: false,
        newProduct: { name: '' },
        busy: {}, toast: { show: false, message: '', type: 'success' },

        tabs() {
            const t = [{ id: 'create', label: 'Create' }, { id: 'knowledge', label: 'Brand knowledge' }];
            if (this.status.isAdmin) t.push({ id: 'usage', label: 'Usage & budgets' });
            return t;
        },
        brandProducts() { const b = this.kTree.brands.find(x => x._id === this.form.brandId); return b ? b.products : []; },
        selectedBrand() { return this.kTree.brands.find(x => x._id === this.form.brandId) || null; },
        selectedProduct() { return this.brandProducts().find(p => p._id === this.form.productId) || null; },
        selectedProductHasImage() { const p = this.selectedProduct(); return Boolean(p && p.approved && (p.approved.images || []).length); },
        brandTemplates() { const b = this.selectedBrand(); return (b && b.approved && b.approved.sceneTemplates) || []; },
        readyToGenerate() { return Boolean(this.brand && this.form.brandId && this.selectedBrand() && this.selectedBrand().approved && this.selectedProductHasImage()); },
        canSubmit() {
            const t = this.form.templateIndex !== '' && this.form.templateIndex !== null && this.form.templateIndex !== undefined;
            return this.readyToGenerate() && this.status.configured && this.budget.canGenerate && !this.busy.image && (t || (this.form.scene || '').trim().length >= 5);
        },
        productName() { const p = this.selectedProduct(); return p ? this.kData(p).name : ''; },
        brandPillLabel() {
            if (!this.brand) return 'Select company';
            const b = this.selectedBrand(); const p = this.selectedProduct();
            if (!b) return this.brand.name + ' › choose brand';
            return [b.approved ? b.approved.name : this.kData(b).name, p ? this.kData(p).name : 'choose product'].join(' › ');
        },
        useTemplate(i) { this.form.templateIndex = String(i); this.$nextTick(() => this.$refs.scene && this.$refs.scene.focus()); },
        autosize(el) { el.style.height = 'auto'; el.style.height = Math.min(el.scrollHeight, 192) + 'px'; },
        formatLabel(r) { return ({ '1:1': 'Square 1:1', '4:5': 'Portrait 4:5', '9:16': 'Story 9:16', '16:9': 'Wide 16:9' })[r] || r; },
        placementLabel(p) { return ({ centre: 'Centre', left: 'Left', right: 'Right' })[p] || p; },
        aspectClass(r) { return ({ '1:1': 'aspect-square', '4:5': 'aspect-[4/5]', '9:16': 'aspect-[9/16]', '16:9': 'aspect-video' })[r] || 'aspect-square'; },
        galleryCols(g) { const n = g.gen ? g.gen.previews.length : g.previews; return n >= 3 ? 'grid-cols-2 sm:grid-cols-3' : n === 2 ? 'grid-cols-2' : 'grid-cols-1 max-w-sm'; },
        chosenUrl(g) { return g.gen && g.gen.chosenIndex !== null && g.gen.previews[g.gen.chosenIndex] ? g.gen.previews[g.gen.chosenIndex].url : ''; },
        metricImages() { return ((this.budget.usage && this.budget.usage.finished) || 0) + '/' + ((this.budget.allowance && this.budget.allowance.total) || 0); },
        ringDash(used, total) { const c = 2 * Math.PI * 15; const f = total ? Math.min(1, (used || 0) / total) : 0; return (c * f).toFixed(1) + ' ' + c.toFixed(1); },
        ringColour(used, total) { const f = total ? (used || 0) / total : 0; return f >= 1 ? 'stroke-red-500' : f >= 0.8 ? 'stroke-amber-500' : 'stroke-indigo-500'; },
        newEntry(o) {
            return Object.assign({ key: 'g' + Date.now() + Math.random().toString(36).slice(2, 6), prompt: '', productName: '', aspectRatio: '1:1', placement: 'centre', previews: 3,
                loading: false, error: null, gen: null, choosing: false, sending: false, sent: false, brandId: null, productId: null,
                cap: { loading: false, error: null, gen: null, edits: [], flags: [], approvedIndex: null } }, o);
        },
        scrollFeed() { this.$nextTick(() => { const el = document.getElementById('ais-feed'); if (el) el.lastElementChild && el.lastElementChild.scrollIntoView({ behavior: 'smooth', block: 'end' }); }); },
        async loadHistory() {
            this.generations = []; this.historyLoaded = false;
            try {
                const d = await this.api(`/generations?clientId=${this.brand._id}&limit=60`);
                const rows = d.generations || [];
                const caps = rows.filter(r => r.kind === 'caption' && r.imageGenerationId);
                const imgs = rows.filter(r => r.kind === 'image' && (r.previews || []).some(p => !p.blocked)).slice(0, 10).reverse();
                this.generations = imgs.map(r => {
                    const c = caps.find(x => String(x.imageGenerationId) === String(r._id));
                    const e = this.newEntry({ key: r._id, prompt: r.userBrief, productName: '', aspectRatio: r.aspectRatio || '1:1', previews: r.previewsRequested || r.previews.length,
                        gen: r, sent: Boolean(r.conceptId), brandId: r.brandItemId, productId: r.productId, history: true });
                    const prod = this.kTree.brands.flatMap(b => b.products || []).find(p => p._id === String(r.productId));
                    e.productName = prod ? this.kData(prod).name : '';
                    if (c) {
                        const opts = c.captionOptions || [];
                        e.cap.gen = c; e.cap.edits = [...opts];
                        const ai = c.approvedCaption ? opts.indexOf(c.approvedCaption) : -1;
                        if (c.approvedCaption && ai < 0) { e.cap.edits.push(c.approvedCaption); e.cap.approvedIndex = e.cap.edits.length - 1; }
                        else e.cap.approvedIndex = ai >= 0 ? ai : null;
                    }
                    return e;
                });
            } catch (e) { /* history is optional */ }
            finally { this.historyLoaded = true; this.scrollFeed(); }
        },
        pickFirstProduct() { const p = this.brandProducts().find(x => x.approved && (x.approved.images || []).length); this.form.productId = p ? p._id : ''; },
        onKnowledgeLoaded(tree) {
            if (!tree.brands.some(b => b._id === this.form.brandId)) {
                const b = tree.brands.find(x => x.approved); this.form.brandId = b ? b._id : ''; this.pickFirstProduct();
            }
        },
        allowanceLabel() {
            const a = this.budget.allowance || {};
            const src = a.source === 'client_allocation' ? 'From client allocation' : a.source === 'ai_override' ? 'Set by admin' : 'Default';
            return src + (a.topUp ? ` + ${a.topUp} top-up` : '');
        },
        headers(json = true) { const h = { Authorization: `Bearer ${localStorage.getItem('token')}` }; if (json) h['Content-Type'] = 'application/json'; return h; },
        async api(path, opts = {}) {
            const res = await fetch(`${API_URL}/ai-studio${path}`, { ...opts, headers: { ...this.headers(!(opts.body instanceof FormData)), ...(opts.headers || {}) } });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(data.message || `Request failed (${res.status})`);
            return data;
        },
        showToast(message, type = 'success') { this.toast = { show: true, message, type }; setTimeout(() => { this.toast.show = false; }, 4000); },

        async init() {
            const token = localStorage.getItem('token');
            if (!token) { this.authChecked = true; return; }
            try {
                const res = await fetch(`${API_URL}/auth/me`, { headers: { Authorization: `Bearer ${token}` } });
                if (res.status === 401 || res.status === 403) throw new Error('auth');
                if (!res.ok) throw new Error('server');
                const data = await res.json();
                this.user = data.user; window.HubAuth.setUser(data.user); localStorage.setItem('user', JSON.stringify(data.user));
                if (this.user.role === 'client') { window.location.href = DASHBOARD_URL; return; }
                this.viewMode = window.HubAuth.applyViewMode(window.HubAuth.readStoredViewMode() === 'client' ? 'admin' : undefined);
                this.status = await this.api('/status');
                this.statusLoaded = true;
                if (!this.status.enabled) return;
                this.brands = (await this.api('/brands')).brands;
                const saved = localStorage.getItem('hubSelectedClientId') || localStorage.getItem('selectedClientId');
                if (saved && this.brands.some(b => b._id === saved)) await this.selectBrand(saved);
            } catch (e) {
                if (e && e.message === 'auth') { localStorage.removeItem('token'); localStorage.removeItem('user'); this.user = null; this.loadError = 'Your session has expired. Please sign in again.'; }
                else if (e && e.message === 'server') { this.user = null; this.loadError = 'We could not verify your session. Please try again in a moment.'; }
                else this.showToast(e.message || 'Some data could not be loaded.', 'error');
            } finally { this.statusLoaded = true; this.authChecked = true; }
        },
        async selectBrand(id) {
            const d = await this.api(`/brands/${id}`);
            this.brand = d.brand; this.profile = d.profile; this.profile.sceneTemplates = this.profile.sceneTemplates || [];
            this.profileText = { doWords: (d.profile.doWords || []).join(', '), dontWords: (d.profile.dontWords || []).join(', '), hashtags: (d.profile.hashtags || []).join(', ') };
            this.budget = d.budget; this.examples = d.examples;
            this.budgetForm.monthlyImageAllowance = d.profile.monthlyImageAllowance ?? '';
            this.form.brandId = ''; this.form.productId = '';
            await this.kInit(id);
            this.loadHistory();
            localStorage.setItem('hubSelectedClientId', id);
        },
        async refreshBrand() { const d = await this.api(`/brands/${this.brand._id}`); this.budget = d.budget; this.examples = d.examples; },
        async generateImages() {
            if (!this.canSubmit()) return;
            const tpl = this.form.templateIndex !== '' ? this.brandTemplates()[parseInt(this.form.templateIndex, 10)] : null;
            const promptText = [tpl ? tpl.name : '', (this.form.scene || '').trim()].filter(Boolean).join(': ');
            const entry = this.newEntry({ prompt: promptText, productName: this.productName(), aspectRatio: this.form.aspectRatio, placement: this.form.placement,
                previews: Math.max(1, Math.min(3, this.form.previews || 3)), loading: true, brandId: this.form.brandId, productId: this.form.productId });
            this.generations.push(entry);
            const g = this.generations[this.generations.length - 1];
            const body = { ...this.form };
            this.form.scene = ''; if (this.$refs.scene) this.$refs.scene.style.height = 'auto';
            this.busy.image = true; this.scrollFeed();
            try {
                const d = await this.api(`/brands/${this.brand._id}/images`, { method: 'POST', body: JSON.stringify(body) });
                g.gen = d.generation; this.budget = d.budget;
                const blocked = d.generation.previews.filter(p => p.blocked).length;
                if (blocked) this.showToast(`${blocked} preview(s) were blocked by the product check; the rest are ready.`, 'error');
            } catch (e) { g.error = e.message; await this.refreshBrand().catch(() => {}); }
            finally { g.loading = false; this.busy.image = false; this.scrollFeed(); }
        },
        async choose(g, i) {
            if (!g.gen || g.choosing || g.sent || g.gen.chosenIndex === i) return;
            const first = g.gen.chosenIndex === null || g.gen.chosenIndex === undefined;
            g.choosing = true;
            try {
                const d = await this.api(`/generations/${g.gen._id}/choose`, { method: 'POST', body: JSON.stringify({ index: i }) });
                g.gen = d.generation; await this.refreshBrand();
                if (first && !g.cap.gen) await this.writeCaptions(g);
            } catch (e) { this.showToast(e.message, 'error'); }
            finally { g.choosing = false; }
        },
        async writeCaptions(g) {
            g.cap.loading = true; g.cap.error = null; this.scrollFeed();
            try {
                const brief = `Post featuring ${g.productName || 'the product'}. Scene: ${g.prompt || 'product photo'}`.slice(0, 1500);
                const d = await this.api(`/brands/${this.brand._id}/captions`, { method: 'POST', body: JSON.stringify({ brief, brandId: g.brandId || this.form.brandId, productId: g.productId || undefined, imageGenerationId: g.gen._id }) });
                g.cap.gen = d.generation; g.cap.edits = [...d.generation.captionOptions]; g.cap.flags = d.flags || []; g.cap.approvedIndex = null;
            } catch (e) { g.cap.error = e.message; }
            finally { g.cap.loading = false; this.scrollFeed(); }
        },
        async approveCaption(g, i) {
            try {
                await this.api(`/generations/${g.cap.gen._id}/approve-caption`, { method: 'POST', body: JSON.stringify({ text: g.cap.edits[i] }) });
                g.cap.approvedIndex = i; this.scrollFeed();
            } catch (e) { this.showToast(e.message, 'error'); }
        },
        async sendToReview(g) {
            g.sending = true;
            try {
                await this.api(`/generations/${g.gen._id}/send-to-review`, { method: 'POST', body: JSON.stringify({ captionGenerationId: g.cap.gen ? g.cap.gen._id : undefined }) });
                g.sent = true; this.showToast('Sent to Workflow for review. Nothing has been posted.');
            } catch (e) { this.showToast(e.message, 'error'); }
            finally { g.sending = false; }
        },
        async saveBudget(body) {
            try { const d = await this.api(`/brands/${this.brand._id}/budget`, { method: 'PUT', body: JSON.stringify(body) }); this.budget = d.budget; this.showToast('Budget updated.'); this.loadUsage(); }
            catch (e) { this.showToast(e.message, 'error'); }
        },
        async loadUsage() { if (!this.status.isAdmin) return; try { this.usage = await this.api('/usage'); } catch (e) { this.showToast(e.message, 'error'); } },
        logout() { localStorage.removeItem('token'); localStorage.removeItem('user'); window.location.href = LOGIN_URL; }
    };
}
</script>

<?php include get_template_directory() . '/inc/change-password-modal.php'; ?>
<?php esirom_hub_staff_mobile_nav('ai_studio'); ?>
</body>
</html>
