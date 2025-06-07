<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

header('Content-Type: application/json');

if (!isset($_ENV['PAYSTACK_PUBLIC_KEY'])) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Public key not set']);
    exit;
}

http_response_code(200); // OK
echo json_encode([
    'key' => $_ENV['PAYSTACK_PUBLIC_KEY']
]);