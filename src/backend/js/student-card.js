async function fetchStudentStats() {
  let packageCard = document.getElementById("packageCard");
  let quizCard = document.getElementById("quizCard");
  let assessmentCard = document.getElementById("assessmentCard");
  let tutorialCard = document.getElementById("tutorialCard");

  // Show skeleton loaders
  [packageCard, quizCard, assessmentCard, tutorialCard].forEach(showSkeleton);

  try {
    let response = await fetch("backend/php/student-fetch-scripts.php");
    if (!response.ok) throw new Error("Network response was not ok");

    let result = await response.json();

    if (result.status === "success") {
      displayDashboardStats(
        result.package_count || 0,
        result.quiz_count || 0,
        result.assessment_count || 0,
        result.tutorial_count || 0
      );
    } else {
      console.error("Error:", result.message);
      showError(packageCard, result.message);
      showError(quizCard, result.message);
      showError(assessmentCard, result.message);
      showError(tutorialCard, result.message);
    }
  } catch (error) {
    console.error("Fetch error:", error);
    let errorMsg = "Failed to load data. Please try again later.";
    showError(packageCard, errorMsg);
    showError(quizCard, errorMsg);
    showError(assessmentCard, errorMsg);
    showError(tutorialCard, errorMsg);
  }
}

function displayDashboardStats(
  packageCount,
  quizCount,
  assessmentCount,
  tutorialCount
) {
  document.getElementById("packageCard").innerHTML = `
        <p class=" mb-2 uppercase font-bold">Subscription</p>
        <hr>
        <div class="flex gap-4 mt-2">
            <div class="text-center">
                <p class=" text-sm">total</p>
                <h5 class="mb-2 text-3xl font-semibold tracking-tight">${packageCount}</h5>
            </div>
            <div class="text-center">
                <p class=" text-sm">total</p>
                <h5 class="mb-2 text-3xl font-semibold tracking-tight">${packageCount}</h5>
            </div>
        </div>
      `;

  document.getElementById("quizCard").innerHTML = `
        <p class=" mb-2 uppercase font-bold ">Quizzes</p>
        <hr>
        <div class="flex gap-4 mt-2 justify-between">      
            <div class="text-center">
                <p class=" text-sm">total</p>
                <h5 class="mb-2 text-3xl font-semibold tracking-tight ">${quizCount}</h5>
            </div>
            <div class="text-center">
                <p class=" text-sm">taken</p>
                <h5 class="mb-2 text-3xl font-semibold tracking-tight ">${quizCount}</h5>
            </div>
            <div class="text-center">
                <p class=" text-sm">remaining</p>
                <h5 class="mb-2 text-3xl font-semibold tracking-tight ">${quizCount}</h5>
            </div>
        </div>
      `;

  document.getElementById("assessmentCard").innerHTML = `
        <p class=" mb-2 uppercase font-bold ">Assessments</p>
        <hr>
        <div class="flex gap-4 mt-2 justify-between">      
            <div class="text-center">
                <p class=" text-sm">total</p>
                <h5 class="mb-2 text-3xl font-semibold tracking-tight ">${assessmentCount}</h5>
            </div>
            <div class="text-center">
                <p class=" text-sm">taken</p>
                <h5 class="mb-2 text-3xl font-semibold tracking-tight ">${assessmentCount}</h5>
            </div>
            <div class="text-center">
                <p class=" text-sm">remaining</p>
                <h5 class="mb-2 text-3xl font-semibold tracking-tight ">${assessmentCount}</h5>
            </div>
        </div>
      `;

  document.getElementById("tutorialCard").innerHTML = `
        <p class=" mb-2 uppercase font-bold ">tutorials</p>
        <hr>
        <div class="flex gap-4 mt-2 justify-between">      
            <div class="text-center">
                <p class=" text-sm">total</p>
                <h5 class="mb-2 text-3xl font-semibold tracking-tight ">${tutorialCount}</h5>
            </div>
            <div class="text-center">
                <p class=" text-sm">completed</p>
                <h5 class="mb-2 text-3xl font-semibold tracking-tight ">${tutorialCount}</h5>
            </div>
            <div class="text-center">
                <p class=" text-sm">remaining</p>
                <h5 class="mb-2 text-3xl font-semibold tracking-tight ">${tutorialCount}</h5>
            </div>
        </div>
      `;
}

// Utility Functions
function showSkeleton(card) {
  card.innerHTML = `
        <div video="status" class="max-w-sm animate-pulse">
            <div class="h-3 bg-gray-200 rounded-md dark:bg-gray-700 w-24 mb-2"></div>
            <div class="h-8 bg-gray-200 rounded-md dark:bg-gray-700 w-16 mb-2"></div>
            <span class="sr-only">Loading...</span>
        </div>`;
}

function showError(card, message) {
  card.innerHTML = `<p class="text-center text-gray-400">${message}</p>`;
}

// Fetch stats when the page loads
document.addEventListener("DOMContentLoaded", fetchStudentStats);
