<?php
session_start();
require_once '../config.php';
require_once '../dbh.inc.php';
require_once '../global.model.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

// Save as .xls for Excel to interpret the HTML table with styles
$filename = 'Product_Catalog_' . date('Y-m-d_H-i') . '.xls';

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$products = get_inventory_cards($pdo);

// Output HTML Table (Excel parses this beautifully and applies all inline styles)
echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
echo '<head><meta charset="UTF-8"></head>';
echo '<body>';
echo '<table border="1" style="font-family: Arial, sans-serif; border-collapse: collapse;">';

// Business Header
echo '<tr>';
echo '<th colspan="10" style="font-size: 20px; font-weight: bold; text-align: center; background-color: #fce4e4; height: 40px; vertical-align: middle;">PRIME CONCEPT INC.</th>';
echo '</tr>';
echo '<tr>';
echo '<th colspan="10" style="font-size: 16px; font-weight: bold; text-align: center; height: 30px; vertical-align: middle;">PRODUCT CATALOG REPORT</th>';
echo '</tr>';
echo '<tr>';
echo '<th colspan="10" style="font-size: 14px; text-align: center; height: 30px; vertical-align: middle;">Generated On: ' . date('F d, Y h:i A') . '</th>';
echo '</tr>';
echo '<tr><td colspan="10" style="border: none;"></td></tr>';

// Column Headers
// Removed background color from TR to prevent infinite color spanning in Excel
echo '<tr style="font-weight: bold; text-align: center; height: 35px;">';
$thStyle = 'background-color: #dc2626; color: #ffffff;';
echo '<th width="120" style="' . $thStyle . '">Product Code</th>';
echo '<th width="250" style="' . $thStyle . '">Product Name</th>';
echo '<th width="150" style="' . $thStyle . '">Category</th>';
echo '<th width="120" style="' . $thStyle . '">Total Variants</th>';
echo '<th width="120" style="' . $thStyle . '">Warehouse Stock</th>';
echo '<th width="120" style="' . $thStyle . '">Showroom Stock</th>';
echo '<th width="120" style="' . $thStyle . '">Overall Stock</th>';
echo '<th width="120" style="' . $thStyle . '">Base Price</th>';
echo '<th width="120" style="' . $thStyle . '">Sale Status</th>';
echo '<th width="130" style="' . $thStyle . '">Effective Price</th>';
echo '</tr>';

if (!empty($products)) {
    foreach ($products as $p) {
        $effectivePrice = $p['is_on_sale'] ? $p['price'] - ($p['price'] * ($p['discount'] / 100)) : $p['price'];
        $saleStatus = $p['is_on_sale'] ? $p['discount'] . '% OFF' : 'Regular';
        
        echo '<tr style="text-align: center; height: 25px;">';
        // Force Text format in Excel to prevent stripping leading zeros or scientific notation
        echo '<td style="mso-number-format:\'\@\';">' . htmlspecialchars((string)($p['code'] ?: 'N/A')) . '</td>';
        echo '<td style="text-align: left;">' . htmlspecialchars((string)$p['name']) . '</td>';
        echo '<td>' . htmlspecialchars((string)$p['category']) . '</td>';
        echo '<td>' . count($p['variants']) . '</td>';
        echo '<td>' . (int)$p['total_wh'] . '</td>';
        echo '<td>' . (int)$p['total_sr'] . '</td>';
        echo '<td style="font-weight: bold; color: ' . ($p['overall'] <= 5 ? '#dc2626' : '#16a34a') . ';">' . (int)$p['overall'] . '</td>';
        
        // Proper numeric currency format so Excel can sum it up
        echo '<td style="text-align: right; mso-number-format:\'\[$₱-409\]\#\,\#\#0\.00\';">' . (float)$p['price'] . '</td>';
        echo '<td>' . htmlspecialchars((string)$saleStatus) . '</td>';
        echo '<td style="text-align: right; font-weight: bold; mso-number-format:\'\[$₱-409\]\#\,\#\#0\.00\';">' . (float)$effectivePrice . '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="10" style="text-align: center; height: 50px;">No products found.</td></tr>';
}

echo '</table>';
echo '</body></html>';
exit;
