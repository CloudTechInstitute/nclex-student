<?php
require_once __DIR__ . '../../../../vendor/autoload.php';

use Ramsey\Uuid\Uuid;
include "connection.php"; // Include DB connection

header("Content-Type: application/json"); // Ensure JSON response

// Check if request is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit();
}


$subscriber = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$amount = $_POST['amount'];
$product = $_POST['product'];
$productUUID = $_POST['product_id'];
$duration = $_POST['duration'];
$paystackRef = $_POST['paystack_ref'];
$plan = $_POST['plans'];
$subscriptionUUID = Uuid::uuid4()->toString();
// Paystack Secret Key (Replace with your secret key)
$paystackSecret = "sk_test_3711c9146136a7d643c65bc2ba54d5e574301af9";
// Get current date
$date = date('Y-m-d');

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
    $query = "INSERT INTO subscriptions(subscriber, email, phone, product_uuid, product, amount, duration, date_subscribed, expiry_date, subscription_id, reference, plan) VALUES ('$subscriber', '$email', '$phone', '$productUUID', '$product', '$amount', '$duration', '$date', '$expiryDate', '$subscriptionUUID', '$paystackRef', '$plan')";

    if (mysqli_query($conn, $query)) {
        echo json_encode(["status" => "success", "message" => "Payment processed successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error: " . mysqli_error($conn)]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Payment verification failed"]);
}
?>