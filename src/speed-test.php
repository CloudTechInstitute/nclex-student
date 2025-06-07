<?php include 'head.php';
include 'backend/php/connection.php';
session_start();
if (!isset($_SESSION['LoggedStudent'])) {
    header('location:login.php');
    exit;
} else {
    $user = $_SESSION['LoggedStudent'];
    $subscription = $_SESSION['subscriptionStatus'];
    if ($subscription == "expired" || $subscription == "no subscription") {
        header('location:subscriptions.php');
        exit;
    }
}

// Fetch all topics from quizs table
$query = "SELECT id, category FROM categories";
$result = $conn->query($query);

$categoryTopics = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categoryTopics[] = $row;
    }
}

?>

<body class="dark:bg-gray-800 bg-gray-200 dark:text-white">
    <div class=" flex h-screen">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php' ?>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col px-6 ">
            <!-- Content Area -->
            <div class="py-4 flex justify-between items-end mb-10">
                <p class="uppercase text-blue-600 dark:text-green-600 font-bold text-xl">speed test</p>

            </div>
            <div class="mb-4 flex items-center gap-4">
                <div class="w-full">Examine questions carefully and attempt all questions. All the best</div>
            </div>

            <div class="overflow-y-auto mt-4">
                <main class="grid grid-cols-3 gap-6 m-2 h-[530px] max-h-[530px] overflow-y-auto">
                    <div class="col-span-2" id="quiz-container">

                    </div>
                    <div class="dark:bg-gray-900 bg-white p-4 rounded-lg shadow-lg border border-gray-300 dark:border-gray-700 space-y-4"
                        id="result-container"></div>
                </main>
            </div>
        </div>
    </div>
    </div>

    <script type="text/javascript" src="backend/js/fetch-speedTest.js"></script>
    <?php include 'footer.php'; ?>