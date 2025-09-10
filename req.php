<?php
include("connection/connect.php");  
error_reporting(0);
session_start();

if(empty($_SESSION["user_id"])) {
    header('location:login.php');
    exit();
} 
else {

if(isset($_POST['submit'])) {
    if(empty($_POST['product_name']) || empty($_POST['description']) || empty($_POST['delivery_date']) || empty($_POST['quantity'])) {
        $error = '<div class="alert alert-danger alert-dismissible fade show">
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <strong>All fields must be filled!</strong>
                  </div>';
    } else {
        $fname = $_FILES['file']['name'];
        $temp = $_FILES['file']['tmp_name'];
        $fsize = $_FILES['file']['size'];
        $extension = strtolower(pathinfo($fname, PATHINFO_EXTENSION));  
        $fnew = uniqid().'.'.$extension;
        $store = "farmer/Res_img/products/".basename($fnew);
        
        if($extension == 'jpg' || $extension == 'png' || $extension == 'gif') {        
            if($fsize >= 1000000) {
                $error = '<div class="alert alert-danger alert-dismissible fade show">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                              <strong>Max Image Size is 1024kb!</strong> Try a different image.
                          </div>';
            } else {
                $u_id = $_SESSION['user_id'];
                $listing_date = date('Y-m-d');
                $delivery_date = $_POST['delivery_date'];
                
                $sql = "INSERT INTO product_requests (u_id, product_name, description, quantity, delivery_date, listing_date, img) 
                        VALUES ('$u_id', '".$_POST['product_name']."', '".$_POST['description']."', '".$_POST['quantity']."', '$delivery_date', '$listing_date', '$fnew')";
                mysqli_query($db, $sql); 
                move_uploaded_file($temp, $store);
                
                $success = '<div class="alert alert-success alert-dismissible fade show">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                New Product Added Successfully.
                            </div>';
            }
        } elseif($extension == '') {
            $error = '<div class="alert alert-danger alert-dismissible fade show">
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          <strong>Select an image</strong>
                      </div>';
        } else {
            $error = '<div class="alert alert-danger alert-dismissible fade show">
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          <strong>Invalid extension!</strong> png, jpg, gif are accepted.
                      </div>';
        }
    }
}
?>