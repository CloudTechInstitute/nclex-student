<?php
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = isset($_POST['fname']) ? trim($_POST['fname']) : '';
    $lname = isset($_POST['lname']) ? trim($_POST['lname']) : '';
    $othername = isset($_POST['othername']) ? trim($_POST['othername']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
    $country = isset($_POST['country']) ? trim($_POST['country']) : '';
    $state = isset($_POST['state']) ? trim($_POST['state']) : '';
    $city = isset($_POST['city']) ? trim($_POST['city']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $cpassword = isset($_POST['cpassword']) ? $_POST['cpassword'] : '';
    $status = 'inactive';
    $date = date('Y-m-d');

    // Validate required fields
    if (empty($fname) || empty($lname) || empty($email) || empty($phone) || empty($address) || empty($gender) || empty($country) || empty($state) || empty($city) || empty($password) || empty($cpassword)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    // Check if passwords match
    if ($password !== $cpassword) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO students (`firstname`, `lastname`, `othernames`, `phone`, `email`, `address`, `gender`, `country`, `state`, `city`, `password`, `date`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssss", $fname, $lname, $othername, $phone, $email, $address, $gender, $country, $state, $city, $hashedPassword, $date, $status);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Account created successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Could not create account', 'error' => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>