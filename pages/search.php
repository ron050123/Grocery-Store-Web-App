<?php
include('../includes/db.php');
include('../includes/header.php');

if (isset($_GET['query'])) {
    $search_query = '%' . $_GET['query'] . '%';

    // search products by name or details
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_name LIKE :query OR product_id LIKE :query");
    $stmt->bindParam(':query', $search_query);
    $stmt->execute();
    $products = $stmt->fetchAll();

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
        echo "<p>No products found matching your search query.</p>";
    }
    echo "</div>";
} else {
    echo "<p>Please enter a search term.</p>";
}

include('../includes/footer.php');
?>
