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
                        <li class="nav-item"> <a class="nav-link active" href="index.php"><i class="fa fa-home"></i> <?php echo $langStrings['home'];?> <span class="sr-only">(current)</span></a> </li>
                        <li class="nav-item"> <a class="nav-link active" href="../VFM/farmer/index.php"><?php echo $langStrings['switch'];?><span class="sr-only"></span></a> </li>
                        <?php 

                        if(empty($_SESSION["user_id"])): ?>
                            <li class="nav-item"><a href="login.php" class="nav-link active"><i class="fa fa-power-off"></i> <?php echo $langStrings['login'];?></a></li>
                            <li class="nav-item"><a href="registration.php" class="nav-link active"><?php echo $langStrings['register'];?></a></li>
                            <li class="nav-item dropdown">
                                <a href="javascript:void(0);" class="nav-link dropdown-toggle text-muted"><?php echo $langStrings['language'];?></a>
                                <div class="dropdown-content">
                                    <a href="<?php echo $base_url; ?>&lang=en">English</a>
                                    <a href="<?php echo $base_url; ?>&lang=sw">SiSwati</a>
                                </div>
                            </li>
                            <li class="nav-item"><a href="user_manual.php" class="nav-link active"><i class="fa fa-book"></i> <?php echo $langStrings['manual'];?></a></li>
                        <?php 
                        else:
                            ?>
                            <?php
                            
                            $u_id = $_SESSION['user_id'];
                            $orders_query = "SELECT * FROM users_orders WHERE u_id = '$u_id'";
                            $orders_result = mysqli_query($db, $orders_query);
                            $orders_count = mysqli_num_rows($orders_result);
                            $msg_query = "SELECT * FROM messages WHERE user_id = '$u_id' AND read_status = 0";
                            $msg_result = mysqli_query($db, $msg_query);
                            $msg_count = mysqli_num_rows($msg_result);
                            $m_query = mysqli_query($db, "SELECT * FROM messages WHERE user_id = '$u_id' ORDER BY created_at DESC");   
                            $msg = mysqli_fetch_assoc($m_query);
                            ?>
                            <li class="nav-item"><a href="your_orders.php" class="nav-link active"><i class="fa fa-shopping-cart"></i> <span><?php echo $langStrings['orders'];?> (<?= $orders_count ?>)</span></a></li>
                            <li class="nav-item"><a href="customer_requests.php" class="nav-link active"><i class="fa fa-plus-circle"></i> <?php echo $langStrings['request'];?></a></li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-muted" href="javascript:void(0);" id="notificationsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-bell"></i> 
                                    <?php
                                    $u_id = $_SESSION['user_id'];
                                    $notifications_query = "SELECT * FROM notifications WHERE u_id = '$u_id' AND read_status = 0";
                                    $notifications_result = mysqli_query($db, $notifications_query);
                                    $unread_count = mysqli_num_rows($notifications_result);
                                    ?>
                                    <?= $unread_count ?> <?php echo $langStrings['notifications'];?>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right animated zoomIn" aria-labelledby="notificationsDropdown">
                                    <ul class="dropdown-user">
                                        <?php
                                        $notif_query = mysqli_query($db, "SELECT * FROM notifications WHERE u_id = '$u_id' ORDER BY created_at DESC LIMIT 10");
                                        if (mysqli_num_rows($notif_query) > 0) {
                                            while ($notification = mysqli_fetch_assoc($notif_query)) {
                                                $notification_id = $notification['id'];
                                                echo '<li class="nav-item"><a href="read_notification.php?id=' . $notification_id . '"><i class="fa fa-info-circle"></i><font size="2px"> ' . $notification['message'] . '</font></a></li>';
                                            } 
                                        } else {
                                            echo '<li class="nav-item"><a href="#"><i class="fa fa-info-circle"></i> No new notifications</a></li>';
                                        }
                                        ?>       
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <?php 
                                $ress= mysqli_query($db,"select * from users where u_id='".$_SESSION["user_id"]."'");
                                $row=mysqli_fetch_array($ress); 
                                ?>
                                <a href="javascript:void(0);" class="nav-link dropdown-toggle text-muted"><?php echo '<i class="fa fa-user"></i> '.$langStrings['hi'].', '.$row['username'].'';?></a>
                                <div class="dropdown-content">
                                    <a href="my_cart.php"><i class="fa fa-shopping-cart"></i> <?php echo $langStrings['cart'];?> (<?php echo $cartItemCount; ?>)</a>
                                    <a href="customer_inbox.php"><i class="fa fa-comments"></i> <?php echo $langStrings['inbox'];?></a>
                                    <a href="wishlist.php"><i class="fa fa-heart"></i> <?php echo $langStrings['wish'];?></a>
                                    <a href="user.php"><i class="fa fa-user"></i> <?php echo $langStrings['profile'];?></a>
                                    <a href="logout.php"><i class="fa fa-power-off"></i> <?php echo $langStrings['logout'];?></a>
                                    <a href="user_manual.php"><i class="fa fa-book"></i> <?php echo $langStrings['manual'];?></a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a href="javascript:void(0);" class="nav-link dropdown-toggle text-muted"><?php echo $langStrings['language'];?></a>
                                <div class="dropdown-content">
                                    <a href="<?php echo $base_url; ?>&lang=en">English</a>
                                    <a href="<?php echo $base_url; ?>&lang=sw">SiSwati</a>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>     
                </div>
                
            </div>
        </nav>
    </header>