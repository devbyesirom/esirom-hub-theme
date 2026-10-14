<?php
/**
 * Shared header account menu (avatar → change password, log out) with an
 * optional dark-mode toggle.
 *
 * Requires the parent Alpine.js component to expose:
 *   user, showPwModal, logout()  and, when $hub_account_theme is true, theme + toggleTheme()
 *
 * Expects:
 *   $hub_account_theme bool  render the dark-mode toggle (default false)
 */
if (!defined('ABSPATH')) {
    exit;
}
$hub_account_theme = !empty($hub_account_theme);
?>
<div class="flex items-center gap-1">
    <?php if ($hub_account_theme) : ?>
    <button type="button" @click="toggleTheme()" class="hub-btn hub-btn--ghost hub-btn--icon" aria-label="Toggle dark mode">
        <svg x-show="theme === 'light'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
        <svg x-show="theme === 'dark'" x-cloak class="text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
    </button>
    <?php endif; ?>
    <div x-data="{ accountOpen: false }" class="relative">
        <button type="button" @click="accountOpen = !accountOpen" class="hub-btn hub-btn--ghost !px-1.5" :aria-expanded="accountOpen" aria-label="Account menu">
            <span class="hub-avatar h-7 w-7">
                <img x-show="user?.clientId?.logo" :src="user?.clientId?.logo" alt="">
                <span x-show="!user?.clientId?.logo" x-text="(user?.firstName?.[0] || user?.fullName?.[0] || 'U')"></span>
            </span>
            <span class="hidden lg:inline text-sm font-medium text-gray-700 dark:text-gray-300 max-w-[10rem] truncate" x-text="user?.clientId?.brandName || user?.fullName || ((user?.firstName || '') + ' ' + (user?.lastName || '')).trim()"></span>
            <svg class="text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="accountOpen" @click.away="accountOpen = false" @keydown.escape.window="accountOpen = false" x-cloak
             class="absolute right-0 mt-2 w-56 hub-card hub-card--elevated py-1 z-50">
            <div class="px-4 py-2.5 border-b border-gray-100 dark:border-gray-700">
                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="user?.fullName || ((user?.firstName || '') + ' ' + (user?.lastName || '')).trim()"></p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate" x-text="user?.email"></p>
                <p class="text-xs text-brand-600 dark:text-brand-300 mt-0.5 capitalize font-medium" x-text="(user?.role || '').replace('_', ' ')"></p>
            </div>
            <a @click.prevent="showPwModal = true; accountOpen = false" href="#" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Change password
            </a>
            <a @click.prevent="logout(); accountOpen = false" href="#" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                Log out
            </a>
        </div>
    </div>
</div>
