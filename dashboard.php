<?php
// Include database connection
include('db_connection.php');  // Assuming this is your connection file

// Initialize variables for filters
$starttime_filter = '';
$endtime_filter = '';
$road_surface_filter = '';
$weather_temperature_filter = '';
$search_keyword = '';

// Check if filters are set
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['starttime'])) {
        $starttime_filter = $_GET['starttime'];
    }
    if (isset($_GET['endtime'])) {
        $endtime_filter = $_GET['endtime'];
    }
    if (isset($_GET['road_surface'])) {
        $road_surface_filter = $_GET['road_surface'];
    }
    if (isset($_GET['weather_temperature'])) {
        $weather_temperature_filter = $_GET['weather_temperature'];
    }
    if (isset($_GET['search'])) {
        $search_keyword = $_GET['search'];
    }
}

// SQL query with dynamic filters
$sql = "SELECT 
            de.experience_id,
            de.starttime,
            de.endtime,
            de.km,
            r.surface AS road_surface,
            r.width AS road_width,
            t.speed AS traffic_speed,
            t.density AS traffic_density,
            w.temperature AS weather_temperature,
            w.visibility AS weather_visibility,
            m.turning AS maneuver_turning,
            m.parking AS maneuver_parking
            
        FROM 
            driving_experience de
        JOIN 
            road r ON de.road_id = r.road_id
        JOIN 
            traffic t ON de.traffic_id = t.traffic_id
        JOIN 
        weather w ON de.weather_id = w.weather_id
        LEFT JOIN 
            includes i ON de.experience_id = i.experience_id
        LEFT JOIN 
            maneuver m ON i.maneuver_id = m.maneuver_id
        WHERE 
            (de.starttime LIKE '%$starttime_filter%' OR '$starttime_filter' = '')
            AND (de.endtime LIKE '%$endtime_filter%' OR '$endtime_filter' = '')
            AND (r.surface LIKE '%$road_surface_filter%' OR '$road_surface_filter' = '')
            AND (w.temperature LIKE '%$weather_temperature_filter%' OR '$weather_temperature_filter' = '')
            AND (de.experience_id LIKE '%$search_keyword%' OR de.starttime LIKE '%$search_keyword%' OR r.surface LIKE '%$search_keyword%' OR t.speed LIKE '%$search_keyword%' OR w.temperature LIKE '%$search_keyword%' OR m.turning LIKE '%$search_keyword%' OR m.parking LIKE '%$search_keyword%')
        GROUP BY 
            de.experience_id";

