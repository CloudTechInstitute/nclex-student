<?php include 'head.php';
include 'backend/php/connection.php';
session_start();
if (!isset($_SESSION['LoggedStudent']) || !isset($_SESSION['studentID'])) {
    header('location:login.php');
    exit;
} else {
    $user = $_SESSION['LoggedStudent'];
    $userID = $_SESSION['studentID'];
    $subscription = $_SESSION['subscriptionStatus'];
    if ($subscription == "expired" || $subscription == "no subscription") {
        header('location:subscriptions.php');
        exit;
    }
}

?>

<body class="dark:bg-gray-800 bg-gray-200 dark:text-white">
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
        <div class="flex-1 flex flex-col w-full overflow-y-auto">
            <!-- Content Area -->
            <div class="px-6 py-2 lg:py-4 md:flex justify-between mt-5 lg:mt-0 items-end mb-8">
                <p class="uppercase text-blue-600 dark:text-green-600 font-bold text-xl">My subscriptions</p>

            </div>
            <div class="overflow-y-auto">
                <div class="px-6 mb-5">
                    <div class="w-full">
                        <div class="border border-gray-500 overflow-x-auto">
                            <?php include 'components/mySubscription-table.php'; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
<script type="text/javascript" src="backend/js/fetchSubscription.js"></script>
<!-- <script type="text/javascript" src="backend/fetchEmployees.js"></script> -->

<?php include 'footer.php'; ?>