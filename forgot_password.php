<?php
session_start();
include("includes/db.connection.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Generate a temporary password
        $temp_password = bin2hex(random_bytes(4)); // 8 chars
        $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);

        // Update user password
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si", $hashed_password, $user['id']);
        $stmt->execute();

        $_SESSION['success'] = "Your temporary password is: <strong>$temp_password</strong>. Use it to log in and change your password immediately.";
    } else {
        $_SESSION['error'] = "Email not found!";
    }

    header("Location: forgot_password.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password - iTech Cart</title>
<link rel="stylesheet" href="bootstrap/bootstrap-5.3.7-dist/css/bootstrap.min.css">
</head>
<body>
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="col-md-6 bg-white p-4 shadow rounded-4">
        <h3 class="text-center mb-4">Forgot Password</h3>

        <?php
        if(isset($_SESSION['error'])) { echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>'; unset($_SESSION['error']); }
        if(isset($_SESSION['success'])) { echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>'; unset($_SESSION['success']); }
        ?>

        <form method="POST">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Get Temporary Password</button>
        </form>
        <div class="mt-3 text-center">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</div>
</body>
</html>
