/**
 * Admin Order Request Page Logic
 */

document.addEventListener('DOMContentLoaded', function() {
    // Progressive Table Logic
    let allRequestsData = [];
    let displayLimit = 3;
    let paginationPageSize = 5;
    let currentPage = 1;

    function fetchProductRequests() {
        const tbody = document.getElementById('productRequestsContent');
        if (!tbody) return;
        tbody.innerHTML = `<tr><td colspan="6" class="py-20 text-center"><div class="flex flex-col items-center gap-2"><div class="size-8 border-4 border-gray-100 border-t-red-600 rounded-full animate-spin"></div><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Loading requests...</p></div></td></tr>`;

        // Reusing the general report action but with All status
        fetch(`../include/inc.admin/admin.ctrl.php?action=get_orders_report&status=All`)
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    // Filter: Hidden Admin POS orders from Request review (Admin POS is pre-approved)
                    allRequestsData = response.data.filter(row => row.creator_role !== 'admin');
                    renderRequestsTable();
                }
            });
    }

    function renderRequestsTable() {
        const tbody = document.getElementById('productRequestsContent');
        const footer = document.getElementById('productRequestsFooter');
        
        if (!tbody) return;

        if (allRequestsData.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="py-20 text-center"><p class="text-[11px] font-black text-gray-300 uppercase tracking-widest">No request records found</p></td></tr>`;
            if (footer) footer.innerHTML = '';
            return;
        }

        let dataToShow = [];
        let total = allRequestsData.length;

        // Logic: If limit is 3, show first 3. If limit expanded, show pagination with page size 5.
        if (displayLimit > 3) {
            let start = (currentPage - 1) * paginationPageSize;
            let end = start + paginationPageSize;
            dataToShow = allRequestsData.slice(start, end);
        } else {
            dataToShow = allRequestsData.slice(0, Math.min(total, displayLimit));
        }

        tbody.innerHTML = dataToShow.map(row => {
            const status = row.status.toLowerCase();
            let statusClass = '';
            if (status === 'approved') statusClass = 'bg-green-50 text-green-600 border-green-100';
            else if (status === 'rejected') statusClass = 'bg-red-50 text-red-600 border-red-100';
            else if (status === 'cancelled') statusClass = 'bg-orange-50 text-orange-600 border-orange-100';
            else if (status === 'success' || status === 'completed') statusClass = 'bg-blue-50 text-blue-600 border-blue-100';
            else statusClass = 'bg-yellow-50 text-yellow-600 border-yellow-100';

            return `
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="px-6 py-5 font-bold text-red-600 font-mono text-[13px] tracking-tight">PR-${row.id}</td>
                    <td class="px-4 py-5 text-gray-800 font-medium tracking-tight">${row.requested_by || 'System'}</td>
                    <td class="px-4 py-5 text-gray-800 font-medium tracking-tight">${row.customer_name || 'N/A'}</td>
                    <td class="px-4 py-5 font-mono text-sm text-gray-600">${new Date(row.created_at).toLocaleDateString()}</td>
                    <td class="px-4 py-5 text-center">
                        <span class="inline-block px-2 py-1 text-[10px] font-bold rounded border uppercase ${statusClass}">
                            ${row.status}
                        </span>
                    </td>
                    <td class="px-6 py-5 text-center">
                        <button data-view-order="${row.id}" class="p-2 rounded-lg hover:bg-gray-200 transition-colors text-gray-400 hover:text-gray-900">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        // Render Footer Controls
        if (footer) {
            if (displayLimit === 3 && total > 3) {
                footer.innerHTML = `
                    <button data-expand-reqs class="flex items-center gap-2 text-[10px] font-black text-gray-900 uppercase tracking-widest hover:text-red-600 transition group">
                        View More (${total})
                        <svg class="size-4 group-hover:translate-y-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" /></svg>
                    </button>`;
            } else if (displayLimit > 3) {
                let totalPages = Math.ceil(total / paginationPageSize);
                let pagesHtml = '';
                for (let i = 1; i <= totalPages; i++) {
                    pagesHtml += `<button data-go-page="${i}" class="size-8 rounded-lg text-xs font-black transition ${currentPage === i ? 'bg-red-600 text-white shadow-lg' : 'text-gray-400 hover:bg-gray-100'}">${i}</button>`;
                }
                footer.innerHTML = `
                    <div class="flex flex-col items-center gap-4">
                        <button data-show-less-reqs class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-red-600 transition group flex items-center gap-2">
                            <svg class="size-4 group-hover:-translate-y-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 15l7-7 7 7" stroke-width="3" /></svg>
                            Show Less
                        </button>
                        <div class="flex items-center gap-2">${pagesHtml}</div>
                    </div>`;
            } else {
                footer.innerHTML = '';
            }
        }
    }

    // Delegation Table Controls
    document.addEventListener('click', (e) => {
        const viewBtn = e.target.closest('[data-view-order]');
        if (viewBtn) {
            const id = viewBtn.getAttribute('data-view-order');
            if (typeof openViewModal === 'function') openViewModal(id);
            return;
        }

        if (e.target.closest('[data-expand-reqs]')) {
            displayLimit = 5;
            renderRequestsTable();
            return;
        }

        if (e.target.closest('[data-show-less-reqs]')) {
            displayLimit = 3;
            currentPage = 1;
            renderRequestsTable();
            return;
        }

        const pageBtn = e.target.closest('[data-go-page]');
        if (pageBtn) {
            currentPage = parseInt(pageBtn.getAttribute('data-go-page'));
            renderRequestsTable();
            return;
        }

        // Modal Controls
        if (e.target.closest('[data-modal-close-req]')) {
            if (typeof closeReviewModal === 'function') closeReviewModal();
            return;
        }

        // Action Buttons (Approve/Reject)
        const actionBtn = e.target.closest('[data-order-action]');
        if (actionBtn) {
            const type = actionBtn.getAttribute('data-order-action');
            if (typeof handleAction === 'function') handleAction(type);
            return;
        }
    });

    // Handle Discount Type Radio Buttons
    document.addEventListener('change', (e) => {
        if (e.target.name === 'discount_type') {
            if (typeof toggleDiscountType === 'function') toggleDiscountType();
        }
    });

    // Handle Input Changes
    document.addEventListener('input', (e) => {
        if (e.target.id === 'admin-discount') {
            if (typeof calculateFinalTotal === 'function') calculateFinalTotal();
        }
        if (e.target.id === 'history-search') {
            if (typeof handleHistoryAutocomplete === 'function') handleHistoryAutocomplete(e.target);
        }
    });

    // Initial load
    fetchProductRequests();
});
