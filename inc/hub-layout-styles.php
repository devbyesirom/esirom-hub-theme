<?php
/**
 * Inline layout CSS for Agency Hub page templates.
 *
 * The shared layout rules (app shell, sidebar, sticky header, mobile nav)
 * live in /css/hub-ui.css, which is linked by esirom_hub_head_assets().
 * This partial only keeps the critical rules that must apply before the
 * stylesheet loads so the page never jumps when WordPress adds its admin
 * bar offsets.
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
#wpadminbar { display: none !important; }
html, html.admin-bar { margin-top: 0 !important; padding-top: 0 !important; }
body.admin-bar { margin-top: 0 !important; padding-top: 0 !important; }
[x-cloak] { display: none !important; }
