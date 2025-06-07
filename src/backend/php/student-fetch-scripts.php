<?php
include 'connection.php';
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['studentID'])) {
        http_response_code(401); // Unauthorized
        echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
        exit;
    }

    $userId = $_SESSION['studentID'];

    // Fetch package count
    $packageQuery = "SELECT COUNT(*) AS package_count FROM subscriptions WHERE user_id = ?";
    $stmt = $conn->prepare($packageQuery);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $packageResult = $stmt->get_result();
    $packageRow = $packageResult->fetch_assoc();
    $packageCount = $packageRow['package_count'];
    $stmt->close();

    // Fetch all subscriptions
    $subscriptionDataQuery = "SELECT * FROM subscriptions WHERE user_id = ?";
    $stmt = $conn->prepare($subscriptionDataQuery);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $subscriptionResult = $stmt->get_result();
    $subscriptions = [];
    while ($row = $subscriptionResult->fetch_assoc()) {
        $subscriptions[] = $row;
    }
    $stmt->close();

    // Fetch quiz count
    $quizQuery = "SELECT COUNT(*) AS quiz_count FROM quizzes WHERE user_id = ?";
    $stmt = $conn->prepare($quizQuery);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $quizResult = $stmt->get_result();
    $quizRow = $quizResult->fetch_assoc();
    $stmt->close();

    // Fetch completed quiz count
    $completeQuizQuery = "SELECT COUNT(*) AS quiz_completed FROM completed_quiz WHERE user_id = ?";
    $stmt = $conn->prepare($completeQuizQuery);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $completeQuizResult = $stmt->get_result();
    $completeQuizRow = $completeQuizResult->fetch_assoc();
    $stmt->close();

    // Fetch mock (ebooks) count
    $mockQuery = "SELECT COUNT(*) AS mock_count FROM mock WHERE user_id = ?";
    $stmt = $conn->prepare($mockQuery);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $mockResult = $stmt->get_result();
    $mockRow = $mockResult->fetch_assoc();
    $stmt->close();

    // Fetch completed mock count
    $completeMockQuery = "SELECT COUNT(*) AS mock_completed FROM completed_mock WHERE user_id = ?";
    $stmt = $conn->prepare($completeMockQuery);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $completeMockResult = $stmt->get_result();
    $completeMockRow = $completeMockResult->fetch_assoc();
    $stmt->close();

    // Fetch tutorial count
    $tutorialQuery = "SELECT COUNT(*) AS tutorial_count FROM tutorials WHERE user_id = ?";
    $stmt = $conn->prepare($tutorialQuery);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $tutorialResult = $stmt->get_result();
    $tutorialRow = $tutorialResult->fetch_assoc();
    $stmt->close();

    http_response_code(200); // OK
    echo json_encode([
        'status' => 'success',
        'package_count' => $packageCount,
        'quiz_count' => $quizRow['quiz_count'],
        'quiz_completed' => $completeQuizRow['quiz_completed'],
        'mock_count' => $mockRow['mock_count'],
        'mock_completed' => $completeMockRow['mock_completed'],
        'tutorial_count' => $tutorialRow['tutorial_count'],
        'subscriptions' => $subscriptions
    ]);

    $conn->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>