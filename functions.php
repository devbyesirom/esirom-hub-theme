<?php
/**
 * Esirom Client Hub Theme Functions
 *
 * @package Esirom_Client_Hub
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function esirom_hub_setup() {
    // Hub templates render their own <title>, so WordPress' title-tag is not used.
    add_theme_support('post-thumbnails');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'esirom-hub'),
    ));
}
add_action('after_setup_theme', 'esirom_hub_setup');

/**
 * Resolve a Hub page URL by slug, with fallback when the WP page is not created yet.
 */
function esirom_hub_page_url($slug) {
    $page = get_page_by_path($slug);
    if ($page && !is_wp_error($page)) {
        $url = get_permalink($page);
        if ($url) {
            return $url;
        }
    }
    return home_url('/' . trim($slug, '/'));
}

/**
 * Alpine x-show for client-facing navigation (real client users always see this).
 */
function esirom_hub_client_nav_show_attr() {
    return 'x-show="viewMode === \'client\' || user?.role === \'client\'"';
}

/**
 * Alpine x-show for agency staff navigation (hidden for real client users).
 */
function esirom_hub_staff_nav_show_attr() {
    return 'x-show="viewMode !== \'client\' && user?.role !== \'client\'"';
}

/**
 * Bootstrap Alpine clientNav store (website project visibility for client menu).
 */
function esirom_hub_client_nav_bootstrap() {
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    include get_template_directory() . '/inc/hub-client-nav-bootstrap.php';
}

/**
 * Render shared staff sidebar navigation (admin / brand_rep).
 */
function esirom_hub_staff_sidebar_nav($active = '', $context = 'site', $alpine_labels = false) {
    $hub_nav_active = $active;
    $hub_nav_context = $context;
    $hub_nav_alpine_labels = $alpine_labels;
    include get_template_directory() . '/inc/hub-staff-sidebar-nav.php';
}

/**
 * Render shared staff sidebar header (logo + collapse toggle).
 */
function esirom_hub_staff_sidebar_header($alpine_labels = false) {
    $hub_nav_alpine_labels = $alpine_labels;
    include get_template_directory() . '/inc/hub-staff-sidebar-header.php';
}

/**
 * Render shared staff sidebar footer (Update KPIs, Admin Panel, Logout).
 */
function esirom_hub_staff_sidebar_footer($context = 'site', $alpine_labels = false, $show_logout = true) {
    $hub_nav_context = $context;
    $hub_nav_alpine_labels = $alpine_labels;
    $hub_footer_show_logout = $show_logout;
    include get_template_directory() . '/inc/hub-staff-sidebar-footer.php';
}

/**
 * Reset WP admin bar offset after wp_head() on Hub page templates.
 */
function esirom_hub_wp_head_reset() {
    echo '<style id="hub-wp-head-reset">#wpadminbar{display:none!important}html{margin-top:0!important;padding-top:0!important}html.admin-bar{margin-top:0!important}body.admin-bar{margin-top:0!important;padding-top:0!important}</style>';
}

/**
 * Render the shared header account menu (change password / log out).
 */
function esirom_hub_account_menu($show_theme_toggle = false) {
    $hub_account_theme = $show_theme_toggle;
    include get_template_directory() . '/inc/hub-account-menu.php';
}

/**
 * Render the shared toast stack (expects `toasts` on the Alpine component).
 */
function esirom_hub_toasts() {
    include get_template_directory() . '/inc/hub-toasts.php';
}

/**
 * Render unified mobile bottom navigation (clients + staff, PWA).
 */
function esirom_hub_mobile_nav($active = '', $context = 'site') {
    $hub_mobile_active = $active;
    $hub_mobile_context = $context;
    include get_template_directory() . '/inc/hub-mobile-nav.php';
}

/**
 * Render shared staff mobile bottom navigation.
 */
function esirom_hub_staff_mobile_nav($active = '') {
    esirom_hub_mobile_nav($active, 'site');
}

/**
 * Render shared client sidebar navigation (viewMode === 'client').
 */
function esirom_hub_client_sidebar_nav($active = '', $context = 'site', $alpine_labels = false) {
    $hub_client_nav_active = $active;
    $hub_client_nav_context = $context;
    $hub_nav_alpine_labels = $alpine_labels;
    include get_template_directory() . '/inc/hub-client-sidebar-nav.php';
    esirom_hub_client_nav_bootstrap();
}

/**
 * Render shared client mobile bottom navigation (viewMode === 'client').
 */
function esirom_hub_client_mobile_nav($context = 'site') {
    esirom_hub_mobile_nav('', $context);
}

/**
 * Inline CSS: hide mobile bottom nav on desktop (prevents grey bar on wide screens).
 */
function esirom_hub_layout_styles() {
    include get_template_directory() . '/inc/hub-layout-styles.php';
}

/**
 * Default backend API URL (used wherever the option is not set).
 */
function esirom_hub_default_api_url() {
    return 'https://esirom-hub-backend-production.up.railway.app/api';
}

/**
 * Resolved backend API URL.
 */
