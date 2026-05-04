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
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'esirom-hub'),
    ));
}
add_action('after_setup_theme', 'esirom_hub_setup');

/**
 * Enqueue scripts and styles
 */
function esirom_hub_scripts() {
    // Only load on specific pages
    if (is_page_template('page-login.php') || is_page_template('page-dashboard.php') || is_page_template('page-admin.php')) {
        // Tailwind CSS
        wp_enqueue_style('tailwindcss', 'https://cdn.tailwindcss.com', array(), null);
        
        // Google Fonts
        wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', array(), null);
        ?>
        <script>
            const ESIROM_API_URL = '<?php echo esc_js(get_option('esirom_api_url', 'https://esirom-hub-backend-production.up.railway.app/api')); ?>';
        </script>
        <?php
    }
}
add_action('wp_head', 'esirom_hub_scripts');

/**
 * Handle media upload via AJAX
 */
function esirom_hub_handle_media_upload() {
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Unauthorized'], 401);
    }
    
    // Include the upload handler
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
        update_option('esirom_api_url', sanitize_text_field($_POST['esirom_api_url']));
        echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
    }

    $api_url = get_option('esirom_api_url', 'http://localhost:5000/api');
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
                        <input type="text" 
                               id="esirom_api_url" 
                               name="esirom_api_url" 
                               value="<?php echo esc_attr($api_url); ?>" 
                               class="regular-text"
                               placeholder="http://localhost:5000/api">
                        <p class="description">Enter the URL of your Esirom Hub API backend.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Save Settings'); ?>
        </form>
        
        <hr>
        
        <h2>Setup Instructions</h2>
        <ol>
            <li>Make sure your backend API is running (see backend/README.md)</li>
            <li>Enter the API URL above (e.g., http://localhost:5000/api or https://api.yourdomain.com/api)</li>
            <li>Create a new page and assign the "Login" template</li>
            <li>Create another page and assign the "Dashboard" template</li>
            <li>Set the login page as your hub entry point</li>
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
    if (is_page_template('page-login.php') || is_page_template('page-dashboard.php')) {
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
    if (is_page_template('page-login.php') || is_page_template('page-dashboard.php')) {
        show_admin_bar(false);
    }
}
add_action('template_redirect', 'esirom_hub_remove_admin_bar');

/**
 * Add rewrite rules for workflow sub-URLs
 */
function esirom_hub_workflow_rewrite_rules() {
    add_rewrite_rule('^workflow/concepts/?$', 'index.php?pagename=workflow', 'top');
    add_rewrite_rule('^workflow/production/?$', 'index.php?pagename=workflow', 'top');
    add_rewrite_rule('^workflow/contentbank/?$', 'index.php?pagename=workflow', 'top');
    add_rewrite_rule('^workflow/tasks/?$', 'index.php?pagename=workflow', 'top');
    add_rewrite_rule('^workflow/planner/?$', 'index.php?pagename=workflow', 'top');
    add_rewrite_rule('^workflow/feed/?$', 'index.php?pagename=workflow', 'top');
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
    if (get_transient('esirom_hub_rewrite_flushed_v4') !== 'yes') {
        esirom_hub_workflow_rewrite_rules();
        flush_rewrite_rules();
        set_transient('esirom_hub_rewrite_flushed_v4', 'yes', 0);
    }
}
add_action('init', 'esirom_hub_maybe_flush_rewrite_rules', 20);
