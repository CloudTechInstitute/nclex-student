<?php
session_start(); // Start the session
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['studentID']) && !empty($_SESSION['studentID']) && isset($_SESSION['LoggedStudent']) && !empty($_SESSION['LoggedStudent'])) {
        $userID = $conn->real_escape_string($_SESSION['studentID']); // Sanitize input
        $username = $conn->real_escape_string($_SESSION['LoggedStudent']); // Sanitize input

        // First, fetch the student's email using the UUID
        $query = "SELECT email FROM students WHERE uuid = '$userID' LIMIT 1";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $email = $row['email']; // Extract email

            // Fetch subscriptions using email and username
            $query = "SELECT * FROM subscriptions WHERE email = '$email' AND subscriber = '$username' ORDER BY ID DESC";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                $subscriptions = [];
                $today = date("Y-m-d"); // Get today's date

                while ($row = $result->fetch_assoc()) {
                    // Convert expiry date to proper format
                    $expiryDate = date("Y-m-d", strtotime($row['expiry_date']));
                    $row['date_subscribed'] = date("d-m-Y", strtotime($row['date_subscribed']));
                    $row['expiry_date'] = date("d-m-Y", strtotime($row['expiry_date']));

                    // Calculate days left
                    $expiryTimestamp = strtotime($expiryDate);
                    $todayTimestamp = strtotime($today);
                    $daysLeft = ceil(($expiryTimestamp - $todayTimestamp) / (60 * 60 * 24));

                    // Prevent backdated expiry from extending validity
                    if ($daysLeft < 0) {
                        $row['status'] = "Expired";
                    } elseif ($daysLeft === 0) {
                        $row['status'] = "Expires Today";
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