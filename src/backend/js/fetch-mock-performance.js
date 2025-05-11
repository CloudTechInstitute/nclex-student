async function fetchMockStats() {
  try {
    let response = await fetch("backend/php/mock-stats.php");
    if (!response.ok) throw new Error("Network response was not ok");

    let result = await response.json();

    if (result.status === "success") {
      const { pass_rate, speed_rate, competitiveness } = result.data;

      // --- Render Pass Rate ---
      const passPercent = parseInt(pass_rate.average_score);
      document.getElementById(
        "mockPassRateBar"
      ).style.width = `${passPercent}%`;
      document.getElementById(
        "mockPassRateLabel"
      ).innerText = `Pass Rate (${pass_rate.average_score})`;

      // --- Render Speed Rate as Text ---
      document.getElementById(
        "mockAvgSpeed"
      ).innerText = `Avg Speed: ${speed_rate.average_speed}s`;
      document.getElementById(
        "mockcriticalSpeed"
      ).innerText = `Critical Speed: ${speed_rate.critical_speed}s`;

      // --- Render Competitiveness Rank as Progress Bar ---
      const rankPercentage = parseFloat(competitiveness.rank_percentage); // Get the rank percentage directly from the response

      // Update competitiveness progress bar
      document.getElementById(
        "mockCompetitivenessBar"
      ).style.width = `${rankPercentage}%`;
      document.getElementById(
        "mockCompetitivenessText"
      ).innerText = `(${competitiveness.rank_position})`;
    } else {
      showStatsError("Failed to load statistics");
    }
  } catch (error) {
    console.error("Fetch error:", error);
    showStatsError("Error fetching statistics");
  }

  function showStatsError(message) {
    document.getElementById("mockPassRateLabel").innerText = message;
    document.getElementById("mockAvgSpeed").innerText = "";
    document.getElementById("mockcriticalSpeed").innerText = "";
    document.getElementById("mockCompetitivenessText").innerText = message;
  }
}

document.addEventListener("DOMContentLoaded", fetchMockStats);
