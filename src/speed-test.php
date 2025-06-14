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
    <!-- Mobile Sidebar Toggle Button -->
    <header
        class="p-4 bg-gray-900 text-white flex items-center justify-between lg:hidden fixed top-0 left-0 right-0 z-50">
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
        <div class="flex-1 flex flex-col px-2 lg:px-6 ">
            <!-- Content Area -->
            <div class="lg:py-4 mt-20 lg:mt-5 flex justify-between items-end lg:mb-10 px-2 lg:px-0">
                <p class="uppercase text-blue-600 dark:text-green-600 font-bold text-xl">speed test</p>

            </div>
            <div class="lg:mb-4 flex items-center gap-4 px-2 lg:px-0">
                <div class="w-full text-xs md:text-sm">Examine questions carefully and attempt all questions. All the
                    best</div>
            </div>

            <div class="mt-4">
                <main class="md:flex gap-6 m-2 h-[530px] max-h-[530px] ">
                    <div class="mb-4 md:mb-0 w-full" id="quiz-container">

                    </div>
                    <div class="dark:bg-gray-900 bg-white p-4 rounded-lg shadow-lg border border-gray-300 dark:border-gray-700 space-y-4 w-full"
                        id="result-container">
                        Result will display here after test</div>
                </main>
            </div>
        </div>
    </div>
    </div>

    <script type="text/javascript" src="backend/js/fetch-speedTest.js"></script>
    <?php include 'footer.php'; ?>