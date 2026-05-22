<?php
/**
 * Shared layout CSS for Agency Hub page templates (mobile bottom nav fix).
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
@media (min-width: 768px) {
    .hub-mobile-bottom-nav {
        display: none !important;
        visibility: hidden !important;
        pointer-events: none !important;
        height: 0 !important;
        overflow: hidden !important;
    }
    body.hub-has-mobile-nav {
        padding-bottom: 0 !important;
    }
}
