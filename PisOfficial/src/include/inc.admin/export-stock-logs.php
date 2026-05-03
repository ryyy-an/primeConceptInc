<?php
session_start();
require_once '../config.php';
require_once '../dbh.inc.php';
require_once 'admin.model.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

$type = $_GET['type'] ?? '';
$from = $_GET['from'] ?? null;
$to = $_GET['to'] ?? null;

if ($type !== 'wh' && $type !== 'sr') {
    die("Invalid export type.");
}

$title = $type === 'wh' ? 'Warehouse Activity Logs' : 'Showroom Activity Logs';
$filename = str_replace(' ', '_', $title) . '_' . date('Y-m-d_H-i') . '.xls';

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$logs = ($type === 'wh') ? get_wh_stock_logs($pdo, 1000, $from, $to) : get_sr_stock_logs($pdo, 1000, $from, $to);

// Output HTML Table
echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
echo '<head><meta charset="UTF-8"></head>';
echo '<body>';
echo '<table border="1" style="font-family: Arial, sans-serif; border-collapse: collapse;">';

// Business Header
echo '<tr>';
echo '<th colspan="6" style="font-size: 20px; font-weight: bold; text-align: center; background-color: #fce4e4; height: 40px; vertical-align: middle;">PRIME CONCEPT INC.</th>';
echo '</tr>';
echo '<tr>';
echo '<th colspan="6" style="font-size: 16px; font-weight: bold; text-align: center; height: 30px; vertical-align: middle;">' . strtoupper($title) . ' REPORT</th>';
echo '</tr>';
echo '<tr>';
echo '<th colspan="6" style="font-size: 14px; text-align: center; height: 30px; vertical-align: middle;">Generated On: ' . date('F d, Y h:i A') . '</th>';
echo '</tr>';
echo '<tr><td colspan="6" style="border: none;"></td></tr>';

// Column Headers
echo '<tr style="font-weight: bold; text-align: center; height: 35px;">';
$thStyle = 'background-color: #dc2626; color: #ffffff;';
echo '<th width="250" style="' . $thStyle . '">Product Name</th>';
echo '<th width="150" style="' . $thStyle . '">Variant</th>';
echo '<th width="120" style="' . $thStyle . '">Product Code</th>';
echo '<th width="150" style="' . $thStyle . '">Adjustment (Qty)</th>';
echo '<th width="120" style="' . $thStyle . '">Date</th>';
echo '<th width="100" style="' . $thStyle . '">Time</th>';
echo '</tr>';

if (!empty($logs)) {
    foreach ($logs as $row) {
        $date = date('Y-m-d', strtotime($row['log_date']));
        $time = date('h:i A', strtotime($row['log_date']));
        $color = $row['qty'] > 0 ? '#16a34a' : '#dc2626';
        $sign = $row['qty'] > 0 ? '+' : '';
        
        echo '<tr style="text-align: center; height: 25px;">';
        echo '<td style="text-align: left;">' . htmlspecialchars((string)$row['product_name']) . '</td>';
        echo '<td>' . htmlspecialchars((string)($row['variant_name'] ?: 'Standard')) . '</td>';
        echo '<td style="mso-number-format:\'\@\';">' . htmlspecialchars((string)($row['prod_code'] ?? 'N/A')) . '</td>';
        echo '<td style="font-weight: bold; color: ' . $color . ';">' . $sign . (int)$row['qty'] . '</td>';
        echo '<td>' . $date . '</td>';
        echo '<td>' . $time . '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="6" style="text-align: center; height: 50px;">No records found within the selected date range.</td></tr>';
}

echo '</table>';
echo '</body></html>';
exit;
