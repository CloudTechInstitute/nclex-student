async function fetchStudentStats() {
  try {
    let response = await fetch("backend/php/test.php");
    if (!response.ok) throw new Error("Network response was not ok");

    let result = await response.json();

    if (result.status === "success") {
      const { pass_rate, speed_rate, competitiveness } = result.data;

      // --- Render Pass Rate ---
      const passPercent = parseInt(pass_rate.average_score);
      document.getElementById("passRateBar").style.width = `${passPercent}%`;
      document.getElementById(
        "passRateLabel"
      ).innerText = `Pass Rate (${pass_rate.average_score})`;

      // --- Render Speed Rate as Text ---
      document.getElementById(
        "avgSpeed"
      ).innerText = `Avg Speed: ${speed_rate.average_speed}s`;
      document.getElementById(
        "criticalSpeed"
      ).innerText = `Critical Speed: ${speed_rate.critical_speed}s`;

      // --- Render Competitiveness Rank as Progress Bar ---
      const rankPercentage = parseFloat(competitiveness.rank_percentage); // Get the rank percentage directly from the response

      // Update competitiveness progress bar
      document.getElementById(
        "competitivenessBar"
      ).style.width = `${rankPercentage}%`;
      document.getElementById(
        "competitivenessText"
      ).innerText = `(${competitiveness.rank_position})`;
    } else {
      showStatsError("Failed to load statistics");
    }
  } catch (error) {
    console.error("Fetch error:", error);
    showStatsError("Error fetching statistics");
  }

  function showStatsError(message) {
    document.getElementById("passRateLabel").innerText = message;
    document.getElementById("avgSpeed").innerText = "";
    document.getElementById("criticalSpeed").innerText = "";
    document.getElementById("competitivenessText").innerText = message;
  }
}

document.addEventListener("DOMContentLoaded", fetchStudentStats);
