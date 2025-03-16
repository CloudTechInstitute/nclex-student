<?php

include 'connection.php'; // Include database connection

header("Content-Type: application/json");

$response = array();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get and sanitize input
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Fill in all required fields!!']);
        exit;
    }

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM students WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['LoggedStudent'] = $user['firstname'] . " " . $user["lastname"];
            unset($user['password']); // Remove password from response for security

            $response["status"] = "success";
            $response["message"] = "Login Successful";
            $response["user"] = $user; // Send user details
        } else {
            $response["status"] = "error";
            $response["message"] = "Invalid username or password. Please try again.";
        }
    } else {
        $response["status"] = "error";
        $response["message"] = "User not found. Please try again.";
    }

    $stmt->close();
} else {
    $response["status"] = "error";
    $response["message"] = "Request method not allowed.";
}

$conn->close();
echo json_encode($response);

?>