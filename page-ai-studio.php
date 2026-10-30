<?php
/**
 * Template Name: AI Studio
 * Description: Product-safe AI images and on-voice captions — staff only (feature-flagged)
 */
if (!defined('ABSPATH')) exit;

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
<body class="hub-has-mobile-nav h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 pb-16 md:pb-0" x-data="aiStudioApp()" x-init="init()">

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
                <div class="min-w-0">
                    <h1 class="hub-page-title">AI Studio <span class="hub-pill ml-1 text-[11px] align-middle px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-200">Beta</span></h1>
                    <p class="hub-page-subtitle">Scenes around real product photos, and captions in each brand's voice. Nothing is posted automatically.</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="relative" x-data="{ open: false }" x-show="status.enabled">
                        <button type="button" @click="open = !open" class="hub-btn hub-btn--soft hub-btn--sm" :aria-expanded="open">
                            <span x-text="brand ? brand.name : 'Select brand'"></span>
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
            <div x-show="status.enabled && !status.configured" x-cloak class="rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-4 text-sm text-amber-900 dark:text-amber-100" data-testid="not-configured">
                <p class="font-semibold">AI Studio is not configured yet</p>
                <p class="mt-1">The Gemini API key hasn't been added to the Hub backend, so images and captions can't be generated. You can still set up brand profiles and product libraries.</p>
            </div>

            <div x-show="status.enabled && !brand" x-cloak class="hub-card p-8 text-center text-sm text-gray-500">Select a brand to get started.</div>

            <template x-if="status.enabled && brand">
            <div class="space-y-4">
                <!-- Budget strip -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="hub-card p-4"><p class="text-xs text-gray-500">Images this month</p><p class="text-2xl font-semibold" x-text="budget.usage.finished + ' / ' + budget.allowance.total"></p><p class="text-[11px] text-gray-400" x-text="allowanceLabel()"></p></div>
                    <div class="hub-card p-4"><p class="text-xs text-gray-500">Previews left this month</p><p class="text-2xl font-semibold" x-text="budget.previewsLeft"></p><p class="text-[11px] text-gray-400" x-text="'of ' + budget.previewCap"></p></div>
                    <div class="hub-card p-4"><p class="text-xs text-gray-500">Your requests today</p><p class="text-2xl font-semibold" x-text="budget.userToday + ' / ' + budget.staffDailyCap"></p><p class="text-[11px] text-gray-400">Resets at midnight (Jamaica)</p></div>
                    <div class="hub-card p-4"><p class="text-xs text-gray-500">Estimated cost this month</p><p class="text-2xl font-semibold" x-text="'US$' + (budget.usage.costUsd || 0).toFixed(2)"></p><p class="text-[11px] text-gray-400" x-text="budget.paused ? 'Paused by an admin' : 'This brand'"></p></div>
                </div>
                <div x-show="!budget.canGenerate" class="rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 p-3 text-sm text-red-800 dark:text-red-100">
                    <template x-for="r in budget.reasons" :key="r.code"><p x-text="r.message"></p></template>
                </div>

                <!-- CREATE -->
                <div x-show="tab === 'create'" class="grid lg:grid-cols-5 gap-4">
                    <div class="lg:col-span-2 space-y-4">
                        <div class="hub-card p-4 space-y-3">
                            <h2 class="font-semibold">1. Image</h2>
                            <div x-show="!products().length" class="text-sm text-gray-500">No products yet. Add approved cut-outs in <button class="underline" @click="tab='products'">Products</button>.</div>
                            <div x-show="products().length">
                                <label class="text-xs font-medium text-gray-500">Product</label>
                                <div class="grid grid-cols-3 gap-2 mt-1">
                                    <template x-for="p in products()" :key="p._id">
                                        <button type="button" @click="form.productId = p._id" class="rounded-lg border p-1 text-xs" :class="form.productId === p._id ? 'border-brand-500 ring-2 ring-brand-500/30' : 'border-gray-200 dark:border-gray-700'">
                                            <img :src="p.imageUrl" alt="" class="h-16 w-full object-contain checker">
                                            <span class="block truncate mt-1" x-text="p.name"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <div x-show="profile.sceneTemplates && profile.sceneTemplates.length">
                                <label class="text-xs font-medium text-gray-500">Scene template</label>
                                <select x-model="form.templateId" class="hub-input w-full mt-1"><option value="">None (describe below)</option><template x-for="t in profile.sceneTemplates" :key="t._id"><option :value="t._id" x-text="t.name"></option></template></select>
                            </div>
                            <div><label class="text-xs font-medium text-gray-500">Scene</label><textarea x-model="form.scene" rows="2" class="hub-input w-full mt-1" placeholder="e.g. sunny kitchen counter with coffee beans and a hibiscus flower"></textarea></div>
                            <div class="grid grid-cols-3 gap-2">
                                <div><label class="text-xs font-medium text-gray-500">Format</label><select x-model="form.aspectRatio" class="hub-input w-full mt-1"><option value="1:1">Square 1:1</option><option value="4:5">Portrait 4:5</option><option value="9:16">Story 9:16</option><option value="16:9">Wide 16:9</option></select></div>
                                <div><label class="text-xs font-medium text-gray-500">Position</label><select x-model="form.placement" class="hub-input w-full mt-1"><option value="centre">Centre</option><option value="left">Left</option><option value="right">Right</option></select></div>
                                <div><label class="text-xs font-medium text-gray-500">Previews</label><select x-model.number="form.previews" class="hub-input w-full mt-1"><option :value="1">1</option><option :value="2">2</option><option :value="3">3</option></select></div>
                            </div>
                            <p class="text-[11px] text-gray-500">The AI only creates the background. Your real product photo is placed on top and checked pixel by pixel, so packaging, colours and text never change.</p>
                            <button class="hub-btn hub-btn--primary hub-btn--block" :disabled="busy.image || !status.configured || !budget.canGenerate || !form.productId" @click="generateImages()">
                                <span x-show="!busy.image">Generate previews</span><span x-show="busy.image">Generating… (up to a minute)</span>
                            </button>
                        </div>
                        <div class="hub-card p-4 space-y-3">
                            <h2 class="font-semibold">2. Caption</h2>
                            <textarea x-model="captionBrief" rows="3" class="hub-input w-full" placeholder="Post brief: what is the post about, any offer or call to action?"></textarea>
                            <p class="text-[11px] text-gray-500" x-text="'Written in this brand\'s voice, using ' + examples.length + ' approved or top-performing captions as examples.'"></p>
                            <button class="hub-btn hub-btn--soft hub-btn--block" :disabled="busy.caption || !status.configured || budget.paused" @click="generateCaptions()"><span x-show="!busy.caption">Suggest 3 captions</span><span x-show="busy.caption">Writing…</span></button>
                            <template x-for="(opt, i) in captionGen ? captionGen.captionOptions : []" :key="i">
                                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3 space-y-2">
                                    <textarea x-model="captionEdits[i]" rows="4" class="hub-input w-full text-sm"></textarea>
                                    <p x-show="captionFlags[i] && captionFlags[i].length" class="text-xs text-red-600" x-text="'Contains words to avoid: ' + (captionFlags[i] || []).join(', ')"></p>
                                    <button class="hub-btn hub-btn--sm" :class="approvedCaptionIndex === i ? 'hub-btn--primary' : 'hub-btn--soft'" @click="approveCaption(i)" x-text="approvedCaptionIndex === i ? 'Approved' : 'Approve this caption'"></button>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="lg:col-span-3 space-y-4">
                        <div class="hub-card p-4">
                            <h2 class="font-semibold mb-3">Previews</h2>
                            <div x-show="!imageGen" class="text-sm text-gray-500 py-10 text-center">Previews will appear here. Only the one you choose counts towards the brand's monthly images.</div>
                            <div class="grid sm:grid-cols-3 gap-3" x-show="imageGen">
                                <template x-for="(p, i) in imageGen ? imageGen.previews : []" :key="i">
                                    <div class="rounded-lg border overflow-hidden" :class="imageGen.chosenIndex === i ? 'border-brand-500 ring-2 ring-brand-500/40' : 'border-gray-200 dark:border-gray-700'">
                                        <template x-if="!p.blocked"><img :src="p.url" alt="Preview" class="w-full aspect-square object-cover"></template>
                                        <template x-if="p.blocked"><div class="aspect-square flex items-center justify-center p-3 text-xs text-red-600 bg-red-50 dark:bg-red-900/20" x-text="'Blocked: ' + p.blockReason"></div></template>
                                        <div class="p-2 flex items-center justify-between gap-2">
                                            <span class="text-[11px]" :class="p.productCheck && p.productCheck.passed ? 'text-emerald-600' : 'text-red-600'" x-text="p.productCheck && p.productCheck.passed ? '✓ Product unaltered' : 'Product check failed'"></span>
                                            <button x-show="!p.blocked" class="hub-btn hub-btn--sm" :class="imageGen.chosenIndex === i ? 'hub-btn--primary' : 'hub-btn--soft'" @click="choose(i)" x-text="imageGen.chosenIndex === i ? 'Chosen' : 'Choose'"></button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="hub-card p-4 flex flex-wrap items-center justify-between gap-3">
                            <div class="text-sm"><p class="font-semibold">3. Send for review</p><p class="text-gray-500 text-xs">Creates a Workflow item awaiting internal review. Nothing is posted.</p></div>
                            <button class="hub-btn hub-btn--primary" :disabled="busy.send || !imageGen || imageGen.chosenIndex === null || imageGen.chosenIndex === undefined" @click="sendToReview()">Send to review</button>
                        </div>
                    </div>
                </div>

                <!-- PROFILE -->
                <div x-show="tab === 'profile'" class="hub-card p-4 space-y-3 max-w-3xl">
                    <h2 class="font-semibold">Brand knowledge</h2>
                    <p class="text-xs text-gray-500">Any staff member can edit this. The AI uses it for every caption and scene.</p>
                    <div><label class="text-xs font-medium text-gray-500">About the company</label><textarea x-model="profile.companyInfo" rows="3" class="hub-input w-full mt-1"></textarea></div>
                    <div><label class="text-xs font-medium text-gray-500">About the products</label><textarea x-model="profile.productInfo" rows="3" class="hub-input w-full mt-1"></textarea></div>
                    <div><label class="text-xs font-medium text-gray-500">Audience</label><input x-model="profile.audience" class="hub-input w-full mt-1"></div>
                    <div><label class="text-xs font-medium text-gray-500">Voice guide</label><textarea x-model="profile.voiceGuide" rows="3" class="hub-input w-full mt-1" placeholder="Tone, sentence length, patois or not, sign-offs…"></textarea></div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div><label class="text-xs font-medium text-gray-500">Words to use (comma-separated)</label><input x-model="profileText.doWords" class="hub-input w-full mt-1"></div>
                        <div><label class="text-xs font-medium text-gray-500">Words never to use</label><input x-model="profileText.dontWords" class="hub-input w-full mt-1"></div>
                        <div><label class="text-xs font-medium text-gray-500">Hashtags</label><input x-model="profileText.hashtags" class="hub-input w-full mt-1"></div>
                        <div><label class="text-xs font-medium text-gray-500">Emoji use</label><select x-model="profile.emojiUse" class="hub-input w-full mt-1"><option value="none">None</option><option value="light">Light</option><option value="moderate">Moderate</option><option value="heavy">Heavy</option></select></div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between"><label class="text-xs font-medium text-gray-500">Scene templates</label><button class="text-xs underline" @click="profile.sceneTemplates.push({ name: '', prompt: '', aspectRatio: '1:1' })">Add template</button></div>
                        <template x-for="(t, i) in profile.sceneTemplates" :key="i">
                            <div class="grid grid-cols-12 gap-2 mt-2">
                                <input x-model="t.name" placeholder="Name" class="hub-input col-span-3">
                                <input x-model="t.prompt" placeholder="Scene description" class="hub-input col-span-6">
                                <select x-model="t.aspectRatio" class="hub-input col-span-2"><option>1:1</option><option>4:5</option><option>9:16</option><option>16:9</option></select>
                                <button class="text-xs text-red-600 col-span-1" @click="profile.sceneTemplates.splice(i, 1)">Remove</button>
                            </div>
                        </template>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Caption examples the AI will follow</p>
                        <ul class="mt-1 space-y-1 text-xs text-gray-600 dark:text-gray-300 max-h-48 overflow-y-auto">
                            <template x-for="(e, i) in examples" :key="i"><li class="border-l-2 pl-2" :class="e.source === 'top_post' ? 'border-emerald-400' : 'border-indigo-300'"><span class="font-medium" x-text="e.label"></span>: <span x-text="e.text.slice(0, 160)"></span></li></template>
                            <li x-show="!examples.length">No approved or synced captions yet for this brand.</li>
                        </ul>
                    </div>
                    <button class="hub-btn hub-btn--primary" :disabled="busy.profile" @click="saveProfile()">Save brand profile</button>
                </div>

                <!-- PRODUCTS -->
                <div x-show="tab === 'products'" class="hub-card p-4 space-y-3">
                    <h2 class="font-semibold">Product library</h2>
                    <p class="text-xs text-gray-500">Upload approved cut-outs (PNG with a transparent background is best, at least 600px). These exact pixels are used in every image.</p>
                    <div class="flex flex-wrap gap-2 items-end">
                        <div><label class="text-xs text-gray-500">Name</label><input x-model="newProduct.name" class="hub-input mt-1"></div>
                        <div><label class="text-xs text-gray-500">Image</label><input type="file" accept="image/png,image/webp,image/jpeg" x-ref="productFile" class="block text-sm mt-1"></div>
                        <button class="hub-btn hub-btn--primary hub-btn--sm" :disabled="busy.product" @click="uploadProduct()">Add product</button>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
                        <template x-for="p in products()" :key="p._id">
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-2 text-xs">
                                <img :src="p.imageUrl" alt="" class="h-28 w-full object-contain checker">
                                <p class="font-medium truncate mt-1" x-text="p.name"></p>
                                <p class="text-gray-400" x-text="p.hasAlpha ? 'Cut-out' : 'No transparency'"></p>
                                <button class="text-red-600 underline mt-1" @click="retireProduct(p)">Remove</button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- USAGE (admin) -->
                <div x-show="tab === 'usage'" class="space-y-4">
                    <div class="hub-card p-4 space-y-3" x-show="status.isAdmin">
                        <h2 class="font-semibold" x-text="'Budget for ' + brand.name"></h2>
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
                        <table class="w-full text-sm mt-1"><thead><tr class="text-left text-xs text-gray-500"><th>Brand</th><th>Images</th><th>Previews</th><th>Captions</th><th>Blocked</th><th>Est. cost</th></tr></thead>
                            <tbody><template x-for="r in usage ? usage.byBrand : []" :key="r.clientId"><tr class="border-t border-gray-100 dark:border-gray-700"><td x-text="r.name + (r.paused ? ' (paused)' : '')"></td><td x-text="r.finished + ' / ' + r.allowance"></td><td x-text="r.previews"></td><td x-text="r.captionRequests"></td><td x-text="r.blocked"></td><td x-text="'US$' + r.costUsd.toFixed(2)"></td></tr></template></tbody></table>
                        <h3 class="text-sm font-medium mt-4">By person</h3>
                        <table class="w-full text-sm mt-1"><thead><tr class="text-left text-xs text-gray-500"><th>Person</th><th>Image requests</th><th>Previews</th><th>Chosen</th><th>Captions</th><th>Est. cost</th></tr></thead>
                            <tbody><template x-for="r in usage ? usage.byPerson : []" :key="r.userId"><tr class="border-t border-gray-100 dark:border-gray-700"><td x-text="r.name"></td><td x-text="r.imageRequests"></td><td x-text="r.previews"></td><td x-text="r.finished"></td><td x-text="r.captionRequests"></td><td x-text="'US$' + r.costUsd.toFixed(2)"></td></tr></template></tbody></table>
                        <p class="text-[11px] text-gray-400 mt-3" x-text="'Models: ' + status.models.image + ' (2K images), ' + status.models.caption + ' (captions). Costs are estimates from Google list prices.'"></p>
                    </div>
                </div>
            </div>
            </template>
        </div>
    </main>
