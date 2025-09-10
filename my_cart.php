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

             <!-- Reviews Section -->
<section href="#" id="cart">
   <div class="breadcrumb">
      <div class="container">
                   
                </div>
            </div>
            <div class="container m-t-30">
                <div class="row">
                  <div class="col-xs-12 col-sm-8 col-md-8 col-lg-2">
                  </div>
                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                        
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
										<?php echo $item["title"]; ?><a href="my_cart.php?res_id=<?php echo $_GET['res_id']; ?>&action=remove&id=<?php echo $item["d_id"];?>" >
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
                                    <div class="price-wrap text-xs-center">
                                    <button><a href="all_products.php"  class="btn theme-btn">Add More Products</a></button>
                                </div>
                                </div>
								
						
								
								
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
</body>

</html>
