<?php
/**
 * Template Name: Inventory
 * Description: Agency equipment inventory — staff only
 */
if (!defined('ABSPATH')) exit;

$api_url = get_option('esirom_api_url', 'https://esirom-hub-backend-production.up.railway.app/api');
$login_url = esc_url(get_permalink(get_page_by_path('login')));
$dashboard_url = esc_url(get_permalink(get_page_by_path('dashboard')));
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory — Agency Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        const API_URL = '<?php echo esc_js($api_url); ?>';
        const LOGIN_URL = '<?php echo esc_js($login_url); ?>';
        const DASHBOARD_URL = '<?php echo esc_js($dashboard_url); ?>';
        tailwind.config = { darkMode: 'class', theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } } };
    </script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
        .inv-fade { animation: fadeIn 0.25s ease forwards; }
        <?php esirom_hub_layout_styles(); ?>
    </style>
</head>
<body class="hub-has-mobile-nav h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 pb-16 md:pb-0" x-data="inventoryApp()" x-init="init()">

<div x-show="!authChecked" class="fixed inset-0 flex items-center justify-center bg-white dark:bg-gray-900 z-50">
    <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
</div>

<div x-show="authChecked && !user" x-cloak class="fixed inset-0 flex items-center justify-center">
    <div class="text-center">
        <p class="text-gray-500 mb-4">You must be logged in.</p>
        <a href="<?php echo $login_url; ?>" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Go to Login</a>
    </div>
</div>

