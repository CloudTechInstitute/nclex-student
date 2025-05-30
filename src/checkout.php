<?php
include 'head.php';
include 'backend/php/connection.php';
session_start();

if (!isset($_SESSION['LoggedStudent'])) {
    header('location:login.php');
    exit;
} else {
    $user = $_SESSION['LoggedStudent'];
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
    <div class=" flex h-screen">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php' ?>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col px-6">
            <!-- Content Area -->
            <div class="py-4 flex justify-between items-end mb-6">
            </div>


            <div class="[&::-webkit-scrollbar]:w-2
                                    [&::-webkit-scrollbar-track]:rounded-full
                                    [&::-webkit-scrollbar-track]:bg-gray-100
                                    [&::-webkit-scrollbar-thumb]:rounded-full
                                    [&::-webkit-scrollbar-thumb]:bg-gray-300
                                    dark:[&::-webkit-scrollbar-track]:bg-gray-700
                                    dark:[&::-webkit-scrollbar-thumb]:bg-gray-600 overflow-y-auto">
                <main class="">
                    <?php include('components/checkout-form.php'); ?>
                </main>
            </div>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const planSelectWrapper = document.getElementById("planSelectWrapper");
            const radioButtons = document.querySelectorAll('input[name="plans"]');

            function togglePlanSelect() {
                const selectedPlan = document.querySelector('input[name="plans"]:checked').value;
                planSelectWrapper.style.display = selectedPlan === "renewable" ? "block" : "none";
            }

            // Attach change event to all radio buttons
            radioButtons.forEach(radio => {
                radio.addEventListener("change", togglePlanSelect);
            });

            // Initial toggle on page load
            togglePlanSelect();
        });
    </script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script type="text/javascript" src="backend/js/payment.js"></script>

    <?php include 'footer.php'; ?>