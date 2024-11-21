<?php
header("Content-Type: application/json");
include '../config/db.php';

// Define the valid API key (store it securely)
$validApiKey = '4ea9d17a5e53b5ae54f53484';  // Replace with the actual API key from tools

// Get the API key from the request headers
$headers = getallheaders();
$apiKey = isset($headers['API-Key']) ? $headers['API-Key'] : '';

// Check if the API key is valid
if ($apiKey !== $validApiKey) {
    echo json_encode(['error' => 'Invalid API Key']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (strpos($path, '/create') !== false && $method == 'POST') {
    // Handle creating a new RFID log (entry)
    $data = json_decode(file_get_contents('php://input'), true);
    $rfid_id = $data['rfid_id'];
    $name = $data['name'];

    $query = "INSERT INTO rfid_logs (rfid_id, name, created_at) 
              VALUES ('$rfid_id', '$name', CURRENT_TIMESTAMP)";
    if ($conn->query($query) === TRUE) {
        echo json_encode(['message' => 'RFID log added successfully']);
    } else {
        echo json_encode(['error' => 'Failed to add RFID log']);
    }
} elseif (strpos($path, '/update') !== false && $method == 'PUT') {
    // Handle updating an RFID log based on rfid_id (exit)
    $data = json_decode(file_get_contents('php://input'), true);
    $rfid_id = $data['rfid_id'];

    $query = "UPDATE rfid_logs SET updated_at = CURRENT_TIMESTAMP WHERE rfid_id = '$rfid_id'";
    if ($conn->query($query) === TRUE) {
        echo json_encode(['message' => 'RFID log updated successfully']);
    } else {
        echo json_encode(['error' => 'Failed to update RFID log']);
    }
} elseif (strpos($path, '/delete') !== false && $method == 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = intval($data['id']);

    $query = "DELETE FROM rfid_logs WHERE id = $id";
    if ($conn->query($query) === TRUE) {
        echo json_encode(['message' => 'RFID log deleted successfully']);
    } else {
        echo json_encode(['error' => 'Failed to delete RFID log']);
    }
} elseif ($method == 'GET') {
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $query = "SELECT *, 
                  DATE_FORMAT(created_at, '%r') AS entry_time, 
                  DATE_FORMAT(updated_at, '%r') AS exit_time 
                  FROM rfid_logs WHERE id = $id";
    } elseif (isset($_GET['rfid_id'])) {
        $id = intval($_GET['rfid_id']);
        $query = "SELECT *, 
                  DATE_FORMAT(created_at, '%r') AS entry_time, 
                  DATE_FORMAT(updated_at, '%r') AS exit_time 
                  FROM rfid_logs WHERE id = $id";
    } else {
        $query = "SELECT *, 
                  DATE_FORMAT(created_at, '%r') AS entry_time, 
                  DATE_FORMAT(updated_at, '%r') AS exit_time 
                  FROM rfid_logs ORDER BY created_at DESC";
    }
    $result = $conn->query($query);

    $logs = [];
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
    echo json_encode($logs);
} else {
    echo json_encode(['error' => 'Invalid Request Method']);
}

$conn->close();
