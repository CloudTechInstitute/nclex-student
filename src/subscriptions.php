<?php include 'head.php';
include 'backend/connection.php';
session_start();
if (!isset($_SESSION['LoggedStudent'])) {
    header('location:login.php');
    exit;
} else {
    $user = $_SESSION['LoggedStudent'];
}

?>

<body class="dark:bg-gray-800 dark:text-white">
    <div class=" flex h-screen">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php' ?>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Content Area -->
            <div class="px-6 py-6 flex justify-between items-end mb-8">
                <p class="uppercase font-bold text-xl">Subscriptions</p>
            </div>
            <div class="overflow-y-auto">
                <div class="px-6 mb-5">
                    <p class="mb-3">Select the package that works for you.</p>
                    <hr>
                    <div class="mt-3">
                        <div
                            class="w-full max-w-xs p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
                            <h5 class="mb-4 text-xl font-medium text-gray-500 dark:text-gray-200">Standard plan</h5>
                            <div class="flex items-baseline text-gray-900 dark:text-white">
                                <span class="text-2xl text-green-500">$</span>
                                <span class="text-5xl text-green-500 tracking-tight">49</span>
                                <span class="ms-1 text-xl font-normal text-gray-500 dark:text-gray-400">/month</span>
                            </div>
                            <ul role="list" class="space-y-5 my-7">
                                <li class="flex items-center">
                                    <svg class="shrink-0 w-4 h-4 text-blue-700 dark:text-green-500" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                    </svg>
                                    <span
                                        class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3">2
                                        team members</span>
                                </li>
                                <li class="flex">
                                    <svg class="shrink-0 w-4 h-4 text-blue-700 dark:text-green-500" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                    </svg>
                                    <span
                                        class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3">20GB
                                        Cloud storage</span>
                                </li>
                                <li class="flex">
                                    <svg class="shrink-0 w-4 h-4 text-blue-700 dark:text-green-500" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                    </svg>
                                    <span
                                        class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3">Integration
                                        help</span>
                                </li>

                            </ul>
                            <button type="button"
                                class="text-white dark:text-gray-900 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-200 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900 font-medium rounded-lg text-sm px-5 py-2.5 inline-flex justify-center w-full text-center">Choose
                                plan</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="backend/js-functions.js"></script>
    <script type="text/javascript" src="backend/fetchEmployees.js"></script>

    <?php include 'footer.php'; ?>