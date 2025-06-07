<aside class="w-64 h-screen p-5 flex flex-col justify-between bg-gray-900">

    <div>
        <div class="flex items-center justify-start">
            <!-- <h1 class="text-xl font-bold text-white">Global Nclex</h1> -->
            <div class="flex justify-center w-full">
                <!-- Dark mode logo -->
                <img src="images/full-logo.png" alt="Dark Logo" class="w-48">
            </div>

        </div>
        <nav class="mt-6 space-y-1">

            <a href="index.php" class="flex items-center font-semibold p-3 hover:bg-gray-800 text-white rounded">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m-4 0h8" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                Dashboard
            </a>

            <?php if ($subscription === 'not expired'): ?>
                <!-- Links visible only to active subscribers -->
                <a href="categories.php" class="flex items-center font-semibold p-3 hover:bg-gray-800 text-white rounded">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Lessons
                </a>

                <a href="my-subscriptions.php"
                    class="flex items-center font-semibold p-3 hover:bg-gray-800 text-white rounded">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    My Subscriptions
                </a>

                <a href="subscriptions.php"
                    class="flex items-center font-semibold p-3 hover:bg-gray-800 text-white rounded">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M7 8h10M7 12h4m1 8a9 9 0 100-18 9 9 0 000 18z" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    Subscriptions
                </a>

                <a href="tutorials.php" class="flex items-center font-semibold p-3 hover:bg-gray-800 text-white rounded">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 20l9-5-9-5-9 5 9 5z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M12 12V4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Tutorials
                </a>

                <a href="quizzes.php" class="flex items-center font-semibold p-3 hover:bg-gray-800 text-white rounded">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 20h9M3 20h3m6-16v6l4 2" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    Quizzes
                </a>

                <a href="assessments.php" class="flex items-center font-semibold p-3 hover:bg-gray-800 text-white rounded">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4M12 20h9M3 20h3m6-16v6l4 2" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    Assessments / Tests
                </a>

            <?php else: ?>
                <!-- Links visible only to inactive or no subscription -->
                <a href="subscriptions.php"
                    class="flex items-center font-semibold p-3 hover:bg-gray-800 text-white rounded">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M7 8h10M7 12h4m1 8a9 9 0 100-18 9 9 0 000 18z" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    Subscriptions
                </a>
            <?php endif; ?>

        </nav>
    </div>

    <div class="p-3 border border-gray-700 rounded-lg bg-gray-800 mt-6">
        <div class="flex justify-start gap-2 items-center mb-2">
            <div class="w-10 h-10 rounded-full bg-gray-400 flex justify-center items-center">
                <i class="fa-solid fa-user text-white"></i>
            </div>
            <p class="text-sm text-white"><?php echo htmlspecialchars($user); ?></p>
        </div>
        <form method="post" action="logout.php">
            <button
                class="block w-full text-white hover:bg-red-500 bg-red-700 rounded-lg text-sm px-5 py-2.5 text-center"
                type="submit">
                Logout
            </button>
        </form>
    </div>

</aside>