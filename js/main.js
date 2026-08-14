/* ══════════════════════════════════════════
   MAIN
   App bootstrap: wires up search input and
   tab clicks, then loads categories and
   services (in that order, since services
   rendering depends on catClass/catLabel).
   ══════════════════════════════════════════ */

document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("search-input").addEventListener("input", (e) => {
    searchQuery = e.target.value;
    filterServices();
  });

  document.querySelectorAll(".tab").forEach((tab) => {
    tab.addEventListener("click", () => switchTab(tab.dataset.tab));
  });

  loadCategories().then(loadServices);
});