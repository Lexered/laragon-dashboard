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
