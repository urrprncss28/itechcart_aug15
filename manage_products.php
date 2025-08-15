<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("includes/db.connection.php");

// Check if admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_products.php");
    exit();
}

// Handle Add
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $storage = $_POST['storage'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("INSERT INTO products (name, storage, price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $name, $storage, $price);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_products.php");
    exit();
}

// Fetch all products
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Products</title>
<link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #fdfbfb, #ebedee);
    font-family: "Poppins", "Segoe UI", sans-serif;
    color: #333;
    min-height: 100vh;
    padding: 20px;
}

/* Remove underline for all links and make consistent */
a {
    text-decoration: none !important;
    color: inherit;
    transition: color 0.3s ease;
}
a:hover {
    color: inherit; /* Keeps color consistent on hover */
}

/* Form Fields Styling */
.form-label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 6px;
}

.form-control {
    border-radius: 8px;
    border: 1px solid #ccc;
    padding: 10px 12px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #1e3c72;
    box-shadow: 0 0 6px rgba(30, 60, 114, 0.3);
    outline: none;
}

/* Row spacing */
.row.g-3 > [class*='col-'] {
    margin-bottom: 20px;
}

/* Responsive */
@media (max-width: 768px) {
    .row.g-3 > [class*='col-'] {
        margin-bottom: 15px;
    }
}

/* Container */
.container.layout_padding {
    padding: 40px 30px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 15px;
    box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
    max-width: 900px;
    margin: auto;
}

/* Page Title */
h1.fashion_taital {
    text-align: center;
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 30px;
    font-size: 2.2rem;
}

/* Form Card */
.card {
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
}
.card-body {
    padding: 25px;
}

/* Product List */
.product-item {
    padding: 20px 25px;
    margin-bottom: 15px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.2s ease-in-out;
}
.product-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

.product-title {
    font-weight: 700;
    font-size: 1.3rem;
    color: #2c3e50;
}
.product-storage, .product-price {
    font-size: 1rem;
    margin-top: 3px;
    color: #555;
}

/* Buttons */
/* Buttons - unified style */
.btn, .btn-lg, .btn-warning, .btn-danger, .btn-secondary {
    background: linear-gradient(135deg, #4b7bec, #3867d6);
    border: none;
    color: #fff;
    font-weight: 600;
    border-radius: 10px;
    padding: 10px 20px;
    font-size: 1rem;
    text-decoration: none !important;
    transition: all 0.3s ease;
    display: inline-block;
}

.btn:hover, 
.btn-lg:hover, 
.btn-warning:hover, 
.btn-danger:hover, 
.btn-secondary:hover {
    background: linear-gradient(135deg, #3867d6, #4b7bec);
    transform: translateY(-2px);
}

/* Small adjustments for button spacing */
.btn + .btn, 
.btn-lg + .btn, 
.btn-warning + .btn, 
.btn-danger + .btn, 
.btn-secondary + .btn {
    margin-left: 10px;
}


/* Responsive adjustments */
@media (max-width: 768px) {
    .product-item {
        flex-direction: column;
        align-items: flex-start;
    }
    .product-item > div:last-child {
        margin-top: 10px;
    }
}

</style>

</head>
<body>

<div class="container layout_padding">
<a href="admin_dashboard.php" class="btn btn-primary mb-3">⬅ Back to Dashboard</a>
<h1 class="fashion_taital">Manage Products</h1>

<!-- Add/Edit Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="post" action="">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Storage</label>
                    <input type="text" name="storage" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Price (₱)</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="add" class="btn btn-lg w-100">Add</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Products List -->
<?php
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
while ($product = $result->fetch_assoc()):
?>
<div class="product-item d-flex justify-content-between align-items-center">
    <div>
        <div class="product-title"><?php echo $product['name']; ?></div>
        <div class="product-storage">Storage: <?php echo $product['storage']; ?></div>
        <div class="product-price">Price: ₱<?php echo number_format($product['price'], 2); ?></div>
    </div>
    <div>
        <a href="?delete=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
    </div>
</div>
<?php endwhile; ?>

</div>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
