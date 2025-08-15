<?php
session_start();

// Destroy session if it exists
if (isset($_SESSION)) {
    $_SESSION = [];
    session_destroy();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Logout</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" href="css/bootstrap.min.css" />
<style>
body {
    background-color: #ececec;
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}
.logout-container {
    background: rgba(255, 255, 255, 0.98);
    border-radius: 20px;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
    padding: 50px 60px;
    max-width: 900px;
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    position: relative;
}
.thank-you {
    font-weight: 800;
    font-size: 2.8rem;
    color: #2c3e50;
    margin-bottom: 12px;
    line-height: 1.1;
}
.info-text {
    font-size: 1.3rem;
    color: #555;
    font-weight: 600;
    line-height: 1.4;
    margin-bottom: 20px;
}
.btn-login {
    background: linear-gradient(135deg, #ff914d, #ff6f3c);
    color: white;
    padding: 14px 36px;
    font-weight: 700;
    font-size: 1.1rem;
    border-radius: 12px;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    transition: background 0.3s ease, box-shadow 0.3s ease;
}
.btn-login:hover {
    background: linear-gradient(135deg, #ff6f3c, #ff914d);
    box-shadow: 0 8px 25px rgba(242, 101, 34, 0.5);
}
.image-box {
    border-radius: 18px;
    overflow: hidden;
    max-width: 350px;
    height: 400px;
    margin: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    transition: transform 0.3s ease;
}
.image-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.image-box:hover {
    transform: scale(1.05);
}
.decorative-circle {
    position: absolute;
    top: 20%;
    left: 10%;
    width: 200px;
    height: 200px;
    background: linear-gradient(135deg, #f26522, #ff914d);
    border-radius: 50%;
    opacity: 0.15;
    pointer-events: none;
    z-index: 0;
}
@media (max-width: 991.98px) {
    .decorative-circle { display: none; }
    .logout-container { flex-direction: column; align-items: center; }
}
</style>
</head>
<body>
<div class="logout-container">

    <div class="decorative-circle"></div>

    <!-- Left image -->
    <div class="image-box col-12 col-md-5 mb-4 mb-md-0">
        <img src="images/iphone_logout.jpg" alt="Thank you illustration" />
    </div>

    <!-- Right content -->
    <div class="col-12 col-md-7 d-flex flex-column justify-content-center">
        <h1 class="thank-you">Thank you for choosing iTech Cart!</h1>
        <p class="info-text">
            Ready to shop again? Log in to discover new deals and exclusive offers curated just for you.
        </p>
        <a href="login.php" class="btn-login">Login</a>
    </div>

</div>
</body>
</html>
