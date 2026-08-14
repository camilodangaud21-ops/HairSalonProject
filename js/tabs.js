/* ══════════════════════════════════════════
   TABS
   Public page tab switching
   (Servicios / Equipo / Reseñas).
   ══════════════════════════════════════════ */

function switchTab(name) {
  document.querySelectorAll(".tab").forEach((t) => {
    t.classList.toggle("active", t.dataset.tab === name);
  });
  document.querySelectorAll(".panel").forEach((p) => {
    p.classList.toggle("active", p.id === "panel-" + name);
  });
}