// Execute query
$result = $conn->query($sql);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driving Experiences Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
    <style>
        body {
            background-image: url('porsche-911-gt3-rs-8-bit-2l-1920x1080.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
            overflow-x: hidden;
            margin: 0;
            color: #ECECEC;
            font-family: Arial, sans-serif;
        }

        h1 {
            font-family: Georgia, 'Times New Roman', Times, serif;
            color: #D1C3E6;
            margin: 20px 0;
            font-size: 32px;
        }

        a {
            color: #D1C3E6;
            text-decoration: none;
            font-size: 16px;
        }

        a:hover {
            color: #EADAFB;
        }

        /* Filter Form */
        form {
            max-width: 600px;
            width: 100%;
            margin: 20px;
            padding: 20px;
            border: 1px solid #5B5E73;
            border-radius: 8px;
            background: rgba(39, 40, 52, 0.8);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        label {
            color: #AFA3C1;
            font-weight: bold;
            margin-bottom: 5px;
            display: inline-block;
        }

        input,
        select {
            font-style: italic;
            color: #2C2E3A;
            background: #D8D6F0;
            margin-bottom: 15px;
            padding: 8px;
            width: 100%;
            border: 1px solid #6B658F;
            border-radius: 5px;
            box-sizing: border-box;
            transition: border-color 0.3s ease, background-color 0.3s ease;
        }

        input::placeholder {
            color: #8C87A6;
            font-style: normal;
        }

        input:focus,
        select:focus {
            border-color: #C785F7;
            background-color: #F0ECFF;
            color: #2C2E3A;
            outline: none;
            box-shadow: 0 0 8px rgba(199, 133, 247, 0.7);
        }

        button {
            background-color: #8E66AF;
            color: #FFF;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            margin: 10px auto;
        }

        button:hover {
            background-color: #C785F7;
        }

        /* Table Styling */
        table {
            width: auto;
            margin: 20px 0;
            border-collapse: collapse;
            background: rgba(39, 40, 52, 0.9);
            color: #ECECEC;
            font-size: 14px;
            border: 1px solid #5B5E73;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            min-width: 1000px; 
        }
        .table-container {
    width: 100%;
    max-width: 90%; /* Adjust this to limit the table's horizontal size */
    max-height: 500px; /* Limit vertical height for better visibility */
    overflow-x: auto; /* Horizontal scrolling */
    overflow-y: auto; /* Vertical scrolling */
    margin: 20px auto; /* Center the container */
    border: 1px solid #5B5E73;
    background: rgba(39, 40, 52, 0.9);
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}
        table thead {
            background-color: #6B658F;
            color: #FFF;
        }

        table th,
        table td {
            padding: 10px;
            border: 1px solid #5B5E73;
            text-align: center;
        }

        table tbody tr:nth-child(even) {
            background-color: #2C2E3A;
        }

        table tbody tr:hover {
            background-color: #6B658F;
            color: #FFF;
        }

        footer {
            margin-top: 20px;
            text-align: center;
            padding: 10px;
            background-color: #2C2E3A;
            color: #AFA3C1;
            width: 100%;
        }
    </style>
</head>
</head>
<body>
    <h1>Driving Experiences Dashboard</h1>
    <a href="index.php">Back to Home</a>
    
    <!-- Filter Form -->
     <!-- Table Styling and Data -->
    
   

 <form method="GET" action="">
        <label for="starttime">Start Time:</label>
        <input type="text" name="starttime" id="starttime" value="<?php echo $starttime_filter; ?>" placeholder="e.g. 2024-12-05 08:00:00">
        
        <label for="endtime">End Time:</label>
        <input type="text" name="endtime" id="endtime" value="<?php echo $endtime_filter; ?>" placeholder="e.g. 2024-12-05 10:00:00">
        
        <label for="road_surface">Road Surface:</label>
        <input type="text" name="road_surface" id="road_surface" value="<?php echo $road_surface_filter; ?>" placeholder="e.g. Asphalt">
        
        <label for="weather_temperature">Weather Temperature:</label>
        <input type="text" name="weather_temperature" id="weather_temperature" value="<?php echo $weather_temperature_filter; ?>" placeholder="e.g. 25">
        
        <label for="search">Search:</label>
        <input type="text" name="search" id="search" value="<?php echo $search_keyword; ?>" placeholder="Search by ID, Road, Weather...">
        
        <button type="submit">Filter</button>
    </form>
    <div class="table-container">
    <table border="1">
        <thead>
            <tr>
                <th>Experience ID</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>KM</th>
                <th>Road Surface</th>
                <th>Road Width (m)</th>
                <th>Traffic Speed (km/h)</th>
                <th>Traffic Density (cars/unit)</th>
                <th>Weather Temperature (°C)</th>
                <th>Weather Visibility (m)</th>
                <th>Maneuver Parking</th>
                <th>Maneuver Turning</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['experience_id']; ?></td>
                <td><?php echo $row['starttime']; ?></td>
                <td><?php echo $row['endtime']; ?></td>
                <td><?php echo $row['km']; ?></td>
                <td><?php echo $row['road_surface']; ?></td>
                <td><?php echo $row['road_width']; ?></td>
                <td><?php echo $row['traffic_speed']; ?></td>
                <td><?php echo $row['traffic_density']; ?></td>
                <td><?php echo $row['weather_temperature']; ?></td>
                <td><?php echo $row['weather_visibility']; ?></td>
                <td><?php echo $row['maneuver_parking']; ?></td>
                <td><?php echo $row['maneuver_turning']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </div>
  



</body>
</html>
         
<?php
// Close the connection
include ('statistics.php');
include('graph.php');

$conn->close();
?>