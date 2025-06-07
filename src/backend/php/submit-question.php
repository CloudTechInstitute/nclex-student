<?php
session_start();
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $uuid = isset($_POST['uuid']) ? trim($_POST['uuid']) : '';
    $questionId = isset($_POST['question_id']) ? trim($_POST['question_id']) : '';
    $submittedAnswer = isset($_POST['answer']) ? trim($_POST['answer']) : '';
    $userId = $_SESSION['studentID'] ?? null;

    if (empty($questionId) || empty($submittedAnswer) || !$userId) {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        exit;
    }

    // Fetch correct answer and solution
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

        // Insert into attempted
        $insertStmt = $conn->prepare("INSERT INTO attempted (user_id, category_id, question_id, question, selected_option, is_correct) VALUES (?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("sssssi", $userId, $uuid, $questionUuid, $questionText, $submittedAnswer, $isCorrect);
        $insertStmt->execute();
        $insertStmt->close();

        http_response_code(200); // OK
        echo json_encode([
            'status' => 'success',
            'correct' => $isCorrect,
            'answer' => $correctAnswer,
            'solution' => $solution,
            'selected_answer' => $submittedAnswer
        ]);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(['status' => 'error', 'message' => 'Question not found']);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>