<div x-show="authChecked && user" x-cloak class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="hidden md:flex flex-col w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex-shrink-0">
        <div class="flex items-center p-5 border-b border-gray-200 dark:border-gray-700">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div><p class="text-xs font-bold">Agency Hub</p><p class="text-[10px] text-gray-500">by esirom</p></div>
        </div>
        <nav class="flex-1 px-2 py-4 space-y-2 overflow-y-auto">
            <?php esirom_hub_staff_sidebar_nav('inventory', 'site', false); ?>
        </nav>
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <?php esirom_hub_staff_sidebar_footer('site', false); ?>
        </div>
    </aside>

    <!-- Main -->
    <main class="flex-1 overflow-y-auto">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 sticky top-0 z-10">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div>
                    <h1 class="text-xl font-bold flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Inventory
                    </h1>
                    <p class="text-xs text-gray-500 mt-0.5">Agency equipment — check out gear for shoots &amp; events</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <button x-show="canManage" @click="openEquipmentModal()" class="px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">+ Add Equipment</button>
                    <button @click="openBookingModal()" class="px-3 py-1.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Request Booking</button>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex gap-1 mt-4 flex-wrap">
                <template x-for="t in tabs" :key="t.id">
                    <button @click="tab = t.id; loadTab()"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg transition-all relative"
                            :class="tab === t.id ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                            x-text="t.label">
                    </button>
                </template>
                <span x-show="canApprove && stats.pendingApprovals > 0"
                      class="ml-1 px-2 py-0.5 text-[10px] font-bold bg-red-500 text-white rounded-full self-center"
                      x-text="stats.pendingApprovals + ' pending'"></span>
            </div>
        </div>

        <div class="p-6 space-y-6">

            <!-- Loading -->
            <div x-show="loading" class="flex justify-center py-20">
                <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
            </div>

            <!-- DASHBOARD -->
            <div x-show="!loading && tab === 'dashboard'" x-cloak class="inv-fade space-y-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    <template x-for="s in statCards" :key="s.key">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-4">
                            <p class="text-2xl font-bold" :class="s.color" x-text="stats[s.key] ?? '—'"></p>
                            <p class="text-xs text-gray-500 mt-1" x-text="s.label"></p>
                        </div>
                    </template>
                </div>

                <!-- Quick available equipment -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                        <h2 class="font-semibold">Available Equipment</h2>
                        <button @click="tab='equipment'; availabilityFilter='available'; loadTab()" class="text-xs text-indigo-600 hover:underline">View all →</button>
                    </div>
                    <div class="divide-y dark:divide-gray-700">
                        <template x-for="item in equipment.filter(e => e.availability === 'available').slice(0,8)" :key="item._id">
                            <div class="px-5 py-3 flex items-center gap-4">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate" x-text="item.name"></p>
                                    <p class="text-xs text-gray-500" x-text="(item.model || '—') + ' · ' + item.category"></p>
                                </div>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">Available</span>
                                <button @click="openBookingModal(item)" class="text-xs text-indigo-600 hover:underline">Book</button>
                            </div>
                        </template>
                        <div x-show="equipment.filter(e => e.availability === 'available').length === 0" class="px-5 py-8 text-center text-sm text-gray-500">No equipment currently available</div>
                    </div>
                </div>
            </div>

            <!-- EQUIPMENT -->
            <div x-show="!loading && tab === 'equipment'" x-cloak class="inv-fade space-y-4">
                <div class="flex gap-3 flex-wrap">
                    <input x-model="search" @input.debounce.300ms="loadEquipment()" type="search" placeholder="Search name, model, serial…" class="flex-1 min-w-[200px] border dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800">
                    <select x-model="categoryFilter" @change="loadEquipment()" class="border dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800">
                        <option value="">All Categories</option>
                        <template x-for="c in categories" :key="c"><option :value="c" x-text="cap(c)"></option></template>
                    </select>
                    <select x-model="availabilityFilter" @change="loadEquipment()" class="border dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800">
                        <option value="">All Availability</option>
                        <option value="available">Available</option>
                        <option value="booked">Booked</option>
                        <option value="checked_out">Checked Out</option>
                        <option value="pending">Pending Approval</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-4 py-3 text-left">Equipment</th>
                                <th class="px-4 py-3 text-left hidden sm:table-cell">Category</th>
                                <th class="px-4 py-3 text-left hidden md:table-cell">Serial</th>
                                <th class="px-4 py-3 text-left">Condition</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            <template x-for="item in equipment" :key="item._id">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-4 py-3">
                                        <p class="font-medium" x-text="item.name"></p>
                                        <p class="text-xs text-gray-500" x-text="item.model || ''"></p>
                                    </td>
                                    <td class="px-4 py-3 hidden sm:table-cell capitalize text-gray-600" x-text="item.category"></td>
                                    <td class="px-4 py-3 hidden md:table-cell text-gray-500 font-mono text-xs" x-text="item.serialNumber || '—'"></td>
                                    <td class="px-4 py-3">
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full capitalize"
                                              :class="conditionClass(item.status)" x-text="item.status.replace('_',' ')"></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full capitalize"
                                              :class="availClass(item.availability)" x-text="item.availability.replace('_',' ')"></span>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <button x-show="item.availability === 'available'" @click="openBookingModal(item)" class="text-xs text-emerald-600 hover:underline mr-2">Book</button>
                                        <button x-show="canManage" @click="openEquipmentModal(item)" class="text-xs text-indigo-600 hover:underline mr-2">Edit</button>
                                        <button x-show="canManage" @click="deleteEquipment(item)" class="text-xs text-red-500 hover:underline">Remove</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div x-show="equipment.length === 0" class="py-12 text-center text-gray-500 text-sm">No equipment found</div>
                </div>
            </div>

            <!-- MY BOOKINGS -->
            <div x-show="!loading && tab === 'bookings'" x-cloak class="inv-fade space-y-4">
                <div class="space-y-3">
                    <template x-for="b in myBookings" :key="b._id">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-4 flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold" x-text="b.equipmentId?.name"></p>
                                <p class="text-sm text-gray-600 dark:text-gray-400" x-text="b.purpose"></p>
                                <p class="text-xs text-gray-500 mt-1"
                                   x-text="fmtDate(b.eventDate) + ' → ' + fmtDate(b.returnDate) + (b.clientId ? ' · ' + (b.clientId.brandName || b.clientId.name) : '')"></p>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-1 rounded-full capitalize self-start"
                                  :class="bookingStatusClass(b.status)" x-text="b.status.replace('_',' ')"></span>
                            <div class="flex gap-2 flex-wrap">
                                <button x-show="b.status === 'approved'" @click="checkout(b)" class="px-3 py-1 bg-indigo-600 text-white text-xs rounded-lg">Check Out</button>
                                <button x-show="b.status === 'checked_out'" @click="checkin(b)" class="px-3 py-1 bg-emerald-600 text-white text-xs rounded-lg">Check In</button>
                                <button x-show="['pending_approval','approved'].includes(b.status)" @click="cancelBooking(b)" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 text-xs rounded-lg">Cancel</button>
                            </div>
                        </div>
                    </template>
                    <div x-show="myBookings.length === 0" class="py-12 text-center text-gray-500 text-sm">No bookings yet — request equipment above</div>
                </div>
            </div>

            <!-- APPROVALS (managers) -->
            <div x-show="!loading && tab === 'approvals' && canApprove" x-cloak class="inv-fade space-y-4">
                <template x-for="b in pendingBookings" :key="b._id">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border-2 border-amber-200 dark:border-amber-800 p-5">
                        <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                            <div class="flex-1">
                                <p class="font-bold text-lg" x-text="b.equipmentId?.name"></p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1" x-text="b.purpose"></p>
                                <div class="flex flex-wrap gap-3 mt-2 text-xs text-gray-500">
                                    <span>👤 <span x-text="(b.bookedBy?.firstName || '') + ' ' + (b.bookedBy?.lastName || '')"></span></span>
                                    <span>📅 <span x-text="fmtDate(b.eventDate) + ' → ' + fmtDate(b.returnDate)"></span></span>
                                    <span x-show="b.clientId">🏢 <span x-text="b.clientId.brandName || b.clientId.name"></span></span>
                                </div>
                                <p x-show="b.notes" class="text-xs text-gray-500 mt-2 italic" x-text="'Note: ' + b.notes"></p>
                            </div>
                            <div class="flex gap-2 flex-shrink-0">
                                <button @click="approveBooking(b)" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Approve</button>
                                <button @click="rejectBooking(b)" class="px-4 py-2 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200">Decline</button>
                            </div>
                        </div>
                    </div>
                </template>
                <div x-show="pendingBookings.length === 0" class="py-12 text-center text-gray-500 text-sm">No pending approvals</div>
            </div>

        </div>
    </main>
