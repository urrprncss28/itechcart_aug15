<?php
session_start();
include("includes/db.connection.php"); // include your database connection

// Check if admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Function to save settings
function saveSettings($conn) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $site_name = $_POST['site_name'] ?? '';
        $contact_email = $_POST['contact_email'] ?? '';
        $currency = $_POST['currency'] ?? '';
        
        // Handle file upload
        $logo_name = null;
        if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $logo_name = time() . '_' . basename($_FILES['site_logo']['name']);
            move_uploaded_file($_FILES['site_logo']['tmp_name'], $upload_dir . $logo_name);
        }

        // Save settings to database (assuming a table `site_settings` with columns `site_name`, `contact_email`, `currency`, `logo`)
        // You can use INSERT ... ON DUPLICATE KEY UPDATE if only one row exists
        $sql = "INSERT INTO site_settings (id, site_name, contact_email, currency, logo)
                VALUES (1, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                site_name = VALUES(site_name),
                contact_email = VALUES(contact_email),
                currency = VALUES(currency),
                logo = IF(? IS NOT NULL, VALUES(logo), logo)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssss', $site_name, $contact_email, $currency, $logo_name, $logo_name);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success text-center'>Settings saved successfully!</div>";
        } else {
            echo "<div class='alert alert-danger text-center'>Failed to save settings: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

// Call the function to handle form submission
saveSettings($conn);

// Load current settings from DB
$result = $conn->query("SELECT * FROM site_settings WHERE id = 1");
if ($result && $row = $result->fetch_assoc()) {
    $site_name = $row['site_name'];
    $contact_email = $row['contact_email'];
    $currency = $row['currency'];
    $logo = $row['logo'];
} else {
    $site_name = "iTech Cart";
    $contact_email = "admin@itechcart.com";
    $currency = "PHP";
    $logo = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Site Settings - Admin Dashboard</title>
<link rel="stylesheet" href="bootstrap-5.3.7-dist/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body {
    font-family: "Poppins", "Segoe UI", sans-serif;
    background: linear-gradient(135deg, #fdfbfb, #ebedee);
    padding: 30px 0;
}

.container.layout_padding {
    max-width: 900px; /* slightly wider */
    margin: 0 auto;
    background: rgba(255,255,255,0.97);
    border-radius: 15px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.08);
    padding: 40px 35px; /* more breathing room */
}

h1.fashion_taital {
    text-align: center;
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 35px;
    font-size: 2.4rem; /* slightly bigger */
}

label {
    font-weight: 600;
    margin-top: 12px;
    font-size: 1rem;
}

.form-control {
    border-radius: 10px;
    padding: 10px 12px; /* increased height for readability */
    font-size: 1rem;
}

.btn-primary {
    background: linear-gradient(135deg, #4b7bec, #3867d6);
    border: none;
    padding: 12px 25px; /* bigger click area */
    border-radius: 10px;
    color: #fff;
    font-size: 1.05rem;
    transition: 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #3867d6, #4b7bec);
    transform: translateY(-2px);
}

a {
    text-decoration: none !important;
    font-size: 0.95rem;
}
</style>

</head>
<body>
<div class="container layout_padding">
    <a href="admin_dashboard.php" class="btn btn-primary mb-3">⬅ Back to Dashboard</a>
    <h1 class="fashion_taital">Site Settings</h1>

    <form method="post" enctype="multipart/form-data">

        <div class="mb-3">
            <label for="site_name">Site Name</label>
            <input type="text" class="form-control" id="site_name" name="site_name" value="<?= htmlspecialchars($site_name); ?>" required>
        </div>

        <div class="mb-3">
            <label for="contact_email">Contact Email</label>
            <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?= htmlspecialchars($contact_email); ?>" required>
        </div>

        <div class="mb-3">
            <label for="currency">Default Currency</label>
            <input type="text" class="form-control" id="currency" name="currency" value="<?= htmlspecialchars($currency); ?>" required>
        </div>

        <div class="mb-3">
            <label for="site_logo">Site Logo</label>
            <input type="file" class="form-control" id="site_logo" name="site_logo">
            <small class="text-muted">Leave empty to keep existing logo</small>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>
</div>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
