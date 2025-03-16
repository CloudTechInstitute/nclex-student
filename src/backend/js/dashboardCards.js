document
  .getElementById("searchButton")
  .addEventListener("click", function (event) {
    event.preventDefault(); // Prevent form submission

    let searchQuery = document.getElementById("default-search").value.trim();
    if (searchQuery === "") {
      fetchCategories(); // If search is empty, fetch all categories
      return;
    }

    searchCategories(searchQuery);
  });

// Function to search categories
async function searchCategories(query) {
  let categoriesDiv = document.getElementById("categoriesDiv");

  // Show the "Searching..." skeleton UI
  categoriesDiv.innerHTML = `
    <div class="block max-w-sm p-6 bg-white border border-gray-400 rounded-lg shadow-sm hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-500 dark:hover:bg-gray-700">
        <div role="status" class="max-w-sm animate-pulse">
            <div class="h-3 bg-gray-200 rounded-md dark:bg-green-200 w-24 mb-2"></div>
            <div class="h-8 bg-gray-200 rounded-md dark:bg-green-200 w-16 mb-2"></div>
            <span class="sr-only">Searching...</span>
        </div>
    </div>`;

  try {
    let response = await fetch(
      `backend/php/search-categories.php?query=${encodeURIComponent(query)}`
    );
    let result = await response.json();

    if (result.status === "success" && Array.isArray(result.data)) {
      displayCategories(result.data);
    } else {
      categoriesDiv.innerHTML = `<p class="text-center text-gray-400">${result.message}</p>`;
    }
  } catch (error) {
    console.error("Search error:", error);
    categoriesDiv.innerHTML = `<p class="text-center text-gray-400">${error.message}</p>`;
  }
}

async function fetchCategories() {
  let categoriesDiv = document.getElementById("categoriesDiv");

  // Show the skeleton UI inside the div
  categoriesDiv.innerHTML = `
    <div class="block max-w-sm p-6 bg-white border border-gray-400 rounded-lg shadow-sm hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-500 dark:hover:bg-gray-700">
        <div role="status" class="max-w-sm animate-pulse">
            <div class="h-3 bg-gray-200 rounded-md dark:bg-green-200 w-24 mb-2"></div>
            <div class="h-8 bg-gray-200 rounded-md dark:bg-green-200 w-16 mb-2"></div>
            <span class="sr-only">Loading...</span>
        </div>
    </div>`;

  try {
    let response = await fetch("backend/php/fetch-categories.php");
    let result = await response.json();

    if (result.status === "success" && Array.isArray(result.data)) {
      displayCategories(result.data);
    } else {
      console.error("Error:", result.message);
      categoriesDiv.innerHTML = `<p class="text-center text-gray-400">${result.message}</p>`;
    }
  } catch (error) {
    console.error("Fetch error:", error);
    categoriesDiv.innerHTML = `<p class="text-center text-gray-400">${error.message}</p>`;
  }
}

// Function to update the div with multiple category cards
function displayCategories(categories) {
  let categoriesDiv = document.getElementById("categoriesDiv");
  categoriesDiv.innerHTML = ""; // Clear previous content

  categories.forEach((category) => {
    let card = document.createElement("a");
    card.href = `expand.php?id=${category.id}`;
    card.className =
      "block max-w-sm px-6 py-10 bg-blue-100 border text-center border-gray-400 rounded-lg shadow-sm hover:bg-gray-100 dark:bg-green-700 dark:border-green-400 dark:hover:bg-green-600";

    card.innerHTML = `
      <p class="font-normal text-lg text-gray-700 dark:text-white uppercase">${category.category}</p>
      <h5 class="mb-2 text-sm font-semibold tracking-tight text-gray-700 dark:text-black">${category.description}</h5>
    `;

    categoriesDiv.appendChild(card);
  });
}

// Fetch stats when the page loads
document.addEventListener("DOMContentLoaded", fetchCategories);
