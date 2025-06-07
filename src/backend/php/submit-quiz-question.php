<?php
session_start();
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $questionId = isset($_POST['question_id']) ? trim($_POST['question_id']) : 0;
    $submittedAnswer = isset($_POST['answer']) ? trim($_POST['answer']) : '';
    $userId = isset($_SESSION['studentID']) ? $_SESSION['studentID'] : null;
    $quiz_uuid = isset($_POST['quiz_id']) ? trim($_POST['quiz_id']) : null;

    if (!$quiz_uuid) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Quiz ID (uuid) is missing']);
        exit;
    }

    if ($questionId === 0 || empty($submittedAnswer) || !$userId) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        exit;
    }

    $stmt = $conn->prepare("SELECT question_uuid, question, answer, solution FROM questions WHERE question_uuid = ?");
    $stmt->bind_param("s", $questionId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $correctAnswer = trim($row['answer']);
        $solution = trim($row['solution']);
        $questionText = $row['question'];
        $questionUuid = $row['question_uuid'];

        $isCorrect = strcasecmp($correctAnswer, $submittedAnswer) === 0;

        $insertStmt = $conn->prepare("INSERT INTO attempted_quiz (quiz_id, user_id, question_id, question, selected_option, is_correct) VALUES (?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("sssssi", $quiz_uuid, $userId, $questionUuid, $questionText, $submittedAnswer, $isCorrect);
        $insertStmt->execute();
        $insertStmt->close();

        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'correct' => $isCorrect,
            'answer' => $correctAnswer,
            'solution' => $solution,
            'selected_answer' => $submittedAnswer
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Question not found']);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>