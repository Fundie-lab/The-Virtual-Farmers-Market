<?php include('language_switcher.php'); ?>
<!DOCTYPE html> 
<?php
include("connection/connect.php");  
error_reporting(0);  
session_start();

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
                    <div class="container">
    
                    </div>
                    <h2>Product Search</h2>
                    <p class="lead">Top search results</p>
                </div>
                <div class="row">
                        <?php  


                        $search_query = isset($_GET['query']) ? $_GET['query'] : '';
                        if ($search_query != '') {
                        // Sanitize the input
                            $search_query = mysqli_real_escape_string($db, $search_query);
                            // Query the database for products matching the search query
                            $query= "SELECT p.*, c.name FROM products p JOIN product_categories c ON p.category_id = c.cat_id WHERE title LIKE '%$search_query%'";  
                        $counter = 0; 
                            $result = mysqli_query($db, $query);

                            // Check if any products match the search query
                            if (mysqli_num_rows($result) > 0) {
                                // Fetch and display the matching products
                                    while ($r = mysqli_fetch_assoc($result)) {
                                        
                                        // Fetch the farmer's coordinates
                                     $farmer_id = $r['rs_id'];
                                     $product_id = $r['d_id'];
                                     $farmer_query = mysqli_query($db, "SELECT * FROM farmer WHERE rs_id = '$farmer_id'");
                                     $farmer_coords = mysqli_fetch_assoc($farmer_query);
                                     $farmer_lat = $farmer_coords['latitude'];
                                     $farmer_lon = $farmer_coords['longitude'];

                                    $listedDat = new DateTime($r['listed_at']);
                                    $currentDat = new DateTime();
                                    $interval = $listedDat->diff($currentDat);
                                    $daysAgo = $interval->days; 

                                    $distance = null;

                                    echo '  <div class="col-xs-12 col-sm-6 col-md-4 food-item">
                                            <div class="food-item-wrap">
                                                <div class="figure-wrap bg-image" data-image-src="farmer/Res_img/products/'.$r['img'].'"></div>
                                                <div class="content">
                                                    <h5><a href="products.php?res_id='.$r['rs_id'].'">'.$r['title'].'</a></h5>
                                                    <div class="product-name">'.$r['name'].' - '.$r['slogan'].'<br>'.$r['quantity'].' '.$langStrings['available'].' <br><a href="map-popup" class="distance-link" data-lat="'.$farmer_coords['latitude'].'" data-lon="'.$farmer_coords['longitude'].'" data-city="'.$farmer_coords['city'].'">
                                    '.$farmer_coords['city'].' - '.($distance !== null ? round($distance, 1) : 'N/A').' km away
                                </a><br>';?> Listed <?php echo time_elapsed_string($r['listed_at']); ?> <?php echo '</div>
                                                    <div class="price-btn-block">';

                                    if($r['sale'] == 1) {
                                                     $percent_off = (100 - (($r['discount']/$r['price'])*100)); 
                                        //$percent_off;
                                        //echo '<font color="red">'; print_r($percent_off); echo'% off</font>';
                                                    echo '<span class="price"><span class="old-price">'.$langStrings['was'].' E'.$r['price'].'</span> <font color="red" size="3px">'; print_r($percent_off); echo'% '.$langStrings['off'].'</font><br><span class="new-price">'.$langStrings['now'].' E'.$r['discount'].'</span></span> <a href="products.php?res_id='.$r['rs_id'].' && product_id='.$r['d_id'].'" class="btn theme-btn-dash pull-right">'.$langStrings['order_now'].'</a> </div>
                                                    <div class="wishlist-icon" data-product-id="'.$r['d_id'].'">
                        <i class="fa fa-heart-o" aria-hidden="true"></i>
                    </div>
                                                </div>
                                                
                                            </div>
                                    </div>';   
                                    } else {
                                        echo '<span class="price">E'.$r['price'].'</span> <a href="products.php?res_id='.$r['rs_id'].' && product_id='.$r['d_id'].'" class="btn theme-btn-dash pull-right">'.$langStrings['order_now'].'</a> </div>
                                        <div class="wishlist-icon" data-product-id="'.$r['d_id'].'">
                        <i class="fa fa-heart-o" aria-hidden="true"></i>
                    </div>

                                                </div>
                                                
                                            </div>
                                    </div>';
                                    }
                                    }
                            } 
                            else {
                                echo "<p>No products found matching your search query.</p>";
                            }
                        } 
                        else {
                            echo "<p>Please enter a search query.</p>";
                        }

                        ?>
                </div>
                <div class="row">
                <div class="col-xs-12 text-center">
                    <center><a href="all_products.php"><button id="view-more" class="btn theme-btn">View all products</button></a></center>
                </div>
            </div>
        </div>
    </section>
    <section class="how-it-works">
        <div class="container">
            
        </div>
        </section>
    <section class="featured-restaurants">
        <div class="container">
            <div class="row">
                <div class="col-sm-4">
                    <div class="title-block pull-left">
                        <h4>Featured Farmers & Markets</h4> 
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="restaurants-filter pull-right">
                        <nav class="primary pull-left">
                            <ul>
                                <li>
                                    <a href="#" class="selected" data-filter="*">all
                                    </a>
                                </li>
                                <?php 
                                $res= mysqli_query($db,"select * from res_category");
                                while($row=mysqli_fetch_array($res))
                                {
                                    echo '<li><a href="#" data-filter=".'.$row['c_name'].'"> '.$row['c_name'].'</a> </li>';
                                }
                                ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="restaurant-listing">
                    <?php
                    // Fetch farmers
                    $farmers = mysqli_query($db, "select * from farmer");
                    while($farmer = mysqli_fetch_array($farmers)) {
                        $query= mysqli_query($db, "select * from res_category where c_id='".$farmer['c_id']."' ");
                        $category = mysqli_fetch_array($query);
                        $review_sql = "SELECT rating FROM reviews WHERE farmer_id='".$farmer['rs_id']."'";
                        $review_result = mysqli_query($db, $review_sql);
                        $total_rating = 0;
                        $review_count = 0;
                        while ($review_row = mysqli_fetch_assoc($review_result)) {
                            $total_rating += $review_row['rating'];
                            $review_count++;
                        }
                        $average_rating = $review_count > 0 ? $total_rating / $review_count : 0;
                        $average_rating = round($average_rating, 1); // Round to 1 decimal place
                                    
                        echo '
                            <div class="col-xs-12 col-sm-12 col-md-6 single-restaurant all '.$category['c_name'].'">
                                <div class="restaurant-wrap">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-3 col-md-12 col-lg-3 text-xs-center">
                                            <a class="restaurant-logo" href="products.php?res_id='.$farmer['rs_id'].'"> <img src="farmer/Res_img/'.$farmer['image'].'" alt="Farmer logo"> </a>
                                        </div>
                                        <div class="col-xs-12 col-sm-9 col-md-12 col-lg-9">
                                            <h5><a href="products.php?res_id='.$farmer['rs_id'].'">'.$farmer['title'].'</a></h5> <span>'.$farmer['address'].' <br>'?> Rating:
                                            <?php for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $average_rating) {
                                            echo '<i class="fa fa-star star"></i>';
                                        } else {
                                            echo '<i class="fa fa-star-o star"></i>';
                                        }
                                    }?>      (<?php echo $average_rating; ?> stars) <?php echo '</span>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                        }
                        // Fetch markets
                        $markets = mysqli_query($db, "select * from market");
                        while($market = mysqli_fetch_array($markets)) {
                            $query= mysqli_query($db, "select * from res_category where c_id='".$market['c_id']."' ");
                            $category = mysqli_fetch_array($query);
                            echo '
                            <div class="col-xs-12 col-sm-12 col-md-6 single-restaurant all '.$category['c_name'].'">
                                <div class="restaurant-wrap">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-3 col-md-12 col-lg-3 text-xs-center">
                                            <a class="restaurant-logo" href="marketproducts.php?res_id='.$market['mar_id'].'"> <img src="farmer/Res_img/'.$market['image'].'" alt="Market logo"> </a>
                                        </div>
                                        <div class="col-xs-12 col-sm-9 col-md-12 col-lg-9">
                                            <h5><a href="marketproducts.php?res_id='.$market['mar_id'].'">'.
                                            $market['title'].'</a></h5> <span>'.$market['address'].'</span>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                        }
                        ?>    
                        </div>
                    </div>
                </div>
            </section>
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
                        <h5>Phone: (+268) 7943 9397</a></h5> </div>
                        <div class="col-xs-12 col-sm-5 additional-info color-gray">
                            <h5>Addition informations</h5>
                           <p>Join thousands of other farmers who benefit from having partnered with us.</p>
                        </div>
                    </div>
                </div>
            </div>  
        </div>
    </footer>
    <script src="js/buut.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="js/tether.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/animsition.min.js"></script>
    <script src="js/bootstrap-slider.min.js"></script>
    <script src="js/jquery.isotope.min.js"></script>
    <script src="js/headroom.js"></script>
    <script src="js/foodpicky.min.js"></script>
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
</body>
</html>