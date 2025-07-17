<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - Airbnb Clone</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .confirmation {
            background: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .confirmation h1 {
            font-size: 2em;
            color: #ff5e62;
            margin-bottom: 20px;
        }
        .confirmation p {
            font-size: 1.2em;
            color: #666;
            margin-bottom: 20px;
        }
        .confirmation button {
            padding: 10px 20px;
            background: #ff5e62;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .confirmation button:hover {
            background: #e04e52;
        }
    </style>
</head>
<body>
    <div class="confirmation">
        <h1>Booking Confirmed!</h1>
        <p>Thank you for your booking. You'll receive a confirmation email soon.</p>
        <button onclick="window.location.href='index.php'">Back to Home</button>
    </div>
</body>
</html>
