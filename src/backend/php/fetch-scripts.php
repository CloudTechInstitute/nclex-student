<?php
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch employee count
    $employeeQuery = "SELECT COUNT(*) AS employee_count FROM employees";
    $employeeResult = $conn->query($employeeQuery);

    // Fetch role count
    $roleQuery = "SELECT COUNT(*) AS role_count FROM roles";
    $roleResult = $conn->query($roleQuery);

    if ($employeeResult && $roleResult) {
        $employeeRow = $employeeResult->fetch_assoc();
        $roleRow = $roleResult->fetch_assoc();

        echo json_encode([
            'status' => 'success',
            'employee_count' => $employeeRow['employee_count'],
            'role_count' => $roleRow['role_count']
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch counts']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>