</div>

<!-- Toast -->
<div x-show="toast.show" x-cloak
     class="fixed bottom-6 right-6 z-50 px-4 py-3 rounded-xl shadow-lg text-sm font-medium text-white max-w-sm"
     :class="toast.type === 'success' ? 'bg-emerald-600' : toast.type === 'error' ? 'bg-red-600' : 'bg-gray-800'"
     x-text="toast.message"></div>

<!-- Equipment Modal -->
<div x-show="showEquipmentModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @keydown.escape.window="showEquipmentModal = false">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-lg p-6" @click.outside="showEquipmentModal = false">
        <h3 class="text-lg font-bold mb-4" x-text="equipmentForm._id ? 'Edit Equipment' : 'Add Equipment'"></h3>
        <div class="space-y-3">
            <div><label class="text-xs font-medium text-gray-500">Name *</label><input x-model="equipmentForm.name" class="w-full border dark:border-gray-600 rounded-lg px-3 py-2 text-sm mt-1 bg-white dark:bg-gray-700"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="text-xs font-medium text-gray-500">Model</label><input x-model="equipmentForm.model" class="w-full border dark:border-gray-600 rounded-lg px-3 py-2 text-sm mt-1 bg-white dark:bg-gray-700"></div>
                <div><label class="text-xs font-medium text-gray-500">Serial Number</label><input x-model="equipmentForm.serialNumber" class="w-full border dark:border-gray-600 rounded-lg px-3 py-2 text-sm mt-1 bg-white dark:bg-gray-700"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="text-xs font-medium text-gray-500">Category</label>
                    <select x-model="equipmentForm.category" class="w-full border dark:border-gray-600 rounded-lg px-3 py-2 text-sm mt-1 bg-white dark:bg-gray-700">
                        <template x-for="c in categories" :key="c"><option :value="c" x-text="cap(c)"></option></template>
                    </select>
                </div>
                <div><label class="text-xs font-medium text-gray-500">Condition</label>
                    <select x-model="equipmentForm.status" class="w-full border dark:border-gray-600 rounded-lg px-3 py-2 text-sm mt-1 bg-white dark:bg-gray-700">
                        <template x-for="s in conditions" :key="s"><option :value="s" x-text="cap(s.replace('_',' '))"></option></template>
                    </select>
                </div>
            </div>
            <div><label class="text-xs font-medium text-gray-500">Storage Location</label><input x-model="equipmentForm.location" class="w-full border dark:border-gray-600 rounded-lg px-3 py-2 text-sm mt-1 bg-white dark:bg-gray-700" placeholder="e.g. Studio shelf A"></div>
            <div><label class="text-xs font-medium text-gray-500">Notes</label><textarea x-model="equipmentForm.notes" rows="2" class="w-full border dark:border-gray-600 rounded-lg px-3 py-2 text-sm mt-1 bg-white dark:bg-gray-700"></textarea></div>
        </div>
        <div class="flex justify-end gap-2 mt-5">
            <button @click="showEquipmentModal = false" class="px-4 py-2 text-sm text-gray-600">Cancel</button>
            <button @click="saveEquipment()" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg">Save</button>
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div x-show="showBookingModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @keydown.escape.window="showBookingModal = false">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-lg p-6" @click.outside="showBookingModal = false">
        <h3 class="text-lg font-bold mb-4">Request Equipment Booking</h3>
        <div class="space-y-3">
            <div><label class="text-xs font-medium text-gray-500">Equipment *</label>
                <select x-model="bookingForm.equipmentId" class="w-full border dark:border-gray-600 rounded-lg px-3 py-2 text-sm mt-1 bg-white dark:bg-gray-700">
                    <option value="">Select equipment…</option>
                    <template x-for="item in equipment.filter(e => ['available','booked','pending'].includes(e.availability) || e._id === bookingForm.equipmentId)" :key="item._id">
                        <option :value="item._id" :disabled="item.availability !== 'available' && item._id !== bookingForm.equipmentId"
                                x-text="item.name + (item.availability === 'available' ? '' : ' (' + item.availability + ')')"></option>
                    </template>
                </select>
            </div>
            <div><label class="text-xs font-medium text-gray-500">Purpose / Shoot / Event *</label>
                <input x-model="bookingForm.purpose" class="w-full border dark:border-gray-600 rounded-lg px-3 py-2 text-sm mt-1 bg-white dark:bg-gray-700" placeholder="e.g. FOSA product shoot, GrabNGo training video">
            </div>
            <div><label class="text-xs font-medium text-gray-500">Client (optional)</label>
                <select x-model="bookingForm.clientId" class="w-full border dark:border-gray-600 rounded-lg px-3 py-2 text-sm mt-1 bg-white dark:bg-gray-700">
                    <option value="">No specific client</option>
                    <template x-for="c in clients" :key="c._id"><option :value="c._id" x-text="c.brandName || c.name"></option></template>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="text-xs font-medium text-gray-500">Pickup / Event Date *</label><input type="date" x-model="bookingForm.eventDate" class="w-full border dark:border-gray-600 rounded-lg px-3 py-2 text-sm mt-1 bg-white dark:bg-gray-700"></div>
                <div><label class="text-xs font-medium text-gray-500">Return Date *</label><input type="date" x-model="bookingForm.returnDate" class="w-full border dark:border-gray-600 rounded-lg px-3 py-2 text-sm mt-1 bg-white dark:bg-gray-700"></div>
            </div>
            <div><label class="text-xs font-medium text-gray-500">Notes</label><textarea x-model="bookingForm.notes" rows="2" class="w-full border dark:border-gray-600 rounded-lg px-3 py-2 text-sm mt-1 bg-white dark:bg-gray-700" placeholder="Any special requirements…"></textarea></div>
            <p class="text-xs text-amber-600 dark:text-amber-400">Managers will be notified by email and in Hub to approve this booking.</p>
        </div>
        <div class="flex justify-end gap-2 mt-5">
            <button @click="showBookingModal = false" class="px-4 py-2 text-sm text-gray-600">Cancel</button>
            <button @click="submitBooking()" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg">Submit Request</button>
        </div>
    </div>
