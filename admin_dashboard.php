<?php
session_start();
include("includes/db.connection.php");

// Check if admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch totals
$total_customers = $conn->query("SELECT COUNT(*) as total_customers FROM users WHERE role='customer'")->fetch_assoc()['total_customers'];

$total_products = $conn->query("SELECT COUNT(*) as total_products FROM products")->fetch_assoc()['total_products'];
$total_orders = $conn->query("SELECT COUNT(*) as total_orders FROM orders")->fetch_assoc()['total_orders'];
$sales_today = $conn->query("SELECT SUM(grand_total) as sales_today FROM orders WHERE DATE(order_date) = CURDATE()")->fetch_assoc()['sales_today'] ?? 0;
$total_sales = $conn->query("SELECT SUM(grand_total) as total_sales FROM orders")->fetch_assoc()['total_sales'] ?? 0;

$first_name = htmlspecialchars($_SESSION['first_name']);
$last_name = htmlspecialchars($_SESSION['last_name']);
$email = htmlspecialchars($_SESSION['email']);
$profile_picture = $_SESSION['profile_picture'] ?? null;

// --- Prepare data for stacked line chart ---
$result = $conn->query("
    SELECT DATE(o.order_date) as order_day, oi.product_name, SUM(oi.quantity) as qty
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    GROUP BY order_day, oi.product_name
    ORDER BY order_day ASC
");

$chart_data = [];
$product_names = [];
$order_days = [];

while($row = $result->fetch_assoc()){
    $order_days[$row['order_day']] = $row['order_day'];
    $product_names[$row['product_name']] = $row['product_name'];
    $chart_data[$row['product_name']][$row['order_day']] = (int)$row['qty'];
}

// Make consistent dataset for each product over all dates
$order_days_sorted = array_values($order_days);
$datasets = [];
$colors = ['#ff6384','#36a2eb','#ffce56','#4bc0c0','#9966ff','#ff9f40','#c9cbcf','#a8e6cf','#ffd3b6','#ffaaa5'];
$i=0;
foreach($product_names as $product){
    $data = [];
    foreach($order_days_sorted as $day){
        $data[] = $chart_data[$product][$day] ?? 0;
    }
    $datasets[] = [
        'label' => $product,
        'data' => $data,
        'backgroundColor' => $colors[$i % count($colors)],
        'borderColor' => $colors[$i % count($colors)],
        'fill' => true
    ];
    $i++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="bootstrap-5.3.7-dist/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
/* Global Styles */
body { font-family: 'Inter', sans-serif; margin:0; background:#f4f6f8; color:#333; }
a { text-decoration:none !important; color:inherit; }

/* Navbar */
.navbar-custom { background: linear-gradient(90deg, #1e3c72, #2a5298); color:white; padding:15px 30px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 3px 12px rgba(0,0,0,0.1);}
.navbar-custom .navbar-brand { font-weight:700; font-size:1.6rem; }
.navbar-custom .user-email { font-size:0.95rem; font-weight:500; }

/* Sidebar */
.sidebar { background:#fff; height:100vh; padding:40px 20px; box-shadow:2px 0 15px rgba(0,0,0,0.05); position:fixed; top:0; left:0; width:220px; overflow-y:auto;}
.sidebar h4 { margin-bottom:25px; color:#2c3e50; font-weight:600; }
.sidebar a { display:flex; justify-content:space-between; align-items:center; padding:12px 15px; margin-bottom:12px; border-radius:8px; color:#2c3e50; font-weight:500; transition: all 0.3s ease; }
.sidebar a:hover { background:#e0f0ff; color:#1e3c72; }
.sidebar a::after { content:'➔'; font-size:0.9rem; color:#1e3c72; }

/* Main Content */
.main-content { margin-left:240px; padding:40px 30px; }
h1.dashboard-title { font-size:2.2rem; margin-bottom:35px; color:#2c3e50; font-weight:600; }

/* Metrics Cards */
.card-metric { background:#fff; border-radius:12px; padding:30px 20px; text-align:center; box-shadow:0 6px 20px rgba(0,0,0,0.08); transition: all 0.3s ease; }
.card-metric:hover { transform:translateY(-6px); box-shadow:0 8px 25px rgba(0,0,0,0.12);}
.card-metric h3 { font-size:2rem; color:#1e3c72; margin-bottom:10px; }
.card-metric p { margin:0; color:#555; font-weight:500; }

/* Info Cards */
.info-card { background:#fff; border-radius:12px; padding:25px 20px; margin-bottom:20px; box-shadow:0 4px 15px rgba(0,0,0,0.05); transition: all 0.3s ease; }
.info-card:hover { transform:translateY(-4px); box-shadow:0 6px 20px rgba(0,0,0,0.08); }
.info-card h5 { margin-bottom:15px; font-weight:600; color:#2c3e50; }

/* Row spacing */
.row.g-4 { margin-left:0; margin-right:0; }
.row.g-4 > [class*='col-'] { padding-left:10px; padding-right:10px; }

/* Responsive */
@media (max-width: 992px) {
    .sidebar { position:relative; width:100%; height:auto; box-shadow:none; padding:20px 15px; }
    .main-content { margin-left:0; padding:25px 15px; }
    .sidebar a::after { display:none; }
}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-custom">
    <span class="navbar-brand">iTechCart Admin</span>
    <span class="user-email"><?= $first_name . ' ' . $last_name . ' | ' . $email; ?></span>
</nav>

<!-- Sidebar -->
<div class="sidebar">
    <!-- Admin Profile Card -->
<div class="row g-4 mb-4">
    <div class="col-lg-12">
        <div class="info-card d-flex align-items-center p-3" style="
            gap: 20px;
            background: linear-gradient(135deg, #3867d6, #113996ff); /* blue gradient */
            color: #ffffff; /* white text */
            border-radius: 5px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        ">
            <div style="position: relative;">
                <img id="adminProfile" src="<?= $profile_picture ?? 'images/logo.png'; ?>" alt="Admin Profile" 
                     style="width:80px; height:80px; border-radius:50%; object-fit:cover; border:2px solid #1e3c72;">
                <form action="update_profile_picture.php" method="POST" enctype="multipart/form-data" 
                      style="position:absolute; bottom:0; left:0;">
                    <label for="profileUpload" style="cursor:pointer; background:#1e3c72; color:#fff; border-radius:50%; padding:4px; font-size:0.8rem;">✎</label>
                    <input type="file" name="profile_picture" id="profileUpload" style="display:none;" onchange="this.form.submit()">
                </form>
            </div>
            <div>
                <h5><?= $first_name . ' ' . $last_name; ?></h5>
                <p><strong>Email:</strong> <?= $email; ?></p>
                <p><strong>Role:</strong> Admin</p>
                <p><strong>Last login:</strong> <?= date("Y-m-d H:i:s"); ?></p>
            </div>
        </div>
    </div>
</div>

    <h4>Navigation</h4>
    <a href="manage_users.php">Manage Users</a>
    <a href="manage_products.php">Manage Products</a>
    <a href="view_orders.php">View Orders</a>
    <a href="site_settings.php">Site Settings</a>
    <a href="logout.php" style="color:#007bff;">Logout</a>
</div>
<!-- Main Content -->
<div class="main-content"><h2 class="dashboard-title">Dashboard</h2>


<!-- Stacked Line Chart -->
<div class="row g-4 mt-4">
    <div class="col-lg-12">
        <div class="info-card">
            <h5>Phones Sold Over Time</h5>
            <canvas id="phonesChart" style="height:400px;"></canvas>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('phonesChart').getContext('2d');
const phonesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($order_days_sorted); ?>,
        datasets: <?= json_encode($datasets); ?>
    },
    options: {
        responsive: true,
        plugins: { title: { display: true, text: 'Stacked Line Chart of Phone Sales' } },
        interaction: { mode: 'nearest', intersect: false },
        scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }
    }
});
</script>
<!-- Metrics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3"><div class="card-metric"><h3><?= $total_customers; ?></h3><p>Total Customers</p></div></div>
    <div class="col-md-6 col-lg-3"><div class="card-metric"><h3><?= $total_products; ?></h3><p>Total Products</p></div></div>
    <div class="col-md-6 col-lg-3"><div class="card-metric"><h3><?= $total_orders; ?></h3><p>Total Orders</p></div></div>
    <div class="col-md-6 col-lg-3"><div class="card-metric"><h3>₱<?= number_format($sales_today,2); ?></h3><p>Sales Today</p></div></div>
    <div class="col-md-6 col-lg-3"><div class="card-metric"><h3>₱<?= number_format($total_sales,2); ?></h3><p>Total Sales</p></div></div>
</div>

<!-- System Info -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="info-card">
            <h5>System Info</h5>
            <p>iTechCart v1.0</p>
            <p>Server: localhost</p>
            <p>PHP: <?= phpversion(); ?></p>
        </div>
    </div>
</div>

</div>
</body>
</html>
