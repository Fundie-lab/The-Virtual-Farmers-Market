<?php include('language_switcher.php'); ?>
<!DOCTYPE html> 
<?php
include("connection/connect.php");  
error_reporting(0); 
 
session_start();

if (!empty($_SESSION["user_id"])) {
    $user_id = $_SESSION['user_id'];
    if (isset($_GET['res_id'])) {
        $product_id = $_GET['res_id'];
        $activity_type = 'order';
        $track_activity_query = "INSERT INTO user_activity (user_id, product_id, activity_type) VALUES ('$user_id', '$product_id', '$activity_type')";
        mysqli_query($db, $track_activity_query);
    }
}
if(empty($_SESSION["user_id"])){
$user_id = empty($_SESSION["user_id"]); 
} else {
    $user_id = $_SESSION['user_id']; 
}


function update_product_status($db) {
    $query_res = mysqli_query($db, "SELECT * FROM products");
    while ($r = mysqli_fetch_array($query_res)) {
        $listedDat = new DateTime($r['listed_at']);
        $currentDat = new DateTime();
        $interval = $listedDat->diff($currentDat);
        $daysAgo = $interval->days;

        $perishability = strtolower($r['perishability']);
        if ($perishability == 'high') {
            $daysThreshold = 7;
        }elseif ($perishability == 'medium'){
            $daysThreshold = 14;  
        } elseif ($perishability == 'low') {
            $daysThreshold = 30;
        }

//Update the product status based on days listed
        if($daysAgo > $daysThreshold && $r['sale'] == 0) {
        // Update the product status to "medium"
            $update_query = "UPDATE products SET sale = 1 WHERE d_id =" . $r['d_id'];
            mysqli_query($db, $update_query);
        }    } 
 }

update_product_status($db);

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


// Track user activity when they click "Order Now"
if (isset($_GET['d_id']) && !empty($user_id)) {
    $product_id = $_GET['d_id'];
    $activity_type = 'order';
    $track_activity_query = "INSERT INTO user_activity (user_id, product_id, activity_type) VALUES ('$user_id', '$product_id', '$activity_type')";
    mysqli_query($db, $track_activity_query);
}

// Track user activity when they search for a product
if (isset($_GET['query']) && !empty($user_id)) {
    $search_query = $_GET['query'];
    $activity_type = 'search';
    $search_product_query = "SELECT d_id FROM products WHERE title LIKE '%$search_query%'";
    $search_result = mysqli_query($db, $search_product_query);
    while ($row = mysqli_fetch_assoc($search_result)) {
        $product_id = $row['d_id'];
        $track_activity_query = "INSERT INTO user_activity (user_id, product_id, activity_type) VALUES ('$user_id', '$product_id', '$activity_type')";
        mysqli_query($db, $track_activity_query);
    }
}


function calculateDynamicPrice($basePrice, $daysListed) {
    $price = $basePrice;
    if ($daysListed > 21) {
        $price *= 0.5; // Apply a 30% discount if listed for more than 3 weeks
    } elseif ($daysListed > 14) {
        $price *= 0.6; // Apply a 20% discount if listed for more than 2 weeks
    } elseif ($daysListed > 7) {
        $price *= 0.9; // Apply a 10% discount if listed for more than a week
    }
    return $price;
}

function updateProductPrices($db) {
    // Fetch all products
    $query_res = mysqli_query($db, "SELECT * FROM products");

    while ($r = mysqli_fetch_array($query_res)) {
        // Calculate the days listed
        $listedDat = new DateTime($r['listed_at']);
        $currentDat = new DateTime();
        $interval = $listedDat->diff($currentDat);
        $daysListed = $interval->days;

        // Calculate the new price
        $originalPrice = $r['price'];
        $newPrice = calculateDynamicPrice($originalPrice, $daysListed);

        // Update the price in the database if it has changed
        if ($newPrice != $originalPrice) {
            $product_id = $r['d_id']; // Assuming 'd_id' is the product ID
            $update_query = "UPDATE products SET discount = '$newPrice' WHERE d_id = '$product_id'";
            mysqli_query($db, $update_query);
        }
    }
}

// Call the function to update prices
updateProductPrices($db);





