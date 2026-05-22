<?php
/**
 * Shared Change Password modal for Agency Hub page templates.
 * Requires the parent Alpine.js component to expose:
 *   showPwModal, pwCurrent, pwNew, pwConfirm, pwLoading, pwError, pwSuccess
 *   changePassword()
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<div x-show="showPwModal" x-cloak
     class="fixed inset-0 z-[200] flex items-center justify-center p-4"
     @keydown.escape.window="showPwModal = false; pwError = ''; pwSuccess = ''">

    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
         @click="showPwModal = false; pwError = ''; pwSuccess = ''"></div>

    <!-- Panel -->
    <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 p-6"
         @click.stop>

        <!-- Header -->
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">Change Password</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Update your account password</p>
                </div>
            </div>
            <button @click="showPwModal = false; pwError = ''; pwSuccess = ''"
                    class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Error message -->
        <div x-show="pwError" class="mb-4 flex items-start gap-2.5 px-4 py-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/40">
            <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <p class="text-sm text-red-700 dark:text-red-400" x-text="pwError"></p>
        </div>

        <!-- Success message -->
        <div x-show="pwSuccess" class="mb-4 flex items-start gap-2.5 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/40">
            <svg class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <p class="text-sm text-emerald-700 dark:text-emerald-400" x-text="pwSuccess"></p>
        </div>

        <!-- Form -->
        <form @submit.prevent="changePassword()" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Current Password</label>
                <input type="password" x-model="pwCurrent" autocomplete="current-password"
                       placeholder="Enter your current password"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">New Password</label>
                <input type="password" x-model="pwNew" autocomplete="new-password"
                       placeholder="Minimum 6 characters"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Confirm New Password</label>
                <input type="password" x-model="pwConfirm" autocomplete="new-password"
                       placeholder="Repeat new password"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 transition">
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button"
                        @click="showPwModal = false; pwError = ''; pwSuccess = ''; pwCurrent = ''; pwNew = ''; pwConfirm = ''"
                        class="flex-1 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" :disabled="pwLoading"
                        class="flex-1 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white text-sm font-semibold transition-colors flex items-center justify-center gap-2">
                    <svg x-show="pwLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="pwLoading ? 'Updating…' : 'Update Password'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
