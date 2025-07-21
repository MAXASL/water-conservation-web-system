<?php
// sensor.php - place in /public/ folder

// Database config
$hostname = "localhost";
$username = "root";
$password = "";
$database = "water";

$conn = new mysqli($hostname, $username, $password, $database);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Helper to get JSON response header
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required params
    if (!isset($_POST['user_id'], $_POST['flow_rate'], $_POST['total_used'], $_POST['area'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing parameters. Required: user_id, flow_rate, total_used, area']);
        exit;
    }

    $user_id = intval($_POST['user_id']);
    $flow_rate = floatval($_POST['flow_rate']);
    $total_used = floatval($_POST['total_used']);
    $area = $conn->real_escape_string($_POST['area']);
    $now = date('Y-m-d H:i:s');
    $date = date('Y-m-d');

    if ($flow_rate == 0 && $total_used == 0) {
        echo json_encode(['status' => 'no_flow', 'message' => 'No flow detected, skipping insert.']);
        exit;
    }

    // Get the latest usage for this user and area to accumulate usage
    $sqlLatest = "SELECT `usage` FROM sensor WHERE user_id = $user_id AND area = '$area' ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sqlLatest);

    $prevUsage = 0.0;
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $prevUsage = floatval($row['usage']);
    }

    // Add the new total_used to the previous usage to get cumulative usage
    $newUsage = $prevUsage + $total_used;

    // Insert the new record with updated usage (usage column wrapped in backticks)
    $stmt = $conn->prepare("INSERT INTO sensor (user_id, area, `usage`, flow_rate, total_used, date, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('isdddsss', $user_id, $area, $newUsage, $flow_rate, $total_used, $date, $now, $now);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Record inserted',
            'data' => [
                'user_id' => $user_id,
                'area' => $area,
                'usage' => $newUsage,
                'flow_rate' => $flow_rate,
                'total_used' => $total_used,
                'date' => $date,
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Insert failed: ' . $stmt->error]);
    }

    $stmt->close();

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Return the last 10 records for all users and areas (usage column wrapped in backticks)
    $sql = "SELECT id, user_id, area, `usage`, flow_rate, total_used, date, created_at, updated_at FROM sensor ORDER BY id DESC LIMIT 10";
    $result = $conn->query($sql);

    $data = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode(['message' => 'No data found']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

$conn->close();
