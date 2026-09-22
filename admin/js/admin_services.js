const SERVICES_API = "/peluqueria/php/api/services_api.php";

let services  = [];
let editingId = null;

// ── CARGAR TABLA ──
async function loadServices() {
  const res = await fetch(`${SERVICES_API}?action=allAdmin`);
  services  = await res.json();
  renderServicesTable();
}

function renderServicesTable() {
  const tbody = document.getElementById("services-tbody");
  tbody.innerHTML = services.map(s => `
    <tr style="${s.active == 0 ? "opacity:.5;" : ""}">
      <td>
        ${s.image
          ? `<img src="/peluqueria/${s.image}" style="width:40px;height:40px;object-fit:cover;border-radius:4px;margin-right:6px;" />`
          : "—"}
        ${s.name}
      </td>
      <td>${s.category}</td>
      <td>$${Number(s.price).toLocaleString("es-CO")}${s.from_of == 1 ? " (desde)" : ""}</td>
      <td>${s.duration}</td>
      <td>${s.popular == 1 ? "⭐" : "—"}</td>
      <td>${s.active == 1 ? "✅ Activo" : "🚫 Oculto"}</td>
      <td>
        <button onclick="editService(${s.id})" class="btn-small">✏️ Editar</button>
        <button onclick="toggleServiceActive(${s.id}, ${s.active == 1 ? 0 : 1})" class="btn-small">
          ${s.active == 1 ? "🚫 Ocultar" : "✅ Mostrar"}
        </button>
        <button onclick="deleteService(${s.id})" class="btn-small btn-danger">🗑️ Eliminar</button>
      </td>
    </tr>
  `).join("");
}

// ── TOGGLE ACTIVE ──
async function toggleServiceActive(id, newActive) {
  try {
    const res  = await fetch(`${SERVICES_API}?action=toggleActive&id=${id}`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ active: newActive }),
    });
    const data = await res.json();
    if (data.success) {
      showAlert("Estado del servicio actualizado. Recargando…", "success", { reload: true });
    } else {
      showAlert(data.message || "No se pudo cambiar el estado.", "error");
    }
  } catch (err) {
    showAlert("Error de conexión, intenta de nuevo.", "error");
  }
}

// ── ABRIR MODAL NUEVO ──
function openServiceForm() {
  editingId = null;
  document.getElementById("service-modal-title").textContent = "Nuevo servicio";
  document.getElementById("service-id").value       = "";
  document.getElementById("service-name").value     = "";
  document.getElementById("service-price").value    = "";
  document.getElementById("service-from-of").checked  = false;
  document.getElementById("service-duration").value   = "";
  document.getElementById("service-popular").checked  = false;
  document.getElementById("service-description").value = "";
  document.getElementById("service-image").value    = "";

  // reset preview e input file
  document.getElementById("service-image-file").value  = "";
  document.getElementById("service-image-preview").style.display = "none";
  document.getElementById("service-image-preview").src = "";

  if (typeof populateServiceCategorySelect === "function") populateServiceCategorySelect();
  document.getElementById("service-error").style.display = "none";
  document.getElementById("service-modal").classList.add("active");
}

// ── ABRIR MODAL EDITAR ──
function editService(id) {
  const s = services.find((x) => x.id == id);
  if (!s) return;
  editingId = id;
  document.getElementById("service-modal-title").textContent   = "Editar servicio";
  document.getElementById("service-id").value                  = s.id;
  document.getElementById("service-name").value                = s.name;
  document.getElementById("service-price").value               = s.price;
  document.getElementById("service-from-of").checked           = s.from_of == 1;
  document.getElementById("service-duration").value            = s.duration;
  document.getElementById("service-popular").checked           = s.popular == 1;
  document.getElementById("service-description").value         = s.description || "";
  document.getElementById("service-image").value               = s.image || "";

  // mostrar preview si ya tiene imagen
  const preview = document.getElementById("service-image-preview");
  if (s.image) {
    preview.src           = `/peluqueria/${s.image}`;
    preview.style.display = "block";
  } else {
    preview.src           = "";
    preview.style.display = "none";
  }

  // reset file input
  document.getElementById("service-image-file").value = "";

  if (typeof populateServiceCategorySelect === "function") populateServiceCategorySelect();
  document.getElementById("service-category").value   = s.category;
  document.getElementById("service-error").style.display = "none";
  document.getElementById("service-modal").classList.add("active");
}

