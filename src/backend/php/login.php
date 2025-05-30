<?php
include 'connection.php';
header("Content-Type: application/json");
$response = array();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Fill in all required fields!!']);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM students WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            function getUserIP()
            {
                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                    return $_SERVER['HTTP_CLIENT_IP'];
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
                } else {
                    return $_SERVER['REMOTE_ADDR'];
                }
            }

            $ip = getUserIP();
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $deviceHash = hash('sha256', $ip . $userAgent);
            $userID = $user['uuid'];

            // Check existing device count
            $stmt = $conn->prepare("SELECT COUNT(*) FROM user_devices WHERE user_id = ?");
            $stmt->bind_param("s", $userID);
            $stmt->execute();
            $stmt->bind_result($deviceCount);
            $stmt->fetch();
            $stmt->close();

            if ($deviceCount >= 2) {
                // Check if current device is already registered
                $stmt = $conn->prepare("SELECT id FROM user_devices WHERE user_id = ? AND device_hash = ?");
                $stmt->bind_param("ss", $userID, $deviceHash);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows === 0) {
                    echo json_encode(['status' => 'error', 'message' => 'Device limit reached. You can only login from 2 devices.']);
                    $stmt->close();
                    $conn->close();
                    exit;
                }

                $stmt->close();
            } else {
                // Register this device
                $stmt = $conn->prepare("INSERT INTO user_devices (user_id, device_hash, ip_address, user_agent) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $userID, $deviceHash, $ip, $userAgent);
                $stmt->execute();
                $stmt->close();
            }

            session_start();
            $_SESSION['LoggedStudent'] = $user['firstname'] . " " . $user["lastname"];
            $_SESSION['studentID'] = $user['uuid'];
            unset($user['password']);

            // Check if the user has a subscription
            $subStmt = $conn->prepare("SELECT product_uuid, expiry_date FROM subscriptions WHERE user_id = ? LIMIT 1");
            $subStmt->bind_param("s", $userID);
            $subStmt->execute();
            $subResult = $subStmt->get_result();

            if ($subResult->num_rows > 0) {
                $subscription = $subResult->fetch_assoc();
                $expiryDate = new DateTime($subscription['expiry_date']);
                $currentDate = new DateTime();

                if ($currentDate > $expiryDate) {
                    // Subscription exists but expired
                    $_SESSION['subscriptionStatus'] = 'expired';
                    $response["subscriptionStatus"] = 'expired';
                } else {
                    // Active subscription
                    $_SESSION['subscriptionStatus'] = 'not expired';
                    $_SESSION['product_uuid'] = $subscription['product_uuid'];
                    $response["subscriptionStatus"] = 'not expired';
                    $response["product_uuid"] = $subscription['product_uuid'];
                }
            } else {
                // No subscription at all
                $_SESSION['subscriptionStatus'] = 'no subscription';
                $response["subscriptionStatus"] = 'no subscription';
            }


            $subStmt->close();

            $response["status"] = "success";
            $response["message"] = "Login Successful";
            $response["user"] = $user;

        } else {
            $response["status"] = "error";
            $response["message"] = "Invalid username or password. Please try again.";
        }
    } else {
        $response["status"] = "error";
        $response["message"] = "User not found. Please try again.";
    }

} else {
    $response["status"] = "error";
    $response["message"] = "Request method not allowed.";
}

$conn->close();
echo json_encode($response);
?>