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

?>

<body class="dark:bg-gray-800 dark:text-white bg-gray-200">
    <div class=" flex h-screen">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php' ?>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col px-6 ">
            <!-- Content Area -->
            <div class="py-10 flex justify-between items-end">
                <p class="uppercase font-bold text-xl text-blue-600 dark:text-green-600">lesson area</p>
            </div>
            <div class="mb-4 flex items-center gap-4">
                <div class="w-full">What category are we learning today?</div>
                <div class="w-full">
                    <form class="max-w-md mx-auto" method="post">
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input type="search" id="default-search"
                                class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search categories, topics..." required autocomplete="off" />
                            <button type="submit" id="searchButton"
                                class="text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Search</button>
                        </div>
                    </form>
                </div>
            </div>
            <hr class="border border-gray-400">
            <div class="overflow-y-auto mt-4">
                <main class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6" id="categoriesDiv">


                </main>
            </div>
        </div>
    </div>
    </div>


    <!-- 
    <script type="text/javascript" src="backend/js-functions.js"></script>
    <script type="text/javascript" src="backend/fetchEmployees.js"></script> -->
    <script type="text/javascript" src="backend/js/dashboardCards.js"></script>

    <?php include 'footer.php'; ?>