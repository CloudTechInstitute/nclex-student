document
  .getElementById("employeeForm")
  .addEventListener("submit", async function (e) {
    e.preventDefault(); // Prevent default form submission

    let formData = new FormData(this);
    let toastMessage = "";
    let toastType = "";

    try {
      let response = await fetch("backend/employeeAccount.php", {
        method: "POST",
        body: formData,
      });

      let result;
      if (response.ok) {
        result = await response.json();
        toastMessage = result.message;
        toastType = "success";

        this.reset();
      } else {
        throw new Error("Unable to create Account. Please try again.");
      }
    } catch (error) {
      console.error("Error:", error);
      toastMessage = "Unable to create Account. Please try again.";
      toastType = "error";
    }

    // Close the modal
    let modal = document.getElementById("crud-modal");
    if (modal) {
      modal.classList.add("hidden");
      //   modal.setAttribute("aria-hidden", "true"); // Accessibility
    }

    // Simulate clicking the toggle button (if available)
    let modalToggle = document.querySelector(
      "[data-modal-toggle='crud-modal']"
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

    // Determine toast icon and color
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

    // Append toast notification to body
    document.body.appendChild(toast);

    // Auto-remove the toast after 3 seconds
    setTimeout(() => {
      toast.remove();
    }, 3000);
    fetchEmployeeAccount();
  });

// Fetch employee accounts

async function fetchEmployeeAccount() {
  let tableBody = document.getElementById("employeeTableBody");

  // Show the spinner inside the table body
  tableBody.innerHTML = `
          <tr>
              <td colspan="4" class="text-center py-4">
                  <svg aria-hidden="true" class="w-8 h-8 mx-auto text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                      <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                  </svg>
                  <span class="block text-gray-600 mt-2 font-semibold">Fetching employees...</span>
              </td>
          </tr>
      `;

  try {
    let response = await fetch("backend/fetch-employee.php");
    let result = await response.json();

    if (result.status === "success") {
      displayEmployees(result.data); // Pass the correct data
    } else {
      console.error("Error:", result.message);
      tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-gray-400">${result.message}</td></tr>`;
    }
  } catch (error) {
    console.error("Fetch error:", error);
    tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-gray-400">${error.message}</td></tr>`; // Corrected error handling
  }
}

// Function to display employees in the table
function displayEmployees(employees) {
  let tableBody = document.getElementById("employeeTableBody");

  if (!employees || employees.length === 0) {
    tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-gray-400">No employees found</td></tr>`;
    return;
  }

  // Get the current page filename without query parameters
  const currentPage = window.location.pathname.split("/").pop().split("?")[0];

  // Apply slice only if on index.php
  let employeesData =
    currentPage === "index.php" ? employees.slice(0, 5) : employees;

  let rows = employeesData
    .map(
      (employee) => `
      <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-200 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
        <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">${
          employee.name
        }</th>
        <td class="px-6 py-2">${employee.role}</td>
        <td class="px-6 py-2">${employee.date}</td>
        <td class="px-6 py-2">••••••</td> <!-- Masked password for security -->
        <td class="px-6 py-2 font-bold ${
          employee.status === "active" ? "text-green-600" : "text-red-600"
        }">${employee.status}</td>
      </tr>
    `
    )
    .join("");

  tableBody.innerHTML = rows;
}
// Fetch roles when the page loads
document.addEventListener("DOMContentLoaded", fetchEmployeeAccount);
