<?php include 'head.php';
include 'backend/php/connection.php';
session_start();
if (!isset($_SESSION['LoggedStudent'])) {
    header('location:login.php');
    exit;
} else {
    $user = $_SESSION['LoggedStudent'];
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
    <div class=" flex h-screen">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php' ?>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col px-6 ">
            <!-- Content Area -->
            <div class="py-4 flex justify-between items-end mb-10">
                <p class="uppercase text-blue-600 dark:text-green-600 font-bold text-xl">mock exams</p>

            </div>
            <div class="mb-4 flex items-center gap-4">
                <div class="w-full">Examine questions carefully and attempt all questions. All the best</div>
            </div>

            <div class="overflow-y-auto mt-4">
                <main class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">

                </main>
            </div>
        </div>
    </div>
    </div>

    <!-- <script type="text/javascript" src="backend/js/create-quiz.js"></script> -->
    <?php include 'footer.php'; ?>