<?php
session_start();
require_once('classes/database.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Redirect Admin to their own dashboard if they access this by mistake
if ($_SESSION['role'] === 'admin') {
    header('Location: admin_dashboard.php');
    exit();
}

// Only allow inventory_staff role
if ($_SESSION['role'] !== 'inventory_staff') {
    header('Location: login.php');
    exit();
}

$full_name = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];

$con = new database();
$db = $con->opencon();

// Total Products
$totalProducts = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();

// Total Categories
$totalCategories = $db->query("SELECT COUNT(DISTINCT category_id) FROM products")->fetchColumn();

// Low Stock Products
$lowStockProducts = $db->query("SELECT product_name, product_stock FROM products WHERE product_stock <= 10 ORDER BY product_stock ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Staff Dashboard</title>
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #ecf0f1;
        }

        /* Sidebar */
        .sidebar {
            width: 200px;
            height: 100vh;
            background-color: #2c3e50;
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            padding: 20px;
            overflow-y: auto;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
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
            padding: 10px;
            border-radius: 4px;
            background-color: #2c3e50;
            transition: background-color 0.3s;
        }

        .sidebar ul li a:hover {
            background-color: #34495e;
        }

        /* Main Content */
        .main-content {
            margin-left: 220px; 
            padding: 20px;
        }

        .main-content h1 {
            margin-bottom: 20px;
            color: #2c3e50;
        }

        /* Dashboard Cards */
        .cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .card {
            background: white;
            padding: 20px;
            flex: 1 1 200px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .card h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .card p {
            font-size: 24px;
            font-weight: bold;
            color: #2980b9;
        }

        /* Table Section */
        .table-section {
            background: white;
            margin-top: 30px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        th {
            background: #2c3e50;
            color: white;
        }
    </style>
</head>
<body>

<!-- Sidebar Navigation -->
<div class="sidebar">
    <h2><?= ($_SESSION['role'] === 'admin') ? 'Admin Panel' : 'Inventory Panel'; ?></h2>
    <ul>
        <li><a href="<?= ($_SESSION['role'] === 'admin') ? 'admin_dashboard.php' : 'inventory_dashboard.php'; ?>">Dashboard</a></li>
        
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <li><a href="users.php">Users</a></li>
        <?php endif; ?>
        
        <li><a href="products.php">Products</a></li>
        <li><a href="orders.php">Orders</a></li>
        
        <li class="has-submenu">
            <a href="#" style="background-color:#34495e;">Sales <span style="float:right;">&#9660;</span></a>
            <ul class="submenu">
                <li><a href="add_transaction.php">Inventory Transactions</a></li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="sales_report.php">Sales Report</a></li>
                <?php endif; ?>
            </ul>
        </li>
        
        <li><a href="suppliers.php">Suppliers</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>


<!-- Main Content -->
<div class="main-content">
    <h1>Welcome, <?= htmlspecialchars($full_name) ?>!</h1>

    <!-- Dashboard Summary Cards -->
    <div class="cards">
        <div class="card">
            <h3>Total Products</h3>
            <p><?= $totalProducts ?></p> 
        </div>
        <div class="card">
            <h3>Total Categories</h3>
            <p><?= $totalCategories ?></p> 
        </div>
    </div>

    <!-- Low Stock Table -->
    <div class="table-section">
        <h3>Low Stock Products (≤10)</h3>
        <table>
            <tr>
                <th>Product Name</th>
                <th>Stock Left</th>
            </tr>
            <?php foreach ($lowStockProducts as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['product_name']) ?></td>
                <td><?= $product['product_stock'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

</body>
</html>
