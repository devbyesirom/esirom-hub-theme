<?php
/**
 * Alpine store for shared client navigation state (website project visibility).
 */
if (!defined('ABSPATH')) {
    exit;
}

$hub_api_url = esirom_hub_api_url();
?>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('clientNav', {
        hasWebsiteProjects: false,
        websiteProjectCount: 0,
        loaded: false,
        async refresh() {
            const token = localStorage.getItem('token');
            if (!token) {
                this.loaded = true;
                return;
            }
            let user = {};
            try {
                user = JSON.parse(localStorage.getItem('user') || '{}') || {};
            } catch (_err) {
                user = {};
            }
            // Stored preview modes only apply to super admins; everyone else gets their real role.
            const viewMode = window.HubAuth.resolveViewMode(window.HubAuth.readStoredViewMode(), window.HubAuth.getUser() || user);
            if (user.role !== 'client' && viewMode !== 'client') {
                this.loaded = true;
                return;
            }
            try {
                const res = await fetch(<?php echo wp_json_encode(esc_url_raw(rtrim($hub_api_url, '/') . '/website-projects/nav-meta')); ?>, {
                    headers: { Authorization: `Bearer ${token}` }
                });
                const data = await res.json();
                if (data.success) {
                    this.hasWebsiteProjects = !!data.hasProjects;
                    this.websiteProjectCount = Number(data.count) || 0;
                }
            } catch (_err) {
                /* leave defaults */
            } finally {
                this.loaded = true;
            }
        }
    });
    Alpine.store('clientNav').refresh();
});
</script>
