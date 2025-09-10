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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.css" />

    <link rel="stylesheet" href="css/navstyle.css">
</head>
<body class="home">
     <?php 
$current_url = $_SERVER['REQUEST_URI'];
        $parsed_url = parse_url($current_url);
        parse_str($parsed_url['query'], $query_params);

        // Remove the 'lang' parameter if it exists
        unset($query_params['lang']);

        // Build the base URL without the 'lang' parameter
        $base_url = $parsed_url['path'] . '?' . http_build_query($query_params);



        // Initialize the count to 0
$cartItemCount = 0;

// Check if the cart exists and has items
if (isset($_SESSION['cart_item']) && is_array($_SESSION['cart_item'])) {
    $cartItemCount = count($_SESSION['cart_item']);
}
?>
<header id="header" class="header-scroll top-header headrom">
        <nav class="navbar navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="index.php"> <img class="img-rounded" src="images/icn.png" alt=""> </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavbarCollapse" aria-controls="mainNavbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <a href="javascript:void(0);" class="toggle-button">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </a>
                </button>
                <div class="collapse navbar-toggleable-md float-lg-right navbar-links" id="mainNavbarCollapse">
                    <ul class="nav navbar-nav">
                        <li class="nav-item"> <a class="nav-link active" href="login.php"><i class="fa fa-home"></i> <?php echo $langStrings['home'];?> <span class="sr-only">(current)</span></a> </li>
                    </ul>     
                </div>
                
            </div>
        </nav>
    </header>
    <section class="hero bg-image" data-image-src="images/img/backg.png">
        <div class="hero-inner">
            <h1 style="color: red;">YOUR ACCOUNT HAS BEEN SUSPENDED</h1>

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
        // Auto-suggestion functionality
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
        // Select suggestion from the list
        $(document).on('click', '.autocomplete-suggestion', function() {
            var text = $(this).text();
            $('#search-input').val(text);
            $('.autocomplete-suggestions').remove();
        });
        // Close suggestions when clicking outside the form
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#search-form').length) {
                $('.autocomplete-suggestions').remove();
            }
        });
        // Voice search functionality
        $('#voiceSearchBtn').on('click', function() {
            var recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
            recognition.lang = 'en-US';  // Set the language
            recognition.interimResults = false;  // Don't show interim results
            recognition.maxAlternatives = 1;  // Only one result needed
            
            recognition.start();  // Start listening
            recognition.onresult = function(event) {
                var voiceSearchQuery = event.results[0][0].transcript;
                voiceSearchQuery = voiceSearchQuery.replace(/[.,!?;:]$/, '');
                $('#search-input').val(voiceSearchQuery);  // Set the transcribed text in the input field
                $('#search-form').submit();  // Trigger the form submission to perform the search
            };
            recognition.onerror = function(event) {
                console.error('Speech recognition error: ' + event.error);
            };
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