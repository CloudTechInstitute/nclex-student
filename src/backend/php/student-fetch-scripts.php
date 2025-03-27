<?php
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch video count
    $packageQuery = "SELECT COUNT(*) AS package_count FROM subscriptions";
    $packageResult = $conn->query($packageQuery);

    // Fetch question count
    $quizQuery = "SELECT COUNT(*) AS quiz_count FROM questions";
    $quizResult = $conn->query($quizQuery);

    // Fetch ebooks count
    $assessmentQuery = "SELECT COUNT(*) AS assessment_count FROM ebooks";
    $assessmentResult = $conn->query($assessmentQuery);

    // Fetch audios count
    $tutorialQuery = "SELECT COUNT(*) AS tutorial_count FROM tutorials";
    $tutorialResult = $conn->query($tutorialQuery);

    if ($packageResult && $quizResult) {
        $packageRow = $packageResult->fetch_assoc();
        $quizRow = $quizResult->fetch_assoc();
        $assessmentRow = $tutorialResult->fetch_assoc();
        $tutorialRow = $assessmentResult->fetch_assoc();

        echo json_encode([
            'status' => 'success',
            'package_count' => $packageRow['package_count'],
            'quiz_count' => $quizRow['quiz_count'],
            'tutorial_count' => $assessmentRow['tutorial_count'],
            'assessment_count' => $tutorialRow['assessment_count']
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch data']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>