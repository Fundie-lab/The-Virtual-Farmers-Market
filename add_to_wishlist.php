<?php
include("connection/connect.php");
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['user_id']) && isset($input['product_id'])) {
        $user_id = $input['user_id'];
        $product_id = $input['product_id'];

        // Check if the product is already in the wishlist
        $check_query = mysqli_query($db, "SELECT * FROM wishlist WHERE user_id = '$user_id' AND product_id = '$product_id'");
        if (mysqli_num_rows($check_query) == 0) {
            // Insert the product into the wishlist
            $query = "INSERT INTO wishlist (user_id, product_id) VALUES ('$user_id', '$product_id')";
            if (mysqli_query($db, $query)) {
                echo json_encode(['success' => true]);
                exit;
            }
        } else {
            // Remove the product from the wishlist
            $query = "DELETE FROM wishlist WHERE user_id = '$user_id' AND product_id = '$product_id'";
            if (mysqli_query($db, $query)) {
                echo json_encode(['success' => true]);
                exit;
            }
        }
    }
}

echo json_encode(['success' => false]);
?>
