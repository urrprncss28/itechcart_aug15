<?php
session_start();
include("includes/db.connection.php");

// ✅ Check if admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// ✅ Handle Block
if (isset($_GET['block'])) {
    $block_id = intval($_GET['block']);
    $stmt = $conn->prepare("UPDATE users SET role='blocked_customer' WHERE id=? AND role='customer'");
    $stmt->bind_param("i", $block_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_users.php");
    exit();
}

// ✅ Handle Unblock
if (isset($_GET['unblock'])) {
    $unblock_id = intval($_GET['unblock']);
    $stmt = $conn->prepare("UPDATE users SET role='customer' WHERE id=? AND role='blocked_customer'");
    $stmt->bind_param("i", $unblock_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_users.php");
    exit();
}

// ✅ Handle Edit
$edit_user = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// ✅ Process Edit Form
if (isset($_POST['edit_user'])) {
    $edit_id = intval($_POST['id']);
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $role = $_POST['role']; // customer or blocked_customer

    $stmt = $conn->prepare("UPDATE users SET firstname=?, lastname=?, role=? WHERE id=?");
    $stmt->bind_param("sssi", $firstname, $lastname, $role, $edit_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_users.php");
    exit();
}

// ✅ Fetch all customers and blocked customers
$sql = "SELECT id, firstname, lastname, email, role FROM users WHERE role IN ('customer','blocked_customer') ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Manage Users - Admin</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<style>
body {
    background: linear-gradient(135deg, #fdfbfb, #ebedee);
    font-family: "Poppins", "Segoe UI", sans-serif;
    color: #333;
    min-height: 100vh;
    padding: 20px;
}

.container.layout_padding {
    padding: 40px 30px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 15px;
    box-shadow: 0 10px 35px rgba(0,0,0,0.08);
    max-width: 1000px;
    margin: auto;
}

h1.fashion_taital {
    text-align: center;
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 30px;
    font-size: 2.2rem;
}

/* Table */
table {
    width: 100%;
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}
thead {
    background: linear-gradient(135deg, #2c3e50, #4b6584);
    color: white;
    text-transform: uppercase;
    font-size: 0.85rem;
}
tbody td {
    padding: 14px 12px;
    vertical-align: middle;
    border-bottom: 1px solid #eee;
    font-size: 0.95rem;
}
tbody tr:hover {
    background-color: #fdfdfd;
    transform: translateY(-1px);
    transition: all 0.2s ease-in-out;
}

/* Buttons */
.btn, .btn-sm {
    font-size: 0.9rem;
    border-radius: 10px;
    padding: 8px 14px;
    font-weight: 600;
    text-decoration: none !important;
    color: #fff;
    border: none;
    transition: all 0.3s ease;
    
}
.btn-primary { background: linear-gradient(135deg, #4b7bec, #3867d6); }
.btn-primary:hover { background: linear-gradient(135deg, #3867d6, #4b7bec); }
.btn-danger { background: linear-gradient(135deg, #ff6b6b, #e63946); }
.btn-danger:hover { background: linear-gradient(135deg, #e63946, #ff6b6b); }
.btn-success { background: linear-gradient(135deg, #28a745, #218838); }
.btn-success:hover { background: linear-gradient(135deg, #218838, #28a745); }
.btn-secondary { background: linear-gradient(135deg, #6c757d, #495057); }
.btn-secondary:hover { background: linear-gradient(135deg, #495057, #6c757d); }


/* Responsive */
@media (max-width: 768px) {
    table, thead, tbody, th, td, tr { display: block; }
    thead tr { display: none; }
    tbody tr {
        margin-bottom: 15px;
        background: #fff;
        border-radius: 10px;
        padding: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    tbody td {
        display: flex;
        justify-content: space-between;
        padding: 10px;
    }
    tbody td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #2c3e50;
    }
}
</style>
</head>
<body>
<div class="container layout_padding">
<a href="admin_dashboard.php" class="btn btn-lg btn-primary mb-3">⬅ Back to Dashboard</a>
<h1 class="fashion_taital">Manage Users</h1>

<!-- Edit Form -->
<?php if($edit_user): ?>
<div class="mb-4">
    <h3>Edit User ID <?= $edit_user['id'] ?></h3>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $edit_user['id'] ?>">
        <div class="mb-2">
            <input type="text" name="firstname" value="<?= htmlspecialchars($edit_user['firstname']) ?>" class="form-control" placeholder="First Name" required>
        </div>
        <div class="mb-2">
            <input type="text" name="lastname" value="<?= htmlspecialchars($edit_user['lastname']) ?>" class="form-control" placeholder="Last Name" required>
        </div>
        <div class="mb-2">
            <input type="email" value="<?= htmlspecialchars($edit_user['email']) ?>" class="form-control" readonly>
        </div>
        <div class="mb-2">
            <label for="role" class="form-label">Role</label>
            <select name="role" id="role" class="form-control">
                <option value="customer" <?= $edit_user['role'] === 'customer' ? 'selected' : '' ?>>Active</option>
                <option value="blocked_customer" <?= $edit_user['role'] === 'blocked_customer' ? 'selected' : '' ?>>Blocked</option>
            </select>
        </div>
        <button type="submit" name="edit_user" class="btn btn-primary">Update User</button>
        <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php endif; ?>


<!-- Users Table -->
<!-- Users Table -->
<?php if($result->num_rows > 0): ?>
<table class="table table-striped table-bordered">
<thead>
<tr>
    <th>ID</th>
    <th>First Name</th>
    <th>Last Name</th>
    <th>Email</th>
    <th>Status</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td data-label="ID"><?= $row['id']; ?></td>
    <td data-label="First Name"><?= htmlspecialchars($row['firstname']); ?></td>
    <td data-label="Last Name"><?= htmlspecialchars($row['lastname']); ?></td>
    <td data-label="Email"><?= htmlspecialchars($row['email']); ?></td>
    <td data-label="Status">
        <?php if($row['role'] === 'blocked_customer'): ?>
            <span class="badge bg-danger">Blocked</span>
        <?php else: ?>
            <span class="badge bg-success text-white">Active</span>
        <?php endif; ?>
    </td>
    <td data-label="Actions">
        <a href="?edit=<?= $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
        <?php if($row['role'] === 'customer'): ?>
            <a href="?block=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Block this user?')">Block</a>
        <?php else: ?>
            <a href="?unblock=<?= $row['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Unblock this user?')">Unblock</a>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
<?php else: ?>
<p class="text-center">No users found.</p>
<?php endif; ?>

</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
