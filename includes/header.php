<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreshBasket</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <div id="logo">
            <a href="../pages/index.php"><img src="../assets/images/logo.png" alt="Logo"></a>
        </div>
        <div class="dropdown">
            <button class="dropbtn">
            <a href="food_category.php" style="color: inherit; text-decoration: none; padding: 0; background-color: transparent;">Food 
            <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-content">
               <a href="food_category.php?category=frozen_food">Frozen Food</a>
               <a href="food_category.php?category=fruits">Fruits</a>
               <a href="food_category.php?category=pet_food">Pet Food</a>
            </div>
         </div>
         <div class="dropdown">
            <button class="dropbtn">
            <a href="health_care.php" style="color: inherit; text-decoration: none; padding: 0; background-color: transparent;">Health & Care
            <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-content">
               <a href="health_care.php?category=medicines">Medicines</a>
               <a href="health_care.php?category=personal_hygiene">Personal Hygiene</a>
               <a href="health_care.php?category=laundry_cleaning">Laundry & Cleaning</a>
            </div>
         </div>
         <div class="dropdown">
            <button class="dropbtn">
            <a href="beverages_snacks.php" style="color: inherit; text-decoration: none; padding: 0; background-color: transparent;">Beverages & Snacks
            <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-content">
               <a href="beverages_snacks.php?category=tea_coffee">Tea & Coffee</a>
               <a href="beverages_snacks.php?category=snacks">Snacks</a>
            </div>
         </div>
        <div id="search-box">
            <form method="GET" action="../pages/search.php">
                <input type="text" name="query" id="search-input" placeholder="Search for products..." autocomplete="on">
            </form>
        </div>
        <div id="cart">
            <a href="../pages/cart.php">Cart</a>
        </div>
    </header>
