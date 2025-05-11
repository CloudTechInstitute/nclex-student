<?php
session_start();

require_once __DIR__ . '/../../../vendor/autoload.php';

include 'connection.php';
header('Content-Type: application/json');

use Ramsey\Uuid\Uuid;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = isset($_SESSION['studentID']) ? $_SESSION['studentID'] : null;

    if (!$userId) {
        echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        exit;
    }

    // Step 1: Generate UUID using Ramsey
    $mockUuid = Uuid::uuid4()->toString();
    $timestamp = date('Y-m-d H:i:s');

    // Step 2: Fetch random unattempted questions
    $stmt = $conn->prepare("
        SELECT q.question_uuid, q.question, q.options, q.type, q.mark_allocated, q.category, q.solution, q.answer
        FROM questions q
        WHERE NOT EXISTS (
            SELECT 1 FROM mock_questions m 
            WHERE m.user_id = ? AND m.question_id = q.question_uuid
        )
        ORDER BY RAND()
        LIMIT 10
    ");
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $questions = [];

    while ($row = $result->fetch_assoc()) {
        unset($row['solution'], $row['answer']);
        $row['mock_uuid'] = $mockUuid;
        $questions[] = $row;
    }

    // Step 3: Store mock info
    if (count($questions) > 0) {
        $totalQuestions = count($questions);
        $duration = $totalQuestions;
        $stmtMock = $conn->prepare("INSERT INTO mock (mock_uuid, user_id, total_questions, created_at, duration) VALUES (?, ?, ?, ?, ?)");
        $stmtMock->bind_param("ssiss", $mockUuid, $userId, $totalQuestions, $timestamp, $duration);
        $stmtMock->execute();
        $stmtMock->close();


        echo json_encode(['status' => 'success', 'mock_uuid' => $mockUuid, 'data' => $questions, 'duration' => $duration]);

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No unattempted questions available']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

?>