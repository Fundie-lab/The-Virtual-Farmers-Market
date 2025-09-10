<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php"); 
error_reporting(0);

session_start();


//$user_id = $_SESSION['user_id'];
//$farmer_id = $_GET['res_id'];
//$product_id = $_GET['d_id'];



//$track_click_query = "INSERT INTO user_behavior (user_id, product_id, behavior_type) VALUES ($user_id, $product_id, 'click')";
//mysqli_query($db, $track_click_query);


















$successMessage = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$errorMessage = isset($_SESSION['error']) ? $_SESSION['error'] : '';

unset($_SESSION['success']);
unset($_SESSION['error']);

include_once 'product-action.php'; 

 $far = mysqli_query($db, "SELECT * FROM farmer WHERE rs_id='$_GET[res_id]'");
 $ro = mysqli_fetch_array($far);
 // Fetch all reviews for this farmer and calculate the average rating
 $review_sql = "SELECT rating FROM reviews WHERE farmer_id='$_GET[res_id]'";
 $review_result = mysqli_query($db, $review_sql);
 $total_rating = 0;
 $review_count = 0;
 while ($review_row = mysqli_fetch_assoc($review_result)) {
    $total_rating += $review_row['rating'];
    $review_count++;
 }
 $average_rating = $review_count > 0 ? $total_rating / $review_count : 0;
 $average_rating = round($average_rating, 1); // Round to 1 decimal place





$sql = "SELECT * FROM growing WHERE estimated_readiness_date > CURDATE()";
$result = mysqli_query($db, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Calculate the progress based on the remaining time until the estimated readiness date
        $listingDate = strtotime($row['listingDate']);
        $estimatedDate = strtotime($row['estimated_readiness_date']);
        $currentDate = time();
        $totalTime = $estimatedDate - $listingDate;
        $elapsedTime = $currentDate - $listingDate;
        //$totalTimeDiff = strtotime('+3 months', $currentDate) - $currentDate; // Assuming 3 months until readiness
        $progress1 = min(100, max(0, floor((1 - ($elapsedTime / $totalTime)) * 100)));
        $progress = 100 - $progress1;

        // Update the progress in the database
        $productId = $row['d_id'];
        $updateSql = "UPDATE growing SET progress = $progress WHERE d_id = $productId";
        mysqli_query($db, $updateSql);

        // Optionally, send notifications to farmers about the updated progress
    }
}

?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="#">
    <title>Products</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style type="text/css">
        .rating {
            display: : inline-block;
            font-size: 50px;
            color: gold;
        }
        .star {
            margin-right: 5px;
        }
        .rating-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        .rating-popup.active {
            display: block;
        }
        .rating-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        .rating-overlay.active {
            display: block;
        }

/* General Navbar Styling */
.navbar {
 background-color: #ffffff; /* White background */
 box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
 padding: 1rem 2rem; /* Padding for spacious look */
 transition: background-color 0.3s ease;
}
.navbar-brand {
 font-size: 1.5rem; /* Larger brand font size */
 color: #333; /* Brand color */
}
.navbar-nav .nav-item {
 margin-left: 1rem; /* Space between nav items */
}
.navbar-nav .nav-link {
 color: #555; /* Link color */
 font-size: 1rem; /* Link font size */
 transition: color 0.3s ease;
}
.navbar-nav .nav-link:hover,
.navbar-nav .nav-link.active {
 color: #007bff; /* Hover and active link color */
}
/* Dropdown Styling */
.dropdown-menu {
 min-width: 300px; /* Minimum width */
 padding: 0.5rem 1rem; /* Padding inside dropdown */
 box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Dropdown shadow */
 border-radius: 0.5rem; /* Rounded corners */
 transition: all 0.3s ease;
}
.dropdown-user {
 list-style: none; /* Remove list bullets */
 padding: 0; /* Remove default padding */
 margin: 0; /* Remove default margin */
}
.dropdown-user li {
 padding: 0.5rem 0; /* Padding for each item */
}
.dropdown-user li a {
 color: #333; /* Item color */
 text-decoration: none; /* Remove underline */
 display: block; /* Make full block clickable */
 transition: background-color 0.3s ease;
}
.dropdown-user li a:hover {
 background-color: #f8f9fa; /* Hover background color */
}
/* Profile Picture */
.profile-pic {
 border-radius: 50%; /* Circular profile picture */
 width: 30px; /* Fixed width */
 height: 30px; /* Fixed height */
}
/* Notifications Badge */
.badge {
 position: absolute;
 top: 10px;
 right: 10px;
 padding: 0.25rem 0.5rem;
 border-radius: 50%;
 background-color: #dc3545; /* Red badge color */
 color: #fff; /* White text color */
}
.unread-notification {
    font-weight: bold;
    background-color: #f5f5f5;
    color: #333;
}
/* Responsive Adjustments */
@media (max-width: 768px) {
 .navbar {
 padding: 0.5rem 1rem;
 }
 .navbar-nav .nav-item {
 margin-left: 0;
 margin-bottom: 0.5rem;
 }
 .dropdown-menu {
 width: 100%;
 }
}
    </style> 
