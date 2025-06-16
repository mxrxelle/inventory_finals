<?php
session_start();
require_once('classes/database.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Allow both Admin and Inventory Staff
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'inventory_staff') {
    header("Location: login.php");
    exit();
}

$first_name = $_SESSION['first_name'] ?? 'Admin';
$last_name = $_SESSION['last_name'] ?? '';
$full_name = $first_name . ' ' . $last_name;

$con = new database();
$db = $con->opencon();

$totalProducts = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalCategories = $db->query("SELECT COUNT(DISTINCT category_id) FROM products")->fetchColumn();

$currentMonth = date("Y-m");
$stmt = $db->prepare("SELECT SUM(total_amount) FROM orders WHERE DATE_FORMAT(order_date, '%Y-%m') = :month");
$stmt->execute(['month' => $currentMonth]);
$totalSalesMonth = $stmt->fetchColumn() ?: 0;

$totalUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$recentOrders = $db->query("SELECT * FROM orders ORDER BY order_date DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$lowStockProducts = $db->query("SELECT product_name, product_stock FROM products WHERE product_stock <= 10 ORDER BY product_stock ASC")->fetchAll(PDO::FETCH_ASSOC);
$salesDataQuery = $db->query("SELECT DATE(order_date) as date, SUM(total_amount) as total FROM orders GROUP BY DATE(order_date) ORDER BY date DESC LIMIT 30");
$salesData = $salesDataQuery->fetchAll(PDO::FETCH_ASSOC);

$salesLabels = array_reverse(array_column($salesData, 'date'));
$salesTotals = array_reverse(array_column($salesData, 'total'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
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

        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin: 15px 0; }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .sidebar ul li a:hover { background-color: #0056b3; }
        .sidebar ul li.has-submenu > a::after {
            content: "\25BC";
            float: right;
            font-size: 0.7rem;
        }

        .submenu {
            list-style: none;
            padding-left: 20px;
            display: none;
        }

        .submenu.show {
            display: block;
        }

        .main-content {
            margin-left: 260px;
            padding: 40px 20px;
        }

        .main-content h1 {
            margin-bottom: 20px;
            color: #0046af;
            font-weight: 700;
        }

        .cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top:30px;
        }

        .card {
            flex: 1 1 220px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            text-align: center;
            border-top: 6px solid #ffc107;
        }

        .card h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .card p {
            font-size: 26px;
            font-weight: 700;
            margin: 0;
            color: #0046af;
        }

        .table-section {
            background: white;
            margin-top: 30px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background: #0046af;
            color: white;
        }
    </style>
</head>
<body>
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

<div class="main-content">
    <h1>👋 Welcome, <?= htmlspecialchars($full_name) ?>!</h1>

    <div class="cards">
        <div class="card">
            <h3>Total Products</h3>
            <p data-target="<?= $totalProducts ?>">0</p>
        </div>
        <div class="card">
            <h3>Total Categories</h3>
            <p data-target="<?= $totalCategories ?>">0</p>
        </div>
        <div class="card">
            <h3>Total Sales This Month</h3>
            <p data-target="<?= $totalSalesMonth ?>" class="money">₱0.00</p>
        </div>
        <div class="card">
            <h3>Total Users</h3>
            <p data-target="<?= $totalUsers ?>">0</p>
        </div>
    </div>

    <div class="table-section">
        <h3>Recent Orders</h3>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Total Amount</th>
                <th>Status</th>
            </tr>
            <?php foreach ($recentOrders as $order): ?>
            <tr>
                <td><?= $order['order_id'] ?></td>
                <td><?= $order['order_date'] ?></td>
                <td>₱<?= number_format($order['total_amount'], 2) ?></td>
                <td><?= htmlspecialchars($order['order_status']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="table-section">
        <h3>Low Stock Products (≤10)</h3>
        <table>
            <tr>
                <th>Product Name</th>
                <th>Stock Left</th>
            </tr>
            <?php foreach ($lowStockProducts as $prod): ?>
            <tr>
                <td><?= htmlspecialchars($prod['product_name']) ?></td>
                <td><?= $prod['product_stock'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="table-section">
        <h3>Sales Overview</h3>
        <canvas id="salesChart" width="400" height="150"></canvas>
    </div>
</div>

<!-- Sales Chart Script -->
<script>
const salesLabels = <?= json_encode($salesLabels) ?>;
const salesData = <?= json_encode($salesTotals) ?>;
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: salesLabels,
        datasets: [{
            label: 'Total Sales (₱)',
            data: salesData,
            backgroundColor: '#0046af',
            borderColor: '#ffc107',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

<!-- Animated Counters Script -->
<script>
document.querySelectorAll('.card p').forEach(counter => {
    const updateCount = () => {
        const target = +counter.getAttribute('data-target');
        const count = +counter.innerText.replace(/[^0-9.]/g, '');
        const increment = target / 100;

        if (count < target) {
            counter.innerText = counter.classList.contains('money')
                ? `₱${(count + increment).toFixed(2)}`
                : Math.ceil(count + increment);
            setTimeout(updateCount, 10);
        } else {
            counter.innerText = counter.classList.contains('money')
                ? `₱${target.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`
                : target;
        }
    };
    updateCount();
});
</script>

<!-- Submenu Toggle Script (Dropdown Fix) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const submenuToggles = document.querySelectorAll('.has-submenu > a');

        submenuToggles.forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault(); // Prevents default anchor click
                const submenu = this.nextElementSibling;
                submenu.classList.toggle('show'); // Toggles visibility
            });
        });
    });
</script>
</body>
</html>
