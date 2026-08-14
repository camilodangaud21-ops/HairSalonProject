/* ══════════════════════════════════════════
   SERVICES
   Loads services from the API, renders
   cards, and handles search/category
   filtering.
   ══════════════════════════════════════════ */

const WHATSAPP = typeof SITE_WHATSAPP !== "undefined" ? SITE_WHATSAPP : "573000000000";

let services       = [];
let activeCategory = "todos";
let searchQuery    = "";

async function loadServices() {
  try {
    const res  = await fetch("/peluqueria/php/api/services_api.php?action=all");
    const data = await res.json();

    services = data.map((s) => ({
      nombre:  s.name,
      cat:     s.category,
      precio:  "$" + Number(s.price).toLocaleString("es-CO"),
      desde:   s.from_of == 1,
      tiempo:  s.duration,
      popular: s.popular == 1,
      desc:    s.description,
    }));

    const countEl = document.getElementById("cat-count-total");
    if (countEl) countEl.textContent = services.length;

    filterServices();
  } catch (error) {
    console.error("Error loading services:", error);
  }
}

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

function filterServices() {
  let list = services;
  if (activeCategory !== "todos") list = list.filter((s) => s.cat === activeCategory);
  if (searchQuery) list = list.filter((s) =>
    s.nombre.toLowerCase().includes(searchQuery.toLowerCase())
  );
  renderServices(list);
}