</head>

<body>
    
        <header id="header" class="header-scroll top-header headrom">
            <nav class="navbar navbar-dark">
                <div class="container">
                    <a class="navbar-brand" href="index.php"> <img class="img-rounded" src="images/icn.png" alt=""> </a>
                     <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <div class="collapse navbar-toggleable-md  float-lg-right" id="mainNavbarCollapse">
                        <ul class="nav navbar-nav">
                            <li class="nav-item"> <a class="nav-link active" href="index.php">Home <span class="sr-only">(current)</span></a> </li>
                            <li class="nav-item"> <a class="nav-link active" href="../onlinefarm/farmer/index.php">Farmer<span class="sr-only"></span></a> </li>
                            <li class="nav-item"> <a class="nav-link active" href="../onlinefarm/all_products.php">Products <span class="sr-only"></span></a> </li>
                            
                           
                            <!-- Inside the navigation bar section -->
                    <!-- Inside the navigation bar section -->
<?php if(empty($_SESSION["user_id"])): ?>
 <li class="nav-item"><a href="login.php" class="nav-link active">Login</a></li>
 <li class="nav-item"><a href="registration.php" class="nav-link active">Register</a></li>
<?php else: ?>

     <?php
 $u_id = $_SESSION['user_id'];
 $orders_query = "SELECT * FROM users_orders WHERE u_id = '$u_id'";
 $orders_result = mysqli_query($db, $orders_query);
 $orders_count = mysqli_num_rows($orders_result);
 ?>
 <li class="nav-item"><a href="your_orders.php" class="nav-link active"> <span>My Orders (<?= $orders_count ?>)</span></a></li>
 <li class="nav-item"><a href="customer_requests.php" class="nav-link active">List a product request</a></li>
 <?php $ress= mysqli_query($db,"select * from users where u_id='".$_SESSION["user_id"]."'");
                                    $row=mysqli_fetch_array($ress); 
                             //echo '<a><span>Hello,' $rows['usernane']; '</span></a>'
                                    echo '<li class="nav-item"><a class="nav-link active">Hi, '.$row['username'].'</a></li>'; ?>






 <!-- Notifications dropdown -->
 <li class="nav-item dropdown">
 <a class="nav-link dropdown-toggle text-muted" href="#" id="notificationsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
 <i class="fa fa-bell"></i> <!-- FontAwesome bell icon -->
 <?php
 $u_id = $_SESSION['user_id'];
 $notifications_query = "SELECT * FROM notifications WHERE u_id = '$u_id' AND read_status = 0";
 $notifications_result = mysqli_query($db, $notifications_query);
 $unread_count = mysqli_num_rows($notifications_result);
 ?>
 <span><?= $unread_count ?> New notifications</span>
 </a>
 <div class="dropdown-menu dropdown-menu-right animated zoomIn" aria-labelledby="notificationsDropdown">
 <!-- List of notifications -->
 <ul class="dropdown-user">
 <?php
 $notif_query = mysqli_query($db, "SELECT * FROM notifications WHERE u_id = '$u_id' ORDER BY created_at DESC LIMIT 10");

 if (mysqli_num_rows($notif_query) > 0) {
     
 while ($notification = mysqli_fetch_assoc($notif_query)) {
 $notification_id = $notification['id'];

 echo '<li class="nav-item"><a href="read_notification.php?id=' . $notification_id . '"><i class="fa fa-info-circle"></i> ' . $notification['message'] . '</a></li>';
 } 
}else {
  echo '<li class="nav-item"><a href="#"><i class="fa fa-info-circle"></i>No new notifications</a></li>';
 }
 ?>
 </ul>
 </div>
 </li>






 <li class="nav-item dropdown">
 <a class="nav-link dropdown-toggle text-muted" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
 <img src="farmer/images/bookingSystem/user.png" alt="user" class="profile-pic"/>
 </a>
 <div class="dropdown-menu dropdown-menu-right animated zoomIn">
 <ul class="dropdown-user">
 <li class="nav-item"><a href="user.php"><i class="fa fa-user"></i>My Profile</a></li>
 <li class="nav-item"><a href="logout.php"><i class="fa fa-power-off"></i>Logout</a></li>
 </ul>
 </div>
 </li>
