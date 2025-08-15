<?php
session_start();
include("includes/db.connection.php");

function renderBuyButtons($id, $name, $price) {
    ?>
    <div class="btn_main">
        <!-- Buy Now -->
        <form action="add_to_cart.php" method="POST" style="display:inline;">
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($id) ?>">
            <input type="hidden" name="product_name" value="<?= htmlspecialchars($name) ?>">
            <input type="hidden" name="product_price" value="<?= htmlspecialchars($price) ?>">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="buy_now" value="1">
            <button type="submit" style="cursor:pointer; color:#f26522; font-weight:bold; background:none; border:none;">
                Buy Now
            </button>
        </form>

        <!-- Add to Cart -->
        <form action="add_to_cart.php" method="POST" style="display:inline;">
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($id) ?>">
            <input type="hidden" name="product_name" value="<?= htmlspecialchars($name) ?>">
            <input type="hidden" name="product_price" value="<?= htmlspecialchars($price) ?>">
            <input type="hidden" name="action" value="add">
            <button type="submit" style="cursor:pointer; color:#f26522; font-weight:bold; background:none; border:none;">
                Add to Cart
            </button>
        </form>
    </div>
    <?php
}
?>
