<?php
 include("connection/connect.php");
 // Define perishability limits in days
 $perishability_limits = [
    'High' => 7,
 'Medium' => 14,
 'Low' => 30
 ];
 // Get the current date
 $current_date = new DateTime();
 // Fetch all products
 $products_query = "SELECT d_id, listed_at, perishability FROM products";
 $products_result = mysqli_query($db, $products_query);
 while ($product = mysqli_fetch_assoc($products_result)) {
 $listed_date = new DateTime($product['listed_at']);
 $interval = $current_date->diff($listed_date);
 $days_since_listed = $interval->days;
 // Determine the perishability limit for the product
 $perishability_limit = $perishability_limits[$product['perishability']];
 // Check if the product has passed its perishability limit
    if ($days_since_listed > $perishability_limit) {
        //
 Put the product on sale
 $update_query = "UPDATE products SET sale = TRUE WHERE d_id = " . $product['d_id'];
        mysqli_query($db, $update_query);
    }
    }
 }
 else {
 // Remove the product from sale if it's within the perishability limit
 $update_query = "UPDATE products SET sale = FALSE WHERE d_id = " . $product['d_id'];
        mysqli_query($db, $update_query);
 echo "Product sale statuses updated.";
 ?>