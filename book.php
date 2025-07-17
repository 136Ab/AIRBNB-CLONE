<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "Please log in to book a property.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = (int)$_POST['property_id'];
    $user_id = $_SESSION['user_id'];
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $total_price = (float)$_POST['total_price'];

    try {
        $stmt = $pdo->prepare("INSERT INTO bookings (property_id, user_id, user_name, user_email, check_in, check_out, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$property_id, $user_id, $user_name, $user_email, $check_in, $check_out, $total_price]);
        echo "Booking successful!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
