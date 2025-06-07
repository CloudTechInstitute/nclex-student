<?php
session_start();
include 'connection.php';
header('Content-Type: application/json');

function set_status($code)
{
    http_response_code($code);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['uuid']) && isset($_SESSION['studentID'])) {
        $uuid = $_GET['uuid'];
        $userId = $_SESSION['studentID'];

        // 1. Check if quiz is scheduled
        $checkQuiz = $conn->prepare("SELECT status, schedule_date, schedule_time, topics FROM quizzes WHERE uuid = ?");
        $checkQuiz->bind_param("s", $uuid);
        $checkQuiz->execute();
        $quizResult = $checkQuiz->get_result();

        if ($quizResult->num_rows === 0) {
            set_status(404);
            echo json_encode(['status' => 'error', 'message' => 'Quiz not found']);
            exit;
        }

        $quizInfo = $quizResult->fetch_assoc();

        if ($quizInfo['status'] === 'scheduled') {
            $now = new DateTime();
            $scheduledDateTime = new DateTime("{$quizInfo['schedule_date']} {$quizInfo['schedule_time']}");
            if ($now < $scheduledDateTime) {
                set_status(403);
                echo json_encode([
                    'status' => 'not_ready',
                    'message' => 'Quiz not available yet. It will be available on ' . $scheduledDateTime->format('d-m-Y') . ' at ' . $scheduledDateTime->format('g:i A')
                ]);
                exit;
            }
        }

        // 2. Check if questions already assigned
        $checkAssigned = $conn->prepare("SELECT COUNT(*) as total FROM quiz_questions WHERE quiz_uuid = ? AND user_id = ?");
        $checkAssigned->bind_param("ss", $uuid, $userId);
        $checkAssigned->execute();
        $assignedCount = $checkAssigned->get_result()->fetch_assoc()['total'];

        if ($assignedCount == 0) {
            // Assign all matching questions (no pool, no limit)
            $categories = array_map('trim', explode(',', $quizInfo['topics']));
            $placeholders = implode(',', array_fill(0, count($categories), '?'));
            $types = str_repeat('s', count($categories));

            // Fetch all matching questions
            $questionQuery = "SELECT question_uuid FROM questions WHERE category IN ($placeholders) ORDER BY RAND()";
            $stmtQ = $conn->prepare($questionQuery);
            $stmtQ->bind_param($types, ...$categories);
            $stmtQ->execute();
            $questionsResult = $stmtQ->get_result();

            $questionUuids = [];
            while ($row = $questionsResult->fetch_assoc()) {
                $questionUuids[] = $row['question_uuid'];
            }

            if (count($questionUuids) === 0) {
                set_status(404);
                echo json_encode(['status' => 'error', 'message' => 'No matching questions found for quiz topics']);
                exit;
            }

            // Store all selected questions
            $insertStmt = $conn->prepare("INSERT IGNORE INTO quiz_questions (quiz_uuid, question_uuid, user_id, question_order) VALUES (?, ?, ?, ?)");
            $order = 1;
            foreach ($questionUuids as $questionId) {
                $insertStmt->bind_param("sssi", $uuid, $questionId, $userId, $order);
                $insertStmt->execute();
                $order++;
            }
            $insertStmt->close();
        }

        // 3. Fetch questions assigned to this quiz attempt
        $stmt = $conn->prepare("
            SELECT q.question_uuid, q.question, q.options, q.type, q.mark_allocated, q.solution, q.answer
            FROM quiz_questions qq
            INNER JOIN questions q ON qq.question_uuid = q.question_uuid
            WHERE qq.quiz_uuid = ? AND qq.user_id = ?
            ORDER BY qq.question_order
        ");
        $stmt->bind_param("ss", $uuid, $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $questions = [];

            while ($row = $result->fetch_assoc()) {
                $questionUuid = $row['question_uuid'];

                // Check attempt status
                $stmt2 = $conn->prepare("SELECT selected_option FROM attempted_quiz WHERE user_id = ? AND question_id = ? AND quiz_id = ?");
                $stmt2->bind_param("sss", $userId, $questionUuid, $uuid);
                $stmt2->execute();
                $attemptResult = $stmt2->get_result();

                if ($attemptResult->num_rows > 0) {
                    $attemptData = $attemptResult->fetch_assoc();
                    $row['attempted'] = true;
                    $row['selected_option'] = $attemptData['selected_option'];
                } else {
                    $row['attempted'] = false;
                    unset($row['solution'], $row['selected_option'], $row['answer']);
                }

                $questions[] = $row;
                $stmt2->close();
            }

            set_status(200);
            echo json_encode(['status' => 'success', 'data' => $questions, 'questions_count' => count($questions)]);
        } else {
            set_status(404);
            echo json_encode(['status' => 'error', 'message' => 'No questions assigned for this quiz']);
        }

        $stmt->close();
        $checkQuiz->close();
        $checkAssigned->close();
        $conn->close();
    } else {
        set_status(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing UUID or student ID']);
    }
} else {
    set_status(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>