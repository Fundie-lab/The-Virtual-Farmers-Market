<?php include('language_switcher.php'); ?>
<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php");
error_reporting(0);
session_start();
if (empty($_SESSION['user_id'])) {
 header('location:login.php');
} else {
?>
<head>
 <meta charset="utf-8">
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
 <meta name="description" content="">
 <meta name="author" content="">
 <link rel="icon" href="#">
 <title>My profile</title>
 <link href="css/bootstrap.min.css" rel="stylesheet">
 <link href="css/font-awesome.min.css" rel="stylesheet">
 <link href="css/animsition.min.css" rel="stylesheet">
 <link href="css/animate.css" rel="stylesheet">
 <link href="css/style.css" rel="stylesheet">
 <style type="text/css" rel="stylesheet">
 .indent-small {
 margin-left: 5px;
 }
 .form-group.internal {
 margin-bottom: 0;
 }
 .dialog-panel {
 margin: 10px;
 }
 .datepicker-dropdown {
 z-index: 200 !important;
 }
 .panel-body {
 background: #e5e5e5;
 font: 600 15px "Open Sans", Arial, sans-serif;
 }
 label.control-label {
 font-weight: 600;
 color: #777;
 }
 </style>
 <link rel="stylesheet" href="css/navstyle.css">
</head>
<body>
<?php
        include("navbar.php");
    ?>
<div class="page-wrapper">
 <div class="inner-page-hero bg-image" data-image-src="images/img/pimg.jpg">
 <div class="container"> </div>
 </div>
 <div class="result-show">
 <div class="container">
 <div class="row"></div>
 </div>
 </div>
 <section class="restaurants-page">
 <div class="container">
 <div class="row">
 <div class="col-xs-2"></div>
 <div class="col-xs-8">
 <div class="bg-gray">
 <div class="row">
 <?php 
 $query_res = mysqli_query($db, "SELECT * from users where u_id='" . 
$_SESSION['user_id'] . "'");
 while ($row = mysqli_fetch_array($query_res)) {
 ?> 
 <table class="table table-bordered table-hover">
 <thead style="background: #404040; color:white;">
 <tr>
 <td colspan="1"><b><?php echo $row['f_name']; ?>'s profile</b></td>
 <td colspan="1"><b>Click row to edit</b></td>
 <td colspan="1"><b>Action</b></td>
 </tr>
 </thead>
 <tbody>
 <tr>
 <td><b>Username:</b></td>
 <td contenteditable="true" id="username"><?php echo htmlentities($row['username']); ?></td>
 <td><button class="btn btn-primary btn-flat btn-addon btn-xs m-b-10" onclick="saveChanges('username', <?php echo $row['u_id']; ?>)">Save</button></td>
 </tr>
 <tr>
 <td><b>Reg Date:</b></td>
 <td><?php echo htmlentities($row['date']); ?></td>
 <td></td>
 </tr>
 <tr>
 <td><b>First Name:</b></td>
 <td contenteditable="true" id="f_name"><?php echo htmlentities($row['f_name']); ?></td>
 <td><button class="btn btn-primary btn-flat btn-addon btn-xs m-b-10" onclick="saveChanges('f_name', <?php echo $row['u_id']; ?>)">Save</button></td>
 </tr>
 <tr>
 <td><b>Last Name:</b></td>
 <td contenteditable="true" id="l_name"><?php echo htmlentities($row['l_name']); ?></td>
 <td><button class="btn btn-primary btn-flat btn-addon btn-xs m-b-10" onclick="saveChanges('l_name', <?php echo $row['u_id']; ?>)">Save</button></td>
 </tr>
 <tr>
 <td><b>User Email:</b>
 <td contenteditable="true" id="email"><?php echo htmlentities($row['email']); ?></td>
 <td><button class="btn btn-primary btn-flat btn-addon btn-xs m-b-10" onclick="saveChanges('email', <?php echo $row['u_id']; ?>)">Save</button></td>
 </tr>
 <tr>
 <td><b>User Phone:</b></td>
 <td contenteditable="true" id="phone"><?php echo htmlentities($row['phone']); ?></td>
 <td><button class="btn btn-primary btn-flat btn-addon btn-xs m-b-10" onclick="saveChanges('phone', <?php echo $row['u_id']; ?>)">Save</button></td>
 </tr>
 <tr>
 <td><b>Address:</b></td>
 <td contenteditable="true" id="address"><?php echo htmlentities($row['address']); ?></td>
 <td><button class="btn btn-primary btn-flat btn-addon btn-xs m-b-10" onclick="saveChanges('address', <?php echo $row['u_id']; ?>)">Save</button></td>
 </tr>
 </tbody>
 </table>
 <?php } ?> 
 </div>
 </div>
 </div>
 </div>
 </div>
 </section>
 <footer class="footer">
 <div class="container">
 <div class="bottom-footer">
 <div class="row">
 <div class="col-xs-12 col-sm-3 payment-options color-gray">
 <h5>Payment Options</h5>
 <ul>
 <li><a href="#"><img src="images/paypal.png" alt="Paypal"></a></li>
 <li><a href="#"><img src="images/mastercard.png" alt="Mastercard"></a></li>
 <li><a href="#"><img src="images/maestro.png" alt="Maestro"></a></li>
 <li><a href="#"><img src="images/stripe.png" alt="Stripe"></a></li>
 <li><a href="#"><img src="images/bitcoin.png" alt="Bitcoin"></a></li>
 </ul>
 </div>
 <div class="col-xs-12 col-sm-4 address color-gray">
 <h5>Address</h5>
 <p>Eswatini Medical Christian University,<br> Lomkiri, Zone 4,<br> Mbabane, 
Eswatini</p>
 <h5>Phone: (+268) 7943 9397</h5>
 </div>
 <div class="col-xs-12 col-sm-5 additional-info color-gray">
 <h5>Additional Information</h5>
 <p>Join thousands of other farmers who benefit from having partnered with us.</p>
 </div>
 </div>
 </div>
 </div>
 </footer>
 <script src="js/jquery.min.js"></script>
 <script src="js/tether.min.js"></script>
 <script src="js/bootstrap.min.js"></script>
 <script src="js/animsition.min.js"></script>
 <script src="js/bootstrap-slider.min.js"></script>
 <script src="js/jquery.isotope.min.js"></script>
 <script src="js/headroom.js"></script>
 <script src="js/foodpicky.min.js"></script>
 <script>
 function saveChanges(field, user_id) {
 const value = document.getElementById(field).innerText;
 console.log(`Saving changes for field: ${field}, value: ${value}, user_id: ${user_id}`); // Debugging
 $.ajax({
 url: 'update_profile.php',
 type: 'POST',
 data: {
 field: field,
 value: value,
 user_id: user_id
 },
 success: function(response) {
 console.log(`Response from server: ${response}`); // Debugging
 alert(response);
 },
 error: function(xhr, status, error) {
 console.error(`Error: ${xhr.responseText}`); // Debugging
 alert("An error occurred: " + xhr.responseText);
 }
 });
 }
 </script>
</body>
</html>
<?php
}
?>