<?php
/**
 * Shared <head> assets for every Agency Hub page template.
 *
 * Outputs: viewport/theme meta, favicons (and PWA manifest when requested),
 * Inter font, Tailwind Play CDN with the shared brand config, the Hub design
 * system stylesheet, the dark-mode bootstrap (runs before first paint), the
 * `ESIROM_API_URL` / `ESIROM_SUPER_ADMIN_EMAILS` globals and the `HubAuth`
 * helper (js/hub-auth.js) used by every page script.
 *
 * Expects (all optional):
 *   $hub_head = [
 *     'pwa'              => bool    include manifest + iOS web-app meta (default false)
 *     'tailwind_plugins' => string  e.g. 'forms,typography' (default '')
 *     'description'      => string  meta description
 *   ]
 */

if (!defined('ABSPATH')) {
    exit;
}

$hub_head = isset($hub_head) && is_array($hub_head) ? $hub_head : array();
$hub_head_pwa = !empty($hub_head['pwa']);
$hub_head_plugins = isset($hub_head['tailwind_plugins']) ? trim((string) $hub_head['tailwind_plugins']) : '';
$hub_head_description = isset($hub_head['description']) ? (string) $hub_head['description'] : '';

$hub_theme_uri = get_template_directory_uri();
$hub_theme_ver = wp_get_theme()->get('Version');
$hub_api_url = get_option('esirom_api_url', 'https://esirom-hub-backend-production.up.railway.app/api');
$hub_tailwind_src = 'https://cdn.tailwindcss.com' . ($hub_head_plugins !== '' ? '?plugins=' . rawurlencode($hub_head_plugins) : '');
?>
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#4F46E5">
<meta name="color-scheme" content="light dark">
<?php if ($hub_head_description !== '') : ?>
<meta name="description" content="<?php echo esc_attr($hub_head_description); ?>">
<?php endif; ?>
<link rel="icon" type="image/png" href="<?php echo esc_url($hub_theme_uri . '/img/agencyhub-icon.png'); ?>">
<link rel="apple-touch-icon" href="<?php echo esc_url($hub_theme_uri . '/img/agencyhub-icon.png'); ?>">
<?php if ($hub_head_pwa) : ?>
<link rel="manifest" href="<?php echo esc_url($hub_theme_uri . '/manifest.json'); ?>">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="Agency Hub">
<?php endif; ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script>
    window.tailwindCDNWarning = false;
    window.ESIROM_API_URL = <?php echo wp_json_encode(esc_url_raw($hub_api_url)); ?>;
    window.ESIROM_SUPER_ADMIN_EMAILS = <?php echo wp_json_encode(array_values(esirom_hub_super_admin_emails())); ?>;
    // Verbose diagnostics are opt-in: run localStorage.setItem('hubDebug', '1') in the console.
    window.hubDebug = function () {
        try {
            if (localStorage.getItem('hubDebug') === '1') { console.log.apply(console, arguments); }
        } catch (e) { /* storage unavailable */ }
    };
    (function () {
        try {
            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark');
            }
        } catch (e) { /* storage unavailable */ }
    })();
</script>
<script src="<?php echo esc_url($hub_theme_uri . '/js/hub-auth.js?ver=' . rawurlencode($hub_theme_ver)); ?>"></script>
<script src="<?php echo esc_url($hub_tailwind_src); ?>"></script>
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'Segoe UI', 'Roboto', 'sans-serif'] },
                colors: {
                    brand: {
                        50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe', 300: '#a5b4fc', 400: '#818cf8',
                        500: '#6366f1', 600: '#4f46e5', 700: '#4338ca', 800: '#3730a3', 900: '#312e81', 950: '#1e1b4b'
                    }
                },
                boxShadow: {
                    hub: '0 1px 2px rgba(15, 23, 42, 0.04), 0 8px 24px rgba(15, 23, 42, 0.06)',
                    'hub-lg': '0 12px 40px rgba(15, 23, 42, 0.14)'
                },
                borderRadius: { hub: '0.75rem' }
            }
        }
    };
</script>
<link rel="stylesheet" href="<?php echo esc_url($hub_theme_uri . '/style.css?ver=' . rawurlencode($hub_theme_ver)); ?>">
<link rel="stylesheet" href="<?php echo esc_url($hub_theme_uri . '/css/hub-ui.css?ver=' . rawurlencode($hub_theme_ver)); ?>">
