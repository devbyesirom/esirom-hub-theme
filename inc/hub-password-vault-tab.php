<?php
/**
 * Password Vault tab for Workflow Manager.
 * Included from page-workflow.php — uses workflowApp() Alpine state/methods.
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<div x-show="activeTab === 'passwords'" x-cloak>
    <!-- Toolbar -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"></path></svg>
                <input type="text" x-model="credentialSearch" @input.debounce.300ms="loadCredentials()" placeholder="Search accounts…" class="pl-9 pr-4 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white w-56">
            </div>

            <select x-show="user.role === 'admin'" x-model="credentialStatusFilter" @change="loadCredentials()" class="px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="archived">Archived</option>
            </select>

            <select x-model="credentialCategoryFilter" @change="loadCredentials()" class="px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">All Categories</option>
                <option value="social_media">Social Media</option>
                <option value="email">Email</option>
                <option value="analytics">Analytics</option>
                <option value="design_tools">Design Tools</option>
                <option value="link_tools">Link Tools</option>
                <option value="hosting">Hosting</option>
                <option value="utilities">Utilities</option>
                <option value="other">Other</option>
            </select>

            <select x-show="(user.role === 'admin' || user.role === 'brand_rep') && clients.length > 0" x-model="credentialClientFilter" @change="loadCredentials()" class="px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">All Brands</option>
                <template x-for="client in clients" :key="client._id">
                    <option :value="client._id" x-text="client.brandName || client.name"></option>
                </template>
            </select>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <div x-show="credentialOverdueCount > 0 && user.role !== 'client'" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                <span x-text="credentialOverdueCount"></span> need verification (90-day cycle)
            </div>
            <label x-show="user.role === 'admin'" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                Import CSV
                <input type="file" accept=".csv,text/csv" @change="importCredentialsCsv($event)" class="hidden">
            </label>
            <button x-show="user.role === 'admin' || user.role === 'brand_rep'" @click="openCredentialModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Account
            </button>
        </div>
    </div>

    <!-- Credentials Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[960px]">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Brand / Group</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Account</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Username</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Recovery</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Verified</th>
                        <th x-show="user.role === 'admin'" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y dark:divide-gray-700">
                    <template x-for="item in credentials" :key="item._id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 text-sm">
                                <p class="font-medium text-gray-900 dark:text-white" x-text="item.clientId?.brandName || item.groupName || '—'"></p>
                                <p class="text-xs text-gray-500" x-show="item.groupName && item.clientId?.brandName" x-text="item.groupName"></p>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                <p x-text="item.accountName"></p>
                                <p class="text-xs text-gray-500 capitalize" x-show="item.platform" x-text="item.platform"></p>
                            </td>
                            <td class="px-4 py-3 text-sm capitalize text-gray-600 dark:text-gray-300" x-text="formatCredentialCategory(item.category)"></td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200 font-mono text-xs" x-text="item.username || '—'"></td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                <p class="truncate max-w-[140px]" x-text="item.recoveryEmail || '—'"></p>
                                <p class="text-xs text-gray-500" x-text="item.recoveryPhone || ''"></p>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span :class="getCredentialVerificationClass(item)" class="px-2 py-1 text-xs rounded-full" x-text="getCredentialVerificationLabel(item)"></span>
                            </td>
                            <td x-show="user.role === 'admin'" class="px-4 py-3 text-sm">
                                <span :class="item.status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'" class="px-2 py-1 text-xs rounded-full capitalize" x-text="item.status"></span>
                                <span x-show="item.visibleToBrandReps" class="ml-1 px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">Rep</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center gap-2">
                                    <button @click="viewCredential(item)" class="px-2.5 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700">View</button>
                                    <button x-show="user.role === 'admin' || user.role === 'brand_rep'" @click="editCredential(item)" class="px-2.5 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700">Edit</button>
                                    <button x-show="user.role === 'admin'" @click="deleteCredential(item)" class="px-2.5 py-1 text-xs text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div x-show="credentials.length === 0" class="p-10 text-center text-gray-500 dark:text-gray-400">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            <p class="font-medium">No credentials found</p>
            <p class="text-sm mt-1" x-show="user.role === 'admin'">Import your CSV or add accounts manually.</p>
        </div>
    </div>

    <!-- Detail / Edit Modal -->
    <div x-show="showCredentialModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @keydown.escape.window="closeCredentialModal()">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden" @click.stop>
            <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="credentialForm._id ? (credentialViewMode ? 'Account Details' : 'Edit Account') : 'Add Account'"></h3>
                    <p class="text-xs text-gray-500" x-show="selectedCredential?.clientId?.brandName" x-text="selectedCredential?.clientId?.brandName || credentialForm.groupName"></p>
                </div>
                <button @click="closeCredentialModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)] space-y-4">
                <!-- View mode -->
                <template x-if="credentialViewMode && selectedCredential">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Account</p>
                                <p class="font-medium text-gray-900 dark:text-white" x-text="selectedCredential.accountName"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Category</p>
                                <p class="font-medium text-gray-900 dark:text-white capitalize" x-text="formatCredentialCategory(selectedCredential.category)"></p>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">Username</p>
                            <div class="flex items-center gap-2">
                                <code class="flex-1 px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm" x-text="selectedCredential.username || '—'"></code>
                                <button x-show="selectedCredential.username" @click="copyCredentialValue(selectedCredential.username)" class="px-3 py-2 text-xs bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Copy</button>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">Password</p>
                            <div class="flex items-center gap-2">
                                <code class="flex-1 px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm font-mono" x-text="revealedPassword ? selectedCredential.password : '••••••••••••'"></code>
                                <button @click="toggleRevealPassword()" class="px-3 py-2 text-xs bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600" x-text="revealedPassword ? 'Hide' : 'Reveal'"></button>
                                <button x-show="selectedCredential.password" @click="copyCredentialValue(selectedCredential.password)" class="px-3 py-2 text-xs bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Copy</button>
                            </div>
                        </div>

                        <div x-show="selectedCredential.twoFactorNotes">
                            <p class="text-xs text-gray-500 mb-1">2FA / Recovery Codes</p>
                            <div class="flex items-start gap-2">
                                <pre class="flex-1 px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm whitespace-pre-wrap" x-text="selectedCredential.twoFactorNotes"></pre>
                                <button @click="copyCredentialValue(selectedCredential.twoFactorNotes)" class="px-3 py-2 text-xs bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 shrink-0">Copy</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Recovery Email</p>
                                <p class="text-sm text-gray-900 dark:text-white" x-text="selectedCredential.recoveryEmail || '—'"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Recovery Phone</p>
                                <p class="text-sm text-gray-900 dark:text-white" x-text="selectedCredential.recoveryPhone || '—'"></p>
                            </div>
                        </div>

                        <div x-show="selectedCredential.url">
                            <p class="text-xs text-gray-500 mb-1">Login URL</p>
                            <a :href="selectedCredential.url" target="_blank" rel="noopener noreferrer" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline break-all" x-text="selectedCredential.url"></a>
                        </div>

                        <div x-show="selectedCredential.notes">
                            <p class="text-xs text-gray-500 mb-1">Notes</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap" x-text="selectedCredential.notes"></p>
                        </div>

                        <div class="p-3 rounded-lg" :class="getCredentialVerificationClass(selectedCredential, true)">
                            <p class="text-sm font-medium" x-text="getCredentialVerificationLabel(selectedCredential, true)"></p>
                            <p class="text-xs mt-1 opacity-80">Recovery details should be verified every 90 days.</p>
                        </div>
                    </div>
                </template>

                <!-- Edit / Create mode -->
                <template x-if="!credentialViewMode">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Brand / Group</label>
                                <input type="text" x-model="credentialForm.groupName" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="e.g. Seprod Group">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Linked Client</label>
                                <select x-model="credentialForm.clientId" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">None</option>
                                    <template x-for="client in clients" :key="client._id">
                                        <option :value="client._id" x-text="client.brandName || client.name"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Account Name *</label>
                                <input type="text" x-model="credentialForm.accountName" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="e.g. Brand (IG)">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Category</label>
                                <select x-model="credentialForm.category" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="social_media">Social Media</option>
                                    <option value="email">Email</option>
                                    <option value="analytics">Analytics</option>
                                    <option value="design_tools">Design Tools</option>
                                    <option value="link_tools">Link Tools</option>
                                    <option value="hosting">Hosting</option>
                                    <option value="utilities">Utilities</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Platform</label>
                                <input type="text" x-model="credentialForm.platform" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="instagram, youtube, etc.">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Username</label>
                                <input type="text" x-model="credentialForm.username" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Password</label>
                            <input type="text" x-model="credentialForm.password" class="w-full px-3 py-2 border rounded-lg text-sm font-mono dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Leave blank to keep existing">
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 mb-1">2FA / Recovery Codes</label>
                            <textarea x-model="credentialForm.twoFactorNotes" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Backup codes, authenticator notes…"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Recovery Email</label>
                                <input type="email" x-model="credentialForm.recoveryEmail" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Recovery Phone</label>
                                <input type="text" x-model="credentialForm.recoveryPhone" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Login URL</label>
                            <input type="url" x-model="credentialForm.url" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="https://">
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Notes</label>
                            <textarea x-model="credentialForm.notes" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                        </div>

                        <div x-show="user.role === 'admin'" class="grid grid-cols-2 gap-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Status</label>
                                <select x-model="credentialForm.status" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="active">Active</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <label class="flex items-center gap-2 cursor-pointer pb-2">
                                    <input type="checkbox" x-model="credentialForm.visibleToBrandReps" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Visible to brand reps</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="px-6 py-4 border-t dark:border-gray-700 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <button x-show="credentialViewMode && (user.role === 'admin' || user.role === 'brand_rep')" @click="verifyCredential()" class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">Mark Verified</button>
                    <button x-show="credentialViewMode && (user.role === 'admin' || user.role === 'brand_rep')" @click="credentialViewMode = false" class="px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Edit</button>
                </div>
                <div class="flex items-center gap-2 ml-auto">
                    <button @click="closeCredentialModal()" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">Close</button>
                    <button x-show="!credentialViewMode" @click="saveCredential()" class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
