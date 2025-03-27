let questions = [];
let currentIndex = 0;
let userAnswers = {};

async function fetchQuestions() {
  let questionsDiv = document.getElementById("questionsDisplay");

  // Get ID from the URL
  const urlParams = new URLSearchParams(window.location.search);
  const categoryId = urlParams.get("uuid");

  if (!categoryId) {
    questionsDiv.innerHTML = `<p class="text-red-500">Error: Category ID is missing.</p>`;
    return;
  }

  try {
    let response = await fetch(
      `backend/php/fetch-question.php?uuid=${categoryId}`
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

// display questions and solution
function displayQuestion(index) {
  let questionsDiv = document.getElementById("questionsDisplay");
  let solutionBox = document.getElementById("solutionBox");

  questionsDiv.innerHTML = ""; // Clear previous content
  solutionBox.innerHTML = ""; // Clear previous solution if any

  if (questions.length === 0) return;

  let question = questions[index];

  let questionCard = document.createElement("div");
  questionCard.className =
    "w-full p-3 bg-white mb-2 border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700";

  questionCard.innerHTML = `
      <p class="font-semibold text-blue-700 dark:text-green-500 mb-2">Question ${
        index + 1
      } of ${questions.length}</p>
      <hr>
      <p class="mb-3 mt-3 font-normal text-gray-700 dark:text-gray-400">${
        question.question
      }</p>
  `;

  questionsDiv.appendChild(questionCard);

  const optionsArray = question.options.split(",").map((opt) => opt.trim());

  optionsArray.forEach((optionText) => {
    let optionDiv = document.createElement("div");
    optionDiv.className =
      "w-full p-3 mb-2 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700";

    let isChecked =
      (question.attempted && question.selected_option === optionText) ||
      userAnswers[question.question_uuid] === optionText;

    let checked = isChecked ? "checked" : "";
    let disabled = question.attempted ? "disabled" : "";

    optionDiv.innerHTML = `
        <label class="flex items-center space-x-2 cursor-pointer">
          <input type="radio" name="question_${question.question_uuid}" value="${optionText}" class="form-radio" ${checked} ${disabled}>
          <span class="text-gray-700 dark:text-gray-300">${optionText}</span>
        </label>
    `;

    // Only add event listener if not attempted
    if (!question.attempted) {
      optionDiv.querySelector("input").addEventListener("change", (e) => {
        userAnswers[question.question_uuid] = e.target.value;
      });
    }

    questionsDiv.appendChild(optionDiv);
  });

  // Disable the submit button if question already attempted
  const submitBtn = document.getElementById("submitBtn");
  submitBtn.disabled = question.attempted;
  if (question.attempted) {
    submitBtn.classList.add("bg-gray-400", "cursor-not-allowed");
    submitBtn.classList.remove("bg-blue-500", "hover:bg-blue-600");
  } else {
    submitBtn.classList.remove("bg-gray-400", "cursor-not-allowed");
    submitBtn.classList.add("bg-blue-900", "hover:bg-green-600");
  }

  // Show solution if already attempted
  if (question.attempted) {
    const solutionCard = document.createElement("div");

    const correctAnswer = question.answer || "Correct answer unavailable";
    const selectedAnswer = question.selected_option || "Not available";

    solutionCard.innerHTML = `
      <p><strong>Your Answer:</strong> <span class="${
        selectedAnswer === correctAnswer ? "text-green-600" : "text-red-600"
      }">${selectedAnswer}</span></p>
      <p><strong>Correct Answer:</strong> ${correctAnswer}</p>
      <p><strong>Solution:</strong> ${question.solution}</p>
    `;

    solutionBox.appendChild(solutionCard);
  }

  // Enable/disable navigation buttons
  document.getElementById("prevBtn").disabled = currentIndex === 0;
  document.getElementById("nextBtn").disabled =
    currentIndex === questions.length - 1;
}

function nextQuestion() {
  if (currentIndex < questions.length - 1) {
    currentIndex++;
    displayQuestion(currentIndex);
  }
}

function prevQuestion() {
  if (currentIndex > 0) {
    currentIndex--;
    displayQuestion(currentIndex);
  }
}

// submit quiz
async function submitQuiz() {
  const solutionBox = document.querySelector("#solutionBox");
  solutionBox.innerHTML = ""; // Clear previous content

  const currentQuestion = questions[currentIndex];
  const selectedAnswer = userAnswers[currentQuestion.question_uuid];

  if (!selectedAnswer) {
    solutionBox.innerHTML = `<p class="text-red-500">Please select an answer before submitting.</p>`;
    return;
  }

  // Show loading spinner
  solutionBox.innerHTML = `
    <div class="text-center mt-4">
      <svg aria-hidden="true" class="w-8 h-8 mx-auto text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
      </svg>
      <span class="block text-gray-600 mt-2 font-semibold">Fetching solution...</span>
    </div>
  `;

  const formData = new FormData();
  formData.append("question_id", currentQuestion.question_uuid);
  formData.append("answer", selectedAnswer);

  try {
    const response = await fetch("backend/php/submit-question.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.status === "success") {
      const solutionCard = document.createElement("div");

      solutionCard.innerHTML = `
        <p><span class="${
          result.correct ? "bg-green-600 p-2 rounded" : "bg-red-600 p-2 rounded"
        }">${result.selected_answer}</span></p>
        <p class="mb-2"><strong>Correct Answer:</strong> ${
          result.correct_answer
        }</p> 
        <hr class="mb-2">  
        <p><strong>Solution:</strong> ${result.solution}</p>
      `;

      // Update the current question state
      currentQuestion.attempted = true;
      currentQuestion.selected_option = result.selected_answer;
      currentQuestion.solution = result.solution;

      // Re-render the question to reflect updated state
      displayQuestion(currentIndex);

      // Scroll to solution box after update
      solutionBox.scrollIntoView({ behavior: "smooth" });
    } else {
      solutionBox.innerHTML = `<p class="text-red-500">${result.message}</p>`;
    }
  } catch (error) {
    console.error("Error submitting current question:", error);
    solutionBox.innerHTML = `<p class="text-red-500">Failed to check answer. Try again.</p>`;
  }
}

// Attach event listeners
document.addEventListener("DOMContentLoaded", () => {
  fetchQuestions();
  document.getElementById("nextBtn").addEventListener("click", nextQuestion);
  document.getElementById("prevBtn").addEventListener("click", prevQuestion);
  document
    .getElementById("submitBtn")
    .addEventListener("click", (e) => submitQuiz(e));
});
