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

<body class="dark:bg-gray-800 bg-gray-200 dark:text-white">
    <div class=" flex h-screen">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php' ?>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col px-6 ">
            <!-- Content Area -->
            <div class="py-2 flex justify-between items-end mb-6">
                <p class="uppercase font-bold text-xl"></p>
            </div>
            <div class="flex justify-between items-center">
                <button id="generateBtn"
                    class="block text-white dark:text-gray-900 bg-blue-900 hover:bg-green-600 focus:ring-4 focus:outline-none focus:ring-blue-300  rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-400 dark:hover:bg-green-400 dark:focus:ring-green-600"
                    type="button">
                    Start Test
                </button>

                <div id="countdownTimer" class="text-red-500 font-semibold text-xl"
                    data-duration="<?php echo (int) $category['quizDuration']; ?>">
                    Loading timer...
                </div>
            </div>

            <div class="[&::-webkit-scrollbar]:w-2
                                    [&::-webkit-scrollbar-track]:rounded-full
                                    [&::-webkit-scrollbar-track]:bg-gray-100
                                    [&::-webkit-scrollbar-thumb]:rounded-full
                                    [&::-webkit-scrollbar-thumb]:bg-gray-300
                                    dark:[&::-webkit-scrollbar-track]:bg-gray-700
                                    dark:[&::-webkit-scrollbar-thumb]:bg-gray-600 overflow-y-auto">
                <main class="m-2">
                    <div class="mb-2 border-b border-gray-200 dark:border-gray-700">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-styled-tab"
                            data-tabs-toggle="#default-styled-tab-content"
                            data-tabs-active-classes="text-blue-600 hover:text-blue-600 dark:text-green-500 dark:hover:text-green-500 border-blue-600 dark:border-green-500"
                            data-tabs-inactive-classes="dark:border-transparent text-gray-500 hover:text-gray-600 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300"
                            role="tablist">

                            <li class="me-2" role="presentation">
                                <button
                                    class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                    id="settings-styled-tab" data-tabs-target="#styled-settings" type="button"
                                    role="tab" aria-controls="settings" aria-selected="false">Comprehensive Assessment
                                    Test</button>
                            </li>

                        </ul>
                    </div>
                    <div id="default-styled-tab-content">
                        <div class="hidden p-4 rounded-lg bg-gray-100 border border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                            id="styled-dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                            <div class="">
                                <div class="flex gap-6 h-[530px] max-h-[530px]">
                                    <div class="border-b border-gray-200 dark:border-gray-700 w-[30%] [&::-webkit-scrollbar]:w-2
                                    [&::-webkit-scrollbar-track]:rounded-full
                                    [&::-webkit-scrollbar-track]:bg-gray-100
                                    [&::-webkit-scrollbar-thumb]:rounded-full
                                    [&::-webkit-scrollbar-thumb]:bg-gray-300
                                    dark:[&::-webkit-scrollbar-track]:bg-gray-700
                                    dark:[&::-webkit-scrollbar-thumb]:bg-gray-600 overflow-y-auto">
                                        <div id="videoIcons">
                                            <ul class="text-sm font-medium text-center m-4" id="default-tab"
                                                data-tabs-toggle="#default-tab-content" role="tablist">

                                            </ul>
                                        </div>
                                    </div>
                                    <div id="default-tab-content" class="w-[70%]">

                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- practice test tab -->
                        <form method="post">
                            <div class="hidden p-4 rounded-lg bg-gray-100 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 h-[520px]"
                                id="styled-settings" role="tabpanel" aria-labelledby="settings-tab">
                                <div class="flex gap-6 h-500px] max-h-[450px]">
                                    <div class="w-full [&::-webkit-scrollbar]:w-2
                                    [&::-webkit-scrollbar-track]:rounded-full
                                    [&::-webkit-scrollbar-track]:bg-gray-100
                                    [&::-webkit-scrollbar-thumb]:rounded-full
                                    [&::-webkit-scrollbar-thumb]:bg-gray-300
                                    dark:[&::-webkit-scrollbar-track]:bg-gray-700
                                    dark:[&::-webkit-scrollbar-thumb]:bg-gray-600 overflow-y-auto">
                                        <div class="mr-2 h-[445px]" id="mock-container">
                                            <!-- display fetched questions -->
                                        </div>
                                    </div>

                                    <div class="w-full ">
                                        <div class="w-full p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-transparent dark:border-gray-600"
                                            id="solutionBox">

                                        </div>
                                    </div>
                                </div>
                                <div class="flex gap-6 justify-end items-center">
                                    <div class="flex justify-start gap-5 w-full">
                                        <button id="prevBtn"
                                            class="block text-white dark:text-gray-900 bg-blue-900 hover:bg-green-600 focus:ring-4 focus:outline-none focus:ring-blue-300  rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-400 dark:hover:bg-green-400 dark:focus:ring-green-600"
                                            type="button">
                                            Previous
                                        </button>
                                        <button id="nextBtn"
                                            class="block text-white dark:text-gray-900 bg-blue-900 hover:bg-green-600 focus:ring-4 focus:outline-none focus:ring-blue-300  rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-400 dark:hover:bg-green-400 dark:focus:ring-green-600"
                                            type="button">
                                            Next
                                        </button>
                                    </div>
                                    <div class="w-full">
                                        <button id="submitBtn"
                                            class="block text-white dark:text-gray-900 bg-blue-900 hover:bg-blue-800 rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-400 dark:hover:bg-green-500"
                                            type="button">
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </main>
            </div>
        </div>
    </div>
    </div>

    <script type="text/javascript" src="backend/js/fetch-mock-test.js"></script>

    <?php include 'footer.php'; ?>