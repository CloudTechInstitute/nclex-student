let videos = []; // Global storage for fetched videos

async function fetchVideos() {
  let videoIcons = document.getElementById("videoIcons").querySelector("ul");
  let videoContainer = document.getElementById("default-tab-content");

  const urlParams = new URLSearchParams(window.location.search);
  const categoryId = urlParams.get("uuid");

  if (!categoryId) {
    videoIcons.innerHTML = `<p class="text-white">Error: Category ID is missing.</p>`;
    return;
  }

  try {
    let response = await fetch(
      `backend/php/fetch-videos.php?uuid=${categoryId}`
    );
    let result = await response.json();

    if (result.status === "success" && Array.isArray(result.data)) {
      videos = result.data;
      displayVideos(videos);
    } else {
      videoIcons.innerHTML = `<p class="text-white">${result.message}</p>`;
    }
  } catch (error) {
    console.error("Fetch error:", error);
    videoIcons.innerHTML = `<p class="text-white">Failed to load videos.</p>`;
  }
}

// Function to populate videos dynamically
function displayVideos(videos) {
  let videoIcons = document.getElementById("videoIcons").querySelector("ul");
  let videoContainer = document.getElementById("default-tab-content");

  videoIcons.innerHTML = ""; // Clear previous content
  videoContainer.innerHTML = ""; // Clear previous video player

  if (videos.length === 0) return; // No videos available

  videos.forEach((video, index) => {
    // Create a new list item
    const listItem = document.createElement("li");
    listItem.classList.add("border", "border-gray-300", "mb-3", "rounded-lg");

    // Create button element
    const button = document.createElement("button");
    button.classList.add("w-full", "p-4", "items-center");
    button.setAttribute("type", "button");
    button.setAttribute("role", "tab");
    button.setAttribute("aria-controls", `video-${video.id}`);
    button.setAttribute("aria-selected", "false");

    // Add click event to play video
    button.addEventListener("click", function () {
      playVideo(video.filename, video.name, `video-${video.id}`);
    });

    // Create thumbnail container
    const thumbnailDiv = document.createElement("div");
    thumbnailDiv.classList.add(
      "w-full",
      "h-36",
      "rounded-md",
      "overflow-hidden",
      "mb-2"
    );

    const thumbnail = document.createElement("img");
    thumbnail.src = "images/thumb1.jpg";
    thumbnail.alt = video.name;
    thumbnail.classList.add("w-full", "h-full", "object-cover");

    // Append image to thumbnail div
    thumbnailDiv.appendChild(thumbnail);

    // Create video title
    const title = document.createElement("span");
    title.classList.add(
      "block",
      "text-md",
      "text-black",
      "text-left",
      "font-semibold",
      "dark:text-white"
    );
    title.innerText = video.name;

    // Append everything to the button
    button.appendChild(thumbnailDiv);
    button.appendChild(title);

    // Append button to list item
    listItem.appendChild(button);

    // Append list item to videoIcons list
    videoIcons.appendChild(listItem);

    // Create video container in hidden tab content
    const videoTab = document.createElement("div");
    videoTab.classList.add(
      "hidden",
      "p-1",
      "rounded-lg",
      "bg-gray-50",
      "dark:bg-gray-800"
    );
    videoTab.id = `video-${video.id}`;
    videoTab.setAttribute("role", "tabpanel");

    const videoElement = document.createElement("video");
    videoElement.classList.add(
      "w-full",
      "rounded-lg",
      "bg-gray-50",
      "dark:bg-gray-800"
    );
    videoElement.setAttribute("playsinline", "");
    videoElement.setAttribute("controls", "");
    videoElement.setAttribute("controlsList", "nodownload");
    videoElement.setAttribute("oncontextmenu", "return false;");

    const sourceElement = document.createElement("source");
    sourceElement.src = `videos/${video.filename}`;
    sourceElement.type = "video/mp4";

    videoElement.appendChild(sourceElement);
    videoTab.appendChild(videoElement);
    videoContainer.appendChild(videoTab);
  });

  // Auto-play first video if available
  if (videos.length > 0) {
    playVideo(videos[0].filename, videos[0].name, `video-${videos[0].id}`);
  }
}

// Function to play video
function playVideo(filename, title, videoId) {
  // Pause all playing videos
  document.querySelectorAll("#default-tab-content video").forEach((video) => {
    video.pause();
  });

  // Hide all video sections
  document.querySelectorAll("#default-tab-content > div").forEach((div) => {
    div.classList.add("hidden");
  });

  // Show the selected video
  document.getElementById(videoId).classList.remove("hidden");
}

// Attach event listener on page load
document.addEventListener("DOMContentLoaded", () => {
  fetchVideos();
});
