<?php
session_start();

// Enable error display for debugging (comment out in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'php_errorlog');

// Attempt to include db.php
try {
    require 'db.php';
} catch (Exception $e) {
    error_log("Signup error: Failed to include db.php: " . $e->getMessage());
    echo "<div style='color: red; text-align: center; font-family: Arial, sans-serif; padding: 20px;'>Error: Failed to connect to database. " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_var(trim($_POST['name'] ?? ''), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    // Validate inputs
    if (empty($name) || empty($email) || empty($password)) {
        error_log("Signup error: Missing required fields (name, email, or password).");
        echo "<script>alert('All fields are required.');</script>";
        echo "<div style='color: red; text-align: center; font-family: Arial, sans-serif; padding: 10px;'>Error: All fields are required.</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("Signup error: Invalid email format for $email.");
        echo "<script>alert('Invalid email format.');</script>";
        echo "<div style='color: red; text-align: center; font-family: Arial, sans-serif; padding: 10px;'>Error: Invalid email format.</div>";
    } elseif (strlen($password) < 6) {
        error_log("Signup error: Password too short for $email.");
        echo "<script>alert('Password must be at least 6 characters.');</script>";
        echo "<div style='color: red; text-align: center; font-family: Arial, sans-serif; padding: 10px;'>Error: Password must be at least 6 characters.</div>";
    } else {
        try {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            if (!$stmt) {
                $errorInfo = $pdo->errorInfo();
                error_log("Signup error: Failed to prepare SELECT query: " . implode(", ", $errorInfo));
                echo "<div style='color: red; text-align: center; font-family: Arial, sans-serif; padding: 10px;'>Error: Failed to prepare query. " . htmlspecialchars(implode(", ", $errorInfo)) . "</div>";
                exit;
            }
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                error_log("Signup error: Email already registered: $email.");
                echo "<script>alert('Email already registered.');</script>";
                echo "<div style='color: red; text-align: center; font-family: Arial, sans-serif; padding: 10px;'>Error: Email already registered.</div>";
            } else {
                // Insert new user
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                if (!$password_hash) {
                    error_log("Signup error: Password hashing failed for $email.");
                    echo "<div style='color: red; text-align: center; font-family: Arial, sans-serif; padding: 10px;'>Error: Unable to process password.</div>";
                    exit;
                }
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                if (!$stmt) {
                    $errorInfo = $pdo->errorInfo();
                    error_log("Signup error: Failed to prepare INSERT query: " . implode(", ", $errorInfo));
                    echo "<div style='color: red; text-align: center; font-family: Arial, sans-serif; padding: 10px;'>Error: Failed to prepare query. " . htmlspecialchars(implode(", ", $errorInfo)) . "</div>";
                    exit;
                }
                $stmt->execute([$name, $email, $password_hash]);
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_name'] = $name;
                error_log("Signup successful for $email.");
                echo "<script>alert('Registration successful!'); window.location.href='home.php';</script>";
            }
        } catch (PDOException $e) {
            error_log("Signup error: Database error for $email: " . $e->getMessage());
            echo "<div style='color: red; text-align: center; font-family: Arial, sans-serif; padding: 10px;'>Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
        } catch (Exception $e) {
            error_log("Signup error: General error for $email: " . $e->getMessage());
            echo "<div style='color: red; text-align: center; font-family: Arial, sans-serif; padding: 10px;'>Server error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Airbnb Clone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background: linear-gradient(135deg, #ff5e62, #f7b733);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .auth-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            transform: scale(1);
            transition: transform 0.3s ease;
        }
        .auth-container:hover {
            transform: scale(1.02);
        }
        .auth-container h1 {
            font-size: 2em;
            color: #ff5e62;
            text-align: center;
            margin-bottom: 20px;
        }
        .auth-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            transition: border-color 0.3s;
        }
        .auth-container input:focus {
            border-color: #ff5e62;
            outline: none;
        }
        .auth-container button {
            width: 100%;
            padding: 12px;
            background: #ff5e62;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            transition: background 0.3s;
        }
        .auth-container button:hover {
            background: #e04e52;
        }
        .auth-container .toggle {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        .auth-container .toggle a {
            color: #ff5e62;
            text-decoration: none;
            font-weight: bold;
        }
        .auth-container .toggle a:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .auth-container {
                padding: 20px;
                max-width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1>Sign Up</h1>
        <form method="POST">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <input type="password" name="password" placeholder="Password (min 6 characters)" required>
            <button type="submit">Sign Up</button>
        </form>
        <div class="toggle">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>
</body>
</html>
