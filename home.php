<?php
session_start();
require 'db.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : '';
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : '';
$price_min = isset($_GET['price_min']) ? (float)$_GET['price_min'] : 0;
$price_max = isset($_GET['price_max']) ? (float)$_GET['price_max'] : 1000;
$property_type = isset($_GET['property_type']) ? $_GET['property_type'] : '';

$query = "SELECT * FROM properties WHERE 1=1";
$params = [];
if ($search) {
    $query .= " AND (title LIKE :search OR location LIKE :search)";
    $params[':search'] = "%$search%";
}
if ($price_min) {
    $query .= " AND price >= :price_min";
    $params[':price_min'] = $price_min;
}
if ($price_max) {
    $query .= " AND price <= :price_max";
    $params[':price_max'] = $price_max;
}
if ($property_type) {
    $query .= " AND property_type = :property_type";
    $params[':property_type'] = $property_type;
}

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Home error: Database query failed: " . $e->getMessage());
    die("Error loading properties.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Airbnb Clone - Home</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background: linear-gradient(135deg, #ff5e62, #f7b733);
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            text-align: center;
            padding: 40px 0;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        header h1 {
            font-size: 2.5em;
            color: #ff5e62;
        }
        .nav-links {
            margin: 10px 0;
        }
        .nav-links a {
            margin: 0 10px;
            color: #ff5e62;
            text-decoration: none;
            font-size: 1.1em;
            transition: color 0.3s;
        }
        .nav-links a:hover {
            color: #e04e52;
        }
        .search-bar {
            display: flex;
            gap: 10px;
            margin: 20px 0;
            background: white;
            padding: 20px;
            border-radius: 50px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .search-bar input, .search-bar select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            flex: 1;
        }
        .search-bar button {
            padding: 10px 20px;
            background: #ff5e62;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .search-bar button:hover {
            background: #e04e52;
        }
        .filters {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }
        .filters input, .filters select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .properties {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .property-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .property-card:hover {
            transform: translateY(-5px);
        }
        .property-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .property-card h3 {
            font-size: 1.5em;
            margin: 10px;
        }
        .property-card p {
            margin: 10px;
            color: #666;
        }
        .property-card .price {
            font-size: 1.2em;
            color: #ff5e62;
            margin: 10px;
        }
        .property-card button {
            width: 100%;
            padding: 10px;
            background: #ff5e62;
            color: white;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }
        .property-card button:hover {
            background: #e04e52;
        }
        @media (max-width: 768px) {
            .search-bar {
                flex-direction: column;
            }
            .filters {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Airbnb Clone</h1>
            <div class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                    <a href="my_bookings.php">My Bookings</a>
                    <a href="#" onclick="logout()">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="signup.php">Sign Up</a>
                <?php endif; ?>
            </div>
            <form id="searchForm">
                <div class="search-bar">
                    <input type="text" name="search" placeholder="Destination" value="<?php echo htmlspecialchars($search); ?>">
                    <input type="date" name="check_in" value="<?php echo htmlspecialchars($check_in); ?>">
                    <input type="date" name="check_out" value="<?php echo htmlspecialchars($check_out); ?>">
                    <button type="submit">Search</button>
                </div>
                <div class="filters">
                    <input type="number" name="price_min" placeholder="Min Price" value="<?php echo htmlspecialchars($price_min); ?>">
                    <input type="number" name="price_max" placeholder="Max Price" value="<?php echo htmlspecialchars($price_max); ?>">
                    <select name="property_type">
                        <option value="">All Types</option>
                        <option value="Villa" <?php if ($property_type == 'Villa') echo 'selected'; ?>>Villa</option>
                        <option value="Apartment" <?php if ($property_type == 'Apartment') echo 'selected'; ?>>Apartment</option>
                        <option value="Cabin" <?php if ($property_type == 'Cabin') echo 'selected'; ?>>Cabin</option>
                    </select>
                </div>
            </form>
        </header>
        <div class="properties">
            <?php if (empty($properties)): ?>
                <p>No properties found.</p>
            <?php else: ?>
                <?php foreach ($properties as $property): ?>
                    <div class="property-card">
                        <img src="<?php echo htmlspecialchars($property['image']); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>">
                        <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                        <p><?php echo htmlspecialchars($property['location']); ?></p>
                        <p><?php echo htmlspecialchars($property['description']); ?></p>
                        <p class="price">$<?php echo number_format($property['price'], 2); ?>/night</p>
                        <p>Rating: <?php echo number_format($property['rating'], 1); ?> ★</p>
                        <button onclick="redirectToProperty(<?php echo $property['id']; ?>)">View Details</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <script>
        function redirectToProperty(id) {
            window.location.href = `property.php?id=${id}`;
        }
        function logout() {
            fetch('logout.php', { method: 'POST' })
                .then(() => window.location.href = 'home.php');
        }
    </script>
</body>
</html>
