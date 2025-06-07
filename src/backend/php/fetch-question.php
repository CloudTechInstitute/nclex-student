<?php
session_start();
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['uuid'])) {
        $uuid = $_GET['uuid'];
        $userId = isset($_SESSION['studentID']) ? $_SESSION['studentID'] : null;

        if (!$userId) {
            http_response_code(401); // Unauthorized
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
                } else {
                    $row['attempted'] = false;
                    unset($row['solution']);
                    unset($row['selected_option']);
                    unset($row['answer']);
                }

                $questions[] = $row;
                $stmt3->close();
            }

            http_response_code(200); // OK
            echo json_encode(['status' => 'success', 'category' => $category, 'data' => $questions]);
            $stmt2->close();
        } else {
            http_response_code(404); // Not Found
            echo json_encode(['status' => 'error', 'message' => 'Category not found']);
        }

        $stmt->close();
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'ID parameter is missing']);
    }

    $conn->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>