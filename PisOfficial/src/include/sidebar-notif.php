<!-- Sidebar Overlay -->
<div id="sidebarOverlay"
    class="fixed inset-0 bg-black/40 z-[60] hidden opacity-0 transition-opacity duration-300">
</div>

<!-- Notification Sidebar -->
<div id="notificationSidebar"
    style="transform: translateX(100%); transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);"
    class="fixed top-0 right-0 h-full w-80 md:w-[450px] bg-white z-[70] shadow-2xl border-l border-gray-100 flex flex-col rounded-l-[2.5rem] overflow-hidden">

    <div class="h-[100px] px-8 border-b border-gray-100 flex justify-between items-center bg-white">
        <div>
            <h3 class="font-black text-gray-900 uppercase text-xs tracking-[0.2em]">Notifications</h3>
            <div class="flex items-center gap-4 mt-1">
                <div class="flex items-center gap-2">
                    <span class="size-1.5 bg-red-500 rounded-full animate-pulse"></span>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">System Updates</p>
                </div>
                <button id="clearAllNotifsBtn" class="text-[9px] font-black text-red-500 uppercase tracking-widest hover:underline active:scale-95 transition-transform">
                    Clear All
                </button>
            </div>
        </div>
        <button id="closeSidebar" class="p-2 rounded-xl hover:bg-red-50 text-gray-400 hover:text-red-500 transition-all active:scale-95">
            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Notification List (Dynamic) -->
    <div id="notifList" class="flex-1 overflow-y-auto p-8 space-y-6">
        <div class="flex flex-col items-center justify-center h-full text-gray-400 space-y-4">
            <div class="size-12 border-4 border-gray-100 border-t-red-600 rounded-full animate-spin"></div>
            <p class="text-[10px] font-black uppercase tracking-widest">Checking alerts...</p>
        </div>
    </div>

    <div class="p-8 border-t border-gray-50 bg-white">
        <button id="markReadAndCloseBtn" class="w-full py-4 bg-gray-50 hover:bg-gray-100 text-gray-500 hover:text-red-500 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all active:scale-[0.98]">
            Mark as Read & Close
        </button>
    </div>
</div>

<div id="clearNotifModal" style="z-index: 9999;" class="fixed inset-0 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
    <div class="absolute inset-0 bg-slate-900/40"></div>
    <div class="relative bg-white w-full max-w-sm rounded-[2rem] shadow-2xl overflow-hidden transform transition-all p-8 text-center border border-gray-100">
        <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 ring-8 ring-red-50/50">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </div>
        <h3 class="text-xl font-black text-gray-900 tracking-tight mb-2">Clear Alerts?</h3>
        <p class="text-sm font-medium text-gray-500 mb-8 leading-relaxed">This will permanently remove all notifications from your history. This action <span class="text-red-600 font-bold">cannot be undone</span>.</p>
        <div class="flex gap-3">
            <button type="button" id="cancelClearNotifsBtn" class="flex-1 py-4 border-2 border-gray-100 rounded-2xl font-bold text-gray-500 hover:border-gray-400 hover:bg-gray-50 hover:text-gray-800 active:scale-95 transition-all duration-300 uppercase text-[10px] tracking-[0.2em]">Keep Alerts</button>
            <button type="button" id="executeClearNotifsBtn" class="flex-1 py-4 bg-red-500 rounded-2xl font-black text-white hover:bg-gray-900 shadow-lg shadow-red-100 active:scale-95 transition-all duration-300 uppercase text-[10px] tracking-[0.2em]">Clear All</button>
        </div>
    </div>
</div>

<script src="../../public/assets/js/notifications.js?v=<?= time() ?>" defer></script>