<?php
include 'connection.php';
session_start();
header('Content-Type: application/json');

function set_status($code)
{
    http_response_code($code);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['studentID'])) {
        $userId = $_SESSION['studentID'];

        $response = [
            'status' => 'success',
            'data' => []
        ];

        // --- Pass Rate Calculation ---
        $passRateQuery = "SELECT correct_answers, total_questions FROM speedtest WHERE user_id = ?";
        $stmt = $conn->prepare($passRateQuery);

        if ($stmt) {
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            $totalCorrect = 0;
            $totalQuestions = 0;
            $attemptCount = 0;

            while ($row = $result->fetch_assoc()) {
                $attemptCount++;
                $totalCorrect += $row['correct_answers'];
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
            set_status(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare query for Pass Rate']);
            exit;
        }

        // --- Speed Rate Calculation ---
        $speedRateQuery = "SELECT time_taken, total_questions FROM speedtest WHERE user_id = ?";
        $stmt = $conn->prepare($speedRateQuery);

        if ($stmt) {
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            $totalTime = 0;
            $totalSpeed = 0;
            $attemptCount = 0;

            while ($row = $result->fetch_assoc()) {
                $attemptCount++;
                $timeTaken = $row['time_taken'];
                $totalQ = $row['total_questions'];
                $timePerQuestion = $totalQ > 0 ? $timeTaken / $totalQ : 0;

                $totalTime += $timeTaken;
                $totalSpeed += $timePerQuestion;
            }

            $averageSpeed = $attemptCount > 0 ? $totalSpeed / $attemptCount : 0;
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
            set_status(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare query for Speed Rate']);
            exit;
        }

        // --- Competitiveness Calculation ---
        $competitivenessQuery = "SELECT user_id, AVG((correct_answers / total_questions) * 100) AS avg_percentage FROM speedtest GROUP BY user_id ORDER BY avg_percentage DESC";
        $stmt = $conn->prepare($competitivenessQuery);

        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();

            $ranking = [];
            $rank = 1;
            $totalParticipants = $result->num_rows;

            while ($row = $result->fetch_assoc()) {
                $ranking[] = [
                    'user_id' => $row['user_id'],
                    'average_score' => round($row['avg_percentage'], 2),
                    'rank' => $rank++
                ];
            }

            $studentRank = null;
            foreach ($ranking as $rankData) {
                if ($rankData['user_id'] == $userId) {
                    $studentRank = $rankData;
                    break;
                }
            }

            if ($totalParticipants === 0 || !$studentRank) {
                $response['data']['competitiveness'] = [
                    'has_data' => false,
                    'message' => 'No speed tests taken yet. Complete one to get ranked.',
                    'rank_position' => 'take a speed test first',
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
            set_status(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare query for Competitiveness']);
            exit;
        }

        set_status(200);
        echo json_encode($response);
        $conn->close();
    } else {
        set_status(401);
        echo json_encode(['status' => 'error', 'message' => 'Student not logged in']);
    }
} else {
    set_status(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}