function closeServiceForm() {
  document.getElementById("service-modal").classList.remove("active");
}

// ── PREVIEW EN TIEMPO REAL ──
document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("service-image-file").addEventListener("change", function () {
    const file    = this.files[0];
    const preview = document.getElementById("service-image-preview");
    if (!file) return;
    const reader  = new FileReader();
    reader.onload = (e) => {
      preview.src           = e.target.result;
      preview.style.display = "block";
    };
    reader.readAsDataURL(file);
  });
});

// ── SUBIR IMAGEN ──
async function uploadServiceImage() {
  const fileInput = document.getElementById("service-image-file");
  const file      = fileInput.files[0];
  if (!file) return null; // sin archivo nuevo, no sube nada

  const formData = new FormData();
  formData.append("image", file);
  formData.append("folder", "services");

  const res  = await fetch(UPLOAD_API, { method: "POST", body: formData });
  const data = await res.json();

  if (!data.success) {
    throw new Error(data.message || "Error al subir la imagen.");
  }
  return data.path; // ej: "assets/images/services/img_abc123.jpg"
}

// ── GUARDAR SERVICIO ──
async function saveService() {
  const errorEl = document.getElementById("service-error");
  errorEl.style.display = "none";

  const name     = document.getElementById("service-name").value.trim();
  const category = document.getElementById("service-category").value;
  const price    = document.getElementById("service-price").value;
  const duration = document.getElementById("service-duration").value.trim();

  if (!name || !category || !price || !duration) {
    errorEl.textContent   = "Completa nombre, categoría, precio y duración.";
    errorEl.style.display = "block";
    return;
  }

  try {
    // 1. subir imagen si eligieron una nueva
    let imagePath = document.getElementById("service-image").value; // ruta actual (edición)
    const newPath = await uploadServiceImage();
    if (newPath) imagePath = newPath; // reemplaza solo si subieron algo nuevo

    // 2. guardar servicio con la ruta final
    const payload = {
      name,
      category,
      price,
      from_of:     document.getElementById("service-from-of").checked ? 1 : 0,
      duration,
      popular:     document.getElementById("service-popular").checked ? 1 : 0,
      description: document.getElementById("service-description").value.trim(),
      image:       imagePath,
    };

    const url = editingId
      ? `${SERVICES_API}?action=update&id=${editingId}`
      : `${SERVICES_API}?action=create`;

    const res  = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
    const data = await res.json();

    if (data.success) {
      closeServiceForm();
      showAlert("Servicio guardado correctamente. Recargando…", "success", { reload: true });
    } else {
      errorEl.textContent   = data.message || "No se pudo guardar el servicio.";
      errorEl.style.display = "block";
    }
  } catch (err) {
    errorEl.textContent   = err.message || "Error de conexión, intenta de nuevo.";
    errorEl.style.display = "block";
  }
}

// ── ELIMINAR ──
async function deleteService(id) {
  if (!confirm("Esto eliminará el servicio permanentemente. ¿Continuar?")) return;
  try {
    const res  = await fetch(`${SERVICES_API}?action=delete&id=${id}`, { method: "POST" });
    const data = await res.json();
    if (data.success) {
      showAlert("Servicio eliminado correctamente. Recargando…", "success", { reload: true });
    } else {
      showAlert(data.message || "No se pudo eliminar el servicio.", "error");
    }
  } catch (err) {
    showAlert("Error de conexión, intenta de nuevo.", "error");
  }
}