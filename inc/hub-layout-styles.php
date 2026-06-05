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

.hub-app-shell {
    height: 100vh;
    height: 100dvh;
    overflow: hidden;
}
.hub-app-sidebar {
    min-height: 0;
    overflow: hidden;
}
.hub-app-main {
    flex: 1 1 0%;
    min-height: 0;
    min-width: 0;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}
.hub-page-content {
    padding-bottom: 2.5rem;
}
@media (min-width: 768px) {
    .hub-page-content {
        padding-bottom: 3rem;
    }
}

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
