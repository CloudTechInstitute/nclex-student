<?php
include 'connection.php';
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['studentID'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['studentID'];

// Fetch subscriptions
$subscriptions = [];
$productId = null;
$stmt = $conn->prepare("SELECT * FROM subscriptions WHERE user_id = ?");
$stmt->bind_param("s", $userId);
$stmt->execute();
$subscriptionResult = $stmt->get_result();

while ($row = $subscriptionResult->fetch_assoc()) {
    $subscriptions[] = $row;
    if (!$productId) {
        $productId = $row['product_uuid']; // first found product_uuid
    }
}
$stmt->close();
$packageCount = count($subscriptions);

// Helper: Fetch quizzes or assessment from subscription or fallback to product
function getValueOrFallback($conn, $userId, $productId, $column)
{
    $value = 0;

    // 1. Try getting from latest subscription
    $query = "SELECT $column FROM subscriptions WHERE user_id = ? ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $value = isset($data[$column]) ? (int) $data[$column] : 0;
    }
    $stmt->close();

    // If value is valid, return it
    if ($value > 0) {
        return $value;
    }

    // 2. Fallback to product
    if ($productId) {
        $query = "SELECT $column FROM products WHERE uuid = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $value = isset($data[$column]) ? (int) $data[$column] : 0;
        }
        $stmt->close();
    }

    return $value;
}

$mockCount = getValueOrFallback($conn, $userId, $productId, 'assessment');
$quizCount = getValueOrFallback($conn, $userId, $productId, 'quizzes');

// Helper: fetch count with alias
function fetchCount($conn, $query, $userId, $alias)
{
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return isset($row[$alias]) ? (int) $row[$alias] : 0;
}

// Fetch other counts
$quizCompleted = fetchCount($conn, "SELECT COUNT(*) AS quiz_completed FROM completed_quiz WHERE user_id = ?", $userId, 'quiz_completed');
$mockCompleted = fetchCount($conn, "SELECT COUNT(*) AS mock_completed FROM completed_mock WHERE user_id = ?", $userId, 'mock_completed');
$tutorialCount = fetchCount($conn, "SELECT COUNT(*) AS tutorial_count FROM tutorials WHERE user_id = ?", $userId, 'tutorial_count');

// Response
http_response_code(200);
echo json_encode([
    'status' => 'success',
    'package_count' => $packageCount,
    'quiz_count' => $quizCount,
    'quiz_completed' => $quizCompleted,
    'mock_count' => $mockCount,
    'mock_completed' => $mockCompleted,
    'tutorial_count' => $tutorialCount,
    'subscriptions' => $subscriptions
]);

$conn->close();