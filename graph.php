<?php
// Include database connection
include('db_connection.php');  // Assuming this is your connection file

// SQL query for statistics data
$parking_sql = "SELECT parking, COUNT(*) AS count
FROM driving_experience de
LEFT JOIN includes i ON de.experience_id = i.experience_id
LEFT JOIN maneuver m ON i.maneuver_id = m.maneuver_id
GROUP BY parking;";

$parking_result = $conn->query($parking_sql);
$parking_data = [];

// Prepare the data for the pie chart
if ($parking_result && $parking_result->num_rows > 0) {
    while ($row = $parking_result->fetch_assoc()) {
        $parking_data[] = [
            'label' => $row['parking'] ?: 'No Parking',
            'count' => $row['count']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maneuver Parking Distribution</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background-color: #2C2E3A;
            color: #ECECEC;
            font-family: Arial, sans-serif;
        }

        canvas {
            max-width: 80%;
            height: 400px;
            margin: 20px auto;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        h1 {
            color: #C785F7;
            margin: 20px;
        }
    </style>
</head>
<body>

<h1>Maneuver Parking Distribution</h1>

<canvas id="parkingChart"></canvas>

<script>
    var parkingData = <?php echo json_encode($parking_data); ?>;

    var labels = parkingData.map(function(item) {
        return item.label;
    });

    var data = parkingData.map(function(item) {
        return item.count;
    });

    var ctx = document.getElementById('parkingChart').getContext('2d');
    var parkingChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#FF9F40', '#E7E9ED', '#C785F7'
                ],
                hoverBackgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#FF9F40', '#E7E9ED', '#C785F7'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw + ' experiences';
                        }
                    }
                }
            }
        }
    });
</script>

</body>
</html>
