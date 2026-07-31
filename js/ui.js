/* ══════════════════════════════════════════
   UI.JS
   Interface logic: service data (currently
   static), card rendering, category/search
   filtering, and tab handling.

   NOTE: When the PHP backend exists, the plan is
   to replace the `servicios` array with a fetch()
   to /php/api/servicios.php (and similarly for
   stylists and reviews).
   ══════════════════════════════════════════ */

// ── CONFIG ──
const WHATSAPP = "573000000000"; // Cambia por el número real

// ── SERVICE DATA ──
const servicios = [
  {
    nombre: "Balayage del cabello total",
    cat: "Peluquería",
    precio: "$350.000",
    desde: true,
    tiempo: "3 h 40 min",
    popular: true,
    desc: "",
  },
  {
    nombre: "Blower",
    cat: "Peluquería",
    precio: "$50.000",
    desde: false,
    tiempo: "40 min",
    popular: true,
    desc: "Lavado, cepillado y planchado adicional",
  },
  {
    nombre: "Blower de dama",
    cat: "Peluquería",
    precio: "$40.000",
    desde: true,
    tiempo: "30 min",
    popular: true,
    desc: "",
  },
  {
    nombre: "Blower, peinado y más",
    cat: "Peluquería",
    precio: "$250.000",
    desde: false,
    tiempo: "2 h 30 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Blower y planchado",
    cat: "Peluquería",
    precio: "$50.000",
    desde: true,
    tiempo: "40 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Cambio de color",
    cat: "Peluquería",
    precio: "$150.000",
    desde: true,
    tiempo: "2 h",
    popular: false,
    desc: "",
  },
  {
    nombre: "Corte de caballero",
    cat: "Peluquería",
    precio: "$40.000",
    desde: true,
    tiempo: "30 min",
    popular: false,
    desc: "Corte de cabello clásico con tijera o con máquina",
  },
  {
    nombre: "Corte de dama",
    cat: "Peluquería",
    precio: "$60.000",
    desde: true,
    tiempo: "30 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Corte de dama y más",
    cat: "Peluquería",
    precio: "$120.000",
    desde: true,
    tiempo: "45 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Glicoproteína capilar",
    cat: "Peluquería",
    precio: "$200.000",
    desde: true,
    tiempo: "30 min",
    popular: false,
    desc: "Para cabello mediano y poca cantidad el proceso va...",
  },
  {
    nombre: "Mechas en todo el cabello",
    cat: "Peluquería",
    precio: "$250.000",
    desde: true,
    tiempo: "4 h 10 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Mechas parciales",
    cat: "Peluquería",
    precio: "$180.000",
    desde: true,
    tiempo: "2 h",
    popular: false,
    desc: "",
  },
  {
    nombre: "Peinados recogidos",
    cat: "Peluquería",
    precio: "$90.000",
    desde: true,
    tiempo: "1 h",
    popular: false,
    desc: "",
  },
  {
    nombre: "Proteína capilar",
    cat: "Peluquería",
    precio: "$180.000",
    desde: true,
    tiempo: "1 h 30 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Tintura o aplicación de color",
    cat: "Peluquería",
    precio: "$180.000",
    desde: false,
    tiempo: "1 h",
    popular: false,
    desc: "Este precio va dependiendo el largo del cabello y el cubrimiento...",
  },
  {
    nombre: "Trenzas para el cabello",
    cat: "Peluquería",
    precio: "$50.000",
    desde: false,
    tiempo: "10 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Depilación de axilas",
    cat: "Depilación",
    precio: "$30.000",
    desde: true,
    tiempo: "15 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Depilación de bikini",
    cat: "Depilación",
    precio: "$80.000",
    desde: true,
    tiempo: "40 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Depilación de bozo",
    cat: "Depilación",
    precio: "$20.000",
    desde: true,
    tiempo: "10 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Depilación de cejas",
    cat: "Depilación",
    precio: "$20.000",
    desde: true,
    tiempo: "10 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Depilación de media pierna",
    cat: "Depilación",
    precio: "$70.000",
    desde: true,
    tiempo: "20 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Depilación media pierna (alt)",
    cat: "Depilación",
    precio: "$60.000",
    desde: true,
    tiempo: "20 min",
    popular: false,
    desc: "Este servicio se presta depilación de media pierna",
  },
  {
    nombre: "Limpieza facial básica",
    cat: "Spa",
    precio: "$80.000",
    desde: true,
    tiempo: "40 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Masaje de relajación",
    cat: "Spa",
    precio: "$170.000",
    desde: true,
    tiempo: "40 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Masaje facial",
    cat: "Spa",
    precio: "$80.000",
    desde: true,
    tiempo: "40 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Manicura con baño de parafina",
    cat: "Manicure y pedicure",
    precio: "$120.000",
    desde: true,
    tiempo: "40 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Manicura en acrílico (opción 1)",
    cat: "Manicure y pedicure",
    precio: "$150.000",
    desde: true,
    tiempo: "1 h 30 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Manicura en acrílico (opción 2)",
    cat: "Manicure y pedicure",
    precio: "$180.000",
    desde: true,
    tiempo: "1 h 30 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Manicura en gel",
    cat: "Manicure y pedicure",
    precio: "$70.000",
    desde: true,
    tiempo: "30 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Manicura en uñas press on",
    cat: "Manicure y pedicure",
    precio: "$80.000",
    desde: true,
    tiempo: "30 min",
    popular: false,
    desc: "Todo se basa en el diseño y el largo de la uña",
  },
  {
    nombre: "Manicura sencilla",
    cat: "Manicure y pedicure",
    precio: "$30.000",
    desde: false,
    tiempo: "30 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Manicura y pedicura clásica",
    cat: "Manicure y pedicure",
    precio: "$70.000",
    desde: true,
    tiempo: "1 h",
    popular: false,
    desc: "",
  },
  {
    nombre: "Pedicura en gel",
    cat: "Manicure y pedicure",
    precio: "$80.000",
    desde: true,
    tiempo: "30 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Pedicura sencilla",
    cat: "Manicure y pedicure",
    precio: "$40.000",
    desde: false,
    tiempo: "40 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Retirada de uñas acrílicas",
    cat: "Manicure y pedicure",
    precio: "$50.000",
    desde: false,
    tiempo: "20 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Uñas press on y manicura",
    cat: "Manicure y pedicure",
    precio: "$150.000",
    desde: false,
    tiempo: "40 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Maquillaje para evento",
    cat: "Maquillaje",
    precio: "$160.000",
    desde: true,
    tiempo: "1 h 30 min",
    popular: false,
    desc: "",
  },
  {
    nombre: "Maquillaje y peinado para evento",
    cat: "Maquillaje",
    precio: "$250.000",
    desde: true,
    tiempo: "2 h",
    popular: false,
    desc: "Servicios prestados a domicilio y el de la prueba de novia...",
  },
  {
    nombre: "Pestañas postizas",
    cat: "Maquillaje",
    precio: "$30.000",
    desde: false,
    tiempo: "30 min",
    popular: false,
    desc: "Estas son pestañas corridas, todo varía de lo que deseas y el tipo de...",
  },
  {
    nombre: "Postura de pestañas",
    cat: "Maquillaje",
    precio: "$70.000",
    desde: true,
    tiempo: "30 min",
    popular: false,
    desc: "Es un servicio prestado de pestañas de punto a punto con efecto natural",
  },
];

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

