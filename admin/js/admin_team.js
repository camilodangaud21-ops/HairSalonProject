/* ══════════════════════════════════════════
   ADMIN TEAM
   CRUD table + modal for team members.
   ══════════════════════════════════════════ */

const TEAM_API = "/peluqueria/php/api/team_api.php";

let teamMembers   = [];
let editingTeamId = null;

async function loadTeam() {
  const res   = await fetch(`${TEAM_API}?action=allAdmin`);
  teamMembers = await res.json();
  renderTeamTable();
}

function renderTeamTable() {
  const tbody = document.getElementById("team-tbody");
  tbody.innerHTML = teamMembers.map(m => `
    <tr style="${m.active == 0 ? "opacity:.5;" : ""}">
      <td>${m.name}</td>
      <td>${m.role}</td>
      <td>${m.rating ?? "—"}</td>
      <td>${m.display_order}</td>
      <td>${m.active == 1 ? "✅ Activo" : "🚫 Oculto"}</td>
      <td>
        <button onclick="editTeamMember(${m.id})" class="btn-small">✏️ Editar</button>
        <button onclick="toggleTeamActive(${m.id}, ${m.active == 1 ? 0 : 1})" class="btn-small">
          ${m.active == 1 ? "🚫 Ocultar" : "✅ Mostrar"}
        </button>
        <button onclick="deleteTeamMember(${m.id})" class="btn-small btn-danger">🗑️ Eliminar</button>
      </td>
    </tr>
  `).join("");
}

function openTeamForm() {
  editingTeamId = null;
  document.getElementById("team-modal-title").textContent = "Nuevo miembro";
  document.getElementById("team-id").value = "";
  document.getElementById("team-name").value = "";
  document.getElementById("team-role").value = "";
  document.getElementById("team-rating").value = "";
  document.getElementById("team-photo").value = "";
  document.getElementById("team-display-order").value = "0";
  document.getElementById("team-error").classList.remove("active");
  document.getElementById("team-modal").classList.add("active");
}

function closeTeamForm() {
  document.getElementById("team-modal").classList.remove("active");
}

function editTeamMember(id) {
  const m = teamMembers.find(x => x.id == id);
  if (!m) return;
  editingTeamId = id;
  document.getElementById("team-modal-title").textContent = "Editar miembro";
  document.getElementById("team-id").value = m.id;
  document.getElementById("team-name").value = m.name;
  document.getElementById("team-role").value = m.role;
  document.getElementById("team-rating").value = m.rating ?? "";
  document.getElementById("team-photo").value = m.photo ?? "";
  document.getElementById("team-display-order").value = m.display_order;
  document.getElementById("team-error").classList.remove("active");
  document.getElementById("team-modal").classList.add("active");
}

async function saveTeamMember() {
  const errorEl = document.getElementById("team-error");
  const payload = {
    name: document.getElementById("team-name").value.trim(),
    role: document.getElementById("team-role").value.trim(),
    rating: document.getElementById("team-rating").value,
    photo: document.getElementById("team-photo").value.trim(),
    display_order: document.getElementById("team-display-order").value || 0,
  };

  try {
    const url = editingTeamId
      ? `${TEAM_API}?action=update&id=${editingTeamId}`
      : `${TEAM_API}?action=create`;

    const res  = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
    const data = await res.json();

    if (data.success) {
      closeTeamForm();
      loadTeam();
    } else {
      errorEl.textContent = data.message || "No se pudo guardar el miembro.";
      errorEl.style.display = "block";
    }
  } catch (err) {
    errorEl.textContent = "Error de conexión, intenta de nuevo.";
    errorEl.style.display = "block";
  }
}

async function toggleTeamActive(id, newActive) {
  try {
    const res  = await fetch(`${TEAM_API}?action=toggleActive&id=${id}`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ active: newActive }),
    });
    const data = await res.json();
    if (data.success) {
      loadTeam();
    } else {
      alert(data.message || "No se pudo cambiar el estado.");
    }
  } catch (err) {
    alert("Error de conexión, intenta de nuevo.");
  }
}

async function deleteTeamMember(id) {
  if (!confirm("Esto eliminará al miembro del equipo permanentemente. ¿Continuar?")) return;

  try {
    const res  = await fetch(`${TEAM_API}?action=delete&id=${id}`, { method: "POST" });
    const data = await res.json();
    if (data.success) {
      loadTeam();
    } else {
      alert(data.message || "No se pudo eliminar.");
    }
  } catch (err) {
    alert("Error de conexión, intenta de nuevo.");
  }
}