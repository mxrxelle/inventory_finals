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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
 
        body {
            background-color: #ecf0f1;
            padding: 40px;
        }
 
        h2 {
            color: #0046af;
            font-weight: 700;
            text-align: center;
            margin-bottom: 40px;
            font-size: 45px;
        }
 
        .order {
            background: white;
            padding: 25px 30px;
            margin-bottom: 40px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
            border-top: 6px solid #ffc107;
        }
 
        h3 {
            color: #0046af;
            font-size: 20px;
            margin-bottom: 15px;
            font-weight: 600;
        }
 
        p {
            margin-bottom: 8px;
            font-size: 15px;
        }
 
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }
 
        th, td {
            padding: 12px 10px;
            text-align: center;
            border: 1px solid #ccc;
            font-size: 14px;
        }
 
        th {
            background-color: #0046af;
            color: white;
        }
 
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
 
        .no-orders {
            text-align: center;
            color: #555;
            margin-top: 50px;
            font-size: 18px;
        }
 
        .back-btn {
            display: inline-block;
            margin-bottom: 30px;
            background-color: #0046af;
            color: white;
            padding: 10px 18px;
            border-radius: 6px;
            font-size: 14px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
 
        .back-btn:hover {
            background-color: #003d80;
            color: #ffc107;
        }
    </style>
</head>
<body>
 
<a class="back-btn" href="<?= ($_SESSION['role'] === 'admin') ? 'admin_dashboard.php' : 'inventory_dashboard.php'; ?>">
    ← Back to Dashboard
</a>
 
<h2>All Orders</h2>
 
<?php if (count($orders) > 0): ?>
    <?php foreach ($orders as $order): ?>
        <div class="order">
            <h3>Order ID: <?= $order['order_id'] ?></h3>
            <p><strong>Date:</strong> <?= $order['order_date'] ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($order['order_status']) ?></p>
            <p><strong>Total Amount:</strong> ₱<?= number_format($order['total_amount'], 2) ?></p>
 
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price (₱)</th>
                        <th>Subtotal (₱)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
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
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p class="no-orders">No orders found.</p>
<?php endif; ?>
 
</body>
</html>
 