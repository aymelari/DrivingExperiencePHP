<?php
// Database connection settings
include('db_connection.php');

// Initialize a message variable
$message = "";

// Process the form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the form data is set and avoid undefined index warnings
    $startTime = $_POST['starttime'] ?? '';
    $endTime = $_POST['endtime'] ?? '';
    $km = $_POST['km'] ?? '';
    $roadSurface = $_POST['roadsurface'] ?? ''; // Added road surface
    $roadWidth = $_POST['m'] ?? ''; // Added road width
    $trafficCondition = $_POST['traffic'] ?? ''; // Corrected traffic condition name
    $trafficSpeed = $_POST['km/h'] ?? ''; // Added traffic speed
    $weatherTemperature = $_POST['C'] ?? ''; // Added weather temperature
    $visibility = $_POST['weather_visibility'] ?? ''; // Added weather visibility
    $maneuverTurning = $_POST['maneuver_turning'] ?? ''; // Added turning type
    $maneuverParking = $_POST['maneuver_parking'] ?? ''; // Added parking type

    // 1. Insert into Traffic table
    $stmt = $conn->prepare("INSERT INTO traffic (density, speed) VALUES (?, ?)");
    $stmt->bind_param("si", $trafficCondition, $trafficSpeed); // Assuming trafficCondition is a string and trafficSpeed is an integer
    $stmt->execute();
    $traffic_id = $stmt->insert_id; // Get the auto-generated traffic_id
    $stmt->close();
   

    // 2. Insert into Weather table
    $stmt = $conn->prepare("INSERT INTO weather (temperature, visibility) VALUES (?, ?)");
    $stmt->bind_param("is", $weatherTemperature, $visibility);
    $stmt->execute();
    $weather_id = $stmt->insert_id; // Get the ID of the inserted Weather row
    $stmt->close();

    // 3. Insert into Road table
    $stmt = $conn->prepare("INSERT INTO road (surface, width) VALUES (?, ?)");
    $stmt->bind_param("si", $roadSurface, $roadWidth);
    $stmt->execute();
    $road_id = $stmt->insert_id; // Get the ID of the inserted Road row
    $stmt->close();

    // 4. Insert into Maneuver table
    $stmt = $conn->prepare("INSERT INTO maneuver (turning, parking) VALUES (?, ?)");
    $stmt->bind_param("ss", $maneuverTurning, $maneuverParking);
    $stmt->execute();
    $maneuver_id = $stmt->insert_id; // Get the ID of the inserted Maneuver row
    $stmt->close();

    // 5. Insert into Driving Experience table
    $stmt = $conn->prepare("INSERT INTO driving_experience (starttime, endtime, km, traffic_id, weather_id, road_id) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisii", $startTime, $endTime, $km, $traffic_id, $weather_id, $road_id);
    $stmt->execute();
    $experience_id = $stmt->insert_id; // Get the ID of the inserted Driving Experience row
    $stmt->close();

    // 6. Insert into includes table (many-to-many relationship)
    // The experience_id is from the driving_experience table and maneuver_id is from the Maneuver table
    $stmt = $conn->prepare("INSERT INTO includes (experience_id, maneuver_id) VALUES (?, ?)");
    $stmt->bind_param('ii', $experience_id, $maneuver_id);
    
    if ($stmt->execute()) {
        $message = "Record saved successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    // Close the statement for driving experience
    $stmt->close();
}

