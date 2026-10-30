<?php
/**
 * Brand knowledge editor markup (Company -> Brands -> Products/SKUs).
 * Used inside an Alpine component that mixes in window.aiKnowledgeEditor(mode).
 */
if (!defined('ABSPATH')) exit;

/** Status/badge row + actions for one item. $v is the Alpine expression for the item. */
function esirom_hub_ai_k_item_head($v) {
    ?>
    <div class="flex flex-wrap items-center gap-2">
        <span class="text-[11px] font-medium px-2 py-0.5 rounded-full" :class="kBadge(<?php echo $v; ?>).cls" x-text="kBadge(<?php echo $v; ?>).text"></span>
        <span x-show="<?php echo $v; ?>.source === 'suggested_from_posts'" class="text-[11px] px-2 py-0.5 rounded-full bg-sky-100 text-sky-700 dark:bg-sky-500/20 dark:text-sky-200" x-text="'Suggested from posts' + (<?php echo $v; ?>.suggestion && <?php echo $v; ?>.suggestion.confidence ? ' · ' + <?php echo $v; ?>.suggestion.confidence + ' confidence' : '')"></span>
        <span class="text-[11px] text-gray-400" x-text="'v' + <?php echo $v; ?>.version + (<?php echo $v; ?>.approvedVersion ? ' · approved v' + <?php echo $v; ?>.approvedVersion : '')"></span>
        <div class="ml-auto flex flex-wrap gap-1">
            <button type="button" class="hub-btn hub-btn--soft hub-btn--sm" @click="kStartEdit(<?php echo $v; ?>)">Edit</button>
            <button type="button" x-show="kMode === 'staff' && <?php echo $v; ?>.status === 'draft'" class="hub-btn hub-btn--soft hub-btn--sm" @click="kAction(<?php echo $v; ?>, 'submit')">Send for client approval</button>
            <button type="button" x-show="kMode === 'client' && <?php echo $v; ?>.status === 'pending_client'" class="hub-btn hub-btn--primary hub-btn--sm" @click="kAction(<?php echo $v; ?>, 'approve')">Approve</button>
            <button type="button" x-show="kMode === 'client' && <?php echo $v; ?>.status === 'pending_client'" class="hub-btn hub-btn--soft hub-btn--sm" @click="kAction(<?php echo $v; ?>, 'request-changes')">Request changes</button>
            <button type="button" class="hub-btn hub-btn--soft hub-btn--sm" @click="kLoadVersions(<?php echo $v; ?>)">History</button>
            <button type="button" x-show="<?php echo $v; ?>.type !== 'company'" class="hub-btn hub-btn--soft hub-btn--sm text-red-600" @click="kAction(<?php echo $v; ?>, 'archive')">Remove</button>
        </div>
    </div>
    <p x-show="<?php echo $v; ?>.changesRequested" class="mt-2 text-xs rounded-lg bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-200 px-3 py-2" x-text="'Client asked for changes: ' + <?php echo $v; ?>.changesRequested"></p>
    <template x-if="kChanges(<?php echo $v; ?>).length">
        <div class="mt-2 text-xs rounded-lg border border-amber-200 dark:border-amber-800 bg-amber-50/60 dark:bg-amber-900/10 p-2 space-y-1">
            <p class="font-medium">Proposed changes (the approved version stays in use until approved):</p>
            <template x-for="c in kChanges(<?php echo $v; ?>)" :key="c.label"><p><span class="font-medium" x-text="c.label + ': '"></span><span class="line-through text-gray-400" x-text="c.from"></span> → <span x-text="c.to"></span></p></template>
        </div>
    </template>
    <?php
}

