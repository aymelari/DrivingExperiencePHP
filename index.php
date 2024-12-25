<?php
// Start session to track the user
session_start();

// Example: Set a sample username for the greeting
$_SESSION['username'] = "John Doe"; // Replace with actual user logic
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
   
    <style>
        body {
            background-image: url('porsche-911-gt3-rs-8-bit-2l-1920x1080.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            color: #ECECEC;
            font-family: Arial, sans-serif;
        }

        h1 {
            font-family: Georgia, 'Times New Roman', Times, serif;
    color: #FFF; /* White text for readability */
    margin: 20px 0;
    font-size: 36px;
    text-align: center;
    background: rgba(0, 0, 0, 0.5); /* Semi-transparent black background */
    padding: 10px 20px;
    border-radius: 8px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7); }

        nav {
            margin-top: 20px;
            background: rgba(39, 40, 52, 0.8); /* Semi-transparent background */
            border-radius: 8px;
            padding: 15px 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            gap: 15px;
        }

        nav ul li {
            display: inline;
        }

        nav a {
            color: #AFA3C1; /* Light pastel purple */
            text-decoration: none;
            font-size: 18px;
            padding: 8px 12px;
            border-radius: 5px;
            transition: color 0.3s ease, background-color 0.3s ease;
        }

        nav a:hover {
            background-color: #8E66AF; /* Mid-purple on hover */
            color: #FFF;
        }
    </style>
</head>
<body>
    <h1>Welcome to Driving Experience</h1>
    

    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="webform.php">Web Form</a></li>
        </ul>
    </nav>
</body>
</html>