<?php endif; ?>





                            
                        </ul>
                         
                    </div>
                </div>
            </nav>



        </header>
        <div class="page-wrapper">
            <div class="top-links">
                <div class="container">
                    <ul class="row links">                        
                    </ul>
                </div>
            </div>
			<?php $ress= mysqli_query($db,"select * from farmer where rs_id='$_GET[res_id]'");
									     $rows=mysqli_fetch_array($ress);
										  
										  ?>
            <section class="inner-page-hero bg-image" data-image-src="images/img/backg.png">
                <div class="profile">
                    <div class="container">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12  col-md-4 col-lg-4 profile-img">
                                <div class="image-wrap">
                                    <figure><?php echo '<img src="farmer/Res_img/'.$rows['image'].'" alt="farmer logo">'; ?></figure>
                                </div>
                            </div>
							
                            <div class="col-xs-12 col-sm-12 col-md-8 col-lg-4 profile-desc">
                                <div class="pull-left right-text white-txt">
                                    <h6><a href="#"><?php echo $rows['title']; ?></a></h6>
                                    <p>Email Address: <a href="mailto:"><?php echo $rows['email']; ?></a></p>
                                    <p>Phone Number: <a href="tel:"><?php echo $rows['phone']; ?></a></p>
                                    <p>Website: <a href="https://<?php echo $rows['url']; ?>"><?php echo $rows['url']; ?></a></p>
                                    <p>Operating hours: <?php echo $rows['o_hr']; ?> - <?php echo $rows['c_hr']; ?></p>
                                    <p>Days of operation: <?php echo $rows['o_days']; ?></p>
                                    <p>Farm Size: <?php echo $rows['Farm_size']; ?></p>
                                    <p>Physical Address: <?php echo $rows['address']; ?></p>
                                </div>
                            </div>
                            <?php

                             $orders_q = "SELECT * FROM users_orders WHERE u_id = '$u_id' AND rs_id='$_GET[res_id]'";
                             $orders_res = mysqli_query($db, $orders_q);
                             $orders_co = mysqli_num_rows($orders_res);

                            if ($orders_co > 0) {    
                            
                            ?>
                            <div class="col-xs-12 col-sm-12 col-md-8 col-lg-4 profile-desc">
                                <div class="pull-left right-text white-txt"><br><br><br><br>
                                    <a href="#" id="rateFarmerBtn" class="btn btn-primary">Rate & Review Farmer</a> 
                                    <div class="rating">
                                    <?php for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $average_rating) {
                                            echo '<i class="fa fa-star star"></i>';
                                        } else {
                                            echo '<i class="fa fa-star-o star"></i>';
                                        }
                                    } ?>
                                    
                                </div>
                                <span>(<?php echo $average_rating; ?> stars)</span>
                                
                                </div>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="col-xs-12 col-sm-12 col-md-8 col-lg-4 profile-desc">
                                <div class="pull-left right-text white-txt"><br><br><br><br>
                                    <div class="rating">
                                    <?php for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $average_rating) {
                                            echo '<i class="fa fa-star star"></i>';
                                        } else {
                                            echo '<i class="fa fa-star-o star"></i>';
                                        }
                                    } ?>
                                    
                                </div>
                                <span>(<?php echo $average_rating; ?> stars)</span>
                                
                                </div>
                            </div>
                        <?php 
                        }
                        ?>
							
							
                        </div>
                    </div>
                </div>
            </section>

             <!-- Reviews Section -->
