let questions = [];
let currentIndex = 0;
let userAnswers = {};
let mockUuid = null;

async function fetchQuestions() {
  const questionsDiv = document.getElementById("mock-container");

  try {
    const response = await fetch(`backend/php/fetch-mock-questions.php`);
    const result = await response.json();

    if (result.status === "success" && Array.isArray(result.data)) {
      questions = result.data;
      mockUuid = result.mock_uuid;
      currentIndex = 0;
      displayQuestion(currentIndex);
    } else {
      questionsDiv.innerHTML = `<p class="text-red-500">${
        result.message || "Failed to load questions"
      }</p>`;
    }
  } catch (error) {
    console.error("Fetch error:", error);
    questionsDiv.innerHTML = `<p class="text-red-500">Network error loading questions.</p>`;
  }
}

function displayQuestion(index) {
  const questionsDiv = document.getElementById("mock-container");
  const solutionBox = document.getElementById("solutionBox");

  questionsDiv.innerHTML = "";
  solutionBox.innerHTML = "";

  if (!questions.length) {
    questionsDiv.innerHTML = "<p>No questions available.</p>";
    return;
  }

  const question = questions[index];
  const options = question.options.split(",").map((opt) => opt.trim());

  questionsDiv.innerHTML = `
    <div class="w-full p-3 bg-white mb-2 border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <p class="font-semibold text-blue-700 dark:text-green-500 mb-2">
            Question ${index + 1} of ${questions.length}
        </p>
        <hr>
        <p class="mb-3 mt-3 font-normal text-gray-700 dark:text-gray-400">
            ${question.question}
        </p>
    </div>
  `;

  options.forEach((option) => {
    const isChecked =
      userAnswers[question.question_uuid] === option ||
      (question.attempted && question.selected_option === option);

    questionsDiv.innerHTML += `
      <div class="w-full p-3 mb-2 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
          <label class="flex items-center space-x-2 cursor-pointer">
              <input type="radio" name="question_${
                question.question_uuid
              }" value="${option}" 
                     class="form-radio" ${isChecked ? "checked" : ""}
                     ${question.attempted ? "disabled" : ""}>
              <span class="text-gray-700 dark:text-gray-300">${option}</span>
          </label>
      </div>
    `;
  });

  if (!question.attempted) {
    document
      .querySelectorAll(`input[name="question_${question.question_uuid}"]`)
      .forEach((radio) => {
        radio.addEventListener("change", (e) => {
          userAnswers[question.question_uuid] = e.target.value;
        });
      });
  }

  document.getElementById("prevBtn").disabled = index === 0;
  document.getElementById("nextBtn").disabled = index === questions.length - 1;

  const submitBtn = document.getElementById("submitBtn");
  if (question.attempted) {
    const solutionCard = document.createElement("div");
    const correctAnswer = question.answer || "Correct answer unavailable";
    const selectedAnswer = question.selected_option || "Not available";

    solutionCard.innerHTML = `
      ${
        selectedAnswer === correctAnswer
          ? "<span class='bg-green-200 text-green-800 text-sm font-medium me-2 px-2.5 py-1 rounded-sm dark:bg-green-900 dark:text-green-300'>Correct answer chosen</span>"
          : "<span class='bg-red-200 text-red-800 text-sm font-medium me-2 px-2.5 py-1 rounded-sm dark:bg-red-900 dark:text-red-300'>Wrong answer chosen</span>"
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
    submitBtn.disabled = true;
    submitBtn.classList.add("bg-gray-400", "cursor-not-allowed");
    submitBtn.classList.remove("bg-blue-500", "hover:bg-blue-600");
  } else {
    submitBtn.disabled = false;
    submitBtn.classList.remove("bg-gray-400", "cursor-not-allowed");
    submitBtn.classList.add("bg-blue-500", "hover:bg-blue-600");
  }
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
  const currentQuestion = questions[currentIndex];
  const selectedAnswer = userAnswers[currentQuestion.question_uuid];
  const solutionBox = document.getElementById("solutionBox");

  if (!selectedAnswer) {
    solutionBox.innerHTML = `<p class="text-red-500">Please select an answer first.</p>`;
    return;
  }

  solutionBox.innerHTML = `<p class="text-blue-500">Checking answer...</p>`;

  try {
    const formData = new FormData();
    formData.append("mock_uuid", mockUuid);
    formData.append("question_uuid", currentQuestion.question_uuid);
    formData.append("answer", selectedAnswer);

    const response = await fetch("backend/php/submit-mock-question.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.status === "success") {
      questions[currentIndex] = {
        ...currentQuestion,
        attempted: true,
        selected_option: selectedAnswer,
        solution: result.solution,
        answer: result.answer,
      };
      displayQuestion(currentIndex);

      if (currentIndex === questions.length - 1) {
        setTimeout(() => {
          fetchQuizResult(mockUuid);
          document.getElementById("submitBtn").style.display = "none";
        }, 1000); // Optional delay to allow solution UI to update
      }
    } else {
      solutionBox.innerHTML = `<p class="text-red-500">${result.message}</p>`;
    }
  } catch (error) {
    console.error("Submission error:", error);
    solutionBox.innerHTML = `<p class="text-red-500">Failed to submit answer.</p>`;
  }
}

function getQueryParam(name) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(name);
}

async function fetchQuizResult(uuid) {
  try {
    const response = await fetch(
      `backend/php/fetch-mock-results.php?uuid=${uuid}`
    );
    const data = await response.json();

    if (data.status === "success") {
      const resultDisplay = document.getElementById("solutionBox");
      if (resultDisplay) {
        resultDisplay.innerHTML = `
          <h3>Quiz Results</h3>
          <p>Total Questions:<strong> ${data.total}</strong></p>
          <p>Correct Answers:<strong> ${data.correct_answers}</strong></p>
          <p>Wrong Answers:<strong> ${data.wrong_answers}</strong></p>
        `;
      }
    } else {
      console.error("Error fetching results:", data.message);
    }
  } catch (error) {
    console.error("Error fetching quiz result:", error);
  }
}

function setupCountdownTimer(durationInMinutes) {
  const countdownElement = document.getElementById("countdownTimer");
  const STORAGE_KEY = "quiz_end_time";
  let endTime = localStorage.getItem(STORAGE_KEY);

  if (!endTime) {
    endTime = Date.now() + durationInMinutes * 60 * 1000;
    localStorage.setItem(STORAGE_KEY, endTime);
  } else {
    endTime = parseInt(endTime);
  }

  function updateCountdown() {
    const now = Date.now();
    let timeLeft = Math.floor((endTime - now) / 1000);

    if (timeLeft <= 0) {
      countdownElement.textContent = "Time is up!";
      localStorage.removeItem(STORAGE_KEY);
      clearInterval(timerInterval);

      // Disable submission
      const submitBtn = document.getElementById("submitBtn");
      if (submitBtn) submitBtn.style.display = "none";

      const uuid = mockUuid || getQueryParam("uuid");
      if (uuid) {
        fetchQuizResult(uuid);
      } else {
        console.error("UUID not found.");
      }
      return;
    }

    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    countdownElement.textContent = `${minutes}m ${
      seconds < 10 ? "0" + seconds : seconds
    }s left`;
  }

  updateCountdown();
  const timerInterval = setInterval(updateCountdown, 1000);
}

// Single DOMContentLoaded
document.addEventListener("DOMContentLoaded", () => {
  const countdownElement = document.getElementById("countdownTimer");
  if (countdownElement) {
    const durationInMinutes = parseInt(
      countdownElement.getAttribute("data-duration")
    );
    if (!isNaN(durationInMinutes)) {
      setupCountdownTimer(durationInMinutes);
    }
  }

  document.getElementById("generateBtn").addEventListener("click", () => {
    fetchQuestions();
    const btn = document.getElementById("generateBtn");
    btn.disabled = true;
    btn.textContent = "Mock Started";
  });

  document.getElementById("nextBtn").addEventListener("click", nextQuestion);
  document.getElementById("prevBtn").addEventListener("click", prevQuestion);
  document.getElementById("submitBtn").addEventListener("click", submitQuiz);
});
