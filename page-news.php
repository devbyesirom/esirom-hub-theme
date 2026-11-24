<?php
/**
 * Template Name: Latest News
 * Description: Latest News from esirom.com for staff and clients, plus the admin-only monthly newsletter review
 */
if (!defined('ABSPATH')) exit;

show_admin_bar(false);

$api_url = get_option('esirom_api_url', 'https://esirom-hub-backend-production.up.railway.app/api');
$login_url = esc_url(esirom_hub_page_url('login'));
$dashboard_url = esc_url(esirom_hub_page_url('dashboard'));
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <title>Latest News — Agency Hub</title>
    <?php esirom_hub_head_assets(array('pwa' => true)); ?>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        const API_URL = '<?php echo esc_js($api_url); ?>';
        const LOGIN_URL = '<?php echo esc_js($login_url); ?>';
        const DASHBOARD_URL = '<?php echo esc_js($dashboard_url); ?>';
    </script>
    <style>
        <?php esirom_hub_layout_styles(); ?>
    </style>
</head>
<body class="hub-has-mobile-nav h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 pb-16 md:pb-0" x-data="newsApp()" x-init="init()">

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
            <?php esirom_hub_client_sidebar_nav('news', 'site', false); ?>
            <?php esirom_hub_staff_sidebar_nav('news', 'site', false); ?>
        </nav>
        <div class="p-3 border-t border-gray-200 dark:border-gray-700/50">
            <?php esirom_hub_staff_sidebar_footer('site', false); ?>
        </div>
    </aside>

    <main class="hub-app-main">
        <header class="hub-sticky-header px-4 sm:px-6 py-3">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="min-w-0">
                    <h1 class="hub-page-title">Latest News</h1>
                    <p class="hub-page-subtitle">What is moving in Jamaican business and marketing, from the Esirom blog.</p>
                </div>
                <div class="flex items-center gap-2"><?php esirom_hub_account_menu(); ?></div>
            </div>
            <div x-show="isAdmin" x-cloak class="mt-3 flex gap-1 border-b border-gray-200 dark:border-gray-700" role="tablist">
                <button type="button" role="tab" @click="setTab('news')" :aria-selected="tab === 'news'" class="px-4 py-2 text-sm font-semibold border-b-2 -mb-px" :class="tab === 'news' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'">News</button>
                <button type="button" role="tab" @click="setTab('newsletter')" :aria-selected="tab === 'newsletter'" class="px-4 py-2 text-sm font-semibold border-b-2 -mb-px" :class="tab === 'newsletter' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'">Monthly newsletter</button>
            </div>
        </header>

        <div class="hub-page-content p-4 sm:p-6 space-y-4">
            <div x-show="toast.show" x-cloak x-transition class="fixed bottom-20 md:bottom-6 right-4 z-[100] max-w-sm px-4 py-3 rounded-xl shadow-lg text-sm text-white" :class="toast.type === 'error' ? 'bg-red-600' : 'bg-emerald-600'" x-text="toast.message"></div>

            <!-- News feed -->
            <section x-show="tab === 'news'" x-cloak>
                <div x-show="loading" class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    <template x-for="n in 6" :key="'sk-' + n"><div class="hub-skeleton h-72 rounded-2xl"></div></template>
                </div>
                <div x-show="!loading && error" class="hub-card p-8 text-center max-w-lg mx-auto">
                    <p class="hub-empty__title">Latest News is unavailable right now</p>
                    <p class="hub-empty__text mt-1" x-text="error"></p>
                    <a href="https://esirom.com/news/" target="_blank" rel="noopener" class="hub-btn hub-btn--secondary mt-4 inline-block">Read it on esirom.com</a>
                </div>
                <div x-show="!loading && !error" class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    <template x-for="post in posts" :key="post.id">
                        <article class="group flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                            <a :href="post.link" target="_blank" rel="noopener" class="block aspect-[16/9] w-full bg-gray-100 dark:bg-gray-700" tabindex="-1" aria-hidden="true">
                                <img x-show="post.image" :src="post.image" alt="" loading="lazy" class="h-full w-full object-cover">
                            </a>
                            <div class="flex flex-1 flex-col p-5">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-indigo-600 dark:text-indigo-400"><span x-text="post.category || 'News'"></span> · <span x-text="formatDate(post.date)"></span></p>
                                <h2 class="mt-1.5 text-base font-bold leading-snug text-gray-900 dark:text-white"><a :href="post.link" target="_blank" rel="noopener" class="hover:text-indigo-600 dark:hover:text-indigo-400" x-text="post.title"></a></h2>
                                <p class="mt-2 line-clamp-4 flex-1 text-sm text-gray-600 dark:text-gray-400" x-text="post.excerpt"></p>
                                <a :href="post.link" target="_blank" rel="noopener" class="mt-4 text-sm font-semibold text-indigo-600 hover:underline dark:text-indigo-400">Read the full story on esirom.com &rarr;</a>
                            </div>
                        </article>
                    </template>
                </div>
                <div x-show="!loading && !error && posts.length < total" class="mt-6 text-center">
                    <button type="button" @click="loadMore()" :disabled="loadingMore" class="hub-btn hub-btn--secondary disabled:opacity-60"><span x-text="loadingMore ? 'Loading…' : 'Load more'"></span></button>
                </div>
            </section>

            <!-- Newsletter (admins only) -->
            <section x-show="tab === 'newsletter' && isAdmin" x-cloak class="space-y-4">
                <div class="hub-card p-5 border border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-700/50 dark:bg-amber-900/20 dark:text-amber-100 rounded-xl text-sm">
                    <p class="font-semibold">Nothing is sent to clients automatically.</p>
                    <p class="mt-1">On the first business day of each month a draft of the previous month's Latest News is created and the admins are emailed. It goes to client contacts only when an admin presses <strong>Approve and send</strong> below. People who unsubscribed are skipped.</p>
                </div>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="space-y-3 lg:col-span-1">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-bold text-gray-900 dark:text-white">Issues</h2>
                            <button type="button" @click="generateDraft()" :disabled="busy" class="text-xs font-semibold text-indigo-600 hover:underline dark:text-indigo-400 disabled:opacity-60">Draft last month</button>
                        </div>
                        <p x-show="!issues.length && !issuesLoading" class="text-sm text-gray-500 dark:text-gray-400">No issues yet. Use “Draft last month” to create one.</p>
                        <template x-for="issue in issues" :key="issue._id">
                            <button type="button" @click="selectIssue(issue)" class="w-full rounded-xl border p-4 text-left transition" :class="selected && selected._id === issue._id ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 dark:border-indigo-400' : 'border-gray-200 bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700/40'">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-semibold text-sm text-gray-900 dark:text-white" x-text="issue.label"></span>
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold" :class="statusClass(issue.status)" x-text="statusLabel(issue.status)"></span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400"><span x-text="issue.posts.length"></span> stories<span x-show="issue.status === 'sent'"> · sent to <span x-text="issue.stats.sent"></span> of <span x-text="issue.stats.recipients"></span></span></p>
                            </button>
                        </template>
                    </div>

                    <div class="lg:col-span-2">
                        <div x-show="!selected" class="hub-card p-8 text-center text-sm text-gray-500 dark:text-gray-400 rounded-xl border border-dashed border-gray-300 dark:border-gray-600">Select an issue to preview it.</div>
                        <div x-show="selected" x-cloak class="space-y-4">
                            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800 space-y-3">
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400">Subject</label>
                                    <input type="text" x-model="form.subject" :disabled="!isDraft" maxlength="150" class="hub-input mt-1 disabled:opacity-60">
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400">Opening paragraph</label>
                                    <textarea x-model="form.intro" :disabled="!isDraft" rows="3" maxlength="800" class="hub-input mt-1 disabled:opacity-60"></textarea>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="button" x-show="isDraft" @click="saveEdits()" :disabled="busy" class="hub-btn hub-btn--secondary hub-btn--sm">Save changes</button>
                                    <button type="button" @click="sendTest()" :disabled="busy" class="hub-btn hub-btn--secondary hub-btn--sm">Email me a test</button>
                                    <button type="button" x-show="isDraft" @click="confirmOpen = true" :disabled="busy" class="hub-btn hub-btn--primary hub-btn--sm">Approve and send to clients</button>
                                    <span x-show="selected && selected.status === 'sending'" class="text-xs font-semibold text-amber-600 dark:text-amber-300">Sending… this page refreshes itself.</span>
                                    <span x-show="selected && selected.status === 'sent'" class="text-xs text-gray-500 dark:text-gray-400">Sent <span x-text="formatDate(selected.sentAt)"></span> · <span x-text="selected.stats.sent"></span> delivered<span x-show="selected.stats.failed"> · <span x-text="selected.stats.failed"></span> failed</span><span x-show="selected.stats.optedOut"> · <span x-text="selected.stats.optedOut"></span> opted out</span></span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Audience today: <strong x-text="audience.recipients"></strong> client contacts<span x-show="audience.optedOut"> (<span x-text="audience.optedOut"></span> unsubscribed and skipped)</span>.</p>
                            </div>
                            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700">
                                <iframe :srcdoc="previewHtml" sandbox="" title="Newsletter preview" class="h-[720px] w-full bg-white"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>

