<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php");
error_reporting(0);
session_start();

$user_id = $_SESSION['user_id'];
function function_alert() {
    echo "<script>alert('Thank you. Your Order has been placed!');</script>";
    echo "<script>window.location.replace('your_orders.php');</script>";
}
if (empty($_SESSION["user_id"])) {
    header('location:login.php');
    exit; // Ensure script stops for unauthorized users
}

// Initialize variables
$user_id = $_SESSION['user_id'];
$item_total = 0;

// Fetch customer name
$fetch_customer_query = "SELECT f_name FROM users WHERE u_id='$user_id'";
$fetch_customer_result = mysqli_query($db, $fetch_customer_query);
if ($fetch_customer_result && mysqli_num_rows($fetch_customer_result) > 0) {
    $customer_row = mysqli_fetch_assoc($fetch_customer_result);
    $customer_name = $customer_row['f_name'];
} else {
    $customer_name = 'Customer';
}

// Calculate total price of items in the cart
foreach ($_SESSION["cart_item"] as $item) {
    $item_total += ($item["price"] * $item["quantity"]);
}

// Handle order submission
if (isset($_POST['submit'])) {
    // Get delivery option
    $delivery_option = $_POST['delivery_option'];
    $delivery_charges = 0; // Default to 0

    if ($delivery_option == 'delivery') {
        // Start database transaction for data consistency
        mysqli_begin_transaction($db);
        try {
            foreach ($_SESSION["cart_item"] as $item) {
                $title = $item["title"];
                $quantity = $item["quantity"];
                $price = $item["price"];
                $product_id = $item["d_id"];

                // Fetch rs_id (farmer's ID) associated with the product
                $fetch_rs_id_query = "SELECT rs_id FROM products WHERE d_id = '$product_id'";
                $fetch_rs_id_result = mysqli_query($db, $fetch_rs_id_query);
                if ($fetch_rs_id_result && mysqli_num_rows($fetch_rs_id_result) > 0) {
                    $row = mysqli_fetch_assoc($fetch_rs_id_result);
                    $rs_id = $row['rs_id'];

                    // Fetch delivery settings for the farmer
                    $fetch_farm_settings_query = "SELECT free_delivery_threshold, fixed_delivery_fee FROM farmer WHERE rs_id = '$rs_id'";
                    $fetch_farm_settings_result = mysqli_query($db, $fetch_farm_settings_query);
                    if ($fetch_farm_settings_result && mysqli_num_rows($fetch_farm_settings_result) > 0) {
                        $settings = mysqli_fetch_assoc($fetch_farm_settings_result);
                        $free_delivery_threshold = $settings['free_delivery_threshold'];
                        $fixed_delivery_fee = $settings['fixed_delivery_fee'];

                        // Calculate delivery charges
                        if ($item_total >= $free_delivery_threshold) {
                            $delivery_charges = 0;
                        } else {
                            $delivery_charges = $fixed_delivery_fee;
                        }

                        // Insert order details into users_orders table
                        $insert_order_query = "INSERT INTO users_orders (u_id, title, quantity, price, rs_id, delivery_option, delivery_charges) 
                        VALUES ('$user_id', '$title', '$quantity', '$price', '$rs_id', '$delivery_option', '$delivery_charges')";
                        mysqli_query($db, $insert_order_query);

                        // Update product quantity in the products table
                        $update_quantity_query = "UPDATE products SET quantity = quantity - $quantity 
                        WHERE d_id = '$product_id'";
                        mysqli_query($db, $update_quantity_query);

                        // Insert notifications
                        $total_amount = $item_total + $delivery_charges;
                        $customer_notification = "You have successfully ordered $quantity of $title at total cost of E$total_amount with delivery option: $delivery_option";
                        $insert_customer_not = "INSERT into notifications (u_id,message,read_status) VALUES ('$user_id','$customer_notification','$read_status')";
                        mysqli_query($db, $insert_customer_not);

                        $farmer_notification = "You have received an order from $customer_name of $quantity of $title at total cost of E$total_amount with delivery option: $delivery_option";
                        $insert_farmer_not = "INSERT into notifications (rs_id,message,read_status) VALUES ('$rs_id','$farmer_notification','$read_status')";
                        mysqli_query($db, $insert_farmer_not);
                    } else {
                        throw new Exception("Error fetching delivery settings for farmer id: $rs_id");
                    }
                } else {
                    throw new Exception("Error fetching rs_id for product id: $product_id");
                }
            }

            // Commit the transaction
            mysqli_commit($db);

            // Clear the cart after successful order placement
            unset($_SESSION["cart_item"]);

            // Show success message and redirect
            function_alert();
        } catch (Exception $e) {
            // Rollback the transaction in case of any error
            mysqli_rollback($db);
            echo "Error: " . $e->getMessage();
        }
    } else {
        // Start database transaction for data consistency
        mysqli_begin_transaction($db);
        try {
            foreach ($_SESSION["cart_item"] as $item) {
                $title = $item["title"];
                $quantity = $item["quantity"];
                $price = $item["price"];
                $product_id = $item["d_id"];

                // Fetch rs_id (farmer's ID) associated with the product
                $fetch_rs_id_query = "SELECT rs_id FROM products WHERE d_id = '$product_id'";
                $fetch_rs_id_result = mysqli_query($db, $fetch_rs_id_query);
                if ($fetch_rs_id_result && mysqli_num_rows($fetch_rs_id_result) > 0) {
                    $row = mysqli_fetch_assoc($fetch_rs_id_result);
                    $rs_id = $row['rs_id'];


                        
                        // Insert order details into users_orders table
                        $insert_order_query = "INSERT INTO users_orders (u_id, title, quantity, price, rs_id, delivery_option) 
                        VALUES ('$user_id', '$title', '$quantity', '$price', '$rs_id', '$delivery_option')";

                        mysqli_query($db, $insert_order_query);

                        // Update product quantity in the products table
                        $update_quantity_query = "UPDATE products SET quantity = quantity - $quantity 
                        WHERE d_id = '$product_id'";
                        mysqli_query($db, $update_quantity_query);

                        // Insert notifications
                        $total_amount = $item_total + $delivery_charges;
                        $customer_notification = "You have successfully ordered $quantity of $title at total cost of E$total_amount with delivery option: $delivery_option";
                        $insert_customer_not = "INSERT into notifications (u_id,message,read_status) VALUES ('$user_id','$customer_notification','$read_status')";
                        mysqli_query($db, $insert_customer_not);

                        $farmer_notification = "You have received an order from $customer_name of $quantity of $title at total cost of E$total_amount with delivery option: $delivery_option";
                        $insert_farmer_not = "INSERT into notifications (rs_id,message,read_status) VALUES ('$rs_id','$farmer_notification','$read_status')";
                        mysqli_query($db, $insert_farmer_not);
                } else {
                    throw new Exception("Error fetching rs_id for product id: $product_id");
                }
            }

            // Commit the transaction
            mysqli_commit($db);

            // Clear the cart after successful order placement
            unset($_SESSION["cart_item"]);

            // Show success message and redirect
            function_alert();
        } catch (Exception $e) {
            // Rollback the transaction in case of any error
            mysqli_rollback($db);
            echo "Error: " . $e->getMessage();
        }
    }
}
?>
<head>
 <meta charset="utf-8">
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
 <meta name="description" content="">
 <meta name="author" content="">
 <link rel="icon" href="#">
 <title>Checkout</title>
 <link href="css/bootstrap.min.css" rel="stylesheet">
 <link href="css/font-awesome.min.css" rel="stylesheet">
 <link href="css/animsition.min.css" rel="stylesheet">
 <link href="css/animate.css" rel="stylesheet">
 <link href="css/style.css" rel="stylesheet">
