/* MAIN: public-page bootstrap and search. */

// Wipe the field and re-arm the readonly guard when the user is not typing.
function guardServiceSearch() {
  const input = document.getElementById("search-input");
  if (!input || input.matches(":focus")) return;
  input.value = "";
  input.setAttribute("readonly", "readonly");
  searchQuery = "";
}

// Unlock the field only after a real user interaction.
function armSearchInputUnlock() {
  const input = document.getElementById("search-input");
  if (!input) return;

  const unlock = () => input.removeAttribute("readonly");
  input.addEventListener("pointerdown", unlock, { once: true });
  input.addEventListener("touchstart", unlock, { once: true });
  input.addEventListener("keydown", unlock, { once: true });
}

// Clear delayed autofill values after the page becomes visible.
function sweepServiceSearch() {
  guardServiceSearch();
  armSearchInputUnlock();
  [50, 150, 300, 600, 1000].forEach((delay) => {
    setTimeout(guardServiceSearch, delay);
  });
}

document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("search-input");

  if (searchInput) {
    searchInput.addEventListener("input", (e) => {
      searchQuery = e.target.value;
      filterServices();
    });

    sweepServiceSearch();
  }

  document.querySelectorAll(".tab").forEach((tab) => {
    tab.addEventListener("click", () => switchTab(tab.dataset.tab));
  });

  loadCategories().then(loadServices);
  loadReviews();
});

// Re-run the guard after back/forward navigation and bfcache restores.
window.addEventListener("pageshow", sweepServiceSearch);

// Re-arm the guard when returning to this browser tab.
document.addEventListener("visibilitychange", () => {
  if (document.visibilityState === "visible") {
    sweepServiceSearch();
  }
});
