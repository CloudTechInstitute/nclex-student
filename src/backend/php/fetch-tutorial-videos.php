<?php
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['uuid'])) {
        $uuid = $_GET['uuid'];

        // Step 1: Fetch the tutorial data including status, schedule_date, and schedule_time
        $stmt = $conn->prepare("SELECT topics, status, schedule_date, schedule_time FROM tutorials WHERE uuid = ?");
        $stmt->bind_param("s", $uuid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $tutorial = $result->fetch_assoc();
            $topics = $tutorial['topics'];
            $status = $tutorial['status'];
            $scheduleDate = $tutorial['schedule_date'];
            $scheduleTime = $tutorial['schedule_time'];

            // Step 2: Check if tutorial is scheduled and not yet due
            if ($status === 'scheduled') {
                $now = new DateTime();
                $scheduledDateTime = new DateTime("$scheduleDate $scheduleTime");

                if ($now < $scheduledDateTime) {
                    echo json_encode([
                        'status' => 'not_ready',
                        'message' => 'Tutorial not available yet. It will be available on ' . $scheduledDateTime->format('d-m-Y') . ' at ' . $scheduledDateTime->format('g:i A'),
                        'available_on' => $scheduledDateTime->format('Y-m-d H:i:s')
                    ]);
                    exit;
                }
            }


            // Step 3: Convert comma-separated categories into array
            $categories = array_map('trim', explode(',', $topics));
            $placeholders = implode(',', array_fill(0, count($categories), '?'));
            $types = str_repeat('s', count($categories));

            // Step 4: Fetch videos for those categories
            $stmt2 = $conn->prepare("SELECT * FROM videos WHERE category IN ($placeholders)");
            $stmt2->bind_param($types, ...$categories);
            $stmt2->execute();
            $result2 = $stmt2->get_result();

            if ($result2->num_rows > 0) {
                $videos = [];
                while ($row = $result2->fetch_assoc()) {
                    $videos[] = $row;
                }
                echo json_encode(['status' => 'success', 'data' => $videos]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No videos found for this tutorial']);
            }

            $stmt2->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Tutorial not found']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'UUID parameter is missing']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>