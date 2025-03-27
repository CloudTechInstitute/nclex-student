<?php
session_start();
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['uuid'])) {
        $uuid = $_GET['uuid'];
        $userId = isset($_SESSION['studentID']) ? $_SESSION['studentID'] : null;

        if (!$userId) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
            exit;
        }

        // Step 1: Get category name from UUID
        $stmt = $conn->prepare("SELECT category FROM categories WHERE uuid = ?");
        $stmt->bind_param("s", $uuid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $categoryData = $result->fetch_assoc();
            $category = $categoryData['category'];

            // Step 2: Fetch all questions under the category
            $stmt2 = $conn->prepare("SELECT question_uuid, question, options, type, mark_allocated, solution, answer FROM questions WHERE category = ?");
            $stmt2->bind_param("s", $category);
            $stmt2->execute();
            $result2 = $stmt2->get_result();

            $questions = [];

            while ($row = $result2->fetch_assoc()) {
                $questionUuid = $row['question_uuid'];

                // Step 3: Check if this question has been attempted by the user
                $stmt3 = $conn->prepare("SELECT selected_option FROM attempted WHERE user_id = ? AND question_id = ?");
                $stmt3->bind_param("ss", $userId, $questionUuid);
                $stmt3->execute();
                $attemptResult = $stmt3->get_result();

                if ($attemptResult->num_rows > 0) {
                    $attemptData = $attemptResult->fetch_assoc();
                    $row['attempted'] = true;
                    $row['selected_option'] = $attemptData['selected_option'];
                    // keep solution visible only if attempted
                } else {
                    $row['attempted'] = false;
                    // $row['selected_option'] = null;
                    unset($row['solution']); // hide solution if not attempted
                    unset($row['selected_option']); // hide solution if not attempted
                    unset($row['answer']); // hide solution if not attempted
                }

                // unset($row['solution']);
                $questions[] = $row;

                $stmt3->close();
            }

            echo json_encode(['status' => 'success', 'category' => $category, 'data' => $questions]);
            $stmt2->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Category not found']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID parameter is missing']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>