<?php
session_start();
require 'db.php';
$property_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->execute([$property_id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    die("Property not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($property['title']); ?> - Airbnb Clone</title>
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .property-details {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .property-details img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }
        .property-details h1 {
            font-size: 2em;
            margin: 20px 0;
            color: #ff5e62;
        }
        .property-details p {
            margin: 10px 0;
            color: #666;
        }
        .property-details .price {
            font-size: 1.5em;
            color: #ff5e62;
        }
        .booking-form {
            margin-top: 20px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 10px;
        }
        .booking-form input {
            padding: 10px;
            margin: 10px 0;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .booking-form button, .nav-button {
            padding: 10px 20px;
            background: #ff5e62;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            margin: 10px;
        }
        .booking-form button:hover, .nav-button:hover {
            background: #e04e52;
        }
        @media (max-width: 768px) {
            .property-details img {
                height: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="property-details">
            <img src="<?php echo htmlspecialchars($property['image']); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>">
            <h1><?php echo htmlspecialchars($property['title']); ?></h1>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($property['location']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($property['description']); ?></p>
            <p><strong>Amenities:</strong> <?php echo htmlspecialchars($property['amenities']); ?></p>
            <p><strong>Rating:</strong> <?php echo number_format($property['rating'], 1); ?> ★</p>
            <p class="price">$<?php echo number_format($property['price'], 2); ?>/night</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="booking-form">
                    <h2>Book This Property</h2>
                    <input type="text" id="user_name" placeholder="Your Name" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" required>
                    <input type="email" id="user_email" placeholder="Your Email" required>
                    <input type="date" id="check_in" required>
                    <input type="date" id="check_out" required>
                    <button onclick="bookProperty(<?php echo $property['id']; ?>, <?php echo $property['price']; ?>)">Book Now</button>
                </div>
                <button class="nav-button" onclick="window.location.href='my_bookings.php'">View My Bookings</button>
            <?php else: ?>
                <p>Please <a href="login.php" style="color: #ff5e62;">log in</a> to book this property.</p>
            <?php endif; ?>
        </div>
    </div>
    <script>
        function bookProperty(propertyId, pricePerNight) {
            const userName = document.getElementById('user_name').value;
            const userEmail = document.getElementById('user_email').value;
            const checkIn = document.getElementById('check_in').value;
            const checkOut = document.getElementById('check_out').value;

            if (!userName || !userEmail || !checkIn || !checkOut) {
                alert('Please fill in all fields.');
                return;
            }

            const checkInDate = new Date(checkIn);
            const checkOutDate = new Date(checkOut);
            const nights = (checkOutDate - checkInDate) / (1000 * 60 * 60 * 24);
            if (nights <= 0) {
                alert('Check-out date must be after check-in date.');
                return;
            }

            const totalPrice = nights * pricePerNight;

            fetch('book.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `property_id=${propertyId}&user_name=${encodeURIComponent(userName)}&user_email=${encodeURIComponent(userEmail)}&check_in=${checkIn}&check_out=${checkOut}&total_price=${totalPrice}`
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                window.location.href = 'confirmation.php';
            })
            .catch(error => alert('Error: ' + error));
        }
    </script>
</body>
</html>
