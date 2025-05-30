let questions = [];
let currentIndex = 0;
let userAnswers = {};
let startTime = Date.now(); // when the quiz starts
let endTime = null;
let timeUsed = 0;
let countdownInterval = null;

async function fetchQuestions() {
  let questionsDiv = document.getElementById("questionsDisplay");

  const urlParams = new URLSearchParams(window.location.search);
  const categoryId = urlParams.get("uuid");

  if (!categoryId) {
    questionsDiv.innerHTML = `<p class="text-red-500">Error: Category ID is missing.</p>`;
    return;
  }

  try {
    let response = await fetch(
      `backend/php/fetch-quiz-questions.php?uuid=${categoryId}`
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

function displayQuestion(index) {
  const questionsDiv = document.getElementById("questionsDisplay");
  const solutionBox = document.getElementById("solutionBox");

  questionsDiv.innerHTML = "";
  solutionBox.innerHTML = "";

  if (questions.length === 0) return;

  const question = questions[index];

  const questionCard = document.createElement("div");
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

  const optionsArray = Array.isArray(question.options)
    ? question.options
    : question.options.split(",").map((opt) => opt.trim());

  optionsArray.forEach((optionText) => {
    const optionDiv = document.createElement("div");
    optionDiv.className =
      "w-full p-3 mb-2 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700";

    const isChecked =
      (question.attempted && question.selected_option === optionText) ||
      userAnswers[question.question_uuid] === optionText;

    const checked = isChecked ? "checked" : "";
    const disabled = question.attempted ? "disabled" : "";

    optionDiv.innerHTML = `
      <label class="flex items-center space-x-2 cursor-pointer">
        <input type="radio" name="question_${question.question_uuid}" value="${optionText}" class="form-radio" ${checked} ${disabled}>
        <span class="text-gray-700 dark:text-gray-300">${optionText}</span>
      </label>
    `;

    if (!question.attempted) {
      optionDiv.querySelector("input").addEventListener("change", (e) => {
        userAnswers[question.question_uuid] = e.target.value;
      });
    }

    questionsDiv.appendChild(optionDiv);
  });

  const submitBtn = document.getElementById("submitBtn");
  if (question.attempted) {
    submitBtn.disabled = true;
    submitBtn.classList.add("bg-gray-400", "cursor-not-allowed");
    submitBtn.classList.remove("bg-blue-500", "hover:bg-blue-600");
  } else {
    submitBtn.disabled = false;
    submitBtn.classList.remove("bg-gray-400", "cursor-not-allowed");
    submitBtn.classList.add("bg-blue-900", "hover:bg-green-600");
  }

  if (question.attempted) {
    const solutionCard = document.createElement("div");
    const correctAnswer = question.answer || "Correct answer unavailable";
    const selectedAnswer = question.selected_option || "Not available";

    solutionCard.innerHTML = `
      ${
        selectedAnswer === correctAnswer
          ? "<span class='bg-green-200 text-green-800 text-sm font-medium px-2.5 py-1 rounded-sm dark:bg-green-900 dark:text-green-300'>Correct answer chosen</span>"
          : "<span class='bg-red-200 text-red-800 text-sm font-medium px-2.5 py-1 rounded-sm dark:bg-red-900 dark:text-red-300'>Wrong answer chosen</span>"
      }
      <hr class="my-2">
      <p class="text-xs font-bold uppercase">Correct Answer:</p>
      <p>${correctAnswer}</p>
      <hr class="my-2">
      <div class="bg-gray-100 dark:bg-gray-600 rounded-sm border border-gray-300 dark:border-gray-600 p-4">
        <p class="font-semibold uppercase">Solution:</p>      
        <p>${question.solution}</p>      
      </div>
    `;
    solutionBox.appendChild(solutionCard);
  }

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

async function submitQuiz() {
  const solutionBox = document.querySelector("#solutionBox");
  solutionBox.innerHTML = "";

  const currentQuestion = questions[currentIndex];
  const selectedAnswer = userAnswers[currentQuestion.question_uuid];

  if (!selectedAnswer) {
    solutionBox.innerHTML = `<p class="text-red-500">Please select an answer before submitting.</p>`;
    return;
  }

  solutionBox.innerHTML = `
    <div class="text-center mt-4">
      <svg aria-hidden="true" class="w-8 h-8 mx-auto text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none">
        <path d="M100 50.59C100 78.21 77.61 100.59 50 100.59C22.39 100.59 0 78.21 0 50.59C0 22.98 22.39 0.59 50 0.59C77.61 0.59 100 22.98 100 50.59Z" fill="currentColor"/>
        <path d="M93.97 39.04C96.39 38.4 97.86 35.91 97.01 33.55..." fill="currentFill"/>
      </svg>
      <span class="block text-gray-600 mt-2 font-semibold">Fetching solution...</span>
    </div>
  `;

  const formData = new FormData();
  const quizId = new URLSearchParams(window.location.search).get("uuid");

  formData.append("question_id", currentQuestion.question_uuid);
  formData.append("answer", selectedAnswer);
  formData.append("quiz_id", quizId);

  try {
    const response = await fetch("backend/php/submit-quiz-question.php", {
      method: "POST",
      body: formData,
    });
    const result = await response.json();

    if (result.status === "success") {
      currentQuestion.attempted = true;
      currentQuestion.selected_option = result.selected_answer;
      currentQuestion.solution = result.solution;
      currentQuestion.answer = result.answer;

      displayQuestion(currentIndex);

      const allAttempted = questions.every((q) => q.attempted);
      if (allAttempted) {
        clearInterval(countdownInterval); // Stop timer if finished early
        endTime = Date.now();
        timeUsed = Math.floor((endTime - startTime) / 1000); // in seconds
        fetchQuizResult(quizId);
      }
    } else {
      solutionBox.innerHTML = `<p class="text-red-500">${result.message}</p>`;
    }
  } catch (error) {
    console.error("Error submitting question:", error);
    solutionBox.innerHTML = `<p class="text-red-500">Failed to check answer. Try again.</p>`;
  }
}

function startCountdown() {
  const countdownElement = document.getElementById("countdownTimer");
  if (!countdownElement) return;

  const durationInMinutes = parseInt(
    countdownElement.getAttribute("data-duration")
  );
  if (isNaN(durationInMinutes)) return;

  const STORAGE_KEY = "quiz_end_time";
  let endTimeStorage = localStorage.getItem(STORAGE_KEY);

  if (!endTimeStorage) {
    endTimeStorage = Date.now() + durationInMinutes * 60 * 1000;
    localStorage.setItem(STORAGE_KEY, endTimeStorage);
  } else {
    endTimeStorage = parseInt(endTimeStorage);
  }

  function updateCountdown() {
    const now = Date.now();
    let timeLeft = Math.floor((endTimeStorage - now) / 1000);

    if (timeLeft <= 0) {
      countdownElement.textContent = "Time is up!";
      localStorage.removeItem(STORAGE_KEY);
      clearInterval(countdownInterval);
      document.getElementById("submitBtn").style.display = "none";

      endTime = Date.now();
      timeUsed = Math.floor((endTime - startTime) / 1000);

      submitQuiz();
      const uuid = new URLSearchParams(window.location.search).get("uuid");
      if (uuid) fetchQuizResult(uuid);
      return;
    }

    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    countdownElement.textContent = `${minutes}m ${
      seconds < 10 ? "0" : ""
    }${seconds}s left`;
  }

  countdownInterval = setInterval(updateCountdown, 1000);
  updateCountdown();
}

async function fetchQuizResult(uuid) {
  try {
    const response = await fetch(
      `backend/php/fetch-quiz-result.php?uuid=${uuid}`
    );
    const data = await response.json();

    const resultDisplay = document.getElementById("solutionBox");

    if (data.status === "success" && resultDisplay) {
      const minutes = Math.floor(timeUsed / 60);
      const seconds = timeUsed % 60;
      const formattedTime = `${minutes}m ${seconds < 10 ? "0" : ""}${seconds}s`;

      resultDisplay.innerHTML = `
        <div class="bg-white p-4 border rounded shadow dark:bg-gray-800 dark:text-white">
          <h3 class="text-xl font-bold mb-2">Quiz Results</h3>
          <p><strong>Total Questions:</strong> ${data.total}</p>
          <p><strong>Correct Answers:</strong> ${data.correct_answers}</p>
          <p><strong>Wrong Answers:</strong> ${data.wrong_answers}</p>
          <p><strong>Score Percentage:</strong> ${Math.round(
            (data.correct_answers / data.total) * 100
          )}%</p>
          <p><strong>Time Used:</strong> ${formattedTime}</p>
        </div>
      `;

      // Send time used to backend
      const updateResponse = await fetch("backend/php/update-quiz-time.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          uuid: uuid,
          time_taken: formattedTime,
        }),
      });

      const updateData = await updateResponse.json();
      if (updateData.status !== "success") {
        console.error("Error updating time:", updateData.message);
      }
    } else {
      console.error("Error fetching results:", data.message);
    }
  } catch (error) {
    console.error("Error fetching quiz result:", error);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  fetchQuestions();
  startCountdown();

  document.getElementById("nextBtn").addEventListener("click", nextQuestion);
  document.getElementById("prevBtn").addEventListener("click", prevQuestion);
  document.getElementById("submitBtn").addEventListener("click", submitQuiz);
});
