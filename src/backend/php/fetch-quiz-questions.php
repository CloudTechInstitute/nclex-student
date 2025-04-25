<?php
session_start();
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['uuid']) && isset($_SESSION['studentID'])) {
        $uuid = $_GET['uuid'];
        $userId = isset($_SESSION['studentID']) ? $_SESSION['studentID'] : null;

        $stmt = $conn->prepare("SELECT topics, status, schedule_date, schedule_time FROM quizzes WHERE uuid = ?");
        $stmt->bind_param("s", $uuid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $quiz = $result->fetch_assoc();
            $topics = $quiz['topics'];
            $status = $quiz['status'];
            $scheduleDate = $quiz['schedule_date'];
            $scheduleTime = $quiz['schedule_time'];

            if ($status === 'scheduled') {
                $now = new DateTime();
                $scheduledDateTime = new DateTime("$scheduleDate $scheduleTime");

                if ($now < $scheduledDateTime) {
                    echo json_encode([
                        'status' => 'not_ready',
                        'message' => 'Quiz not available yet. It will be available on ' . $scheduledDateTime->format('d-m-Y') . ' at ' . $scheduledDateTime->format('g:i A'),

                    ]);
                    exit;
                }
            }

            $categories = array_map('trim', explode(',', $topics));
            $placeholders = implode(',', array_fill(0, count($categories), '?'));
            $types = str_repeat('s', count($categories));

            $stmt2 = $conn->prepare("SELECT question_uuid, question, options, type, mark_allocated, solution, answer FROM questions WHERE category IN ($placeholders)");
            $stmt2->bind_param($types, ...$categories);
            $stmt2->execute();
            $result2 = $stmt2->get_result();

            if ($result2->num_rows > 0) {
                $questions = [];
                while ($row = $result2->fetch_assoc()) {
                    $questionUuid = $row['question_uuid'];

                    $stmt3 = $conn->prepare("SELECT selected_option FROM attempted_quiz WHERE user_id = ? AND question_id = ? AND quiz_id = ?");
                    $stmt3->bind_param("sss", $userId, $questionUuid, $uuid);
                    $stmt3->execute();
                    $attemptResult = $stmt3->get_result();

                    if ($attemptResult->num_rows > 0) {
                        $attemptData = $attemptResult->fetch_assoc();
                        $row['attempted'] = true;
                        $row['selected_option'] = $attemptData['selected_option'];
                    } else {
                        $row['attempted'] = false;
                        unset($row['solution']);
                        unset($row['selected_option']);
                        unset($row['answer']);
                    }

                    $questions[] = $row;
                    $stmt3->close();
                }

                echo json_encode(['status' => 'success', 'data' => $questions]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No questions found for this quiz']);
            }

            $stmt2->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Quiz not found']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'UUID or user_id parameter is missing']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>