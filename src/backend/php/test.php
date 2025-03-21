<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['studentID']) || empty($_SESSION['studentID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Session studentID is missing']);
    exit();
}

if (!isset($_SESSION['loggedStudent']) || empty($_SESSION['loggedStudent'])) {
    echo json_encode(['status' => 'error', 'message' => 'Session loggedStudent is missing']);
    exit();
}

echo json_encode(['status' => 'success', 'message' => 'Session variables exist', 'studentID' => $_SESSION['studentID'], 'loggedStudent' => $_SESSION['loggedStudent']]);
?>