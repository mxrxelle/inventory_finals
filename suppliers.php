<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Allow both Admin and Inventory Staff
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'inventory_staff') {
    header("Location: login.php");
    exit();
}

$con = new database();
$stmt = $con->opencon()->query("SELECT * FROM supplier");
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Suppliers</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background-color: #ecf0f1; }

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

        h2 { margin-bottom: 10px; color: #2c3e50; }
        p { margin-bottom: 20px; color: #555; }

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

        .edit-btn { background-color: #2980b9; }
        .delete-btn { background-color: #c0392b; }
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


<!-- Main Content Area -->
<div class="main-content">
    <h2>Suppliers</h2>
    <p>Manage all your suppliers here. You can add, edit, or delete suppliers.</p>

    <a href="add_supplier.php" class="add-btn">Add New Supplier</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Phone Number</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php if (count($suppliers) > 0): ?>
            <?php foreach ($suppliers as $supplier): ?>
            <tr>
                <td><?= htmlspecialchars($supplier['supplier_id']) ?></td>
                <td><?= htmlspecialchars($supplier['supplier_name']) ?></td>
                <td><?= htmlspecialchars($supplier['supplier_phonenumber']) ?></td>
                <td><?= htmlspecialchars($supplier['supplier_email']) ?></td>
                <td>
                    <!-- Fixed: Pass 'id' instead of 'supplier_id' to match edit_supplier.php -->
                    <a href="edit_supplier.php?id=<?= $supplier['supplier_id'] ?>" class="action-btn edit-btn">Edit</a>
                    <a href="delete_supplier.php?id=<?= $supplier['supplier_id'] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this supplier?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align: center;">No suppliers found.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
