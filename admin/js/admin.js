// Admin JS: manage services table and settings
const API = "/peluqueria/php/api/services_api.php";

let services = [];
let editingId = null;

async function loadServices() {
  const res = await fetch(`${API}?action=allAdmin`);
  services = await res.json();
  renderTable();
}

function renderTable() {
  const tbody = document.getElementById("services-tbody");
  tbody.innerHTML = services
    .map(
      (s) => `
    <tr style="${s.active == 0 ? "opacity:.5;" : ""}">
      <td>${s.name}</td>
      <td>${s.category}</td>
      <td>$${Number(s.price).toLocaleString("es-CO")}${s.from_of == 1 ? " (desde)" : ""}</td>
      <td>${s.duration}</td>
      <td>${s.popular == 1 ? "⭐" : "—"}</td>
      <td>${s.active == 1 ? "✅ Activo" : "🚫 Oculto"}</td>
      <td>
        <button onclick="editService(${s.id})" class="btn-small">✏️ Editar</button>
        <button onclick="toggleActive(${s.id}, ${s.active == 1 ? 0 : 1})" class="btn-small">
          ${s.active == 1 ? "🚫 Ocultar" : "✅ Mostrar"}
        </button>
        <button onclick="deleteService(${s.id})" class="btn-small btn-danger">🗑️ Eliminar</button>
      </td>
    </tr>
  `,
    )
    .join("");
}

async function toggleActive(id, newActive) {
  try {
    const res = await fetch(`${API}?action=toggleActive&id=${id}`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ active: newActive }),
    });
    const data = await res.json();
    if (data.success) {
      loadServices();
    } else {
      alert(data.message || "No se pudo cambiar el estado.");
    }
  } catch (err) {
    alert("Error de conexión, intenta de nuevo.");
  }
}

function openServiceForm() {
  editingId = null;
  document.getElementById("service-modal-title").textContent = "Nuevo servicio";
  document.getElementById("service-id").value = "";
  document.getElementById("service-name").value = "";
  document.getElementById("service-category").value = "Peluquería";
  document.getElementById("service-price").value = "";
  document.getElementById("service-from-of").checked = false;
  document.getElementById("service-duration").value = "";
  document.getElementById("service-popular").checked = false;
  document.getElementById("service-description").value = "";
  document.getElementById("service-image").value = "";
  document.getElementById("service-error").style.display = "none";
  document.getElementById("service-modal").style.display = "flex";
}

function closeServiceForm() {
  document.getElementById("service-modal").style.display = "none";
}

function editService(id) {
  const s = services.find((x) => x.id == id);
  if (!s) return;
  editingId = id;
  document.getElementById("service-modal-title").textContent =
    "Editar servicio";
  document.getElementById("service-id").value = s.id;
  document.getElementById("service-name").value = s.name;
  document.getElementById("service-category").value = s.category;
  document.getElementById("service-price").value = s.price;
  document.getElementById("service-from-of").checked = s.from_of == 1;
  document.getElementById("service-duration").value = s.duration;
  document.getElementById("service-popular").checked = s.popular == 1;
  document.getElementById("service-description").value = s.description || "";
  document.getElementById("service-image").value = s.image || "";
  document.getElementById("service-error").style.display = "none";
  document.getElementById("service-modal").style.display = "flex";
}

async function saveService() {
  const errorEl = document.getElementById("service-error");
  const payload = {
    name: document.getElementById("service-name").value.trim(),
    category: document.getElementById("service-category").value,
    price: document.getElementById("service-price").value,
    from_of: document.getElementById("service-from-of").checked ? 1 : 0,
    duration: document.getElementById("service-duration").value.trim(),
    popular: document.getElementById("service-popular").checked ? 1 : 0,
    description: document.getElementById("service-description").value.trim(),
    image: document.getElementById("service-image").value.trim(),
  };

  if (
    !payload.name ||
    !payload.category ||
    !payload.price ||
    !payload.duration
  ) {
    errorEl.textContent = "Completa nombre, categoría, precio y duración.";
    errorEl.style.display = "block";
    return;
  }

  try {
    const url = editingId
      ? `${API}?action=update&id=${editingId}`
      : `${API}?action=create`;

    const res = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
    const data = await res.json();

    if (data.success) {
      closeServiceForm();
      loadServices();
    } else {
      errorEl.textContent = data.message || "No se pudo guardar el servicio.";
      errorEl.style.display = "block";
    }
  } catch (err) {
    errorEl.textContent = "Error de conexión, intenta de nuevo.";
    errorEl.style.display = "block";
  }
}

async function deleteService(id) {
  if (!confirm("Esto eliminará el servicio permanentemente. ¿Continuar?"))
    return;

  try {
    const res = await fetch(`${API}?action=delete&id=${id}`, {
      method: "POST",
    });
    const data = await res.json();
    if (data.success) {
      loadServices();
    } else {
      alert(data.message || "No se pudo eliminar el servicio.");
    }
  } catch (err) {
    alert("Error de conexión, intenta de nuevo.");
  }
}

document.addEventListener("DOMContentLoaded", loadServices);
const SETTINGS_API = "/peluqueria/php/api/settings_api.php";

// ── ADMIN TABS ──
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
  loadSettings();
});

// ── SETTINGS ──
async function loadSettings() {
  try {
    const res = await fetch(`${SETTINGS_API}?action=all`);
    const settings = await res.json();
    settings.forEach((s) => {
      const input = document.getElementById(`setting-${s.setting_key}`);
      if (input) input.value = s.setting_value;
    });
  } catch (err) {
    console.error("Error loading settings:", err);
  }
}

async function saveSettings() {
  const messageEl = document.getElementById("settings-message");
  const payload = {
    whatsapp_number: document
      .getElementById("setting-whatsapp_number")
      .value.trim(),
    about_us_text: document
      .getElementById("setting-about_us_text")
      .value.trim(),
    schedule_today: document
      .getElementById("setting-schedule_today")
      .value.trim(),
    address: document.getElementById("setting-address").value.trim(),
  };

  try {
    const res = await fetch(`${SETTINGS_API}?action=update`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
    const data = await res.json();

    messageEl.style.display = "block";
    if (data.success) {
      messageEl.style.color = "var(--admin-success)";
      messageEl.textContent = "Cambios guardados correctamente.";
    } else {
      messageEl.style.color = "var(--admin-danger)";
      messageEl.textContent =
        data.message || "No se pudieron guardar los cambios.";
    }
  } catch (err) {
    messageEl.style.display = "block";
    messageEl.style.color = "var(--admin-danger)";
    messageEl.textContent = "Error de conexión, intenta de nuevo.";
  }
}
