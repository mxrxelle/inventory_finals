<?php
require_once('classes/database.php');
$con = new database();

$sweetAlertConfig = "";

// Fetch categories for dropdown
$categories = $con->getAllCategories();

// Check if category_id is coming from URL (from products.php click)
$selectedCategoryId = isset($_GET['category_id']) ? $_GET['category_id'] : '';

// When form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = trim($_POST['product_name']);
    $categoryId = $_POST['category_id'];
    $productPrice = trim($_POST['product_price']);
    $productStock = trim($_POST['product_stock']);

    // Validation (simple)
    if (empty($productName) || empty($categoryId) || empty($productPrice) || empty($productStock)) {
        $sweetAlertConfig = "
            <script>
              Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fill in all fields.'
              });
            </script>";
    } else {
        // Insert to DB
        $result = $con->addProduct($productName, $categoryId, $productPrice, $productStock);
        if ($result) {
            $sweetAlertConfig = "
            <script>
              Swal.fire({
                icon: 'success',
                title: 'Product Added',
                confirmButtonText: 'OK'
              }).then(() => {
                window.location.href = 'products.php';
              });
            </script>";
        } else {
            $sweetAlertConfig = "
            <script>
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to add product.'
              });
            </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container mt-5">
  <h2>Add Product</h2>
  <form method="POST" action="">
    <div class="mb-3">
      <label for="product_name" class="form-label">Product Name</label>
      <input type="text" class="form-control" id="product_name" name="product_name" required>
    </div>

    <div class="mb-3">
      <label for="category_id" class="form-label">Category</label>
      <select class="form-select" id="category_id" name="category_id" required>
        <option value="">Select Category</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['category_id'] ?>" <?= ($cat['category_id'] == $selectedCategoryId) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['category_name']) ?>
            </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label for="product_price" class="form-label">Price</label>
      <input type="number" class="form-control" id="product_price" name="product_price" required>
    </div>

    <div class="mb-3">
      <label for="product_stock" class="form-label">Stock</label>
      <input type="number" class="form-control" id="product_stock" name="product_stock" required>
    </div>

    <button type="submit" class="btn btn-primary">Add Product</button>
  </form>
</div>

<?php echo $sweetAlertConfig; ?>
</body>
</html>
