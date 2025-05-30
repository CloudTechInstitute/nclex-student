<?php include 'head.php'; ?>
<?php include 'backend/php/connection.php'; // Include database connection ?>

<body class="">

    <div
        class="w-full p-4 flex justify-center items-center h-screen bg-white sm:p-6 md:p-8 dark:bg-gray-900 dark:border-gray-700">
        <form class="border border-gray-300 dark:border-lime-800 p-10 space-y-6 w-full rounded-lg max-w-lg mx-auto"
            action="#" id="loginForm" method="post">
            <div class="flex justify-center w-full">
                <!-- Light mode logo -->
                <img src="images/logo-full.png" alt="Light Logo" class="w-64 block dark:hidden">

                <!-- Dark mode logo -->
                <img src="images/full-logo.png" alt="Dark Logo" class="w-64 hidden dark:block">
            </div>
            <h5 class="text-3xl text-center font-medium text-gray-900 dark:text-white">Sign In</h5>

            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                <input type="email" name="email" id="email"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                    placeholder="Enter your email" required />
            </div>

            <div>
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your
                    password</label>
                <input type="password" name="password" id="password" placeholder="••••••••"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                    required />
            </div>

            <button type="submit" id="loginBtn"
                class="w-full text-white dark:text-gray-800 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-lime-500 dark:hover:bg-lime-500 dark:focus:ring-lime-500">
                Login
            </button>
            <div class="dark:text-white text-gray-800">
                <a href="register.php">Don't have an account? <span
                        class="font-bold text-blue-600 dark:text-lime-400">Register</span></a>
            </div>

        </form>
    </div>

    <script type="text/javascript" src="backend/js/login.js"></script>
    <?php include 'footer.php'; ?>