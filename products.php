<?php include('language_switcher.php'); ?>
<!DOCTYPE html> 
<?php
include("connection/connect.php"); 
error_reporting(0);

session_start();

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

function time_elapsed_string($datetime, $full = false, $timezone = 'Africa/Mbabane') {
    $now = new DateTime('now', new DateTimeZone($timezone));
    $ago = new DateTime($datetime, new DateTimeZone($timezone));
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
    $result = array();
    foreach ($string as $k => $v) {
        if ($diff->$k) {
            $result[$k] = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
    if (!$full) $result = array_slice($result, 0, 1);
    return $result ? implode(', ', $result) . ' ago' : 'just now';
}



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

$userId = $_SESSION['user_id']; // Ensure you have user_id stored in session after login





    $user_id = $_SESSION['user_id'];
    if (isset($_GET['product_id'])) {
        $product_id = $_GET['product_id'];
        $activity_type = 'order';

        // Delete any existing entry for this product and user
        $delete_query = "DELETE FROM user_activity WHERE user_id = '$user_id' AND product_id = '$product_id'";
        mysqli_query($db, $delete_query);

        $track_activity_query = "INSERT INTO user_activity (user_id, product_id, activity_type) VALUES ('$user_id', '$product_id', '$activity_type')";
        mysqli_query($db, $track_activity_query);
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

    </style>
    <link rel="stylesheet" href="css/navstyle.css">
</head>

<body class="home">
        <?php
        // Get the current URL and query parameters
        
        include("navbar.php");
     ?>
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
                                    <p><?php echo $langStrings['email_address'];?>: <a href="mailto:"><?php echo $rows['email']; ?></a></p>
                                    <p><?php echo $langStrings['phone'];?>: <a href="tel:"><?php echo $rows['phone']; ?></a></p>
                                    <p>Website: <a href="https://<?php echo $rows['url']; ?>"><?php echo $rows['url']; ?></a></p>                                    <p><?php echo $langStrings['days'];?>: <?php echo $rows['o_days']; ?> (<?php echo $rows['o_hr']; ?> - <?php echo $rows['c_hr']; ?>)</p>
                                    <p><?php echo $langStrings['farm_size'];?>: <?php echo $rows['Farm_size']; ?></p>
                                    <p><?php echo $langStrings['physical_add'];?>: <?php echo $rows['address']; ?></p>
                                    <p><?php echo $langStrings['farm_size'];?>: <?php echo $rows['Farm_size']; ?></p>
                                    <p><?php echo $langStrings['deliv'];?> <?php echo $rows['delivery_place']; ?> @ <?php echo $langStrings['fee'];?> <?php echo $rows['fixed_delivery_fee']; ?>. <br><?php echo $langStrings['freedeliv'];?> E<?php echo $rows['free_delivery_threshold']; ?></p>


                                    <?php
                                    if($rows['has_deliveries'] == 1)
                                    {
                                        $resss= mysqli_query($db,"select * from delivery_options where farmer_id='$_GET[res_id]'");
                                         $rowsss=mysqli_fetch_array($resss);
                                        ?>
                                        <p><?php echo $langStrings['deliv_add'];?>: <?php echo $rowsss['delivery_place']; ?> (<?php echo $langStrings['in'];?> <?php echo $rowsss['delivery_time']; ?> <?php echo $langStrings['dayss'];?>)</p>
                                        <?php
                                         
                                    }
                                    ?> 
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
                                            <?php echo $item["title"]; ?>
                                            <a href="products.php?res_id=<?php echo $_GET['res_id']; ?>&action=remove&id=<?php echo $item["d_id"]; ?>" ><i class="fa fa-trash pull-right"></i>
                                            </a>
                                        </div>								
                                        <div class="form-group row no-gutter">
                                            <div class="col-xs-8">
                                                 <input type="text" class="form-control b-r-0" value=<?php echo "E".$item["price"]; ?> readonly id="exampleSelect1">
                                                   
                                            </div>
                                            <div class="col-xs-4">
                                               <input class="form-control" type="text" readonly value='<?php echo $item["quantity"]; ?>' id="example-number-input"> </div>
                                        
									  </div>
									  
	<?php


    $product_id = $item["d_id"];  // The product ID from the session cart
    $quantity = $item["quantity"]; // The quantity of the product

     // Check if the product already exists in the cart for this user
    $stmt = $db->prepare("SELECT quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Product exists, update quantity
        $existing_item = $result->fetch_assoc();
        $new_quantity = ($existing_item['quantity'] + $quantity) - $existing_item['quantity'];

        // Update the quantity in the cart_items table
        $update_stmt = $db->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $update_stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        // Product doesn't exist, insert it
        $insert_stmt = $db->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $insert_stmt->execute();
        $insert_stmt->close();
    }

    $stmt->close();
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
                                    <div class="price-wrap text-xs-center">
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

                                            $listedDat = new DateTime($product['listed_at']);
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
                                                <p> Listed <?php echo time_elapsed_string($product['listed_at']); ?></p>
                                            </div>                           
                                        </div>
                               
                                        <div class="col-xs-12 col-sm-12 col-lg-3 pull-right item-cart-info">
                                        <?php 
                                        $percent_off = (100 - (($product['discount']/$product['price'])*100)); 
                                        $percent_off;
                                        if ($product['sale'] == 1) {
                                            $price == $product['discount'];
                                        ?> 
                                        <font color="red"><?php print_r($percent_off);?>% off</font>
                                        <span class="price pull-left">
                                            <span class="old-price">was E<?php echo $product['price']; ?></span><br>
                                            <span class="new-price">NOW E<?php echo $product['discount']; ?></span>
                                        </span>
                                        <?php 
                                        } else {
                                        ?> 
                                        <span class="price pull-left" >E<?php echo $product['price']; ?></span>
                                        <?php 
                                        }
                                        ?> 
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
                        <h5 class="modal-title" id="rateReviewModalLabel">Rate and Review Farmer</h5>
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
