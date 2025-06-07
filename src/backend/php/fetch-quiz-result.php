<?php
include 'connection.php';
session_start();
header('Content-Type: application/json');

function set_status($code)
{
    http_response_code($code);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['uuid']) && isset($_SESSION['studentID'])) {
        $uuid = $_GET['uuid'];
        $userID = $_SESSION['studentID'];

        // First query: Count correct answers
        $stmt = $conn->prepare("SELECT COUNT(*) AS correct FROM attempted_quiz WHERE user_id = ? AND quiz_id = ? AND is_correct = 1");
        $stmt->bind_param("ss", $userID, $uuid);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $correct = $row['correct'];
            $stmt->close();

            // Second query: Count total attempts
            $stmt2 = $conn->prepare("SELECT number_of_questions FROM quizzes WHERE uuid = ?");
            $stmt2->bind_param("s", $uuid);

            if ($stmt2->execute()) {
                $result2 = $stmt2->get_result();
                $row2 = $result2->fetch_assoc();
                $total = $row2['number_of_questions'];
                $stmt2->close();

                // Insert into completed_quiz
                $insert = $conn->prepare("INSERT INTO completed_quiz (quiz_id, user_id, user_score, total_questions) VALUES (?, ?, ?, ?)");
                $insert->bind_param("ssss", $uuid, $userID, $correct, $total);

                if ($insert->execute()) {
                    set_status(200);
                    echo json_encode([
                        'status' => 'success',
                        'correct_answers' => $correct,
                        'total' => $total,
                        'wrong_answers' => $total - $correct,
                        'student' => $userID,
                        'quiz' => $uuid
                    ]);
                } else {
                    set_status(500);
                    echo json_encode(['status' => 'error', 'message' => 'Failed to insert completion record']);
                }

                $insert->close();
            } else {
                set_status(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to fetch total count']);
            }
        } else {
            set_status(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch correct count']);
        }

        $conn->close();
    } else {
        set_status(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing uuid or not logged in']);
    }
} else {
    set_status(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>