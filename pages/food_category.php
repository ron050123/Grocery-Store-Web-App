<?php
include('../includes/header.php');
include('../includes/db.php');

// url parameter to filter products by category
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Fetch all products in the "Food" category
$query = "SELECT * FROM products WHERE product_name IN ('Fish Fingers', 'Hamburger Patties', 'Shelled Prawns', 'T Bone Steak', 'Cheddar Cheese', 'Tub Ice Cream', 'Navel Oranges', 'Bananas', 'Peaches', 'Grapes', 'Apples', 'Dry Dog Food', 'Bird Food', 'Cat Food', 'Fish Food')";

// selected category
if ($category) {
    if ($category == 'frozen_food') {
        $query .= " AND product_name IN ('Fish Fingers', 'Hamburger Patties', 'Shelled Prawns', 'T Bone Steak', 'Cheddar Cheese', 'Tub Ice Cream')";
    } elseif ($category == 'fruits') {
        $query .= " AND product_name IN ('Navel Oranges', 'Bananas', 'Peaches', 'Grapes', 'Apples')";
    } elseif ($category == 'pet_food') {
        $query .= " AND product_name IN ('Dry Dog Food', 'Bird Food', 'Cat Food', 'Fish Food')";
    }
}

$stmt = $pdo->query($query);
$products = $stmt->fetchAll();

// Display the products
echo "<div class='product-grid'>";
if (count($products) > 0) {
    foreach ($products as $product) {
        $is_in_stock = $product['in_stock'] > 0 ? 'In Stock' : 'Out of Stock';
        $disabled = $is_in_stock === 'Out of Stock' ? 'disabled' : '';
        $button_text = $is_in_stock === 'Out of Stock' ? 'Out of Stock' : 'Add to Cart';
        $button_class = $is_in_stock === 'Out of Stock' ? 'out-of-stock' : '';

        echo "<div class='product'>
                <img src='../assets/images/{$product['product_id']}.jpg' alt='{$product['product_name']}'>
                <h3>{$product['product_name']}</h3>
                <p>Weight: {$product['unit_quantity']}</p>
                <p>Price: \$" . number_format($product['unit_price'], 2) . "</p>
                <p>In stock: {$product['in_stock']}</p>
                <form method='POST' action='../pages/cart.php'>
                    <input type='hidden' name='product_id' value='{$product['product_id']}'>
                    <input type='number' name='quantity' value='1' min='1' $disabled>
                    <button type='submit' name='add_to_cart' class='$button_class' $disabled>$button_text</button>
                </form>
              </div>";
    }
} else {
    echo "<p>No products available at the moment.</p>";
}
echo "</div>";

include('../includes/footer.php');
?>
