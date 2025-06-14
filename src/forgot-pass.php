<?php include 'head.php';
include 'backend/php/connection.php';
session_start();
if (isset($_SESSION['LoggedStudent'])) {
    header('location:index.php');
    exit;
}

?>


<body class="bg-[url('images/bg-img.webp')] bg-cover bg-center overflow-hidden">

    <div
        class="w-full p-4 flex justify-center items-center h-screen bg-white/80 sm:p-6 md:p-8 dark:bg-blue-950/90 dark:border-lime-400">

        <form
            class="border-2 border-blue-600 dark:border-lime-800 p-10 space-y-4 w-full rounded-lg max-w-lg mx-auto bg-white/70 dark:bg-blue-950/70 shadow-lg"
            action="" id="forgotPassForm" method="post">
            <div class="flex justify-center w-full">
                <!-- Light mode logo -->
                <img src="images/logo-full.png" alt="Light Logo" class="w-64 block dark:hidden">

                <!-- Dark mode logo -->
                <img src="images/full-logo.png" alt="Dark Logo" class="w-64 hidden dark:block">
            </div>
            <!-- <h5 class="text-2xl text-center font-medium text-gray-900 dark:text-white">Forgot Password</h5> -->

            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                <input type="email" name="email" id="email"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-transparent dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                    placeholder="Enter the email you used for registration" required />
            </div>



            <button type="submit" id="forgotPassBtn"
                class="w-full text-white dark:text-gray-800 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-lime-500 dark:hover:bg-lime-500 dark:focus:ring-lime-500">
                Submit
            </button>
            <div>
                <div class="dark:text-white text-gray-800 text-xs">
                    <a href="login.php">Remembered your password? <span
                            class="text-md text-blue-600 dark:text-lime-400">Login</span></a>
                </div>

            </div>

        </form>
    </div>
    <div>powered by me</div>

    <script type="text/javascript" src="backend/js/forgot-password.js"></script>
    <?php include 'footer.php'; ?>