<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../../../vendor/autoload.php';

include 'connection.php';
session_start(); // Start session at the top
header("Content-Type: application/json");

$response = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if (empty($email)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Email is required!']);
        exit;
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT uuid, firstname, lastname FROM students WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $fname = $user['firstname'];
        $lname = $user['lastname'];

        // Generate OTP
        $otp = random_int(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        // Store OTP
        $storeOtp = $conn->prepare("REPLACE INTO password_resets (email, otp, expires_at) VALUES (?, ?, ?)");
        $storeOtp->bind_param("sss", $email, $otp, $expiry);
        $storeOtp->execute();
        $storeOtp->close();

        // Store email in session
        $_SESSION['reset_email'] = $email;

        // Send OTP via email
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'globalnclexamscenter.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'info@globalnclexamscenter.com';
            $mail->Password = 'Globalnclex@exams';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('info@globalnclexamscenter.com', 'Global NCLEX Exams Center');
            $mail->addAddress($email, "$fname $lname");

            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
            $mail->Body = "
                <h2>Password Reset OTP</h2>
                <p>Hello $fname,</p>
                <p>Your One-Time Password (OTP) is:</p>
                <div style='
                    font-size: 24px;
                    font-weight: bold;
                    padding: 10px;
                    background: #f0f0f0;
                    border-radius: 8px;
                    display: inline-block;
                    color: #2563eb;'
                >$otp</div>
                <p>This code will expire in 10 minutes.</p>
                <p>If you didn't request this, please ignore this email.</p>
            ";

            $mail->send();

            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => 'OTP sent to your email address.'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to send email. Please try again later.',
            ]);
        }
    } else {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'No user found with this email address.'
        ]);
    }

    $stmt->close();
} else {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method.'
    ]);
}

$conn->close();