/* MAIN: public-page bootstrap and search. */
function resetServiceSearch() {
  const input = document.getElementById("search-input");
  if (!input) return;
  input.value = "";
  input.removeAttribute("readonly");
  searchQuery = "";
}

document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("search-input");

  if (searchInput) {
    searchInput.value = "";
    searchInput.addEventListener("focus", () => {
      searchInput.removeAttribute("readonly");
    }, { once: true });

    searchInput.addEventListener("input", (e) => {
      searchQuery = e.target.value;
      filterServices();
    });

    // Password managers/browser autofill can run after DOMContentLoaded.
    setTimeout(resetServiceSearch, 150);
  }

  document.querySelectorAll(".tab").forEach((tab) => {
    tab.addEventListener("click", () => switchTab(tab.dataset.tab));
  });

  loadCategories().then(loadServices);
  loadReviews();
});

window.addEventListener("pageshow", () => {
  const input = document.getElementById("search-input");
  if (input && !input.matches(":focus")) {
    input.value = "";
    searchQuery = "";
  }
});