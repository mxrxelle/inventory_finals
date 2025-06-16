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
    <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
 
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
        }
 
        .sidebar {
            width: 240px;
            height: 100vh;
            background-color: rgb(0, 70, 175);
            color: white;
            position: fixed;
            padding: 20px;
        }
 
        .sidebar h2 {
            margin-bottom: 30px;
            font-size: 1.8rem;
            font-weight: 700;
            color: #ffc107;
            padding-left: 10px;
            margin-top: 30px;
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
            transition: background-color 0.3s;
        }
 
        .sidebar ul li a:hover {
            background-color: #0056b3;
        }
 
        .has-submenu > a::after {
            content: "\25BC";
            float: right;
            font-size: 0.7rem;
            transition: transform 0.3s;
        }
 
        .submenu {
            list-style: none;
            padding-left: 20px;
            display: none;
        }
 
        .has-submenu.active .submenu {
            display: block;
        }
 
        .has-submenu.active > a::after {
            transform: rotate(180deg);
        }
 
        .main-content {
            margin-left: 260px;
            padding: 40px 20px;
        }
 
        .main-content h1 {
            margin-bottom: 20px;
            color: rgb(0, 70, 175);
            font-weight: 700;
        }
 
        .add-btn {
            margin-bottom: 15px;
            display: inline-block;
            padding: 8px 15px;
            background: #ffc107;
            color: #333;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
        }
 
        .add-btn:hover {
            background: #e0a800;
            color: white;
        }
 
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
 
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
 
        th {
            background-color: rgb(0, 70, 175);
            color: white;
        }
 
        td {
            font-size: 0.95rem;
        }
 
        a.button {
            padding: 6px 12px;
            background: #198754;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.85rem;
        }
 
        a.button:hover {
            background: #157347;
        }
 
        a.delete {
            background: #dc3545;
        }
 
        a.delete:hover {
            background: #bb2d3b;
        }
    </style>
</head>
<body>
 
<!-- Sidebar -->
<div class="sidebar">
    <h2><i class="bi bi-speedometer2"></i> <?= ($_SESSION['role'] === 'admin') ? 'Admin Panel' : 'Inventory Panel'; ?></h2>
    <ul>
        <li><a href="<?= ($_SESSION['role'] === 'admin') ? 'admin_dashboard.php' : 'inventory_dashboard.php'; ?>">
            <i class="bi bi-house-door"></i> Dashboard</a>
        </li>
        
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <li><a href="users.php"><i class="bi bi-people"></i> Users</a></li>
        <?php endif; ?>

        <li><a href="products.php"><i class="bi bi-box"></i> Products</a></li>
        <li><a href="orders.php"><i class="bi bi-cart"></i> Orders</a></li>
        
        <li class="has-submenu">
            <a href="#"><i class="bi bi-receipt"></i> Sales</a>
            <ul class="submenu">
                <li><a href="add_transaction.php">Inventory Transactions</a></li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="sales_report.php">Sales Report</a></li>
                <?php endif; ?>
            </ul>
        </li>

        <li><a href="suppliers.php"><i class="bi bi-truck"></i> Suppliers</a></li>
        <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
    </ul>
</div>
 
<!-- Main Content -->
<div class="main-content">
    <h1>Manage Users</h1>
 
    <a href="add_user.php" class="add-btn"><i class="bi bi-person-plus-fill"></i> Add New User</a>
 
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
 
<!-- Submenu Toggle Script -->
<script>
    document.querySelectorAll('.has-submenu > a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            this.parentElement.classList.toggle('active');
        });
    });
</script>
 
</body>
</html>
