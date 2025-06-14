<?php
require_once __DIR__ . '../../../../vendor/autoload.php';

use Ramsey\Uuid\Uuid;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

include "connection.php";
session_start();
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit();
}

$user_id = $_SESSION['studentID'];
$subscriber = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$amount = $_POST['amount'];
$product = $_POST['product'];
$productUUID = $_POST['product_uuid'];
$duration = $_POST['duration'];
$paystackRef = $_POST['paystack_ref'];

$subscriptionUUID = Uuid::uuid4()->toString();
$paymentUUID = Uuid::uuid4()->toString();
$paystackSecret = $_ENV['PAYSTACK_SECRET_KEY'];
$date = date('Y-m-d');

// Verify payment with Paystack API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/verify/" . $paystackRef);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $paystackSecret"]);
$response = curl_exec($ch);
curl_close($ch);

$paystackResponse = json_decode($response, true);

if ($paystackResponse['status'] && $paystackResponse['data']['status'] == "success") {
    $today = date('Y-m-d');
    $expiryDate = null;
    $existingSubscription = false;

    // Fetch quizzes and assessment from the products table using the product UUID
    $productQuery = "SELECT quizzes, assessment FROM products WHERE uuid = '$productUUID'";
    $productResult = mysqli_query($conn, $productQuery);

    if (!$productResult || mysqli_num_rows($productResult) === 0) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Product not found"]);
        exit();
    }

    $productData = mysqli_fetch_assoc($productResult);
    $productQuizzes = (int) $productData['quizzes'];
    $productAssessment = (int) $productData['assessment'];

    // Check for existing subscription
    $checkQuery = "SELECT id, expiry_date, quizzes, assessment FROM subscriptions WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 1";
    $checkResult = mysqli_query($conn, $checkQuery);

    if ($checkResult && mysqli_num_rows($checkResult) > 0) {
        $existing = mysqli_fetch_assoc($checkResult);
        $existingSubscription = true;
        $currentExpiry = $existing['expiry_date'];

        if (strtotime($currentExpiry) > strtotime($today)) {
            // Active subscription: extend from current expiry
            $expiryDate = date('Y-m-d', strtotime("+$duration days", strtotime($currentExpiry)));
        } else {
            // Expired subscription: start from today
            $expiryDate = date('Y-m-d', strtotime("+$duration days", strtotime($today)));
        }

        // Update quizzes and assessments
        $updatedQuizzes = (int) $existing['quizzes'] + $productQuizzes;
        $updatedAssessment = (int) $existing['assessment'] + $productAssessment;

        // Update the existing subscription
        $updateQuery = "UPDATE subscriptions SET 
                            expiry_date = '$expiryDate'
                        WHERE id = '{$existing['id']}'";

        $updateResult = mysqli_query($conn, $updateQuery);

        if (!$updateResult) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to update subscription: " . mysqli_error($conn)]);
            exit();
        }

    } else {
        // New subscription
        $expiryDate = date('Y-m-d', strtotime("+$duration days", strtotime($today)));

        $insertQuery = "INSERT INTO subscriptions(subscriber, user_id, email, phone, product_uuid, product, amount, duration, date_subscribed, expiry_date, subscription_id, reference, quizzes, assessment)
                        VALUES ('$subscriber', '$user_id', '$email', '$phone', '$productUUID', '$product', '$amount', '$duration', '$date', '$expiryDate', '$subscriptionUUID', '$paystackRef', '$productQuizzes', '$productAssessment')";

        if (!mysqli_query($conn, $insertQuery)) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to insert subscription: " . mysqli_error($conn)]);
            exit();
        }
    }

    // Always log payment
    $paymentQuery = "INSERT INTO payments (payment_uuid, subscriber, subscriber_email, subscriber_phone, package, amount, transaction_id, date)
                     VALUES ('$paymentUUID', '$subscriber', '$email', '$phone', '$product', '$amount', '$paystackRef', '$date')";

    if (mysqli_query($conn, $paymentQuery)) {
        $_SESSION['subscriptionStatus'] = "not expired";
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Subscription processed successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Failed to save payment record: " . mysqli_error($conn)]);
    }

} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Payment verification failed"]);
}
?>