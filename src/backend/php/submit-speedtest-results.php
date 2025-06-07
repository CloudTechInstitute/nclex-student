<?php
session_start();
require_once __DIR__ . '/../../../vendor/autoload.php';
include 'connection.php'; // Should define $conn (MySQLi)
header('Content-Type: application/json');
use Ramsey\Uuid\Uuid;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['studentID'])) {
        $userID = $_SESSION['studentID'];

        // Decode JSON payload
        $input = json_decode(file_get_contents('php://input'), true);

        // Sanitize and extract data
        $totalQuestions = isset($input['totalQuestions']) ? (int) $input['totalQuestions'] : 0;
        $correctAnswers = isset($input['correctAnswers']) ? (int) $input['correctAnswers'] : 0;
        $percentage = isset($input['percentage']) ? htmlspecialchars(strip_tags(trim($input['percentage']))) : '';
        $totalTimeTaken = isset($input['totalTimeTaken']) ? htmlspecialchars(strip_tags(trim($input['totalTimeTaken']))) : '';
        $date = date('Y-m-d H:i:s');
        $speedTestUUID = Uuid::uuid4()->toString();

        // Validate required fields
        if ($totalQuestions <= 0 || $correctAnswers < 0 || $percentage === '' || $totalTimeTaken === '') {
            http_response_code(400); // Bad Request
            echo json_encode(['status' => 'error', 'message' => 'Missing or invalid required fields']);
            exit;
        }

        // Prepare and execute SQL insert
        $stmt = $conn->prepare("INSERT INTO speedtest (`user_id`, `uuid`, `total_questions`, `correct_answers`, `percentage`, `time_taken`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiisss", $userID, $speedTestUUID, $totalQuestions, $correctAnswers, $percentage, $totalTimeTaken, $date);

        if ($stmt->execute()) {
            http_response_code(201); // Created
            echo json_encode(['status' => 'success', 'message' => 'Speed test results submitted successfully']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['status' => 'error', 'message' => 'Database error', 'error' => $stmt->error]);
        }

        $stmt->close();
        $conn->close();
    } else {
        http_response_code(401); // Unauthorized
        echo json_encode(['status' => 'error', 'message' => 'User session not found']);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>