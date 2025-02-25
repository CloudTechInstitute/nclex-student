<?php
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT * FROM employees ORDER BY ID DESC";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $employees = [];
        while ($row = $result->fetch_assoc()) {
            // Convert date format from Y-m-d to d-m-y
            $row['date'] = date("d-m-Y", strtotime($row['date']));
            $employees[] = $row;
        }
        echo json_encode(['status' => 'success', 'data' => $employees]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No employees found']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>