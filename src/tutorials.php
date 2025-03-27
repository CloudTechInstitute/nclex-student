<?php include 'head.php';
include 'backend/php/connection.php';
session_start();
if (!isset($_SESSION['LoggedStudent'])) {
    header('location:login.php');
    exit;
} else {
    $user = $_SESSION['LoggedStudent'];
}

// Fetch all topics from tutorials table
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
                <p class="uppercase font-bold text-xl">tutorials</p>
                <div class="flex gap-4 justify-between">
                    <button data-modal-target="tutorial-modal" data-modal-toggle="tutorial-modal"
                        class="block text-white dark:text-gray-900 bg-blue-900 hover:bg-green-600 focus:ring-4 focus:outline-none focus:ring-blue-300  rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-400 dark:hover:bg-green-400 dark:focus:ring-green-600"
                        type="button">
                        New Tutorial
                    </button>
                </div>
            </div>
            <div class="mb-4 flex items-center gap-4">
                <div class="w-full">Select or search for any tutorial to start learning</div>
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
                                placeholder="Search tutorials..." required autocomplete="off" />
                            <button type="submit" id="searchTutorialButton"
                                class="text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-4 py-2 dark:bg-green-600 dark:hover:bg-green-700">Search</button>
                        </div>
                    </form>
                </div>
            </div>
            <hr class="border border-gray-400">
            <div class="overflow-y-auto mt-4">
                <main class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6" id="tutorialsDiv">


                </main>
            </div>
        </div>
    </div>
    </div>
    <!-- role modal -->
    <div id="tutorial-modal" tabindex="-1"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <?php include 'components/modals/tutorial-modal.php'; ?>
    </div>

    <script type="text/javascript" src="backend/js/create-tutorials.js"></script>
    <?php include 'footer.php'; ?>