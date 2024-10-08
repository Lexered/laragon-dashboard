function toggleTheme() {
  const html = document.documentElement;
  const currentTheme = html.getAttribute("data-theme") || "light";
  const newTheme = currentTheme === "dark" ? "light" : "dark";

  html.setAttribute("data-theme", newTheme);

  localStorage.setItem("theme", newTheme);

  toggleSlider = document.querySelector(".theme-toggle__content");
  if (currentTheme === "dark") {
    toggleSlider.classList.add("dark");
  }
  toggleSlider.classList.toggle("dark");
}

function applyStoredPreferences() {
  const storedTheme = localStorage.getItem("theme") || "light";
  document.documentElement.setAttribute("data-theme", storedTheme);
  toggleSlider = document.querySelector(".theme-toggle__content");
  if (storedTheme === "dark") {
    toggleSlider.classList.add("dark");
  }
}

document.addEventListener("DOMContentLoaded", applyStoredPreferences);

document.getElementById("theme-toggle").addEventListener("click", toggleTheme);

//---------------------------------------------------------------------------------

function closeAllPopups() {
  document.querySelectorAll(".popup-container.open").forEach((popup) => {
    popup.classList.remove("open");
  });
}

document.addEventListener("click", (e) => {
  const target = e.target;

  if (target.hasAttribute("data-popup-target")) {
    const popup = document.querySelector(
      `.popup-container[data-popup-content="${target.getAttribute(
        "data-popup-target"
      )}"]`
    );
    if (popup) {
      closeAllPopups();
      popup.classList.add("open");
    }
  }

  if (
    target.classList.contains("popup-container") ||
    target.classList.contains("close")
  ) {
    closeAllPopups();
  }
});

//---------------------------------------------------------------------------------
document.addEventListener("DOMContentLoaded", () => {
  const searchEmail = document.querySelector("#search");
  const emailItems = document.querySelectorAll(".email-item");

  searchEmail.addEventListener("input", () => {
    emailItems.forEach((email) => {
      const searchEmailValue = searchEmail.value.toLowerCase();

      if (email.textContent.toLowerCase().includes(searchEmailValue)) {
        email.style.display = "block";
      } else {
        email.style.display = "none";
      }
    });
  });
});
