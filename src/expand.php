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
// Check if ID is provided in the URL
if (isset($_GET['uuid'])) {
    $id = $_GET['uuid']; // Convert to integer to prevent SQL injection

    // Prepare SQL query to fetch data
    $stmt = $conn->prepare("SELECT * FROM categories WHERE uuid = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
    } else {
        echo "<p class='text-red-500'>No record found.</p>";
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<p class='text-red-500'>Invalid request.</p>";
    exit;
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
            <div class="uppercase">
                <div><?php echo htmlspecialchars($category['category']) . ' '; ?> Category</div>
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
                                    id="dashboard-styled-tab" data-tabs-target="#styled-dashboard" type="button"
                                    role="tab" aria-controls="dashboard" aria-selected="false">Videos (12)</button>
                            </li>
                            <li class="me-2" role="presentation">
                                <button
                                    class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                    id="settings-styled-tab" data-tabs-target="#styled-settings" type="button"
                                    role="tab" aria-controls="settings" aria-selected="false">Practice Test</button>
                            </li>
                            <li class="me-2" role="presentation">
                                <button
                                    class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                    id="settings-styled-tab" data-tabs-target="#styled-notes" type="button" role="tab"
                                    aria-controls="notes" aria-selected="false">Lesson Notes</button>
                            </li>

                        </ul>
                    </div>
                    <div id="default-styled-tab-content">
                        <!-- videos tab -->
                        <div class="hidden p-4 rounded-lg bg-gray-100 border border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                            id="styled-dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                            <div class="">
                                <div class="flex gap-6 h-[470px] max-h-[470px]">
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
                                <div class="p-2">
                                    <form method="post" id="notesForm">
                                        <label for="notes"
                                            class="block text-sm font-medium text-gray-600 dark:text-gray-400">Leave
                                            notes here for later</label>
                                        <div class="flex gap-4 items-center">
                                            <input type="text" id="notes" name="notes" required
                                                class="mt-1 block w-full p-2 border border-gray-300 rounded-lg shadow-sm dark:border-gray-600 dark:bg-gray-700">
                                            <button id="notesBtn"
                                                class="block text-white w-36 dark:text-gray-900 bg-blue-900 rounded-lg text-sm py-2 px-3 text-center dark:bg-green-400"
                                                type="submit" name="notesbtn">
                                                Submit
                                            </button>
                                        </div>
                                    </form>
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
                                        <div class="mr-2 h-[445px]" id="questionsDisplay">
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
                                            class="block text-white dark:text-gray-900 bg-blue-900 rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-400"
                                            type="button">
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="hidden p-4 rounded-lg bg-gray-100 border border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                            id="styled-notes" role="tabpanel" aria-labelledby="notes-tab">
                            <div id="notesDiv">

                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
    </div>

    <script type="text/javascript" src="backend/js/fetch-question.js"></script>
    <script type="text/javascript" src="backend/js/fetch-videos.js"></script>
    <script type="text/javascript" src="backend/js/fetch-notes.js"></script>

    <?php include 'footer.php'; ?>