<section href="#" id="cart">
<div class="col-md-12">     
<div class="menu-widget" id="2">
<div class="widget-heading">
    <h3 class="widget-title text-dark">
          Customer Reviews <a class="btn btn-link pull-right" data-toggle="collapse" href="#popularr" aria-expanded="false">
      <i class="fa fa-angle-right pull-right"></i>
      <i class="fa fa-angle-down pull-right"></i>
      </a>
    </h3>
    <div class="clearfix"></div>
    </div>
    <div class="collapse out" id="popularr">             
        <div class="food-item">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                    
                    <?php 

                    $revql = "SELECT * FROM reviews WHERE farmer_id='$_GET[res_id]'";
                    $reviews = mysqli_query($db, $revql);
                    //print_r($reviews);

                    if (!empty($reviews)): ?>
                        <ul class="list-unstyled">
                            <?php foreach ($reviews as $review): ?>
                                <li>
                                    <div>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fa fa-star <?php echo $i <= $review['rating'] ? 'gold' : ''; ?>"></i>
                                        <?php endfor; ?>
                                        <span><?php echo $review['rating']; ?> / 5</span>
                                    </div>
                                    <p><?php echo $review['review']; ?></p>
                                    <small><?php echo $review['date']; ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No reviews yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
     </div>
     </div>
     </div>




            <div class="breadcrumb">
                <div class="container">
                   
                </div>
            </div>
            <div class="container m-t-30">
                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
                        
                         <div class="widget widget-cart">
                                <div class="widget-heading">
                                    <h3 class="widget-title text-dark">
                                 Your Cart
                              </h3>
							  				  
							  
                                    <div class="clearfix"></div>
                                </div>
                          
                                <div class="order-row bg-white">
                                    <div class="widget-body">
									
									
	<?php

$item_total = 0;

