<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../'); // Path to your .env file
$dotenv->load();

header('Content-Type: application/json');

if (!isset($_ENV['PAYSTACK_PUBLIC_KEY'])) {
    echo json_encode(['error' => 'Public key not set']);
    exit;
}

echo json_encode([
    'key' => $_ENV['PAYSTACK_PUBLIC_KEY']
]);