<?php
session_start();

if (isset($_POST['userLat']) && isset($_POST['userLon'])) {
    $_SESSION['userLat'] = $_POST['userLat'];
    $_SESSION['userLon'] = $_POST['userLon'];
}
?>
