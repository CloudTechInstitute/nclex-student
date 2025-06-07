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
}

?>

<body class="dark:bg-gray-800 bg-gray-200 dark:text-white">
    <div class=" flex h-screen">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php' ?>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Content Area -->
            <div class="px-6 py-6 flex justify-between items-end mb-4">
                <p class="uppercase text-blue-600 dark:text-green-600 font-bold text-xl">My subscriptions</p>

            </div>
            <div class="overflow-y-auto">
                <div class="px-6 mb-5">
                    <div class="border border-gray-500 ">
                        <?php include 'components/mySubscription-table.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script type="text/javascript" src="backend/js/fetchSubscription.js"></script>
<!-- <script type="text/javascript" src="backend/fetchEmployees.js"></script> -->

<?php include 'footer.php'; ?>