</div>

<script>
function inventoryApp() {
    return {
        authChecked: false,
        user: null,
        loading: false,
        tab: 'dashboard',
        stats: {},
        equipment: [],
        myBookings: [],
        pendingBookings: [],
        clients: [],
        search: '',
        categoryFilter: '',
        availabilityFilter: '',
        showEquipmentModal: false,
        showBookingModal: false,
        equipmentForm: {},
        bookingForm: {},
        toast: { show: false, message: '', type: 'success' },
        viewMode: localStorage.getItem('viewMode') || 'admin',

        categories: ['camera','lens','lighting','audio','grip','computer','drone','other'],
        conditions: ['new','good','needs_servicing','old','other'],

        get canManage() { return this.user?.role === 'admin' || this.user?.isManager; },
        get canApprove() { return this.user?.role === 'admin' || this.user?.isManager; },

        get tabs() {
            const t = [
                { id: 'dashboard', label: 'Dashboard' },
                { id: 'equipment', label: 'Equipment' },
                { id: 'bookings', label: 'My Bookings' }
            ];
            if (this.canApprove) t.push({ id: 'approvals', label: 'Approvals' });
            return t;
        },

        get statCards() {
            return [
                { key: 'total', label: 'Total Items', color: 'text-gray-900 dark:text-white' },
                { key: 'available', label: 'Available', color: 'text-emerald-600' },
                { key: 'checkedOut', label: 'Checked Out', color: 'text-indigo-600' },
                { key: 'booked', label: 'Booked / Pending', color: 'text-amber-600' },
                { key: 'needsServicing', label: 'Needs Servicing', color: 'text-orange-600' },
                { key: 'pendingApprovals', label: 'Awaiting Approval', color: 'text-red-600' }
            ];
        },

        async init() {
            const token = localStorage.getItem('token');
            if (!token) { this.authChecked = true; return; }
            try {
                const res = await fetch(`${API_URL}/auth/me`, { headers: { Authorization: `Bearer ${token}` } });
                if (!res.ok) throw new Error('auth');
                const data = await res.json();
                this.user = data.user;
                if (this.user.role === 'client') {
                    window.location.href = DASHBOARD_URL;
                    return;
                }
                const params = new URLSearchParams(window.location.search);
                const urlTab = params.get('tab');
                if (urlTab) this.tab = urlTab;
                await this.loadAll();
            } catch (e) {
                this.user = null;
            } finally {
                this.authChecked = true;
            }
        },

        async loadAll() {
            this.loading = true;
            try {
                await Promise.all([
                    this.loadDashboard(),
                    this.loadEquipment(),
                    this.loadBookings(),
                    this.loadClients()
                ]);
                if (this.canApprove) await this.loadPending();
            } finally {
                this.loading = false;
            }
        },

        async loadTab() {
            this.loading = true;
            try {
                if (this.tab === 'dashboard') await this.loadDashboard();
                if (this.tab === 'equipment') await this.loadEquipment();
                if (this.tab === 'bookings') await this.loadBookings();
                if (this.tab === 'approvals') await this.loadPending();
            } finally { this.loading = false; }
        },

        headers() {
            return { Authorization: `Bearer ${localStorage.getItem('token')}`, 'Content-Type': 'application/json' };
        },

        async loadDashboard() {
            const res = await fetch(`${API_URL}/inventory/dashboard`, { headers: this.headers() });
            const data = await res.json();
            if (data.success) this.stats = data.stats;
        },

        async loadEquipment() {
            const params = new URLSearchParams();
            if (this.search) params.set('search', this.search);
            if (this.categoryFilter) params.set('category', this.categoryFilter);
            if (this.availabilityFilter) params.set('availability', this.availabilityFilter);
            const res = await fetch(`${API_URL}/inventory/equipment?${params}`, { headers: this.headers() });
            const data = await res.json();
            if (data.success) this.equipment = data.equipment;
        },

        async loadBookings() {
            const res = await fetch(`${API_URL}/inventory/bookings?mine=true`, { headers: this.headers() });
            const data = await res.json();
            if (data.success) this.myBookings = data.bookings;
        },

        async loadPending() {
            const res = await fetch(`${API_URL}/inventory/bookings?pending=true`, { headers: this.headers() });
            const data = await res.json();
            if (data.success) this.pendingBookings = data.bookings;
        },

        async loadClients() {
            const res = await fetch(`${API_URL}/inventory/clients`, { headers: this.headers() });
            const data = await res.json();
            if (data.success) this.clients = data.clients;
        },

        openEquipmentModal(item = null) {
            this.equipmentForm = item ? { ...item } : { name: '', model: '', serialNumber: '', category: 'camera', status: 'good', location: '', notes: '' };
            this.showEquipmentModal = true;
        },

        async saveEquipment() {
            if (!this.equipmentForm.name?.trim()) { this.showToast('Name is required', 'error'); return; }
            const isEdit = !!this.equipmentForm._id;
            const url = isEdit ? `${API_URL}/inventory/equipment/${this.equipmentForm._id}` : `${API_URL}/inventory/equipment`;
            const res = await fetch(url, { method: isEdit ? 'PATCH' : 'POST', headers: this.headers(), body: JSON.stringify(this.equipmentForm) });
            const data = await res.json();
            if (data.success) {
                this.showEquipmentModal = false;
                this.showToast(isEdit ? 'Equipment updated' : 'Equipment added', 'success');
                await this.loadAll();
            } else {
                this.showToast(data.message || 'Failed to save', 'error');
            }
        },

        async deleteEquipment(item) {
            if (!confirm(`Remove "${item.name}" from inventory?`)) return;
            const res = await fetch(`${API_URL}/inventory/equipment/${item._id}`, { method: 'DELETE', headers: this.headers() });
            const data = await res.json();
            if (data.success) { this.showToast('Equipment removed', 'success'); await this.loadAll(); }
            else this.showToast(data.message || 'Failed', 'error');
        },

        openBookingModal(item = null) {
            const today = new Date().toISOString().slice(0, 10);
            this.bookingForm = { equipmentId: item?._id || '', purpose: '', clientId: '', eventDate: today, returnDate: today, notes: '' };
            this.showBookingModal = true;
        },

        async submitBooking() {
            const f = this.bookingForm;
            if (!f.equipmentId || !f.purpose || !f.eventDate || !f.returnDate) {
                this.showToast('Fill in all required fields', 'error'); return;
            }
            const res = await fetch(`${API_URL}/inventory/bookings`, {
                method: 'POST', headers: this.headers(),
                body: JSON.stringify({ ...f, clientId: f.clientId || null })
            });
            const data = await res.json();
            if (data.success) {
                this.showBookingModal = false;
                this.showToast('Booking request sent — awaiting manager approval', 'success');
                this.tab = 'bookings';
                await this.loadAll();
            } else {
                this.showToast(data.message || 'Failed to submit', 'error');
            }
        },

        async approveBooking(b) {
            const res = await fetch(`${API_URL}/inventory/bookings/${b._id}/approve`, { method: 'PATCH', headers: this.headers(), body: '{}' });
            const data = await res.json();
            if (data.success) { this.showToast('Booking approved — staff notified', 'success'); await this.loadAll(); }
            else this.showToast(data.message || 'Failed', 'error');
        },

        async rejectBooking(b) {
            const reason = prompt('Reason for declining (optional):') || '';
            const res = await fetch(`${API_URL}/inventory/bookings/${b._id}/reject`, { method: 'PATCH', headers: this.headers(), body: JSON.stringify({ rejectionReason: reason }) });
            const data = await res.json();
            if (data.success) { this.showToast('Booking declined', 'success'); await this.loadAll(); }
            else this.showToast(data.message || 'Failed', 'error');
        },

        async checkout(b) {
            const res = await fetch(`${API_URL}/inventory/bookings/${b._id}/checkout`, { method: 'PATCH', headers: this.headers(), body: '{}' });
            const data = await res.json();
            if (data.success) { this.showToast('Equipment checked out', 'success'); await this.loadAll(); }
            else this.showToast(data.message || 'Failed', 'error');
        },

        async checkin(b) {
            const res = await fetch(`${API_URL}/inventory/bookings/${b._id}/checkin`, { method: 'PATCH', headers: this.headers(), body: '{}' });
            const data = await res.json();
            if (data.success) { this.showToast('Equipment returned — thanks!', 'success'); await this.loadAll(); }
            else this.showToast(data.message || 'Failed', 'error');
        },

        async cancelBooking(b) {
            if (!confirm('Cancel this booking request?')) return;
            const res = await fetch(`${API_URL}/inventory/bookings/${b._id}/cancel`, { method: 'PATCH', headers: this.headers(), body: '{}' });
            const data = await res.json();
            if (data.success) { this.showToast('Booking cancelled', 'success'); await this.loadAll(); }
            else this.showToast(data.message || 'Failed', 'error');
        },

        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => { this.toast.show = false; }, 4000);
        },

        cap(s) { return String(s || '').replace(/\b\w/g, c => c.toUpperCase()).replace(/_/g, ' '); },
        fmtDate(d) { return d ? new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : '—'; },

        conditionClass(s) {
            return { new: 'bg-emerald-100 text-emerald-700', good: 'bg-blue-100 text-blue-700', needs_servicing: 'bg-orange-100 text-orange-700', old: 'bg-gray-200 text-gray-600', other: 'bg-gray-100 text-gray-600' }[s] || 'bg-gray-100 text-gray-600';
        },
        availClass(a) {
            return { available: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300', checked_out: 'bg-indigo-100 text-indigo-700', booked: 'bg-amber-100 text-amber-700', pending: 'bg-yellow-100 text-yellow-700', unavailable: 'bg-red-100 text-red-700', inactive: 'bg-gray-200 text-gray-500' }[a] || 'bg-gray-100 text-gray-600';
        },
        bookingStatusClass(s) {
            return { pending_approval: 'bg-amber-100 text-amber-700', approved: 'bg-blue-100 text-blue-700', rejected: 'bg-red-100 text-red-700', checked_out: 'bg-indigo-100 text-indigo-700', returned: 'bg-emerald-100 text-emerald-700', cancelled: 'bg-gray-200 text-gray-500' }[s] || 'bg-gray-100';
        }
    };
}
</script>

<?php esirom_hub_staff_mobile_nav('inventory'); ?>
</body>
</html>
