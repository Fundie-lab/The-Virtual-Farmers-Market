<?php
include("connection/connect.php");
session_start();

if (empty($_SESSION["user_id"])) {
    header('location:login.php');
}else{



if ($_SERVER["REQUEST_METHOD"] == "POST") {
 $message = $_POST['message'];
 $product_id = $_POST['product_id'];
 $farmer_id = $_POST['farmer_id'];
 $user_id = $_SESSION["user_id"]; // Assuming the user is logged in and their ID is stored in the session
 // Validate input
 if (empty($message) || empty($product_id) || empty($farmer_id)) {
 // Handle error - required fields missing
 header("Location: products.php?res_id=$farmer_id&error=1");
 exit();
 }
 // Insert message into database
 $sql = "INSERT INTO messages (user_id, farmer_id, product_id, message, created_at) 
VALUES (?, ?, ?, ?, NOW())";
 $stmt = $db->prepare($sql);
 $stmt->bind_param("iiis", $user_id, $farmer_id, $product_id, $message);
 if ($stmt->execute()) {
 // Redirect back to the products page with success message
 header("Location: productfarmer.php?res_id=$farmer_id&success=1");
 } else {
 // Handle error - failed to insert message
 header("Location: products.php?res_id=$farmer_id&error=2");
 }
 $stmt->close();
 $db->close();
}
}
?>