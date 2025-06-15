<?php
include 'classes/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$db = new database();
$con = $db->opencon();

// Fetch all categories
$categoryStmt = $con->query("SELECT * FROM Category");
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle clicked category
$selectedCategory = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// Fetch products under the selected category
$products = [];
if ($selectedCategory > 0) {
    $productStmt = $con->prepare("SELECT * FROM Products WHERE category_id = ?");
    $productStmt->execute([$selectedCategory]);
    $products = $productStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
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

        /* Category Buttons */
        .category-buttons {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .category-card {
            background: #2c3e50;
            color: white;
            padding: 30px 20px;
            border-radius: 8px;
            text-align: center;
            flex: 1 1 200px;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            transition: background 0.3s;
        }

        .category-card:hover {
            background: #34495e;
        }

        /* Product List */
        .product {
            background: white;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        /* Action Buttons */
        .action-btn {
            display: inline-block;
            padding: 5px 10px;
            margin-top: 5px;
            margin-right: 5px;
            color: white;
            border-radius: 3px;
            text-decoration: none;
            font-size: 14px;
        }
        .edit-btn { background-color: #2980b9; }
        .delete-btn { background-color: #c0392b; }
        .add-btn {
            background-color: #27ae60;
            padding: 10px 20px;
            margin-bottom: 20px;
            display: inline-block;
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
                <li><a href="sales_report.php">Sales Report</a></li>
            </ul>
            </li>
        <li><a href="suppliers.php">Suppliers</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<!-- Main Content Area -->
<div class="main-content">
    <h2>Products</h2>
    <p>Here you can view all products categorized into Oils, Lubricants, and Chemicals. Click a category below to see the products under it.</p>

    <div class="category-buttons">
        <?php foreach ($categories as $category): ?>
            <a href="products.php?category_id=<?= $category['category_id'] ?>" class="category-card">
                <?= htmlspecialchars($category['category_name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if ($selectedCategory > 0): ?>
        <h3>Products in: 
            <?php 
            foreach ($categories as $cat) {
                if ($cat['category_id'] == $selectedCategory) {
                    echo htmlspecialchars($cat['category_name']);
                    break;
                }
            }
            ?>
        </h3>

        <!-- Add Product Button -->
        <a href="add_product.php?category_id=<?= $selectedCategory ?>" class="add-btn">Add Product</a>

        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): ?>
                <div class="product">
                    <strong><?= htmlspecialchars($product['product_name']) ?></strong><br>
                    Price: ﷼<?= htmlspecialchars($product['product_price']) ?><br>
                    Stock: <?= htmlspecialchars($product['product_stock']) ?><br><br>

                    <!-- Edit and Delete Buttons -->
                    <a href="edit_product.php?product_id=<?= $product['products_id'] ?>" class="action-btn edit-btn">Edit</a>
                    <a href="delete_product.php?product_id=<?= $product['products_id'] ?>" onclick="return confirm('Are you sure you want to delete this product?');" class="action-btn delete-btn">Delete</a>
                    
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products in this category.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
