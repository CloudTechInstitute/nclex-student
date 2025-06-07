<?php
include 'connection.php';
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['studentID'])) {
        $userId = $_SESSION['studentID'];

        $response = [
            'status' => 'success',
            'data' => []
        ];

        // --- Pass Rate Calculation ---
        $passRateQuery = "SELECT mock_uuid, user_score, total_questions FROM completed_mock WHERE user_id = ?";
        $stmt = $conn->prepare($passRateQuery);

        if ($stmt) {
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            $totalCorrect = 0;
            $totalQuestions = 0;
            $mockCount = 0;

            while ($row = $result->fetch_assoc()) {
                $mockCount++;
                $totalCorrect += $row['user_score'];
                $totalQuestions += $row['total_questions'];
            }

            $passRate = $totalQuestions > 0 ? ($totalCorrect / $totalQuestions) * 100 : 0;
            $passGrade = $passRate >= 80 ? 'Pass' : 'Fail';

            $response['data']['pass_rate'] = [
                'average_score' => round($passRate) . "%",
                'grade' => $passGrade
            ];

            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare query for Pass Rate']);
            exit;
        }

        // --- Speed Rate Calculation ---
        $speedRateQuery = "SELECT mock_uuid, time_taken, total_questions FROM completed_mock WHERE user_id = ?";
        $stmt = $conn->prepare($speedRateQuery);

        if ($stmt) {
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            $totalTime = 0;
            $totalSpeed = 0;
            $mockCount = 0;

            while ($row = $result->fetch_assoc()) {
                $mockCount++;
                $timeTaken = $row['time_taken'];
                $totalQ = $row['total_questions'];
                $timePerQuestion = $totalQ > 0 ? $timeTaken / $totalQ : 0;

                $totalTime += $timeTaken;
                $totalSpeed += $timePerQuestion;
            }

            $averageSpeed = $mockCount > 0 ? $totalSpeed / $mockCount : 0;
            $criticalSpeed = $totalQuestions > 0 ? $totalTime / $totalQuestions : 0;

            $benchmarkSpeed = 10;
            $speedPercentage = $averageSpeed > 0 ? ($benchmarkSpeed / $averageSpeed) * 100 : 0;

            $response['data']['speed_rate'] = [
                'average_speed' => round($averageSpeed, 2),
                'critical_speed' => round($criticalSpeed, 2),
                'average_speed_percentage' => round($speedPercentage, 2) . '%'
            ];

            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare query for Speed Rate']);
            exit;
        }

        // --- Competitiveness Calculation ---
        $competitivenessQuery = "SELECT user_id, AVG(user_score / total_questions) * 100 AS average_percentage_score FROM completed_mock GROUP BY user_id ORDER BY average_percentage_score DESC";
        $stmt = $conn->prepare($competitivenessQuery);

        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();

            $competitivenessRanking = [];
            $rank = 1;
            $totalParticipants = $result->num_rows;

            while ($row = $result->fetch_assoc()) {
                $competitivenessRanking[] = [
                    'user_id' => $row['user_id'],
                    'average_score' => round($row['average_percentage_score'], 2),
                    'rank' => $rank++
                ];
            }

            $studentRank = null;
            foreach ($competitivenessRanking as $rankData) {
                if ($rankData['user_id'] == $userId) {
                    $studentRank = $rankData;
                    break;
                }
            }

            if ($totalParticipants === 0 || !$studentRank) {
                $response['data']['competitiveness'] = [
                    'has_data' => false,
                    'message' => 'No mocks taken yet. Complete a mock to get ranked.',
                    'rank_position' => 'take a mock first',
                    'rank_percentage' => 0,
                    'average_score' => 0
                ];
            } else {
                $rankPercentage = (($totalParticipants - $studentRank['rank'] + 1) / $totalParticipants) * 100;
                $rankPosition = $studentRank['rank'] . ' of ' . $totalParticipants;

                $response['data']['competitiveness'] = [
                    'has_data' => true,
                    'rank_position' => $rankPosition,
                    'rank_percentage' => round($rankPercentage, 2) . '%',
                    'average_score' => round($studentRank['average_score'], 2) . '%'
                ];
            }

            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare query for Competitiveness']);
            exit;
        }

        http_response_code(200);
        echo json_encode($response);
        $conn->close();
    } else {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Student not logged in']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}