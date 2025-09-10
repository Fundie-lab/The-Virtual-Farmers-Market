<?php
include("connection/connect.php");
session_start();
if(empty($_SESSION['user_id'])) {
 echo "You must be logged in to update your profile.";
 exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
 $user_id = intval($_POST['user_id']);
 $field = $_POST['field'];
 $value = $_POST['value'];
 // Validate the input
 if ($field == 'email') {
 if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
 echo "Invalid email format.";
 exit;
 }
 // Check if the email already exists
 $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND u_id != ?");
 $stmt->bind_param("si", $value, $user_id);
 $stmt->execute();
 $result = $stmt->get_result();
 if ($result->num_rows > 0) {
 echo "This email is already registered with another account.";
 exit;
 }
 }

if ($field == 'username') {
 
 // Check if the email already exists
 $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND u_id != ?");
 $stmt->bind_param("si", $value, $user_id);
 $stmt->execute();
 $result = $stmt->get_result();
 if ($result->num_rows > 0) {
 echo "This username is already registered with another account, Please use another one.";
 exit;
 }
 }
 // Update the user's profile
 $stmt = $db->prepare("UPDATE users SET $field = ? WHERE u_id = ?");
 $stmt->bind_param("si", $value, $user_id);
 if ($stmt->execute()) {
 echo "Profile updated successfully.";
 } else {
 echo "Error updating profile.";
 }
 $stmt->close();
 $db->close();
} else {
 echo "Invalid request.";
}
?>