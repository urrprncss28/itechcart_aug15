<?php
session_start();
include("includes/db.connection.php");

// ✅ Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Get logged-in user's details
$sql = "SELECT firstname, lastname, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// ✅ Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Base update query (without password)
    if (!empty($new_password)) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET firstname=?, lastname=?, email=?, password_hash=? WHERE id=?";
            $stmt_update = $conn->prepare($update_sql);
            $stmt_update->bind_param("ssssi", $firstname, $lastname, $email, $hashed_password, $user_id);
        } else {
            $error_message = "❌ Passwords do not match.";
        }
    } else {
        $update_sql = "UPDATE users SET firstname=?, lastname=?, email=? WHERE id=?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("sssi", $firstname, $lastname, $email, $user_id);
    }

    if (isset($stmt_update) && $stmt_update->execute()) {
        $success_message = "✅ Profile updated successfully!";
        // Refresh data
        $sql = "SELECT firstname, lastname, email FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    } elseif (isset($stmt_update)) {
        $error_message = "❌ Failed to update profile.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Profile</title>
<style>
    body {
        background: linear-gradient(135deg, #fdfbfb, #ebedee);
        font-family: "Poppins", "Segoe UI", sans-serif;
        color: #333;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }
    .container.layout_padding {
        padding: 50px 40px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
        max-width: 500px;
        width: 100%;
    }
    h1.fashion_taital {
        text-align: center;
        color: #2c3e50;
        font-weight: 700;
        margin-bottom: 5px;
        font-size: 2rem;
    }
    p.subheading {
        text-align: center;
        color: #666;
        margin-bottom: 30px;
        font-size: 1rem;
    }
    label {
        display: block;
        font-weight: 600;
        margin-top: 20px;
        margin-bottom: 5px;
    }
    input {
        width: 100%;
        padding: 12px 15px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 1rem;
        box-sizing: border-box;
    }
    .btn-lg {
        background: linear-gradient(135deg, #ff914d, #ff6f3c);
        border: none;
        font-size: 1rem;
        padding: 12px;
        border-radius: 8px;
        color: white;
        cursor: pointer;
        margin-top: 25px;
        width: 100%;
        font-weight: 600;
    }
    .btn-lg:hover {
        background: linear-gradient(135deg, #ff6f3c, #ff914d);
    }
    .message {
        text-align: center;
        padding: 10px;
        border-radius: 6px;
        margin-top: 15px;
        font-size: 0.95rem;
    }
    .success { background: #d4edda; color: #155724; }
    .error { background: #f8d7da; color: #721c24; }
</style>
</head>
<body>

<div class="container layout_padding">
    <div style="text-align:left; margin-bottom:10px;">
    <a href="home.php" style="text-decoration:none; color:#2c3e50; font-weight:bold; font-size:1rem;">
        &#8592; Back to Shop
    </a>
</div>

    <h1 class="fashion_taital"><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></h1>
    <p class="subheading">Full Name</p>

    <?php if (!empty($success_message)) echo "<div class='message success'>$success_message</div>"; ?>
    <?php if (!empty($error_message)) echo "<div class='message error'>$error_message</div>"; ?>

    <form method="POST">
        <label>First name</label>
        <input type="text" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>

        <label>Last name</label>
        <input type="text" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label>New Password (leave blank to keep current)</label>
        <input type="password" name="new_password">

        <label>Confirm New Password</label>
        <input type="password" name="confirm_password">

        <button type="submit" class="btn-lg">Save Changes</button>
    </form>
    <div style="text-align:center; margin-top:15px;">
    <form method="POST" action="logout.php">
        <button type="submit" class="btn-lg" style="background: linear-gradient(135deg, #440cecff, #8338ec);">
            Logout
        </button>
    </form>
</div>

</div>

</body>
</html>
