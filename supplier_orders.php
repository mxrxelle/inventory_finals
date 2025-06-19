<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id']) || 
    ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'inventory_staff')) {
    header("Location: login.php");
    exit();
}

$con = new database();
$db = $con->opencon();

$stmt = $db->query("SELECT so.*, s.supplier_name FROM supplier_orders so
                    JOIN supplier s ON so.supplier_id = s.supplier_id
                    ORDER BY so.order_date DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Supplier Orders</title>
    <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="main-content" style="margin-left: 240px; padding: 30px;">
    <h2>Supplier Orders</h2>
    <a href="admin_dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>
    <a href="add_supplier_order.php" class="btn btn-warning mb-3">Add New Order</a>
    
    <table class="table table-bordered">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Supplier</th>
                <th>Order Date</th>
                <th>Expected Delivery</th>
                <th>Total Cost</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order['supplier_order_id'] ?></td>
                <td><?= htmlspecialchars($order['supplier_name']) ?></td>
                <td><?= $order['order_date'] ?></td>
                <td><?= $order['expected_delivery_date'] ?></td>
                <td>₱<?= number_format($order['total_cost'], 2) ?></td>
                <td><?= $order['order_status'] ?></td>
                <td>
                    <a href="edit_supplier_order.php?id=<?= $order['supplier_order_id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="delete_supplier_order.php?id=<?= $order['supplier_order_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this order?');">Delete</a>
                    <?php if ($order['order_status'] == 'Pending'): ?>
                        <a href="confirm_order.php?id=<?= $order['supplier_order_id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Confirm this order?');">Confirm</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (count($orders) === 0): ?>
            <tr><td colspan="7" class="text-center">No orders found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- SweetAlert for feedback -->
<?php if (isset($_GET['status'])): ?>
<script>
    let status = "<?= $_GET['status'] ?>";

    switch (status) {
        case 'added':
            Swal.fire('Order Added!', 'The supplier order was successfully created.', 'success');
            break;
        case 'edited':
            Swal.fire('Order Updated!', 'The supplier order was successfully updated.', 'info');
            break;
        case 'deleted':
            Swal.fire('Order Deleted!', 'The supplier order was removed.', 'error');
            break;
        case 'confirmed':
            Swal.fire('Order Confirmed!', 'The supplier order is now marked as Approved.', 'success');
            break;
    }
</script>
<?php endif; ?>

</body>
</html>
