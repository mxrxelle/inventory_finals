<?php
session_start();
require_once('classes/database.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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

// Total Sales Today
$dateToday = date("Y-m-d");
$stmt = $db->prepare("SELECT SUM(total_amount) FROM orders WHERE DATE(order_date) = :today");
$stmt->execute(['today' => $dateToday]);
$totalSalesToday = $stmt->fetchColumn();
$totalSalesToday = $totalSalesToday ? $totalSalesToday : 0;

// Total Users
$totalUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Recent Orders
$recentOrders = $db->query("SELECT * FROM orders ORDER BY order_date DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Low Stock Products
$lowStockProducts = $db->query("SELECT product_name, product_stock FROM products WHERE product_stock <= 10 ORDER BY product_stock ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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
            position: relative;
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

        /* Submenu */
        .has-submenu > a::after {
            content: '▼';
            float: right;
            font-size: 12px;
        }

        .submenu {
            display: none;
            list-style: none;
            padding-left: 15px;
        }

        .has-submenu:hover .submenu {
            display: block;
        }

        .submenu li a {
            background-color: #34495e;
            margin: 5px 0;
            padding: 8px;
            font-size: 14px;
            border-radius: 4px;
        }

        .submenu li a:hover {
            background-color: #3d566e;
        }

        /* Main Content */
        .main-content {
            margin-left: 220px; /* To prevent overlap with sidebar */
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
    <h2>Admin Panel</h2>
    <ul>
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="users.php">Users</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="orders.php">Orders</a></li>
        <li class="has-submenu">
            <a href="#">Sales</a>
            <ul class="submenu">
                <li><a href="add_transaction.php">Inventory Transactions</a></li>
                <li><a href="sales_report.php">Sales Report</a></li>
            </ul>
        </li>
        <li><a href="suppliers.php">Suppliers</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    <h1>Welcome, Admin!</h1>

    <!-- Dashboard Summary Cards -->
    <div class="cards">
        <div class="card">
            <h3>Total Products</h3>
            <p>100</p> <!-- Example Value -->
        </div>
        <div class="card">
            <h3>Total Categories</h3>
            <p>10</p> <!-- Example Value -->
        </div>
        <div class="card">
            <h3>Total Sales Today</h3>
            <p>₱5000.00</p> <!-- Example Value -->
        </div>
        <div class="card">
            <h3>Total Users</h3>
            <p>20</p> <!-- Example Value -->
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="table-section">
        <h3>Recent Orders</h3>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Total Amount</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>1</td>
                <td>2025-06-15</td>
                <td>₱1000.00</td>
                <td>Completed</td>
            </tr>
        </table>
    </div>

    <!-- Low Stock Table -->
    <div class="table-section">
        <h3>Low Stock Products (≤10)</h3>
        <table>
            <tr>
                <th>Product Name</th>
                <th>Stock Left</th>
            </tr>
            <tr>
                <td>Lubricant X</td>
                <td>5</td>
            </tr>
        </table>
    </div>
</div>

</body>
</html>
