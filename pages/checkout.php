<?php
session_start();
include('../includes/db.php');
include('../includes/header_1.php');
?>

<div class="checkout-container">
<h2>Checkout</h2>

<?php
// Check if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php"); // Redirect to cart if it's empty
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user inputs
    $recipient_name = $_POST['recipient_name'] ?? '';
    $street_address = $_POST['street_address'] ?? '';
    $city_suburb = $_POST['city_suburb'] ?? '';
    $state = $_POST['state'] ?? '';
    $mobile_number = $_POST['mobile_number'] ?? '';
    $email = $_POST['email'] ?? '';

    // validate inputs
    $errors = [];

    // validate recipient name
    if (empty($recipient_name)) {
        $errors[] = "Recipient name is required.";
    }

    // validate address fields
    if (empty($street_address) || empty($city_suburb) || empty($state)) {
        $errors[] = "All address fields (street, city/suburb, and state) are required.";
    }

    // validate mobile number (only numbers, 10 digits)
    if (!preg_match('/^\d{10}$/', $mobile_number)) {
        $errors[] = "Mobile number must be 10 digits.";
    }

    // validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    // check if all items are in stock and quantity is sufficient
    $cart_is_valid = true;
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        // fetch product details
        $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $product = $stmt->fetch();

        // check stock availability
        if ($product['in_stock'] < $quantity) {
            $cart_is_valid = false;
            $errors[] = "Product '{$product['product_name']}' is out of stock or insufficient quantity.";
        }
    }

    if (!empty($errors)) {
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
    } else {
        // update quantities in the database and clear cart
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            // update the stock in the database
            $stmt = $pdo->prepare("UPDATE products SET in_stock = in_stock - :quantity WHERE product_id = :product_id");
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->execute();
        }

        // compile order details after form submitted
        $order_details = "Order Summary: \n";
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            // fetch product details again for order summary
            $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = :product_id");
            $stmt->bindParam(':product_id', $product_id);
            $stmt->execute();
            $product = $stmt->fetch();
            $order_details .= "Product: {$product['product_name']} - Quantity: $quantity\n";
        }

        echo "<h3>Thank you for your order, <strong>$recipient_name</strong>!</h3>";
        echo "<p><strong>Your Delivery Details:</strong></p>";
        echo "<ul>";
        echo "<li><strong>Recipient Name:</strong> $recipient_name</li>";
        echo "<li><strong>Address:</strong> $street_address, $city_suburb, $state</li>";
        echo "<li><strong>Mobile Number:</strong> $mobile_number</li>";
        echo "<li><strong>Email Address:</strong> $email</li>";
        echo "</ul>";

        echo "<p><strong>Order Summary:</strong></p>";
        echo "<ul style='list-style-type: none; padding: 0; margin: 20px 0; font-family: Arial, sans-serif;'>";
            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = :product_id");
                $stmt->bindParam(':product_id', $product_id);
                $stmt->execute();
                $product = $stmt->fetch();

        echo "<li style='display: flex; align-items: flex-start; border-bottom: 1px solid #ddd; padding: 10px 0; margin-bottom: 15px;'>";
        echo "<img src='../assets/images/{$product['product_id']}.jpg' alt='{$product['product_name']}' style='max-width: 120px; height: auto; margin-right: 20px; border-radius: 8px;'>";
        echo "<div class='product-details' style='flex: 1; display: flex; flex-direction: column;'>";
        echo "<div class='product-name' style='font-size: 16px; font-weight: bold; margin-bottom: 5px; color: #2c3e50;'>{$product['product_name']}</div>";
        echo "<div class='product-price' style='font-size: 14px; margin-bottom: 5px; color: #7f8c8d;'>Unit Price: $" . number_format($product['unit_price'], 2) . "</div>";
        echo "<div class='product-weight' style='font-size: 14px; margin-bottom: 5px; color: #7f8c8d;'>Weight: {$product['unit_quantity']}</div>";
        echo "<div class='product-price' style='font-size: 14px; margin-bottom: 5px; color: #2c3e50;'>Quantity: $quantity </div>";
        echo "<div class='total-price' style='font-size: 16px; font-weight: bold; color: #e60000;'>Price: $" . number_format($product['unit_price'] * $quantity, 2) . "</div>";
        echo "</div>";
        echo "</li>";
        }
        echo "</ul>";
        echo "<p><strong>Total Price:</strong> $" . number_format(array_sum(array_map(function($product_id, $quantity) use ($pdo) {
            $stmt = $pdo->prepare("SELECT unit_price FROM products WHERE product_id = :product_id");
            $stmt->bindParam(':product_id', $product_id);
            $stmt->execute();
            $product = $stmt->fetch();
            return $product['unit_price'] * $quantity;
        }, array_keys($_SESSION['cart']), $_SESSION['cart'])), 2) . "</p>";

        echo "<p>An email confirmation has been sent to <strong>{$email}</strong> with the order details.</p>";

        // clear cart after successful order
        unset($_SESSION['cart']);
        exit();
    }
}
?>


<form method="POST" action="checkout.php">
    <label for="recipient_name">Recipient's Name:</label>
    <input type="text" name="recipient_name" id="recipient_name" required value="<?= htmlspecialchars($recipient_name ?? '') ?>"><br>

    <label for="street_address">Street Address:</label>
    <input type="text" name="street_address" id="street_address" required value="<?= htmlspecialchars($street_address ?? '') ?>"><br>

    <label for="city_suburb">City/Suburb:</label>
    <input type="text" name="city_suburb" id="city_suburb" required value="<?= htmlspecialchars($city_suburb ?? '') ?>"><br>

    <label for="state">State:</label>
    <select name="state" id="state" required>
        <option value="" disabled selected>Select State</option>
        <option value="NSW" <?= (isset($state) && $state == 'NSW') ? 'selected' : '' ?>>New South Wales</option>
        <option value="VIC" <?= (isset($state) && $state == 'VIC') ? 'selected' : '' ?>>Victoria</option>
        <option value="QLD" <?= (isset($state) && $state == 'QLD') ? 'selected' : '' ?>>Queensland</option>
        <option value="WA" <?= (isset($state) && $state == 'WA') ? 'selected' : '' ?>>Western Australia</option>
        <option value="SA" <?= (isset($state) && $state == 'SA') ? 'selected' : '' ?>>South Australia</option>
        <option value="TAS" <?= (isset($state) && $state == 'TAS') ? 'selected' : '' ?>>Tasmania</option>
        <option value="ACT" <?= (isset($state) && $state == 'ACT') ? 'selected' : '' ?>>Australian Capital Territory</option>
        <option value="NT" <?= (isset($state) && $state == 'NT') ? 'selected' : '' ?>>Northern Territory</option>
        <option value="Others" <?= (isset($state) && $state == 'Others') ? 'selected' : '' ?>>Other</option>
    </select><br>

    <label for="mobile_number">Mobile Number:</label>
    <input type="text" name="mobile_number" id="mobile_number" required value="<?= htmlspecialchars($mobile_number ?? '') ?>"><br>

    <label for="email">Email Address:</label>
    <input type="email" name="email" id="email" required value="<?= htmlspecialchars($email ?? '') ?>"><br>

    <button type="submit">Place Order</button>
</form>
</div>

<?php
include('../includes/footer.php');
?>
