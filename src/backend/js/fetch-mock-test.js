let questions = [];
let currentIndex = 0;
let userAnswers = {};
let mockUuid = null;
let timerInterval;
let quizStartTime;
let quizEndTime;

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

      if (result.duration) {
        setupCountdownTimer(parseInt(result.duration));
      }
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
  const options = question.options.split("|").map((opt) => opt.trim());
  const isMsq = question.type === "msq";
  const inputType = isMsq ? "checkbox" : "radio";
  const answerKey = question.question_uuid;

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
    let isChecked;
    if (isMsq) {
      // For MSQ, userAnswers[answerKey] is an array of selected options
      isChecked =
        (Array.isArray(userAnswers[answerKey]) &&
          userAnswers[answerKey].includes(option)) ||
        (question.attempted &&
          Array.isArray(question.selected_option) &&
          question.selected_option.includes(option));
    } else {
      // For MCQ, userAnswers[answerKey] is a string
      isChecked =
        userAnswers[answerKey] === option ||
        (question.attempted && question.selected_option === option);
    }

    questionsDiv.innerHTML += `
      <div class="w-full p-3 mb-2 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
          <label class="flex items-center space-x-2 cursor-pointer">
              <input type="${inputType}" name="question_${answerKey}" value="${option}" 
                     class="form-${inputType}" ${isChecked ? "checked" : ""}
                     ${question.attempted ? "disabled" : ""}>
              <span class="text-gray-700 dark:text-gray-300">${option}</span>
          </label>
      </div>
    `;
  });

  if (!question.attempted) {
    if (isMsq) {
      // For MSQ, handle checkboxes
      document
        .querySelectorAll(`input[name="question_${answerKey}"]`)
        .forEach((checkbox) => {
          checkbox.addEventListener("change", (e) => {
            if (!Array.isArray(userAnswers[answerKey])) {
              userAnswers[answerKey] = [];
            }
            if (e.target.checked) {
              if (!userAnswers[answerKey].includes(e.target.value)) {
                userAnswers[answerKey].push(e.target.value);
              }
            } else {
              userAnswers[answerKey] = userAnswers[answerKey].filter(
                (val) => val !== e.target.value
              );
            }
          });
        });
    } else {
      // For MCQ, handle radio buttons
      document
        .querySelectorAll(`input[name="question_${answerKey}"]`)
        .forEach((radio) => {
          radio.addEventListener("change", (e) => {
            userAnswers[answerKey] = e.target.value;
          });
        });
    }
  }

  document.getElementById("prevBtn").disabled = index === 0;
  document.getElementById("nextBtn").disabled = index === questions.length - 1;

  const submitBtn = document.getElementById("submitBtn");
  if (question.attempted) {
    const solutionCard = document.createElement("div");
    // For MSQ, show arrays as comma separated
    const correctAnswer = Array.isArray(question.answer)
      ? question.answer.join(", ")
      : question.answer || "Correct answer unavailable";
    const selectedAnswer = Array.isArray(question.selected_option)
      ? question.selected_option.join(", ")
      : question.selected_option || "Not available";

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
    submitBtn.classList.remove("bg-blue-900", "hover:bg-blue-800");
  } else {
    submitBtn.disabled = false;
    submitBtn.classList.remove("bg-gray-400", "cursor-not-allowed");
    submitBtn.classList.add("bg-blue-900", "hover:bg-blue-800");
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
          quizEndTime = Date.now();
          clearInterval(timerInterval);
          showFinalResult(mockUuid);
          document.getElementById("submitBtn").style.display = "none";
        }, 1000);
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

async function showFinalResult(uuid) {
  try {
    // Fetch the quiz results first
    const response = await fetch(
      `backend/php/fetch-mock-results.php?uuid=${uuid}`
    );
    const data = await response.json();

    if (data.status === "success") {
      const resultDisplay = document.getElementById("solutionBox");

      const durationInSeconds = Math.floor(
        (quizEndTime - quizStartTime) / 1000
      );
      const minutesTaken = Math.floor(durationInSeconds / 60);
      const secondsTaken = durationInSeconds % 60;
      const formattedTime = `${minutesTaken}m ${
        secondsTaken < 10 ? "0" : ""
      }${secondsTaken}s`;
      const percentageScore = (
        (data.correct_answers / data.total) *
        100
      ).toFixed(0);

      if (resultDisplay) {
        resultDisplay.innerHTML = `
          <h3>Quiz Results</h3>
          <p>Total Questions: <strong>${data.total}</strong></p>
          <p>Correct Answers: <strong>${data.correct_answers}</strong></p>
          <p>Wrong Answers: <strong>${data.wrong_answers}</strong></p>
          <p><strong>Time Taken:</strong> ${formattedTime}</p>
          <p><strong>Percentage Score:</strong> ${percentageScore}%</p>
        `;
      }

      // Now update the time in the database (second API call)
      const updateResponse = await fetch("backend/php/update-time.php", {
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

function setupCountdownTimer(durationInMinutes) {
  const countdownElement = document.getElementById("countdownTimer");
  quizStartTime = Date.now();
  const endTime = quizStartTime + durationInMinutes * 60 * 1000;

  function updateCountdown() {
    const now = Date.now();
    let timeLeft = Math.floor((endTime - now) / 1000);

    if (timeLeft <= 0) {
      countdownElement.textContent = "Time is up!";
      clearInterval(timerInterval);

      const submitBtn = document.getElementById("submitBtn");
      if (submitBtn) submitBtn.style.display = "none";

      const uuid = mockUuid || getQueryParam("uuid");
      if (uuid) {
        quizEndTime = endTime;
        showFinalResult(uuid);
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
  timerInterval = setInterval(updateCountdown, 1000);
}

// Single DOMContentLoaded
document.addEventListener("DOMContentLoaded", () => {
  const countdownElement = document.getElementById("countdownTimer");
  if (countdownElement) {
  }

  document.getElementById("generateBtn").addEventListener("click", () => {
    fetchQuestions();
    const btn = document.getElementById("generateBtn");
    btn.disabled = true;
    btn.textContent = "Test started...";
  });

  document.getElementById("nextBtn").addEventListener("click", nextQuestion);
  document.getElementById("prevBtn").addEventListener("click", prevQuestion);
  document.getElementById("submitBtn").addEventListener("click", submitQuiz);
});
