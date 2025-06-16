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
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
 
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }
 
    body {
      background-color: #f4f6f9;;
    }
      .sidebar {
        width: 240px;
        height: 100vh;
        background-color: #0046af;
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
        background-color: #003d80;
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
  padding: 40px 30px;
}
 
 
    h2 {
        color: #0046af;
        font-weight: 700;
        margin-bottom: 10px;
        font-size: 50px;
    }
 
    p {
      margin-bottom: 20px;
      color: #555;
    }
 
    .add-btn {
      background-color: #ffc107;
      padding: 10px 20px;
      margin-bottom: 20px;
      display: inline-block;
      color: black;
      text-decoration: none;
      border-radius: 4px;
      transition: background-color 0.3s;
      font-weight: 500;
    }
 
    .add-btn:hover {
      background-color: #e0a800;
    }
 
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
 
    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
 
    th {
      background-color: #0046af;
      color: white;
      font-weight: 600;
    }
 
    tr:hover {
      background-color: #f9f9f9;
    }
 
    .action-btn {
      display: inline-block;
      padding: 5px 12px;
      margin: 2px;
      border-radius: 4px;
      font-size: 14px;
      text-decoration: none;
      transition: 0.2s;
      font-weight: 500;
    }
 
    .view-btn {
      background-color: #0046af;
      color: white;
    }
 
    .view-btn:hover {
      background-color: #003b96;
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
            <td>₱<?= htmlspecialchars(number_format($order['total_amount'], 2)) ?></td>
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
 
<!-- Submenu toggle script -->
<script>
  document.querySelectorAll('.has-submenu > a').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      this.parentElement.classList.toggle('active');
    });
  });
</script>
 
</body>
</html>
 