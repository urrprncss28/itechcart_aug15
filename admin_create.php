<?php
include("includes/db.connection.php");

// === Admin Details ===
$first_name = "Admin";
$last_name  = "User";
$username   = "admin"; // You can change this username
$email      = "admin@example.com";
$password_plain = getenv("ADMIN_PASSWORD") ?: "admin123"; // Use env var or fallback
$role       = "admin";

// === Check if Admin Exists ===
$sql_check = "SELECT id FROM users WHERE email = ? OR username = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ss", $email, $username);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    echo "⚠️ Admin account already exists (email or username is taken).<br>";
    echo "No changes made.<br>";
    $stmt_check->close();
    $conn->close();
    exit();
}
$stmt_check->close();

// === Hash Password ===
$hashed_password = password_hash($password_plain, PASSWORD_DEFAULT);

// === Insert Admin ===
$sql_insert = "INSERT INTO users (first_name, last_name, username, email, password, role) VALUES (?, ?, ?, ?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("ssssss", $first_name, $last_name, $username, $email, $hashed_password, $role);

if ($stmt_insert->execute()) {
    echo "✅ Admin account created successfully!<br>";
    echo "👤 Username: $username<br>";
    echo "📧 Email: $email<br>";
    echo "🔑 Password: $password_plain<br>";
    echo "<small>(Change this password after first login!)</small>";
} else {
    echo "❌ Error creating admin: " . $stmt_insert->error;
}

$stmt_insert->close();
$conn->close();
?>
