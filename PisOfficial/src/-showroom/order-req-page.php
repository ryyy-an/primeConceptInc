<?php

declare(strict_types=1);

require_once '../include/config.php';
require_once '../include/dbh.inc.php';
require_once '../include/inc.showroom/sr.model.php';

/** @var PDO $pdo */
if (!isset($pdo) || !($pdo instanceof PDO)) {
    die('Database connection not established.');
}

if (isset($_SESSION['user_id'])) {
    $userId = (int) $_SESSION['user_id'];
    $username = htmlspecialchars($_SESSION['username']);
    $role = htmlspecialchars($_SESSION['role']);

    // Fetch total cart items for notification badge
    require_once '../include/global.model.php';
    $cartItemsCount = count(get_cart_items($pdo, $userId));
    $totalCartItems = $cartItemsCount;

    // Fetch requests specific to this showroom user
    $requests = fetch_requests($pdo, $userId);

    // Fetch recent activities for the notification dropdown
    $activities = get_recent_activities($pdo, 5);

    // Fetch counts for the stats cards
    $totalProducts = $pdo->query("SELECT COUNT(DISTINCT p.id) FROM products p JOIN product_variant pv ON p.id = pv.prod_id WHERE p.is_deleted = 0")->fetchColumn();
    $totalTransactions = $pdo->prepare("SELECT COUNT(*) FROM transactions t JOIN orders o ON t.order_id = o.id WHERE o.created_by = ?");
    $totalTransactions->execute([$userId]);
    $totalTransactionsCount = $totalTransactions->fetchColumn();

    $pendingRequests = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE created_by = ? AND status IN ('For Review', 'Pending', 'Approved')");
    $pendingRequests->execute([$userId]);
    $pendingRequestsCount = $pendingRequests->fetchColumn();
} else {
    header("Location: ../../public/index.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prime-In-Sync | Order Requests</title>
    <link rel="stylesheet" href="../output.css">
    <script src="../../public/assets/js/global.js?v=1.2" defer></script>
    <script src="../../public/assets/js/order.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php include '../include/toast.php'; ?>

    <style>
        /* Shrink entire UI by 10% */
        html {
            zoom: 90%;
        }
    </style>

</head>

<body class="bg-white flex flex-col gap-6 text-gray-800 font-sans py-5 px-[100px]">
    <header
        class="sticky top-0 z-40 flex h-[100px] items-center justify-between border-b border-gray-200 px-6 bg-white container">

        <div class="flex container">
            <a href="#" class="flex items-center gap-4">
                <div class="h-full w-20">
                    <img src="../../public/assets/img/primeLogo.ico" alt="Prime Concept Logo"
                        class="h-full object-contain" />
                </div>
                <div>
                    <h1 class="text-2xl font-semibold text-red-600">Prime-In-Sync</h1>
                    <h4 class="text-base text-gray-500">Welcome, <?= h($username) ?></h4>
                </div>
            </a>
        </div>

        <!-- Right: Role + Icons -->
        <div class="flex items-center gap-4 justify-end w-1/2">
            <div class="rounded-md bg-red-100 px-3 py-1 text-sm text-red-600 font-medium">
                <?= h(ucfirst($role)) ?> User
            </div>

            <div class="relative inline-block">
                <button id="notifButton"
                    class="flex items-center justify-center border border-gray-300 size-9 rounded-lg hover:bg-red-100 transition active:scale-95">
                    <svg class="size-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" />
                    </svg>
                </button>

                <div id="notifDropdown"
                    class="hidden absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-xl shadow-2xl z-50 overflow-hidden transition-all duration-300">
                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-tight">Recent Activity</h3>
                    </div>

                    <div id="notifList" class="overflow-y-auto transition-all duration-500 ease-in-out"
                        style="max-height: 200px;">
                        <div class="divide-y divide-gray-50 bg-white">
                            <?php if (empty($activities)): ?>
                                <div class="px-4 py-6 text-center text-gray-400 text-xs italic">
                                    No recent activities found.
                                </div>
                            <?php else: ?>
                                <?php foreach ($activities as $act): ?>
                                    <div class="px-4 py-3 hover:bg-blue-50/50 transition-colors">
                                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">
                                            <?= $act['type'] === 'request' ? 'New Request' : 'Order Approved' ?>
                                        </p>
                                        <div class="text-xs text-gray-700 leading-relaxed">
                                            <?php if ($act['type'] === 'request'): ?>
                                                <span
                                                    class="font-bold text-gray-900"><?= h($act['fname'] . ' ' . $act['lname']) ?></span>
                                                placed a request for <span
                                                    class="text-green-600 font-semibold"><?= $act['item_count'] ?> items</span>.
                                            <?php else: ?>
                                                Order <span class="font-bold text-gray-900">#<?= h($act['ref_id']) ?></span>
                                                for <span
                                                    class="text-blue-600 font-semibold"><?= h($act['fname'] . ' ' . $act['lname']) ?></span>
                                                has been processed.
                                            <?php endif; ?>
                                        </div>
                                        <span
                                            class="text-[10px] text-gray-400 mt-2 block italic"><?= format_activity_time($act['timestamp']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <button id="viewAllBtn"
                        class="block w-full py-3 text-center text-[11px] font-extrabold text-blue-600 bg-gray-50 hover:bg-blue-100 border-t border-gray-100 transition-all uppercase tracking-widest">
                        View All Notifications
                    </button>
                </div>
            </div>

            <!-- Logout -->
            <a href="javascript:void(0)" onclick="toggleLogoutModal(true)"
                class="flex items-center gap-2 border border-gray-300 px-4 h-9 rounded-lg hover:bg-red-50 hover:border-red-200 transition group">
                <svg class="size-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                </svg>
                <span class="text-sm text-red-600 font-medium">Logout</span>
            </a>

            <?php include '../include/logout-modal.php'; ?>

        </div>
    </header>

    <section class="px-6 py-4">
        <div class="grid grid-cols-[repeat(3,400px)] justify-center gap-5">
            <!-- Card 1 -->
            <div class="flex flex-col justify-between bg-white border border-gray-300 rounded-lg shadow h-[180px] p-6">
                <div class="text-sm uppercase tracking-wide text-gray-500">Available Products</div>
                <div class="text-4xl font-bold text-gray-800"><?= number_format((float) $totalProducts) ?></div>
                <div class="text-sm text-gray-600">Total products in the catalog.</div>
            </div>

            <!-- Card 2 -->
            <div class="flex flex-col justify-between bg-white border border-gray-300 rounded-lg shadow h-[180px] p-6">
                <div class="text-sm uppercase tracking-wide text-gray-500">Total Transactions</div>
                <div class="text-4xl font-bold text-gray-800"><?= number_format((float) $totalTransactionsCount) ?>
                </div>
                <div class="text-sm text-gray-600">Your completed transactions.</div>
            </div>

            <!-- Card 3 -->
            <div class="flex flex-col justify-between bg-white border border-gray-300 rounded-lg shadow h-[180px] p-6">
                <div class="text-sm uppercase tracking-wide text-gray-500">Active Request</div>
                <div class="text-4xl font-bold text-red-600"><?= number_format((float) $pendingRequestsCount) ?></div>
                <div class="text-sm text-gray-600">Your active order requests.</div>
            </div>

        </div>
    </section>

    <nav class="px-5 flex justify-center">
        <div class="max-w-7xl w-full">
            <ul class="grid grid-cols-3 bg-gray-100 rounded-3xl h-12 shadow-sm px-5 items-center gap-2">

                <!-- Order Products -->
                <li>
                    <a href="home-page.php"
                        class="relative flex items-center justify-center gap-2 h-10 px-4 text-gray-700 font-medium hover:text-red-600 transition">
                        <svg class="w-5 h-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 
                     1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 
                     1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 
                     1 5.513 7.5h12.974c.576 0 1.059.435 1.119 
                     1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 
                     .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 
                     1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        <span>Order Products</span>
                        <span id="cart-badge-showroom"
                            class="cart-badge <?= $totalCartItems > 0 ? 'flex' : 'hidden' ?> absolute top-0 right-2 bg-red-600 text-white text-[10px] font-black w-5 h-5 items-center justify-center rounded-full shadow-md border-2 border-white transform translate-x-1/2 -translate-y-1/2 transition-all duration-300">
                            <?= $totalCartItems ?>
                        </span>
                    </a>
                </li>

                <!-- Transaction History -->
                <li>
                    <a href="transaction-history.php"
                        class="flex items-center justify-center gap-2 h-10 px-4 text-gray-700 font-medium hover:text-red-600 transition">
                        <svg class="w-5 h-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3M3.22302 14C4.13247 18.008 7.71683 21 
                     12 21c4.9706 0 9-4.0294 9-9 0-4.97056-4.0294-9-9-9-3.72916 
                     0-6.92858 2.26806-8.29409 5.5M7 9H3V5" />
                        </svg>
                        <span>Transaction History</span>
                    </a>
                </li>

                <!-- My Order Requests -->
                <li>
                    <a href="order-req-page.php"
                        class="flex items-center justify-center gap-2 h-10 px-4 text-red-600 font-semibold border-b-2 border-red-600">
                        <svg class="w-5 h-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 
                     4.242 0 1.172 1.025 1.172 2.687 0 
                     3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 
                     1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 
                     1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                        </svg>
                        <span>My Order Requests</span>
                    </a>
                </li>

            </ul>
        </div>
    </nav>

    <!-- Product Request -->
    <div class="flex flex-center w-full">
        <div class="border border-gray-300 rounded-2xl p-12 gap w-[1250px]">

            <div>
                <h2 class="text-2xl font-semibold mb-2">Product Requests</h2>
                <p class="text-gray-600 mb-6 font-medium">Review and manage your product requests below.</p>
            </div>

            <div
                class="w-full overflow-hidden border border-gray-100 rounded-2xl shadow-sm bg-white font-sans text-gray-900">
                <table class="w-full text-md text-left text-gray-700 table-auto border-collapse">
                    <thead
                        class="bg-gray-50 text-gray-400 text-[9px] font-bold uppercase tracking-widest border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 w-[15%]">Request ID</th>
                            <th class="px-4 py-4 w-[20%]">Requested By</th>
                            <th class="px-4 py-4 w-[25%]">Customer Name</th>
                            <th class="px-4 py-4 w-[15%]">Date Requested</th>
                            <th class="px-4 py-4 text-center w-[15%]">Current Status</th>
                            <th class="px-6 py-4 text-center w-[10%]">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-50">
                        <?php if (empty($requests)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-15 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div
                                            class="size-16 bg-gray-50 rounded-full flex items-center justify-center border border-gray-100">
                                            <svg class="size-8 text-gray-300" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-400 font-medium italic mb-10">No pending requests at the
                                            moment.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($requests as $row):
                                // Match Admin Status Logic
                                $status = strtolower($row['status'] ?? 'pending');

                                // Dynamic Status Coloring
                                if ($status === 'approved') {
                                    $statusClass = 'bg-green-50 text-green-600 border border-green-100';
                                } else if ($status === 'rejected') {
                                    $statusClass = 'bg-red-50 text-red-600 border border-red-100';
                                } else if ($status === 'cancelled') {
                                    $statusClass = 'bg-orange-50 text-orange-600 border border-orange-100';
                                } else if ($status === 'success') {
                                    $statusClass = 'bg-blue-50 text-blue-600 border border-blue-100';
                                } else {
                                    $statusClass = 'bg-yellow-50 text-yellow-600 border border-yellow-100';
                                }
                            ?>
                                <tr class="hover:bg-gray-50 transition-colors group" data-pr-no="<?= h($row['pr_no']) ?>">
                                    <td class="px-6 py-5 font-bold text-gray-900">
                                        <?= h($row['pr_no']) ?>
                                    </td>
                                    <td class="px-4 py-5 text-gray-800 font-medium tracking-tight">
                                        <?= h($row['full_name'] ?? 'N/A') ?>
                                    </td>
                                    <td class="px-4 py-5 text-gray-800 font-medium tracking-tight">
                                        <?= h($row['customer_name'] ?: ($row['temp_customer_name'] ?? 'N/A')) ?>
                                    </td>
                                    <td class="px-4 py-5 font-mono text-sm text-gray-600">
                                        <?= date('m/d/Y', strtotime($row['date'])) ?>
                                    </td>
                                    <td class="px-4 py-5 text-center">
                                        <span
                                            class="inline-block px-2 py-1 text-[10px] font-bold rounded <?= $statusClass ?> uppercase">
                                            <?= h($row['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick="openRequestInfoModal(<?= h(json_encode($row)) ?>)"
                                                class="p-2 rounded-lg hover:bg-gray-200 transition-colors cursor-pointer text-gray-400 hover:text-gray-900"
                                                title="View Details">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                    stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- View Modal -->
            <div id="requestInfoModal"
                class="fixed inset-0 z-50 hidden items-center justify-center p-4 transition-all duration-300">
                <div class="absolute inset-0 bg-slate-900/60"></div>

                <div class="relative bg-white rounded-2xl shadow-2xl h-[85vh] max-w-6xl w-full overflow-hidden transform transition-all scale-95 opacity-0 duration-300 flex flex-col border border-gray-200"
                    id="requestInfoBox">

                    <div
                        class="flex-none px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-white z-20">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-8 bg-red-600 rounded-full"></div>
                            <h3 class="text-xl font-black text-gray-900 tracking-tight uppercase">Request Details</h3>
                        </div>
                        <button onclick="closeRequestInfoModal()"
                            class="p-2 hover:bg-gray-100 rounded-xl transition-all group">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 text-gray-400 group-hover:text-gray-600" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 flex flex-row overflow-hidden">

                        <!-- Left Column: Order Stats & Base Info -->
                        <div
                            class="flex-1 border-r border-gray-100 overflow-y-auto p-8 space-y-8 bg-gray-50/10 custom-scrollbar">

                            <div class="max-w-xl py-4 px-2 space-y-5 bg-white">

                                <div class="border-b border-gray-100 pb-3 mb-6">
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Ref:</span>
                                        <span id="modal-id" class="text-sm font-mono font-bold text-gray-900">--</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-x-16">
                                    <div class="flex flex-col border-l-2 border-gray-100 pl-3">
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-tight mb-0.5">Requested By</label>
                                        <p id="modal-by" class="text-sm font-bold text-gray-800">--</p>
                                    </div>
                                    <div class="flex flex-col border-l-2 border-gray-100 pl-3">
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-tight mb-0.5">Date Filed</label>
                                        <p id="modal-date" class="text-sm font-medium text-gray-600">--</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-x-16 pt-2">
                                    <div class="flex flex-col border-l-2 border-gray-100 pl-3">
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-tight mb-0.5">Customer Name</label>
                                        <p id="modal-customer" class="text-sm font-black text-gray-900 tracking-tight">--</p>
                                    </div>
                                    <div class="flex flex-col border-l-2 border-gray-100 pl-3">
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-tight mb-0.5">Status</label>
                                        <div id="modal-status">
                                            <span id="modal-status-badge" class="text-xs font-black text-yellow-600 tracking-tighter uppercase">
                                                ● Pending
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="pt-8 border-t border-gray-100 space-y-6">
                                <div id="modal-discount-section"
                                    class="p-4 rounded-xl border border-green-100 bg-green-50/30 flex justify-between items-center">
                                    <span class="text-[11px] font-bold text-green-700 uppercase tracking-widest">Applied
                                        Discount:</span>
                                    <span id="modal-discount-amount" class="text-xl font-bold text-green-800">₱
                                        0.00</span>
                                </div>

                                <div id="modal-remarks-section" class="space-y-3">
                                    <label
                                        class="text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">Admin
                                        Comment/Remarks</label>
                                    <div id="remarks-bubble"
                                        class="p-5 rounded-xl bg-blue-50/30 border border-blue-100 min-h-[100px]">
                                        <p id="modal-remarks-text"
                                            class="text-sm italic text-blue-900/70 leading-relaxed font-medium">
                                            No remarks provided yet.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-8 border-t border-gray-100">
                                <div id="modal-cancel-container" class="w-full"></div>
                            </div>
                        </div>

                        <!-- Middle Column: Finalize Transaction -->
                        <div id="modal-right-column" class="flex-[1.5] flex flex-col bg-white overflow-hidden">
                            <div
                                class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/20 shrink-0">
                                <div id="modal-right-header-container">
                                    <h2 id="modal-right-header-text"
                                        class="text-2xl font-black text-gray-900 tracking-tight uppercase">Finalize
                                        Transaction</h2>
                                    <p id="modal-right-sub-header"
                                        class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1">
                                        Order
                                        Processing & Billing</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span id="customerBadge"
                                        class="px-3 py-1 text-[10px] font-black bg-blue-50 text-blue-600 rounded-full border border-blue-100 uppercase italic transition-all">Linked
                                        Account</span>
                                </div>
                            </div>

                            <div class="flex-1 overflow-y-auto p-8 space-y-10 custom-scrollbar">

                                <section id="modal-order-items-section" class="space-y-4">
                                    <div class="flex items-center justify-between px-1">
                                        <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.3em]">
                                            Current Order</h3>
                                        <span id="summaryItemCount"
                                            class="bg-black text-white text-[10px] px-3 py-1 rounded-full font-black italic">0
                                            Items</span>
                                    </div>

                                    <div class="bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm">
                                        <table class="w-full text-left border-collapse">
                                            <thead>
                                                <tr class="bg-gray-50/50 border-b border-gray-100">
                                                    <th
                                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">
                                                        Source</th>
                                                    <th
                                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">
                                                        Product Details</th>
                                                    <th
                                                        class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase">
                                                        Qty</th>
                                                    <th
                                                        class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase">
                                                        Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody id="summaryTableBody" class="divide-y divide-gray-100">
                                                <!-- Dynamic Content -->
                                            </tbody>
                                        </table>
                                    </div>
                                </section>

                                <section id="modal-client-section" class="space-y-4 border-t border-gray-100 pt-6">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Client
                                            Information</h3>
                                    </div>

                                    <div class="grid grid-cols-3 gap-4">
                                        <div class="col-span-2 space-y-1.5">
                                            <label class="text-xs font-semibold text-gray-700 ml-1">Full Name /
                                                Authorized Person</label>
                                            <div class="relative">
                                                <input type="text" id="clientName" autocomplete="off"
                                                    oninput="handleCustomerSearch(this.value)"
                                                    placeholder="Enter name..."
                                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none font-semibold">

                                                <!-- Suggestions Dropdown -->
                                                <div id="customerSuggestions"
                                                    class="absolute z-50 left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden hidden max-h-48 overflow-y-auto custom-scrollbar">
                                                    <!-- suggestions appended here -->
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-span-2 space-y-1.5">
                                            <label class="text-xs font-semibold text-gray-700 ml-1">Contact
                                                Number</label>
                                            <input type="text" id="clientContact" placeholder="09XX XXX XXXX"
                                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                        </div>

                                        <div class="col-span-1 space-y-1.5">
                                            <label class="text-xs font-semibold text-gray-700 ml-1">Discount
                                                (%)</label>
                                            <div class="relative">
                                                <input type="number" id="adminDiscount" placeholder="0"
                                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none pr-8 font-bold">
                                                <span class="absolute right-4 top-3 text-gray-400 font-bold">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section id="modal-shipping-section" class="space-y-4 border-t border-gray-100">
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Shipping
                                        Details</h3>
                                    <div class="grid grid-cols-1 gap-4">
                                        <div class="space-y-1.5">
                                            <label class="text-xs font-semibold text-gray-700 ml-1">Shipping
                                                Mode</label>
                                            <select id="shippingMode" onchange="toggleAddress(this.value)"
                                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all cursor-pointer">
                                                <option value="pickup">Store Pickup</option>
                                                <option value="delivery">Delivery Service</option>
                                            </select>
                                        </div>

                                        <div id="deliveryAddressSection"
                                            class="hidden animate-in fade-in slide-in-from-top-2">
                                            <label class="text-xs font-semibold text-orange-600 ml-1">Exact Delivery
                                                Address</label>
                                            <textarea id="deliveryAddress" rows="2"
                                                placeholder="House/Bldg No., Street, Brgy, City..."
                                                class="w-full mt-1 bg-orange-50/30 border border-orange-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 outline-none resize-none font-medium"></textarea>
                                        </div>
                                    </div>
                                </section>

                                <section id="modal-payment-section" class="space-y-4 border-t border-gray-100">
                                    <div class="flex items-center justify-between px-1">
                                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Payment
                                            Configuration</h3>
                                        <span id="typeBadge"
                                            class="text-[9px] font-black px-2 py-0.5 rounded bg-blue-100 text-blue-600 uppercase">Standard
                                            Sale</span>
                                    </div>

                                    <!-- Simplified: Only Full Payment allowed in Showroom -->
                                    <input type="hidden" id="transactionType" value="full">
                                    <input type="hidden" id="interestRate" value="0">
                                    <input type="hidden" id="installmentTerm" value="1">

                                        <div class="col-span-2 md:col-span-1 space-y-1.5">
                                            <label class="text-xs font-semibold text-gray-700 ml-1">Payment
                                                Method</label>
                                            <select id="paymentMethod"
                                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none font-semibold text-gray-900">
                                                <option value="eWallet">E-Wallet</option>
                                                <option value="gcash">GCash</option>
                                                <option value="bankTransfer">Bank Transfer</option>
                                            </select>
                                        </div>

                                        <div class="col-span-2 md:col-span-1 space-y-1.5">
                                            <label class="text-xs font-semibold text-gray-700 ml-1">Reference
                                                No.</label>
                                            <input type="text" id="paymentRef" placeholder="Ref ID / Trace #"
                                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none font-medium">
                                        </div>

                                        <div class="col-span-2 space-y-1.5">
                                            <label id="amountLabel"
                                                class="text-xs font-semibold text-gray-700 ml-1">Final Amount
                                                Paid</label>
                                            <div class="relative">
                                                <span
                                                    class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-blue-600">₱</span>
                                                <input type="number" id="amountPaid" readonly
                                                    class="w-full bg-gray-100 border border-gray-200 rounded-xl pl-8 pr-4 py-4 text-lg outline-none font-black text-gray-700 cursor-not-allowed">
                                            </div>
                                        </div>

                                        <div class="col-span-2 space-y-1.5">
                                            <label class="text-xs font-semibold text-gray-700 ml-1">Payment Remarks /
                                                Bank Details</label>

                                            <textarea id="paymentRemarks" rows="2"
                                                placeholder="e.g. Paid via BDO Transfer, Check No. XXXX"
                                                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none font-medium resize-none transition-all"></textarea>
                                            <br>
                                            <br>
                                            <div id="modal-grand-total-section"
                                                class="p-8 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                                                <div>
                                                    <p
                                                        class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 leading-none">
                                                        Grand Total</p>
                                                    <h1 id="summaryGrandTotal"
                                                        class="text-3xl font-black text-gray-900 tracking-tighter leading-none">
                                                        ₱0.00</h1>
                                                </div>
                                            </div>


                                        </div>

                                        <input type="hidden" id="calcBalance" value="0">
                                    </div>
                                </section>

                            </div>


                        </div>
                    </div>

                    <div class="p-8 flex justify-between items-center gap-4 bg-white border-t border-gray-100 z-20">
                        <button onclick="closeRequestInfoModal()"
                            class="flex-1 justify-center bg-black hover:bg-zinc-800 text-white rounded-xl py-4 px-6 font-black text-[11px] uppercase tracking-[0.2em] transition-all shadow-xl shadow-gray-200 active:scale-95 flex items-center gap-2">
                            <span>Close Window</span>
                        </button>

                        <div id="hidden-order-id" class="hidden"></div>

                        <button id="showroomCompleteSaleBtn" onclick="handleShowroomCompleteSale()"
                            class="flex-1 justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-xl py-4 px-6 font-black text-[11px] uppercase tracking-[0.2em] transition-all shadow-xl shadow-blue-100 active:scale-95 flex items-center gap-2">
                            <span>Complete Sale</span>
                        </button>
                    </div>

                </div>
            </div>

            <script>
                // --- VIEW MODAL LOGIC ---
                function openRequestInfoModal(req) {
                    document.body.style.overflow = 'hidden';
                    const modal = document.getElementById('requestInfoModal');
                    const box = document.getElementById('requestInfoBox');

                    // Set basic info
                    document.getElementById('modal-id').textContent = req.pr_no;
                    document.getElementById('modal-by').textContent = req.full_name || 'N/A';
                    document.getElementById('modal-customer').textContent = req.customer_name || req.temp_customer_name || 'N/A';
                    document.getElementById('modal-date').textContent = new Date(req.date).toLocaleDateString();

                    // Status Badge
                    const status = (req.status || 'pending').toLowerCase();
                    let statusClass = 'bg-yellow-50 text-yellow-600 border-yellow-100 ring-yellow-50';

                    if (status === 'approved') {
                        statusClass = 'bg-green-50 text-green-600 border-green-100 ring-green-50';
                    } else if (status === 'rejected') {
                        statusClass = 'bg-red-50 text-red-600 border-red-100 ring-red-50';
                    } else if (status === 'cancelled') {
                        statusClass = 'bg-orange-50 text-orange-600 border-orange-100 ring-orange-50';
                    } else if (status === 'success') {
                        statusClass = 'bg-blue-50 text-blue-600 border-blue-100 ring-blue-50';
                    } else {
                        statusClass = 'bg-gray-50 text-gray-400 border-gray-100 ring-gray-100';
                    }

                    const badge = document.getElementById('modal-status-badge');
                    badge.className = `text-[9px] font-black px-2 py-0.5 rounded-md uppercase border tracking-wider ${statusClass}`;
                    badge.textContent = req.status;

                    // UI Reorganization based on status
                    const isForReview = status === 'for review';
                    const isCancelled = status === 'cancelled';
                    const isRejected = status === 'rejected';
                    const isSuccess = status === 'success';
                    const isApproved = status === 'approved';

                    // Group all non-checkout/read-only statuses
                    const isReadOnlyView = isForReview || isCancelled || isRejected || isSuccess;

                    const rightHeader = document.getElementById('modal-right-header-text');
                    const rightSubHeader = document.getElementById('modal-right-sub-header');
                    const clientSection = document.getElementById('modal-client-section');
                    const shippingSection = document.getElementById('modal-shipping-section');
                    const paymentSection = document.getElementById('modal-payment-section');
                    const totalSection = document.getElementById('modal-grand-total-section');
                    const completeBtn = document.getElementById('showroomCompleteSaleBtn');

                    if (isReadOnlyView) {
                        // Change Right Column to "Order Summary" mode
                        rightHeader.textContent = "Order Summary";
                        if (rightSubHeader) rightSubHeader.classList.add('hidden');
                        if (clientSection) clientSection.classList.add('hidden');
                        if (shippingSection) shippingSection.classList.add('hidden');
                        if (paymentSection) paymentSection.classList.add('hidden');
                        if (totalSection) totalSection.classList.add('hidden');

                        if (isForReview) {
                            // Show "Cancel Request" instead of "Complete Sale"
                            completeBtn.classList.remove('hidden');
                            completeBtn.disabled = false;
                            completeBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'shadow-blue-100', 'opacity-50', 'cursor-not-allowed');
                            completeBtn.classList.add('bg-red-600', 'hover:bg-red-700', 'shadow-red-100');
                            completeBtn.innerHTML = `<span>Cancel Request</span>`;
                            completeBtn.onclick = () => cancelRequest(req.pr_no);
                        } else {
                            // Cancelled: Hide the button entirely
                            completeBtn.classList.add('hidden');
                        }
                    } else {
                        // Restore "Finalize Transaction" mode
                        rightHeader.textContent = "Finalize Transaction";
                        if (rightSubHeader) rightSubHeader.classList.remove('hidden');
                        if (clientSection) clientSection.classList.remove('hidden');
                        if (shippingSection) shippingSection.classList.remove('hidden');
                        if (paymentSection) paymentSection.classList.remove('hidden');
                        if (totalSection) totalSection.classList.remove('hidden');

                        // Reset button to "Complete Sale" defaults
                        completeBtn.classList.remove('hidden');
                        completeBtn.classList.remove('bg-red-600', 'hover:bg-red-700', 'shadow-red-100');
                        completeBtn.classList.add('bg-blue-600', 'hover:bg-blue-700', 'shadow-blue-100');
                        completeBtn.onclick = handleShowroomCompleteSale;

                        if (!isApproved) {
                            completeBtn.disabled = true;
                            completeBtn.classList.add('opacity-50', 'cursor-not-allowed');
                            completeBtn.innerHTML = `<span>Awaiting Admin Approval</span>`;
                        } else {
                            completeBtn.disabled = false;
                            completeBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                            completeBtn.innerHTML = `<span>Complete Sale</span>`;
                        }
                    }

                    // Cancel Button visibility (Left Column - may keep it or hide if redundant)
                    // The user wanted the footer button to be the Cancel button for 'For Review'.
                    const cancelContainer = document.getElementById('modal-cancel-container');
                    if (status === 'pending') { // Only keep it here for Pending if not For Review
                        cancelContainer.innerHTML = `
                            <button onclick="cancelRequest(${req.pr_no})" 
                                class="w-full py-4 bg-white border-2 border-red-50 text-red-600 font-bold rounded-2xl hover:bg-red-50 transition-all active:scale-95 uppercase text-[11px] tracking-widest shadow-sm">
                                Cancel Request
                            </button>
                        `;
                    } else {
                        cancelContainer.innerHTML = '';
                    }

                    // Discount & Remarks
                    document.getElementById('modal-discount-amount').textContent = `₱ ${parseFloat(req.discount || 0).toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
                    document.getElementById('modal-remarks-text').textContent = req.comment || 'No remarks provided yet.';

                    // Trigger summary detail population
                    populateRequestSummary(req);

                    // Show Modal
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    setTimeout(() => {
                        box.classList.remove('scale-95', 'opacity-0');
                    }, 10);
                }

                function closeRequestInfoModal() {
                    document.body.style.overflow = '';
                    const modal = document.getElementById('requestInfoModal');
                    const box = document.getElementById('requestInfoBox');
                    box.classList.add('scale-95', 'opacity-0');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }, 300);
                }

                function cancelRequest(prNo) {
                    window.showCustomConfirm?.(`Cancel Request #${prNo}?`, () => {
                        fetch('../include/inc.showroom/sr.ctrl.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: `action=cancel_request&pr_no=${prNo}`
                            })
                            .then(r => r.json())
                            .then(res => {
                                if (res.success) {
                                    showToast('Request cancelled successfully!', 'success');
                                    location.reload();
                                } else {
                                    showToast(res.error || 'Failed to cancel request.', 'error');
                                }
                            });
                    });
                }

                // --- PROCEED MODAL LOGIC ---
                /**
                 * Populates the right-hand "Finalize Transaction" UI with data from the specific request
                 */
                async function populateRequestSummary(req) {
                    const tableBody = document.getElementById("summaryTableBody");
                    const grandTotalEl = document.getElementById("summaryGrandTotal");
                    const itemCountEl = document.getElementById("summaryItemCount");
                    const amountPaidInput = document.getElementById("amountPaid");
                    const hiddenOrderId = document.getElementById("hidden-order-id");

                    if (!tableBody || !grandTotalEl || !itemCountEl) return;

                    // Reset view
                    tableBody.innerHTML = `<tr><td colspan="4" class="px-6 py-10 text-center font-bold text-gray-400 opacity-50 italic">Loading order details...</td></tr>`;

                    try {
                        // Fetch items for this specific order
                        const response = await fetch(`../include/inc.showroom/sr.ctrl.php?action=get_items&pr_no=${req.pr_no}`);
                        const data = await response.json();

                        if (data.success && data.items) {
                            tableBody.innerHTML = "";
                            let total = 0;
                            let count = 0;

                            if (data.items.length === 0) {
                                tableBody.innerHTML = `<tr><td colspan="4" class="px-6 py-10 text-center font-bold text-gray-400 opacity-50 italic uppercase">No items found for this request.</td></tr>`;
                            } else {
                                data.items.forEach((item) => {
                                    const qty = parseInt(item.quantity);
                                    const price = parseFloat(item.price);
                                    const subtotal = price * qty;
                                    total += subtotal;
                                    count += qty;

                                    const srcBadge = item.location === "SR" ? "bg-red-600" : "bg-gray-800";

                                    const html = `
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="${srcBadge} text-white text-[9px] px-2 py-1 rounded-md font-black shadow-sm uppercase italic">${item.location}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <h1 class="text-sm font-black text-gray-900 leading-tight">${item.prod_name}</h1>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">${item.variant} &bull; ${item.location === "SR" ? "Showroom" : "Warehouse"}</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-sm font-black text-gray-600">${qty}x</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm font-black text-gray-900">₱${subtotal.toLocaleString(undefined, { minimumFractionDigits: 2 })}</span>
                                    </td>
                                </tr>
                            `;
                                    tableBody.insertAdjacentHTML("beforeend", html);
                                });
                            }

                            // Update Global for order.js calculations
                            window.currentCheckoutTotal = total;

                            if (hiddenOrderId) hiddenOrderId.innerText = req.id;
                            itemCountEl.innerText = `${count} Items`;
                            grandTotalEl.innerText = `₱${total.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;

                            // Fill Client Info dynamically
                            const info = data.details;
                            if (document.getElementById("clientName")) document.getElementById("clientName").value = info.customer_name || "";
                            if (document.getElementById("clientContact")) document.getElementById("clientContact").value = info.contact_no || "";
                            if (document.getElementById("adminDiscount")) document.getElementById("adminDiscount").value = info.admin_discount || 0;
                            if (document.getElementById("shippingMode")) document.getElementById("shippingMode").value = info.shipping_type || "pickup";
                            if (document.getElementById("deliveryAddress")) document.getElementById("deliveryAddress").value = info.delivery_address || "";

                            // Adjust UI based on initial data
                            if (typeof toggleAddress === "function") toggleAddress(info.shipping_type || "pickup");

                             // Auto-fill payment amount for full payment
                             if (amountPaidInput) {
                                 amountPaidInput.value = total.toFixed(2);
                             }

                             // Initial calculation (minimal for full payment)
                             const totalWithInterestHidden = document.getElementById("totalWithInterest");
                             if (totalWithInterestHidden) totalWithInterestHidden.value = total.toFixed(2);
                        } else {
                            tableBody.innerHTML = `<tr><td colspan="4" class="px-6 py-10 text-center font-bold text-red-500">Failed to load request items.</td></tr>`;
                        }
                    } catch (err) {
                        console.error(err);
                        tableBody.innerHTML = `<tr><td colspan="4" class="px-6 py-10 text-center font-bold text-red-500">Error fetching data.</td></tr>`;
                    }
                }

                /**
                 * Combined Finalization logic for Showroom Request
                 */
                async function handleShowroomCompleteSale() {
                    const btn = document.getElementById("showroomCompleteSaleBtn");
                    const orderId = document.getElementById("hidden-order-id")?.innerText;

                    if (!btn || btn.disabled || !orderId) return;

                    const payload = {
                        action: "finalize_order",
                        order_id: orderId,
                        customer_name: document.getElementById("clientName")?.value.trim(),
                        clientType: 'Private / Individual',
                        govBranch: null,
                        contact_no: document.getElementById("clientContact")?.value.trim(),
                        adminDiscount: document.getElementById("adminDiscount")?.value || 0,
                        order_type: document.getElementById("shippingMode")?.value,
                        address: document.getElementById("deliveryAddress")?.value.trim(),
                        transactionType: 'full',
                        interestRate: 0,
                        installmentTerm: 1,
                        paymentMethod: document.getElementById("paymentMethod")?.value,
                        paymentRef: document.getElementById("paymentRef")?.value.trim(),
                        amountPaid: document.getElementById("amountPaid")?.value || 0,
                        paymentRemarks: document.getElementById("paymentRemarks")?.value.trim(),
                        totalWithInterest: document.getElementById("amountPaid")?.value || 0,
                        balance: 0,
                    };

                    if (!payload.customer_name) {
                        Swal.fire('Required', 'Customer name is required.', 'warning');
                        return;
                    }

                    Swal.fire({
                        title: 'Finalize Order?',
                        text: "This will record the transaction and update inventory.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#2563eb',
                        confirmButtonText: 'Yes, Process Sale'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            btn.disabled = true;
                            btn.innerHTML = `<span class="animate-pulse">Processing...</span>`;

                            const formData = new FormData();
                            for (const key in payload) formData.append(key, payload[key]);

                            try {
                                const res = await fetch("../include/inc.showroom/sr.ctrl.php", {
                                    method: "POST",
                                    body: formData
                                });
                                const result = await res.json();
                                if (result.success) {
                                    Swal.fire('Success', 'Transaction completed!', 'success').then(() => window.location.reload());
                                } else {
                                    Swal.fire('Failed', result.message || "Could not finalize.", 'error');
                                    btn.disabled = false;
                                    btn.innerText = "Complete Sale";
                                }
                            } catch (err) {
                                console.error(err);
                                btn.disabled = false;
                                btn.innerText = "Complete Sale";
                            }
                        }
                    });
                }

                // --- DEPRECATED PROCEED MODAL LOGIC ---
                function openProceedModal(prNo, customerName) {
                    document.body.style.overflow = 'hidden';
                    document.getElementById('display-pr-no').textContent = prNo;
                    document.getElementById('proceed-order-id').value = prNo;
                    document.getElementById('proceed-customer-name').value = customerName;

                    const modal = document.getElementById('proceedModal');
                    const box = document.getElementById('proceedModalBox');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    setTimeout(() => {
                        box.classList.remove('scale-95', 'opacity-0');
                    }, 10);
                }

                function closeProceedModal() {
                    document.body.style.overflow = '';
                    const modal = document.getElementById('proceedModal');
                    const box = document.getElementById('proceedModalBox');
                    box.classList.add('scale-95', 'opacity-0');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }, 200);
                }

                // --- GLOBAL INITIALIZATION ---
                document.addEventListener('DOMContentLoaded', () => {
                    if (typeof updateCartBadgeCount === 'function') updateCartBadgeCount();
                });
            </script>

            <style>
                .custom-scrollbar::-webkit-scrollbar {
                    width: 6px;
                }

                .custom-scrollbar::-webkit-scrollbar-track {
                    background: transparent;
                }

                .custom-scrollbar::-webkit-scrollbar-thumb {
                    background-color: #d1d5db;
                    border-radius: 10px;
                    border: 2px solid transparent;
                }

                .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                    background-color: #9ca3af;
                }

                label:has(input:checked) {
                    background-color: white !important;
                    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
                    color: #dc2626 !important;
                    border-color: #e5e7eb !important;
                }
            </style>

</body>

</html>