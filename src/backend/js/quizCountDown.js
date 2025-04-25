document.addEventListener("DOMContentLoaded", () => {
  const countdownElement = document.getElementById("countdownTimer");
  if (!countdownElement) return;

  const durationInMinutes = parseInt(
    countdownElement.getAttribute("data-duration")
  );
  if (isNaN(durationInMinutes)) return;

  const STORAGE_KEY = "quiz_end_time";
  let endTime = localStorage.getItem(STORAGE_KEY);

  if (!endTime) {
    // First time loading — set end time
    endTime = Date.now() + durationInMinutes * 60 * 1000;
    localStorage.setItem(STORAGE_KEY, endTime);
  } else {
    endTime = parseInt(endTime);
  }

  function updateCountdown() {
    const now = Date.now();
    let timeLeft = Math.floor((endTime - now) / 1000); // in seconds

    if (timeLeft <= 0) {
      countdownElement.textContent = "Time is up!";
      localStorage.removeItem(STORAGE_KEY);
      clearInterval(timerInterval);
      const submitBtn = document.getElementById("submitBtn");
      if (submitBtn) submitBtn.click();
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
});
