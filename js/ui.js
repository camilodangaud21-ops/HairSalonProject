/* ══════════════════════════════════════════
   UI.JS
   Interface logic: service data (from API),
   card rendering, category/search
   filtering, and tab handling.
   ══════════════════════════════════════════ */

// ── CONFIG ──
const WHATSAPP = "573000000000";

const catClass = {
  Peluquería: "cat-pelq",
  "Manicure y pedicure": "cat-mani",
  Maquillaje: "cat-maqui",
  Spa: "cat-spa",
  Depilación: "cat-depi",
};

const catLabel = {
  Peluquería: "PELUQUERÍA",
  "Manicure y pedicure": "MANICURE",
  Maquillaje: "MAQUILLAJE",
  Spa: "SPA",
  Depilación: "DEPILACIÓN",
};

// ── SERVICE DATA ──
let services = [];

async function loadServices() {
  try {
    const res  = await fetch('/peluqueria/php/api/services_api.php?action=all');
    const data = await res.json();

    services = data.map(s => ({
      nombre:  s.name,
      cat:     s.category,
      precio:  '$' + Number(s.price).toLocaleString('es-CO'),
      desde:   s.from_of == 1,
      tiempo:  s.duration,
      popular: s.popular == 1,
      desc:    s.description
    }));

    filterServices();
  } catch (error) {
    console.error('Error loading services:', error);
  }
}

// ── RENDERING ──
function renderServices(list) {
  const grid  = document.getElementById("services-grid");
  const noRes = document.getElementById("no-results");
  if (list.length === 0) {
    grid.innerHTML = "";
    noRes.style.display = "block";
    return;
  }
  noRes.style.display = "none";
  grid.innerHTML = list.map((s) => {
    const msg = encodeURIComponent(
      `Hola! Me interesa reservar el servicio: ${s.nombre} (${s.precio})`
    );
    return `
    <div class="service-card">
      <div class="service-thumb ${catClass[s.cat] || "cat-pelq"}">
        ${catLabel[s.cat] || s.cat}
      </div>
      <div class="service-body">
        ${s.popular ? '<span class="service-popular">⭐ Popular</span>' : ""}
        <div class="service-name">${s.nombre}</div>
        <div class="service-cat">${s.cat}</div>
        ${s.desc ? `<div class="service-desc">${s.desc}</div>` : ""}
        <div class="service-footer">
          <div class="service-price">
            ${s.desde ? '<span class="desde">Precio a partir de</span>' : ""}
            ${s.precio}
          </div>
          <div>
            <div class="service-time">🕐 ${s.tiempo}</div>
          </div>
        </div>
        <a class="btn-reservar" href="https://wa.me/${WHATSAPP}?text=${msg}" target="_blank" style="margin-top:8px;text-align:center;display:block;">
          Reservar
        </a>
      </div>
    </div>`;
  }).join("");
}

// ── FILTERS ──
let activeCategory = "todos";
let searchQuery    = "";

function filterServices() {
  let list = services;
  if (activeCategory !== "todos") list = list.filter((s) => s.cat === activeCategory);
  if (searchQuery) list = list.filter((s) =>
    s.nombre.toLowerCase().includes(searchQuery.toLowerCase())
  );
  renderServices(list);
}

// ── TABS ──
function switchTab(name) {
  document.querySelectorAll(".tab").forEach((t) => {
    t.classList.toggle("active", t.dataset.tab === name);
  });
  document.querySelectorAll(".panel").forEach((p) => {
    p.classList.toggle("active", p.id === "panel-" + name);
  });
}

// ── EVENTS ──
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".cat-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      document.querySelectorAll(".cat-btn").forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      activeCategory = btn.dataset.cat;
      filterServices();
    });
  });

  document.getElementById("search-input").addEventListener("input", (e) => {
    searchQuery = e.target.value;
    filterServices();
  });

  document.querySelectorAll(".tab").forEach((tab) => {
    tab.addEventListener("click", () => switchTab(tab.dataset.tab));
  });

  loadServices();
});