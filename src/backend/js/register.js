// Move showToast out here
function showToast(message, type) {
  let toast = document.createElement("div");
  toast.className =
    "flex items-center w-full max-w-sm p-4 mb-4 text-gray-500 bg-gray-100 rounded-lg shadow-lg dark:text-gray-400 dark:bg-gray-700 fixed top-5 left-1/2 -translate-x-1/2";

  let iconColor =
    type === "success"
      ? "text-green-500 bg-green-100 dark:bg-green-800 dark:text-green-200"
      : "text-red-500 bg-red-100 dark:bg-red-800 dark:text-red-200";

  toast.innerHTML = `
        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 ${iconColor}">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
            </svg>
        </div>
        <div class="ms-3 text-sm font-normal">${message}</div>
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
}

document
  .getElementById("registerForm")
  .addEventListener("submit", async function (e) {
    e.preventDefault(); // Prevent default form submission

    let formData = new FormData(this);
    let toastMessage = "";
    let toastType = "";
    let password = formData.get("password").trim();
    let confirmPassword = formData.get("cpassword").trim();
    let registerBtn = document.getElementById("registerBtn");
    let originalText = registerBtn.innerHTML;

    if (password.length < 8) {
      toastMessage = "Password must be at least 8 characters long.";
      toastType = "error";
      showToast(toastMessage, toastType);
      return;
    }

    if (password !== confirmPassword) {
      toastMessage = "Passwords do not match. Please try again.";
      toastType = "error";
      showToast(toastMessage, toastType);
      return;
    }

    registerBtn.disabled = true;
    registerBtn.innerHTML = `
      <svg aria-hidden="true" role="status" class="inline w-4 h-4 me-3 text-white dark:text-gray-800 animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
      <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
      </svg> Please wait...
    `;

    try {
      let response = await fetch("backend/php/register.php", {
        method: "POST",
        body: formData,
      });

      let result;
      if (response.ok) {
        result = await response.json();
        toastMessage = result.message;
        toastType = result.status === "success" ? "success" : "error";
        if (toastType === "success") {
          this.reset();
        }
      } else {
        try {
          result = await response.json();
          toastMessage = result.message;
        } catch {
          toastMessage =
            result.message || "An error occurred. Please try again.";
        }
        toastType = "error";
      }
    } catch (error) {
      console.error("Error:", error);
      toastMessage = error.message || "Network error occurred.";
      toastType = "error";
    }

    registerBtn.disabled = false;
    registerBtn.innerHTML = originalText;

    showToast(toastMessage, toastType);
  });
