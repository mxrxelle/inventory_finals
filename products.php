
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

// Fetch categories
$catStmt = $con->query("SELECT * FROM Category");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// Category filter
$selectedCat = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Base query for products list
$query = "SELECT * FROM Products WHERE 1=1";
$params = [];

if ($selectedCat > 0) {
    $query .= " AND category_id = ?";
    $params[] = $selectedCat;
}

if (!empty($search)) {
    $query .= " AND product_name LIKE ?";
    $params[] = '%' . $search . '%';
}

$stmt = $con->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch low stock products filtered by selected category
$lowStockQuery = "SELECT product_name, product_stock FROM Products WHERE product_stock <= 5";
$lowStockParams = [];

if ($selectedCat > 0) {
    $lowStockQuery .= " AND category_id = ?";
    $lowStockParams[] = $selectedCat;
}

$lowStockStmt = $con->prepare($lowStockQuery);
$lowStockStmt->execute($lowStockParams);
$lowStockProducts = $lowStockStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f9;
        }
        .low-stock-label {
            color: #dc3545;
            font-weight: bold;
            font-size: 0.9em;
        }
        .sidebar {
            width: 220px;
            height: 100vh;
            background-color: #2c3e50;
            color: white;
            position: fixed;
            padding: 30px 20px;
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
        .main-content {
            margin-left: 240px;
            padding: 40px 30px;
        }
        .search-box {
            max-width: 300px;
        }
        #suggestions {
            position: absolute;
            background: white;
            width: 300px;
            z-index: 1000;
            border: 1px solid #ccc;
            border-top: none;
            display: none;
        }
        #suggestions li {
            padding: 8px 10px;
            cursor: pointer;
        }
        #suggestions li:hover {
            background-color: #f1f1f1;
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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Products</h2><a href="add_product.php<?= $selectedCat > 0 ? '?category_id=' . $selectedCat : '' ?>" class="btn btn-success">
  <i class="bi bi-plus-circle me-1"></i> Add Product
</a>
    </div>

    <!-- Search and Category Filter -->
    <form method="GET" class="d-flex align-items-center mb-3 gap-3 flex-wrap position-relative">
        <input type="text" name="search" id="productSearch" value="<?= htmlspecialchars($search) ?>" class="form-control search-box" placeholder="Search products..." autocomplete="off" />
        <ul id="suggestions" class="list-group"></ul>
        <select name="category_id" class="form-select" onchange="this.form.submit()" style="max-width: 200px;">
            <option value="0">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['category_id'] ?>" <?= $selectedCat == $cat['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['category_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-outline-primary">Search</button>
    </form>

    <!-- Low Stock Dropdown -->
    <div class="mb-3">
        <button class="btn btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#lowStockList">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> Show Low Stock Products
        </button>
    </div>
    <div class="collapse mb-4" id="lowStockList">
        <div class="card card-body border border-danger">
            <h5 class="text-danger mb-3"><i class="bi bi-box-seam-fill me-1"></i> Low Stock Items</h5>
            <?php
            if (count($lowStockProducts) > 0) {
                foreach ($lowStockProducts as $prod) {
                    echo '<div class="text-danger mb-1">⚠️ ' . htmlspecialchars($prod['product_name']) . ' — Stock: ' . $prod['product_stock'] . '</div>';
                }
            } else {
                echo '<div class="text-success">✅ All products are sufficiently stocked.</div>';
            }
            ?>
        </div>
    </div>

    <!-- Products List -->
    <div class="row">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $prod): ?>
                <?php $isLow = (int)$prod['product_stock'] <= 5; ?>
                <div class="col-md-4 mb-4">
                    <div class="card <?= $isLow ? 'border border-danger' : '' ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($prod['product_name']) ?></h5>
                            <p>Price: <strong>ر.س<?= number_format($prod['product_price'], 2) ?></strong></p>
                            <p>
                                Stock: <?= $prod['product_stock'] ?>
                                <?= $isLow ? '<span class="low-stock-label">⚠️ Low</span>' : '' ?>
                            </p>
                            <a href="edit_product.php?product_id=<?= $prod['products_id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete_product.php?product_id=<?= $prod['products_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning">No products found.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("productSearch");
    const suggestionBox = document.getElementById("suggestions");

    searchInput.addEventListener("input", function () {
        const query = this.value;
        if (query.length < 1) {
            suggestionBox.style.display = "none";
            suggestionBox.innerHTML = "";
            return;
        }

        fetch(`search_suggestions.php?query=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                suggestionBox.innerHTML = "";
                if (data.length > 0) {
                    data.forEach(item => {
                        const li = document.createElement("li");
                        li.className = "list-group-item list-group-item-action";
                        li.textContent = item;
                        li.onclick = () => {
                            searchInput.value = item;
                            suggestionBox.innerHTML = "";
                            suggestionBox.style.display = "none";
                        };
                        suggestionBox.appendChild(li);
                    });
                    suggestionBox.style.display = "block";
                } else {
                    suggestionBox.style.display = "none";
                }
            });
    });

    document.addEventListener("click", (e) => {
        if (!searchInput.contains(e.target)) {
            suggestionBox.style.display = "none";
        }
    });
});
</script>
</body>
</html>
