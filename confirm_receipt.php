<?php
include("connection/connect.php");
session_start();

if(isset($_POST['order_id']) && !empty($_SESSION['user_id'])) {
    $order_id = $_POST['order_id'];
    
    // Update customer_confirm to 'received'
    $query = "UPDATE users_orders SET customer_confirm='received' WHERE o_id='$order_id' AND u_id='".$_SESSION['user_id']."'";
    
    if(mysqli_query($db, $query)) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
