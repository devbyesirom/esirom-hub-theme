/**
 * Global Role Switcher Component for Agency Hub
 * Allows admins to switch between different user view modes
 */

// Role Switcher HTML Template
function getRoleSwitcherHTML() {
    return `
        <div x-show="user?.role === 'admin'" class="relative" x-data="{ showRoleSwitcher: false }">
            <button @click="showRoleSwitcher = !showRoleSwitcher" class="flex items-center gap-2 px-3 py-2 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 hover:bg-purple-200 dark:hover:bg-purple-900/50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                <span class="text-sm font-medium" x-text="viewMode === 'admin' ? 'Admin View' : viewMode === 'brand_rep' ? 'Brand Rep View' : 'Client View'"></span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="showRoleSwitcher" @click.away="showRoleSwitcher = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50">
                <div class="p-2">
                    <button @click="switchViewMode('admin'); showRoleSwitcher = false" :class="viewMode === 'admin' ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        Admin View
                    </button>
                    <button @click="switchViewMode('brand_rep'); showRoleSwitcher = false" :class="viewMode === 'brand_rep' ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        Brand Rep View
                    </button>
                    <button @click="switchViewMode('client'); showRoleSwitcher = false" :class="viewMode === 'client' ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        Client View
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Initialize role switcher on page load
document.addEventListener('DOMContentLoaded', function() {
    // Find all role-switcher-container elements and inject the HTML
    const containers = document.querySelectorAll('[data-role-switcher]');
    containers.forEach(container => {
        container.innerHTML = getRoleSwitcherHTML();
    });
});

// Global view mode utilities
window.AgencyHubViewMode = {
    get: function() {
        return localStorage.getItem('viewMode') || 'admin';
    },
    
    set: function(mode) {
        localStorage.setItem('viewMode', mode);
    },
    
    // Filter API requests based on view mode
    applyViewModeToURL: function(url, user) {
        const viewMode = this.get();
        
        // If in client view mode and user is admin, add client filter
        if (viewMode === 'client' && user?.role === 'admin') {
            // Get first available client for demo purposes
            // In production, you'd want to select a specific client
            const urlObj = new URL(url, window.location.origin);
            urlObj.searchParams.set('viewAs', 'client');
            return urlObj.toString();
        }
        
        // If in brand_rep view mode and user is admin
        if (viewMode === 'brand_rep' && user?.role === 'admin') {
            const urlObj = new URL(url, window.location.origin);
            urlObj.searchParams.set('viewAs', 'brand_rep');
            return urlObj.toString();
        }
        
        return url;
    }
};
