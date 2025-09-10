<?php include('language_switcher.php'); ?>
<!DOCTYPE html>
<?php
include("connection/connect.php"); 
error_reporting(0); 
session_destroy();
session_start();

?>
<head>
  <meta charset="UTF-8">
  <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
	    <style type="text/css">
	       #buttn{
		  color:#fff;
		  background-color: #5c4ac7;
	  }
	  </style>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
  

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.css" />
    <style type="text/css">
        .input-group {
        position: relative;
    }
    .autocomplete-suggestions {
        position: absolute;
        z-index: 9999;
        border: 1px solid #ddd;
        background-color: #fff;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        display: none;
    }
    .autocomplete-suggestion {
        padding: 10px;
        cursor: pointer;
    }
    .autocomplete-suggestion:hover {
        background-color: #f0f0f0;
    }


    </style>
    <link rel="stylesheet" href="css/navstyle.css">
</head>

<body>
<?php
        include("navbar.php");
    ?>
<div style=" background-image: url('images/img/pimg.jpg');">

<?php
include("connection/connect.php"); 
error_reporting(0); 
session_start(); 
if(isset($_POST['submit']))  
{
	$username = $_POST['username'];  
	$password = $_POST['password'];
	
	if(!empty($_POST["submit"]))   
     {
	$loginquery ="SELECT * FROM users WHERE username='$username' && password='".md5($password)."'"; //selecting matching records
	$result=mysqli_query($db, $loginquery); //executing
	$row=mysqli_fetch_array($result);
	
	                        if(is_array($row)) 
								{
                                    if ($row['activity'] == "active") {
                                        $_SESSION["user_id"] = $row['u_id']; 
                                         header("refresh:1;url=index.php");
                                    } else {
                                        $_SESSION["user_id"] = $row['u_id']; 
                                         header("refresh:1;url=indexsus.php");
                                    }
                                    	 
	                            } 
							else
							    {
                                      	$message = "Invalid Username or Password!"; 
                                }
	 }
	
	
}
?>
  

<div class="pen-title">
  <
</div>

<div class="module form-module">
  <div class="toggle">
   
  </div>
  <div class="form">
    <h2><?php echo $langStrings['login_acc'];?></h2>
	  <span style="color:red;"><?php echo $message; ?></span> 
   <span style="color:green;"><?php echo $success; ?></span>
    <form action="" method="post">
      <input type="text" placeholder="Username"  name="username"/>
      <input type="password" placeholder="Password" name="password"/>
      <input type="submit" id="buttn" name="submit" value="<?php echo $langStrings['login'];?>" />
    </form>
  </div>
  
  <div class="cta"><?php echo $langStrings['not_reg'];?><a href="registration.php" style="color:#5c4ac7;"> <?php echo $langStrings['create_acc'];?></a></div>
</div>
  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>

  

   
  <div class="container-fluid pt-3">
	<p></p>
  </div>


   
        <footer class="footer">
            <div class="container">

             
                <div class="bottom-footer">
                    <div class="row">
                        <div class="col-xs-12 col-sm-3 payment-options color-gray">
                            <h5>Payment Options</h5>
                            <ul>
                                <li>
                                    <a href="#"> <img src="images/paypal.png" alt="Paypal"> </a>
                                </li>
                                <li>
                                    <a href="#"> <img src="images/mastercard.png" alt="Mastercard"> </a>
                                </li>
                                <li>
                                    <a href="#"> <img src="images/maestro.png" alt="Maestro"> </a>
                                </li>
                                <li>
                                    <a href="#"> <img src="images/stripe.png" alt="Stripe"> </a>
                                </li>
                                <li>
                                    <a href="#"> <img src="images/bitcoin.png" alt="Bitcoin"> </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-xs-12 col-sm-4 address color-gray">
                            <h5>Address</h5>
                            <p>1086 Stockert Hollow Road, Seattle</p>
                                    <h5>Phone: 75696969855</a></h5> </div>
                        <div class="col-xs-12 col-sm-5 additional-info color-gray">
                            <h5>Addition informations</h5>
                           <p>Join thousands of other restaurants who benefit from having partnered with us.</p>
                        </div>
                    </div>
                </div>
            
            </div>
        </footer>
       


</body>

</html>
