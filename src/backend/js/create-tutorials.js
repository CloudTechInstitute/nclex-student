document
  .getElementById("tutorialForm")
  .addEventListener("submit", async function (e) {
    e.preventDefault(); // Prevent default form submission

    let formData = new FormData(this);
    let topics = [];

    // Get all checked checkboxes
    document
      .querySelectorAll("input[name='topics[]']:checked")
      .forEach((checkbox) => {
        topics.push(checkbox.value);
      });

    // Append topics as a single string separated by commas
    formData.append("topics", topics.join(","));

    let toastMessage = "";
    let toastType = "";

    try {
      let response = await fetch("backend/php/create-tutorial.php", {
        method: "POST",
        body: formData,
      });

      let result;
      if (response.ok) {
        result = await response.json();
        toastMessage = result.message;
        toastType = "success";

        this.reset();
        fetchTutorials();
      } else {
        throw new Error("Unable to create tutorial. Please try again.");
      }
    } catch (error) {
      console.error("Error:", error);
      toastMessage = "Unable to create tutorial. Please try again.";
      toastType = "error";
    }

    // Close the modal
    let modal = document.getElementById("tutorial-modal");
    if (modal) {
      modal.classList.add("hidden");
      modal.setAttribute("aria-hidden", "true"); // Accessibility
    }

    // Simulate clicking the toggle button (if available)
    let modalToggle = document.querySelector(
      "[data-modal-toggle='tutorial-modal']"
    );
    if (modalToggle) {
      modalToggle.click();
    }

    // Remove backdrop if still present
    let backdrop = document.querySelector(
      ".fixed.inset-0.bg-black.bg-opacity-50"
    );
    if (backdrop) {
      backdrop.remove();
    }

    // Restore scrolling to the body
    document.body.classList.remove("overflow-hidden");

    // Create toast notification dynamically
    let toast = document.createElement("div");
    toast.className =
      "flex items-center w-full max-w-sm p-4 mb-4 text-gray-500 bg-gray-100 rounded-lg shadow-lg dark:text-gray-400 dark:bg-gray-700 fixed top-5 left-1/2 -translate-x-1/2";

    let iconColor =
      toastType === "success"
        ? "text-green-500 bg-green-100 dark:bg-green-800 dark:text-green-200"
        : "text-red-500 bg-red-100 dark:bg-red-800 dark:text-red-200";

    toast.innerHTML = `
      <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 ${iconColor}">
          <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
          </svg>
      </div>
      <div class="ms-3 text-sm font-normal">${toastMessage}</div>
      <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700"
          onclick="this.parentElement.remove()">
          <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
          </svg>
      </button>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
      toast.remove();
    }, 3000);
  });

document
  .getElementById("searchTutorialButton")
  .addEventListener("click", function (event) {
    event.preventDefault(); // Prevent form submission

    let searchQuery = document.getElementById("default-search").value.trim();
    if (searchQuery === "") {
      fetchTutorials(); // If search is empty, fetch all tutorials
      return;
    }

    searchTutorials(searchQuery);
  });

// Function to search tutorials
async function searchTutorials(query) {
  let tutorialsDiv = document.getElementById("tutorialsDiv");

  // Show the "Searching..." skeleton UI
  tutorialsDiv.innerHTML = `
    <div class="block max-w-sm p-6 bg-white border border-gray-400 rounded-lg shadow-sm hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-500 dark:hover:bg-gray-700">
        <div role="status" class="max-w-sm animate-pulse">
            <div class="h-3 bg-gray-200 rounded-md dark:bg-green-200 w-24 mb-2"></div>
            <div class="h-8 bg-gray-200 rounded-md dark:bg-green-200 w-16 mb-2"></div>
            <span class="sr-only">Searching...</span>
        </div>
    </div>`;

  try {
    let response = await fetch(
      `backend/php/search-tutorials.php?query=${encodeURIComponent(query)}`
    );
    let result = await response.json();

    if (result.status === "success" && Array.isArray(result.data)) {
      displayTutorials(result.data);
    } else {
      tutorialsDiv.innerHTML = `<p class="text-center text-gray-400">${result.message}</p>`;
    }
  } catch (error) {
    console.error("Search error:", error);
    tutorialsDiv.innerHTML = `<p class="text-center text-gray-400">${error.message}</p>`;
  }
}

async function fetchTutorials() {
  try {
    let response = await fetch("backend/php/fetch-tutorials.php");
    let result = await response.json();

    if (result.status === "success") {
      displayTutorials(result.data);
    } else {
      console.error("Failed to fetch tutorials:", result.message);
    }
  } catch (error) {
    console.error("Error fetching tutorials:", error);
  }
}

function displayTutorials(tutorials) {
  let tutorialsDiv = document.getElementById("tutorialsDiv");
  tutorialsDiv.innerHTML = ""; // Clear previous content

  tutorials.forEach((tutorial) => {
    let card = document.createElement("a");
    card.href = `tutorial-expand.php?uuid=${tutorial.uuid}`;
    card.className =
      "relative block max-w-sm px-6 py-10 bg-white border text-center border-gray-400 rounded-lg shadow-sm hover:bg-gray-100 dark:bg-green-700 dark:border-green-400 dark:hover:bg-green-600";

    card.innerHTML = `
        <p class="font-normal text-sm text-blue-700 dark:text-white uppercase">${
          tutorial.title
        }</p>
        ${
          tutorial.status === "scheduled"
            ? `
            <div class="absolute bottom-2 right-2 text-gray-500 dark:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />
                </svg>
            </div>
        `
            : ""
        }
      `;

    tutorialsDiv.appendChild(card);
  });
}

// Call function to fetch tutorials on page load
document.addEventListener("DOMContentLoaded", fetchTutorials);
