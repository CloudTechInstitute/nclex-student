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
            class="border-2 border-blue-600 dark:border-lime-800 p-10 space-y-4 w-full rounded-lg max-w-2xl mx-auto bg-white/70 dark:bg-blue-950/70 shadow-lg"
            action="#" id="registerForm" method="post">
            <h5 class="text-3xl text-center font-medium text-gray-900 dark:text-white">Create Account</h5>
            <div class="flex gap-4 w-full">
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white">First
                        name</label>
                    <input type="text" name="fname" id="fname"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-transparent dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        required />
                </div>
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white">Last
                        name</label>
                    <input type="text" name="lname" id="lname"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-transparent dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        required />
                </div>
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white">Other
                        names</label>
                    <input type="text" name="othername" id="othername"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-transparent dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" />
                </div>
            </div>
            <div class="flex gap-4 w-full">
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white">Email</label>
                    <input type="email" name="email" id="email"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-transparent dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        placeholder="eg. me@nclex.com" required />
                </div>

            </div>

            <div class="flex gap-4">
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white">Country</label>
                    <input type="text" name="country" id="country"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-transparent dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        required />
                </div>
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white">Phone
                        number</label>
                    <input type="text" name="phone" id="phone"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-transparent dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        required />
                </div>
            </div>
            <div class="flex gap-4">
                <div class="w-full">
                    <label for="password" class="block text-sm font-medium text-gray-900 dark:text-white">
                        Password</label>
                    <input type="password" name="password" id="password" placeholder="••••••••"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-transparent dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        required />
                </div>
                <div class="w-full">
                    <label for="cpassword" class="block text-sm font-medium text-gray-900 dark:text-white">Confirm
                        password</label>
                    <input type="password" name="cpassword" id="cpassword" placeholder="••••••••"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-transparent dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        required />
                </div>
            </div>
            <button type="submit" id="registerBtn"
                class="w-full text-white dark:text-gray-800 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-lime-500 dark:hover:bg-lime-500 dark:focus:ring-lime-500">
                Create Account
            </button>
            <div class="dark:text-white text-gray-800">
                <a href="login.php">Already a member? <span
                        class="font-bold text-blue-600 dark:text-lime-400">Login</span></a>
            </div>
        </form>
    </div>

    <script type="text/javascript" src="backend/js/register.js"></script>
    <?php include 'footer.php'; ?>