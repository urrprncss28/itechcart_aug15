<?php
session_start();
include("includes/db.connection.php");

// Check if admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM orders WHERE id=?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: view_orders.php");
    exit();
}

// Handle Edit (update)
if (isset($_POST['edit_order'])) {
    $id = intval($_POST['id']);
    $fullname = $_POST['fullname'];
    $address = $_POST['address'];
    $payment_method = $_POST['payment_method'];
    $total_amount = $_POST['total_amount'];
    $shipping_fee = $_POST['shipping_fee'];
    $discount = $_POST['discount'];
    $grand_total = $_POST['grand_total'];

    $stmt = $conn->prepare("UPDATE orders SET fullname=?, address=?, payment_method=?, total_amount=?, shipping_fee=?, discount=?, grand_total=? WHERE id=?");
    $stmt->bind_param("sssddddi", $fullname, $address, $payment_method, $total_amount, $shipping_fee, $discount, $grand_total, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: view_orders.php");
    exit();
}

// If edit parameter is set, fetch order data
$edit_order = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_order = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Fetch all orders
$result = $conn->query("SELECT * FROM orders ORDER BY id DESC");
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>View Orders - Admin</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<style>
body {
    background: linear-gradient(135deg, #fdfbfb, #ebedee);
    font-family: "Poppins", "Segoe UI", sans-serif;
    color: #333;
    min-height: 100vh;
    padding: 30px 20px;
    display: flex;
    justify-content: center;
}
.container.layout_padding {
    padding: 50px 40px;
    background: rgba(255, 255, 255, 0.97);
    border-radius: 20px;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
    max-width: 1200px;
    margin: auto;
}
h1.fashion_taital {
    text-align: center;
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 40px;
    font-size: 2.4rem;
    letter-spacing: 1px;
}

/* Form styling */
form input, form select {
    border-radius: 8px;
    border: 1px solid #ccc;
    padding: 8px 12px;
    width: 100%;
    margin-bottom: 10px;
}
form button {
    border-radius: 10px;
    border: none;
    padding: 8px 18px;
    font-weight: 600;
}

/* Table styling */
table {
    width: 100%;
    border-radius: 12px;
    overflow: hidden;
    border-collapse: separate;
    border-spacing: 0;
    background: #fff;
    box-shadow: 0 6px 20px rgba(0,0,0,0.05);
}
thead {
    background: linear-gradient(135deg, #2c3e50, #4b6584);
    color: #fff;
    text-transform: uppercase;
    font-size: 0.9rem;
}
tbody td {
    padding: 12px 10px;
    vertical-align: middle;
    border-bottom: 1px solid #eee;
    font-size: 0.95rem;
}
tbody tr:hover {
    background-color: #f9f9f9;
    transform: translateY(-1px);
    transition: all 0.2s ease-in-out;
}
.btn {
    font-size: 0.9rem;           /* slightly larger for readability */
    border-radius: 12px;         /* more rounded corners */
    padding: 8px 16px;           /* more comfortable padding */
    font-weight: 600;
    text-decoration: none !important;
    color: #fff;
    border: none;
    display: inline-block;
    transition: all 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* subtle shadow */
}

.btn-primary {
    background: linear-gradient(135deg, #4b7bec, #3867d6);
}
.btn-primary:hover {
    background: linear-gradient(135deg, #3867d6, #4b7bec);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.btn-danger {
    background: linear-gradient(135deg, #ff6b6b, #e63946);
}
.btn-danger:hover {
    background: linear-gradient(135deg, #e63946, #ff6b6b);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}


/* Responsive */
@media (max-width: 768px) {
    table, thead, tbody, th, td, tr {
        display: block;
    }
    thead tr { display: none; }
    tbody tr {
        margin-bottom: 20px;
        background: #fff;
        border-radius: 12px;
        padding: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
    tbody td {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
    }
    tbody td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #2c3e50;
        width: 45%;
    }
}
</style>
</head>
<body>

<div class="container layout_padding">
    <a href="admin_dashboard.php" class="btn btn-primary mb-4">⬅ Back to Dashboard</a>
    <h1 class="fashion_taital">View Orders</h1>

    <!-- Edit Order Form -->
    <?php if($edit_order): ?>
    <div class="mb-4">
        <h4>Edit Order ID <?= $edit_order['id'] ?></h4>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $edit_order['id'] ?>">
            <input type="text" name="fullname" placeholder="Full Name" value="<?= htmlspecialchars($edit_order['fullname']) ?>" required>
            <input type="text" name="address" placeholder="Address" value="<?= htmlspecialchars($edit_order['address']) ?>" required>
            <input type="text" name="payment_method" placeholder="Payment Method" value="<?= htmlspecialchars($edit_order['payment_method']) ?>" required>
            <input type="number" step="0.01" name="total_amount" placeholder="Total Amount" value="<?= $edit_order['total_amount'] ?>" required>
            <input type="number" step="0.01" name="shipping_fee" placeholder="Shipping Fee" value="<?= $edit_order['shipping_fee'] ?>" required>
            <input type="number" step="0.01" name="discount" placeholder="Discount" value="<?= $edit_order['discount'] ?>" required>
            <input type="number" step="0.01" name="grand_total" placeholder="Grand Total" value="<?= $edit_order['grand_total'] ?>" required>
            <button type="submit" name="update_order" class="btn btn-primary">Update Order</button>
            <a href="view_orders.php" class="btn btn-danger">Cancel</a>
        </form>
    </div>
    <?php endif; ?>

    <!-- Orders Table -->
    <?php if($result->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Full Name</th>
                    <th>Address</th>
                    <th>Payment Method</th>
                    <th>Total Amount</th>
                    <th>Shipping Fee</th>
                    <th>Discount</th>
                    <th>Grand Total</th>
                    <th>Order Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td data-label="Order ID"><?= $row['id']; ?></td>
                    <td data-label="Full Name"><?= htmlspecialchars($row['fullname']); ?></td>
                    <td data-label="Address"><?= htmlspecialchars($row['address']); ?></td>
                    <td data-label="Payment Method"><?= htmlspecialchars($row['payment_method']); ?></td>
                    <td data-label="Total Amount">₱<?= number_format($row['total_amount'], 2); ?></td>
                    <td data-label="Shipping Fee">₱<?= number_format($row['shipping_fee'], 2); ?></td>
                    <td data-label="Discount">₱<?= number_format($row['discount'], 2); ?></td>
                    <td data-label="Grand Total">₱<?= number_format($row['grand_total'], 2); ?></td>
                    <td data-label="Order Date"><?= $row['order_date']; ?></td>
                    <td data-label="Actions">
                        <a href="?edit=<?= $row['id']; ?>" class="btn btn-primary mb-1">Edit</a>
                        <a href="?delete=<?= $row['id']; ?>" class="btn btn-danger mb-1" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="alert alert-warning text-center mt-3">
            No orders found.
        </div>
    <?php endif; ?>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>