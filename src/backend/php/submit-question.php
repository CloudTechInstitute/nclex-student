<?php
session_start(); // start session at the top
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $questionId = isset($_POST['question_id']) ? trim($_POST['question_id']) : 0;
    $submittedAnswer = isset($_POST['answer']) ? trim($_POST['answer']) : '';
    $userId = isset($_SESSION['studentID']) ? $_SESSION['studentID'] : null;

    if ($questionId === 0 || empty($submittedAnswer) || !$userId) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        exit;
    }

    // Fetch the correct answer and solution from the database
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

        // Insert into attempted table
        $insertStmt = $conn->prepare("INSERT INTO attempted (user_id, question_id, question, selected_option, is_correct) VALUES (?, ?, ?, ?, ?)");
        $insertStmt->bind_param("ssssi", $userId, $questionUuid, $questionText, $submittedAnswer, $isCorrect);
        $insertStmt->execute();
        $insertStmt->close();

        echo json_encode([
            'status' => 'success',
            'correct' => $isCorrect,
            'answer' => $correctAnswer,
            'solution' => $solution,
            'selected_answer' => $submittedAnswer
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Question not found']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>