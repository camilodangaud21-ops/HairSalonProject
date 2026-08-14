/* ══════════════════════════════════════════
   ADMIN CORE
   Tab switching and app bootstrap.
   ══════════════════════════════════════════ */

function switchAdminTab(name) {
  document.querySelectorAll(".tab").forEach((t) => {
    t.classList.toggle("active", t.dataset.admintab === name);
  });
  document.querySelectorAll(".admin-panel").forEach((p) => {
    p.classList.toggle("active", p.id === "admin-panel-" + name);
  });
}

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".tab").forEach((tab) => {
    tab.addEventListener("click", () => switchAdminTab(tab.dataset.admintab));
  });

  loadServices();
  loadSettings();
  loadTeam();
  loadCategories();
});