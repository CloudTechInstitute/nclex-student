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
        $quizDuration = isset($_POST['quizDuration']) ? trim($_POST['quizDuration']) : '';
        $topics = isset($_POST['topics']) ? trim($_POST['topics']) : '';
        $status = isset($_POST['quizStatus']) ? trim($_POST['quizStatus']) : '';
        $date = date('Y-m-d');
        $QuizUUID = Uuid::uuid4()->toString();

        $quizCountStmt = $conn->prepare("SELECT COUNT(*) as total FROM quizzes WHERE user_id = ?");
        $quizCountStmt->bind_param("s", $userID);
        $quizCountStmt->execute();
        $quizCountResult = $quizCountStmt->get_result();
        $quizData = $quizCountResult->fetch_assoc();
        $existingQuizCount = $quizData['total'];
        $quizCountStmt->close();

        if ($existingQuizCount >= 10) {
            http_response_code(429); // Too Many Requests
            echo json_encode([
                'status' => 'error',
                'message' => 'Quiz limit reached. You cannot create more than 10 quizzes.'
            ]);
            exit;
        }

        if (empty($title) || empty($topics)) {
            http_response_code(400); // Bad Request
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            exit;
        }

        $topicArray = array_map('trim', explode(',', $topics));
        $placeholders = implode(',', array_fill(0, count($topicArray), '?'));

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

        $quizDuration = $questionCount;

        $insertQuery = "INSERT INTO quizzes (`uuid`, `user_id`, `title`, `topics`, `status`, `schedule_date`, `schedule_time`, `quizDuration`, `date_created`, `number_of_questions`)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param(
            "sssssssssi",
            $QuizUUID,
            $userID,
            $title,
            $topics,
            $status,
            $schedule_date,
            $schedule_time,
            $quizDuration,
            $date,
            $questionCount
        );

        if ($stmt->execute()) {
            http_response_code(201); // Created
            echo json_encode([
                'status' => 'success',
                'message' => 'Quiz created successfully',
                'question_count' => $questionCount
            ]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode([
                'status' => 'error',
                'message' => 'Could not create Quiz, something went wrong',
                'error' => $stmt->error
            ]);
        }

        $stmt->close();
        $conn->close();
    } else {
        http_response_code(401); // Unauthorized
        echo json_encode([
            'status' => 'error',
            'message' => 'Session expired or user not authenticated'
        ]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
?>