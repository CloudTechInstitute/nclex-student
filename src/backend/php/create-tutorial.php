<?php
session_start();
require_once __DIR__ . '/../../../vendor/autoload.php';
use Ramsey\Uuid\Uuid;

include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['studentID'])) {
        $userID = $_SESSION['studentID'];
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $schedule_date = isset($_POST['tutorialDate']) ? trim($_POST['tutorialDate']) : '';
        $schedule_time = isset($_POST['tutorialTime']) ? trim($_POST['tutorialTime']) : '';
        $tutStatus = isset($_POST['tutStatus']) ? trim($_POST['tutStatus']) : '';
        $topics = isset($_POST['topics']) ? trim($_POST['topics']) : '';
        $date = date('Y-m-d');
        $tutorialUUID = Uuid::uuid4()->toString();
        // Validate required fields
        if (empty($title) || empty($topics)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            exit;
        }

        // Prepare statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO tutorials (`uuid`, `user_id`, `title`, `topics`, `schedule_date`, `schedule_time`, `status`, `date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $tutorialUUID, $userID, $title, $topics, $schedule_date, $schedule_time, $tutStatus, $date);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Tutorial created successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Could not create tutorial, something went wrong', 'error' => $stmt->error]);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    }
}
?>