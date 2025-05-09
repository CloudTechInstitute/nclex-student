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

        // Split the topics and prepare for IN clause
        $topicArray = array_map('trim', explode(',', $topics));
        $placeholders = implode(',', array_fill(0, count($topicArray), '?'));

        // Count number of questions matching the selected categories
        $questionCount = 0;
        $questionQuery = "SELECT COUNT(*) as total FROM questions WHERE category IN ($placeholders)";
        $stmt = $conn->prepare($questionQuery);
        $stmt->bind_param(str_repeat('s', count($topicArray)), ...$topicArray);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $questionCount = $row['total'];
        }
        $stmt->close();

        // Insert into quizzes table including question count
        $insertQuery = "INSERT INTO quizzes (`uuid`, `user_id`, `title`, `topics`, `status`, `schedule_date`, `schedule_time`, `quizType`, `quizDuration`, `date_created`, `number_of_questions`)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param(
            "ssssssssssi",
            $QuizUUID,
            $userID,
            $title,
            $topics,
            $quizStatus,
            $schedule_date,
            $schedule_time,
            $quizType,
            $quizDuration,
            $date,
            $questionCount
        );

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Quiz created successfully', 'question_count' => $questionCount]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Could not create Quiz, something went wrong', 'error' => $stmt->error]);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Session expired or user not authenticated']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>