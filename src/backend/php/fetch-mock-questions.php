<?php
session_start();

require_once __DIR__ . '/../../../vendor/autoload.php';

include 'connection.php';
header('Content-Type: application/json');

use Ramsey\Uuid\Uuid;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = isset($_SESSION['studentID']) ? $_SESSION['studentID'] : null;
    $product = isset($_SESSION['product_uuid']) ? $_SESSION['product_uuid'] : null;

    if (!$userId) {
        http_response_code(401); // Unauthorized
        echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        exit;
    }
    if (!$product) {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Product not specified']);
        exit;
    }

    $stmtProduct = $conn->prepare("SELECT assessment FROM products WHERE uuid = ?");
    $stmtProduct->bind_param("s", $product);
    $stmtProduct->execute();
    $resultProduct = $stmtProduct->get_result();
    $productData = $resultProduct->fetch_assoc();
    $stmtProduct->close();

    if (!$productData) {
        http_response_code(404); // Not Found
        echo json_encode(['status' => 'error', 'message' => 'Product not found']);
        exit;
    }

    $assessmentsAllowed = (int) $productData['assessment'];

    $stmtMocks = $conn->prepare("SELECT COUNT(*) as completed FROM completed_mock WHERE user_id = ?");
    $stmtMocks->bind_param("s", $userId);
    $stmtMocks->execute();
    $resultMocks = $stmtMocks->get_result();
    $mockData = $resultMocks->fetch_assoc();
    $stmtMocks->close();

    $completedMocks = (int) $mockData['completed'];

    if ($completedMocks >= $assessmentsAllowed) {
        http_response_code(403); // Forbidden
        echo json_encode(['status' => 'error', 'message' => 'Cannot create assessment, you have exceeded the allowed number of assessments for the package you subscribed to.']);
        exit;
    }

    $mockUuid = Uuid::uuid4()->toString();
    $timestamp = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("
        SELECT q.question_uuid, q.question, q.options, q.type, q.mark_allocated, q.category, q.solution, q.answer
        FROM questions q
        WHERE NOT EXISTS (
            SELECT 1 FROM mock_questions m 
            WHERE m.user_id = ? AND m.question_id = q.question_uuid
        )
        ORDER BY RAND()
        LIMIT 200
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

    if (count($questions) > 0) {
        $totalQuestions = count($questions);
        $duration = $totalQuestions / 2; // 2 minutes per question
        $stmtMock = $conn->prepare("INSERT INTO mock (mock_uuid, user_id, total_questions, created_at, duration) VALUES (?, ?, ?, ?, ?)");
        $stmtMock->bind_param("ssiss", $mockUuid, $userId, $totalQuestions, $timestamp, $duration);
        $stmtMock->execute();
        $stmtMock->close();

        http_response_code(200); // OK
        echo json_encode(['status' => 'success', 'mock_uuid' => $mockUuid, 'data' => $questions, 'duration' => $duration]);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(['status' => 'error', 'message' => 'No unattempted questions available']);
    }

    $stmt->close();
    $conn->close();

} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>