<?php
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $EmployeeName = isset($_POST['EmployeeName']) ? trim($_POST['EmployeeName']) : '';
    $EmployeeEmail = isset($_POST['EmployeeEmail']) ? trim($_POST['EmployeeEmail']) : '';
    $EmployeeRole = isset($_POST['EmployeeRole']) ? trim($_POST['EmployeeRole']) : '';
    $EmployeePhone = isset($_POST['EmployeePhone']) ? trim($_POST['EmployeePhone']) : '';
    $EmployeeAddress = isset($_POST['EmployeeAddress']) ? trim($_POST['EmployeeAddress']) : '';
    $EmployeeID = isset($_POST['EmployeeID']) ? trim($_POST['EmployeeID']) : '';
    $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+'), 0, 12);
    // $encryptedPassword = md5($password);
    $status = 'inactive';
    $date = date('Y-m-d');

    //Validate required fields
    if (empty($EmployeeName) || empty($EmployeeEmail) || empty($EmployeeRole) || empty($EmployeePhone) || empty($EmployeeAddress) || empty($EmployeeID)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO employees (`name`, `email`, `role`, `phone`, `address`, `IDnumber`, `password`, `status`, `date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $EmployeeName, $EmployeeEmail, $EmployeeRole, $EmployeePhone, $EmployeeAddress, $EmployeeID, $password, $status, $date);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Account added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Could not add Account, something went wrong', 'error' => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>