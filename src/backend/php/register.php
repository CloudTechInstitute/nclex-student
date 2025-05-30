<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../../../vendor/autoload.php'; // Adjust path as needed
use Ramsey\Uuid\Uuid;

include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $othername = trim($_POST['othername'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $password = $_POST['password'] ?? '';
    $cpassword = $_POST['cpassword'] ?? '';
    $status = 'inactive';
    $studentUUID = Uuid::uuid4()->toString();
    $date = date('Y-m-d');

    // Validate required fields
    if (
        empty($fname) || empty($lname) || empty($email) || empty($phone) || empty($country) || empty($password) || empty($cpassword)
    ) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    // Password confirmation
    if ($password !== $cpassword) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Generate verification token
    $verificationToken = bin2hex(random_bytes(32));
    $verificationLink = "http://localhost/nclex-student/src/verify.php?token=$verificationToken&uuid=$studentUUID";

    // Check if email already exists
    $stmt = $conn->prepare("SELECT uuid FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        http_response_code(409); // Conflict
        echo json_encode(['status' => 'error', 'message' => 'Email already registered']);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    // Send verification email
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
        $mail->addAddress($email, $fname . " " . $lname);

        $mail->isHTML(true);
        $mail->Subject = 'Email Verification';
        $mail->Body = "
            <h2>Email Verification</h2>
            <p>Hello $fname,</p>
            <p>Thank you for registering. Please click the link below to verify your email:</p>
            <a href='$verificationLink' 
            style='
                display: inline-block;
                padding: 10px 20px;
                font-size: 16px;
                color: #fff;
                background-color: #2563eb;
                text-decoration: none;
                border-radius: 8px;
                font-weight: 500;
                text-align: center;
                '
            >
            Click to verify
            </a>
            <p>If you did not register, please ignore this email.</p>
        ";

        if ($mail->send()) {
            // Insert user into database with verification token
            $stmt = $conn->prepare("INSERT INTO students (`uuid`, `firstname`, `lastname`, `othernames`, `phone`, `email`, `country`, `password`, `date`, `status`, `verification_token`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssss", $studentUUID, $fname, $lname, $othername, $phone, $email, $country, $hashedPassword, $date, $status, $verificationToken);

            if ($stmt->execute()) {
                http_response_code(201); // Created
                echo json_encode(['status' => 'success', 'message' => 'Account created. Please check your email to verify.']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Could not create account', 'error' => $stmt->error]);
            }

            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Email could not be sent', 'error' => $mail->ErrorInfo]);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Email sending failed', 'error' => $mail->ErrorInfo]);
    }

    $conn->close();

} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>