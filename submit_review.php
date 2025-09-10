<?php
 include("connection/connect.php");
 session_start();
 if ($_SERVER["REQUEST_METHOD"] == "POST") {
 $rating = intval($_POST['rating']);
 $review = mysqli_real_escape_string($db, $_POST['review']);
 $farmer_id = intval($_POST['farmer_id']);
 $user_id = $_SESSION["user_id"];
 if (!empty($rating) && !empty($review) && !empty($farmer_id) && !empty($user_id)) {
 $query = "INSERT INTO reviews (user_id, farmer_id, rating, review, date) VALUES ('$user_id', '$farmer_id', '$rating', '$review', NOW())";
        if(mysqli_query($db, $query)) {
         echo "<script> alert('Thank you for your review!'); window.location.href = 'products.php?res_id=$farmer_id';</script>";
        }
        }
    }
 else {
 $_SESSION['error'] = "Error: Could not submit review. Please try again.";
 
 $_SESSION['error'] = "All fields are required!";
    
    header("Location: farmer_profile.php?res_id=$farmer_id");
    
 $_SESSION['error'] = "Invalid request method!";
    header("Location: index.php");
    exit();
 }
 ?>