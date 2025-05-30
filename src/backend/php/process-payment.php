<?php
require_once __DIR__ . '../../../../vendor/autoload.php';

use Ramsey\Uuid\Uuid;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

include "connection.php"; // Include DB connection
session_start();
header("Content-Type: application/json"); // Ensure JSON response

// Check if request is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
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
$plan = $_POST['plans'];
$subscriptionUUID = Uuid::uuid4()->toString();
$paystackSecret = $_ENV['PAYSTACK_SECRET_KEY'];
$date = date('Y-m-d');

$paystackPublicKey = $_ENV['PAYSTACK_PUBLIC_KEY'];

// Calculate expiry date
$expiryDate = date('Y-m-d', strtotime("+$duration days", strtotime($date)));

// Verify payment with Paystack API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/verify/" . $paystackRef);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $paystackSecret"]);
$response = curl_exec($ch);
curl_close($ch);

$paystackResponse = json_decode($response, true);

if ($paystackResponse['status'] && $paystackResponse['data']['status'] == "success") {
    // Insert data into database
    $query = "INSERT INTO subscriptions(subscriber, user_id, email, phone, product_uuid, product, amount, duration, date_subscribed, expiry_date, subscription_id, reference, plan) VALUES ('$subscriber', '$user_id', '$email', '$phone', '$productUUID', '$product', '$amount', '$duration', '$date', '$expiryDate', '$subscriptionUUID', '$paystackRef', '$plan')";

    if (mysqli_query($conn, $query)) {

        echo json_encode(["status" => "success", "message" => "Payment processed successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error: " . mysqli_error($conn)]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Payment verification failed"]);
}
?>