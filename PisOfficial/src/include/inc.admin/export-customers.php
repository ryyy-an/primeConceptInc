<?php
session_start();
require_once '../config.php';
require_once '../dbh.inc.php';
require_once 'admin.model.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

$filename = 'Customer_Directory_' . date('Y-m-d_H-i') . '.xls';

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$customers = get_report_customers($pdo, 5000);

// Output HTML Table
echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
echo '<head><meta charset="UTF-8"></head>';
echo '<body>';
echo '<table border="1" style="font-family: Arial, sans-serif; border-collapse: collapse;">';

// Business Header
echo '<tr>';
echo '<th colspan="7" style="font-size: 20px; font-weight: bold; text-align: center; background-color: #fce4e4; height: 40px; vertical-align: middle;">PRIME CONCEPT INC.</th>';
echo '</tr>';
echo '<tr>';
echo '<th colspan="7" style="font-size: 16px; font-weight: bold; text-align: center; height: 30px; vertical-align: middle;">CUSTOMER DIRECTORY REPORT</th>';
echo '</tr>';
echo '<tr>';
echo '<th colspan="7" style="font-size: 14px; text-align: center; height: 30px; vertical-align: middle;">Generated On: ' . date('F d, Y h:i A') . '</th>';
echo '</tr>';
echo '<tr><td colspan="7" style="border: none;"></td></tr>';

// Column Headers
echo '<tr style="font-weight: bold; text-align: center; height: 35px;">';
$thStyle = 'background-color: #dc2626; color: #ffffff;';
echo '<th width="120" style="' . $thStyle . '">Customer ID</th>';
echo '<th width="250" style="' . $thStyle . '">Name</th>';
echo '<th width="150" style="' . $thStyle . '">Contact No</th>';
echo '<th width="200" style="' . $thStyle . '">Client Type</th>';
echo '<th width="200" style="' . $thStyle . '">Gov Branch / Dept</th>';
echo '<th width="120" style="' . $thStyle . '">Total Orders</th>';
echo '<th width="150" style="' . $thStyle . '">Total Spend</th>';
echo '</tr>';

if (!empty($customers)) {
    foreach ($customers as $row) {
        echo '<tr style="text-align: center; height: 25px;">';
        echo '<td style="font-weight: bold;">CUST-' . htmlspecialchars((string)$row['id']) . '</td>';
        echo '<td style="text-align: left;">' . htmlspecialchars((string)$row['name']) . '</td>';
        echo '<td style="mso-number-format:\'\@\';">' . htmlspecialchars((string)($row['contact_no'] ?: 'N/A')) . '</td>';
        echo '<td>' . htmlspecialchars((string)$row['client_type']) . '</td>';
        echo '<td>' . htmlspecialchars((string)($row['gov_branch'] ?? 'N/A')) . '</td>';
        echo '<td>' . (int)$row['total_orders'] . '</td>';
        echo '<td style="text-align: right; font-weight: bold; color: #dc2626; mso-number-format:\'\[$₱-409\]\#\,\#\#0\.00\';">' . (float)($row['total_spend'] ?? 0) . '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="7" style="text-align: center; height: 50px;">No customer records found.</td></tr>';
}

echo '</table>';
echo '</body></html>';
exit;