foreach ($_SESSION["cart_item"] as $item)  
{
?>									
									
                                        <div class="title-row">
										<?php echo $item["title"]; ?><a href="products.php?res_id=<?php echo $_GET['res_id']; ?>&action=remove&id=<?php echo $item["d_id"]; ?>" >
										<i class="fa fa-trash pull-right"></i></a>
										</div>
										
                                        <div class="form-group row no-gutter">
                                            <div class="col-xs-8">
                                                 <input type="text" class="form-control b-r-0" value=<?php echo "E".$item["price"]; ?> readonly id="exampleSelect1">
                                                   
                                            </div>
                                            <div class="col-xs-4">
                                               <input class="form-control" type="text" readonly value='<?php echo $item["quantity"]; ?>' id="example-number-input"> </div>
                                        
									  </div>
									  
	<?php
$item_total += ($item["price"]*$item["quantity"]); 
}
?>								  
									  
									  
									  
                                    </div>
                                </div>
                               
                         
                             
                                <div class="widget-body">
                                    <div class="price-wrap text-xs-center">
                                        <p>TOTAL</p>
                                        <h3 class="value"><strong><?php echo "E".$item_total; ?></strong></h3>
                                        <?php
                                        if($item_total==0){
                                        ?>

                                        
                                        <a href="checkout.php?res_id=<?php echo $_GET['res_id'];?>&action=check"  class="btn btn-danger btn-lg disabled">Checkout</a>

                                        <?php
                                        }
                                        else{   
                                        ?>
                                        <a href="checkout.php?res_id=<?php echo $_GET['res_id'];?>&action=check"  class="btn btn-success btn-lg active">Checkout</a>
                                        <?php   
                                        }
                                        ?>

                                    </div>
                                </div>
                                <div class="widget-body">
                                    <div class="price-wrap text-xs-center"
                                    <button><a href="all_products.php"  class="btn theme-btn">Add More Products</a></button>
                                </div>
                                </div>
								
						
								
								
                            </div>
                    </div>
                

                    <div class="col-md-8">
                      
             
                        <div class="menu-widget" id="2">
                            <div class="widget-heading">
                                <h3 class="widget-title text-dark">
                              <?php echo $rows['title']; ?>'s available products <a class="btn btn-link pull-right" data-toggle="collapse" href="#popular2" aria-expanded="true">
                              <i class="fa fa-angle-right pull-right"></i>
                              <i class="fa fa-angle-down pull-right"></i>
                              </a>
                           </h3>
                                <div class="clearfix"></div>
                            </div>
                            <div class="collapse in" id="popular2">
						<?php  
									$stmt = $db->prepare("select * from products where rs_id='$_GET[res_id]'");
									$stmt->execute();
									$products = $stmt->get_result();
									if (!empty($products)) 
									{
									foreach($products as $product)
										{ 

                                            $listedDat = new DateTime($product['listing_date']);
                                            $currentDat = new DateTime();
                                            $interval = $listedDat->diff($currentDat);
                                            $daysAgo = $interval->days; 
										 ?>
                                <div class="food-item">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-lg-8">
										<form method="post" href="cart" action='products.php?res_id=<?php echo $_GET['res_id'];?>&action=add&id=<?php echo $product['d_id']; ?>'>
                                            <div class="rest-logo pull-left">
                                                <a class="restaurant-logo pull-left" href="#"><?php echo '<img src="farmer/Res_img/products/'.$product['img'].'" alt="product logo">'; ?></a>
                                            </div>
                                
                                            <div class="rest-descr">
                                                <h6><a href="#"><?php echo $product['title']; ?></a></h6>
                                                <span> <?php echo $product['slogan']; ?></span><br>
                                                <span> <?php echo $product['quantity']; ?> Available</span>
                                                <p> Listed <?php echo $daysAgo; ?> day(s) ago</p>
                                            </div>                           
                                        </div>
                               
                                        <div class="col-xs-12 col-sm-12 col-lg-3 pull-right item-cart-info"> 
										<span class="price pull-left" >E<?php echo $product['price']; ?></span>
										  <input class="b-r-0" type="text" name="quantity"  style="margin-left:30px;" value="1" size="2" />
										  <input type="submit" class="btn theme-btn" style="margin-left:40px;" id="add-to-cart-btn" value="Add To Cart" />
										</div>
										</form>
                                        <div class="col-xs-6">
                    <form method="post" action="send_message.php">
                        <div class="form-group">
                            <textarea class="form-control" name="message" placeholder="Type a message e.g. Hi, can i get more info on this?..." required></textarea>
                        </div>
                        <input type="hidden" name="product_id" value="<?php echo $product['d_id']; ?>">
                        <input type="hidden" name="farmer_id" value="<?php echo $product['rs_id']; ?>">
                        <button type="submit" class="btn theme-btn">Send Message</button>
                    </form>

                    <?php
                        if (isset($_GET['success']) && $_GET['success'] == 1) {
                         echo '<div class="alert alert-success" role="alert">Message sent successfully!</div>';
                        } elseif (isset($_GET['error'])) {
                         $errorMessages = [
                         1 => 'Please fill in all required fields.',
                         2 => 'Failed to send the message. Please try again later.'
                         ];
                         $error = intval($_GET['error']);
                         echo '<div class="alert alert-danger" role="alert">' . $errorMessages[$error] . '</div>';
                        }
                        ?>
                </div>
                                    </div>
              
                                </div>


                
								
								<?php
									  }
									}
									
								?>
                            </div>
             
                        </div>

                        <div class="menu-widget" id="3">
                            <div class="widget-heading">
                                <h3 class="widget-title text-dark">
                              <?php echo $rows['title']; ?>'s growing products <a class="btn btn-link pull-right" data-toggle="collapse" href="#popular3" aria-expanded="true">
                              <i class="fa fa-angle-right pull-right"></i>
                              <i class="fa fa-angle-down pull-right"></i>
                              </a>
                           </h3>
                                <div class="clearfix"></div>
                            </div>
                            <div class="collapse in" id="popular3">
                        <?php  
                                    $stmt = $db->prepare("select * from growing where rs_id='$_GET[res_id]'");
                                    $stmt->execute();
                                    $products = $stmt->get_result();
                                    if (!empty($products)) 
                                    {
                                    foreach($products as $product)
                                        {
                        
                                                    
                                                     
                                                     ?>
                                <div class="food-item">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-lg-8">
                                        <form method="post" action='products.php?res_id=<?php echo $_GET['res_id'];?>&action=add&id=<?php echo $product['d_id']; ?>'>
                                            <div class="rest-logo pull-left">
                                                <a class="restaurant-logo pull-left" href="#"><?php echo '<img src="farmer/Res_img/products/'.$product['img'].'" alt="product logo">'; ?></a>
                                            </div>
                                
                                            <div class="rest-descr">
                                                <h6><a href="#"><?php echo $product['title']; ?></a></h6>
                                                <p> <?php echo $product['slogan']; ?>, listed at: <?php echo $product['created_at']; ?></p>
                                                <p> <?php echo $product['quantity']; ?> products estimated, to be available around: <?php echo $product['estimated_readiness_date']; ?></p>
                                            </div>
                           
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-lg-3 pull-right item-cart-info"> 
                                        <span class="price pull-left" ><?php echo $product['progress']; ?>% ready</span>
                                        </div>
                                        </form>
                                    </div>
              
                                </div>
                
                                
                                <?php
                                      }
                                    }
                                    
                                ?>

                            </div>
                        </div>   
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
<!-- Rating and Review Modal -->
        <div id="rateReviewModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="rateReviewModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rateReviewModalLabel">Rate and Review Farmer</
 h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="submit_review.php">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="rating">Rating</label>
                                <select class="form-control" id="rating" name="rating">
                                    <option value="1">1 Star</option>
                                    <option value="2">2 Stars</option>
                                    <option value="3">3 Stars</option>
                                    <option value="4">4 Stars</option>
                                    <option value="5">5 Stars</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="review">Review</label>
                                <textarea class="form-control" id="review" name="review" rows="4"></textarea>
                            </div>
                            <input type="hidden" name="farmer_id" value="<?php echo $_GET['res_id']; ?>">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <footer class="footer">
            <div class="container">
                
          
                <div class="bottom-footer">
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 address color-gray">
                                    <h5>Address</h5>
                                    <p>Eswatini Medical Christian University, <br> Lomkiri, Zone 4, <br> Mbabane, Eswatini</p>
                                    <h5>Phone: (+268) 7943 9397</a></h5> </div>
                                <div class="col-xs-12 col-sm-5 additional-info color-gray">
                                    <h5>Addition information</h5>
                                   <p>Join thousands of other farmers who benefit from having partnered with us.</p>
                                </div>
                    </div>
                </div>
          
            </div>
        </footer>
      
        </div>
  
    </div>
 
    <script src="js/jquery.min.js"></script>
    <script src="js/tether.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/animsition.min.js"></script>
    <script src="js/bootstrap-slider.min.js"></script>
    <script src="js/jquery.isotope.min.js"></script>
    <script src="js/headroom.js"></script>
    <script src="js/foodpicky.min.js"></script>
    <script>
 $(document).ready(function() {
 $('#rateFarmerBtn').on('click', function() {
 $('#rateReviewModal').modal('show');
            });
        });


 // Assuming you have jQuery for AJAX handling
 $('.add-to-cart-btn').on('click', function(e) {
    e.preventDefault();
    var productId = $(this).data('d_id');
    
    $.ajax({
        type: 'POST',
        url: 'products.php?res_id=<?php echo $_GET['res_id'];?>&action=add&id=<?php echo $product['d_id']; ?>', // Replace with your server endpoint for adding to cart
        data: { productId: productId },
        success: function(response) {
            // Update cart section
            updateCartSummary(response.cart);
            // Show a message or handle success feedback
            alert('Item added to cart!');
        },
        error: function(err) {
            console.error('Error adding to cart:', err);
            // Handle error feedback if needed
        }
    });
 });
 function updateCartSummary(cartData) {
    // Update the cart summary section with new data
    $('#cart-items-count').text(cartData.totalItems);
    $('#cart-total-price').text(cartData.totalPrice);
 }
 // Example for "Add more" button behavior
 $('#add-more-btn').on('click', function() {
    window.location.href = '/all_products.php'; // Replace with your all products page URL
 });
    </script>
</body>

</html>
