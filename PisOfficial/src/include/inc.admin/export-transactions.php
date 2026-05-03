<?php
session_start();
require_once '../config.php';
require_once '../dbh.inc.php';
require_once 'admin.model.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

$from = $_GET['from'] ?? null;
$to = $_GET['to'] ?? null;

$filename = 'Transaction_Report_' . date('Y-m-d_H-i') . '.xls';

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$transactions = get_report_transactions($pdo, 2000, $from, $to);

// Output HTML Table
echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
echo '<head><meta charset="UTF-8"></head>';
echo '<body>';
echo '<table border="1" style="font-family: Arial, sans-serif; border-collapse: collapse;">';

// Business Header
echo '<tr>';
echo '<th colspan="8" style="font-size: 20px; font-weight: bold; text-align: center; background-color: #fce4e4; height: 40px; vertical-align: middle;">PRIME CONCEPT INC.</th>';
echo '</tr>';
echo '<tr>';
echo '<th colspan="8" style="font-size: 16px; font-weight: bold; text-align: center; height: 30px; vertical-align: middle;">TRANSACTION REPORT</th>';
echo '</tr>';
echo '<tr>';
echo '<th colspan="8" style="font-size: 14px; text-align: center; height: 30px; vertical-align: middle;">Generated On: ' . date('F d, Y h:i A') . '</th>';
echo '</tr>';
echo '<tr><td colspan="8" style="border: none;"></td></tr>';

// Column Headers
echo '<tr style="font-weight: bold; text-align: center; height: 35px;">';
$thStyle = 'background-color: #dc2626; color: #ffffff;';
echo '<th width="120" style="' . $thStyle . '">Trans ID</th>';
echo '<th width="250" style="' . $thStyle . '">Customer Name</th>';
echo '<th width="150" style="' . $thStyle . '">Client Type</th>';
echo '<th width="150" style="' . $thStyle . '">Amount</th>';
echo '<th width="150" style="' . $thStyle . '">Payment Type</th>';
echo '<th width="150" style="' . $thStyle . '">Status</th>';
echo '<th width="120" style="' . $thStyle . '">Date</th>';
echo '<th width="100" style="' . $thStyle . '">Time</th>';
echo '</tr>';

if (!empty($transactions)) {
    foreach ($transactions as $row) {
        $date = date('Y-m-d', strtotime($row['transaction_date']));
        $time = date('h:i A', strtotime($row['transaction_date']));
        
        echo '<tr style="text-align: center; height: 25px;">';
        echo '<td style="font-weight: bold;">#' . htmlspecialchars((string)$row['trans_id']) . '</td>';
        echo '<td style="text-align: left;">' . htmlspecialchars((string)$row['customer_name']) . '</td>';
        echo '<td>' . htmlspecialchars((string)$row['client_type']) . '</td>';
        echo '<td style="text-align: right; font-weight: bold; mso-number-format:\'\[$₱-409\]\#\,\#\#0\.00\';">' . (float)$row['amount'] . '</td>';
        echo '<td>' . htmlspecialchars((string)$row['payment_type']) . '</td>';
        echo '<td style="color: ' . ($row['trans_status'] === 'Completed' ? '#16a34a' : '#ea580c') . ';">' . htmlspecialchars((string)$row['trans_status']) . '</td>';
        echo '<td>' . $date . '</td>';
        echo '<td>' . $time . '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="8" style="text-align: center; height: 50px;">No transaction records found.</td></tr>';
}

echo '</table>';
echo '</body></html>';
exit;
