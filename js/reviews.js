/* ══════════════════════════════════════════
   REVIEWS
   Loads reviews and average rating from
   the API and renders them on the public page.
   ══════════════════════════════════════════ */

const REVIEWS_API = "/peluqueria/php/api/reviews_api.php";

async function loadReviews() {
  try {
    const [reviewsRes, avgRes] = await Promise.all([
      fetch(`${REVIEWS_API}?action=all`),
      fetch(`${REVIEWS_API}?action=average`)
    ]);

    const reviews = await reviewsRes.json();
    const avg     = await avgRes.json();

    renderRatingSummary(avg);
    renderReviews(reviews);
  } catch (error) {
    console.error("Error loading reviews:", error);
  }
}

function renderRatingSummary(avg) {
  if (!avg || !avg.total) return;

  document.querySelector(".rating-big .num").textContent  = avg.average ?? "—";
  document.querySelector(".rating-big .count").textContent = `${avg.total} reseñas`;

  const total = avg.total || 1;
  const bars  = [avg.five, avg.four, avg.three, avg.two, avg.one];

  document.querySelectorAll(".bar-fill").forEach((bar, i) => {
    bar.style.width = Math.round((bars[i] / total) * 100) + "%";
  });

  document.querySelectorAll(".bar-num").forEach((el, i) => {
    el.textContent = bars[i];
  });
}

function renderReviews(reviews) {
  const list = document.getElementById("resenas-list");
  if (!reviews.length) {
    list.innerHTML = '<p style="color:var(--muted); text-align:center; padding:20px;">No hay reseñas aún.</p>';
    return;
  }

  list.innerHTML = reviews.map(r => `
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
  `).join("");
}

function formatDate(dateStr) {
  if (!dateStr) return "";
  const date = new Date(dateStr);
  return date.toLocaleDateString("es-CO", { year: "numeric", month: "long", day: "numeric" });
}