function esirom_hub_api_url() {
    $url = get_option('esirom_api_url', esirom_hub_default_api_url());
    return $url ? rtrim($url, '/') : esirom_hub_default_api_url();
}

/**
 * Default super admin accounts that may use the "Viewing as" role switcher.
 */
function esirom_hub_default_super_admin_emails() {
    return array('alex@esirom.com');
}

/**
 * Normalise a list of emails (string with newlines/commas or array) to a
 * de-duplicated, lower-cased array.
 */
function esirom_hub_normalise_email_list($raw) {
    $items = is_array($raw) ? $raw : preg_split('/[\s,;]+/', (string) $raw);
    $emails = array();
    foreach ($items as $item) {
        $email = strtolower(trim((string) $item));
        if ($email !== '' && is_email($email)) {
            $emails[$email] = true;
        }
    }
    return array_keys($emails);
}

/**
 * Super admin emails allowed to use the "Viewing as" switcher.
 *
 * Super admin status is resolved client-side from the user object returned by
 * the backend (`/auth/me`): the account must have the `admin` role AND either
 * carry an `isSuperAdmin: true` flag from the backend or match this list.
 */
function esirom_hub_super_admin_emails() {
    $stored = get_option('esirom_super_admin_emails', null);
    if ($stored === null || $stored === false) {
        return esirom_hub_default_super_admin_emails();
    }
    return esirom_hub_normalise_email_list($stored);
}

/**
 * All page templates that make up the Hub application.
 */
function esirom_hub_templates() {
    return array(
        'page-login.php',
        'page-register.php',
        'page-reset-password.php',
        'page-dashboard.php',
        'page-admin.php',
        'page-password-vault.php',
        'page-workflow.php',
        'page-overview.php',
        'page-content-calendar.php',
        'page-ads.php',
        'page-messages.php',
        'page-inventory.php',
        'page-website-projects.php',
        'page-progress.php',
        'page-privacy.php',
    );
}

/**
 * Whether the current request renders one of the Hub page templates.
 */
function esirom_hub_is_hub_template() {
    foreach (esirom_hub_templates() as $template) {
        if (is_page_template($template)) {
            return true;
        }
    }
    return false;
}

/**
 * Shared <head> assets (fonts, Tailwind + brand config, design system CSS,
 * dark-mode bootstrap, API URL global). Every Hub template calls this.
 *
 * @param array $args { pwa: bool, tailwind_plugins: string, description: string }
 */
function esirom_hub_head_assets($args = array()) {
    $hub_head = is_array($args) ? $args : array();
    include get_template_directory() . '/inc/hub-head.php';
}

/**
 * Hub templates render their own <head>; nothing extra is enqueued for them.
 * (The design system stylesheet is linked directly by esirom_hub_head_assets().)
 */
function esirom_hub_scripts() {
    if (esirom_hub_is_hub_template()) {
        return;
    }
    wp_enqueue_style(
        'esirom-hub-theme',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'esirom_hub_scripts');

/**
 * Handle media upload via AJAX
 */
function esirom_hub_handle_media_upload() {
    $authorised = false;

    // A WordPress administrator session is enough.
    if (is_user_logged_in() && current_user_can('manage_options')) {
        $authorised = true;
    }

    // Otherwise verify the Hub JWT against the backend and require the admin role.
    if (!$authorised && !empty($_POST['auth_token'])) {
        $token = sanitize_text_field(wp_unslash($_POST['auth_token']));
        $response = wp_remote_get(esirom_hub_api_url() . '/auth/me', array(
            'timeout' => 10,
            'headers' => array('Authorization' => 'Bearer ' . $token),
        ));
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $role = isset($body['user']['role']) ? $body['user']['role'] : '';
            if ($role === 'admin') {
                $authorised = true;
            }
        }
    }

    if (!$authorised) {
        wp_send_json_error(array('message' => 'Unauthorised – please sign in to the Hub as an administrator.'), 401);
    }

    define('ESIROM_HUB_UPLOAD_AUTHORISED', true);
    require_once get_template_directory() . '/upload-media.php';
    wp_die();
}
add_action('wp_ajax_upload_media', 'esirom_hub_handle_media_upload');
add_action('wp_ajax_nopriv_upload_media', 'esirom_hub_handle_media_upload');

/**
 * Register theme settings page
 */
function esirom_hub_settings_page() {
    add_theme_page(
        'Esirom Hub Settings',
        'Hub Settings',
        'manage_options',
        'esirom-hub-settings',
        'esirom_hub_settings_page_html'
    );
}
add_action('admin_menu', 'esirom_hub_settings_page');

/**
 * Settings page HTML
 */
