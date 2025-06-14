<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 w-64 transform -translate-x-full lg:translate-x-0 lg:static lg:flex h-screen p-5 flex-col justify-between bg-gray-900 transition-transform duration-300 ease-in-out">

    <!-- Close button inside sidebar (only visible on mobile) -->
    <div class="flex justify-end mb-4 lg:hidden">
        <button onclick="toggleSidebar()" class="text-white">
            <!-- X Icon -->
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
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
                <div class="w-5 h-5 mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor"
                        class="w-5 h-5 text-blue-400 dark:text-green-600">
                        <path
                            d="M0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm320 96c0-26.9-16.5-49.9-40-59.3L280 88c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 204.7c-23.5 9.5-40 32.5-40 59.3c0 35.3 28.7 64 64 64s64-28.7 64-64zM144 176a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm-16 80a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zm288 32a32 32 0 1 0 0-64 32 32 0 1 0 0 64zM400 144a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z" />
                    </svg>
                </div>
                Dashboard
            </a>


            <?php if ($subscription === 'not expired'): ?>
                <!-- Links visible only to active subscribers -->
                <a href="categories.php" class="flex items-center font-semibold p-3 hover:bg-gray-800 text-white rounded">
                    <div class="w-5 h-5 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor"
                            class="w-5 h-5 text-blue-400 dark:text-green-600"
                            class="w-5 h-5 text-blue-400 dark:text-green-600">
                            <path
                                d="M160 96a96 96 0 1 1 192 0A96 96 0 1 1 160 96zm80 152l0 264-48.4-24.2c-20.9-10.4-43.5-17-66.8-19.3l-96-9.6C12.5 457.2 0 443.5 0 427L0 224c0-17.7 14.3-32 32-32l30.3 0c63.6 0 125.6 19.6 177.7 56zm32 264l0-264c52.1-36.4 114.1-56 177.7-56l30.3 0c17.7 0 32 14.3 32 32l0 203c0 16.4-12.5 30.2-28.8 31.8l-96 9.6c-23.2 2.3-45.9 8.9-66.8 19.3L272 512z" />
                        </svg>
                    </div>
                    Lessons
                </a>


                <a href="my-subscriptions.php"
                    class="flex items-center font-semibold p-3 hover:bg-gray-800 text-white rounded">
                    <div class="w-5 h-5 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor"
                            class="w-5 h-5 text-blue-400 dark:text-green-600">
                            <path
                                d="M0 96C0 78.3 14.3 64 32 64H544c17.7 0 32 14.3 32 32V160H0V96zM0 192H576V416c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32V192zm96 64a16 16 0 1 0 0 32 16 16 0 1 0 0-32zm64 0a16 16 0 1 0 0 32 16 16 0 1 0 0-32z" />
                        </svg>
                    </div>
                    My Subscriptions
                </a>


                <a href="subscriptions.php"
                    class="flex items-center font-semibold p-3 hover:bg-gray-800 text-white rounded">
                    <div class="w-5 h-5 mr-3">
                        <!-- Font Awesome Credit Card Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor"
                            class="w-5 h-5 text-blue-400 dark:text-green-600">
                            <path
                                d="M0 96C0 78.3 14.3 64 32 64H544c17.7 0 32 14.3 32 32V160H0V96zM0 192H576V416c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32V192zm96 64a16 16 0 1 0 0 32 16 16 0 1 0 0-32zm64 0a16 16 0 1 0 0 32 16 16 0 1 0 0-32z" />
                        </svg>
                    </div>
                    Subscriptions
                </a>

                <a href="tutorials.php" class="flex items-center font-semibold p-3 hover:bg-gray-800 text-white rounded">
                    <div class="w-5 h-5 mr-3">
                        <!-- Font Awesome Chalkboard Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" fill="currentColor"
                            class="w-5 h-5 text-blue-400 dark:text-green-600">
                            <path
                                d="M64 64C46.3 64 32 78.3 32 96v288c0 17.7 14.3 32 32 32h224v32H224c-8.8 0-16 7.2-16 16s7.2 16 16 16h192c8.8 0 16-7.2 16-16s-7.2-16-16-16H352V416h224c17.7 0 32-14.3 32-32V96c0-17.7-14.3-32-32-32H64zm0-32H576c35.3 0 64 28.7 64 64V384c0 35.3-28.7 64-64 64H448v32h48c8.8 0 16 7.2 16 16s-7.2 16-16 16H144c-8.8 0-16-7.2-16-16s7.2-16 16-16h48V448H64c-35.3 0-64-28.7-64-64V96C0 60.7 28.7 32 64 32z" />
                        </svg>
                    </div>
                    Tutorials
                </a>


                <a href="quizzes.php" class="flex items-center font-semibold p-3 hover:bg-gray-800 text-white rounded">
                    <div class="w-5 h-5 mr-3">
                        <!-- Font Awesome Question Circle Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor"
                            class="w-5 h-5 text-blue-400 dark:text-green-600">
                            <path
                                d="M256 0C114.62 0 0 114.6 0 256s114.62 256 256 256 256-114.6 256-256S397.38 0 256 0zM280 400c0 13.3-10.7 24-24 24s-24-10.7-24-24 10.7-24 24-24 24 10.7 24 24zm37.4-165.3-21.6 18.2C281.9 264.3 272 277.4 272 304h-32v-8c0-17 8.8-33.1 23.4-44.2l30.6-25.8c9.4-7.9 14-19.6 12.3-31.2-2.6-17.2-17.5-31.4-36.5-32.6-22.2-1.4-41.2 14.9-42.7 37.1l-31.8-2c2.8-41.8 37.5-73.4 80.7-71 36.4 2.2 67.1 29.7 71.9 65.9 2.6 19.7-5.1 39.4-20.5 52.1z" />
                        </svg>
                    </div>
                    Quizzes
                </a>


                <a href="assessments.php" class="flex items-center font-semibold p-3 hover:bg-gray-800 text-white rounded">
                    <div class="w-5 h-5 mr-3">
                        <!-- Font Awesome Clipboard List Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" fill="currentColor"
                            class="w-5 h-5 text-blue-400 dark:text-green-600">
                            <path d="M336 64h-80c0-35.3-28.7-64-64-64s-64 28.7-64 64H48C21.5 64 0 85.5 0 112v352c0 
                26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zM192 
                32c17.7 0 32 14.3 32 32s-14.3 32-32 32-32-14.3-32-32 14.3-32 32-32zM128 
                416c-8.8 0-16-7.2-16-16s7.2-16 16-16h128c8.8 0 16 7.2 16 16s-7.2 16-16 
                16H128zm0-96c-8.8 0-16-7.2-16-16s7.2-16 16-16h128c8.8 0 16 7.2 16 
                16s-7.2 16-16 16H128zm0-96c-8.8 0-16-7.2-16-16s7.2-16 16-16h128c8.8 0 
                16 7.2 16 16s-7.2 16-16 16H128z" />
                        </svg>
                    </div>
                    Assessments / Tests
                </a>


            <?php else: ?>
                <!-- Links visible only to inactive or no subscription -->
                <a href="subscriptions.php"
                    class="flex items-center font-semibold p-3 hover:bg-gray-800 text-white rounded">
                    <div class="w-5 h-5 mr-3">
                        <!-- Font Awesome Credit Card Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor"
                            class="w-5 h-5 text-blue-400 dark:text-green-600">
                            <path
                                d="M0 96C0 78.3 14.3 64 32 64H544c17.7 0 32 14.3 32 32V160H0V96zM0 192H576V416c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32V192zm96 64a16 16 0 1 0 0 32 16 16 0 1 0 0-32zm64 0a16 16 0 1 0 0 32 16 16 0 1 0 0-32z" />
                        </svg>
                    </div>
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