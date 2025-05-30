const paymentForm = document.getElementById("paymentForm");
paymentForm.addEventListener("submit", payWithPaystack, false);

let PAYSTACK_PUBLIC_KEY = "";
let toastMessage = "";
let toastType = "";

// Fetch Paystack public key
async function fetchPaystackKey() {
  try {
    const res = await fetch("backend/php/get-paystack-key.php");
    const data = await res.json();
    PAYSTACK_PUBLIC_KEY = data.key;
    // Enable submit button if previously disabled
    paymentForm.querySelector("button[type='submit']").disabled = false;
  } catch (error) {
    console.error("Error fetching Paystack key:", error);
  }
}

// Initially disable submit until key is loaded
paymentForm.querySelector("button[type='submit']").disabled = true;
fetchPaystackKey();

async function payWithPaystack(e) {
  e.preventDefault();
  if (!PAYSTACK_PUBLIC_KEY) {
    await fetchPaystackKey(); // Ensure the key is available
  }

  let handler = PaystackPop.setup({
    key: PAYSTACK_PUBLIC_KEY,
    email: document.getElementById("email").value,
    subscriber: document.getElementById("name").value,
    phone: document.getElementById("phone").value,
    product: document.getElementById("product").value,
    duration: document.getElementById("duration").value,
    selectedPlan:
      document.querySelector('input[name="plans"]:checked')?.value || "",
    amount: document.querySelector("input[name='amount']").value * 100,
    currency: "GHS",
    ref: "GlobalNclex-" + Math.floor(Math.random() * 1000000000 + 1),

    onClose: function () {
      toastType = "error";
      toastMessage = "Payment window closed.";
      showToast();
    },

    callback: function (response) {
      verifyAndSavePayment(response.reference);
    },
  });

  handler.openIframe();
}

// Display toast notification
function showToast() {
  let toast = document.createElement("div");
  toast.className =
    "flex items-center w-full max-w-sm p-4 mb-4 text-gray-500 bg-gray-100 rounded-lg shadow-lg dark:text-gray-400 dark:bg-gray-700 fixed top-5 left-1/2 -translate-x-1/2";

  let iconColor =
    toastType === "success"
      ? "text-green-500 bg-green-100 dark:bg-green-800 dark:text-green-200"
      : "text-red-500 bg-red-100 dark:bg-red-800 dark:text-red-200";

  toast.innerHTML = `
    <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 ${iconColor}">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
        </svg>
    </div>
    <div class="ms-3 text-sm font-normal">${toastMessage}</div>
    <button type="button" class="ms-auto bg-white text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-100 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700"
        onclick="this.parentElement.remove()">
        <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
        </svg>
    </button>
  `;

  document.body.appendChild(toast);

  setTimeout(() => {
    toast.remove();
  }, 3000);
}

// Send verified payment to backend
async function verifyAndSavePayment(paystackRef) {
  let formData = new FormData(document.getElementById("paymentForm"));
  formData.append("paystack_ref", paystackRef);

  try {
    const res = await fetch("backend/php/process-payment.php", {
      method: "POST",
      body: formData,
    });

    const data = await res.json();

    if (data.status === "success") {
      toastType = "success";
      toastMessage = data.message;
      showToast();
      setTimeout(() => {
        window.location.href = "my-subscriptions.php";
      }, 1500);
    } else {
      toastType = "error";
      toastMessage = data.message;
      showToast();
    }
  } catch (error) {
    console.error("Error:", error);
    toastType = "error";
    toastMessage =
      "An error occurred while processing your payment. Please try again.";
    showToast();
  }
}
