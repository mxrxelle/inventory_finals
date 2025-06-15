<?php
include 'classes/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$db = new database();
$con = $db->opencon();

$stmt = $con->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
$full_name = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #ecf0f1;
        }

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

        .main-content {
            margin-left: 220px;
            padding: 20px;
        }

        .main-content h1 {
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .add-btn {
            margin-bottom: 15px;
            display: inline-block;
            padding: 8px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .add-btn:hover {
            background: #0069d9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        a.button {
            padding: 5px 10px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        a.button:hover {
            background: #218838;
        }

        a.delete {
            background: #dc3545;
        }

        a.delete:hover {
            background: #c82333;
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
            <a href="#" style="background-color:#34495e;">Sales <span style="float:right;">&#9660;</span></a>
            <ul class="submenu">
                <li><a href="add_transaction.php">Inventory Transactions</a></li>
                <li><a href="sales.php">Sales Orders</a></li>
                <li><a href="sales_report.php">Sales Report</a></li>
            </ul>
            </li>
            <li><a href="suppliers.php">Suppliers</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <h1>Manage Users</h1>

        <a href="add_user.php" class="add-btn">Add New User</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['user_id']) ?></td>
                        <td><?= htmlspecialchars($user['first_name']) ?></td>
                        <td><?= htmlspecialchars($user['last_name']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td><?= htmlspecialchars($user['created_at']) ?></td>
                        <td>
                            <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="button">Edit</a>
                            <a href="delete_user.php?id=<?= $user['user_id'] ?>" class="button delete" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
