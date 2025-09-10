<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php"); 
error_reporting(0);
session_start();

//include_once 'product-action.php'; 

$market_id = $_GET['res_id'];
$market_query = "SELECT * FROM market where mar_id = '$_GET[res_id]'";
$market_result = mysqli_query($db, $market_query);
$market_row = mysqli_fetch_assoc($market_result);

function time_elapsed_string($datetime, $full = false) {
 $now = new DateTime;
 $ago = new DateTime($datetime);
 $diff = $now->diff($ago);
 $diff->w = floor($diff->d / 7);
 $diff->d -= $diff->w * 7;
 $string = array(
 'y' => 'year',
 'm' => 'month',
 'w' => 'week',
 'd' => 'day',
 'h' => 'hour',
 'i' => 'minute',
 's' => 'second',
 );
 foreach ($string as $k => &$v) {
 if ($diff->$k) {
 $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
 } else {
 unset($string[$k]);
 }
 }
 if (!$full) $string = array_slice($string, 0, 1);
 return $string ? implode(', ', $string) . ' ago' : 'just now';
}

?>

<head>
 <meta charset="utf-8">
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
 <meta name="description" content="">
 <meta name="author" content="">
 <title>Products</title>
 <link href="css/bootstrap.min.css" rel="stylesheet">
 <link href="css/font-awesome.min.css" rel="stylesheet">
 <link href="css/animsition.min.css" rel="stylesheet">
 <link href="css/animate.css" rel="stylesheet">
 <link href="css/style.css" rel="stylesheet">
</head>
<body>
 <header id="header" class="header-scroll top-header headrom">
 <nav class="navbar navbar-dark">
 <div class="container">
 <button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse" 
data-target="#mainNavbarCollapse">&#9776;</button>
 <a class="navbar-brand" href="index.php"> <img class="img-rounded" src="images/
icn.png" alt=""> </a>
 <div class="collapse navbar-toggleable-md float-lg-right" id="mainNavbarCollapse">
 <ul class="nav navbar-nav">
 <li class="nav-item"> <a class="nav-link active" href="index.php">Home <span 
class="sr-only">(current)</span></a> </li>
 <li class="nav-item"> <a class="nav-link active" href="../onlinefarm/farmer/
index.php">Farmer Account <span class="sr-only"></span></a> </li>
 <?php
 if(empty($_SESSION["user_id"])) {
 echo '<li class="nav-item"><a href="login.php" class="nav-link 
active">Login</a> </li>
 <li class="nav-item"><a href="registration.php" class="nav-link 
active">Register</a> </li>';
 } else {
 echo '<li class="nav-item"><a href="your_orders.php" class="nav-link 
active">My Orders</a> </li>';
 echo '<li class="nav-item"><a href="logout.php" class="nav-link 
active">Logout</a> </li>';
 }
 ?>
 </ul>
 </div>
 </div>
 </nav>
 </header>
 <div class="page-wrapper">
 <section class="inner-page-hero bg-image" data-image-src="images/img/backg.png">
 <div class="profile">
 <div class="container">
 <div class="row">
 <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 profile-img">
 <div class="image-wrap">
 <figure><img src="farmer/Res_img/<?php echo $market_row['image']; ?>" 
alt="farmer logo"></figure>
 </div>
 </div>
 <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 profile-desc">
 <div class="pull-left right-text white-txt">
 <h6><a href="#"><?php echo $market_row['title']; ?></a></h6>
 <p>Email Address: <a href="mailto:<?php echo $market_row['email']; ?>"><?php echo $market_row['email']; ?></a></p>
 <p>Phone Number: <a href="tel:<?php echo $market_row['phone']; ?>"><?php echo $market_row['phone']; ?></a></p>
 <p>Website: <a href="https://<?php echo $market_row['url']; ?>"><?php 
echo $market_row['url']; ?></a></p>
 <p>Operating hours: <?php echo $market_row['o_hr']; ?> - <?php echo 
$market_row['c_hr']; ?></p>
 <p>Days of operation: <?php echo $market_row['o_days']; ?></p>
 <p>Farm Size: <?php echo $market_row['market_size']; ?></p>
 <p>Physical Address: <?php echo $market_row['address']; ?></p>
 </div>
 </div>
 </div>
 </div>
 </div>
 </section>
 <div class="container m-t-30">
 <div class="row">
 <div class="col-md-2">
 <!-- Optional sidebar or additional widgets -->
 </div>
 <div class="col-md-8">
 <div class="menu-widget" id="2">
 <div class="widget-heading">
 <h3 class="widget-title text-dark"><?php echo $market_row['title']; ?> sellers' 
available products</h3>
 <a class="btn btn-link pull-right" data-toggle="collapse" href="#popular2" 
aria-expanded="true">
 <i class="fa fa-angle-right pull-right"></i>
 <i class="fa fa-angle-down pull-right"></i>
 </a>
 <div class="clearfix"></div>
 </div>
 <div class="collapse in" id="popular2">
 <?php
 $product_query = "SELECT DISTINCT p.* FROM products p join product_market pm on p.d_id = pm.product_id WHERE pm.market_id = '$market_id'";
$product_result = mysqli_query($db, $product_query);

if (mysqli_num_rows($product_result) > 0){

 while ($product = mysqli_fetch_assoc($product_result)) {
 $listedDate = new DateTime($product['listing_date']);
 $currentDate = new DateTime();
 $interval = $listedDate->diff($currentDate);
 $daysAgo = $interval->days; 
 ?>
 <div class="food-item">
 <div class="row">
 <div class="col-xs-12 col-sm-12 col-lg-8">
 <form method="post" action='marketproducts.php?res_id=<?php
    echo $_GET['res_id'];?>&action=add&id=<?php echo $product['d_id']; ?>'>
 <div class="rest-logo pull-left">
 <a class="restaurant-logo pull-left" href="#">
 <img src="farmer/Res_img/products/<?php echo 
$product['img']; ?>" alt="product logo">
 </a>
 </div>
 <div class="rest-descr">
 <h6><a href="#"><?php echo $product['title']; ?></a></h6>
 <span><?php echo $product['slogan']; ?></span><br>
 <p>Listed  <?php echo time_elapsed_string($product['created_at']); ?></p>
 </div> 
 </form>
 </div>
 </div>
 </div>
 <?php
}
 } else {
    echo '<p>No products.</p>';
 }
 ?>
 </div>
 </div>
 </div>
 </div>
 </div>
 <footer class="footer">
 <div class="container">
 <div class="bottom-footer">
 <div class="row">
 <div class="col-xs-12 col-sm-4 address color-gray">
 <h5>Address</h5>
 <p>Eswatini Medical Christian University, <br> Lomkiri, Zone 4, <br> Mbabane, 
Eswatini</p>
 <h5>Phone: (+268) 7943 9397</h5>
 </div>
 <div class="col-xs-12 col-sm-5 additional-info color-gray">
 <h5>Additional Information</h5>
 <p>Join thousands of other farmers who benefit from having partnered with 
us.</p>
 </div>
 </div>
 </div>
 </div>
 </footer>
 </div>
<script src="js/jquery.min.js"></script>
    <script src="js/tether.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/animsition.min.js"></script>
    <script src="js/bootstrap-slider.min.js"></script>
    <script src="js/jquery.isotope.min.js"></script>
    <script src="js/headroom.js"></script>
    <script src="js/foodpicky.min.js"></script>
</body>

</html>
