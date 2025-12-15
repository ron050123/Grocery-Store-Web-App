<?php
session_start();
include('../includes/db.php');
include('../includes/header_1.php');
?>

<h2>Your Shopping Cart</h2>
<div class="cart-container">
    
<?php

// adding product to the cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'] ?? 1;

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }

    echo "<p>Product added to cart!</p>";
}

// removing product from the cart
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    unset($_SESSION['cart'][$product_id]);
    echo "<p>Product removed from cart!</p>";
}

// clearing the entire cart
if (isset($_POST['clear_cart'])) {
    unset($_SESSION['cart']);
    echo "<p>Your cart has been cleared.</p>";
}

// updating cart quantities
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        // Ensure quantity is not less than 1
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$product_id]);
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }

    echo "<p>Cart updated!</p>";
}

// check if cart is empty
$cart_is_empty = empty($_SESSION['cart']);

?>

<?php
if (!$cart_is_empty) {
    echo "<form method='POST' action='cart.php'>";
    echo "<ul class='cart-list'>";
    
    $total_price = 0;
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        // fetch product details from the database
        $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $product = $stmt->fetch();

        $total_price += $product['unit_price'] * $quantity;

        echo "<li>";
        echo "<img src='../assets/images/{$product['product_id']}.jpg' alt='{$product['product_name']}' style='width: 100%; max-width: 120px;'>";
        echo "<div class='product-details'>";
        echo "<div class='product-name'>{$product['product_name']}</div>";
        echo "<div class='product-price'>Unit Price: $" . number_format($product['unit_price'], 2) . "</div>";
        echo "<div class='product-price'>Weight: {$product['unit_quantity']}</div>";
        echo "<div class='product-quantity'>Quantity: <input type='number' name='quantity[$product_id]' value='$quantity' min='1'></div>";
        echo "<div class='product-price'>Price: $" . number_format($product['unit_price'] * $quantity, 2) . "</div>";
        echo "<a href='cart.php?remove=$product_id'>Remove</a>";
        echo "</div>";
        echo "</li>";
    }

    echo "</ul>";
    echo "<div class='cart-summary'>";
    echo "<p>Total Price: $" . number_format($total_price, 2) . "</p>";
    echo "<button type='submit' name='update_cart'>Update Cart</button>";
    echo "<button type='submit' name='clear_cart' class='clear-cart-button'>Clear Cart</button>";
    echo "</div>";
    echo "</form>";

    echo "<div class='cart-summary'>";
    echo "<form method='POST' action='checkout.php'>
            <button type='submit' name='place_order' class='place-order-button' " . ($cart_is_empty ? "disabled" : "") . ">Place Order</button>
            </form>";
    echo "</div>";
} else {
    echo "<p>Your cart is empty. You cannot proceed to checkout until you add items.</p>";
}

?>

</div> 

<?php
include('../includes/footer.php');
?>
