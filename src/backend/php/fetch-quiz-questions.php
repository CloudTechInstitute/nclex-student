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
        $checkAssigned = $conn->prepare("SELECT question_ids FROM quiz_questions WHERE quiz_uuid = ? AND user_id = ?");
        $checkAssigned->bind_param("ss", $uuid, $userId);
        $checkAssigned->execute();
        $assignedResult = $checkAssigned->get_result();
        $questionIdsString = null;

        if ($assignedResult->num_rows === 0) {
            // Assign questions
            $categories = array_map('trim', explode(',', $quizInfo['topics']));
            $placeholders = implode(',', array_fill(0, count($categories), '?'));
            $types = str_repeat('s', count($categories));

            $questionQuery = "SELECT question_uuid FROM questions WHERE category IN ($placeholders) ORDER BY RAND()";
            $stmtQ = $conn->prepare($questionQuery);
            $stmtQ->bind_param($types, ...$categories);
            $stmtQ->execute();
            $questionsResult = $stmtQ->get_result();

            $questionUuids = [];
            while ($row = $questionsResult->fetch_assoc()) {
                $questionUuids[] = $row['question_uuid'];
            }

            if (empty($questionUuids)) {
                set_status(404);
                echo json_encode(['status' => 'error', 'message' => 'No matching questions found']);
                exit;
            }

            $questionIdsString = implode(',', $questionUuids);

            $insert = $conn->prepare("INSERT INTO quiz_questions (quiz_uuid, user_id, question_ids) VALUES (?, ?, ?)");
            $insert->bind_param("sss", $uuid, $userId, $questionIdsString);
            if (!$insert->execute()) {
                set_status(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to assign questions']);
                exit;
            }
            $insert->close();
        } else {
            $questionIdsString = $assignedResult->fetch_assoc()['question_ids'];
        }

        // 3. Fetch and return questions
        $questionUuids = explode(',', $questionIdsString);
        $placeholders = implode(',', array_fill(0, count($questionUuids), '?'));
        $types = str_repeat('s', count($questionUuids));

        $query = "
            SELECT question_uuid, question, options, type, mark_allocated, solution, answer
            FROM questions
            WHERE question_uuid IN ($placeholders)
        ";

        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$questionUuids);
        $stmt->execute();
        $result = $stmt->get_result();

        $questions = [];
        while ($row = $result->fetch_assoc()) {
            $questionUuid = $row['question_uuid'];

            // Attempted status
            $attemptStmt = $conn->prepare("SELECT selected_option FROM attempted_quiz WHERE user_id = ? AND question_id = ? AND quiz_id = ?");
            $attemptStmt->bind_param("sss", $userId, $questionUuid, $uuid);
            $attemptStmt->execute();
            $attemptResult = $attemptStmt->get_result();

            if ($attemptResult->num_rows > 0) {
                $row['attempted'] = true;
                $row['selected_option'] = $attemptResult->fetch_assoc()['selected_option'];
            } else {
                $row['attempted'] = false;
                unset($row['solution'], $row['selected_option'], $row['answer']);
            }

            $questions[$questionUuid] = $row;
            $attemptStmt->close();
        }

        // Order questions as per original UUID order
        $orderedQuestions = [];
        foreach ($questionUuids as $qid) {
            if (isset($questions[$qid])) {
                $orderedQuestions[] = $questions[$qid];
            }
        }

        set_status(200);
        echo json_encode(['status' => 'success', 'data' => $orderedQuestions, 'questions_count' => count($orderedQuestions)]);

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