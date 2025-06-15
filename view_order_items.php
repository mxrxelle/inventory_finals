
<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['order_id'])) {
    echo "Order ID is required.";
    exit();
}

$orderId = $_GET['order_id'];

$con = new database();
$db = $con->opencon();

// Get the specific order
$orderStmt = $db->prepare("SELECT * FROM orders WHERE order_id = ?");
$orderStmt->execute([$orderId]);
$order = $orderStmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Order not found.";
    exit();
}

// Get order items
$itemStmt = $db->prepare("SELECT p.product_name, oi.order_quantity, oi.order_price
    FROM order_items oi
    JOIN products p ON oi.products_id = p.products_id
    WHERE oi.order_id = ?");
$itemStmt->execute([$orderId]);
$items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #ecf0f1; padding: 40px; }
        .order {
            background: white; padding: 20px; border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h2, h4 { color: #2c3e50; }
        table {
            width: 100%; border-collapse: collapse; margin-top: 20px;
        }
        th, td {
            padding: 10px; text-align: center; border: 1px solid #ccc;
        }
        th {
            background-color: #2c3e50;
            color: white;
        }
        .back-btn {
            margin-top: 20px;
            display: inline-block;
            background-color: #34495e;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .back-btn:hover {
            background-color: #2c3e50;
        }
    </style>
</head>
<body>

<div class="order">
    <h2>Order #<?= htmlspecialchars($order['order_id']) ?></h2>
    <p><strong>Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($order['order_status']) ?></p>
    <p><strong>Total Amount:</strong> ر.س <?= number_format($order['total_amount'], 2) ?></p>

    <h4>Items</h4>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price (ر.س)</th>
                <th>Subtotal (ر.س)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= $item['order_quantity'] ?></td>
                    <td><?= number_format($item['order_price'], 2) ?></td>
                    <td><?= number_format($item['order_quantity'] * $item['order_price'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="orders.php" class="back-btn">Back</a>
</div>

</body>
</html>
