/**
 * Notification Sidebar Logic
 */

document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('notifButton');
    const sidebar = document.getElementById('notificationSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const closeBtn = document.getElementById('closeSidebar');
    const list = document.getElementById('notifList');

    if (!sidebar || !overlay || !closeBtn || !list) return;

    // Unified centralized controller path
    // Note: Since this is included in pages at different directory depths, 
    // we might need a more robust pathing strategy, but usually it's ../include/global.ctrl.php
    // from the perspective of the page.
    const ctrlPath = '../include/global.ctrl.php';

    function showSidebar() {
        overlay.classList.remove('hidden');
        setTimeout(() => overlay.classList.add('opacity-100'), 10);
        sidebar.style.transform = 'translateX(0)';
        document.body.style.overflow = 'hidden';

        // Just refresh list, mark as read happens on CLOSE
        fetchNotifications();
    }

    function hideSidebar() {
        sidebar.style.transform = 'translateX(100%)';
        overlay.classList.remove('opacity-100');
        setTimeout(() => overlay.classList.add('hidden'), 300);
        document.body.style.overflow = '';

        // Mark all as read when closed
        fetch(`${ctrlPath}?action=mark_notifs_read`)
            .then(() => fetchNotifications());
    }

    const clearAllBtn = document.getElementById('clearAllNotifsBtn');
    const markReadBtn = document.getElementById('markReadAndCloseBtn');
    const cancelClearBtn = document.getElementById('cancelClearNotifsBtn');
    const executeClearBtn = document.getElementById('executeClearNotifsBtn');

    function toggleClearModal(show) {
        const modal = document.getElementById('clearNotifModal');
        if (!modal) return;
        const container = modal.querySelector('div');
        if (show) {
            modal.classList.remove('pointer-events-none', 'opacity-0');
            if (container) container.classList.remove('scale-95');
            if (container) container.classList.add('scale-100');
        } else {
            modal.classList.add('pointer-events-none', 'opacity-0');
            if (container) container.classList.remove('scale-100');
            if (container) container.classList.add('scale-95');
        }
    }

    if (clearAllBtn) clearAllBtn.addEventListener('click', () => toggleClearModal(true));
    if (cancelClearBtn) cancelClearBtn.addEventListener('click', () => toggleClearModal(false));
    
    if (markReadBtn) {
        markReadBtn.addEventListener('click', () => {
            fetch(`${ctrlPath}?action=mark_notifs_read`)
                .then(() => {
                    fetchNotifications();
                    hideSidebar();
                });
        });
    }

    if (executeClearBtn) {
        executeClearBtn.addEventListener('click', () => {
            fetch(`${ctrlPath}?action=clear_all_notifs`)
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        renderNotifications([]); // Instant UI feedback
                        updateBadge(0);
                        toggleClearModal(false);
                        if (window.showToast) {
                            window.showToast('All notifications cleared successfully', 'success');
                        }
                    }
                });
        });
    }

    let lastSeenNotifId = 0;

    function fetchNotifications(isPolling = false) {
        fetch(`${ctrlPath}?action=get_notifications`)
            .then(res => {
                if (!res.ok) throw new Error("HTTP " + res.status);
                return res.json();
            })
            .then(res => {
                if (res && res.success) {
                    const notifs = res.notifications || [];

                    // Check for new notifications to show toast
                    if (isPolling && notifs.length > 0) {
                        const newNotifs = notifs.filter(n => parseInt(n.id) > lastSeenNotifId && n.is_read == '0');
                        newNotifs.forEach(n => {
                            if (window.showToast) {
                                // Use the message content for the toast
                                const shortMsg = n.message.split('\n')[1] || n.message.split('\n')[0];
                                window.showToast(shortMsg, 'success');
                            }
                        });
                    }

                    // Update lastSeenNotifId
                    if (notifs.length > 0) {
                        const maxId = Math.max(...notifs.map(n => parseInt(n.id)));
                        if (maxId > lastSeenNotifId) lastSeenNotifId = maxId;
                    }

                    renderNotifications(notifs);
                    updateBadge(res.unread);
                }
            })
            .catch(err => {
                if (!isPolling) console.error("Notif Error:", err);
            });
    }

    function renderNotifications(notifs) {
        if (!list) return;
        if (!notifs || notifs.length === 0) {
            list.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full text-gray-300 space-y-2 py-20">
                    <svg class="size-12 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <p class="text-[10px] font-black uppercase tracking-widest">No active alerts</p>
                </div>`;
            return;
        }

        list.innerHTML = notifs.map(n => {
            let colorClass = 'bg-blue-500';
            let tagClass = 'bg-blue-100 text-blue-600';

            if (n.type === 'low_stock') {
                colorClass = 'bg-red-500';
                tagClass = 'bg-red-100 text-red-600';
            }
            if (n.type === 'result') {
                colorClass = 'bg-green-500';
                tagClass = 'bg-green-100 text-green-600';
            }
            if (n.type === 'fulfillment') {
                colorClass = 'bg-orange-500';
                tagClass = 'bg-orange-100 text-orange-600';
            }

            const time = timeAgo(new Date(n.created_at));

            return `
                <div class="p-5 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition-all group cursor-pointer relative overflow-hidden">
                    <div class="absolute left-0 top-0 h-full w-1 ${colorClass}"></div>
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 ${tagClass} text-[9px] font-black rounded-md uppercase tracking-tighter">${n.type.replace('_',' ')}</span>
                            ${n.is_read == '0' ? '<span class="px-1.5 py-0.5 bg-red-600 text-white text-[7px] font-black rounded flex items-center justify-center uppercase tracking-tighter animate-pulse">NEW</span>' : ''}
                        </div>
                        <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">${time}</span>
                    </div>
                    <p class="text-[13px] text-gray-700 font-bold leading-relaxed whitespace-pre-line">
                        ${n.message}
                    </p>
                    <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-2 italic">From: ${n.sender_name || 'System'}</p>
                </div>
            `;
        }).join('');
    }

    function updateBadge(count) {
        const btn = document.getElementById('notifButton');
        if (!btn) return;

        let badge = document.getElementById('notifBadge');
        if (!badge) {
            btn.classList.add('relative');
            badge = document.createElement('span');
            badge.id = 'notifBadge';
            badge.className = 'absolute top-0 right-0 transform translate-x-[90%] -translate-y-1/2 size-4 bg-red-600 text-white text-[8px] font-black rounded-full flex items-center justify-center border-2 border-white shadow-sm transition-all animate-in zoom-in z-[10]';
            btn.appendChild(badge);
        }

        if (count > 0) {
            badge.textContent = count > 9 ? '9+' : count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    function timeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        let interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + "y ago";
        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + "mo ago";
        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + "d ago";
        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + "h ago";
        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + "m ago";
        return "Just now";
    }

    if (btn) btn.addEventListener('click', (e) => {
        e.preventDefault();
        showSidebar();
    });
    if (closeBtn) closeBtn.addEventListener('click', hideSidebar);
    if (overlay) overlay.addEventListener('click', hideSidebar);
    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape") hideSidebar();
    });

    // Initial full fetch on load
    fetchNotifications(false);

    // Responsive 3s poll
    setInterval(() => {
        fetchNotifications(true);
    }, 3000);
});
