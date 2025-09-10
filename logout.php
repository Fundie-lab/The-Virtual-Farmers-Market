<?php
session_start();
include("connection/connect.php");  // Include your database connection

// Assuming the user ID is stored in the session
$user_id = $_SESSION['user_id'];

// Check if the user is logged in
if (isset($user_id)) {
    // Check if there are any items in the cart
    $sql = "SELECT COUNT(*) AS item_count FROM cart_items WHERE user_id = ?";
    
    /* if ($stmt = $db->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($item_count);
        $stmt->fetch();
        $stmt->close();

        If there are items in the cart, create a notification
        if ($item_count > 0) {
            $cart_reminder_message = "You have items in your cart that you haven't checked out.";
            $read_status = 'unread'; // Default status

            // Insert the notification into the database
            $insert_notification = "INSERT INTO notifications (u_id, message, read_status) VALUES (?, ?, ?)";
            
            if ($stmt = $db->prepare($insert_notification)) {
                $stmt->bind_param("sss", $user_id, $cart_reminder_message, $read_status);
                $stmt->execute();
                $stmt->close();
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        }
    } else {
        echo "Error preparing statement: " . $conn->error;
    }*/

    // Destroy the session and log out the user
    session_unset();
    session_destroy();

    // Redirect to login or home page
    header("Location: login.php"); // Or your desired location
    exit();
}
?>
