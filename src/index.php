<?php include 'head.php';
include 'backend/php/connection.php';
session_start();
if (!isset($_SESSION['LoggedStudent'])) {
    header('location:login.php');
    exit;
} else {
    $user = $_SESSION['LoggedStudent'];
    // $role = $_SESSION['UserRole'];
}

?>

<body class="dark:bg-gray-800 dark:text-white bg-gray-200">
    <div class=" flex h-screen">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php' ?>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col px-6 ">
            <!-- Content Area -->
            <div class="py-4 flex justify-between items-end">
                <p class="uppercase text-blue-600 font-bold text-xl">dashboard</p>
            </div>

            <div class="overflow-y-auto">
                <div class="flex gap-6 mb-4">
                    <main class="grid grid-cols-1 md:grid-cols-2 gap-2 w-full">
                        <?php include 'components/student-card.php'; ?>
                    </main>
                    <div class="bg-white dark:bg-gray-700 shadow-sm rounded-lg p-4 w-full">
                        <?php include 'components/progress.php'; ?>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="bg-white dark:bg-gray-700 shadow-sm rounded-lg p-4 w-[70%]">

                    </div>
                    <div class="bg-white dark:bg-gray-700 shadow-sm rounded-lg p-4 w-[30%]">

                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>



    <script type="text/javascript" src="backend/js/dashboardCards.js"></script>
    <script type="text/javascript" src="backend/js/student-card.js"></script>

    <?php include 'footer.php'; ?>