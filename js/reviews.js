/* ══════════════════════════════════════════
   REVIEWS
   Loads reviews and average rating from
   the API and renders them on the public page.
   ══════════════════════════════════════════ */

const REVIEWS_API = "/peluqueria/php/api/reviews_api.php";

async function loadReviews() {
  try {
    const [reviewsRes, summaryRes] = await Promise.all([
      fetch(`${REVIEWS_API}?action=featured`),
      fetch(`${REVIEWS_API}?action=summary`),
    ]);

    const reviews = await reviewsRes.json();
    const summary = await summaryRes.json();

    renderRatingSummary(summary);
    renderReviews(reviews);
  } catch (error) {
    console.error("Error loading reviews:", error);
  }
}

async function loadAllReviews() {
  try {
    const response = await fetch(`${REVIEWS_API}?action=all`);
    const reviews = await response.json();
    renderReviews(reviews);
    const button = document.getElementById("btn-ver-todas-resenas");
    if (button) button.style.display = "none";
  } catch (error) {
    console.error("Error loading all reviews:", error);
  }
}

function renderRatingSummary(summary) {
  if (!summary) return;

  document.querySelector(".rating-big .num").textContent =
    summary.count > 0 ? summary.average : "—";
  document.querySelector(".rating-big .count").textContent =
    `${summary.count} reseñas`;

  const total = summary.count || 1;
  const bars = [
    summary.stars[5],
    summary.stars[4],
    summary.stars[3],
    summary.stars[2],
    summary.stars[1],
  ];

  document.querySelectorAll(".bar-fill").forEach((bar, i) => {
    bar.style.width = Math.round((bars[i] / total) * 100) + "%";
  });

  document.querySelectorAll(".bar-num").forEach((el, i) => {
    el.textContent = bars[i];
  });
}

function renderReviews(reviews) {
  const list = document.getElementById("resenas-list");
  const button = document.getElementById("btn-ver-todas-resenas");
  if (button) button.style.display = reviews.length ? "block" : "none";

  if (!reviews.length) {
    list.innerHTML =
      '<p style="color:var(--muted); text-align:center; padding:20px;">No hay reseñas aún.</p>';
    return;
  }

  list.innerHTML = reviews
    .map(
      (r) => `
    <div class="resena-card">
      <div class="resena-header">
        <div class="resena-avatar">😊</div>
        <div>
          <div class="resena-name">${r.author_name}</div>
          <div class="resena-stars">${"★".repeat(r.rating)}${"☆".repeat(5 - r.rating)}</div>
          <div class="resena-date">${formatDate(r.created_at)}</div>
        </div>
      </div>
      ${r.comment ? `<p style="font-size:.85rem; color:var(--muted); margin-top:6px;">${r.comment}</p>` : ""}
    </div>
  `,
    )
    .join("");
}

function formatDate(dateStr) {
  if (!dateStr) return "";
  const date = new Date(dateStr);
  return date.toLocaleDateString("es-CO", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
}

// ── REVIEW FORM ──
let selectedRating = 0;

function openReviewForm() {
  const modal = document.getElementById("review-modal");
  if (!modal) return;

  selectedRating = 0;
  document.getElementById("review-rating").value = "0";
  document.getElementById("review-comment").value = "";
  document.getElementById("review-error").style.display = "none";
  document.querySelectorAll("#review-stars span").forEach((star) => {
    star.textContent = "☆";
  });
  modal.classList.add("active");
}

function closeReviewForm() {
  const modal = document.getElementById("review-modal");
  if (modal) modal.classList.remove("active");
}

document.addEventListener("DOMContentLoaded", () => {
  const stars = document.querySelectorAll("#review-stars span");
  if (!stars.length) return;

  stars.forEach((star) => {
    star.addEventListener("mouseover", () => {
      const val = +star.dataset.val;
      stars.forEach((s) => (s.textContent = +s.dataset.val <= val ? "★" : "☆"));
    });

    star.addEventListener("mouseout", () => {
      stars.forEach(
        (s) => (s.textContent = +s.dataset.val <= selectedRating ? "★" : "☆"),
      );
    });

    star.addEventListener("click", () => {
      selectedRating = +star.dataset.val;
      document.getElementById("review-rating").value = selectedRating;
    });
  });
});

async function submitReview() {
  const messageEl = document.getElementById("review-error");
  const rating = document.getElementById("review-rating").value;
  const comment = document.getElementById("review-comment").value.trim();

  if (!rating || rating == 0) {
    messageEl.style.color = "#e55";
    messageEl.style.display = "block";
    messageEl.textContent = "Selecciona una calificación.";
    return;
  }

  try {
    const res = await fetch(`${REVIEWS_API}?action=createClient`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ rating, comment }),
    });

    if (res.status === 401) {
      messageEl.style.color = "#e55";
      messageEl.style.display = "block";
      messageEl.textContent = "Tu sesión expiró. Inicia sesión de nuevo.";
      return;
    }

    const data = await res.json();
    messageEl.style.display = "block";

    if (data.success) {
      closeReviewForm();
      document.getElementById("review-comment").value = "";
      selectedRating = 0;
      document
        .querySelectorAll("#review-stars span")
        .forEach((s) => (s.textContent = "☆"));
      loadReviews();
    } else {
      messageEl.style.color = "#e55";
      messageEl.textContent = data.message || "No se pudo enviar la reseña.";
    }
  } catch (err) {
    messageEl.style.color = "#e55";
    messageEl.style.display = "block";
    messageEl.textContent = "Error de conexión, intenta de nuevo.";
  }
}
