<?php
session_start();
include("includes/db.connection.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Admin can log in without password check
        if ($row['role'] === 'admin' || password_verify($password, $row['password'])) {
            // ✅ Store user info in session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['last_name'] = $row['last_name'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];

            // Redirect based on role
            if ($row['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: home.php"); // customer goes to user.php
            }
            exit();
        } else {
            $_SESSION['error'] = "Invalid password!";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "No user found with that email!";
        header("Location: login.php");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <link rel="stylesheet" href="bootstrap/bootstrap-5.3.7-dist/css/bootstrap.min.css">


    <link rel="stylesheet" href="login.css">

    <title>Login</title>
</head>
<body>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 bg-white p-4 shadow rounded-4">
            <div class="justify-content-center">
                <div class="content-center align-items-center">
                    <div class="header-text mb-4">
                        <h2 class="mb-4 text-center">Login</h2>
                    </div>


                    <form method="POST" action="login.php">
    <div class="input-group mb-3">
        <input type="text" name="email" class="form-control form-control-lg bg-light fs-6" placeholder="Email" required>
    </div>
    <div class="input-group mb-2">
        <input type="password" name="password" class="form-control form-control-lg bg-light fs-6" placeholder="Password" required>
    </div>
    <!-- Forgot Password link -->
    <div class="mb-3 text-end">
        <a href="forgot_password.php" class="text-decoration-none" style="font-size: 0.9rem; color: #007bff;">Forgot Password?</a>
    </div>

    <div class="input-group mb-3">
        <button type="submit" class="btn btn-lg w-100 fs-6" style="background-color: #f26522; color: white; border: none;">Login</button>
    </div>

    <div class="row">
        <small>Don't have an account? <a href="register.php">Sign Up</a></small>
    </div>
</form>


                </div>
            </div> 

        </div>
    </div>

    <script src="bootstrap/bootstrap-5.3.7-dist/js/bootstrap.min.js"></script>
</body>
</html>