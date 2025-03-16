let questions = []; // Store fetched questions globally
let currentIndex = 0; // Track the current question index

async function fetchQuestions() {
  let questionsDiv = document.getElementById("questionsDisplay");

  // Get ID from the URL
  const urlParams = new URLSearchParams(window.location.search);
  const categoryId = urlParams.get("id");

  if (!categoryId) {
    questionsDiv.innerHTML = `<p class="text-red-500">Error: Category ID is missing.</p>`;
    return;
  }

  try {
    let response = await fetch(
      `backend/php/fetch-question.php?id=${categoryId}`
    );
    let result = await response.json();

    if (result.status === "success" && Array.isArray(result.data)) {
      questions = result.data;
      currentIndex = 0;
      displayQuestion(currentIndex);
    } else {
      questionsDiv.innerHTML = `<p>${result.message}</p>`;
    }
  } catch (error) {
    console.error("Fetch error:", error);
    questionsDiv.innerHTML = `<p class="text-red-500">Failed to load questions.</p>`;
  }
}

// Function to display a single question at a time
function displayQuestion(index) {
  let questionsDiv = document.getElementById("questionsDisplay");
  questionsDiv.innerHTML = ""; // Clear previous content

  if (questions.length === 0) return; // No questions available

  let question = questions[index];

  // Create question container
  let questionCard = document.createElement("div");
  questionCard.className =
    "w-full p-3 bg-white mb-2 border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700";

  questionCard.innerHTML = `
      <p class="font-semibold text-blue-700 dark:text-green-500 mb-1">Question ${
        index + 1
      } of ${questions.length}</p>
      <hr>
      <p class="mb-3 mt-3 font-normal text-gray-700 dark:text-gray-400">${
        question.question
      }</p>
  `;

  questionsDiv.appendChild(questionCard);

  // Create options dynamically
  let options = [
    { value: "option1", text: question.option1 },
    { value: "option2", text: question.option2 },
    { value: "option3", text: question.option3 },
    { value: "option4", text: question.option4 },
  ];

  options.forEach((opt) => {
    let optionDiv = document.createElement("div");
    optionDiv.className =
      "w-full p-3 mb-2 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700";

    optionDiv.innerHTML = `
        <label class="flex items-center space-x-2 cursor-pointer">
          <input type="radio" name="question_${question.id}" value="${opt.value}" class="form-radio">
          <span class="text-gray-700 dark:text-gray-300">${opt.text}</span>
        </label>
    `;

    questionsDiv.appendChild(optionDiv);
  });

  // Update button states
  document.getElementById("prevBtn").disabled = currentIndex === 0;
  document.getElementById("nextBtn").disabled =
    currentIndex === questions.length - 1;
}

// Function to go to the next question
function nextQuestion() {
  if (currentIndex < questions.length - 1) {
    currentIndex++;
    displayQuestion(currentIndex);
  }
}

// Function to go to the previous question
function prevQuestion() {
  if (currentIndex > 0) {
    currentIndex--;
    displayQuestion(currentIndex);
  }
}

// Submit function (Modify this as per your requirements)
function submitQuiz() {
  alert("Form submitted! Implement actual submission logic.");
}

// Attach event listeners
document.addEventListener("DOMContentLoaded", () => {
  fetchQuestions();
  document.getElementById("nextBtn").addEventListener("click", nextQuestion);
  document.getElementById("prevBtn").addEventListener("click", prevQuestion);
  document.getElementById("submitBtn").addEventListener("click", submitQuiz);
});
