<?php
include("includes/db.connection.php");
session_start();

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? 'add';
    $product_id = $_POST['product_id'] ?? null;

    if ($action === 'add' && $product_id) {
        $product_name  = $_POST['product_name'] ?? '';
        $product_price = $_POST['product_price'] ?? '';

        // Check if product exists in cart
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $product_id) {
                $item['quantity'] += 1;
                $found = true;
                break;
            }
        }

        // If not in cart, add it
        if (!$found) {
            $_SESSION['cart'][] = [
                'id'       => $product_id,
                'name'     => $product_name,
                'price'    => $product_price,
                'quantity' => 1
            ];
        }

        $_SESSION['message'] = "Successfully added to the cart";

        // If Buy Now clicked, set only this item as selected for checkout
        if (!empty($_POST['buy_now'])) {
            $_SESSION['selected_items'] = [$product_id];
        }
    } 
    elseif ($action === 'remove' && $product_id) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $product_id) {
                unset($_SESSION['cart'][$key]);
                break;
            }
        }
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

// Redirect depending on button clicked
if (!empty($_POST['buy_now'])) {
    header("Location: checkout.php");
} else {
    header("Location: cart.php");
}
exit();
