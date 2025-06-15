<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$db = new database();
$con = $db->opencon();

$sql = "SELECT o.*, u.username 
        FROM orders o 
        JOIN users u ON o.user_id = u.user_id 
        ORDER BY o.order_date DESC";
$stmt = $con->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background-color: #ecf0f1; }

        .sidebar {
            width: 220px;
            height: 100vh;
            background-color: #2c3e50;
            color: white;
            position: fixed;
            padding: 30px 20px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 22px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .sidebar ul li a:hover {
            background-color: #34495e;
        }

        .sidebar ul li ul.submenu {
            margin-top: 5px;
            padding-left: 15px;
        }

        .sidebar ul li ul.submenu li {
            margin: 10px 0;
        }

        .main-content {
            margin-left: 240px;
            padding: 40px 30px;
        }

        h2 {
            margin-bottom: 10px;
            color: #2c3e50;
        }

        p {
            margin-bottom: 20px;
            color: #555;
        }

        .add-btn {
            background-color: #27ae60;
            padding: 10px 20px;
            margin-bottom: 20px;
            display: inline-block;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .add-btn:hover {
            background-color: #219150;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .action-btn {
            display: inline-block;
            padding: 5px 10px;
            margin: 2px;
            color: white;
            border-radius: 3px;
            text-decoration: none;
            font-size: 14px;
        }

        .view-btn {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
        <li><a href="admin_dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
        <li><a href="users.php"><i class="bi bi-people me-2"></i>Users</a></li>
        <li><a href="products.php"><i class="bi bi-box-seam me-2"></i>Products</a></li>
        <li><a href="orders.php" style="background-color:#34495e;"><i class="bi bi-cart me-2"></i>Orders</a></li>
        <li>
            <a href="#"><i class="bi bi-bar-chart me-2"></i>Sales <span style="float:right;">▼</span></a>
            <ul class="submenu ps-3 mt-2">
                <li><a href="add_transaction.php">Inventory Transactions</a></li>
                <li><a href="sales_report.php">Sales Report</a></li>
            </ul>
        </li>
        <li><a href="suppliers.php"><i class="bi bi-truck me-2"></i>Suppliers</a></li>
        <li><a href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    <h2>Orders</h2>
    <p>View all sales orders placed by staff here.</p>

    <a href="create_order.php" class="add-btn">Create New Order</a>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Placed By</th>
                <th>Order Date</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>View Items</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($orders) > 0): ?>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['order_id']) ?></td>
                    <td><?= htmlspecialchars($order['username']) ?></td>
                    <td><?= htmlspecialchars($order['order_date']) ?></td>
                    <td>ر.س <?= htmlspecialchars(number_format($order['total_amount'], 2)) ?></td>
                    <td><?= htmlspecialchars($order['order_status']) ?></td>
                    <td>
                        <a href="view_order_items.php?order_id=<?= $order['order_id'] ?>" class="action-btn view-btn">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align: center;">No orders found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>