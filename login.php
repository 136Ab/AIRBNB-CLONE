<?php
session_start();
require 'db.php';

// Enable error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errorlog');
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        error_log("Login error: Email or password missing.");
        echo "<script>alert('Email and password are required.');</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("Login error: Invalid email format for $email.");
        echo "<script>alert('Invalid email format.');</script>";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                error_log("Login successful for $email.");
                echo "<script>alert('Login successful!'); window.location.href='home.php';</script>";
            } else {
                error_log("Login error: Invalid credentials for $email.");
                echo "<script>alert('Invalid email or password.');</script>";
            }
        } catch (PDOException $e) {
            error_log("Login error: Database error for $email: " . $e->getMessage());
            echo "<script>alert('Error: " . htmlspecialchars($e->getMessage()) . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Airbnb Clone</title>
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
            cursor:
