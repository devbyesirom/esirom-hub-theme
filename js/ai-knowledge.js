/* AI Studio brand knowledge editor (Company -> Brands -> Products/SKUs).
 * Shared by the staff AI Studio page and the client "Brand info" page.
 * mode: 'staff' | 'client'. Requires window.ESIROM_API_URL (or API_URL) and a JWT in localStorage. */
window.aiKnowledgeEditor = function (mode) {
  const API = () => (typeof API_URL !== 'undefined' ? API_URL : window.ESIROM_API_URL);
  const FIELDS = {
    company: [['name', 'Company name', 'text'], ['industry', 'Industry', 'text'], ['website', 'Website', 'text'], ['about', 'About the company', 'area'], ['audience', 'Main customers', 'area'], ['notes', 'Notes', 'area']],
    brand: [['name', 'Brand name', 'text'], ['about', 'About the brand', 'area'], ['audience', 'Audience', 'area'], ['voiceGuide', 'Voice guide', 'area'], ['doWords', 'Words to use (comma-separated)', 'list'], ['dontWords', 'Words never to use', 'list'], ['hashtags', 'Hashtags', 'list'], ['emojiUse', 'Emoji use', 'emoji']],
    product: [['name', 'Product name', 'text'], ['sku', 'SKU code', 'text'], ['description', 'Description', 'area'], ['claims', 'Key claims (one per line)', 'lines'], ['variants', 'Sizes / variants (comma-separated)', 'list']]
  };
  const LABEL = { company: 'Company', brand: 'Brand', product: 'Product' };
  return {
    kMode: mode, kCompanies: [], kCompanyId: '', kTree: { companyItem: null, brands: [], companyName: '' }, kLoading: false,
    kEdit: null, kForm: {}, kSaving: false, kAudit: [], kShowAudit: false, kVersions: null, kOpen: {},
    kFilter: 'all',
    kFields(type) { return FIELDS[type] || []; },
    kLabel(type) { return LABEL[type]; },
    async kApi(path, opts = {}) {
      const headers = { Authorization: `Bearer ${localStorage.getItem('token')}` };
      if (!(opts.body instanceof FormData)) headers['Content-Type'] = 'application/json';
      const res = await fetch(`${API()}/ai-knowledge${path}`, { ...opts, headers });
      const data = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(data.message || `Request failed (${res.status})`);
      return data;
    },
    kToast(msg, type) { if (typeof this.showToast === 'function') this.showToast(msg, type); else alert(msg); },
    async kInit(preselect) {
      try {
        this.kCompanies = (await this.kApi('/companies')).companies;
        const id = preselect || (this.kCompanies.length === 1 ? this.kCompanies[0]._id : '');
        if (id) await this.kLoad(id);
      } catch (e) { this.kToast(e.message, 'error'); }
    },
    async kLoad(id) {
      this.kCompanyId = id; this.kLoading = true;
      try { const d = await this.kApi(`/companies/${id}`); this.kTree = { companyItem: d.company, brands: d.brands, companyName: d.client.name }; if (typeof this.onKnowledgeLoaded === 'function') this.onKnowledgeLoaded(this.kTree); }
      catch (e) { this.kToast(e.message, 'error'); } finally { this.kLoading = false; }
      if (this.kShowAudit) this.kLoadAudit();
    },
    kData(item) { return (item && (item.draft || item.approved)) || {}; },
    kBadge(item) {
      if (!item) return { text: '', cls: '' };
      if (item.badge === 'client_approved') return { text: 'Client approved', cls: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200' };
      const pending = item.status === 'pending_client';
      if (item.badge === 'changes_awaiting_client_approval') return { text: pending ? 'Changes awaiting client approval' : 'Unsent changes (draft)', cls: 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-200' };
      return { text: pending ? 'Awaiting client approval' : 'Draft, not yet sent to client', cls: pending ? 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' };
    },
    kChanges(item) {
      if (!item || !item.approved || !item.hasPendingChanges) return [];
      const out = [];
      for (const [k, label] of (FIELDS[item.type] || [])) {
        const a = JSON.stringify(item.approved[k] ?? ''); const b = JSON.stringify(item.draft[k] ?? '');
        if (a !== b) out.push({ label, from: this.kFmt(item.approved[k]), to: this.kFmt(item.draft[k]) });
      }
      if (JSON.stringify(item.approved.images || []) !== JSON.stringify(item.draft.images || [])) out.push({ label: 'Images', from: `${(item.approved.images || []).length} image(s)`, to: `${(item.draft.images || []).length} image(s)` });
      return out;
    },
    kFmt(v) { return Array.isArray(v) ? v.join(', ') : (v || '—'); },
    kPendingCount() {
      let n = 0; const all = [this.kTree.companyItem, ...this.kTree.brands, ...this.kTree.brands.flatMap((b) => b.products)].filter(Boolean);
      all.forEach((i) => { if (i.status === 'pending_client') n++; }); return n;
    },
    kVisible(item) {
      if (this.kFilter === 'all') return true;
      if (this.kFilter === 'pending') return item.status === 'pending_client';
      if (this.kFilter === 'suggested') return item.source === 'suggested_from_posts' && !item.approved;
      return true;
    },
    kStartEdit(item, type, brandId) {
      const t = item ? item.type : type; const d = item ? this.kData(item) : {};
      const f = {};
      for (const [k, , kind] of FIELDS[t]) f[k] = kind === 'list' ? (d[k] || []).join(', ') : kind === 'lines' ? (d[k] || []).join('\n') : (d[k] || (kind === 'emoji' ? 'light' : ''));
      if (t === 'brand') f.sceneTemplates = JSON.parse(JSON.stringify(d.sceneTemplates || []));
      this.kForm = f; this.kEdit = { item, type: t, brandId: brandId || (item && item.brandId) || null };
    },
    kPayload() {
      const t = this.kEdit.type; const d = {};
      for (const [k, , kind] of FIELDS[t]) d[k] = kind === 'lines' ? String(this.kForm[k] || '').split('\n') : this.kForm[k];
      if (t === 'brand') d.sceneTemplates = this.kForm.sceneTemplates;
      return d;
    },
    async kSave(submit) {
      this.kSaving = true;
      try {
        const data = this.kPayload();
        if (this.kEdit.item) await this.kApi(`/items/${this.kEdit.item._id}`, { method: 'PUT', body: JSON.stringify({ data, submit }) });
        else await this.kApi(`/companies/${this.kCompanyId}/items`, { method: 'POST', body: JSON.stringify({ type: this.kEdit.type, brandId: this.kEdit.brandId, data, submit }) });
        this.kToast(this.kMode === 'client' ? 'Saved and approved.' : submit ? 'Saved and sent for client approval.' : 'Saved as a draft.');
        this.kEdit = null; await this.kLoad(this.kCompanyId);
      } catch (e) { this.kToast(e.message, 'error'); } finally { this.kSaving = false; }
    },
    async kAction(item, action) {
      try {
        if (action === 'archive' && !confirm(`Remove ${this.kData(item).name || 'this item'}${item.type === 'brand' ? ' and its products' : ''}?`)) return;
        let body = {};
        if (action === 'request-changes') { const note = prompt('What should change?'); if (note === null) return; body = { note }; }
        if (action === 'archive') await this.kApi(`/items/${item._id}`, { method: 'DELETE' });
        else await this.kApi(`/items/${item._id}/${action}`, { method: 'POST', body: JSON.stringify(body) });
        this.kToast({ submit: 'Sent for client approval.', approve: 'Approved.', 'request-changes': 'Changes requested.', archive: 'Removed.' }[action]);
        await this.kLoad(this.kCompanyId);
      } catch (e) { this.kToast(e.message, 'error'); }
    },
    async kUpload(item, ev) {
      const f = ev.target.files[0]; if (!f) return;
      const fd = new FormData(); fd.append('file', f);
      try { const d = await this.kApi(`/items/${item._id}/images`, { method: 'POST', body: fd }); this.kToast(d.warning || 'Image added.', d.warning ? 'error' : 'success'); await this.kLoad(this.kCompanyId); }
      catch (e) { this.kToast(e.message, 'error'); } finally { ev.target.value = ''; }
    },
    async kRemoveImage(item, idx) {
      if (!confirm('Remove this image?')) return;
      try { await this.kApi(`/items/${item._id}/images/${idx}`, { method: 'DELETE' }); await this.kLoad(this.kCompanyId); } catch (e) { this.kToast(e.message, 'error'); }
    },
    async kLoadAudit() { try { this.kAudit = (await this.kApi(`/companies/${this.kCompanyId}/audit`)).audit; } catch (e) { this.kToast(e.message, 'error'); } },
    async kLoadVersions(item) { try { this.kVersions = { item, list: (await this.kApi(`/items/${item._id}/versions`)).versions }; } catch (e) { this.kToast(e.message, 'error'); } },
    kWhen(d) { try { return new Date(d).toLocaleString('en-GB', { timeZone: 'America/Jamaica', day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' }); } catch (e) { return d; } },
    kActionLabel(a) { return { create: 'created', save: 'saved a draft', submit: 'sent for client approval', approve: 'approved', request_changes: 'requested changes', client_edit: 'edited (client, approved)', archive: 'removed', seed: 'suggested from posts', image_add: 'added an image', image_remove: 'removed an image' }[a] || a; }
  };
};
