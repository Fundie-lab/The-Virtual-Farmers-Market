<?php
require('farmer/fpdf186/fpdf.php');
 include("connection/connect.php");
 session_start();
 if (empty($_SESSION["user_id"])) {
    die("User not logged in.");
 }
 class PDF extends FPDF {
    // Page header
    function Header() {
        global $order;
        if ($order['image']) {
            $this->Image('farmer/Res_img/' . $order['image'], 10, 6, 30);
        }
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(80);
        $this->Cell(30, 10, 'Receipt from ' . $order['rs_name'], 0, 1, 'C');
        $this->Ln(5);
    }
    // Page footer
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
 }
 if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $sql = "SELECT users.*, users_orders.*, farmer.title as rs_name, farmer.image as image 
            FROM users 
            INNER JOIN users_orders ON users.u_id = users_orders.u_id 
            INNER JOIN farmer ON users_orders.rs_id = farmer.rs_id
            WHERE users_orders.o_id = '$order_id' AND users_orders.u_id = '".$_SESSION['user_id']."'";
    $query = mysqli_query($db, $sql);
    if ($query) {
        $order = mysqli_fetch_assoc($query);
        if ($order) {
            $pdf = new PDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', '', 12);
            // Order Header
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Order Details', 0, 1, 'L');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, 'Order ID: ' . $order['o_id'], 0, 1, 'L');
            $pdf->Cell(0, 10, 'Date: ' . $order['date'], 0, 1, 'L');
            $pdf->Cell(0, 10, 'Customer Name: ' . $order['f_name'] . ' ' . $order['l_name'], 0, 1, 'L');
            $pdf->Cell(0, 10, 'Address: ' . $order['address'], 0, 1, 'L');
            $pdf->Ln(10);
            // Table Header
            $pdf->SetFillColor(200, 220, 255);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(80, 10, 'Item', 1, 0, 'C', true);
            $pdf->Cell(30, 10, 'Quantity', 1, 0, 'C', true);
            $pdf->Cell(30, 10, 'Price', 1, 0, 'C', true);
            $pdf->Cell(30, 10, 'Total', 1, 0, 'C', true);
            $pdf->Ln();
            // Table Content
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(80, 10, $order['title'], 1);
            $pdf->Cell(30, 10, $order['quantity'], 1);
            $pdf->Cell(30, 10, 'E' . $order['price'], 1);
            $pdf->Cell(30, 10, 'E' . ($order['price'] * $order['quantity']), 1);
            $pdf->Ln();
            // Total Amount
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(140, 10, 'Grand Total', 1);
            $pdf->Cell(30, 10, 'E' . ($order['price'] * $order['quantity']), 1);
            $pdf->Ln();
            // Footer Notes
$pdf->Ln(10);
 $pdf->SetFont('Arial', 'I', 10);
 $pdf->MultiCell(0, 10, "\nStamp/Signature:.....................................................", 0, 'C');
 $pdf->MultiCell(0, 10, "Thank you for your purchase!\nPlease visit again.", 0, 'C');
 $pdf->Output();     
   }else {
            echo "No order found.";
        }
    } else {
 echo "Query failed.";
    }
 } else {
    echo "Order ID not set.";
 }
 ?>