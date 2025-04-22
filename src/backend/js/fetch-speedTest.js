let questionStartTime;
let totalTimeTaken = 0; // in seconds
let currentQuestionIndex = 0;
let questionInterval;
let countdownInterval;

async function fetchSpeedTest() {
  try {
    let response = await fetch("backend/php/speedtest.php");
    let result = await response.json();

    if (result.status === "success") {
      displaySpeedTest(result.questions);
    } else {
      console.error("Failed to fetch questions:", result.message);
    }
  } catch (error) {
    console.error("Error fetching questions:", error);
  }
}

const userResponses = []; // To store submitted answers

function displaySpeedTest(questions) {
  const quizContainer = document.getElementById("quiz-container");
  quizContainer.innerHTML = "";

  function showQuestion(index) {
    if (index >= questions.length) {
      clearInterval(questionInterval);
      clearInterval(countdownInterval);
      displayResults();
      return;
    }

    const question = questions[index];
    const options = question.options.split(",").map((opt) => opt.trim());

    const optionsHTML = options
      .map(
        (option, optIndex) => `
        <div>
          <label class="flex items-center space-x-2">
            <input type="radio" name="question_${index}" value="${option}" />
            <span>${option}</span>
          </label>
        </div>
      `
      )
      .join("");

    quizContainer.innerHTML = `
      <div class="dark:bg-gray-900 p-4 rounded-lg shadow border border-gray-700 space-y-4">
        <div class="text-green-600 text-sm">Question ${index + 1} of ${
      questions.length
    }</div>
        <hr />
        <div class="text-white text-base">${question.question}</div>
        <form id="questionForm" class="flex flex-col space-y-3">
          ${optionsHTML}
        </form>
        <div class="flex justify-between items-center">
          <div class="text-xs text-red-500" id="countdown">Next question in 20 seconds...</div>
          <button id="submitBtn" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition">Submit</button>
        </div>
      </div>
    `;

    questionStartTime = Date.now(); // capture time in milliseconds
    let secondsLeft = 20;
    const countdownEl = document.getElementById("countdown");

    countdownInterval = setInterval(() => {
      secondsLeft--;
      countdownEl.textContent = `Next question in ${secondsLeft} second${
        secondsLeft !== 1 ? "s" : ""
      }...`;

      if (secondsLeft <= 0) {
        clearInterval(countdownInterval);
        handleSubmit(); // Auto-submit when time runs out
      }
    }, 1000);

    // Manual submit
    document.getElementById("submitBtn").addEventListener("click", (e) => {
      e.preventDefault();
      clearInterval(countdownInterval);
      handleSubmit();
    });

    function handleSubmit() {
      const selected = document.querySelector(
        `input[name="question_${index}"]:checked`
      );
      const answer = selected ? selected.value : null;

      const timeTaken = Math.floor((Date.now() - questionStartTime) / 1000);
      totalTimeTaken += timeTaken;

      userResponses.push({
        questionId: question.id,
        selectedAnswer: answer,
        correctAnswer: question.answer,
        isCorrect: answer === question.answer,
        timeTaken: timeTaken,
      });

      currentQuestionIndex++;
      showQuestion(currentQuestionIndex);
    }
  }

  showQuestion(currentQuestionIndex);
}

// Optional: Display result at the end
function displayResults() {
  const quizContainer = document.getElementById("result-container");
  const correctCount = userResponses.filter((r) => r.isCorrect).length;
  const total = userResponses.length;
  const percentage = ((correctCount / total) * 100).toFixed(0);

  quizContainer.innerHTML = `
      <div class="dark:bg-gray-900 p-6 rounded-lg shadow text-white text-center space-y-4">
        <h2 class="text-xl font-bold text-green-400">Quiz Completed 🎉</h2>
        <p>You got <strong>${correctCount}</strong> out of <strong>${total}</strong> correct.</p>
        <p>Your Score:</p>
        <p class="text-5xl font-bold text-green-600">${percentage}%</p>
        <p>Total Time Taken: <strong>${totalTimeTaken}</strong> second${
    totalTimeTaken !== 1 ? "s" : ""
  }</p>
      </div>
    `;
}

document.addEventListener("DOMContentLoaded", fetchSpeedTest);
