<?php
include 'backend/php/connection.php';

$uuid = $_GET['uuid'];

$stmt = $conn->prepare("UPDATE students SET status = 'active' WHERE uuid = ?");
$stmt->bind_param("s", $uuid);
$stmt->execute();
$stmt->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Account Verified</title>
    <meta http-equiv="refresh" content="5;url=login.php">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="fontawesome/css/fontawesome.css" rel="stylesheet" />
    <link href="fontawesome/css/brands.css" rel="stylesheet" />
    <link href="fontawesome/css/solid.css" rel="stylesheet" />
    <link href="output.css" rel="stylesheet" />
    <link href="styles.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="flex items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900">
        <div class="p-8 text-center bg-white rounded-lg shadow dark:bg-gray-800 max-w-md w-full">
            <div
                class="w-16 h-16 rounded-full bg-green-100 dark:bg-green-900 p-3 flex items-center justify-center mx-auto mb-5">
                <svg aria-hidden="true" class="w-10 h-10 text-green-500 dark:text-green-400" fill="currentColor"
                    viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="sr-only">Success</span>
            </div>
            <p class="mb-6 text-xl font-semibold text-gray-900 dark:text-white">Your account has been verified!</p>
            <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm">You will be redirected in a few seconds...</p>
        </div>
    </div>
</body>

</html>