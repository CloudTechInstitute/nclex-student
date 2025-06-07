<?php
session_start();
include 'connection.php';
header('Content-Type: application/json');

// Get logged-in user ID
$userId = $_SESSION['studentID'] ?? null;

if (!$userId) {
    http_response_code(401); // Unauthorized
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$competenceQuery = "
    SELECT 
        c.category, 
        a.category_id, 
        AVG(a.is_correct) AS average_score
    FROM attempted a
    JOIN categories c ON a.category_id = c.uuid
    WHERE a.user_id = ?
    GROUP BY a.category_id, c.category
";

$stmt = $conn->prepare($competenceQuery);
if ($stmt) {
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $competenceList = [];
    $highestCompetence = null;

    while ($row = $result->fetch_assoc()) {
        $competence = [
            'category_name' => $row['category'],
            'average_score' => round($row['average_score'], 2)
        ];

        $competenceList[] = $competence;

        if (
            !$highestCompetence ||
            $competence['average_score'] > $highestCompetence['average_score']
        ) {
            $highestCompetence = $competence;
        }
    }

    http_response_code(200); // OK
    echo json_encode([
        'status' => 'success',
        'data' => [
            'competence_list' => $competenceList,
            'top_competence' => $highestCompetence
        ]
    ]);
    $stmt->close();
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare query']);
}
$conn->close();
?>