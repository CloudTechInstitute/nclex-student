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
    $address = trim($_POST['address'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $password = $_POST['password'] ?? '';
    $cpassword = $_POST['cpassword'] ?? '';
    $status = 'inactive';
    $studentUUID = Uuid::uuid4()->toString();
    $date = date('Y-m-d');

    // Validate required fields
    if (
        empty($fname) || empty($lname) || empty($email) || empty($phone) || empty($address) ||
        empty($gender) || empty($country) || empty($state) || empty($city) || empty($password) || empty($cpassword)
    ) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    // Password confirmation
    if ($password !== $cpassword) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Generate verification token
    $verificationToken = bin2hex(random_bytes(32));
    $verificationLink = "localhost/nclex-student/src/verify.php?token=$verificationToken&uuid=$studentUUID";

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
            <a href='$verificationLink'>$verificationLink</a>
            <p>If you did not register, please ignore this email.</p>
        ";

        if ($mail->send()) {
            // Insert user into database with verification token
            $stmt = $conn->prepare("INSERT INTO students (`uuid`, `firstname`, `lastname`, `othernames`, `phone`, `email`, `address`, `gender`, `country`, `state`, `city`, `password`, `date`, `status`, `verification_token`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssssssss", $studentUUID, $fname, $lname, $othername, $phone, $email, $address, $gender, $country, $state, $city, $hashedPassword, $date, $status, $verificationToken);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Account created. Please check your email to verify.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Could not create account', 'error' => $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Email could not be sent', 'error' => $mail->ErrorInfo]);
        }

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Email sending failed', 'error' => $mail->ErrorInfo]);
    }

    $conn->close();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>