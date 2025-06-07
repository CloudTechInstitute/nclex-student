<?php
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if 'uuid' is provided in the URL
    if (isset($_GET['uuid'])) {
        $id = ($_GET['uuid']);

        // Step 1: Fetch the category using the ID
        $stmt = $conn->prepare("SELECT category FROM categories WHERE uuid = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $categoryData = $result->fetch_assoc();
            $category = $categoryData['category'];

            // Step 2: Use the category to fetch videos
            $stmt2 = $conn->prepare("SELECT * FROM videos WHERE category = ?");
            $stmt2->bind_param("s", $category);
            $stmt2->execute();
            $result2 = $stmt2->get_result();

            if ($result2->num_rows > 0) {
                $videos = [];
                while ($row = $result2->fetch_assoc()) {
                    $videos[] = $row;
                }
                http_response_code(200);
                echo json_encode(['status' => 'success', 'category' => $category, 'data' => $videos]);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'No videos found for this category']);
            }

            $stmt2->close();
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Category not found']);
        }

        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID parameter is missing']);
    }

    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>