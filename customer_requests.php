<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php");  
error_reporting(E_ALL);
ini_set('display_errors', 1);
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

<head>
  <meta charset="UTF-8">
  <title>Product Request</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
  <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900|RobotoDraft:400,100,300,500,700,900'>
  <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>
  <link rel="stylesheet" href="css/login.css">

  <style type="text/css">
    #buttn {
      color: #fff;
      background-color: #5c4ac7;
    }
  </style>

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/font-awesome.min.css" rel="stylesheet">
  <link href="css/animsition.min.css" rel="stylesheet">
  <link href="css/animate.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="csss/style.css" rel="stylesheet">
</head>

<body>
<header id="header" class="header-scroll top-header headrom">
  <nav class="navbar navbar-dark">
    <div class="container">
      <button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse" data-target="#mainNavbarCollapse">&#9776;</button>
      <a class="navbar-brand" href="index.php"><img class="img-rounded" src="images/icn.png" alt=""></a>
      <div class="collapse navbar-toggleable-md float-lg-right" id="mainNavbarCollapse">
        <ul class="nav navbar-nav">
          <li class="nav-item"><a class="nav-link active" href="index.php">Home <span class="sr-only">(current)</span></a></li>
          <?php
          if(empty($_SESSION["user_id"])) {
            echo '<li class="nav-item"><a href="login.php" class="nav-link active">Login</a></li>';
            echo '<li class="nav-item"><a href="registration.php" class="nav-link active">Register</a></li>';
          } else {
            echo '<li class="nav-item"><a href="your_orders.php" class="nav-link active">My Orders</a></li>';
            echo '<li class="nav-item"><a href="logout.php" class="nav-link active">Logout</a></li>';
          }
          ?>
        </ul>
      </div>
    </div>
  </nav>
</header>

<section class="popular">
  <div class="container">                
    <div class="title text-xs-center m-b-30">
      <div class="container"></div>
    </div>
    <div class="row">
      <div class="col-md-3"></div>
      <div class="col-md-6">
        <div class="table-responsive m-t-40">
          <div class="container mt-5">
            <br><br>
            <h1>Product Request Form</h1>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
              <div class="form-group">
                <label for="product_name">Product name:</label>
                <input type="text" class="form-control" id="product_name" placeholder="e.g., cabbages" name="product_name" required>
              </div>
              <div class="form-group">
                <label for="description">Description:</label>
                <input type="text" class="form-control" id="description" placeholder="4 crates of product" name="description" required>
              </div>
              <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" class="form-control" id="quantity" placeholder="1" name="quantity" required>
              </div>
              <div class="form-group">
                <label for="delivery_date">Preferred delivery date:</label>
                <input type="date" class="form-control" id="delivery_date" name="delivery_date" required>
              </div>
              <div class="form-group has-danger">
                <label class="control-label">Image</label>
                <input type="file" name="file" id="lastName" class="form-control form-control-danger" placeholder="12n">
              </div>                  
              <button type="submit" name="submit" class="btn btn-primary">Send Request</button>
            </form>
            <?php
            if (isset($error)) {
              echo $error;
            } elseif (isset($success)) {
              echo $success;
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>

<div class="container-fluid pt-3">
  <p></p>
</div>

<footer class="footer">
  <div class="container">
    <div class="bottom-footer">
      <div class="row">
        <div class="col-xs-12 col-sm-4 address color-gray">
          <h5>Address</h5>
          <p>Eswatini Medical Christian University, <br> Lomkiri, Zone 4, <br> Mbabane, Eswatini</p>
          <h5>Phone: (+268) 7943 9397</a></h5>
        </div>
        <div class="col-xs-12 col-sm-5 additional-info color-gray">
          <h5>Additional Information</h5>
          <p>Join thousands of other farmers who benefit from having partnered with us.</p>
        </div>
      </div>
    </div>
  </div>
</footer>
</body>
</html>
<?php
}
?>
