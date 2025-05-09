<?php
session_start();
require_once __DIR__ . '/../../../vendor/autoload.php'; // Adjust path as needed
use Ramsey\Uuid\Uuid;

include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['studentID'])) {
        $userID = $_SESSION['studentID'];
        $title = isset($_POST['quizTitle']) ? trim($_POST['quizTitle']) : '';
        $schedule_date = isset($_POST['quizDate']) ? trim($_POST['quizDate']) : '';
        $schedule_time = isset($_POST['quizTime']) ? trim($_POST['quizTime']) : '';
        $quizStatus = isset($_POST['quizStatus']) ? trim($_POST['quizStatus']) : '';
        $quizType = isset($_POST['quizType']) ? trim($_POST['quizType']) : '';
        $quizDuration = isset($_POST['quizDuration']) ? trim($_POST['quizDuration']) : '';
        $topics = isset($_POST['topics']) ? trim($_POST['topics']) : '';
        $date = date('Y-m-d');
        $QuizUUID = Uuid::uuid4()->toString();
        // Validate required fields
        if (empty($title) || empty($topics) || empty($quizStatus)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            exit;
        }

        // Prepare statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO quizzes (`uuid`, `user_id`, `title`, `topics`, `status`, `schedule_date`, `schedule_time`, `quizType`, `quizDuration`, `date_created`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $QuizUUID, $userID, $title, $topics, $quizStatus, $schedule_date, $schedule_time, $quizType, $quizDuration, $date);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Quiz created successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Could not create Quiz, something went wrong', 'error' => $stmt->error]);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    }
}
?>