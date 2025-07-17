<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in to view your bookings.'); window.location.href='login.php';</script>";
    exit;
}

$stmt = $pdo->prepare("SELECT b.*, p.title, p.image FROM bookings b JOIN properties p ON b.property_id = p.id WHERE b.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Airbnb Clone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background: linear-gradient(135deg, #f7b733, #ff5e62);
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
        }
        header h1 {
            font-size: 2em;
            color: #ff5e62;
        }
        .bookings {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .booking-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .booking-card:hover {
            transform: translateY(-5px);
        }
        .booking-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }
        .booking-card p {
            margin: 10px 0;
            color: #666;
        }
        .booking-card .price {
            font-size: 1.2em;
            color: #ff5e62;
        }
        .back-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background: #ff5e62;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            text-align: center;
            text-decoration: none;
        }
        .back-button:hover {
            background: #e04e52;
        }
        @media (max-width: 768px) {
            .bookings {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>My Bookings</h1>
        </header>
        <div class="bookings">
            <?php if (empty($bookings)): ?>
                <p>No bookings found.</p>
            <?php else: ?>
                <?php foreach ($bookings as $booking): ?>
                    <div class="booking-card">
                        <img src="<?php echo htmlspecialchars($booking['image']); ?>" alt="<?php echo htmlspecialchars($booking['title']); ?>">
                        <p><strong>Property:</strong> <?php echo htmlspecialchars($booking['title']); ?></p>
                        <p><strong>Check-in:</strong> <?php echo htmlspecialchars($booking['check_in']); ?></p>
                        <p><strong>Check-out:</strong> <?php echo htmlspecialchars($booking['check_out']); ?></p>
                        <p class="price">Total: $<?php echo number_format($booking['total_price'], 2); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <a href="index.php" class="back-button">Back to Home</a>
    </div>
</body>
</html>