</div>
<style>.checker{background-image:linear-gradient(45deg,#e5e7eb 25%,transparent 25%),linear-gradient(-45deg,#e5e7eb 25%,transparent 25%),linear-gradient(45deg,transparent 75%,#e5e7eb 75%),linear-gradient(-45deg,transparent 75%,#e5e7eb 75%);background-size:12px 12px;background-position:0 0,0 6px,6px -6px,-6px 0}</style>

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
        form: { productId: '', templateId: '', scene: '', aspectRatio: '1:1', placement: 'centre', previews: 3 },
        imageGen: null, captionBrief: '', captionGen: null, captionEdits: [], captionFlags: [], approvedCaptionIndex: null,
        newProduct: { name: '' },
        busy: {}, toast: { show: false, message: '', type: 'success' },

        tabs() {
            const t = [{ id: 'create', label: 'Create' }, { id: 'profile', label: 'Brand profile' }, { id: 'products', label: 'Products' }];
            if (this.status.isAdmin) t.push({ id: 'usage', label: 'Usage & budgets' });
            return t;
        },
        products() { return (this.profile.products || []).filter(p => p.approved !== false); },
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
            this.imageGen = null; this.captionGen = null; this.approvedCaptionIndex = null;
            this.form.productId = this.products()[0]?._id || '';
            localStorage.setItem('hubSelectedClientId', id);
        },
        async refreshBrand() { const d = await this.api(`/brands/${this.brand._id}`); this.budget = d.budget; this.examples = d.examples; },
        async generateImages() {
            this.busy.image = true;
            try {
                const d = await this.api(`/brands/${this.brand._id}/images`, { method: 'POST', body: JSON.stringify(this.form) });
                this.imageGen = d.generation; this.budget = d.budget;
                const blocked = d.generation.previews.filter(p => p.blocked).length;
                this.showToast(blocked ? `${blocked} preview(s) were blocked; the rest are ready.` : 'Previews ready.', blocked ? 'error' : 'success');
            } catch (e) { this.showToast(e.message, 'error'); await this.refreshBrand().catch(() => {}); }
            finally { this.busy.image = false; }
        },
        async choose(i) {
            try { const d = await this.api(`/generations/${this.imageGen._id}/choose`, { method: 'POST', body: JSON.stringify({ index: i }) }); this.imageGen = d.generation; await this.refreshBrand(); }
            catch (e) { this.showToast(e.message, 'error'); }
        },
        async generateCaptions() {
            this.busy.caption = true;
            try {
                const d = await this.api(`/brands/${this.brand._id}/captions`, { method: 'POST', body: JSON.stringify({ brief: this.captionBrief, productId: this.form.productId, imageGenerationId: this.imageGen?._id }) });
                this.captionGen = d.generation; this.captionEdits = [...d.generation.captionOptions]; this.captionFlags = d.flags || []; this.approvedCaptionIndex = null;
            } catch (e) { this.showToast(e.message, 'error'); }
            finally { this.busy.caption = false; }
        },
        async approveCaption(i) {
            try { await this.api(`/generations/${this.captionGen._id}/approve-caption`, { method: 'POST', body: JSON.stringify({ text: this.captionEdits[i] }) }); this.approvedCaptionIndex = i; this.showToast('Caption approved. It will help train this brand\'s voice.'); }
            catch (e) { this.showToast(e.message, 'error'); }
        },
        async sendToReview() {
            this.busy.send = true;
            try {
                const body = { captionGenerationId: this.approvedCaptionIndex !== null ? this.captionGen._id : undefined };
                await this.api(`/generations/${this.imageGen._id}/send-to-review`, { method: 'POST', body: JSON.stringify(body) });
                this.showToast('Sent to Workflow for review.');
            } catch (e) { this.showToast(e.message, 'error'); }
            finally { this.busy.send = false; }
        },
        async saveProfile() {
            this.busy.profile = true;
            try {
                const d = await this.api(`/brands/${this.brand._id}/profile`, { method: 'PUT', body: JSON.stringify({ ...this.profile, products: undefined, topUps: undefined, ...this.profileText }) });
                this.profile = d.profile; this.showToast('Brand profile saved.');
            } catch (e) { this.showToast(e.message, 'error'); }
            finally { this.busy.profile = false; }
        },
        async uploadProduct() {
            const f = this.$refs.productFile.files[0];
            if (!f) { this.showToast('Choose an image first.', 'error'); return; }
            this.busy.product = true;
            try {
                const fd = new FormData(); fd.append('file', f); fd.append('name', this.newProduct.name || f.name);
                const d = await this.api(`/brands/${this.brand._id}/products`, { method: 'POST', body: fd });
                this.profile.products.push(d.product); this.newProduct.name = ''; this.$refs.productFile.value = '';
                if (!this.form.productId) this.form.productId = d.product._id;
                this.showToast(d.warning || 'Product added.', d.warning ? 'error' : 'success');
            } catch (e) { this.showToast(e.message, 'error'); }
            finally { this.busy.product = false; }
        },
        async retireProduct(p) {
            if (!confirm(`Remove ${p.name} from the library?`)) return;
            try { await this.api(`/brands/${this.brand._id}/products/${p._id}`, { method: 'DELETE' }); p.approved = false; this.profile.products = [...this.profile.products]; }
            catch (e) { this.showToast(e.message, 'error'); }
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