?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">   
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="#">
    <title>Home</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.css" />
    <style type="text/css">
        body {
 background: linear-gradient(1deg, #ffffff, #0001);

}
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
    .tabs {
            display: flex;
            flex-wrap: wrap;
            /*border-bottom: 1px solid #ccc;*/
            margin-bottom: 1rem;
        }
        .tab {
            padding: 1rem 2rem;
            cursor: pointer;
            background: #f7f7f7;
            border: 1px solid #ccc;
            border-bottom: none;
            transition: background 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
 border-radius: 10px;


        }
        .tab.active {
            background: green;
            font-weight: bold;
            color: White;
        }
        .tab-content {
            background: rgba(255, 255, 255, 0.1);
            display: none;
            padding: 2rem;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
 backdrop-filter: blur(10px);
 -webkit-backdrop-filter: blur(10px);
 border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .tab-content.active {
            display: block;
        }


    </style>
    <link rel="stylesheet" href="css/navstyle.css">
</head>

<body class="home">
    <?php
        include("navbar.php");
     ?>

    <section class="hero bg-image" data-image-src="images/img/backg.png">
        <div class="hero-inner">
            <div class="container text-center hero-text font-white">
                <h1>
                    Welcome to <br> Virtual Farmers' Market 
                </h1>        
                <div class="banner-form">
                    <form class="form-inline">
                          
                    </form>
                </div>
                <div class="steps">
                    <div class="step-item step1">
                        <h4>
                            <span style="color:white;">Join us in fostering a community of sustainable living and mindful eating.</span><br><br><br><br>
                        </h4>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <form action="search_results.php" method="GET" id="search-form">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search for products..." name="query" id="search-input" required>
                                <span class="input-group-btn">
                                    <button class="btn theme-btn" type="submit"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-4"></div>
            </div>      
    </section>
    <section class="popular">
            <div class="container">   
                <div class="title text-xs-center m-b-30">

                </div>
                <div class="col-md-3"></div>
                <div class="tabs">
                    <div class="tab active" data-tab="all-products">All Products</div>
                    <div class="tab active" data-tab="growing-products">Growing Products</div>
                    <div class="tab active" data-tab="requested-products">Requested Products</div>
                </div>
                <div class="tab-content active" id="all-products">
                    <h2>All Products</h2>
            
                    <div class="row">
                        <?php 
    $user_id = $_SESSION['user_id'];
            $wishlist_query = mysqli_query($db, "SELECT product_id FROM wishlist WHERE user_id = '$user_id'");
            $wishlist_items = [];
            while ($row = mysqli_fetch_assoc($wishlist_query)) {
                $wishlist_items[] = $row['product_id'];
            }
    
  // Fetch recommended products based on user activity
            $recommended_product_ids = [];

            //show recommended single product
                $recommend_query = mysqli_query($db, "
                    SELECT p.*, c.name FROM products p JOIN product_categories c ON p.category_id = c.cat_id JOIN user_activity ua ON p.d_id = ua.product_id WHERE ua.user_id = '$user_id' ORDER BY ua.activity_time DESC LIMIT 6");

                if (mysqli_num_rows($recommend_query) > 0) {
            ?>

                    <div class="row">
                    <?php
                    while ($rec = mysqli_fetch_array($recommend_query)) {
                        $recommended_product_ids[] = $rec['d_id'];
                        // Fetch the farmer's coordinates
                        $farmer_id = $rec['rs_id'];
                        $product_id = $rec['d_id'];
                        $farmer_query = mysqli_query($db, "SELECT * FROM farmer WHERE rs_id = '$farmer_id'");
                        $farmer_coords = mysqli_fetch_assoc($farmer_query);
                        
                        $listedDat = new DateTime($rec['listed_at']);
                        $currentDat = new DateTime();
                        $interval = $listedDat->diff($currentDat);
                        $daysAgo = $interval->days; 

                        $distance = NULL;

                        echo '  <div class="col-xs-12 col-sm-6 col-md-4 food-item">
                                    <div class="food-item-wrap">
                                        <div class="figure-wrap bg-image" data-image-src="farmer/Res_img/products/'.$rec['img'].'"></div>
                                            <div class="content">
                                                <h5>
                                                <a href="products.php?res_id='.$rec['rs_id'].' && product_id='.$rec['d_id'].'">'.$rec['title'].'</a></h5>
                                                <div class="product-name">'.$rec['name'].' - '.$rec['slogan'].'<br>'.$rec['quantity'].' '.$langStrings['available'].' <br>
                                                    <a href="map-popup" class="distance-link" data-lat="'.$farmer_coords['latitude'].'" data-lon="'.$farmer_coords['longitude'].'" data-city="'.$farmer_coords['city'].'">'.$farmer_coords['city'].' - '.($distance !== null ? round($distance, 1) : 'N/A').' km away
                                                    </a><br>';?> Listed <?php echo time_elapsed_string($rec['listed_at']); ?> <?php echo '
                                                </div>
                                                <div class="price-btn-block">';

                                    if($rec['sale'] == 1) {
                                                     $percent_off = (100 - (($rec['discount']/$rec['price'])*100)); 
                                        //$percent_off;
                                        //echo '<font color="red">'; print_r($percent_off); echo'% off</font>';
                                                    echo '<span class="price"><span class="old-price">'.$langStrings['was'].' E'.$rec['price'].'</span> <font color="red" size="3px">'; print_r($percent_off); echo'% '.$langStrings['off'].'</font><br><span class="new-price">'.$langStrings['now'].' E'.$rec['discount'].'</span></span> <a href="products.php?res_id='.$rec['rs_id'].' && product_id='.$rec['d_id'].'" class="btn theme-btn-dash pull-right">'.$langStrings['order_now'].'</a> </div>';
                                                    
                                                    // Check if the product is in the wishlist
                                                        if (in_array($product_id, $wishlist_items)) {
                                                            echo '<div class="wishlist-icon" data-product-id="'.$product_id.'">
                                                                    <i class="fa fa-heart"></i> <span><em> '.$langStrings['added_to_wish'].'</em></span>
                                                                  </div>';
                                                        } else {
                                                            echo '<div class="wishlist-icon" data-product-id="'.$product_id.'">
                                                                    <i class="fa fa-heart-o"></i><span><em> '.$langStrings['add_to_wish'].'</em></span>
                                                                  </div>';
                                                        }
                                                echo'</div>
                                                
                                            </div>
                                    </div>';   
                                    } else {
                                        echo '<span class="price">E'.$rec['price'].'</span> <a href="products.php?res_id='.$rec['rs_id'].' && product_id='.$rec['d_id'].'" class="btn theme-btn-dash pull-right">'.$langStrings['order_now'].'</a> </div>';
                                                    
                                                    // Check if the product is in the wishlist
                                                        if (in_array($product_id, $wishlist_items)) {
                                                            echo '<div class="wishlist-icon" data-product-id="'.$product_id.'">
                                                                    <i class="fa fa-heart"></i> <span><em> '.$langStrings['added_to_wish'].'</em></span>
                                                                  </div>';
                                                        } else {
                                                            echo '<div class="wishlist-icon" data-product-id="'.$product_id.'">
                                                                    <i class="fa fa-heart-o"></i><span><em> '.$langStrings['add_to_wish'].'</em></span>
                                                                  </div>';
                                                        }
                                                echo'</div>
                                                
                                            </div>
                                    </div>';
                                                    }
                    }
                
                

                    if (!empty($recommended_product_ids)) {
                        $not_in_clause = "AND p.d_id NOT IN (" . implode(',', $recommended_product_ids) . ")";
                    } else {
                        $not_in_clause = "";
                    }


                                // Fetch products from the same category
                                $same_category_query = mysqli_query($db, "
                                    SELECT p.*, c.name 
                                    FROM products p 
                                    JOIN product_categories c ON p.category_id = c.cat_id 
                                    WHERE p.category_id IN (
                                        SELECT category_id 
                                        FROM products 
                                        WHERE d_id IN (
                                            SELECT product_id 
                                            FROM user_activity 
                                            WHERE user_id = '$user_id'
                                        )
                                    )
                                    $not_in_clause
                                    ORDER BY p.listed_at DESC 
                                    LIMIT 6
                                ");
                                if (mysqli_num_rows($same_category_query) > 0) {

                                    while ($cat = mysqli_fetch_array($same_category_query)) {
                                        $recommended_product_ids[] = $cat['d_id'];
            

                                    // Fetch the farmer's coordinates
                                     $farmer_id = $cat['rs_id'];
                                     $product_id = $cat['d_id'];
                                     $farmer_query = mysqli_query($db, "SELECT * FROM farmer WHERE rs_id = '$farmer_id'");
                                     $farmer_coords = mysqli_fetch_assoc($farmer_query);
                                     

                                    $listedDat = new DateTime($cat['listed_at']);
                                    $currentDat = new DateTime();
                                    $interval = $listedDat->diff($currentDat);
                                    $daysAgo = $interval->days; 

                                    $distance = NULL;

                                    echo '  <div class="col-xs-12 col-sm-6 col-md-4 food-item">
                                            <div class="food-item-wrap">
                                                <div class="figure-wrap bg-image" data-image-src="farmer/Res_img/products/'.$cat['img'].'"></div>
                                                <div class="content">
                                                    <h5><a href="products.php?res_id='.$cat['rs_id'].' && product_id='.$cat['d_id'].'">'.$cat['title'].'</a></h5>
                                                    <div class="product-name">'.$cat['name'].' - '.$cat['slogan'].'<br>'.$cat['quantity'].' '.$langStrings['available'].' <br><a href="map-popup" class="distance-link" data-lat="'.$farmer_coords['latitude'].'" data-lon="'.$farmer_coords['longitude'].'" data-city="'.$farmer_coords['city'].'">
                                    '.$farmer_coords['city'].' - '.($distance !== null ? round($distance, 1) : 'N/A').' km away
                                </a><br>';?> Listed <?php echo time_elapsed_string($cat['listed_at']); ?> <?php echo '</div>
                                                    <div class="price-btn-block">';

                                    if($cat['sale'] == 1) {
                                                     $percent_off = (100 - (($cat['discount']/$cat['price'])*100)); 
                                        //$percent_off;
                                        //echo '<font color="red">'; print_r($percent_off); echo'% off</font>';
                                                    echo '<span class="price"><span class="old-price">'.$langStrings['was'].' E'.$cat['price'].'</span> <font color="red" size="3px">'; print_r($percent_off); echo'% '.$langStrings['off'].'</font><br><span class="new-price"> '.$langStrings['now'].' E'.$cat['discount'].'</span></span> <a href="products.php?res_id='.$cat['rs_id'].' && product_id='.$cat['d_id'].'" class="btn theme-btn-dash pull-right">'.$langStrings['order_now'].'</a> </div>';
                                                    
                                                    // Check if the product is in the wishlist
                                                        if (in_array($product_id, $wishlist_items)) {
                                                            echo '<div class="wishlist-icon" data-product-id="'.$product_id.'">
                                                                    <i class="fa fa-heart"></i> <span><em> '.$langStrings['added_to_wish'].'</em></span>
                                                                  </div>';
                                                        } else {
                                                            echo '<div class="wishlist-icon" data-product-id="'.$product_id.'">
                                                                    <i class="fa fa-heart-o"></i><span><em> '.$langStrings['add_to_wish'].'</em></span>
                                                                  </div>';
                                                        }
                                                echo'</div>
                                                
                                            </div>
                                    </div>';  
                                    } else {
                                        echo '<span class="price">E'.$cat['price'].'</span> <a href="products.php?res_id='.$cat['rs_id'].' && product_id='.$cat['d_id'].'" class="btn theme-btn-dash pull-right">'.$langStrings['order_now'].'</a> </div>';
                                                    
                                                    // Check if the product is in the wishlist
                                                        if (in_array($product_id, $wishlist_items)) {
                                                            echo '<div class="wishlist-icon" data-product-id="'.$product_id.'">
                                                                    <i class="fa fa-heart"></i> <span><em> '.$langStrings['added_to_wish'].'</em></span>
                                                                  </div>';
                                                        } else {
                                                            echo '<div class="wishlist-icon" data-product-id="'.$product_id.'">
                                                                    <i class="fa fa-heart-o"></i><span><em> '.$langStrings['add_to_wish'].'</em></span><br><br>
                                                                  </div>';
                                                        }
                                                echo'</div>
                                                
                                            </div>
                                    </div>';
                                    }                                  
                                
                                        }
                                    }

                                    // Fetch all products excluding recommended ones
                                if (!empty($recommended_product_ids)) {
                                    $not_in_clause = "AND p.d_id NOT IN (" . implode(',', $recommended_product_ids) . ")";
                                } else {
                                    $not_in_clause = "";
                                }

                                $query_res = mysqli_query($db, "
                                    SELECT p.*, c.name 
                                    FROM products p 
                                    JOIN product_categories c ON p.category_id = c.cat_id 
                                    WHERE 1=1
                                    $not_in_clause
                                    ORDER BY p.listed_at DESC
                                ");




                                    while($r=mysqli_fetch_array($query_res))
                                                                {

                                    // Fetch the farmer's coordinates
                                     $farmer_id = $r['rs_id'];
                                     $product_id = $r['d_id'];
                                     $farmer_query = mysqli_query($db, "SELECT * FROM farmer WHERE rs_id = '$farmer_id'");
                                     $farmer_coords = mysqli_fetch_assoc($farmer_query);
                                     

                                    $listedDat = new DateTime($r['listed_at']);
                                    $currentDat = new DateTime();
                                    $interval = $listedDat->diff($currentDat);
                                    $daysAgo = $interval->days; 

                                    $distance = NULL;

                                    echo '  <div class="col-xs-12 col-sm-6 col-md-4 food-item">
                                            <div class="food-item-wrap">
                                                <div class="figure-wrap bg-image" data-image-src="farmer/Res_img/products/'.$r['img'].'"></div>
                                                <div class="content">
                                                    <h5><a href="products.php?res_id='.$r['rs_id'].' && product_id='.$r['d_id'].'">'.$r['title'].'</a></h5>
                                                    <div class="product-name">'.$r['name'].' - '.$r['slogan'].'<br>'.$r['quantity'].' '.$langStrings['available'].' <br><a href="map-popup" class="distance-link" data-lat="'.$farmer_coords['latitude'].'" data-lon="'.$farmer_coords['longitude'].'" data-city="'.$farmer_coords['city'].'">
                                    '.$farmer_coords['city'].' - '.($distance !== null ? round($distance, 1) : 'N/A').' km away
                                </a><br>';?> Listed <?php echo time_elapsed_string($r['listed_at']); ?> <?php echo '</div>
                                                    <div class="price-btn-block">';

                                    if($r['sale'] == 1) {
                                         
                                        $percent_off = (100 - (($r['discount']/$r['price'])*100)); 
                                        //$percent_off;
                                        //echo '<font color="red">'; print_r($percent_off); echo'% off</font>';
                                                    echo '<span class="price"><span class="old-price">'.$langStrings['was'].' E'.$r['price'].'</span> <font color="red" size="3px">'; print_r($percent_off); echo'% '.$langStrings['off'].'</font><br><span class="new-price"> '.$langStrings['now'].' E'.$r['discount'].'</span></span> <a href="products.php?res_id='.$r['rs_id'].' && product_id='.$r['d_id'].'" class="btn theme-btn-dash pull-right">'.$langStrings['order_now'].'</a> </div>';
                                                    
                                                    // Check if the product is in the wishlist
                                                        if (in_array($product_id, $wishlist_items)) {
                                                            echo '<div class="wishlist-icon" data-product-id="'.$product_id.'">
                                                                    <i class="fa fa-heart"></i> <span><em> '.$langStrings['added_to_wish'].'</em></span>
                                                                  </div>';
                                                        } else {
                                                            echo '<div class="wishlist-icon" data-product-id="'.$product_id.'">
                                                                    <i class="fa fa-heart-o"></i><span><em> '.$langStrings['add_to_wish'].'</em></span>
                                                                  </div>';
                                                        }
                                                echo'</div>
                                                
                                            </div>
                                    </div>';  
                                    } else {
                                        echo '<span class="price">E'.$r['price'].'</span> <a href="products.php?res_id='.$r['rs_id'].' && product_id='.$r['d_id'].'" class="btn theme-btn-dash pull-right">'.$langStrings['order_now'].'</a> </div>';
                                                    
                                                    // Check if the product is in the wishlist
                                                        if (in_array($product_id, $wishlist_items)) {
                                                            echo '<div class="wishlist-icon" data-product-id="'.$product_id.'">
                                                                    <i class="fa fa-heart"></i> <span><em> '.$langStrings['added_to_wish'].'</em></span>
                                                                  </div>';
                                                        } else {
                                                            echo '<div class="wishlist-icon" data-product-id="'.$product_id.'">
                                                                    <i class="fa fa-heart-o"></i><span><em> '.$langStrings['add_to_wish'].'</em></span><br><br><br>
                                                                  </div>';
                                                        }
                                                echo'</div>
                                                
                                            </div>
                                    </div>';
                                    }                                  
                                }
                                ?>
                    
                </div>
                    <?php

        } else {
                    ?>
            <?php
            // Show latest product listings if no user activity
            $query_res = mysqli_query($db, "SELECT p.*, c.name FROM products p JOIN product_categories c ON p.category_id = c.cat_id ORDER BY p.listed_at DESC");
            ?> 
                <?php
                    while($r=mysqli_fetch_array($query_res))
                    {

                        // Fetch the farmer's coordinates
                        $farmer_id = $r['rs_id'];
                        $product_id = $r['d_id'];
                        $farmer_query = mysqli_query($db, "SELECT * FROM farmer WHERE rs_id = '$farmer_id'");
                        $farmer_coords = mysqli_fetch_assoc($farmer_query);
                        $listedDat = new DateTime($r['listed_at']);
                        $currentDat = new DateTime();
                        $interval = $listedDat->diff($currentDat);
                        $daysAgo = $interval->days; 
                        $distance = NULL;

                                    echo '  <div class="col-xs-12 col-sm-6 col-md-4 food-item">
                                            <div class="food-item-wrap">
                                                <div class="figure-wrap bg-image" data-image-src="farmer/Res_img/products/'.$r['img'].'"></div>
                                                <div class="content">
                                                    <h5><a href="products.php?res_id='.$r['rs_id'].' && product_id='.$r['d_id'].'">'.$r['title'].'</a></h5>
                                                    <div class="product-name">'.$r['name'].' - '.$r['slogan'].'<br>'.$r['quantity'].' '.$langStrings['available'].' <br><a href="map-popup" class="distance-link" data-lat="'.$farmer_coords['latitude'].'" data-lon="'.$farmer_coords['longitude'].'" data-city="'.$farmer_coords['city'].'">
                                    '.$farmer_coords['city'].' - '.($distance !== null ? round($distance, 1) : 'N/A').' km away
                                </a><br>';?> Listed <?php echo time_elapsed_string($r['listed_at']); ?> <?php echo '</div>
                                                    <div class="price-btn-block">';

                                    if($r['sale'] == 1) {
                                    $percent_off = (100 - (($r['discount']/$r['price'])*100)); 
                                        //$percent_off;
                                        //echo '<font color="red">'; print_r($percent_off); echo'% off</font>';
                                                    echo '<span class="price"><span class="old-price">'.$langStrings['was'].' E'.$r['price'].'</span> <font color="red" size="3px">'; print_r($percent_off); echo'% '.$langStrings['off'].'</font><br><span class="new-price"> '.$langStrings['now'].' E'.$r['discount'].'</span></span> <a href="products.php?res_id='.$r['rs_id'].' && product_id='.$r['d_id'].'" class="btn theme-btn-dash pull-right">'.$langStrings['order_now'].'</a> </div>';
                                                    
                                                // Check if the product is in the wishlist
                                                        if (in_array($product_id, $wishlist_items)) {
                                                            echo '<div class="wishlist-icon" data-product-id="'.$product_id.'">
                                                                    <i class="fa fa-heart"></i> <span><em> '.$langStrings['added_to_wish'].'</em></span>
                                                                  </div>';
                                                        } else {
                                                            echo '<div class="wishlist-icon" data-product-id="'.$product_id.'">
                                                                    <i class="fa fa-heart-o"></i><span><em> '.$langStrings['add_to_wish'].'</em></span>
                                                                  </div>';
                                                        }
                                                echo'</div>
                                                
                                            </div>
                                    </div>';
                                    } else {
                                        echo '<span class="price">E'.$r['price'].'</span> <a href="products.php?res_id='.$r['rs_id'].' && product_id='.$r['d_id'].'" class="btn theme-btn-dash pull-right">'.$langStrings['order_now'].'</a> </div>';
                                        // Check if the product is in the wishlist
                                                        if (in_array($product_id, $wishlist_items)) {
                                                            echo '<div class="wishlist-icon" data-product-id="'.$product_id.'">
                                                                    <i class="fa fa-heart"></i> <span><em> '.$langStrings['added_to_wish'].'</em></span>
                                                                  </div>';
                                                        } else {
                                                            echo '<div class="wishlist-icon" data-product-id="'.$product_id.'">
                                                                    <i class="fa fa-heart-o"></i><span><em> '.$langStrings['add_to_wish'].'</em></span>
                                                                  </div>';
                                                        }
                                                echo'</div>
                                                
                                            </div>
                                    </div>';
                                    }                                  
                                }
                            }
          
?>

                    </div>
                        </div>
  
                        <div class="tab-content active" id="growing-products">
                    <h2>Growing Products</h2>
                            <div class="row">
                                <?php
                                $query_res2 = mysqli_query($db, "select * from growing");
                                while ($r = mysqli_fetch_array($query_res2)) {
                                    
                                    echo '<div class="col-xs-12 col-sm-6 col-md-3 food-item">
                                        <div class="food-item-wrap">
                                            <div class="figure-wrap bg-image" data-image-src="farmer/Res_img/products/'.$r['img'].'"></div>
                                            <div class="content">
                                                <h5><a href="products.php?res_id='.$r['rs_id'].'">'.$r['title'].'</a></h5>
                                                <div class="product-name">'.$r['slogan'].' <br><b>'.$r['quantity'].'</b> products estimated to be available on '.$r['estimated_readiness_date'].' <br>'.$r['progress'].'% Ready</div>
                                                <div class="price-btn-block"> <span class="price">E'.$r['price'].'</span> <a href="products.php?res_id='.$r['rs_id'].'" class="btn theme-btn-dash pull-right">Visit farmer</a> </div>
                                            </div>
                                        </div>
                                    </div>';
                                }
                                ?>
                            </div>
                        </div>
                    
                        <div class="tab-content active" id="requested-products">
                    <h2>Requested Products</h2>
                            <div class="row">
                                <?php
                                $query_res3 = mysqli_query($db, "SELECT pr.*, u.username FROM product_requests pr INNER JOIN users u ON pr.u_id = u.u_id");
                                while ($r = mysqli_fetch_array($query_res3)) {
                                    $listedDate = new DateTime($r['listing_date']);
                                    $currentDate = new DateTime();
                                    $interval = $listedDate->diff($currentDate);
                                    $daysAgo = $interval->days;

                                    echo '<div class="col-xs-12 col-sm-6 col-md-3 food-item">
                                        <div class="food-item-wrap">
                                            <div class="figure-wrap bg-image" data-image-src="farmer/Res_img/products/'.$r['img'].'"></div>
                                            <div class="content">
                                                <div class="product-name">'.$r['product_name'].' <br><b>'.$r['quantity'].'</b> Requested <br>Listed by '.$r['username'].' <br>Listed '.$daysAgo.' days ago</div>
                                            </div>
                                        </div>
                                    </div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="map-popup">
        <span class="close-btn">×</span>
        <div id="popup-map" style="height: 300px;"></div>
    </div>

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
                        <h5>Additional information</h5>
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
        $(document).ready(function() {
            $('.tab').click(function() {
                var tab_id = $(this).attr('data-tab');
                $('.tab').removeClass('active');
$('.tab-content').removeClass('active');
 $(this).addClass('active');
 $("#" + tab_id).addClass('active');
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.js"></script>
   <script>
        document.addEventListener('DOMContentLoaded', function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var userLat = position.coords.latitude;
            var userLon = position.coords.longitude;

            document.querySelectorAll('.distance-link').forEach(function(link) {
                var farmerLat = parseFloat(link.getAttribute('data-lat'));
                var farmerLon = parseFloat(link.getAttribute('data-lon'));
                var farmerCity = link.getAttribute('data-city'); // Assuming you have the city data attribute

                // Calculate distance using the same method
                var distance = haversine(userLat, userLon, farmerLat, farmerLon);
                link.innerHTML = farmerCity + ' - ' + distance.toFixed(1) + ' km away';

                // Add click event listener for map popup
                link.addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent the default link behavior

                    // Show popup
                    var popup = document.getElementById('map-popup');
                    popup.style.display = 'block';

                    // Initialize map
                    var map = L.map('popup-map').setView([farmerLat, farmerLon], 13);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                    }).addTo(map);

                    L.marker([userLat, userLon]).addTo(map)
                        .bindPopup('You are here')
                        .openPopup();

                    L.marker([farmerLat, farmerLon]).addTo(map)
                        .bindPopup('Farmer Location')
                        .openPopup();

                    L.Routing.control({
                        waypoints: [
                            L.latLng(userLat, userLon),
                            L.latLng(farmerLat, farmerLon)
                        ],
                        createMarker: function() {
                            return null; // Disable default markers
                        }
                    }).addTo(map);

                    // Calculate distance using the same method
                    var distance = haversine(userLat, userLon, farmerLat, farmerLon);
                    document.getElementById('distance-info').innerText = 'Distance: ' + distance.toFixed(2) + ' km';
                });
            });
        }, function() {
            alert("Unable to retrieve your location.");
        });
    } else {
        alert("Geolocation is not supported by this browser.");
    }
});

function haversine(lat1, lon1, lat2, lon2) {
    var R = 6371; // Radius of the Earth in kilometers
    var dLat = deg2rad(lat2 - lat1);
    var dLon = deg2rad(lon2 - lon1);
    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c; // Distance in kilometers
}

function deg2rad(deg) {
    return deg * (Math.PI / 180);
}

document.querySelector('#map-popup .close-btn').addEventListener('click', function() {
    document.getElementById('map-popup').style.display = 'none';
    var mapContainer = document.getElementById('popup-map');
    mapContainer._leaflet_id = null; // Reset the map container
});

    </script>
    <script>
    $(document).ready(function() {
        $('#search-input').on('input', function() {
            var query = $(this).val();
            if (query.length > 0) {
                $.ajax({
                    url: 'search_suggestions.php',
                    method: 'GET',
                    data: { query: query },
                    success: function(data) {
                        $('.autocomplete-suggestions').remove();
                        var suggestions = JSON.parse(data);
                        if (suggestions.length > 0) {
                            var suggestionsList = '<div class="autocomplete-suggestions">';
                            suggestions.forEach(function(suggestion) {
                                suggestionsList += '<div class="autocomplete-suggestion">' + suggestion + '</div>';
                            });
                            suggestionsList += '</div>';
                            $('#search-form').append(suggestionsList);
                            $('.autocomplete-suggestions').show();
                        }
                    }
                });
            } else {
                $('.autocomplete-suggestions').remove();
            }
        });

        $(document).on('click', '.autocomplete-suggestion', function() {
            var text = $(this).text();
            $('#search-input').val(text);
            $('.autocomplete-suggestions').remove();
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#search-form').length) {
                $('.autocomplete-suggestions').remove();
            }
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const wishlistIcons = document.querySelectorAll('.wishlist-icon');

    wishlistIcons.forEach(icon => {
        icon.addEventListener('click', function () {
            const productId = this.getAttribute('data-product-id');
            const userId = <?php echo $_SESSION['user_id']; ?>;
            
            if (userId) {
                fetch('add_to_wishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ user_id: userId, product_id: productId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.querySelector('i').classList.toggle('fa-heart-o');
                        this.querySelector('i').classList.toggle('fa-heart');
                    } else {
                        alert('Could not add to wishlist');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            } else {
                alert('You must be logged in to add to the wishlist');
            }
        });
    });
});
</script>
</body>
</html>