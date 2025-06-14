async function fetchStudentStats() {
  let packageCard = document.getElementById("packageCard");
  let quizCard = document.getElementById("quizCard");
  let assessmentCard = document.getElementById("assessmentCard");
  let tutorialCard = document.getElementById("tutorialCard");

  [packageCard, quizCard, assessmentCard, tutorialCard].forEach(showSkeleton);

  try {
    let response = await fetch("backend/php/student-fetch-scripts.php");
    if (!response.ok) throw new Error("Network response was not ok");

    let result = await response.json();

    if (result.status === "success") {
      const subscription = result.subscriptions?.[0] || {};

      const data = {
        product: subscription.product || "None",
        duration: subscription.duration || "0",
        dateSubscribed: subscription.date_subscribed || "N/A",
        dateExpired: subscription.expiry_date || "N/A",
        duration: subscription.duration || "0",
        packageCount: result.package_count || 0,
        quizCount: result.quiz_count || 0,
        quizCompleted: result.quiz_completed || 0,
        mockCount: result.mock_count || 0,
        mockCompleted: result.mock_completed || 0,
        tutorialCount: result.tutorial_count || 0,
        tutorialCompleted: result.tutorial_completed || 0,
      };

      displayDashboardStats(data);
    } else {
      showAllErrors(result.message);
    }
  } catch (error) {
    console.error("Fetch error:", error);
    showAllErrors("Failed to load data. Please try again later.");
  }

  function showAllErrors(message) {
    [packageCard, quizCard, assessmentCard, tutorialCard].forEach((card) =>
      showError(card, message)
    );
  }
}

function displayDashboardStats({
  product,
  // duration,
  dateSubscribed,
  dateExpired,
  quizCount,
  quizCompleted,
  mockCount,
  mockCompleted,
  tutorialCount,
  tutorialCompleted = 0,
}) {
  // Calculate days left
  let daysLeft = "N/A";
  if (dateSubscribed !== "N/A" && dateExpired !== "N/A") {
    const subDate = new Date(dateSubscribed);
    const expDate = new Date(dateExpired);
    const diffTime = expDate - new Date();
    daysLeft =
      diffTime > 0 ? Math.ceil(diffTime / (1000 * 60 * 60 * 24)) : "expired";
  }

  document.getElementById("packageCard").innerHTML = `
    <p class="mb-2 uppercase font-bold">Subscription</p>
    <hr>
    <div class="flex gap-4 mt-2">
        <div class="text-center">
            <p class="text-sm">Package</p>
            <h5 class="mb-2 md:text-xl font-semibold tracking-tight">${product}</h5>
        </div>
        <div class="text-center">
            <p class="text-sm">Days Left</p>
            <h5 class="mb-2 text-lg md:text-3xl font-semibold tracking-tight">${daysLeft}</h5>
        </div>
    </div>
  `;

  document.getElementById("quizCard").innerHTML = `
    <p class="mb-2 uppercase font-bold">Quizzes</p>
    <hr>
    <div class="flex gap-4 mt-2 justify-between">
        <div class="text-center">
            <p class="text-sm">Total</p>
            <h5 class="mb-2 text-lg md:text-3xl font-semibold tracking-tight">${quizCount}</h5>
        </div>
        <div class="text-center">
            <p class="text-sm">Taken</p>
            <h5 class="mb-2 text-lg md:text-3xl font-semibold tracking-tight">${quizCompleted}</h5>
        </div>
        <div class="text-center">
            <p class="text-sm">Remaining</p>
            <h5 class="mb-2 text-lg md:text-3xl font-semibold tracking-tight">${
              quizCount - quizCompleted
            }</h5>
        </div>
    </div>
  `;

  document.getElementById("assessmentCard").innerHTML = `
    <p class="mb-2 uppercase font-bold">Assessment</p>
    <hr>
    <div class="flex gap-4 mt-2 justify-between">
        <div class="text-center">
            <p class="text-sm">Total</p>
            <h5 class="mb-2 text-lg md:text-3xl font-semibold tracking-tight">${mockCount}</h5>
        </div>
        <div class="text-center">
            <p class="text-sm">Taken</p>
            <h5 class="mb-2 text-lg md:text-3xl font-semibold tracking-tight">${mockCompleted}</h5>
        </div>
        <div class="text-center">
            <p class="text-sm">Remaining</p>
            <h5 class="mb-2 text-lg md:text-3xl font-semibold tracking-tight">${
              mockCount - mockCompleted
            }</h5>
        </div>
    </div>
  `;

  document.getElementById("tutorialCard").innerHTML = `
    <p class="mb-2 uppercase font-bold">Tutorials</p>
    <hr>
    <div class="flex gap-4 mt-2 justify-between">
        <div class="text-center">
            <p class="text-sm">Total</p>
            <h5 class="mb-2 text-lg md:text-3xl font-semibold tracking-tight">${tutorialCount}</h5>
        </div>
        <div class="text-center">
            <p class="text-sm">Completed</p>
            <h5 class="mb-2 text-lg md:text-3xl font-semibold tracking-tight">${tutorialCompleted}</h5>
        </div>
        <div class="text-center">
            <p class="text-sm">Remaining</p>
            <h5 class="mb-2 text-lg md:text-3xl font-semibold tracking-tight">${
              tutorialCount - tutorialCompleted
            }</h5>
        </div>
    </div>
  `;
}

// Utility Functions
function showSkeleton(card) {
  card.innerHTML = `
    <div video="status" class="max-w-sm animate-pulse">
        <div class="h-3 bg-gray-200 rounded-md dark:bg-gray-800 w-24 mb-2"></div>
        <div class="h-8 bg-gray-200 rounded-md dark:bg-gray-800 w-16 mb-2"></div>
        <span class="sr-only">Loading...</span>
    </div>`;
}

function showError(card, message) {
  card.innerHTML = `<p class="text-center text-gray-400">${message}</p>`;
}

document.addEventListener("DOMContentLoaded", fetchStudentStats);
