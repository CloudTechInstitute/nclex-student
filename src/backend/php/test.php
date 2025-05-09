<?php
include 'connection.php';
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['studentID'])) {
        $userId = $_SESSION['studentID'];

        // Initialize response array
        $response = [
            'status' => 'success',
            'data' => []
        ];

        // Pass Rate Calculation (Total correct answers / Total questions)
        $passRateQuery = "SELECT quiz_id, correct_answers, total_questions FROM quiz_results WHERE user_id = ?";
        $stmt = $conn->prepare($passRateQuery);
        if ($stmt) {
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            $totalCorrect = 0;
            $totalQuestions = 0;
            $quizResults = [];
            while ($row = $result->fetch_assoc()) {
                $quizResults[] = $row;
                $totalCorrect += $row['correct_answers'];
                $totalQuestions += $row['total_questions'];
            }

            $passRate = ($totalQuestions > 0) ? ($totalCorrect / $totalQuestions) * 100 : 0;
            $passGrade = $passRate >= 50 ? 'Pass' : 'Fail';  // Grade assignment
            $response['data']['pass_rate'] = [
                'score' => round($passRate, 2),
                'grade' => $passGrade,
                'quiz_results' => $quizResults
            ];

            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare query for Pass Rate']);
            exit;
        }

        // Speed Rate Calculation (average time per question)
        $speedRateQuery = "SELECT quiz_id, time_taken, total_questions FROM quiz_results WHERE user_id = ?";
        $stmt = $conn->prepare($speedRateQuery);
        if ($stmt) {
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            $totalTime = 0;
            $totalSpeed = 0;
            $speedDetails = [];
            while ($row = $result->fetch_assoc()) {
                $speedDetails[] = $row;
                $totalTime += $row['time_taken'];
                $totalSpeed += $row['time_taken'] / $row['total_questions'];
            }

            $averageSpeed = (count($speedDetails) > 0) ? $totalSpeed / count($speedDetails) : 0;
            $criticalSpeed = ($totalTime > 0) ? $totalTime / $totalQuestions : 0;  // Assuming critical speed depends on total time
            $response['data']['speed_rate'] = [
                'average_speed' => round($averageSpeed, 2),
                'critical_speed' => round($criticalSpeed, 2),
                'speed_details' => $speedDetails
            ];

            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare query for Speed Rate']);
            exit;
        }

        // Competence Calculation (average score per subject area)
        $competenceQuery = "SELECT subject_area, AVG(score) AS average_score FROM quiz_results WHERE user_id = ? GROUP BY subject_area";
        $stmt = $conn->prepare($competenceQuery);
        if ($stmt) {
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            $competenceDetails = [];
            while ($row = $result->fetch_assoc()) {
                $competenceDetails[] = $row;
            }

            $response['data']['competence'] = $competenceDetails;

            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare query for Competence']);
            exit;
        }

        // Competitiveness Calculation (ranking against other students)
        $competitivenessQuery = "SELECT user_id, AVG(score) AS average_score FROM quiz_results GROUP BY user_id ORDER BY average_score DESC";
        $stmt = $conn->prepare($competitivenessQuery);
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();

            $competitivenessRanking = [];
            $rank = 1;
            while ($row = $result->fetch_assoc()) {
                $competitivenessRanking[] = [
                    'user_id' => $row['user_id'],
                    'average_score' => $row['average_score'],
                    'rank' => $rank++
                ];
            }

            // Find student's ranking
            $studentRank = null;
            foreach ($competitivenessRanking as $rankData) {
                if ($rankData['user_id'] == $userId) {
                    $studentRank = $rankData;
                    break;
                }
            }

            $response['data']['competitiveness'] = $studentRank ? $studentRank : ['rank' => 'N/A', 'average_score' => 0];

            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare query for Competitiveness']);
            exit;
        }

        echo json_encode($response);
        $conn->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Student not logged in']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>