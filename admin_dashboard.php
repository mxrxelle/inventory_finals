<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$full_name = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
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

        /* Body */
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
            padding: 20px;
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
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="users.php">Users</a></li>
            <li><a href="category.php">Category</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="sales.php">Sales</a></li>
            <li><a href="suppliers.php">Suppliers</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <h1>Welcome, Admin!</h1>

        <!-- Dashboard Summary Cards -->
        <div class="cards">
            <div class="card">
                <h3>Total Products</h3>
                <p>0</p> <!-- Placeholder -->
            </div>
            <div class="card">
                <h3>Total Categories</h3>
                <p>0</p> <!-- Placeholder -->
            </div>
            <div class="card">
                <h3>Total Sales Today</h3>
                <p>₱0.00</p> <!-- Placeholder -->
            </div>
            <div class="card">
                <h3>Total Users</h3>
                <p>0</p> <!-- Placeholder -->
            </div>
        </div>
    </div>

</body>
</html>
