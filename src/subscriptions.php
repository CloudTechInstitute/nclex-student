<?php include 'head.php';
include 'backend/php/connection.php';
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
            <div class="px-6 py-6 flex justify-between items-end mb-5">
                <p class="uppercase font-bold text-xl">Subscriptions</p>
            </div>
            <div class="">
                <div class="px-6 mb-5">
                    <p class="mb-3">Select the package that works for you.</p>
                    <hr>
                    <div class="overflow-y-auto max-h-[550px] mt-4 px-4 [&::-webkit-scrollbar]:w-2
                                    [&::-webkit-scrollbar-track]:rounded-full
                                    [&::-webkit-scrollbar-track]:bg-gray-100
                                    [&::-webkit-scrollbar-thumb]:rounded-full
                                    [&::-webkit-scrollbar-thumb]:bg-gray-300
                                    dark:[&::-webkit-scrollbar-track]:bg-green-900
                                    dark:[&::-webkit-scrollbar-thumb]:bg-green-600">
                        <div class="mt-3 grid grid-cols-4 gap-4 ">
                            <?php
                            // Fetch all product details
                            $stmt = $conn->prepare("SELECT * FROM products");
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                $products = $result->fetch_all(MYSQLI_ASSOC);
                            } else {
                                echo "<p class='text-center text-red-500'>Products not found!</p>";
                                exit();
                            }

                            $stmt->close();

                            // Loop through each product and display it
                            foreach ($products as $product):
                                ?>
                            <div
                                class="w-full p-4 bg-white border-2 border-blue-400 rounded-lg shadow-lg sm:p-8 dark:bg-gray-700 dark:border-green-400">
                                <form method="GET" action="checkout.php">
                                    <input type='hidden' name='product_id'
                                        value='<?php echo htmlspecialchars($product['uuid']); ?>' />

                                    <h5 class="mb-4 text-sm text-center rounded-full font-medium dark:text-green-600">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </h5>
                                    <div class="flex items-stretch text-gray-900 dark:text-white justify-center">
                                        <span class="text-2xl text-blue-600 dark:text-green-500">$</span>
                                        <span
                                            class="text-5xl font-semibold text-blue-600 dark:text-green-500 tracking-tight">
                                            <?php echo htmlspecialchars($product['cost']); ?>
                                        </span>
                                    </div>
                                    <div class="text-base text-blue-600 dark:text-white tracking-tight text-center">
                                        <?php echo htmlspecialchars($product['duration']); ?> days Access
                                    </div>
                                    <ul role="list" class="space-y-3 my-4">
                                        <li class="flex items-center">
                                            <svg class="shrink-0 w-4 h-4 text-blue-700 dark:text-green-500"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                            </svg>
                                            <span
                                                class="font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3 text-xs">
                                                2,300+ Practice Questions
                                            </span>
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="shrink-0 w-4 h-4 text-blue-700 dark:text-green-500"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                            </svg>
                                            <span
                                                class="font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3 text-xs">
                                                500+ NGN Questions
                                            </span>
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="shrink-0 w-4 h-4 text-blue-700 dark:text-green-500"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                            </svg>
                                            <span
                                                class="font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3 text-xs">
                                                High Yield Review Videos
                                            </span>
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="shrink-0 w-4 h-4 text-blue-700 dark:text-green-500"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                            </svg>
                                            <span
                                                class="font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3 text-xs">
                                                <?php echo htmlspecialchars($product['quizzes']); ?> Quizzes
                                            </span>
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="shrink-0 w-4 h-4 text-blue-700 dark:text-green-500"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                            </svg>
                                            <span
                                                class="font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3 text-xs">
                                                <?php echo htmlspecialchars($product['speedTest']); ?> Speed Tests
                                            </span>
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="shrink-0 w-4 h-4 text-blue-700 dark:text-green-500"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                            </svg>
                                            <span
                                                class="font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3 text-xs">
                                                <?php echo htmlspecialchars($product['assessment']); ?> Self Assessments
                                                Tests
                                            </span>
                                        </li>
                                    </ul>
                                    <button type="submit"
                                        class="text-white dark:text-gray-900 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-200 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900 font-medium rounded-lg text-sm px-5 py-2.5 inline-flex justify-center w-full text-center">
                                        Choose plan
                                    </button>
                                </form>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <script type="text/javascript" src="backend/js-functions.js"></script>
    <script type="text/javascript" src="backend/fetchEmployees.js"></script> -->

    <?php include 'footer.php'; ?>