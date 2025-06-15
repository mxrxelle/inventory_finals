<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$con = new database();
$db = $con->opencon();

// Get all orders
$orderStmt = $db->query("SELECT * FROM orders ORDER BY order_date DESC");
$orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Orders</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #ecf0f1; padding: 40px; }
        .order {
            background: white; padding: 20px; margin-bottom: 30px; border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h2 { color: #2c3e50; }
        table {
            width: 100%; border-collapse: collapse; margin-top: 10px;
        }
        th, td {
            padding: 8px; text-align: center; border: 1px solid #ccc;
        }
        th { background-color: #2c3e50; color: white; }
        .total { text-align: right; margin-top: 10px; font-weight: bold; }
    </style>
</head>
<body>

<h2>All Orders</h2>

<?php if (count($orders) > 0): ?>
    <?php foreach ($orders as $order): ?>
        <div class="order">
            <h3>Order ID: <?= $order['order_id'] ?></h3>
            <p><strong>Date:</strong> <?= $order['order_date'] ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($order['order_status']) ?></p>
            <p><strong>Total Amount:</strong> ₱<?= number_format($order['total_amount'], 2) ?></p>

            <table>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price (₱)</th>
                    <th>Subtotal (₱)</th>
                </tr>
                <?php
                // Get items for this order
                $itemStmt = $db->prepare("SELECT p.product_name, oi.order_quantity, oi.order_price
                    FROM order_items oi
                    JOIN products p ON oi.products_id = p.products_id
                    WHERE oi.order_id = ?");
                $itemStmt->execute([$order['order_id']]);
                $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= $item['order_quantity'] ?></td>
                        <td><?= number_format($item['order_price'], 2) ?></td>
                        <td><?= number_format($item['order_quantity'] * $item['order_price'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No orders found.</p>
<?php endif; ?>

</body>
</html>
