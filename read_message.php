<?php
include("connection/connect.php");
session_start();
if (isset($_GET['message_id']) && !empty($_SESSION['user_id'])) {
 $message_id = $_GET['id'];
 $u_id = $_SESSION['user_id'];
 $update_query = "UPDATE messages SET read_status = 1 WHERE message_id = '$message_id' AND user_id = '$u_id'";
 mysqli_query($db, $update_query);
}
header('Location: index.php'); // Replace with the page where you want to redirect the user
exit;
?>