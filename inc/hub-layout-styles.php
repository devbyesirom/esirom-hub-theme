<?php
/**
 * Shared layout CSS for Agency Hub page templates.
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
#wpadminbar { display: none !important; }
html { margin-top: 0 !important; padding-top: 0 !important; }
html.admin-bar { margin-top: 0 !important; }
body.admin-bar { margin-top: 0 !important; padding-top: 0 !important; }

.sidebar { transition: width 0.3s ease; }
.sidebar-collapsed { width: 5rem; }
.sidebar-expanded { width: 16rem; }
.sidebar .nav-text { display: inline; }
.sidebar-collapsed .nav-text { display: none; }
.sidebar-collapsed .justify-between { justify-content: center; }

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