function esirom_hub_settings_page_html() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Save settings
    if (isset($_POST['esirom_hub_settings_nonce']) && wp_verify_nonce($_POST['esirom_hub_settings_nonce'], 'esirom_hub_settings')) {
        $new_url = isset($_POST['esirom_api_url']) ? esc_url_raw(trim(wp_unslash($_POST['esirom_api_url']))) : '';
        update_option('esirom_api_url', $new_url ? rtrim($new_url, '/') : esirom_hub_default_api_url());
        $new_super_admins = isset($_POST['esirom_super_admin_emails']) ? sanitize_textarea_field(wp_unslash($_POST['esirom_super_admin_emails'])) : '';
        update_option('esirom_super_admin_emails', implode("\n", esirom_hub_normalise_email_list($new_super_admins)));
        echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
    }

    $api_url = esirom_hub_api_url();
    $super_admin_emails = esirom_hub_super_admin_emails();
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('esirom_hub_settings', 'esirom_hub_settings_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="esirom_api_url">API URL</label>
                    </th>
                    <td>
                        <input type="url"
                               id="esirom_api_url"
                               name="esirom_api_url"
                               value="<?php echo esc_attr($api_url); ?>"
                               class="regular-text"
                               placeholder="<?php echo esc_attr(esirom_hub_default_api_url()); ?>">
                        <p class="description">Base URL of the Esirom Hub API backend (including <code>/api</code>).</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="esirom_super_admin_emails">Super admin emails</label>
                    </th>
                    <td>
                        <textarea id="esirom_super_admin_emails"
                                  name="esirom_super_admin_emails"
                                  rows="3"
                                  class="large-text code"
                                  placeholder="alex@esirom.com"><?php echo esc_textarea(implode("\n", $super_admin_emails)); ?></textarea>
                        <p class="description">One email per line. Only these accounts (they must also have the <code>admin</code> role in the Hub backend) can use the "Viewing as" switcher to preview the Hub as a brand rep or client. Accounts flagged <code>isSuperAdmin</code> by the backend are allowed too.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Save Settings'); ?>
        </form>
        
        <hr>
        
        <h2>Setup Instructions</h2>
        <ol>
            <li>Make sure your backend API is running and reachable from the browser.</li>
            <li>Enter the API URL above (for example <code><?php echo esc_html(esirom_hub_default_api_url()); ?></code>).</li>
            <li>Create pages with the slugs <code>login</code>, <code>register</code>, <code>reset-password</code>, <code>dashboard</code>, <code>workflow</code>, <code>overview</code>, <code>content-calendar</code>, <code>messages</code>, <code>ads</code>, <code>inventory</code>, <code>progress</code>, <code>password-vault</code>, <code>website-projects</code> and <code>admin</code>, each assigned to the matching Hub template.</li>
            <li>Set the login page as the static front page so the Hub opens at the site root.</li>
        </ol>
        
        <h2>Quick Links</h2>
        <ul>
            <li><a href="<?php echo esc_url(admin_url('themes.php?page=esirom-hub-settings')); ?>">Hub Settings</a></li>
            <li><a href="<?php echo esc_url(get_theme_file_uri('README.md')); ?>" target="_blank">Documentation</a></li>
            <li><a href="<?php echo esc_url(get_theme_file_uri('SETUP_GUIDE.md')); ?>" target="_blank">Setup Guide</a></li>
        </ul>
    </div>
    <?php
}

/**
 * Redirect non-authenticated users
 * This is a basic check - the real authentication happens via the API
 */
function esirom_hub_auth_check() {
    if (is_page_template('page-dashboard.php')) {
        // JavaScript will handle the actual auth check
        // This is just to ensure the page template loads
    }
}
add_action('template_redirect', 'esirom_hub_auth_check');

/**
 * Add custom body classes
 */
function esirom_hub_body_classes($classes) {
    if (esirom_hub_is_hub_template()) {
        $classes[] = 'esirom-hub-page';
        $classes[] = 'h-full';
    }
    return $classes;
}
add_filter('body_class', 'esirom_hub_body_classes');

/**
 * Remove admin bar for hub pages
 */
function esirom_hub_remove_admin_bar() {
    if (esirom_hub_is_hub_template()) {
        show_admin_bar(false);
    }
}
add_action('template_redirect', 'esirom_hub_remove_admin_bar');

/**
 * Add rewrite rules for workflow sub-URLs
 */
function esirom_hub_workflow_rewrite_rules() {
    $sections = array('dashboard', 'concepts', 'production', 'contentbank', 'tasks', 'planner', 'feed', 'assets');
    foreach ($sections as $section) {
        add_rewrite_rule('^workflow/' . $section . '/?$', 'index.php?pagename=workflow', 'top');
    }
}
add_action('init', 'esirom_hub_workflow_rewrite_rules');

/**
 * Flush rewrite rules on theme activation
 */
function esirom_hub_flush_rewrite_rules() {
    esirom_hub_workflow_rewrite_rules();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'esirom_hub_flush_rewrite_rules');

/**
 * Auto-flush rewrite rules if not yet registered (runs once)
 */
function esirom_hub_maybe_flush_rewrite_rules() {
    if (get_transient('esirom_hub_rewrite_flushed_v7') !== 'yes') {
        esirom_hub_workflow_rewrite_rules();
        flush_rewrite_rules();
        set_transient('esirom_hub_rewrite_flushed_v7', 'yes', 0);
    }
}
add_action('init', 'esirom_hub_maybe_flush_rewrite_rules', 20);
