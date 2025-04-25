<?php
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch package count
    $packageQuery = "SELECT COUNT(*) AS package_count FROM subscriptions";
    $packageResult = $conn->query($packageQuery);
    $packageRow = $packageResult->fetch_assoc();
    $packageCount = $packageRow['package_count'];

    // Fetch all subscription data
    $subscriptionDataQuery = "SELECT * FROM subscriptions";
    $subscriptionResult = $conn->query($subscriptionDataQuery);
    $subscriptions = [];
    while ($row = $subscriptionResult->fetch_assoc()) {
        $subscriptions[] = $row;
    }

    // Fetch quiz count
    $quizQuery = "SELECT COUNT(*) AS quiz_count FROM quizzes";
    $quizResult = $conn->query($quizQuery);
    $quizRow = $quizResult->fetch_assoc();

    // Fetch assessment (ebooks) count
    $assessmentQuery = "SELECT COUNT(*) AS assessment_count FROM ebooks";
    $assessmentResult = $conn->query($assessmentQuery);
    $assessmentRow = $assessmentResult->fetch_assoc();

    // Fetch tutorial (audios) count
    $tutorialQuery = "SELECT COUNT(*) AS tutorial_count FROM tutorials";
    $tutorialResult = $conn->query($tutorialQuery);
    $tutorialRow = $tutorialResult->fetch_assoc();

    if ($packageResult && $quizResult && $assessmentResult && $tutorialResult) {
        echo json_encode([
            'status' => 'success',
            'package_count' => $packageCount,
            'quiz_count' => $quizRow['quiz_count'],
            'assessment_count' => $assessmentRow['assessment_count'],
            'tutorial_count' => $tutorialRow['tutorial_count'],
            'subscriptions' => $subscriptions
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch data']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>