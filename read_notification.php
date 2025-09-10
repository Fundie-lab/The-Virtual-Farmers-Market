<?php
include("connection/connect.php");
session_start();
if (isset($_GET['id']) && !empty($_SESSION['user_id'])) {
 $notification_id = $_GET['id'];
 $u_id = $_SESSION['user_id'];
 $update_query = "UPDATE notifications SET read_status = 1 WHERE id = '$notification_id' AND u_id = '$u_id'";
 mysqli_query($db, $update_query);
}
header('Location: index.php'); // Replace with the page where you want to redirect the user
exit;
?>