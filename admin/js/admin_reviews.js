function escapeHtml(value) {
  return String(value ?? "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

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
      <td>${escapeHtml(r.author_name)}</td>
      <td>${"★".repeat(r.rating)}${"☆".repeat(5 - r.rating)}</td>
      <td style="max-width:200px; font-size:.8rem;">${escapeHtml(r.comment || "—")}</td>
      <td>${r.featured == 1 ? "⭐ Sí" : "—"}</td>
      <td>${r.active == 1 ? "✅ Activa" : "🚫 Oculta"}</td>
      <td style="font-size:.8rem;">${formatReviewDate(r.created_at)}</td>
      <td>
        <button onclick="editReview(${r.id})" class="btn-small">✏️ Editar</button>
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

let editingReviewId = null;

function openReviewForm() {
  editingReviewId = null;
  document.getElementById("review-modal-title").textContent = "Nueva reseña";
  document.getElementById("review-id").value       = "";
  document.getElementById("review-author").value   = "";
  document.getElementById("review-rating").value   = "5";
  document.getElementById("review-comment").value  = "";
  document.getElementById("review-featured").checked = false;
  document.getElementById("review-error").style.display = "none";
  document.getElementById("review-modal").classList.add("active");
}

function editReview(id) {
  const r = adminReviews.find(x => x.id == id);
  if (!r) return;
  editingReviewId = id;
  document.getElementById("review-modal-title").textContent = "Editar reseña";
  document.getElementById("review-id").value       = r.id;
  document.getElementById("review-author").value   = r.author_name;
  document.getElementById("review-rating").value   = r.rating;
  document.getElementById("review-comment").value  = r.comment ?? "";
  document.getElementById("review-featured").checked = r.featured == 1;
  document.getElementById("review-error").style.display = "none";
  document.getElementById("review-modal").classList.add("active");
}

function closeReviewForm() {
  document.getElementById("review-modal").classList.remove("active");
}

async function saveReview() {
  const errorEl = document.getElementById("review-error");
  errorEl.style.display = "none";

  const payload = {
    author_name: document.getElementById("review-author").value.trim(),
    rating:      document.getElementById("review-rating").value,
    comment:     document.getElementById("review-comment").value.trim(),
    featured:    document.getElementById("review-featured").checked ? 1 : 0,
  };

  if (!payload.author_name || !payload.comment) {
    errorEl.textContent   = "Nombre y comentario son obligatorios.";
    errorEl.style.display = "block";
    return;
  }

  try {
    const url = editingReviewId
      ? `${ADMIN_REVIEWS_API}?action=update&id=${editingReviewId}`
      : `${ADMIN_REVIEWS_API}?action=create`;

    const res  = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
    const data = await res.json();

    if (data.success) {
      closeReviewForm();
      showAlert("Reseña guardada correctamente. Recargando…", "success", { reload: true });
    } else {
      errorEl.textContent   = data.message || "No se pudo guardar la reseña.";
      errorEl.style.display = "block";
    }
  } catch (err) {
    errorEl.textContent   = "Error de conexión, intenta de nuevo.";
    errorEl.style.display = "block";
  }
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