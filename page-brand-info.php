<?php
/**
 * Template Name: Brand Info
 * Description: Company, brands and products (SKUs) for AI Studio — clients review and approve; staff draft
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
    <title>Brand info — Agency Hub</title>
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
<body class="hub-has-mobile-nav h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 pb-16 md:pb-0" x-data="Object.assign(aiKnowledgeEditor('client'), brandInfoApp())" x-init="init()">

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
            <?php esirom_hub_client_sidebar_nav('brand_info', 'site', false); ?>
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
                    <h1 class="hub-page-title">Brand info</h1>
                    <p class="hub-page-subtitle">Your company, brands and products. Esirom only uses details you've approved when creating content.</p>
                </div>
                <div class="flex items-center gap-2"><?php esirom_hub_account_menu(); ?></div>
            </div>
        </header>
        <div class="hub-page-content p-4 sm:p-6 space-y-4">
            <div x-show="toast.show" x-cloak x-transition class="fixed bottom-20 md:bottom-6 right-4 z-[100] max-w-sm px-4 py-3 rounded-xl shadow-lg text-sm text-white" :class="toast.type === 'error' ? 'bg-red-600' : 'bg-emerald-600'" x-text="toast.message"></div>
            <div x-show="statusLoaded && !enabled" x-cloak class="hub-card p-8 text-center max-w-lg mx-auto">
                <p class="hub-empty__title">Brand info isn't available yet</p>
                <p class="hub-empty__text mt-1">We'll let you know when it's ready.</p>
            </div>
            <div x-show="enabled" x-cloak><?php esirom_hub_ai_knowledge_editor(); ?></div>
        </div>
    </main>
</div>

<script>
function brandInfoApp() {
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
        viewMode: 'client', isSidebarOpen: true, statusLoaded: false, enabled: false,
        toast: { show: false, message: '', type: 'success' },
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
                this.viewMode = window.HubAuth.applyViewMode(this.user.role === 'client' ? 'client' : undefined);
                this.kMode = (this.user.role === 'client' || this.viewMode === 'client') ? 'client' : 'staff';
                const st = await this.kApi('/status');
                this.enabled = st.enabled; this.kMode = st.role === 'client' ? 'client' : 'staff';
                if (this.enabled) {
                    const pre = new URLSearchParams(location.search).get('company') || (this.kMode === 'staff' ? (localStorage.getItem('hubSelectedClientId') || '') : '');
                    await this.kInit(pre);
                }
            } catch (e) {
                if (e && e.message === 'auth') { localStorage.removeItem('token'); localStorage.removeItem('user'); this.user = null; this.loadError = 'Your session has expired. Please sign in again.'; }
                else if (e && e.message === 'server') { this.user = null; this.loadError = 'We could not verify your session. Please try again in a moment.'; }
                else this.showToast(e.message || 'Some data could not be loaded.', 'error');
            } finally { this.statusLoaded = true; this.authChecked = true; }
        },
        logout() { localStorage.removeItem('token'); localStorage.removeItem('user'); window.location.href = LOGIN_URL; }
    };
}
</script>

<?php include get_template_directory() . '/inc/change-password-modal.php'; ?>
<?php esirom_hub_mobile_nav('brand_info', 'site'); ?>
</body>
</html>
