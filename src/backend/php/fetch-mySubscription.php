<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../../../vendor/autoload.php';

session_start();
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_SESSION['studentID']) && !empty($_SESSION['LoggedStudent'])) {
        $userID = $conn->real_escape_string($_SESSION['studentID']);
        $username = $conn->real_escape_string($_SESSION['LoggedStudent']);

        $query = "SELECT email, firstname, lastname FROM students WHERE uuid = '$userID' LIMIT 1";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $email = $row['email'];
            $fname = $row['firstname'];
            $lname = $row['lastname'];

            $query = "SELECT * FROM subscriptions WHERE email = '$email' AND subscriber = '$username' ORDER BY ID DESC";
            $result = $conn->query($query);

            if ($result && $result->num_rows > 0) {
                $subscriptions = [];
                $today = date("Y-m-d");

                while ($row = $result->fetch_assoc()) {
                    $expiryDate = date("Y-m-d", strtotime($row['expiry_date']));
                    $row['date_subscribed'] = date("d-m-Y", strtotime($row['date_subscribed']));
                    $row['expiry_date'] = date("d-m-Y", strtotime($row['expiry_date']));

                    $expiryTimestamp = strtotime($expiryDate);
                    $todayTimestamp = strtotime($today);
                    $daysLeft = ceil(($expiryTimestamp - $todayTimestamp) / (60 * 60 * 24));

                    if ($daysLeft < 0) {
                        $row['status'] = "Expired";
                    } elseif ($daysLeft === 4) {
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
                            $mail->Subject = 'Subscription Expiry';
                            $mail->Body = "
                                <h2>Subscription Expiry Notice</h2>
                                <p>Hello $fname,</p>
                                <p>Your subscription with us has $daysLeft days left. We encourage you to renew before it expires.</p>
                                <p>If you did not have an active subscription with us, please ignore this email.</p>
                                <p>Kind Regards,<br>Global NCLEX Exams Center</p>
                            ";
                            $mail->send();
                        } catch (Exception $e) {
                            // Log error here instead of returning it
                        }
                        $row['status'] = "Expires in $daysLeft days";
                    } else {
                        $row['status'] = "$daysLeft days left";
                    }

                    $subscriptions[] = $row;
                }

                echo json_encode(['status' => 'success', 'data' => $subscriptions]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'You do not have any subscription yet']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Student not found']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not logged in or session expired']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>