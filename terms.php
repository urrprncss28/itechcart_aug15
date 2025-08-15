<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>iTech Cart - Terms of Service</title>
<link rel="stylesheet" href="bootstrap/bootstrap-5.3.7-dist/css/bootstrap.min.css">
<style>
body { font-family: "Poppins", sans-serif; background-color: #f8f9fa; }
.container { max-width: 900px; margin-top: 50px; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.05); }
h1 { margin-bottom: 20px; }
h4 { margin-top: 25px; }
a { text-decoration: none; color: #007bff; }
p { margin-bottom: 15px; line-height: 1.6; }
ul { margin-bottom: 15px; }
</style>
</head>
<body>
<div class="container">
    <a href="register.php" class="btn btn-sm btn-secondary mb-3">⬅ Back to Register</a>
    <h1>Terms of Service</h1>
    <p>Welcome to <strong>iTech Cart</strong>, your trusted online store for genuine iPhones, accessories, and related services. By accessing or using our website, you agree to comply with and be bound by the following terms. Please read them carefully.</p>

    <h4>1. Account & Registration</h4>
    <p>To access certain features of our website, you must create an account. You agree to:</p>
    <ul>
        <li>Provide accurate, current, and complete information during registration.</li>
        <li>Maintain the confidentiality of your account credentials.</li>
        <li>Notify us immediately of any unauthorized use of your account.</li>
    </ul>
    <p>Failure to do so may result in suspension or termination of your account.</p>

    <h4>2. Orders & Payment</h4>
    <p>All prices are in Philippine Peso (PHP) and are inclusive of applicable taxes unless otherwise stated. When placing an order:</p>
    <ul>
        <li>Payment must be completed before shipment. Accepted methods include credit/debit cards, GCash, and bank transfers.</li>
        <li>Orders are subject to availability. We reserve the right to cancel or modify orders if stock is insufficient.</li>
        <li>iTech Cart reserves the right to refuse any order for any reason, including suspected fraudulent activity.</li>
    </ul>

    <h4>3. Shipping & Delivery</h4>
    <p>We aim to deliver your products promptly, but delivery times are estimates only. By placing an order, you agree to:</p>
    <ul>
        <li>Provide a valid shipping address with accurate contact details.</li>
        <li>Accept that iTech Cart is not liable for delays caused by third-party shipping services, natural disasters, or other unforeseen events.</li>
        <li>Inspect all items upon delivery and report any damages or missing items immediately.</li>
    </ul>

    <h4>4. Returns & Refunds</h4>
    <p>Our Return Policy applies to all purchases. You may request a return if:</p>
    <ul>
        <li>The product is defective, damaged during shipping, or not as described.</li>
        <li>Returns are requested within 7 days of receipt. After verification, refunds will be issued to the original payment method.</li>
        <li>Products must be returned in their original packaging with all accessories included.</li>
    </ul>

    <h4>5. Product Warranty</h4>
    <p>Products sold on iTech Cart may come with manufacturer warranties. We are not responsible for warranty claims beyond the coverage provided by the manufacturer. Please retain all receipts and warranty documentation.</p>

    <h4>6. Limitation of Liability</h4>
    <p>iTech Cart shall not be liable for:</p>
    <ul>
        <li>Indirect, incidental, or consequential damages arising from the use of our website or products.</li>
        <li>Losses caused by service interruptions, data corruption, or errors in pricing or product descriptions.</li>
        <li>Any damages resulting from misuse of products or failure to follow instructions.</li>
    </ul>

    <h4>7. Intellectual Property</h4>
    <p>All content on iTech Cart, including logos, images, and text, is protected by copyright and trademark laws. Unauthorized use is prohibited.</p>

    <h4>8. Privacy</h4>
    <p>By using iTech Cart, you consent to the collection and use of personal information in accordance with our <a href="privacy.php">Privacy Policy</a>. We take your privacy seriously and implement industry-standard measures to protect your data.</p>

    <h4>9. Changes to Terms</h4>
    <p>iTech Cart may update these Terms of Service at any time. Updated terms will be posted on this page. Continued use of our website after changes constitutes acceptance of the new terms.</p>

    <h4>10. Contact Information</h4>
    <p>If you have questions about these terms, please contact us at <a href="mailto:support@itechcart.com">support@itechcart.com</a> or call our customer support at 09912948281.</p>

    <p>Thank you for choosing <strong>iTech Cart</strong>. We value your trust and are committed to providing a safe and reliable shopping experience.</p>

    <p>By using iTech Cart, you consent to the collection and use of personal information in accordance with our 
<a href="privacy.php" target="_blank">Privacy Policy</a>. We take your privacy seriously and implement industry-standard measures to protect your data.</p>

<!-- Cookie Consent Banner -->
<div id="cookieConsent" style="position: fixed; bottom: 0; left: 0; width: 100%; background: #343a40; color: #fff; padding: 15px; text-align: center; display: none; z-index: 1000;">
    We use cookies to enhance your experience. By continuing to visit this site, you agree to our 
    <a href="privacy.php" style="color: #3117faff; text-decoration: underline;">Privacy Policy</a>.
    <button id="acceptCookies" class="btn btn-sm btn-warning ms-2">Accept</button>
</div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    if (!localStorage.getItem("cookiesAccepted")) {
        document.getElementById("cookieConsent").style.display = "block";
    }

    document.getElementById("acceptCookies").addEventListener("click", function() {
        localStorage.setItem("cookiesAccepted", "true");
        document.getElementById("cookieConsent").style.display = "none";
    });
});
</script>

<script src="bootstrap/bootstrap-5.3.7-dist/js/bootstrap.min.js"></script>
</body>
</html>
