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
.hub-sticky-header {
    position: sticky;
    top: 0;
    z-index: 40;
    overflow: visible;
}
.hub-menu-dropdown {
    position: fixed;
    z-index: 100;
    max-height: min(24rem, calc(100dvh - 6rem));
    overflow-y: auto;
    background: #fff;
    border: 1px solid rgba(15, 23, 42, 0.08);
    border-radius: 0.75rem;
    box-shadow: 0 12px 40px rgba(15, 23, 42, 0.14);
}
.dark .hub-menu-dropdown {
    background: rgb(31, 41, 55);
    border-color: rgba(148, 163, 184, 0.2);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.45);
}
.hub-page-content {
    position: relative;
    z-index: 0;
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

.hub-mobile-bottom-nav {
    padding-bottom: env(safe-area-inset-bottom, 0);
}

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
