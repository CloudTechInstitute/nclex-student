<?php
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if 'id' is provided in the URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']); // Convert to integer for security

        // Step 1: Fetch the category using the ID
        $stmt = $conn->prepare("SELECT category FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $categoryData = $result->fetch_assoc();
            $category = $categoryData['category'];

            // Step 2: Use the category to fetch questions
            $stmt2 = $conn->prepare("SELECT * FROM videos WHERE category = ?");
            $stmt2->bind_param("s", $category);
            $stmt2->execute();
            $result2 = $stmt2->get_result();

            if ($result2->num_rows > 0) {
                $videos = [];
                while ($row = $result2->fetch_assoc()) {
                    $videos[] = $row;
                }
                echo json_encode(['status' => 'success', 'category' => $category, 'data' => $videos]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No videos found for this category']);
            }

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