<?php
/**
 * Template Name: Messages
 * Description: Facebook Messenger and Instagram DMs — staff only
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
    <title>Messages — Agency Hub</title>
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
        .msg-thread-active { background: rgb(238 242 255); }
        .dark .msg-thread-active { background: rgb(49 46 129 / 0.25); }
        .msg-bubble-page { background: rgb(79 70 229); color: white; margin-left: auto; }
        .msg-bubble-user { background: rgb(243 244 246); color: rgb(17 24 39); }
        .dark .msg-bubble-user { background: rgb(55 65 81); color: rgb(243 244 246); }
    </style>
</head>
<body class="hub-has-mobile-nav h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 pb-16 md:pb-0" x-data="messagesApp()" x-init="init()">

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
            <?php esirom_hub_staff_sidebar_nav('messages', 'site', false); ?>
        </nav>
        <div class="p-3 border-t border-gray-200 dark:border-gray-700/50">
            <?php esirom_hub_staff_sidebar_footer('site', false); ?>
        </div>
    </aside>

    <main class="hub-app-main">
        <header class="hub-sticky-header px-4 sm:px-6 py-3">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="min-w-0">
                    <h1 class="hub-page-title">Messages</h1>
                    <p class="hub-page-subtitle">DMs and post comments · Facebook &amp; Instagram</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="hub-btn hub-btn--soft hub-btn--sm" :aria-expanded="open">
                            <span x-text="selectedClientName || 'Select brand'"></span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-64 hub-card hub-card--elevated z-50 max-h-80 overflow-y-auto py-1">
                            <template x-for="client in clients" :key="client._id">
                                <button type="button" @click="selectClient(client); open = false" class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700" :class="selectedClient === client._id ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-200 font-semibold' : 'text-gray-700 dark:text-gray-200'">
                                    <span x-text="client.brandName || client.name"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <?php esirom_hub_account_menu(); ?>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 mt-3" data-inbox-filters>
                <div class="hub-segmented" role="tablist" aria-label="Channel">
                    <template x-for="o in [['all','All channels'],['facebook','Facebook'],['instagram','Instagram']]" :key="o[0]">
                        <button type="button" role="tab" @click="f.channel = o[0]; filterThreads()" :aria-selected="f.channel === o[0]" class="hub-segmented__item" :class="f.channel === o[0] ? 'is-active' : ''" x-text="o[1]"></button>
                    </template>
                </div>
                <div class="hub-segmented" role="tablist" aria-label="Type">
                    <template x-for="o in [['all','All'],['dm','DMs'],['comment','Comments']]" :key="o[0]">
                        <button type="button" role="tab" @click="f.type = o[0]; filterThreads()" :aria-selected="f.type === o[0]" class="hub-segmented__item" :class="f.type === o[0] ? 'is-active' : ''">
                            <span x-text="o[1]"></span><span class="ml-1 opacity-60" x-text="countOf(o[0])"></span>
                        </button>
                    </template>
                </div>
                <div class="hub-segmented" role="tablist" aria-label="Status">
                    <template x-for="o in [['open','Open'],['done','Done'],['all','All']]" :key="o[0]">
                        <button type="button" role="tab" @click="f.status = o[0]; filterThreads()" :aria-selected="f.status === o[0]" class="hub-segmented__item" :class="f.status === o[0] ? 'is-active' : ''" x-text="o[1]"></button>
                    </template>
                </div>
                <label class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 px-2">
                    <input type="checkbox" x-model="f.unanswered" @change="filterThreads()" class="rounded"> Unanswered
                </label>
                <select x-model="f.assigned" @change="filterThreads()" class="text-xs rounded-lg border dark:border-gray-600 bg-white dark:bg-gray-800 px-2 py-1.5">
                    <option value="">Anyone</option>
                    <option value="me">Assigned to me</option>
                    <option value="unassigned">Unassigned</option>
                    <template x-for="u in assignees" :key="u._id"><option :value="u._id" x-text="u.firstName + ' ' + (u.lastName || '')"></option></template>
                </select>
                <button type="button" @click="loadMessages(true)" :disabled="loading" class="text-xs text-indigo-600 hover:underline ml-auto">Sync now</button>
            </div>
        </header>

        <div class="hub-page-content p-4 sm:p-6 space-y-4">
            <div x-show="!selectedClient" class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-8 text-center text-sm text-gray-500">
                Select a brand to view inbox threads.
            </div>

            <div x-show="selectedClient && perms && perms.connected === false" x-cloak class="rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-4 text-sm text-amber-800 dark:text-amber-200">
                This brand's Facebook Page is not connected, so DMs and comments can't load. Connect it in <a href="<?php echo esc_url($admin_url); ?>" class="underline font-medium">Admin → Social Media Connections</a>.
            </div>
            <div x-show="selectedClient && perms && perms.missing && perms.missing.length" x-cloak class="rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-4 text-sm text-amber-800 dark:text-amber-200" data-perm-gaps>
                <p class="font-semibold">Some inbox actions need extra Meta permissions</p>
                <p class="text-xs mt-1">Missing on this brand's Page token: <span class="font-mono" x-text="(perms?.missing || []).join(', ')"></span>.</p>
                <ul class="text-xs mt-1 list-disc pl-5">
                    <li x-show="perms?.can && !perms.can.fbReply">Facebook: public replies, hide and delete are off until <code>pages_manage_engagement</code> is granted.</li>
                    <li x-show="perms?.can && !perms.can.igRead">Instagram: comments can't load until <code>instagram_manage_comments</code> is granted.</li>
                    <li x-show="perms?.can && !perms.can.fbPrivate">Private replies need <code>pages_messaging</code>.</li>
                </ul>
                <p class="text-xs mt-2">Grant them in the Meta app, then reconnect the brand in <a href="<?php echo esc_url($admin_url); ?>" class="underline font-medium">Admin → Social Media Connections</a>.</p>
            </div>

            <div x-show="errors && Object.keys(errors).length" x-cloak class="rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-4 text-sm text-amber-800 dark:text-amber-200">
                <p class="font-semibold mb-1">Some items could not be loaded</p>
                <template x-for="(msg, k) in (errors || {})" :key="k"><p class="text-xs mt-1"><strong x-text="errorLabel(k) + ':'"></strong> <span x-text="msg"></span></p></template>
            </div>

            <div x-show="loading" class="flex justify-center py-16">
                <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
            </div>

            <div x-show="!loading && selectedClient && filteredThreads.length === 0" x-cloak class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-10 text-center">
                <p class="text-gray-600 dark:text-gray-400 text-sm">Nothing here with these filters.</p>
                <p class="text-xs text-gray-500 mt-2" x-show="f.channel !== 'instagram'">DMs and comments appear here once Messenger or Instagram messaging is connected and active.</p>
                <div x-show="f.channel === 'instagram' && f.type === 'dm'" class="text-xs text-gray-500 mt-3 space-y-2 max-w-md mx-auto text-left">
                    <p>If Instagram DMs should appear here:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>In Instagram → Settings → Messages and story replies → turn on <strong>Connected tools</strong> / Allow access to messages.</li>
                        <li>Reconnect Facebook for this brand in Admin so the Page token includes <code class="text-[11px]">instagram_manage_messages</code>.</li>
                        <li>Your Meta app needs Advanced Access for Instagram messaging (not Development-only testers).</li>
                    </ul>
                </div>
            </div>

            <div x-show="!loading && filteredThreads.length > 0" x-cloak class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 overflow-hidden flex flex-col md:flex-row min-h-[520px] max-h-[calc(100dvh-220px)]">
                <!-- Thread list -->
                <div class="md:w-80 lg:w-96 border-b md:border-b-0 md:border-r dark:border-gray-700 flex flex-col min-h-0" :class="activeThread && 'hidden md:flex'">
                    <div class="px-4 py-3 border-b dark:border-gray-700 text-xs font-semibold text-gray-500 uppercase tracking-wide flex justify-between"><span>Inbox</span><span x-text="filteredThreads.length + ' items'"></span></div>
                    <div class="flex-1 overflow-y-auto divide-y dark:divide-gray-700">
                        <template x-for="thread in filteredThreads" :key="thread.key">
                            <button type="button" @click="openThread(thread)"
                                    class="w-full text-left px-4 py-3 flex items-start gap-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
                                    :class="activeThread?.key === thread.key ? 'msg-thread-active' : ''">
                                <div class="relative flex-shrink-0">
                                    <img x-show="thread.type === 'comment' && thread.post?.thumbnail" :src="thread.post?.thumbnail" alt="" class="w-9 h-9 rounded-lg object-cover">
                                    <div x-show="!(thread.type === 'comment' && thread.post?.thumbnail)" class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold"
                                         :class="thread.platform === 'instagram' ? 'bg-gradient-to-br from-purple-500 to-pink-500' : 'bg-blue-600'"
                                         x-text="(thread.name || '?').charAt(0).toUpperCase()"></div>
                                    <span x-show="thread.unanswered" class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-indigo-600 ring-2 ring-white dark:ring-gray-800" title="Unanswered"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="font-semibold text-sm truncate" x-text="thread.name"></p>
                                        <span class="flex gap-1 flex-shrink-0">
                                            <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300" x-text="thread.type === 'comment' ? 'Comment' : 'DM'"></span>
                                            <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded-full"
                                                  :class="thread.platform === 'instagram' ? 'bg-pink-100 text-pink-700' : 'bg-blue-100 text-blue-700'"
                                                  x-text="thread.platform === 'instagram' ? 'IG' : 'FB'"></span>
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5" x-text="thread.snippet || 'No preview'"></p>
                                    <p class="text-[10px] text-gray-400 mt-1">
                                        <span x-text="fmtTime(thread.time)"></span>
                                        <span x-show="thread.type === 'comment' && thread.post?.caption" x-text="' · on “' + (thread.post?.caption || '').slice(0, 40) + '…”'"></span>
                                        <span x-show="thread.hidden" class="text-amber-600"> · hidden</span>
                                        <span x-show="thread.status === 'done'" class="text-green-600"> · done</span>
                                        <span x-show="thread.assignedTo" class="text-indigo-600" x-text="' · ' + assigneeName(thread.assignedTo)"></span>
                                    </p>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Conversation panel -->
                <div class="flex-1 flex flex-col min-h-0 min-w-0" :class="!activeThread && 'hidden md:flex'">
                    <div x-show="!activeThread" class="flex-1 flex items-center justify-center text-sm text-gray-500 p-8 text-center">
                        Select a conversation to read and reply
                    </div>

                    <template x-if="activeThread">
                        <div class="flex flex-col flex-1 min-h-0">
                            <div class="px-4 py-3 border-b dark:border-gray-700 flex items-center gap-3 flex-shrink-0">
                                <button type="button" @click="closeThread()" class="md:hidden p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                     :class="activeThread.platform === 'instagram' ? 'bg-gradient-to-br from-purple-500 to-pink-500' : 'bg-blue-600'"
                                     x-text="(activeThread.name || '?').charAt(0).toUpperCase()"></div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold truncate" x-text="activeThread.name"></p>
                                    <p class="text-xs text-gray-500" x-text="(activeThread.platform === 'instagram' ? 'Instagram ' : 'Facebook ') + (activeThread.type === 'comment' ? 'comment' : 'DM')"></p>
                                </div>
                                <select @change="assign($event.target.value)" class="text-xs rounded-lg border dark:border-gray-600 bg-white dark:bg-gray-800 px-2 py-1 max-w-[9rem]" title="Assign" data-assign>
                                    <option value="" :selected="!activeThread.assignedTo">Unassigned</option>
                                    <template x-for="u in assignees" :key="u._id"><option :value="u._id" :selected="String(activeThread.assignedTo || '') === u._id" x-text="u.firstName + ' ' + (u.lastName || '')"></option></template>
                                </select>
                                <button type="button" @click="toggleDone()" class="text-xs px-2 py-1 rounded-lg border" :class="activeThread.status === 'done' ? 'border-gray-300 text-gray-600' : 'border-green-300 text-green-700 hover:bg-green-50'" x-text="activeThread.status === 'done' ? 'Reopen' : '✓ Mark done'"></button>
                                <button type="button" @click="loadThreadMessages()" class="text-xs text-indigo-600 hover:underline" :disabled="threadLoading">Refresh</button>
                            </div>

                            <div x-show="threadLoading" class="flex-1 flex items-center justify-center">
                                <div class="w-7 h-7 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                            </div>

                            <div x-show="threadError && !threadLoading" class="mx-4 mt-4 rounded-lg border border-amber-200 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-800 p-3 text-xs text-amber-800 dark:text-amber-200" x-text="threadError"></div>

                            <template x-if="activeThread.type === 'comment' && comment">
                                <div class="mx-4 mt-3 rounded-xl border dark:border-gray-700 p-3 flex gap-3 items-start bg-gray-50 dark:bg-gray-900/40" data-post-context>
                                    <img x-show="comment.postThumbnail" :src="comment.postThumbnail" alt="" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[10px] uppercase font-bold text-gray-400">Comment on post · <span x-text="fmtTime(comment.postTime)"></span></p>
                                        <p class="text-xs text-gray-700 dark:text-gray-300 line-clamp-3" x-text="comment.postCaption || '(no caption)'"></p>
                                        <a x-show="comment.postPermalink" :href="comment.postPermalink" target="_blank" rel="noopener" class="text-xs text-indigo-600 hover:underline">Open post ↗</a>
                                    </div>
                                    <div class="flex flex-col gap-1 flex-shrink-0">
                                        <button type="button" @click="hideComment(!comment.hidden)" :disabled="modBusy || comment.canHide === false || (perms?.can && !(comment.platform === 'instagram' ? perms.can.igModerate : perms.can.fbModerate))"
                                                class="text-xs px-2 py-1 rounded-lg border border-amber-300 text-amber-700 hover:bg-amber-50 disabled:opacity-40" x-text="comment.hidden ? 'Unhide' : 'Hide'"></button>
                                        <button type="button" @click="deleteComment()" :disabled="modBusy || comment.canDelete === false || (perms?.can && !(comment.platform === 'instagram' ? perms.can.igModerate : perms.can.fbModerate))"
                                                class="text-xs px-2 py-1 rounded-lg border border-red-300 text-red-700 hover:bg-red-50 disabled:opacity-40">Delete</button>
                                    </div>
                                </div>
                            </template>
                            <p x-show="modError" class="mx-4 mt-2 text-xs text-red-600" x-text="modError"></p>

                            <div x-show="!threadLoading" class="flex-1 overflow-y-auto p-4 space-y-3" id="msg-scroll-area">
                                <template x-for="msg in threadMessages" :key="msg.id">
                                    <div class="flex" :class="msg.isPage ? 'justify-end' : 'justify-start'">
                                        <div class="max-w-[85%] rounded-2xl px-4 py-2 text-sm shadow-sm"
                                             :class="msg.isPage ? 'msg-bubble-page rounded-br-md' : 'msg-bubble-user rounded-bl-md dark:bg-gray-700'">
                                            <p class="whitespace-pre-wrap break-words" x-text="msg.text"></p>
                                            <p class="text-[10px] mt-1 opacity-70" x-text="fmtTime(msg.createdTime) + (msg.isPage ? ' · You' : '')"></p>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="!threadLoading && threadMessages.length === 0" class="text-center text-sm text-gray-500 py-8">No messages loaded for this thread.</div>
                            </div>

                            <div class="border-t dark:border-gray-700 p-3 flex-shrink-0 bg-gray-50 dark:bg-gray-900/50">
                                <!-- AI suggested replies: staff choose and edit; nothing is sent automatically -->
                                <div x-show="ai.enabled" class="mb-2" data-ai-replies>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <button type="button" @click="suggestReplies()" :disabled="ai.loading || !lastIncoming()"
                                                class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-indigo-300 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 disabled:opacity-50">
                                            <span x-show="!ai.loading">✨ Suggest replies</span><span x-show="ai.loading">Thinking…</span>
                                        </button>
                                        <select x-show="ai.brands.length" x-model="ai.brandId" class="text-xs rounded-lg border dark:border-gray-600 bg-white dark:bg-gray-800 px-2 py-1">
                                            <template x-for="b in ai.brands" :key="b._id"><option :value="b._id" x-text="b.name + (b.approved ? '' : ' (awaiting client approval)')" :disabled="!b.approved"></option></template>
                                        </select>
                                        <span class="text-[10px] text-gray-400">Suggestions only. You review, edit and send.</span>
                                    </div>
                                    <p x-show="ai.error" class="text-xs text-red-600 mt-1" x-text="ai.error"></p>
                                    <div x-show="ai.sensitive" class="mt-2 rounded-lg border border-amber-300 bg-amber-50 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 text-xs p-2">
                                        <strong>Needs a person:</strong> flagged as <span x-text="ai.categories.join(', ')"></span>. The suggestions below are holding replies only. Check with the account lead before promising anything.
                                    </div>
                                    <p x-show="ai.note" class="text-[11px] text-gray-500 mt-1" x-text="ai.note"></p>
                                    <div class="mt-2 grid gap-2" x-show="ai.suggestions.length">
                                        <template x-for="(s, i) in ai.suggestions" :key="i">
                                            <button type="button" @click="useSuggestion(i)"
                                                    :class="ai.chosen === i ? 'ring-2 ring-indigo-500' : ''"
                                                    class="text-left text-xs rounded-lg border dark:border-gray-600 bg-white dark:bg-gray-800 p-2 hover:border-indigo-400">
                                                <span x-text="s"></span>
                                                <span x-show="(ai.flags[i] || []).length" class="block mt-1 text-[10px] text-red-600" x-text="'Contains words to avoid: ' + (ai.flags[i] || []).join(', ')"></span>
                                            </button>
                                        </template>
                                        <p class="text-[10px] text-gray-400">Click one to put it in the reply box, then edit before sending.</p>
                                    </div>
                                    <label x-show="ai.sensitive && ai.chosen !== null" class="mt-2 flex items-center gap-2 text-xs text-amber-800 dark:text-amber-200">
                                        <input type="checkbox" x-model="ai.acknowledged"> A person has checked this reply for a sensitive message
                                    </label>
                                </div>
                                <div x-show="activeThread.type === 'comment'" class="flex items-center gap-2 mb-2 text-xs" data-reply-mode>
                                    <span class="text-gray-500">Reply</span>
                                    <div class="hub-segmented">
                                        <button type="button" class="hub-segmented__item" :class="replyMode === 'public' ? 'is-active' : ''" @click="replyMode = 'public'">Publicly on the post</button>
                                        <button type="button" class="hub-segmented__item" :class="replyMode === 'private' ? 'is-active' : ''" :disabled="comment?.privateReplySent || comment?.canReplyPrivately === false" @click="replyMode = 'private'">Privately (DM)</button>
                                    </div>
                                    <span x-show="replyMode === 'private'" class="text-[10px] text-gray-400">One private reply per comment, within 7 days.</span>
                                    <span x-show="comment?.privateReplySent" class="text-[10px] text-green-600">Private reply sent.</span>
                                </div>
                                <div class="flex gap-2 items-end">
                                    <textarea x-model="replyText" rows="2" placeholder="Type a reply…"
                                              @keydown.meta.enter="sendReply()" @keydown.ctrl.enter="sendReply()"
                                              class="flex-1 resize-none rounded-xl border dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                                    <button type="button" @click="sendReply()" :disabled="sending || !replyText.trim() || (ai.sensitive && ai.chosen !== null && !ai.acknowledged)"
                                            class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0">
                                        <span x-show="!sending">Send</span>
                                        <span x-show="sending">…</span>
                                    </button>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-2" x-text="activeThread.type === 'comment' ? (replyMode === 'public' ? 'Public replies appear under the comment on the post. Cmd/Ctrl+Enter to send.' : 'Sends one private message to the commenter. Cmd/Ctrl+Enter to send.') : 'Replies must be sent within Meta’s 24-hour messaging window. Cmd/Ctrl+Enter to send.'"></p>
                                <p x-show="sendError" class="text-xs text-red-600 mt-1" x-text="sendError"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function messagesApp() {
    return {
        authChecked: false,
        loadError: '',
        user: null,
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
        viewMode: window.HubAuth.resolveViewMode(window.HubAuth.readStoredViewMode(), (() => { try { return JSON.parse(localStorage.getItem('user') || '{}') || {}; } catch (e) { return {}; } })()), // provisional until /auth/me answers
        isSidebarOpen: true,
        loading: false,
        clients: [],
        selectedClient: null,
        selectedClientName: '',
        threads: [],
        filteredThreads: [],
        platforms: {},
        errors: null,
        f: { channel: 'all', type: 'all', status: 'open', unanswered: false, assigned: '' },
        perms: null,
        assignees: [],
        comment: null,
        replyMode: 'public',
        modBusy: false,
        modError: null,
        activeThread: null,
        threadMessages: [],
        threadLoading: false,
        threadError: null,
        replyText: '',
        sending: false,
        sendError: null,
        ai: { enabled: false, brands: [], brandId: '', loading: false, error: null, suggestions: [], flags: [], sensitive: false, categories: [], note: null, generationId: null, chosen: null, acknowledged: false },

        resetAi() {
            Object.assign(this.ai, { loading: false, error: null, suggestions: [], flags: [], sensitive: false, categories: [], note: null, generationId: null, chosen: null, acknowledged: false });
        },

        async loadAiStatus() {
            try {
                const r = await fetch(`${API_URL}/ai-studio/status`, { headers: this.headers() });
                const d = await r.json();
                this.ai.enabled = Boolean(d.enabled);
            } catch (e) { this.ai.enabled = false; }
        },

        async loadAiBrands() {
            this.ai.brands = []; this.ai.brandId = '';
            if (!this.ai.enabled || !this.selectedClient) return;
            try {
                const r = await fetch(`${API_URL}/ai-studio/brands/${this.selectedClient}/reply-profiles`, { headers: this.headers() });
                const d = await r.json();
                if (d.success) {
                    this.ai.brands = d.brands || [];
                    const first = this.ai.brands.find(b => b.approved);
                    this.ai.brandId = first ? first._id : '';
                }
            } catch (e) { /* optional */ }
        },

        lastIncoming() {
            const list = this.threadMessages || [];
            for (let i = list.length - 1; i >= 0; i--) if (!list[i].isPage && list[i].text) return list[i].text;
            return '';
        },

        async suggestReplies() {
            const incoming = this.lastIncoming();
            if (!incoming || this.ai.loading) return;
            this.resetAi();
            this.ai.loading = true;
            try {
                const r = await fetch(`${API_URL}/ai-studio/brands/${this.selectedClient}/replies`, {
                    method: 'POST', headers: this.headers(),
                    body: JSON.stringify({
                        itemType: this.activeThread.type === 'comment' ? 'comment' : 'dm', platform: this.activeThread.platform, threadId: this.activeThread.id,
                        incomingText: incoming, brandId: this.ai.brandId || undefined,
                        thread: (this.threadMessages || []).slice(-10).map(m => ({ text: m.text || '', isPage: Boolean(m.isPage) }))
                    })
                });
                const d = await r.json();
                if (!d.success) { this.ai.error = d.message || 'Could not get suggestions'; return; }
                Object.assign(this.ai, { suggestions: d.suggestions || [], flags: d.flags || [], sensitive: Boolean(d.sensitive), categories: d.categories || [], note: d.note || null, generationId: d.generationId });
            } catch (e) {
                this.ai.error = 'Could not get suggestions';
            } finally {
                this.ai.loading = false;
            }
        },

        useSuggestion(i) {
            this.ai.chosen = i;
            this.replyText = this.ai.suggestions[i] || '';
        },

        async init() {
            const token = localStorage.getItem('token');
            if (!token) { this.authChecked = true; return; }
            try {
                const res = await fetch(`${API_URL}/auth/me`, { headers: { Authorization: `Bearer ${token}` } });
                if (res.status === 401 || res.status === 403) throw new Error('auth');
                if (!res.ok) throw new Error('server');
                const data = await res.json();
                this.user = data.user;
                window.HubAuth.setUser(data.user);
                localStorage.setItem('user', JSON.stringify(data.user));
                if (this.user.role === 'client') {
                    window.location.href = DASHBOARD_URL;
                    return;
                }
                // Staff-only page: derive the view mode from the verified role; a super admin previewing as client falls back to admin.
                this.viewMode = window.HubAuth.applyViewMode(window.HubAuth.readStoredViewMode() === 'client' ? 'admin' : undefined);
                await this.loadAiStatus();
                await this.loadClients();
                const saved = localStorage.getItem('selectedClientId') || localStorage.getItem('hubSelectedClientId');
                if (saved && this.clients.some(c => c._id === saved)) {
                    const client = this.clients.find(c => c._id === saved);
                    await this.selectClient(client);
                } else if (this.clients.length === 1) {
                    await this.selectClient(this.clients[0]);
                }
            } catch (e) {
                if (e && e.message === 'auth') {
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    this.user = null;
                    this.loadError = 'Your session has expired. Please sign in again.';
                } else if (e && e.message === 'server') {
                    this.user = null;
                    this.loadError = 'We could not verify your session. Please try again in a moment.';
                } else {
                    this.showToast('Some data could not be loaded. Please refresh to try again.', 'error');
                }
            } finally {
                this.authChecked = true;
            }
        },

        headers() {
            return { Authorization: `Bearer ${localStorage.getItem('token')}`, 'Content-Type': 'application/json' };
        },

        async loadClients() {
            const res = await fetch(`${API_URL}/calendar/clients`, { headers: this.headers() });
            const data = await res.json();
            if (data.success) this.clients = window.HubAuth.activeClients(data.data);
        },

        async selectClient(client) {
            this.selectedClient = client._id;
            this.selectedClientName = client.brandName || client.name;
            localStorage.setItem('hubSelectedClientId', client._id);
            localStorage.setItem('selectedClientId', client._id);
            this.activeThread = null;
            this.threadMessages = [];
            this.resetAi();
            await Promise.all([this.loadAiBrands(), this.loadInboxMeta()]);
            await this.loadMessages();
        },

        async loadMessages(refresh = false) {
            if (!this.selectedClient) return;
            this.loading = true;
            this.errors = null;
            try {
                const res = await fetch(`${API_URL}/inbox/${this.selectedClient}?status=all${refresh ? '&refresh=1' : ''}`, { headers: this.headers() });
                const data = await res.json();
                if (data.success) {
                    this.threads = data.items || [];
                    this.errors = data.errors;
                    this.filterThreads();
                    if (this.activeThread) {
                        const fresh = this.threads.find(t => t.key === this.activeThread.key);
                        if (fresh) Object.assign(this.activeThread, fresh);
                    }
                } else {
                    this.errors = { inbox: data.message || 'Could not load the inbox' };
                }
            } catch (e) {
                this.errors = { inbox: 'Could not load the inbox' };
            } finally {
                this.loading = false;
            }
        },

        async loadInboxMeta() {
            this.perms = null; this.assignees = [];
            if (!this.selectedClient) return;
            const [p, a] = await Promise.all([
                fetch(`${API_URL}/inbox/${this.selectedClient}/permissions`, { headers: this.headers() }).then(r => r.json()).catch(() => null),
                fetch(`${API_URL}/inbox/${this.selectedClient}/assignees`, { headers: this.headers() }).then(r => r.json()).catch(() => null)
            ]);
            if (p && p.success) this.perms = p;
            if (a && a.success) this.assignees = a.users || [];
        },

        matches(t, ignoreType = false) {
            const f = this.f; const me = this.user?._id || this.user?.id;
            if (f.channel !== 'all' && t.platform !== f.channel) return false;
            if (!ignoreType && f.type !== 'all' && t.type !== f.type) return false;
            if (f.status !== 'all' && t.status !== f.status) return false;
            if (f.unanswered && !t.unanswered) return false;
            if (f.assigned === 'me' && String(t.assignedTo || '') !== String(me)) return false;
            if (f.assigned === 'unassigned' && t.assignedTo) return false;
            if (f.assigned && !['me', 'unassigned'].includes(f.assigned) && String(t.assignedTo || '') !== f.assigned) return false;
            return true;
        },

        countOf(type) {
            const n = this.threads.filter(t => this.matches(t, true) && (type === 'all' || t.type === type)).length;
            return n ? n : '';
        },

        filterThreads() {
            this.filteredThreads = this.threads.filter(t => this.matches(t));
            if (this.activeThread && !this.filteredThreads.some(t => t.key === this.activeThread.key)) {
                this.closeThread();
            }
        },

        errorLabel(k) {
            return ({ facebook_dm: 'Messenger', instagram_dm: 'Instagram DMs', facebook_comments: 'Facebook comments', instagram_comments: 'Instagram comments', inbox: 'Inbox' })[k] || k;
        },

        assigneeName(id) {
            const u = this.assignees.find(x => x._id === String(id));
            return u ? `${u.firstName} ${(u.lastName || '').charAt(0)}.`.trim() : 'Assigned';
        },

        openThread(thread) {
            this.activeThread = thread;
            this.resetAi();
            this.replyText = '';
            this.replyMode = 'public';
            this.comment = null;
            this.modError = null;
            this.sendError = null;
            this.threadError = null;
            this.loadThreadMessages();
        },

        closeThread() {
            this.activeThread = null;
            this.threadMessages = [];
            this.comment = null;
            this.replyText = '';
            this.threadError = null;
            this.sendError = null;
        },

        async loadThreadMessages() {
            if (!this.selectedClient || !this.activeThread) return;
            this.threadLoading = true;
            this.threadError = null;
            try {
                if (this.activeThread.type === 'comment') {
                    const res = await fetch(`${API_URL}/inbox/${this.selectedClient}/comments/${encodeURIComponent(this.activeThread.id)}`, { headers: this.headers() });
                    const data = await res.json();
                    if (!data.success) { this.threadError = data.message || 'Could not load comment'; this.threadMessages = []; return; }
                    this.comment = data.comment;
                    const c = data.comment;
                    this.threadMessages = [{ id: c.externalId, text: c.text, isPage: false, fromName: c.authorName, createdTime: c.createdTime }]
                        .concat((c.replies || []).map(r => ({ id: r.externalId, text: r.text, isPage: Boolean(r.fromPage), fromName: r.authorName, createdTime: r.createdTime })));
                } else {
                    const params = new URLSearchParams();
                    if (this.activeThread.recipientId) params.set('recipientId', this.activeThread.recipientId);
                    const res = await fetch(
                        `${API_URL}/social-media/messages/${this.selectedClient}/threads/${encodeURIComponent(this.activeThread.id)}?${params}`,
                        { headers: this.headers() }
                    );
                    const data = await res.json();
                    if (data.success) {
                        this.threadMessages = data.data.messages || [];
                        if (data.data.error) this.threadError = data.data.error;
                    } else {
                        this.threadError = data.message || 'Could not load messages';
                        this.threadMessages = [];
                    }
                }
                this.$nextTick(() => {
                    const el = document.getElementById('msg-scroll-area');
                    if (el) el.scrollTop = el.scrollHeight;
                });
            } catch (e) {
                this.threadError = 'Failed to load conversation';
                this.threadMessages = [];
            } finally {
                this.threadLoading = false;
            }
        },

        async setState(body) {
            const res = await fetch(`${API_URL}/inbox/${this.selectedClient}/state`, {
                method: 'PATCH', headers: this.headers(), body: JSON.stringify({ key: this.activeThread.key, ...body })
            });
            const d = await res.json();
            if (!d.success) { this.sendError = d.message || 'Could not update'; return null; }
            return d.state;
        },

        async toggleDone() {
            const done = this.activeThread.status !== 'done';
            const st = await this.setState({ status: done ? 'done' : 'open', lastActivity: this.activeThread.time });
            if (!st) return;
            this.activeThread.status = done ? 'done' : 'open';
            const t = this.threads.find(x => x.key === this.activeThread.key); if (t) t.status = this.activeThread.status;
            this.filterThreads();
        },

        async assign(userId) {
            const st = await this.setState({ assignedTo: userId || null });
            if (!st) return;
            this.activeThread.assignedTo = userId || null;
            const t = this.threads.find(x => x.key === this.activeThread.key); if (t) t.assignedTo = this.activeThread.assignedTo;
            this.filterThreads();
        },

        async hideComment(hidden) {
            this.modBusy = true; this.modError = null;
            try {
                const res = await fetch(`${API_URL}/inbox/${this.selectedClient}/comments/${encodeURIComponent(this.comment.externalId)}/hide`, { method: 'POST', headers: this.headers(), body: JSON.stringify({ hidden }) });
                const d = await res.json();
                if (!d.success) { this.modError = d.message; return; }
                this.comment.hidden = hidden; this.activeThread.hidden = hidden;
            } finally { this.modBusy = false; }
        },

        async deleteComment() {
            if (!confirm('Delete this comment from the post? This cannot be undone.')) return;
            this.modBusy = true; this.modError = null;
            try {
                const res = await fetch(`${API_URL}/inbox/${this.selectedClient}/comments/${encodeURIComponent(this.comment.externalId)}`, { method: 'DELETE', headers: this.headers() });
                const d = await res.json();
                if (!d.success) { this.modError = d.message; return; }
                const key = this.activeThread.key;
                this.threads = this.threads.filter(t => t.key !== key);
                this.closeThread(); this.filterThreads();
            } finally { this.modBusy = false; }
        },

        async sendReply() {
            if (!this.activeThread || !this.replyText.trim() || this.sending) return;
            const isComment = this.activeThread.type === 'comment';
            if (!isComment && !this.activeThread.recipientId) {
                this.sendError = 'Cannot reply — missing recipient ID. Refresh the inbox and try again.';
                return;
            }
            this.sending = true;
            this.sendError = null;
            const ai = {
                aiGenerationId: this.ai.chosen !== null ? this.ai.generationId : undefined,
                suggestionIndex: this.ai.chosen !== null ? this.ai.chosen : undefined,
                sensitiveAcknowledged: this.ai.acknowledged
            };
            try {
                const url = isComment
                    ? `${API_URL}/inbox/${this.selectedClient}/comments/${encodeURIComponent(this.activeThread.id)}/reply`
                    : `${API_URL}/social-media/messages/${this.selectedClient}/reply`;
                const body = isComment
                    ? { text: this.replyText.trim(), mode: this.replyMode, ...ai }
                    : { recipientId: this.activeThread.recipientId, text: this.replyText.trim(), platform: this.activeThread.platform, threadId: this.activeThread.id, incomingText: this.lastIncoming(), ...ai };
                const res = await fetch(url, { method: 'POST', headers: this.headers(), body: JSON.stringify(body) });
                const data = await res.json();
                if (data.success) {
                    this.replyText = '';
                    this.resetAi();
                    this.activeThread.unanswered = false;
                    const t = this.threads.find(x => x.key === this.activeThread.key); if (t) t.unanswered = false;
                    await this.loadThreadMessages();
                } else {
                    this.sendError = data.message || 'Failed to send reply';
                }
            } catch (e) {
                this.sendError = 'Failed to send reply';
            } finally {
                this.sending = false;
            }
        },

        fmtTime(value) {
            if (!value) return '';
            return new Date(value).toLocaleString('en-GB', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
        },

        logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = LOGIN_URL;
        }
    };
}
</script>

<?php include get_template_directory() . '/inc/change-password-modal.php'; ?>
<?php esirom_hub_staff_mobile_nav('messages'); ?>
</body>
</html>
