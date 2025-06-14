<?php include 'head.php';
include 'backend/php/connection.php';
session_start();
if (!isset($_SESSION['LoggedStudent'])) {
    header('location:login.php');
    exit;
} else {
    $user = $_SESSION['LoggedStudent'];
    $subscription = $_SESSION['subscriptionStatus'];
}

?>

<body class="dark:bg-gray-800 dark:text-white bg-gray-200">
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
    <div class=" flex h-screen mt-12 lg:mt-0">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php' ?>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col px-6 md:px-4">
            <!-- Content Area -->
            <div class="py-4 flex justify-between items-end">
                <p class="uppercase text-blue-600 dark:text-green-600 font-bold text-xl">dashboard</p>

            </div>

            <div class="overflow-y-auto">
                <div class="flex gap-6 mb-4">
                    <main class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4 gap-4 w-full">
                        <?php include 'components/student-card.php'; ?>
                    </main>
                </div>
                <div class="xl:flex gap-4 mb-2">
                    <div class="bg-white dark:bg-gray-700 shadow-sm rounded-lg p-4 w-full mb-4 xl:mb-0">
                        <p class="text-xl text-blue-600 dark:text-green-600 font-bold">Quiz</p>
                        <hr class="mb-4">
                        <?php include 'components/progress.php'; ?>
                    </div>
                    <div class="bg-white dark:bg-gray-700 shadow-sm rounded-lg p-4 w-full mb-4 xl:mb-0">
                        <p class="text-xl text-blue-600 dark:text-green-600 font-bold">Mock</p>
                        <hr class="mb-4">
                        <?php include 'components/mock-progress.php'; ?>
                    </div>
                    <div class="bg-white dark:bg-gray-700 shadow-sm rounded-lg p-4 w-full mb-4 xl:mb-0">
                        <p class="text-xl text-blue-600 dark:text-green-600 font-bold">Speed test</p>
                        <hr class="mb-4">
                        <?php include 'components/speed-progress.php'; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>

    <script type="text/javascript" src="backend/js/student-card.js"></script>
    <script type="text/javascript" src="backend/js/fetch-student-perfomance.js"></script>
    <script type="text/javascript" src="backend/js/fetch-mock-performance.js"></script>
    <script type="text/javascript" src="backend/js/fetch-speedTest-stats.js"></script>

    <?php include 'footer.php'; ?>