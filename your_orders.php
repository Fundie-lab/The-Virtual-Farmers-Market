<?php include('language_switcher.php'); ?>
<!DOCTYPE html> 
<?php
include("connection/connect.php");
error_reporting(0);
session_start();

if(empty($_SESSION['user_id']))  
{
	header('location:login.php');
}
else
{
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="#">
    <title>My Orders</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
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
  background: radial-gradient(ellipse at center, #e5e5e5 0%, #ffffff 100%);
  font: 600 15px "Open Sans", Arial, sans-serif;
}
label.control-label {
  font-weight: 600;
  color: #777;
}

table { 
	width: 100%; 
	border-collapse: collapse; 
	margin: auto;
}

/* Zebra striping */
 tr:nth-of-type(odd) { 
	background: #eee; 
}

th { 
	background: #404040; 
	color: white; 
	font-weight: bold; 
}

td, th { 
	padding: 10px; 
	border: 1px solid #ccc; 
	text-align: left; 
	font-size: 15px;
}

@media 
only screen and (max-width: 760px),
(min-device-width: 768px) and (max-device-width: 1024px) {

	table, thead, tbody, th, td, tr { 
		display: block; 
	}

	thead tr { 
		position: absolute;
		top: -9999px;
		left: -9999px;
	}
	
	tr { 
		border: 1px solid #ccc; 
		margin-bottom: 5px;
	}
	
	td { 
		border: none;
		border-bottom: 1px solid #eee; 
		position: relative;
		padding-left: 50%; 
	}

	td:before { 
		position: absolute;
		top: 6px;
		left: 6px;
		width: 45%; 
		padding-right: 10px; 
		white-space: nowrap;
		content: attr(data-column);
		color: #000;
		font-weight: bold;
	} 
}
</style>
<link rel="stylesheet" href="css/navstyle.css">
</head>

<body class="home">
    <?php
        include("navbar.php");
     ?>
        <div class="page-wrapper">
            <div class="inner-page-hero bg-image" data-image-src="images/img/pimg.jpg">
                <div class="container"> </div>
        
            </div>
            <div class="result-show">
                <div class="container">
                    <div class="row">
                       
                       
                    </div>
                </div>
            </div>
    
            <section class="restaurants-page">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12">
                          </div>
                        <div class="col-xs-12">
                            <div class="bg-gray">
                                <div class="row">
								
							<table class="table table-bordered table-hover">
						  <thead style = "background: #404040; color:white;">
							<tr>
							  <th>Item</th>
                                            <th>Farmer Email</th>
                                            <th>Quantity</th>
                                            <th>Total Price (E)</th>
                                            <th>Delivery Option</th>
                                            <th>Delivery Charge (E)</th>
                                            <th>Grand Total (E)</th>
                                            <th>Status</th>
                                            <th>Remark</th>
                                            <th>Date</th>
							     <?php
							
                                                if($status !="closed") {
                                                    echo '<th>Action</th>';
                                                }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $query_res= mysqli_query($db,"SELECT farmer.*, users_orders.* FROM farmer INNER JOIN users_orders ON farmer.rs_id=users_orders.rs_id AND u_id='".$_SESSION['user_id']."' ORDER BY o_id DESC");
                                        if(!mysqli_num_rows($query_res) > 0 ) {
                                            echo '<td colspan="12"><center>You have No orders Placed yet. </center></td>';
                                        } else {
                                            while($row=mysqli_fetch_array($query_res)) {
                                                $item_total = ($row["price"] * $row["quantity"]);
                                                $delivery_charges = $row["delivery_charges"];
                                                $grand_total = $item_total + $delivery_charges;
                                        ?>
                                                <tr>
                                                    <td data-column="Item"> <?php echo $row['title']; ?></td>
                                                    <td data-column="Farmer Email"> <?php echo $row['email']; ?></td>
                                                    <td data-column="Quantity"> <?php echo $row['quantity']; ?></td>
                                                    <td data-column="Total Price"><?php echo $item_total; ?></td>
                                                    <td data-column="Delivery Option"> <?php echo $row['delivery_option']; ?></td>
                                                    <td data-column="Delivery Charge"><?php echo $row['delivery_charges']; ?></td>
                                                    <td data-column="Grand Total"><?php echo $grand_total; ?></td>
                                                    <td data-column="Status"> 
                                                        <?php 
                                                            $status = $row['status'];
                                                            if($status=="" or $status=="Dispatch") {
                                                                echo '<button type="button" class="btn btn-info"><span class="fa fa-bars" aria-hidden="true" ></span> Dispatch</button>';
                                                                echo '<td data-column="Remark"> No remark yet. '.$row['remark'].'</td>';
                                                                echo '<td data-column="Date"> '.$row['date'].'</td>';
                                                                echo '<td data-column="Action"><a href="delete_orders.php?order_del='.$row['o_id'].'" onclick="return confirm(\'Are you sure you want to cancel your order?\');" class="btn btn-danger btn-flat btn-addon btn-xs m-b-10"><i class="fa fa-trash-o" style="font-size:16px"></i> Cancel order</a></td>';
                                                            } elseif($status=="in process") {
                                                                echo '<button type="button" class="btn btn-warning"><span class="fa fa-cog fa-spin" aria-hidden="true" ></span> On The Way!</button>';
                                                                echo '<td data-column="Remark"> '.$row['remark'].'</td>';
                                                                echo '<td data-column="Date"> '.$row['date'].'</td>';
                                                                echo '<td data-column="Action"><a href="delete_orders.php?order_del='.$row['o_id'].'" onclick="return confirm(\'Are you sure you want to cancel your order?\');" class="btn btn-danger btn-flat btn-addon btn-xs m-b-10"><i class="fa fa-trash-o" style="font-size:16px"></i> Cancel order</a></td>';
                                                            } elseif($status == "closed") {
                                                                if($row['customer_confirm'] == 'pending') {
                                                                    echo '<button type="button" class="btn btn-success" onclick="confirmReceipt('.$row['o_id'].')"><span class="fa fa-check-circle" aria-hidden="true"></span> Confirm Receipt</button>';
                                                                } else {
                                                                    echo '<button type="button" class="btn btn-success disabled"><span class="fa fa-check-circle" aria-hidden="true"></span> Received</button>';
                                                                }
                                                                echo '<td data-column="Remark">'.$row['remark'].'</td>';
                                                                echo '<td data-column="Date">'.$row['date'].'</td>';
                                                                echo '<td data-column="Receipt"><a href="generate_receipt.php?order_id='.$row['o_id'].'" class="btn btn-info btn-flat btn-addon btn-sm m-b-10 m-l-5"><i class="fa fa-file-pdf-o" style="font-size:16px"></i> Print Receipt</a></td>';
                                                            } elseif($status=="rejected") {
                                                                echo '<button type="button" class="btn btn-danger"> <i class="fa fa-close"></i> Cancelled</button>';
                                                                echo '<td data-column="Remark">'.$row['remark'].'</td>';
                                                                echo '<td data-column="Date">'.$row['date'].'</td>';
                                                                echo '<td data-column="Action"><a href="delete_orders.php?order_del='.$row['o_id'].'" onclick="return confirm(\'Are you sure you want to cancel your order?\');" class="btn btn-danger btn-flat btn-addon btn-xs m-b-10"><i class="fa fa-trash-o" style="font-size:16px"></i> Cancel order</a></td>';
                                                            }
                                                        ?>
                                                    </td>
                                                </tr>
                                        <?php 
                                            }
                                        } 
                                        ?>
						  </tbody>
					</table>
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
                                    <h5>Addition informations</h5>
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
function confirmReceipt(orderId) {
    if (confirm('Have you received your order?')) {
        $.ajax({
            url: 'confirm_receipt.php',
            type: 'POST',
            data: {order_id: orderId},
            success: function(response) {
                if (response == 'success') {
                    alert('Thank you for confirming!');
                    location.reload();
                } else {
                    alert('Something went wrong, please try again.');
                }
            }
        });
    }
}
</script>

</body>

</html>
<?php
}
?>