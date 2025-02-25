<?php
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $status = 'inactive';
    $date = date('Y-m-d');

    // Validate required fields
    if (empty($role) || empty($description)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO roles (`role`, `description`, `status`, `date`) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $role, $description, $status, $date);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Role added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Could not add role, something went wrong', 'error' => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>