</head>
<body>
<div class="site-wrapper">
 <header id="header" class="header-scroll top-header headrom">
 <nav class="navbar navbar-dark">
 <div class="container">
 <button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse" data-target="#mainNavbarCollapse">&#9776;</button>
 <a class="navbar-brand" href="index.php"> <img class="img-rounded" src="images/icn.png" alt=""> </a>
 <div class="collapse navbar-toggleable-md float-lg-right" id="mainNavbarCollapse">
 <ul class="nav navbar-nav">
 <li class="nav-item"> <a class="nav-link active" href="index.php">Home <span class="sr-only">(current)</span></a> </li>
 <?php
 if (empty($_SESSION["user_id"])) {
    echo '<li class="nav-item"><a href="login.php" class="nav-link active">Login<a> </li>
    <li class="nav-item"><a href="registration.php" class="nav-link active">Register</a> </li>';
} else {
    echo '<li class="nav-item"><a href="your_orders.php" class="nav-link active">My Orders</a> </li>';
    echo '<li class="nav-item"><a href="logout.php" class="nav-link active">Logout</a> </li>';
}
 ?>
 </ul>
 </div>
 </div>
 </nav>
 </header>
</div>
 <div class="page-wrapper">
 <div class="container">
 <span style="color:green;"><?php echo $success; ?></span>
 </div>
 <div class="container m-t-30">
 <form action="" method="post">
 <div class="widget clearfix">
 <div class="widget-body">
 <div class="row">
 <div class="col-sm-12">
 <div class="cart-totals margin-b-20">
 <div class="cart-totals-title">
 <h4>Cart Summary</h4>
 </div>
 <div class="cart-totals-fields">
 <table class="table">
 <tbody>
 <tr>
 <td>Cart Subtotal</td>
 <td><?php echo "E".$item_total; ?></td>
 </tr>
 <tr>
 <td>Delivery Charges</td>
 <td>
 <?php
 // Display delivery charges based on selected option
 if ($delivery_option == 'delivery') {
     echo "E".$delivery_charges;
 } else {
     echo "E0.00"; // Pickup has no delivery charges
 }
 ?>
 </td>
 </tr>
 <tr>
 <td class="text-color"><strong>Total</strong></td>
 <td class="text-color"><strong><?php echo "E".($item_total + $delivery_charges); ?></strong></td>
 </tr>
 </tbody>
 </table>
 </div>
 </div>
 <div class="payment-option">
 <ul class="list-unstyled">
 <li>
 <label class="custom-control custom-radio m-b-20">
 <input name="delivery_option" id="delivery" value="delivery" type="radio" class="custom-control-input">
 <span class="custom-control-indicator"></span>
 <span class="custom-control-description">Delivery</span>
 </label>
 </li>
 <li>
 <label class="custom-control custom-radio m-b-20">
 <input name="delivery_option" id="pickup" value="pickup" type="radio" class="custom-control-input" checked>
 <span class="custom-control-indicator"></span>
 <span class="custom-control-description">Pickup</span>
 </label>
 </li>
 </ul>
 <p class="text-xs-center">
 <input type="submit" onclick="return confirm('Do you want to confirm the order?');" name="submit" class="btn btn-success btn-block" value="Order Now">
 </p>
 </div>
 </div>
 </div>
 </div>
 </form>
 </div>
 </div>
</body>
</html>
