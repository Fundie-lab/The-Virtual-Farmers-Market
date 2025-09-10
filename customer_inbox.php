<?php include('language_switcher.php'); ?>
<!DOCTYPE html>
<?php
include("connection/connect.php");
error_reporting(0);
session_start();

$user_id = $_SESSION['user_id'];


// Get the logged-in user's ID
$user_id = $_SESSION['user_id']; // Assuming the user ID is stored in the session

// Query to get farmers the user has messaged
$sql = "
    SELECT farmer.rs_id, farmer.title, farmer.image 
    FROM farmer 
    INNER JOIN messages 
    ON farmer.rs_id = messages.farmer_id 
    WHERE messages.user_id = '$user_id'
    GROUP BY farmer.rs_id
    ORDER BY messages.created_at DESC
";
$query = mysqli_query($db, $sql);

?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="#">
    <title>User Messages</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .farmer-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin: 15px;
            width: 250px;
            display: inline-block;
            vertical-align: top;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .farmer-card img {
            border-radius: 50%;
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
        }
        .farmer-card h3 {
            margin: 0;
            font-size: 20px;
        }
        .farmer-card a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }
        .farmer-card a:hover {
            background-color: #218838;
        }
    </style>
    <link rel="stylesheet" href="css/navstyle.css">
</head>

<body class="home">
    <?php include("navbar.php"); ?>
    <div class="page-wrapper">
        <div class="top-links">
            <div class="container">
                <ul class="row links">                        
                </ul>
            </div>
        </div>
        <section href="#" id="cart">
            <div class="breadcrumb">
                <div class="container"></div>
            </div>
            <div class="container m-t-30">
                <div class="row">
                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-1"></div>
                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-10">
                        <div class="widget widget-cart">
                            <div class="widget-heading">
                                <h3 class="widget-title text-dark">
                                    Your Chats with Farmers
                                </h3>
                                <div class="clearfix"></div>
                            </div>
                            <div class="widget-body">
                                <div class="container">
        
        <div class="farmers-list">
            <?php
            if(mysqli_num_rows($query) > 0) {
                while($farmer = mysqli_fetch_assoc($query)) {
                    echo '
                    <div class="farmer-card">
                        <img src="../VFM/farmer/Res_img/'.$farmer['image'].'" alt="Farmer Image">
                        <h3>'.$farmer['title'].'</h3>
                        <a href="chat.php?farmer_id='.$farmer['rs_id'].'">Chat with '.$farmer['title'].'</a>
                    </div>';
                }
            } else {
                echo '<p>You have no messages with any farmers yet.</p>';
            }
            ?>
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
                            <h5>Phone: (+268) 7943 9397</h5>
                        </div>
                        <div class="col-xs-12 col-sm-5 additional-info color-gray">
                            <h5>Addition information</h5>
                            <p>Join thousands of other farmers who benefit from having partnered with us.</p>
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
