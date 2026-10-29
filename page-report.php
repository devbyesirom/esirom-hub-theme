<?php
/**
 * Web report: hub.esirom.com/reports/{id}/
 * Renders a monthly report from its versioned JSON snapshot. "Download PDF" prints this same page.
 */
if (!defined('ABSPATH')) exit;
show_admin_bar(false);
status_header(200);
$report_id = preg_replace('/[^a-fA-F0-9]/', '', (string) get_query_var('esirom_report_id'));
$api_url = get_option('esirom_api_url', 'https://esirom-hub-backend-production.up.railway.app/api');
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Monthly Report — Agency Hub</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
const API_URL = '<?php echo esc_js($api_url); ?>';
const REPORT_ID = '<?php echo esc_js($report_id); ?>';
const LOGIN_URL = '<?php echo esc_js(function_exists('esirom_hub_page_url') ? esirom_hub_page_url('login') : home_url('/login/')); ?>';
const DASHBOARD_URL = '<?php echo esc_js(function_exists('esirom_hub_page_url') ? esirom_hub_page_url('dashboard') : home_url('/dashboard/')); ?>';
</script>
<style>
:root{--ink:#0f172a;--mut:#64748b;--line:#e2e8f0;--brand:#7c3aed;--acc:#10b981;--bg:#f8fafc}
*{box-sizing:border-box}body{margin:0;font-family:Inter,system-ui,Segoe UI,sans-serif;background:var(--bg);color:var(--ink)}
[x-cloak]{display:none!important}
.wrap{max-width:1100px;margin:0 auto;padding:28px 24px 48px}
.toolbar{display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:space-between;margin-bottom:18px}
.btn{border:1px solid var(--line);background:#fff;color:var(--ink);border-radius:10px;padding:8px 14px;font:600 13px Inter,sans-serif;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px}
.btn:hover{border-color:#cbd5e1}.btn-p{background:var(--brand);border-color:var(--brand);color:#fff}.btn-g{background:#059669;border-color:#059669;color:#fff}.btn-w{color:#b45309}
.btn[disabled]{opacity:.5;cursor:default}
.hero{background:linear-gradient(135deg,#1e1b4b,#7c3aed 60%,#10b981);color:#fff;border-radius:24px;padding:34px 38px;display:flex;justify-content:space-between;align-items:flex-end;gap:16px;flex-wrap:wrap}
.hero h1{margin:6px 0 4px;font-size:34px;letter-spacing:-.02em}.tag{font-size:12px;text-transform:uppercase;letter-spacing:.12em;opacity:.8}
.hero img{height:44px;border-radius:10px;background:#fff;padding:4px;margin-bottom:10px}
.pill{display:inline-block;background:rgba(255,255,255,.18);padding:6px 12px;border-radius:999px;font-size:12px;margin:3px 0 3px 6px;text-transform:capitalize}
h2{font-size:14px;text-transform:uppercase;letter-spacing:.1em;color:var(--mut);margin:34px 0 14px}
.grid{display:grid;gap:16px}.k4{grid-template-columns:repeat(4,1fr)}.k2{grid-template-columns:1.3fr 1fr}.k3{grid-template-columns:repeat(3,1fr)}
@media(max-width:820px){.k4{grid-template-columns:repeat(2,1fr)}.k2,.k3{grid-template-columns:1fr}.hero h1{font-size:26px}}
.card{background:#fff;border:1px solid var(--line);border-radius:18px;padding:20px 22px}
.kpi .v{font-size:30px;font-weight:700;letter-spacing:-.02em}.kpi .l{color:var(--mut);font-size:13px}
.up{color:#059669;font-weight:600;font-size:13px}.dn{color:#dc2626;font-weight:600;font-size:13px}.na{color:var(--mut);font-size:12px}
.summary p{font-size:17px;line-height:1.65;margin:0;white-space:pre-line}.ai{font-size:11px;color:var(--brand);font-weight:700;letter-spacing:.08em;text-transform:uppercase;margin-bottom:8px}
.bar{height:12px;border-radius:999px;background:var(--line);overflow:hidden;display:flex}.bar i{display:block;height:100%}
.legend{display:flex;gap:14px;flex-wrap:wrap;font-size:13px;margin-top:10px;color:var(--mut)}.legend b{color:var(--ink)}
.dot{display:inline-block;width:10px;height:10px;border-radius:3px;margin-right:6px;vertical-align:middle}
.post{padding:0;overflow:hidden;display:block;color:inherit;text-decoration:none}.thumb{height:170px;background:linear-gradient(135deg,#fde68a,#f97316) center/cover no-repeat;display:flex;align-items:flex-end;padding:10px}
.thumb span{background:rgba(15,23,42,.78);color:#fff;font-size:11px;padding:4px 8px;border-radius:999px}
.post .b{padding:14px 16px;font-size:13px}.post .b b{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.post .b .m{color:var(--mut);margin-top:6px}
ul.rec{margin:0;padding-left:18px;line-height:1.7}
table.pb{width:100%;border-collapse:collapse;font-size:14px}table.pb td,table.pb th{padding:8px 6px;border-bottom:1px solid var(--line);text-align:right}table.pb th:first-child,table.pb td:first-child{text-align:left}table.pb th{color:var(--mut);font-weight:600;font-size:12px}
.foot{color:var(--mut);font-size:12px;margin-top:34px;display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px}
.banner{border-radius:12px;padding:10px 14px;font-size:13px;margin-bottom:14px}
.b-draft{background:#fef3c7;border:1px solid #fcd34d;color:#92400e}.b-hidden{background:#fee2e2;border:1px solid #fca5a5;color:#991b1b}.b-ok{background:#dcfce7;border:1px solid #86efac;color:#166534}
.editor textarea{width:100%;border:1px solid var(--line);border-radius:10px;padding:10px;font:14px/1.5 Inter,sans-serif;min-height:90px}
.editor label{font-size:12px;font-weight:700;color:var(--mut);text-transform:uppercase;letter-spacing:.06em;display:block;margin:12px 0 6px}
.center{text-align:center;padding:80px 0;color:var(--mut)}
@media print{
  @page{size:A4;margin:12mm}
  body{background:#fff;-webkit-print-color-adjust:exact;print-color-adjust:exact}
  .no-print{display:none!important}.wrap{padding:0;max-width:none}
  .card,.hero{break-inside:avoid}.k4{grid-template-columns:repeat(4,1fr)}.k3{grid-template-columns:repeat(3,1fr)}.k2{grid-template-columns:1.3fr 1fr}
}
</style>
</head>
<body>
<div class="wrap" x-data="webReport()" x-init="init()">
  <div x-show="loading" class="center">Loading report…</div>
  <div x-show="error" x-cloak class="center" x-text="error"></div>

  <template x-if="r && s">
  <div>
    <div class="toolbar no-print">
      <a class="btn" :href="DASHBOARD_URL">← Back to Hub</a>
      <div style="display:flex;gap:8px;flex-wrap:wrap">
        <button class="btn btn-p" @click="window.print()">Download PDF</button>
        <a class="btn" x-show="r.files.csvUrl" :href="r.files.csvUrl" target="_blank" rel="noopener">CSV</a>
        <a class="btn" x-show="r.files.pptUrl" :href="r.files.pptUrl" target="_blank" rel="noopener">PowerPoint</a>
        <button class="btn" x-show="isStaff" @click="copyLink()" x-text="copied ? 'Link copied' : 'Copy link'"></button>
      </div>
    </div>

    <div class="no-print">
      <div x-show="r.hidden" class="banner b-hidden">This version has been replaced by a newer one and is hidden from the client.
        <a x-show="r.supersededBy" :href="'../' + r.supersededBy + '/'">Open the current version</a></div>
      <div x-show="isStaff && r.reviewStatus !== 'published'" class="banner b-draft">
        <b x-text="statusLabel()"></b>. Clients can't see this report until an admin publishes it.
      </div>
      <div x-show="isStaff && r.reviewStatus === 'published'" class="banner b-ok">Published<span x-show="r.review?.publishedAt" x-text="' on ' + fmtDate(r.review?.publishedAt)"></span>. The client can see this report.</div>
    </div>

    <!-- Staff review panel -->
    <div x-show="isStaff && !r.hidden" class="card editor no-print" style="margin-bottom:18px">
      <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px">
        <div class="ai" style="margin:0">Review &amp; edit</div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
          <button class="btn" @click="editing = !editing" x-text="editing ? 'Close editor' : 'Edit summary & plan'" :disabled="r.reviewStatus === 'published' && role !== 'admin'"></button>
          <button class="btn" x-show="r.reviewStatus === 'draft'" @click="act('submit')" :disabled="busy">Submit for approval</button>
          <button class="btn btn-w" x-show="role === 'admin' && ['in_review','approved'].includes(r.reviewStatus)" @click="act('return')" :disabled="busy">Send back to draft</button>
          <button class="btn btn-p" x-show="role === 'admin' && ['draft','in_review'].includes(r.reviewStatus)" @click="act('approve')" :disabled="busy">Approve</button>
          <button class="btn btn-g" x-show="role === 'admin' && r.reviewStatus === 'approved'" @click="publish()" :disabled="busy">Publish &amp; email client</button>
        </div>
      </div>
      <div x-show="editing">
        <label>Executive summary</label>
        <textarea x-model="form.executiveSummary" rows="4"></textarea>
        <label>What worked (one per line)</label>
        <textarea x-model="form.whatWorked"></textarea>
        <label>Next month plan (one per line)</label>
        <textarea x-model="form.nextMonthPlan"></textarea>
        <div style="margin-top:10px"><button class="btn btn-p" @click="save()" :disabled="busy">Save changes</button></div>
      </div>
      <p class="na" x-show="msg" x-text="msg" style="margin:10px 0 0"></p>
      <p class="na" x-show="r.review?.history?.length" style="margin:8px 0 0" x-text="historyLine()"></p>
    </div>

    <div class="hero">
      <div>
        <img x-show="s.brand.logo" :src="s.brand.logo" alt="">
        <div class="tag">Esirom · Monthly Social Report</div>
        <h1 x-text="s.brand.name"></h1>
        <div x-text="s.period.label + ' · Jamaica time'"></div>
      </div>
      <div>
        <template x-for="p in s.platforms"><span class="pill" x-text="p"></span></template>
        <span class="pill" x-text="'v' + r.version + (r.reviewStatus === 'published' ? ' · Published' : ' · ' + statusLabel())"></span>
      </div>
    </div>

    <h2>Executive summary</h2>
    <div class="card summary">
      <div class="ai" x-text="r.review?.autoDrafted === false ? '✦ Summary · reviewed by the Esirom team' : '✦ Auto-drafted summary'"></div>
      <p x-text="r.review?.executiveSummary || 'Summary not written yet.'"></p>
    </div>

    <h2>Headline numbers</h2>
    <div class="grid k4">
      <div class="card kpi"><div class="l">Content published</div><div class="v" x-text="num(s.kpis.posts)"></div>
        <div :class="s.target && s.kpis.posts >= s.target ? 'up' : 'na'" x-text="s.target ? ('Target ' + s.target + ' · ' + Math.round(s.kpis.posts / s.target * 100) + '%') : change(s.kpis.changes.posts)"></div></div>
      <div class="card kpi"><div class="l">Reach</div><div class="v" x-text="num(s.kpis.reach)"></div><div :class="cls(s.kpis.changes.reach)" x-text="change(s.kpis.changes.reach)"></div></div>
      <div class="card kpi"><div class="l">Engagement</div><div class="v" x-text="num(s.kpis.engagement)"></div><div :class="cls(s.kpis.changes.engagement)" x-text="change(s.kpis.changes.engagement)"></div></div>
      <div class="card kpi"><div class="l">Engagement rate</div><div class="v" x-text="(s.kpis.engagementRate || 0).toFixed(1) + '%'"></div><div class="na">engagements ÷ impressions</div></div>
    </div>

    <h2>Content mix</h2>
    <div class="grid k2">
      <div class="card">
        <div class="bar"><i :style="'width:' + share(s.content.carousels) + '%;background:#7c3aed'"></i><i :style="'width:' + share(s.content.singles) + '%;background:#f97316'"></i><i :style="'width:' + share(s.content.reels) + '%;background:#10b981'"></i></div>
        <div class="legend">
          <span><i class="dot" style="background:#7c3aed"></i><b x-text="s.content.carousels || 0"></b> carousels<span x-show="s.content.carouselSlides" x-text="' (' + s.content.carouselSlides + ' slides)'"></span></span>
          <span><i class="dot" style="background:#f97316"></i><b x-text="s.content.singles || 0"></b> single posts</span>
          <span><i class="dot" style="background:#10b981"></i><b x-text="s.content.reels || 0"></b> reels</span>
        </div>
        <p class="na" style="margin:10px 0 0">A carousel counts as one post. The same post shared on Facebook and Instagram counts once.</p>
      </div>
      <div class="card">
        <div class="l na" style="font-size:13px">Platform split (posts per platform)</div>
        <div class="bar" style="margin-top:10px"><i :style="'width:' + pshare('facebook') + '%;background:#1877f2'"></i><i :style="'width:' + pshare('instagram') + '%;background:#e1306c'"></i></div>
        <div class="legend">
          <template x-for="[p, c] in Object.entries(s.content.byPlatform || {})"><span><i class="dot" :style="'background:' + (p === 'facebook' ? '#1877f2' : p === 'instagram' ? '#e1306c' : '#64748b')"></i><b x-text="c"></b> <span x-text="label(p)"></span></span></template>
          <span><b x-text="s.content.crossPosted || 0"></b> shared on both</span>
        </div>
      </div>
    </div>

    <div x-show="Object.keys(s.platformBreakdown || {}).length">
      <h2>By platform</h2>
      <div class="card"><table class="pb"><thead><tr><th>Platform</th><th>Posts</th><th>Reach</th><th>Impressions</th><th>Engagement</th></tr></thead>
        <tbody><template x-for="[p, v] in Object.entries(s.platformBreakdown)"><tr><td x-text="label(p)"></td><td x-text="v.posts"></td><td x-text="num(v.reach)"></td><td x-text="num(v.impressions)"></td><td x-text="num(v.engagement)"></td></tr></template></tbody></table></div>
    </div>

    <div x-show="(s.topContent || []).length">
      <h2>Top posts</h2>
      <div class="grid k3">
        <template x-for="t in s.topContent.slice(0, 6)">
          <a class="card post" :href="t.permalink || null" target="_blank" rel="noopener">
            <div class="thumb" :style="t.imageUrl ? 'background-image:url(' + t.imageUrl + ')' : ''"><span x-text="typeLabel(t)"></span></div>
            <div class="b"><b x-text="t.title"></b>
              <div class="m" x-text="(t.platforms || [t.platform]).map(label).join(' + ') + ' · reach ' + num(t.reach) + ' · ' + num(t.engagement) + ' engagements'"></div></div>
          </a>
        </template>
      </div>
    </div>

    <h2>Insights &amp; next month</h2>
    <div class="grid k2">
      <div class="card"><div class="ai">✦ What worked</div><ul class="rec"><template x-for="w in (r.review?.whatWorked || [])"><li x-text="w"></li></template></ul></div>
      <div class="card"><div class="ai">✦ Next month plan</div><ul class="rec"><template x-for="w in (r.review?.nextMonthPlan || [])"><li x-text="w"></li></template></ul></div>
    </div>

    <div x-show="s.audience && (s.audience.followers?.facebook || s.audience.followers?.instagram)">
      <h2>Audience</h2>
      <div class="grid k3">
        <div class="card kpi"><div class="l">Facebook followers</div><div class="v" x-text="num(s.audience.followers?.facebook)"></div></div>
        <div class="card kpi"><div class="l">Instagram followers</div><div class="v" x-text="num(s.audience.followers?.instagram)"></div></div>
        <div class="card kpi"><div class="l">Net follower change</div><div class="v" x-text="(s.kpis.followerGrowth > 0 ? '+' : '') + num(s.kpis.followerGrowth)"></div></div>
      </div>
    </div>

    <div x-show="s.website">
      <h2>Website</h2>
      <div class="grid k3">
        <div class="card kpi"><div class="l">Sessions</div><div class="v" x-text="num(s.website?.summary?.sessions)"></div></div>
        <div class="card kpi"><div class="l">Users</div><div class="v" x-text="num(s.website?.summary?.users)"></div></div>
        <div class="card kpi"><div class="l">Page views</div><div class="v" x-text="num(s.website?.summary?.pageViews)"></div></div>
      </div>
    </div>

    <div class="foot"><span>Prepared by Esirom · hub.esirom.com</span><span x-text="'Data snapshot ' + fmtDate(s.generatedAt) + ' · v' + r.version"></span></div>
  </div>
  </template>
</div>
<script>
function webReport() {
  return {
    loading: true, error: '', r: null, s: null, role: null, isStaff: false, editing: false, busy: false, msg: '', copied: false,
    form: { executiveSummary: '', whatWorked: '', nextMonthPlan: '' },
    headers() { return { 'Content-Type': 'application/json', Authorization: 'Bearer ' + localStorage.getItem('token') }; },
    async init() {
      if (!localStorage.getItem('token')) { window.location.href = LOGIN_URL + '?redirect=' + encodeURIComponent(location.pathname); return; }
      try {
        const me = await fetch(API_URL + '/auth/me', { headers: this.headers() });
        if (me.status === 401) { window.location.href = LOGIN_URL; return; }
        const mj = await me.json();
        this.role = (mj.data || mj.user || mj)?.role;
        this.isStaff = this.role === 'admin' || this.role === 'brand_rep';
        await this.load();
      } catch (e) { this.error = 'Could not load this report.'; }
      this.loading = false;
    },
    async load() {
      const res = await fetch(API_URL + '/reports/' + REPORT_ID + '/snapshot', { headers: this.headers() });
      const j = await res.json().catch(() => ({}));
      if (!res.ok) { this.error = res.status === 404 ? 'This report is not available.' : (j.message || 'Could not load this report.'); return; }
      this.r = j.data; this.s = j.data.snapshot;
      if (!this.s) { this.error = 'This report has no web version (uploaded PDF).'; this.r = null; return; }
      document.title = (this.s.brand.name + ' — ' + this.s.period.label + ' Report');
      this.form = {
        executiveSummary: this.r.review?.executiveSummary || '',
        whatWorked: (this.r.review?.whatWorked || []).join('\n'),
        nextMonthPlan: (this.r.review?.nextMonthPlan || []).join('\n')
      };
    },
    async save() {
      this.busy = true; this.msg = '';
      const res = await fetch(API_URL + '/reports/' + REPORT_ID + '/review', { method: 'PATCH', headers: this.headers(), body: JSON.stringify(this.form) });
      const j = await res.json().catch(() => ({}));
      this.busy = false;
      if (!res.ok) { this.msg = j.message || 'Could not save.'; return; }
      await this.load(); this.editing = false; this.msg = 'Saved.';
    },
    async act(action, body = {}) {
      this.busy = true; this.msg = '';
      const res = await fetch(API_URL + '/reports/' + REPORT_ID + '/' + action, { method: 'POST', headers: this.headers(), body: JSON.stringify(body) });
      const j = await res.json().catch(() => ({}));
      this.busy = false;
      if (!res.ok) { this.msg = j.message || 'Something went wrong.'; return; }
      await this.load();
      const ce = this.r.review?.clientEmail;
      this.msg = action === 'publish' ? ('Published. ' + (ce?.skipped ? (ce.message || 'No client email sent.') : ('Client emails sent: ' + (ce?.sent || 0) + (ce?.failed ? ', failed: ' + ce.failed : '')))) : 'Done.';
    },
    publish() {
      if (!confirm('Publish this report? The client will see it and the brand\'s client contacts will be emailed.')) return;
      this.act('publish', { sendEmail: true });
    },
    statusLabel() { return ({ draft: 'Draft', in_review: 'In review', approved: 'Approved', published: 'Published' })[this.r?.reviewStatus] || 'Draft'; },
    historyLine() {
      const h = this.r.review.history.slice(-3).map((x) => x.action + ' ' + this.fmtDate(x.at));
      return 'Recent: ' + h.join(' · ');
    },
    num(v) { v = Number(v) || 0; if (v >= 1e6) return (v / 1e6).toFixed(1) + 'M'; if (v >= 1e4) return Math.round(v / 1e3) + 'K'; if (v >= 1e3) return (v / 1e3).toFixed(1) + 'K'; return String(Math.round(v)); },
    change(c) { if (c === undefined || c === null || !isFinite(c)) return 'vs last month: —'; return (c > 0 ? '▲ +' : c < 0 ? '▼ ' : '') + Math.round(c) + '% vs last month'; },
    cls(c) { return c > 0 ? 'up' : c < 0 ? 'dn' : 'na'; },
    share(x) { const t = this.s.content.posts || 0; return t ? ((x || 0) / t * 100).toFixed(1) : 0; },
    pshare(p) { const t = this.s.content.platformPosts || 0; return t ? (((this.s.content.byPlatform || {})[p] || 0) / t * 100).toFixed(1) : 0; },
    label(p) { return ({ facebook: 'Facebook', instagram: 'Instagram', linkedin: 'LinkedIn', youtube: 'YouTube', tiktok: 'TikTok', x: 'X' })[p] || p; },
    typeLabel(t) { return t.contentType === 'carousel' ? ('Carousel' + (t.slides ? ' · ' + t.slides + ' slides' : '')) : t.contentType === 'reel' ? 'Reel' : 'Single post'; },
    fmtDate(d) { return d ? new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', timeZone: 'America/Jamaica' }) : ''; },
    async copyLink() { try { await navigator.clipboard.writeText(location.href); this.copied = true; setTimeout(() => this.copied = false, 2000); } catch (e) {} }
  };
}
</script>
</body>
</html>
