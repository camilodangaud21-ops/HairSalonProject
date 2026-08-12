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
  loadTeam();
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
const TEAM_API = '/peluqueria/php/api/team_api.php';

let teamMembers   = [];
let editingTeamId = null;

async function loadTeam() {
  const res    = await fetch(`${TEAM_API}?action=allAdmin`);
  teamMembers  = await res.json();
  renderTeamTable();
}

function renderTeamTable() {
  const tbody = document.getElementById('team-tbody');
  tbody.innerHTML = teamMembers.map(m => `
    <tr style="${m.active == 0 ? 'opacity:.5;' : ''}">
      <td>${m.name}</td>
      <td>${m.role}</td>
      <td>${m.rating ?? '—'}</td>
      <td>${m.display_order}</td>
      <td>${m.active == 1 ? '✅ Activo' : '🚫 Oculto'}</td>
      <td>
        <button onclick="editTeamMember(${m.id})" class="btn-small">✏️ Editar</button>
        <button onclick="toggleTeamActive(${m.id}, ${m.active == 1 ? 0 : 1})" class="btn-small">
          ${m.active == 1 ? '🚫 Ocultar' : '✅ Mostrar'}
        </button>
        <button onclick="deleteTeamMember(${m.id})" class="btn-small btn-danger">🗑️ Eliminar</button>
      </td>
    </tr>
  `).join('');
}

function openTeamForm() {
  editingTeamId = null;
  document.getElementById('team-modal-title').textContent = 'Nuevo miembro';
  document.getElementById('team-id').value = '';
  document.getElementById('team-name').value = '';
  document.getElementById('team-role').value = '';
  document.getElementById('team-rating').value = '';
  document.getElementById('team-photo').value = '';
  document.getElementById('team-display-order').value = '0';
  document.getElementById('team-error').style.display = 'none';
  document.getElementById('team-modal').style.display = 'flex';
}

function closeTeamForm() {
  document.getElementById('team-modal').style.display = 'none';
}

function editTeamMember(id) {
  const m = teamMembers.find(x => x.id == id);
  if (!m) return;
  editingTeamId = id;
  document.getElementById('team-modal-title').textContent = 'Editar miembro';
  document.getElementById('team-id').value = m.id;
  document.getElementById('team-name').value = m.name;
  document.getElementById('team-role').value = m.role;
  document.getElementById('team-rating').value = m.rating ?? '';
  document.getElementById('team-photo').value = m.photo ?? '';
  document.getElementById('team-display-order').value = m.display_order;
  document.getElementById('team-error').style.display = 'none';
  document.getElementById('team-modal').style.display = 'flex';
}

async function saveTeamMember() {
  const errorEl = document.getElementById('team-error');
  const payload = {
    name: document.getElementById('team-name').value.trim(),
    role: document.getElementById('team-role').value.trim(),
    rating: document.getElementById('team-rating').value,
    photo: document.getElementById('team-photo').value.trim(),
    display_order: document.getElementById('team-display-order').value || 0,
  };

  try {
    const url = editingTeamId
      ? `${TEAM_API}?action=update&id=${editingTeamId}`
      : `${TEAM_API}?action=create`;

    const res  = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const data = await res.json();

    if (data.success) {
      closeTeamForm();
      loadTeam();
    } else {
      errorEl.textContent = data.message || 'No se pudo guardar el miembro.';
      errorEl.style.display = 'block';
    }
  } catch (err) {
    errorEl.textContent = 'Error de conexión, intenta de nuevo.';
    errorEl.style.display = 'block';
  }
}

async function toggleTeamActive(id, newActive) {
  try {
    const res  = await fetch(`${TEAM_API}?action=toggleActive&id=${id}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ active: newActive })
    });
    const data = await res.json();
    if (data.success) {
      loadTeam();
    } else {
      alert(data.message || 'No se pudo cambiar el estado.');
    }
  } catch (err) {
    alert('Error de conexión, intenta de nuevo.');
  }
}

async function deleteTeamMember(id) {
  if (!confirm('Esto eliminará al miembro del equipo permanentemente. ¿Continuar?')) return;

  try {
    const res  = await fetch(`${TEAM_API}?action=delete&id=${id}`, { method: 'POST' });
    const data = await res.json();
    if (data.success) {
      loadTeam();
    } else {
      alert(data.message || 'No se pudo eliminar.');
    }
  } catch (err) {
    alert('Error de conexión, intenta de nuevo.');
  }
}