// Close the database connection
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driving Experience Form</title>
    <style>
    
    body {
      background-image: url('porsche-911-gt3-rs-8-bit-2l-1920x1080.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            /* height: 160vh; Adjusted to 100vh to fit within the viewport */
            margin: 0;
            color: #ECECEC;
            font-family: Arial, sans-serif;
    }
    a {
        font-family: Georgia, 'Times New Roman', Times, serif;
    color: #D1C3E6;
    text-decoration: none;
    font-size: 16px;
}
    header {
      text-align: center;
    }

    h1 {
        ont-family: Georgia, 'Times New Roman', Times, serif;
    color: #D1C3E6; /* Light pastel purple */
    margin: 20px 0;
    font-size: 32px;
      
    }

    form {
        max-width: 400px;
    width: 100%;
    margin: 20px;
    padding: 15px;
    border: 1px solid #5B5E73; /* Dark purple border */
    border-radius: 8px;
    background: rgba(39, 40, 52, 0.8); /* Dark purple/grey background with transparency */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    }

    label {
        color: #AFA3C1; /* Light pastel purple */
    font-weight: bold;
    margin-bottom: 5px;
    display: inline-block;
    }
    input,
select {
    font-style: italic;
    color: #2C2E3A; /* Darker text color for better contrast */
    background: #D8D6F0; /* Light purple-grey background */
    margin-bottom: 10px;
    padding: 8px;
    width: 100%;
    border: 1px solid #6B658F; /* Mid-purple border */
    border-radius: 5px;
    box-sizing: border-box;
    transition: border-color 0.3s ease, background-color 0.3s ease;
}

input::placeholder {
    color: #8C87A6; /* Lighter purple-grey for placeholder text */
    font-style: normal;
}

input:focus,
select:focus {
    border-color: #C785F7; /* Bright purple border on focus */
    background-color: #F0ECFF; /* Slightly brighter background on focus */
    color: #2C2E3A; /* Darker text color */
    outline: none;
    box-shadow: 0 0 8px rgba(199, 133, 247, 0.7); /* Light purple glow effect */
}

    footer {
      margin-top: 20px;
      text-align: center;
      padding: 10px;
      background-color: #2C2E3A;
      color: #AFA3C1; 
    }
    button {
        background-color: #8E66AF; /* Mid-purple */
    color: #FFF; /* White text */
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    display : block;
    margin : 0 auto;
    }
    button:hover {
    background-color: #C785F7; /* Light purple on hover */
}
a:hover {
    color: #EADAFB; /* Light pastel purple on hover */
}
  </style>
</head>
<body>
    <h1>Record Your Driving Experience</h1>
    <a href="index.php">Back to Home</a>

    <?php
    if (!empty($message)) {
        echo "<p>$message</p>";
    }
    ?>

    <form id="drivingForm" action="webform.php" method="POST">
        <label for="starttime">Start Time:</label><br>
        <input type="time" id="starttime" name="starttime" required><br>

        <label for="endtime">End Time:</label><br>
        <input type="time" id="endtime" name="endtime" required><br>

        <label for="km">Kilometers Driven:</label><br>
        <input type="number" id="km" name="km" required><br>
          
        
       
    <label for="roadsurface">Select road surface</label><br>

    <select id="roadsurface" name="roadsurface" required>
        <option value="Dry Asphalt">Dry Asphalt</option>
        <option value="Wet Asphalt">Wet Asphalt</option>
        <option value="Snow/Ice">Snow/Ice</option>
        <option value="Mud">Mud</option>
    </select>
    <br>
    <label for="m">Road width</label><br>
        <input type="number" id="m" name="m" required><br>
          
        
        <label for="traffic density">Select the traffic condition</label><br>

    <select id="traffic" name="traffic" required>
        <option value="light">Light</option>
        <option value="moderate">Moderate</option>
        <option value="heavy">Heavy</option>
    </select><br>


    <label for="km/h">Speed of Traffic </label><br>
 <input type="number" id="km/h" name="km/h" required><br>

        <!-- <label for="date">Date:</label><br>
        <input type="date" id="date" name="date" required><br><br> -->

        <label for="weather">Weather temperature :</label><br>
        <input type="number" id="C" name="C" required><br>


    <label for="weather_visibility">Select the visibility</label><br>

    <select id="weather_visibility" name="weather_visibility" required>
        <option value="low">Low</option>
        <option value="medium">Medium</option>
        <option value="high">High</option>
    </select>
    <br>
    <label for="maneuver_turning">Select the turning type</label><br>

    <select id="maneuver_turning" name="maneuver_turning" required>
        <option value="Left Turn1">Left Turn</option>
        <option value="Right Turn">Right Turn</option>
        <option value="U-Turn">U-Turn</option>
    </select><br>
<label for="maneuver_parking">Select the parking type</label>
    <select id="maneuver_parking" name="maneuver_parking" required>
        <option value="Parallel Parking">Parallel Parking</option>
        <option value="Angle Parking (Left)">Angle Parking (Left)</option>
        <option value="Angle Parking (Right)">Angle Parking (Right)</option>
    </select>


  <button type="submit">Record Driving Experience</button>
  </form>
  <footer>
    <p> created by AYSU &copy; 2023. All rights reserved.</p>
  </footer>
</body>
</html>