<!-- Send confirmation -->
<div x-show="confirmOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @keydown.escape.window="confirmOpen = false">
    <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-2xl dark:bg-gray-800" @click.outside="confirmOpen = false">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Send to clients?</h3>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">This emails the <strong x-text="selected && selected.label"></strong> newsletter to <strong x-text="audience.recipients"></strong> client contacts. It cannot be recalled.</p>
        <div class="mt-5 flex justify-end gap-2">
            <button type="button" @click="confirmOpen = false" class="hub-btn hub-btn--secondary hub-btn--sm">Cancel</button>
            <button type="button" @click="approveAndSend()" :disabled="busy" class="hub-btn hub-btn--primary hub-btn--sm">Yes, send it</button>
        </div>
    </div>
</div>

<script>
function newsApp() {
    return {
        authChecked: false, loadError: '', user: null, viewMode: 'client', isSidebarOpen: true,
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
        tab: 'news',
        posts: [], total: 0, page: 1, loading: true, loadingMore: false, error: '',
        issues: [], issuesLoading: false, selected: null, previewHtml: '', audience: { recipients: 0, optedOut: 0 },
        form: { subject: '', intro: '' }, busy: false, confirmOpen: false, pollTimer: null,
        toast: { show: false, message: '', type: 'success' },
        showToast(message, type = 'success') { this.toast = { show: true, message, type }; setTimeout(() => { this.toast.show = false; }, 4500); },
        get isAdmin() { return this.user && this.user.role === 'admin' && this.viewMode !== 'client'; },
        get isDraft() { return this.selected && this.selected.status === 'draft'; },
        authHeaders(json) { const h = { Authorization: `Bearer ${localStorage.getItem('token')}` }; if (json) h['Content-Type'] = 'application/json'; return h; },
        formatDate(v) { const d = new Date(v); return Number.isNaN(d.getTime()) ? '' : d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', timeZone: 'America/Jamaica' }); },
        statusLabel(s) { return { draft: 'Draft', sending: 'Sending', sent: 'Sent' }[s] || s; },
        statusClass(s) { return { draft: 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200', sending: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200', sent: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200' }[s] || ''; },
        async init() {
            const token = localStorage.getItem('token');
            if (!token) { this.authChecked = true; return; }
            try {
                const res = await fetch(`${API_URL}/auth/me`, { headers: this.authHeaders() });
                if (res.status === 401 || res.status === 403) throw new Error('auth');
                if (!res.ok) throw new Error('server');
                const data = await res.json();
                this.user = data.user; window.HubAuth.setUser(data.user); localStorage.setItem('user', JSON.stringify(data.user));
                this.viewMode = window.HubAuth.applyViewMode(this.user.role === 'client' ? 'client' : undefined);
                if (location.hash === '#newsletter' && this.isAdmin) this.tab = 'newsletter';
                await this.loadNews(true);
                if (this.isAdmin) this.loadIssues();
            } catch (e) {
                if (e && e.message === 'auth') { localStorage.removeItem('token'); localStorage.removeItem('user'); this.user = null; this.loadError = 'Your session has expired. Please sign in again.'; }
                else { this.user = null; this.loadError = 'We could not verify your session. Please try again in a moment.'; }
            } finally { this.authChecked = true; }
        },
        setTab(t) { this.tab = t; history.replaceState(null, '', t === 'newsletter' ? '#newsletter' : location.pathname + location.search); },
        async loadNews(reset) {
            if (reset) { this.loading = true; this.page = 1; }
            this.error = '';
            try {
                const res = await fetch(`${API_URL}/news?limit=12&page=${this.page}`, { headers: this.authHeaders() });
                const data = await res.json().catch(() => ({}));
                if (!res.ok || !data.success) throw new Error('unavailable');
                this.posts = reset ? data.posts : this.posts.concat(data.posts);
                this.total = data.total || this.posts.length;
            } catch (e) { this.error = 'We could not reach esirom.com just now. Please try again shortly.'; }
            finally { this.loading = false; this.loadingMore = false; }
        },
        async loadMore() { this.loadingMore = true; this.page += 1; await this.loadNews(false); },
        async loadIssues() {
            this.issuesLoading = true;
            try {
                const res = await fetch(`${API_URL}/newsletters`, { headers: this.authHeaders() });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Could not load newsletters');
                this.issues = data.data || []; this.audience = data.audience || this.audience;
                const keep = this.selected && this.issues.find((i) => i._id === this.selected._id);
                if (keep) await this.selectIssue(keep, true); else if (this.issues.length) await this.selectIssue(this.issues[0]);
                this.pollIfSending();
            } catch (e) { this.showToast(e.message, 'error'); } finally { this.issuesLoading = false; }
        },
        pollIfSending() {
            clearTimeout(this.pollTimer);
            if (this.issues.some((i) => i.status === 'sending')) this.pollTimer = setTimeout(() => this.loadIssues(), 8000);
        },
        async selectIssue(issue, silent) {
            this.selected = issue; this.form = { subject: issue.subject, intro: issue.intro || '' };
            try {
                const res = await fetch(`${API_URL}/newsletters/${issue._id}/preview`, { headers: this.authHeaders() });
                const data = await res.json();
                this.previewHtml = data.html || '';
            } catch (e) { if (!silent) this.showToast('Could not load the preview', 'error'); }
        },
        async api(path, method, body) {
            const res = await fetch(`${API_URL}/newsletters${path}`, { method, headers: this.authHeaders(true), body: body ? JSON.stringify(body) : undefined });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(data.message || 'Something went wrong');
            return data;
        },
        async generateDraft() {
            this.busy = true;
            try {
                const out = await this.api('/generate', 'POST', {});
                this.showToast(out.created ? 'Draft created' : (out.reason || 'A newsletter for last month already exists'), out.created ? 'success' : 'error');
                await this.loadIssues();
            } catch (e) { this.showToast(e.message, 'error'); } finally { this.busy = false; }
        },
        async saveEdits() {
            this.busy = true;
            try { await this.api(`/${this.selected._id}`, 'PATCH', this.form); this.showToast('Saved'); await this.loadIssues(); }
            catch (e) { this.showToast(e.message, 'error'); } finally { this.busy = false; }
        },
        async sendTest() {
            this.busy = true;
            try { if (this.isDraft) await this.api(`/${this.selected._id}`, 'PATCH', this.form); const out = await this.api(`/${this.selected._id}/test`, 'POST', {}); this.showToast(`Test sent to ${out.sentTo}`); }
            catch (e) { this.showToast(e.message, 'error'); } finally { this.busy = false; }
        },
        async approveAndSend() {
            this.busy = true;
            try {
                await this.api(`/${this.selected._id}`, 'PATCH', this.form);
                const out = await this.api(`/${this.selected._id}/send`, 'POST', { confirm: true });
                this.confirmOpen = false; this.showToast(out.message || 'Sending…');
                await this.loadIssues();
            } catch (e) { this.showToast(e.message, 'error'); } finally { this.busy = false; }
        },
        logout() { localStorage.removeItem('token'); localStorage.removeItem('user'); window.location.href = LOGIN_URL; }
    };
}
</script>

<?php include get_template_directory() . '/inc/change-password-modal.php'; ?>
<?php esirom_hub_mobile_nav('news', 'site'); ?>
</body>
</html>