// ── SERVICES RENDERING ──
function renderServicios(lista) {
  const grid = document.getElementById("services-grid");
  const noRes = document.getElementById("no-results");
  if (lista.length === 0) {
    grid.innerHTML = "";
    noRes.style.display = "block";
    return;
  }
  noRes.style.display = "none";
  grid.innerHTML = lista
    .map((s) => {
      const msg = encodeURIComponent(
        `Hola! Me interesa reservar el servicio: ${s.nombre} (${s.precio})`,
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
    })
    .join("");
}

// ── FILTERS (category + search) ──
let catActiva = "todos";
let busqueda = "";

function filtrar() {
  let lista = servicios;
  if (catActiva !== "todos") lista = lista.filter((s) => s.cat === catActiva);
  if (busqueda)
    lista = lista.filter((s) =>
      s.nombre.toLowerCase().includes(busqueda.toLowerCase()),
    );
  renderServicios(lista);
}

// ── TABS ──
function switchTab(nombre) {
  document.querySelectorAll(".tab").forEach((t) => {
    t.classList.toggle("active", t.dataset.tab === nombre);
  });
  document.querySelectorAll(".panel").forEach((p) => {
    p.classList.toggle("active", p.id === "panel-" + nombre);
  });
}

// ── EVENTS ──
document.addEventListener("DOMContentLoaded", () => {
  // Categorías
  document.querySelectorAll(".cat-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      document
        .querySelectorAll(".cat-btn")
        .forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      catActiva = btn.dataset.cat;
      filtrar();
    });
  });

  // Búsqueda
  document.getElementById("search-input").addEventListener("input", (e) => {
    busqueda = e.target.value;
    filtrar();
  });

  // Tabs
  document.querySelectorAll(".tab").forEach((tab) => {
    tab.addEventListener("click", () => switchTab(tab.dataset.tab));
  });

  // Render inicial
  filtrar();
});
