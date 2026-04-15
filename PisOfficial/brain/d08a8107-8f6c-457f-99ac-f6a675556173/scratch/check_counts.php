<?php
require_once "src/include/dbh.inc.php";
try {
    echo "Products: " . $pdo->query("SELECT COUNT(*) FROM product")->fetchColumn() . "\n";
    echo "Variants: " . $pdo->query("SELECT COUNT(*) FROM product_variant")->fetchColumn() . "\n";
    echo "Orders: " . $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn() . "\n";
    echo "Pending Warehouse: " . $pdo->query("SELECT COUNT(DISTINCT o.id) FROM orders o JOIN order_items oi ON o.id = oi.order_id WHERE o.status = 'pending' AND (oi.get_from = 'WH' OR oi.get_from = 'Warehouse')")->fetchColumn() . "\n";
    echo "Pending Showroom: " . $pdo->query("SELECT COUNT(DISTINCT o.id) FROM orders o JOIN order_items oi ON o.id = oi.order_id WHERE o.status = 'pending' AND (oi.get_from = 'SR' OR oi.get_from = 'Showroom')")->fetchColumn() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
