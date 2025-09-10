<?php
 include("connection/connect.php"); //connection to db
 error_reporting(0);
 session_start();
 // Get order ID
 $order_id = $_GET['order_del'];
 $orderQuery = "SELECT title, quantity, status FROM users_orders WHERE o_id = '$order_id'";
 $orderResult = mysqli_query($db, $orderQuery);
 if ($orderResult && mysqli_num_rows($orderResult) > 0) {
    $order = mysqli_fetch_assoc($orderResult);
    $title = $order['title'];
    $quantity = $order['quantity'];
    $status = $order['status'];
    $productQuery = "SELECT d_id FROM products WHERE title = '$title'";
    $productResult = mysqli_query($db, $productQuery);
    
    if ($productResult && mysqli_num_rows($productResult) > 0) {
        $product = mysqli_fetch_assoc($productResult);
        $product_id = $product['d_id'];
        $deleteQuery = "DELETE FROM users_orders WHERE o_id = '$order_id'";
        
        if (mysqli_query($db, $deleteQuery)) {
            // Update product quantity only if the order status is not "delivered"
            if ($status != 'closed') {
                $updateQuantityQuery = "UPDATE products SET quantity = quantity + $quantity 
WHERE d_id = '$product_id'";
                mysqli_query($db, $updateQuantityQuery);
            }
            echo "<script>alert('Order deleted successfully');</script>";
            echo "<script>window.location.replace('your_orders.php');</script>";
        } else {
            echo "Error deleting order: " . mysqli_error($db);
        }
    } else {
        echo "Product not found!";
    }
} else {
 echo "Order not found!";
 }
 ?>