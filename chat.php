<?php include('language_switcher.php'); ?>
<!DOCTYPE html>
<?php
include("connection/connect.php");
error_reporting(0);
session_start();

$user_id = $_SESSION['user_id'];
$farmer_id = $_GET['farmer_id']; // Get the farmer_id from the URL
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : NULL; // Get the product_id from the URL if it exists
error_reporting(E_ALL);

// Fetch farmer details
$farmer_sql = "SELECT title, image FROM farmer WHERE rs_id = '$farmer_id'";
$farmer_query = mysqli_query($db, $farmer_sql);
$farmer = mysqli_fetch_assoc($farmer_query);

// Fetch chat messages and replies between the user and the farmer
$message_sql = "
    SELECT m.message_id, m.message, m.created_at AS message_time, 'user' AS sender, p.title AS product_title
    FROM messages m
    LEFT JOIN products p ON m.product_id = p.d_id
    WHERE m.user_id = '$user_id' AND m.farmer_id = '$farmer_id'
    UNION
    SELECT r.message_id, r.reply_content AS message, r.created_at AS message_time, 'farmer' AS sender, p.title AS product_title
    FROM replies r
    INNER JOIN messages m ON r.message_id = m.message_id
    LEFT JOIN products p ON m.product_id = p.d_id
    WHERE m.user_id = '$user_id' AND r.farmer_id = '$farmer_id'
    ORDER BY message_time ASC
";
$message_query = mysqli_query($db, $message_sql);

// Handle new message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = mysqli_real_escape_string($db, $_POST['message']);
    $created_at = date('Y-m-d H:i:s');

    // Insert new message into the messages table with product_id
    $insert_sql = "
        INSERT INTO messages (user_id, farmer_id, product_id, message, created_at, read_status)
        VALUES ('$user_id', '$farmer_id', '$product_id', '$message', '$created_at', 0)
    ";
    mysqli_query($db, $insert_sql);

    // Refresh the page to display the new message
    header("Location: chat.php?farmer_id=$farmer_id" . ($product_id ? "&product_id=$product_id" : ""));
    exit;
}
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="#">
    <title>Chat with <?php echo $farmer['title']; ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
    body {
        background: linear-gradient(to right, #f8f9fa, #e9ecef);
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 0;
    }

    .chat-container {
        width: 100%;
        max-width: 800px;
        margin: 30px auto;
        padding: 20px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        overflow: hidden;
        transition: background 0.3s, box-shadow 0.3s;
    }

    .chat-container:hover {
        background: rgba(255, 255, 255, 0.3);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.4);
    }

    .chat-header {
        display: flex;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.5);
        padding-bottom: 15px;
        margin-bottom: 20px;
        color: #fff;
    }

    .chat-header img {
        border-radius: 50%;
        width: 60px;
        height: 60px;
        margin-right: 15px;
        border: 2px solid #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .chat-header h3 {
        font-size: 1.5em;
        margin: 0;
    }

    .chat-messages {
        height: 400px;
        overflow-y: auto;
        margin-bottom: 20px;
        padding-right: 10px;
    }

    .message {
        padding: 12px;
        margin: 10px 0;
        border-radius: 12px;
        max-width: 70%;
        position: relative;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        transition: background 0.3s;
    }

    .message:hover {
        background: rgba(255, 255, 255, 0.9);
    }

    .message.user {
        margin-left: auto;
        background: rgba(0, 204, 102, 0.9); /* Slightly darker green */
        color: #fff;
    }

    .message.farmer {
        background: rgba(255, 87, 87, 0.9); /* Slightly darker red */
        color: #fff;
    }

    .chat-input {
        display: flex;
        border-top: 1px solid rgba(255, 255, 255, 0.5);
        padding-top: 15px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(15px);
    }

    .chat-input textarea {
        flex-grow: 1;
        resize: none;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        background: rgba(255, 255, 255, 0.7);
        color: #333;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .chat-input button {
        margin-left: 10px;
        padding: 12px 24px;
        background: linear-gradient(45deg, #28a745, #218838);
        color: white;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-size: 1em;
        transition: background 0.3s, transform 0.2s;
    }

    .chat-input button:hover {
        background: linear-gradient(45deg, #218838, #28a745);
        transform: scale(1.05);
    }

    .product-title {
        font-weight: bold;
        margin-bottom: 5px;
        color: #fff;
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
                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-2"></div>
                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                        <div class="chat-container">
                            <div class="chat-header">
                                <img src="../VFM/farmer/Res_img/<?php echo $farmer['image']; ?>" alt="Farmer Image">
                                <h3><?php echo $farmer['title']; ?></h3>
                            </div>
                            <div class="chat-messages">
                                <?php
                                if(mysqli_num_rows($message_query) > 0) {
                                    while($message = mysqli_fetch_assoc($message_query)) {
                                        $message_class = $message['sender'] == 'user' ? 'user' : 'farmer';
                                        $product_title = $message['product_title'] ? '<div class="product-title">Product: '.$message['product_title'].'</div>' : '';
                                        echo '<div class="message '.$message_class.'">'.$product_title.$message['message'].'</div>';
                                    }
                                } else {
                                    echo '<p>No messages yet. Start the conversation!</p>';
                                }
                                ?>
                            </div>
                            <div class="chat-input">
                                <form method="POST" action="">
                                    <textarea name="message" rows="2" placeholder="Type your message here..." required></textarea>
                                    <button type="submit">Send</button>
                                </form>
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
