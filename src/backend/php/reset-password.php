<?php
session_start();
include 'connection.php';
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_SESSION['reset_email'] ?? '';
    $otp = isset($_POST['otp']) ? trim($_POST['otp']) : '';
    $newPassword = isset($_POST['newpass']) ? $_POST['newpass'] : '';
    $confirmPassword = isset($_POST['confirmNewPass']) ? $_POST['confirmNewPass'] : '';

    if (empty($email) || empty($otp) || empty($newPassword) || empty($confirmPassword)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'All fields are required!']);
        exit;
    }

    if (strlen($newPassword) < 8) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters long!']);
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match!']);
        exit;
    }


    // Verify OTP
    $checkOtp = $conn->prepare("SELECT * FROM password_resets WHERE email = ? AND otp = ? AND expires_at >= NOW() LIMIT 1");
    $checkOtp->bind_param("ss", $email, $otp);
    $checkOtp->execute();
    $otpResult = $checkOtp->get_result();

    if ($otpResult->num_rows === 0) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Invalid or expired OTP.']);
        exit;
    }

    // OTP is valid - now update password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $updatePassword = $conn->prepare("UPDATE students SET password = ? WHERE email = ?");
    $updatePassword->bind_param("ss", $hashedPassword, $email);

    if ($updatePassword->execute()) {
        // Delete OTP to prevent reuse
        $deleteOtp = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
        $deleteOtp->bind_param("s", $email);
        $deleteOtp->execute();
        $deleteOtp->close();

        unset($_SESSION['reset_email']);

        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Password reset successful. You can now log in.']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to reset password. Please try again.']);
    }

    $checkOtp->close();
    $updatePassword->close();
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

$conn->close();