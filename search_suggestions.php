<?php
include("connection/connect.php");  // Include your database connection file

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $sql = "SELECT title FROM products WHERE title LIKE '%$query%' LIMIT 10";
    $result = mysqli_query($db, $sql);
    $suggestions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $suggestions[] = $row['title'];
    }
    echo json_encode($suggestions);
}
?>
