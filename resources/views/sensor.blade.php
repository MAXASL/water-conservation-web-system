<?php

$hostname = "localhost";
$username = "root";
$password = "";
$database = "water";

$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $flow_rate = isset($_POST['flow_rate']) ? floatval($_POST['flow_rate']) : 0;
    $total_used = isset($_POST['total_used']) ? floatval($_POST['total_used']) : 0;
    $area = isset($_POST['area']) ? $_POST['area'] : 'kitchen';

    if ($flow_rate == 0 && $total_used == 0) {
        echo "No flow detected. Skipping insert.";
    } else {
        // Get the latest usage value to calculate the new usage
        $lastUsageQuery = "SELECT usage FROM sensor WHERE user_id = $user_id ORDER BY id DESC LIMIT 1";
        $lastUsageResult = mysqli_query($conn, $lastUsageQuery);
        $lastUsage = 0;

        if ($lastUsageResult && mysqli_num_rows($lastUsageResult) > 0) {
            $row = mysqli_fetch_assoc($lastUsageResult);
            $lastUsage = floatval($row['usage']);
        }

        $newUsage = $lastUsage + $total_used;
        $today = date("Y-m-d");
        $now = date("Y-m-d H:i:s");

        $sql = "INSERT INTO sensor (user_id, flow_rate, total_used, usage, area, date, created_at, updated_at)
                VALUES ($user_id, $flow_rate, $total_used, $newUsage, '$area', '$today', '$now', '$now')";

        if (mysqli_query($conn, $sql)) {
            echo "Inserted | Flow Rate: $flow_rate | Total Used: $total_used | Usage: $newUsage";
        } else {
            echo "SQL Error: " . mysqli_error($conn);
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM sensor ORDER BY id DESC LIMIT 10";
    $result = mysqli_query($conn, $sql);
    $data = [];

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode(["error" => "No data found"]);
    }
}

mysqli_close($conn);
?>
