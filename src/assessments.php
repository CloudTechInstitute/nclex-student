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
    <header class="p-4 bg-gray-900 text-white flex items-center justify-between lg:hidden">
        <button onclick="toggleSidebar()" class="text-white">
            <!-- Hamburger Icon -->
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <span class="font-semibold text-lg">Global Nclex</span>

    </header>
    <div class=" flex h-screen">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php' ?>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col px-6 ">
            <!-- Content Area -->
            <div class="py-4 flex justify-between items-end mb-4">
                <p class="uppercase text-blue-600 dark:text-green-600 font-bold text-xl">assessments</p>

            </div>

            <div class="overflow-y-auto mt-4">
                <main class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
                    <a href="speed-test.php">
                        <div
                            class="w-full max-w-sm bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-green-600 dark:border-green-400">
                            <div class="flex justify-center items-center p-10">
                                <h5 class="mb-1 text-md font-normal text-blue-600 dark:text-white">Speed Test</h5>
                            </div>
                        </div>
                    </a>
                    <a href="mock.php">
                        <div
                            class="w-full max-w-sm bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-green-600 dark:border-green-400">
                            <div class="flex justify-center items-center p-10">
                                <h5 class="mb-1 text-md font-normal text-blue-600 dark:text-white">Assessment Test</h5>
                            </div>
                        </div>
                    </a>
                </main>
            </div>
        </div>
    </div>
    </div>

    <!-- <script type="text/javascript" src="backend/js/create-quiz.js"></script> -->
    <?php include 'footer.php'; ?>