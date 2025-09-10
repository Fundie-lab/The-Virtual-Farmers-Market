<?php
include("connection/connect.php");  
error_reporting(E_ALL); 
ini_set('display_errors', 1); 
session_start(); 

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
 
        } else {
            // Update the product status to "medium"
    $update_query = "UPDATE products SET sale = 0 WHERE d_id =" . $r['d_id'];

 mysqli_query($db, $update_query);
        }
    } 
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

                        $query_res= mysqli_query($db,"select * from products ORDER BY d_id DESC LIMIT 6, 9999");  
                        $counter = 0;
                                while($r=mysqli_fetch_array($query_res))
                                {
                                    
                                    $listedDat = new DateTime($r['listed_at']);
                                    $currentDat = new DateTime();
                                    $interval = $listedDat->diff($currentDat);
                                    $daysAgo = $interval->days; 
                                    $hidden_class = $counter > 6 ? 'hidden' : '';
                                    echo '  <div class="col-xs-12 col-sm-6 col-md-4 food-item">
                                            <div class="food-item-wrap">
                                                <div class="figure-wrap bg-image" data-image-src="farmer/Res_img/products/'.$r['img'].'"></div>
                                                <div class="content">
                                                    <h5><a href="products.php?res_id='.$r['rs_id'].'">'.$r['title'].'</a></h5>
                                                    <div class="product-name">'.$r['slogan'].' <br>'.$r['quantity'].' Available <br>';?> Listed <?php echo time_elapsed_string($r['listed_at']); ?> <?php echo '</div>
                                                    <div class="price-btn-block"> <span class="price">';

                                    if($r['sale'] == 1) {
                                                    echo '<font color="red">ON SALE!</font></span> <a href="products.php?res_id='.$r['rs_id'].'" class="btn theme-btn-dash pull-right">Order Now</a> </div>
                                                </div>
                                                
                                            </div>
                                    </div>';  
                                    } else {
                                        echo 'E'.$r['price'].'</span> <a href="products.php?res_id='.$r['rs_id'].'" class="btn theme-btn-dash pull-right">Order Now</a> </div>
                                                </div>
                                                
                                            </div>
                                    </div>';
                                    }  
                                                                   
                                }   
                        ?>
                