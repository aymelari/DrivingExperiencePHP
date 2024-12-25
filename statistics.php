<?php
// Include the database connection
include('db_connection.php');  // Assuming this is your connection file

// Define the filters (if any) you want to apply. You can replace these with actual data from a form, for example.
$starttime_filter = ''; // e.g., a specific date filter
$endtime_filter = ''; // e.g., a specific end date filter
$road_surface_filter = ''; // e.g., 'asphalt'
$weather_temperature_filter = ''; // e.g., 25 for 25°C

// SQL query to get statistics
$statistics_sql = "SELECT 
                        SUM(de.km) AS total_km,
                        AVG(t.speed) AS avg_speed,
                        AVG(w.temperature) AS avg_temp
                    FROM 
                        driving_experience de
                    JOIN 
                        traffic t ON de.traffic_id = t.traffic_id
                    JOIN 
                        weather w ON de.weather_id = w.weather_id
                    JOIN
                        road r ON de.road_id = r.road_id
                    WHERE 
                        (de.starttime LIKE '%$starttime_filter%' OR '$starttime_filter' = '')
                        AND (de.endtime LIKE '%$endtime_filter%' OR '$endtime_filter' = '')
                        AND (r.surface LIKE '%$road_surface_filter%' OR '$road_surface_filter' = '')
                        AND (w.temperature LIKE '%$weather_temperature_filter%' OR '$weather_temperature_filter' = '')";

// Execute the query
$statistics_result = $conn->query($statistics_sql);

// Fetch the statistics result
if ($statistics_result && $statistics_result->num_rows > 0) {
    $stats = $statistics_result->fetch_assoc();
    $total_km = $stats['total_km'];
    $avg_speed = round($stats['avg_speed'], 2); // rounding to 2 decimal places
    $avg_temp = round($stats['avg_temp'], 2); // rounding to 2 decimal places
} else {
    $total_km = 0;
    $avg_speed = 0;
    $avg_temp = 0;
}

// Close the connection
$conn->close();?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driving Experience Statistics</title>
    <style>
      body {
    font-family: Georgia, 'Times New Roman', Times, serif;
    color: #FFF; /* White text for readability */
    background-color: #2C2E3A; /* Dark background */
    margin: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
}

/* Heading Style */
h1 {
    font-family: Georgia, 'Times New Roman', Times, serif;
    color: #FFF; /* White text for readability */
    margin: 20px 0;
    font-size: 36px;
    text-align: center;
    background: rgba(0, 0, 0, 0.5); /* Semi-transparent black background */
    padding: 10px 20px;
    border-radius: 8px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7); /* Subtle text shadow */
}

/* Container for Statistics */
.stats-container {
    background: rgba(0, 0, 0, 0.6); /* Semi-transparent background */
    border-radius: 8px;
    padding: 20px;
    margin: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); /* Soft shadow for the container */
    width: 80%; /* Responsive width */
    max-width: 600px; /* Limit max width */
}

/* Title Inside Stats Container */
.stats-container h2 {
    color: #C785F7; /* Light Purple for Titles */
    font-size: 28px;
    margin-bottom: 15px;
}

/* Paragraphs Inside Stats Container */
.stats-container p {
    font-size: 18px;
    margin: 8px 0;
}

/* Link Styling */
a {
    color: #C785F7; /* Light Purple Link */
    text-decoration: none;
    font-size: 16px;
    padding: 8px 15px;
    border-radius: 5px;
    margin-top: 20px;
    display: inline-block;
}

a:hover {
    color: #EADAFB; /* Light Purple on hover */
    background-color: rgba(39, 40, 52, 0.8); /* Darker background on hover */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4); /* Subtle hover shadow */
}

/* Add some spacing between elements */
p, h2 {
    margin-bottom: 10px;
}

    </style>
</head>
<body>

    <h1>Driving Experience Statistics</h1>
    
    <div class="stats-container">
        <h2>Statistics</h2>
        <p>Total Distance Traveled: <?php echo $total_km; ?> km</p>
        <p>Average Traffic Speed: <?php echo $avg_speed; ?> km/h</p>
        <p>Average Weather Temperature: <?php echo $avg_temp; ?> °C</p>
    </div>

    

</body>
</html>
