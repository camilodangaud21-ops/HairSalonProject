/* ══════════════════════════════════════════
   ADMIN REVIEWS
   Table for moderating reviews.
══════════════════════════════════════════ */

const ADMIN_REVIEWS_API = "/peluqueria/php/api/reviews_api.php";

let adminReviews = [];

async function loadReviews() {
    const res = await fetch(`${ADMIN_REVIEWS_API}?action=allAdmin`);
    adminReviews = await res.json();
    renderReviewsTable(adminReviews);
}

function renderReviewsTable(reviews) {
    const tBody = document.getElementById("reviews-tbody");
    tBody.innerHTML = reviews.map(r => `
    <tr style="${r.active == 0 ? "opacity:.5;" : ""}">
      <td>${r.author_name}</td>
      <td>${"★".repeat(r.rating)}${"☆".repeat(5 - r.rating)}</td>
      <td style="max-width:200px; font-size:.8rem;">${r.comment ?? "—"}</td>
      <td>${r.featured == 1 ? "⭐ Sí" : "—"}</td>
      <td>${r.active == 1 ? "✅ Activa" : "🚫 Oculta"}</td>
      <td style="font-size:.8rem;">${formatReviewDate(r.created_at)}</td>
      <td>
        <button onclick="toggleReviewFeatured(${r.id}, ${r.featured == 1 ? 0 : 1})" class="btn-small">
          ${r.featured == 1 ? "★ Quitar destacado" : "⭐ Destacar"}
        </button>
        <button onclick="toggleReviewActive(${r.id}, ${r.active == 1 ? 0 : 1})" class="btn-small">
          ${r.active == 1 ? "🚫 Ocultar" : "✅ Mostrar"}
        </button>
        <button onclick="deleteReview(${r.id})" class="btn-small btn-danger">🗑️ Eliminar</button>
      </td>
    </tr>
  `).join("");

}

function formatReviewDate(dateStr) {
  if (!dateStr) return "—";
  const date = new Date(dateStr);
  return date.toLocaleDateString("es-CO", { year: "numeric", month: "short", day: "numeric" });

}

async function toggleReviewFeatured(id, newValue) {
  try {
    const res  = await fetch(`${ADMIN_REVIEWS_API}?action=toggleFeatured&id=${id}`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ featured: newValue }),
    });
    const data = await res.json();
    if (data.success) {
      showAlert("Destacado actualizado. Recargando…", "success", { reload: true });
    } else {
      showAlert(data.message || "No se pudo cambiar el destacado.", "error");
    }
  } catch (err) {
    showAlert("Error de conexión, intenta de nuevo.", "error");
  }
}

async function toggleReviewActive(id, newActive) {
  try {
    const res = await fetch(`${ADMIN_REVIEWS_API}?action=toggleActive&id=${id}`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ active: newActive }),
    });
    const data = await res.json();
    if (data.success) {
      showAlert("Estado de la reseña actualizado. Recargando…", "success", { reload: true });
    } else {
      showAlert(data.message || "No se pudo cambiar el estado.", "error");
    }
  } catch (err) {
    showAlert("Error de conexión, intenta de nuevo.", "error");
  }
}

async function deleteReview(id) {
  if (!confirm("¿Eliminar esta reseña permanentemente?")) return;

  try {
    const res  = await fetch(`${ADMIN_REVIEWS_API}?action=delete&id=${id}`, { method: "POST" });
    const data = await res.json();
    if (data.success) {
      showAlert("Reseña eliminada correctamente. Recargando…", "success", { reload: true });
    } else {
      showAlert(data.message || "No se pudo eliminar.", "error");
    }
  } catch (err) {
    showAlert("Error de conexión, intenta de nuevo.", "error");
  }
}