function esirom_hub_ai_knowledge_editor() {
    ?>
<div class="space-y-4" data-ai-knowledge>
    <div class="flex flex-wrap items-center gap-3">
        <div x-show="kCompanies.length > 1">
            <select class="hub-input" :value="kCompanyId" @change="kLoad($event.target.value)">
                <option value="">Choose a company…</option>
                <template x-for="c in kCompanies" :key="c._id"><option :value="c._id" :selected="c._id === kCompanyId" x-text="c.name + (c.pending ? ' · ' + c.pending + ' awaiting approval' : '') + (c.draft ? ' · ' + c.draft + ' draft' : '')"></option></template>
            </select>
        </div>
        <div class="hub-segmented" x-show="kCompanyId">
            <button type="button" class="hub-segmented__item" :class="kFilter === 'all' ? 'is-active' : ''" @click="kFilter = 'all'">All</button>
            <button type="button" class="hub-segmented__item" :class="kFilter === 'pending' ? 'is-active' : ''" @click="kFilter = 'pending'" x-text="'Awaiting approval (' + kPendingCount() + ')'"></button>
            <button type="button" class="hub-segmented__item" :class="kFilter === 'suggested' ? 'is-active' : ''" @click="kFilter = 'suggested'">Suggested drafts</button>
        </div>
        <button type="button" x-show="kCompanyId" class="hub-btn hub-btn--soft hub-btn--sm ml-auto" @click="kShowAudit = !kShowAudit; if (kShowAudit) kLoadAudit()" x-text="kShowAudit ? 'Hide activity' : 'Activity log'"></button>
    </div>

    <div x-show="kMode === 'client' && kCompanyId" class="rounded-xl border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-900/20 p-3 text-sm text-indigo-900 dark:text-indigo-100">
        This is the information Esirom's team uses when creating content for you. Please check anything marked <b>Awaiting client approval</b>. Changes you make yourself are approved straight away.
    </div>
    <div x-show="kMode === 'staff' && kCompanyId" class="text-xs text-gray-500">Staff edits are saved as drafts and are only used for images and captions once the client approves them. Items marked <b>Suggested from posts</b> were inferred automatically: check them before sending.</div>

    <!-- Company -->
    <div x-show="kCompanyId" class="hub-card p-4">
        <div class="flex items-center justify-between gap-2">
            <h3 class="font-semibold" x-text="'Company: ' + (kData(kTree.companyItem).name || kTree.companyName || '')"></h3>
            <button type="button" x-show="!kTree.companyItem" class="hub-btn hub-btn--primary hub-btn--sm" @click="kStartEdit(null, 'company')">Add company info</button>
        </div>
        <template x-if="kTree.companyItem">
            <div class="mt-2 space-y-2">
                <?php esirom_hub_ai_k_item_head('kTree.companyItem'); ?>
                <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line" x-text="kData(kTree.companyItem).about || 'No description yet.'"></p>
            </div>
        </template>
    </div>

    <!-- Brands -->
    <div x-show="kCompanyId" class="flex items-center justify-between">
        <h3 class="font-semibold" x-text="'Brands (' + kTree.brands.length + ')'"></h3>
        <button type="button" class="hub-btn hub-btn--primary hub-btn--sm" @click="kStartEdit(null, 'brand')">Add brand</button>
    </div>
    <template x-for="b in kTree.brands" :key="b._id">
        <div class="hub-card p-4 space-y-3" x-show="kVisible(b) || b.products.some(p => kVisible(p))">
            <div class="flex items-center gap-2">
                <button type="button" class="text-left font-semibold" @click="kOpen[b._id] = !kOpen[b._id]" x-text="(kOpen[b._id] === false ? '▸ ' : '▾ ') + (kData(b).name || 'Brand')"></button>
                <span class="text-xs text-gray-400" x-text="b.products.length + ' product' + (b.products.length === 1 ? '' : 's')"></span>
            </div>
            <?php esirom_hub_ai_k_item_head('b'); ?>
            <div x-show="kOpen[b._id] !== false" class="space-y-3">
                <div class="grid sm:grid-cols-2 gap-2 text-xs text-gray-600 dark:text-gray-300">
                    <p x-show="kData(b).voiceGuide"><span class="font-medium">Voice:</span> <span x-text="kData(b).voiceGuide"></span></p>
                    <p x-show="(kData(b).hashtags || []).length"><span class="font-medium">Hashtags:</span> <span x-text="(kData(b).hashtags || []).join(' ')"></span></p>
                    <p x-show="(kData(b).dontWords || []).length"><span class="font-medium">Never say:</span> <span x-text="(kData(b).dontWords || []).join(', ')"></span></p>
                    <p><span class="font-medium">Emoji:</span> <span x-text="kData(b).emojiUse || 'light'"></span></p>
                </div>
                <div class="border-t border-gray-100 dark:border-gray-700 pt-3 space-y-2">
                    <div class="flex items-center justify-between"><p class="text-sm font-medium">Products / SKUs</p><button type="button" class="hub-btn hub-btn--soft hub-btn--sm" @click="kStartEdit(null, 'product', b._id)">Add product</button></div>
                    <template x-for="p in b.products" :key="p._id">
                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3 space-y-2" x-show="kVisible(p)">
                            <div class="flex items-start gap-3">
                                <div class="flex gap-1 flex-shrink-0">
                                    <template x-for="(img, ix) in (kData(p).images || [])" :key="ix">
                                        <div class="relative"><img :src="img.url" alt="" class="h-14 w-14 object-contain rounded checker"><button type="button" class="absolute -top-1 -right-1 bg-white dark:bg-gray-800 rounded-full text-[10px] px-1 border" @click="kRemoveImage(p, ix)" title="Remove image">×</button></div>
                                    </template>
                                    <label class="h-14 w-14 rounded border border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center text-[10px] text-gray-500 cursor-pointer text-center leading-tight">+ cut-out<input type="file" accept="image/png,image/webp,image/jpeg" class="hidden" @change="kUpload(p, $event)"></label>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-sm" x-text="kData(p).name + (kData(p).sku ? ' · ' + kData(p).sku : '')"></p>
                                    <p class="text-xs text-gray-500" x-show="(kData(p).variants || []).length" x-text="'Sizes / variants: ' + (kData(p).variants || []).join(', ')"></p>
                                    <p class="text-xs text-gray-500" x-show="(kData(p).claims || []).length" x-text="'Claims: ' + (kData(p).claims || []).join('; ')"></p>
                                    <p class="text-xs text-gray-400 italic" x-show="p.suggestion && p.suggestion.evidence && p.suggestion.evidence.length" x-text="'Seen in posts: “' + ((p.suggestion && p.suggestion.evidence) || [])[0] + '”'"></p>
                                </div>
                            </div>
                            <?php esirom_hub_ai_k_item_head('p'); ?>
                        </div>
                    </template>
                    <p x-show="!b.products.length" class="text-xs text-gray-500">No products yet.</p>
                </div>
            </div>
        </div>
    </template>
    <p x-show="kCompanyId && !kTree.brands.length && !kLoading" class="text-sm text-gray-500">No brands yet. Add the brands this company sells under.</p>

    <!-- Activity -->
    <div x-show="kShowAudit" class="hub-card p-4">
        <h3 class="font-semibold mb-2">Activity log</h3>
        <ul class="text-xs space-y-1 max-h-80 overflow-y-auto">
            <template x-for="a in kAudit" :key="a._id"><li><span class="text-gray-400" x-text="kWhen(a.createdAt)"></span> · <span class="font-medium" x-text="a.userName || 'System'"></span> <span x-text="kActionLabel(a.action)"></span> <span x-text="(a.itemType || '') + ' “' + (a.itemName || '') + '”'"></span><span class="text-gray-400" x-show="a.changedFields && a.changedFields.length" x-text="' (' + (a.changedFields || []).join(', ') + ')'"></span></li></template>
            <li x-show="!kAudit.length" class="text-gray-500">No activity yet.</li>
        </ul>
    </div>

    <!-- Edit modal -->
    <div x-show="kEdit" x-cloak class="fixed inset-0 z-[90] bg-black/40 flex items-start justify-center p-4 overflow-y-auto" @keydown.escape.window="kEdit = null">
        <div class="hub-card hub-card--elevated w-full max-w-2xl p-5 space-y-3 mt-10" @click.outside="kEdit = null">
            <h3 class="font-semibold" x-text="kEdit ? ((kEdit.item ? 'Edit ' : 'Add ') + kLabel(kEdit.type).toLowerCase()) : ''"></h3>
            <template x-if="kEdit">
                <div class="space-y-3">
                    <template x-for="f in kFields(kEdit.type)" :key="f[0]">
                        <div>
                            <label class="text-xs font-medium text-gray-500" x-text="f[1]"></label>
                            <template x-if="f[2] === 'area' || f[2] === 'lines'"><textarea x-model="kForm[f[0]]" rows="3" class="hub-input w-full mt-1"></textarea></template>
                            <template x-if="f[2] === 'text' || f[2] === 'list'"><input x-model="kForm[f[0]]" class="hub-input w-full mt-1"></template>
                            <template x-if="f[2] === 'emoji'"><select x-model="kForm[f[0]]" class="hub-input w-full mt-1"><option value="none">None</option><option value="light">Light</option><option value="moderate">Moderate</option><option value="heavy">Heavy</option></select></template>
                        </div>
                    </template>
                    <div x-show="kEdit.type === 'brand'">
                        <div class="flex items-center justify-between"><label class="text-xs font-medium text-gray-500">Scene templates</label><button type="button" class="text-xs underline" @click="kForm.sceneTemplates.push({ name: '', prompt: '', aspectRatio: '1:1' })">Add template</button></div>
                        <template x-for="(t, i) in (kForm.sceneTemplates || [])" :key="i">
                            <div class="grid grid-cols-12 gap-2 mt-2"><input x-model="t.name" placeholder="Name" class="hub-input col-span-3"><input x-model="t.prompt" placeholder="Scene description" class="hub-input col-span-6"><select x-model="t.aspectRatio" class="hub-input col-span-2"><option>1:1</option><option>4:5</option><option>9:16</option><option>16:9</option></select><button type="button" class="text-xs text-red-600 col-span-1" @click="kForm.sceneTemplates.splice(i, 1)">×</button></div>
                        </template>
                    </div>
                    <p class="text-xs text-gray-500" x-text="kMode === 'client' ? 'Your changes are approved as soon as you save.' : 'The client must approve this before it is used for images or captions.'"></p>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="hub-btn hub-btn--soft" @click="kEdit = null">Cancel</button>
                        <button type="button" x-show="kMode === 'staff'" class="hub-btn hub-btn--soft" :disabled="kSaving" @click="kSave(false)">Save draft</button>
                        <button type="button" class="hub-btn hub-btn--primary" :disabled="kSaving" @click="kSave(kMode === 'staff')" x-text="kMode === 'staff' ? 'Save and send for client approval' : 'Save'"></button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Versions modal -->
    <div x-show="kVersions" x-cloak class="fixed inset-0 z-[90] bg-black/40 flex items-start justify-center p-4 overflow-y-auto" @keydown.escape.window="kVersions = null">
        <div class="hub-card hub-card--elevated w-full max-w-2xl p-5 mt-10" @click.outside="kVersions = null">
            <div class="flex justify-between"><h3 class="font-semibold">Version history</h3><button type="button" @click="kVersions = null">×</button></div>
            <ul class="mt-3 space-y-2 text-xs max-h-[60vh] overflow-y-auto">
                <template x-for="v in (kVersions ? kVersions.list : [])" :key="v.version">
                    <li class="border-l-2 pl-2" :class="v.action === 'approve' || v.action === 'client_edit' ? 'border-emerald-400' : 'border-gray-300'">
                        <p><span class="font-medium" x-text="'v' + v.version"></span> · <span x-text="kActionLabel(v.action)"></span> · <span x-text="v.byRole"></span> · <span class="text-gray-400" x-text="kWhen(v.at)"></span></p>
                        <p class="text-gray-500 break-words" x-text="Object.entries(v.data || {}).filter(e => e[0] !== 'images' && e[1] && (!Array.isArray(e[1]) || e[1].length)).map(e => e[0] + ': ' + (Array.isArray(e[1]) ? e[1].map(x => typeof x === 'object' ? x.name : x).join(', ') : e[1])).join(' · ').slice(0, 400)"></p>
                    </li>
                </template>
            </ul>
        </div>
    </div>
</div>
<style>.checker{background-image:linear-gradient(45deg,#e5e7eb 25%,transparent 25%),linear-gradient(-45deg,#e5e7eb 25%,transparent 25%),linear-gradient(45deg,transparent 75%,#e5e7eb 75%),linear-gradient(-45deg,transparent 75%,#e5e7eb 75%);background-size:12px 12px;background-position:0 0,0 6px,6px -6px,-6px 0}</style>
    <?php
}
