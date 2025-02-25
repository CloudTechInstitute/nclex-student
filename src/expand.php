<?php include 'head.php';
include 'backend/connection.php';
session_start();
if (!isset($_SESSION['LoggedStudent'])) {
    header('location:login.php');
    exit;
} else {
    $user = $_SESSION['LoggedStudent'];
}
// Check if ID is provided in the URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Convert to integer to prevent SQL injection

    // Prepare SQL query to fetch data
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
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
        <div class="flex-1 flex flex-col px-6 ">
            <!-- Content Area -->
            <div class="py-4 flex justify-between items-end mb-6">
                <p class="uppercase font-bold text-xl">dashboard</p>
            </div>
            <div class="">
                <div><?php echo htmlspecialchars($category['category']) . ' '; ?> Category</div>
            </div>

            <div class="[&::-webkit-scrollbar]:w-2
                                    [&::-webkit-scrollbar-track]:rounded-full
                                    [&::-webkit-scrollbar-track]:bg-gray-100
                                    [&::-webkit-scrollbar-thumb]:rounded-full
                                    [&::-webkit-scrollbar-thumb]:bg-gray-300
                                    dark:[&::-webkit-scrollbar-track]:bg-gray-700
                                    dark:[&::-webkit-scrollbar-thumb]:bg-gray-600 overflow-y-auto">
                <main class="m-2">
                    <div class="mb-2 border-b border-gray-200 dark:border-gray-700">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-styled-tab"
                            data-tabs-toggle="#default-styled-tab-content"
                            data-tabs-active-classes="text-blue-600 hover:text-blue-600 dark:text-green-500 dark:hover:text-green-500 border-blue-600 dark:border-green-500"
                            data-tabs-inactive-classes="dark:border-transparent text-gray-500 hover:text-gray-600 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300"
                            role="tablist">
                            <li class="me-2" role="presentation">
                                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-styled-tab"
                                    data-tabs-target="#styled-profile" type="button" role="tab" aria-controls="profile"
                                    aria-selected="false">Lessons (20)</button>
                            </li>
                            <li class="me-2" role="presentation">
                                <button
                                    class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                    id="dashboard-styled-tab" data-tabs-target="#styled-dashboard" type="button"
                                    role="tab" aria-controls="dashboard" aria-selected="false">Videos (12)</button>
                            </li>
                            <li class="me-2" role="presentation">
                                <button
                                    class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                    id="settings-styled-tab" data-tabs-target="#styled-settings" type="button"
                                    role="tab" aria-controls="settings" aria-selected="false">Practice Test</button>
                            </li>

                        </ul>
                    </div>
                    <div id="default-styled-tab-content">
                        <div class="hidden p-4 rounded-lg bg-gray-100 border border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                            id="styled-profile" role="tabpanel" aria-labelledby="profile-tab">
                            <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder content the
                                <strong class="font-medium text-gray-800 dark:text-white">Profile tab's associated
                                    content</strong>. Clicking another tab will toggle the visibility of this one for
                                the next. The tab JavaScript swaps classes to control the content visibility and
                                styling.
                            </p>
                        </div>
                        <div class="hidden p-4 rounded-lg bg-gray-100 border border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                            id="styled-dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                            <div class="flex gap-6 h-[470px] max-h-[470px] ">
                                <div class="mb-4 border-b border-gray-200 dark:border-gray-700 w-[30%] [&::-webkit-scrollbar]:w-2
                                    [&::-webkit-scrollbar-track]:rounded-full
                                    [&::-webkit-scrollbar-track]:bg-gray-100
                                    [&::-webkit-scrollbar-thumb]:rounded-full
                                    [&::-webkit-scrollbar-thumb]:bg-gray-300
                                    dark:[&::-webkit-scrollbar-track]:bg-gray-700
                                    dark:[&::-webkit-scrollbar-thumb]:bg-gray-600 overflow-y-auto">
                                    <ul class="text-sm font-medium text-center m-4" id="default-tab"
                                        data-tabs-toggle="#default-tab-content" role="tablist">
                                        <li class="border border-gray-300 mb-3 rounded-lg" role="presentation">
                                            <button class="w-full p-4 items-center" id="profile-tab"
                                                data-tabs-target="#profile" type="button" role="tab"
                                                aria-controls="profile" aria-selected="false">
                                                <div class="w-full h-36 rounded-md overflow-hidden mb-2">
                                                    <img src="images/thumb1.jpg" alt="Profile"
                                                        class="w-full h-full object-cover">
                                                </div>
                                                <span class="block text-lg font-semibold text-white">Abdominal
                                                    Hernia</span>
                                                <span class="block text-gray-400">Description</span>
                                            </button>
                                        </li>
                                        <li class="border border-gray-300 mb-3 rounded-lg" role="presentation">
                                            <button class="w-full p-4 items-center" id="dashboard-tab"
                                                data-tabs-target="#dashboard" type="button" role="tab"
                                                aria-controls="dashboard" aria-selected="false">
                                                <div class="w-full h-36 rounded-md overflow-hidden mb-2">
                                                    <img src="images/thumb1.jpg" alt="Profile"
                                                        class="w-full h-full object-cover">
                                                </div>
                                                <span>Dashboard</span>
                                            </button>
                                        </li>
                                        <li class="border border-gray-300 mb-3 rounded-lg" role="presentation">
                                            <button class="w-full p-4 items-center" id="settings-tab"
                                                data-tabs-target="#settings" type="button" role="tab"
                                                aria-controls="settings" aria-selected="false">
                                                <div class="w-full h-36 rounded-md overflow-hidden mb-2">
                                                    <img src="images/thumb1.jpg" alt="Profile"
                                                        class="w-full h-full object-cover">
                                                </div>
                                                <span>Settings</span>
                                            </button>
                                        </li>
                                        <li class="border border-gray-300 mb-3 rounded-lg" role="presentation">
                                            <button class="w-full p-4 items-center" id="contacts-tab"
                                                data-tabs-target="#contacts" type="button" role="tab"
                                                aria-controls="contacts" aria-selected="false">
                                                <div class="w-full h-36 rounded-md overflow-hidden mb-2">
                                                    <img src="images/thumb1.jpg" alt="Profile"
                                                        class="w-full h-full object-cover">
                                                </div>
                                                <span>Contacts</span>
                                            </button>
                                        </li>

                                    </ul>
                                </div>
                                <div id="default-tab-content" class="w-[70%]">
                                    <div class="hidden p-1 rounded-lg bg-gray-50 dark:bg-gray-800 " id="profile"
                                        role="tabpanel" aria-labelledby="profile-tab">
                                        <video class="w-full rounded-lg bg-gray-50 dark:bg-gray-800" playsinline
                                            controls controlsList="nodownload" oncontextmenu="return false;">
                                            <source src="videos/drone.mp4" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="dashboard"
                                        role="tabpanel" aria-labelledby="dashboard-tab">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder
                                            content
                                            the <strong class="font-medium text-gray-800 dark:text-white">Dashboard
                                                tab's
                                                associated content</strong>. Clicking another tab will toggle the
                                            visibility
                                            of this one for the next. The tab JavaScript swaps classes to control the
                                            content visibility and styling.</p>
                                    </div>
                                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="settings"
                                        role="tabpanel" aria-labelledby="settings-tab">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder
                                            content
                                            the <strong class="font-medium text-gray-800 dark:text-white">Settings tab's
                                                associated content</strong>. Clicking another tab will toggle the
                                            visibility
                                            of this one for the next. The tab JavaScript swaps classes to control the
                                            content visibility and styling.</p>
                                    </div>
                                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="contacts"
                                        role="tabpanel" aria-labelledby="contacts-tab">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder
                                            content
                                            the <strong class="font-medium text-gray-800 dark:text-white">Contacts tab's
                                                associated content</strong>. Clicking another tab will toggle the
                                            visibility
                                            of this one for the next. The tab JavaScript swaps classes to control the
                                            content visibility and styling.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- practice test tab -->
                        <div class="hidden p-4 rounded-lg bg-gray-100 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 h-[520px]"
                            id="styled-settings" role="tabpanel" aria-labelledby="settings-tab">
                            <div class="flex gap-6 h-500px] max-h-[450px]">
                                <div class="w-full [&::-webkit-scrollbar]:w-2
                                    [&::-webkit-scrollbar-track]:rounded-full
                                    [&::-webkit-scrollbar-track]:bg-gray-100
                                    [&::-webkit-scrollbar-thumb]:rounded-full
                                    [&::-webkit-scrollbar-thumb]:bg-gray-300
                                    dark:[&::-webkit-scrollbar-track]:bg-gray-700
                                    dark:[&::-webkit-scrollbar-thumb]:bg-gray-600 overflow-y-auto">
                                    <div class="mr-2 h-[445px]" id="questionsDisplay">

                                    </div>
                                </div>

                                <div class="w-full ">
                                    <div
                                        class="w-full p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-transparent dark:border-gray-600">

                                        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Here is the
                                            solution</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-6 justify-end items-center">
                                <div class="flex justify-start gap-5 w-full">
                                    <button id="prevBtn"
                                        class="block text-white dark:text-gray-900 bg-blue-900 hover:bg-green-600 focus:ring-4 focus:outline-none focus:ring-blue-300  rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-400 dark:hover:bg-green-400 dark:focus:ring-green-600"
                                        type="button">
                                        Previous
                                    </button>
                                    <button id="nextBtn"
                                        class="block text-white dark:text-gray-900 bg-blue-900 hover:bg-green-600 focus:ring-4 focus:outline-none focus:ring-blue-300  rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-400 dark:hover:bg-green-400 dark:focus:ring-green-600"
                                        type="button">
                                        Next
                                    </button>
                                </div>
                                <div class="w-full">
                                    <button id="submitBtn"
                                        class="block text-white dark:text-gray-900 bg-blue-900 hover:bg-green-600 focus:ring-4 focus:outline-none focus:ring-blue-300  rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-400 dark:hover:bg-green-400 dark:focus:ring-green-600"
                                        type="button">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
    </div>


    <!-- 
    <script type="text/javascript" src="backend/js-functions.js"></script>-->
    <script type="text/javascript" src="backend/fetch-question.js"></script>
    <!-- <script type="text/javascript" src="backend/dashboardCards.js"></script> -->

    <?php include 'footer.php'; ?>