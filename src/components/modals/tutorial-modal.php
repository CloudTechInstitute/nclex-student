<div class="relative p-4 w-full max-w-md max-h-full">
    <!-- Modal content -->
    <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
        <!-- Modal header -->
        <div
            class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Create New Tutorial
            </h3>
            <button type="button"
                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                data-modal-toggle="tutorial-modal">
                <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
        </div>
        <!-- Modal body -->
        <form class="p-4 md:p-5" id="tutorialForm" method="post">
            <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                    <label for="role"
                        class="block mb-2 text-xs uppercase font-medium text-gray-900 dark:text-gray-300">Tutorial
                        title</label>
                    <input type="text" name="title" id="title"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="enter tutorial title" required>
                </div>
                <div class="col-span-2">
                    <label for="status"
                        class="block mb-2 text-xs uppercase font-medium text-gray-900 dark:text-gray-300">Quiz
                        Status</label>
                    <select id="tutStatus" name="tutStatus"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        <option hidden selected disabled>Choose one</option>
                        <option value="notScheduled">Not Scheduled</option>
                        <option value="scheduled">Scheduled</option>
                    </select>
                </div>
                <!-- This wrapper will be hidden or shown -->
                <div id="tutscheduleFields" class="col-span-2 grid grid-cols-2 gap-4 hidden">
                    <div class="col-span-1">
                        <label for="tutorialDate"
                            class="block mb-2 text-xs uppercase font-medium text-gray-900 dark:text-gray-300">Tutorial
                            Date</label>
                        <input type="date" id="tutorialDate" name="tutorialDate"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                    </div>
                    <div class="col-span-1">
                        <label for="tutorialTime"
                            class="block mb-2 text-xs uppercase font-medium text-gray-900 dark:text-gray-300">Tutorial
                            Time</label>
                        <input type="time" id="tutorialTime" name="tutorialTime"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                    </div>
                </div>
                <div class="col-span-2 space-y-3">
                    <label for="description"
                        class="block mb-2 text-xs uppercase font-medium text-gray-900 dark:text-gray-300">
                        Select Topics
                    </label>
                    <?php foreach ($categoryTopics as $topic): ?>
                        <div class="flex items-center gap-2 h-5">
                            <input type="checkbox" name="topics[]" value="<?= htmlspecialchars($topic['category']) ?>"
                                class="w-4 h-4 border border-gray-300 rounded-sm bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-green-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800" />
                            <?= htmlspecialchars($topic['category']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="submit"
                class="inline-flex font-bold text-white dark:text-gray-900 bg-blue-900 hover:bg-green-600 focus:ring-4 focus:outline-none focus:ring-blue-300  rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-400 dark:hover:bg-green-400 dark:focus:ring-green-600">
                <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                        clip-rule="evenodd"></path>
                </svg>
                Add Tutorial
            </button>
        </form>
    </div>
</div>