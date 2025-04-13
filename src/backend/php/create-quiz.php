<?php

require_once __DIR__ . '/../../../vendor/autoload.php'; // Adjust path as needed
use Ramsey\Uuid\Uuid;

include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['quizTitle']) ? trim($_POST['quizTitle']) : '';
    $topics = isset($_POST['topics']) ? trim($_POST['topics']) : ''; // Already a comma-separated string
    $date = date('Y-m-d');
    $QuizUUID = Uuid::uuid4()->toString();
    // Validate required fields
    if (empty($title) || empty($topics)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO quizzes (`uuid`,`title`, `topics`, `date`) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $QuizUUID, $title, $topics, $date);

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
?>