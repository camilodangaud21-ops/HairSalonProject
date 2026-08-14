/* ══════════════════════════════════════════
   CATEGORIES
   Loads categories from the API and renders
   the filter buttons dynamically.
   ══════════════════════════════════════════ */

let catClass = {};
let catLabel = {};

async function loadCategories() {
  try {
    const res        = await fetch("/peluqueria/php/api/categories_api.php?action=all");
    const categories = await res.json();

    catClass = {};
    catLabel = {};
    categories.forEach((c) => {
      catClass[c.name] = c.css_class;
      catLabel[c.name] = c.label;
    });

    renderCategoryFilters(categories);
  } catch (error) {
    console.error("Error loading categories:", error);
  }
}

function renderCategoryFilters(categories) {
  const wrap = document.querySelector(".cat-scroll");
  const buttonsHtml = categories.map((c) =>
    `<button class="cat-btn" data-cat="${c.name}">${c.name}</button>`
  ).join("");

  wrap.innerHTML = `
    <button class="cat-btn active" data-cat="todos">Todos los servicios <span class="cat-count" id="cat-count-total">0</span></button>
    ${buttonsHtml}
  `;

  document.querySelectorAll(".cat-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      document.querySelectorAll(".cat-btn").forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      activeCategory = btn.dataset.cat;
      filterServices();
    });
  });
}