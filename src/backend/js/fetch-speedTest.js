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
    const options = question.options.split("|").map((opt) => opt.trim());
    const isMSQ = question.type && question.type.toLowerCase() === "msq";

    const inputType = isMSQ ? "checkbox" : "radio";
    const nameAttr = isMSQ ? `option_${index}_` : `question_${index}`;

    const optionsHTML = options
      .map(
        (option, optIndex) => `
        <div>
          <label class="flex items-center space-x-2">
            <input type="${inputType}" name="${nameAttr}" value="${option}" />
            <span>${option}</span>
          </label>
        </div>
      `
      )
      .join("");

    quizContainer.innerHTML = `
      <div class="dark:bg-gray-900 p-4 rounded-lg bg-white shadow border border-gray-300 dark:border-gray-700 space-y-4">
        <div class="text-blue-600 dark:text-green-600 text-sm">Question ${
          index + 1
        } of ${questions.length}</div>
        <hr />
        <div class="dark:text-white text-base">${question.question}</div>
        <form id="questionForm" class="flex flex-col space-y-3">
          ${optionsHTML}
        </form>
        <div class="flex justify-between items-center">
          <div class="text-xs text-red-500" id="countdown">Next question in 30 seconds...</div>
          <button id="submitBtn" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition">Submit</button>
        </div>
      </div>
    `;

    questionStartTime = Date.now(); // capture time in milliseconds
    let secondsLeft = 30;
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
      let answer;
      if (isMSQ) {
        // For MSQ, collect all checked values as an array
        answer = Array.from(
          document.querySelectorAll(
            `input[type="checkbox"][name="${nameAttr}"]:checked`
          )
        ).map((el) => el.value);
      } else {
        // For MCQ, get the selected radio value
        const selected = document.querySelector(
          `input[type="radio"][name="${nameAttr}"]:checked`
        );
        answer = selected ? selected.value : null;
      }

      const timeTaken = Math.floor((Date.now() - questionStartTime) / 1000);
      totalTimeTaken += timeTaken;

      // For MSQ, compare arrays (assuming question.answer is also an array or a pipe-separated string)
      let isCorrect;
      if (isMSQ) {
        const correctAnswers = Array.isArray(question.answer)
          ? question.answer
          : question.answer.split("|").map((a) => a.trim());
        isCorrect =
          Array.isArray(answer) &&
          answer.length === correctAnswers.length &&
          answer.every((ans) => correctAnswers.includes(ans)) &&
          correctAnswers.every((ans) => answer.includes(ans));
      } else {
        isCorrect = answer === question.answer;
      }

      userResponses.push({
        questionId: question.id,
        selectedAnswer: answer,
        correctAnswer: question.answer,
        isCorrect: isCorrect,
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
      <div class="bg-white dark:bg-gray-900 p-6 rounded-lg dark:text-white text-center space-y-4">
        <h2 class="text-xl font-bold text-green-400">Quiz Completed 🎉</h2>
        <p>You got <strong>${correctCount}</strong> out of <strong>${total}</strong> correct.</p>
        <p>Your Score:</p>
        <p class="text-5xl font-bold text-green-600">${percentage}%</p>
        <p>Total Time Taken: <strong>${totalTimeTaken}</strong> second${
    totalTimeTaken !== 1 ? "s" : ""
  }</p>
      </div>
  `;

  // Pass the values to the submitResults function
  submitResults(correctCount, total, percentage, totalTimeTaken);
}

async function submitResults(
  correctCount,
  totalQuestions,
  percentage,
  totalTimeTaken
) {
  try {
    const payload = {
      correctAnswers: correctCount,
      totalQuestions: totalQuestions,
      percentage: percentage,
      totalTimeTaken: totalTimeTaken,
    };

    const response = await fetch("backend/php/submit-speedtest-results.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(payload),
    });

    const result = await response.json();

    if (result.status === "success") {
      console.log("Results submitted successfully.");
    } else {
      console.error("Failed to submit results:", result.message);
    }
  } catch (error) {
    console.error("Error submitting results:", error);
  }
}

document.addEventListener("DOMContentLoaded", fetchSpeedTest);
