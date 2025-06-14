<?php
include 'head.php';
include 'backend/php/connection.php';
session_start();

if (!isset($_SESSION['LoggedStudent'])) {
    header('location:login.php');
    exit;
} else {
    $user = $_SESSION['LoggedStudent'];
    $subscription = $_SESSION['subscriptionStatus'];
}

$nameParts = explode(" ", $user);
$firstname = $nameParts[0];
$lastname = $nameParts[1];

if (isset($_GET['product_uuid'])) {
    $id = ($_GET['product_uuid']);


    $stmt = $conn->prepare("SELECT * FROM products WHERE uuid = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $product_result = $stmt->get_result();


    $stmt = $conn->prepare("SELECT * FROM students WHERE firstname = ? AND lastname = ?");
    $stmt->bind_param("ss", $firstname, $lastname);
    $stmt->execute();
    $student_result = $stmt->get_result();

    if ($product_result->num_rows > 0 && $student_result->num_rows > 0) {
        $product = $product_result->fetch_assoc();
        $student = $student_result->fetch_assoc();
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


<body class="dark:bg-gray-800 dark:text-white">
    <header class="p-4 bg-gray-900 text-white flex items-center justify-between lg:hidden">
        <button onclick="toggleSidebar()" class="text-white">
            <!-- Hamburger Icon -->
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <span class="font-semibold text-lg">Global Nclex</span>

    </header>
    <div class=" flex h-screen">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php' ?>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col px-6">
            <!-- Content Area -->
            <div class="md:py-4 flex justify-between items-end mb-2">
            </div>


            <div class="">
                <main class="">
                    <?php include('components/checkout-form.php'); ?>
                </main>
            </div>
        </div>
    </div>
    </div>


    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script type="text/javascript" src="backend/js/payment.js"></script>

    <?php include 'footer.php'; ?>