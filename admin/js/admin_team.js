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
      <td>
        ${m.photo
          ? `<img src="/peluqueria/${m.photo}" style="width:40px;height:40px;object-fit:cover;border-radius:50%;margin-right:6px;" />`
          : "—"}
        ${m.name}
      </td>
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
  document.getElementById("team-modal-title").textContent      = "Nuevo miembro";
  document.getElementById("team-id").value                     = "";
  document.getElementById("team-name").value                   = "";
  document.getElementById("team-role").value                   = "";
  document.getElementById("team-rating").value                 = "";
  document.getElementById("team-display-order").value          = "0";
  document.getElementById("team-photo").value                  = "";
  document.getElementById("team-photo-file").value             = "";
  document.getElementById("team-photo-preview").src            = "";
  document.getElementById("team-photo-preview").style.display  = "none";
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
  document.getElementById("team-id").value                = m.id;
  document.getElementById("team-name").value              = m.name;
  document.getElementById("team-role").value              = m.role;
  document.getElementById("team-rating").value            = m.rating ?? "";
  document.getElementById("team-display-order").value     = m.display_order;
  document.getElementById("team-photo").value             = m.photo ?? "";
  document.getElementById("team-photo-file").value        = "";

  const preview = document.getElementById("team-photo-preview");
  if (m.photo) {
    preview.src           = `/peluqueria/${m.photo}`;
    preview.style.display = "block";
  } else {
    preview.src           = "";
    preview.style.display = "none";
  }

  document.getElementById("team-error").classList.remove("active");
  document.getElementById("team-modal").classList.add("active");
}

// ── IMAGE PREVIEW ──
document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("team-photo-file").addEventListener("change", function () {
    const file    = this.files[0];
    const preview = document.getElementById("team-photo-preview");
    if (!file) return;
    const reader  = new FileReader();
    reader.onload = (e) => {
      preview.src           = e.target.result;
      preview.style.display = "block";
    };
    reader.readAsDataURL(file);
  });
});

// ── UPLOAD PHOTO ──
async function uploadTeamPhoto() {
  const fileInput = document.getElementById("team-photo-file");
  const file      = fileInput.files[0];
  if (!file) return null;

  const formData = new FormData();
  formData.append("image", file);
  formData.append("folder", "team");

  const res  = await fetch(UPLOAD_API, { method: "POST", body: formData });
  const data = await res.json();

  if (!data.success) throw new Error(data.message || "Error al subir la foto.");
  return data.path;
}

async function saveTeamMember() {
  const errorEl = document.getElementById("team-error");
  errorEl.classList.remove("active");

  const payload = {
    name:          document.getElementById("team-name").value.trim(),
    role:          document.getElementById("team-role").value.trim(),
    rating:        document.getElementById("team-rating").value,
    display_order: document.getElementById("team-display-order").value || 0,
    photo:         document.getElementById("team-photo").value,
  };

  if (!payload.name || !payload.role) {
    errorEl.textContent = "Nombre y rol son obligatorios.";
    errorEl.classList.add("active");
    return;
  }

  try {
    // Upload photo if a new file was selected
    const newPhoto = await uploadTeamPhoto();
    if (newPhoto) payload.photo = newPhoto;

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
      showAlert("Miembro guardado correctamente. Recargando…", "success", { reload: true });
    } else {
      errorEl.textContent = data.message || "No se pudo guardar el miembro.";
      errorEl.classList.add("active");
    }
  } catch (err) {
    errorEl.textContent = err.message || "Error de conexión, intenta de nuevo.";
    errorEl.classList.add("active");
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
      showAlert("Estado del miembro actualizado. Recargando…", "success", { reload: true });
    } else {
      showAlert(data.message || "No se pudo cambiar el estado.", "error");
    }
  } catch (err) {
    showAlert("Error de conexión, intenta de nuevo.", "error");
  }
}

async function deleteTeamMember(id) {
  if (!confirm("Esto eliminará al miembro del equipo permanentemente. ¿Continuar?")) return;

  try {
    const res  = await fetch(`${TEAM_API}?action=delete&id=${id}`, { method: "POST" });
    const data = await res.json();
    if (data.success) {
      showAlert("Miembro eliminado correctamente. Recargando…", "success", { reload: true });
    } else {
      showAlert(data.message || "No se pudo eliminar.", "error");
    }
  } catch (err) {
    showAlert("Error de conexión, intenta de nuevo.", "error");
  }
}