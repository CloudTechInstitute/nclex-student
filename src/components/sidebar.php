<aside class="w-64 h-screen p-5 flex flex-col justify-between bg-gray-900">
    <div>
        <h1 class="text-xl font-bold text-white">Global Nclex</h1>

        <nav class="mt-6">
            <a href="index.php" class="block font-semibold p-4 hover:bg-gray-800 text-white rounded">Dashboard</a>
            <a href="categories.php" class="block font-semibold p-4 hover:bg-gray-800 text-white rounded">Lessons</a>
            <a href="my-subscriptions.php" class="block font-semibold p-4 hover:bg-gray-800 text-white rounded">My
                Subscriptions</a>
            <a href="subscriptions.php"
                class="block font-semibold p-4 hover:bg-gray-800 text-white rounded">Subscriptions</a>
            <a href="tutorials.php" class="block font-semibold p-4 hover:bg-gray-800 text-white rounded">Tutorials</a>
            <a href="#" class="block font-semibold p-4 hover:bg-gray-800 text-white rounded">My Progress</a>
            <a href="quizzes.php" class="block font-semibold p-4 hover:bg-gray-800 text-white rounded">Quizzes</a>
            <a href="#" class="block font-semibold p-4 hover:bg-gray-800 text-white rounded">Assessments</a>
            <a href="settings.php" class="block font-semibold p-4 hover:bg-gray-800 text-white rounded">Settings</a>
        </nav>
    </div>
    <div class="p-4 border border-gray-700 rounded-lg bg-gray-800">
        <div class="flex justify-start gap-2 items-center mb-2">
            <div class="flex justify-start items-center">
                <div class="w-10 h-10 rounded-full bg-gray-400 flex justify-center items-center">
                    <i class="fa-solid fa-user text-white"></i>
                </div>
            </div>
            <div class="w-full flex justify-start">
                <p class="text-sm text-white"><?php echo $user; ?></p>
            </div>
        </div>
        <form method="post" action="logout.php">
            <button
                class="block w-full text-white hover:bg-red-500 bg-red-700 rounded-lg text-sm px-5 py-2.5 text-center "
                type="submit">
                Logout
            </button>
        </form>
    </div>
</aside>