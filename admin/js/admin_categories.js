/* ══════════════════════════════════════════
   ADMIN CATEGORIES
   CRUD table + modal for categories.
   Also keeps the service-form category
   <select> in sync.
   ══════════════════════════════════════════ */

const CATEGORIES_API = "/peluqueria/php/api/categories_api.php";

let categories        = [];
let editingCategoryId = null;

async function loadCategories() {
  const res  = await fetch(`${CATEGORIES_API}?action=allAdmin`);
  categories = await res.json();
  renderCategoriesTable();
  populateServiceCategorySelect();
}

function renderCategoriesTable() {
  const tbody = document.getElementById("categories-tbody");
  tbody.innerHTML = categories.map(c => `
    <tr style="${c.active == 0 ? "opacity:.5;" : ""}">
      <td>${c.name}</td>
      <td>${c.label}</td>
      <td><code>${c.css_class}</code></td>
      <td>${c.display_order}</td>
      <td>${c.active == 1 ? "✅ Activo" : "🚫 Oculto"}</td>
      <td>
        <button onclick="editCategory(${c.id})" class="btn-small">✏️ Editar</button>
        <button onclick="toggleCategoryActive(${c.id}, ${c.active == 1 ? 0 : 1})" class="btn-small">
          ${c.active == 1 ? "🚫 Ocultar" : "✅ Mostrar"}
        </button>
        <button onclick="deleteCategory(${c.id})" class="btn-small btn-danger">🗑️ Eliminar</button>
      </td>
    </tr>
  `).join("");
}

function populateServiceCategorySelect() {
  const select = document.getElementById("service-category");
  if (!select) return;
  const current = select.value;
  select.innerHTML = categories
    .filter(c => c.active == 1)
    .map(c => `<option value="${c.name}">${c.name}</option>`)
    .join("");
  if (current) select.value = current;
}

function openCategoryForm() {
  editingCategoryId = null;
  document.getElementById("category-modal-title").textContent = "Nueva categoría";
  document.getElementById("category-id").value = "";
  document.getElementById("category-name").value = "";
  document.getElementById("category-label").value = "";
  document.getElementById("category-css-class").value = "";
  document.getElementById("category-display-order").value = "0";
  document.getElementById("category-error").style.display = "none";
  document.getElementById("category-modal").style.display = "flex";
}

function closeCategoryForm() {
  document.getElementById("category-modal").style.display = "none";
}

function editCategory(id) {
  const c = categories.find(x => x.id == id);
  if (!c) return;
  editingCategoryId = id;
  document.getElementById("category-modal-title").textContent = "Editar categoría";
  document.getElementById("category-id").value = c.id;
  document.getElementById("category-name").value = c.name;
  document.getElementById("category-label").value = c.label;
  document.getElementById("category-css-class").value = c.css_class;
  document.getElementById("category-display-order").value = c.display_order;
  document.getElementById("category-error").style.display = "none";
  document.getElementById("category-modal").style.display = "flex";
}

async function saveCategory() {
  const errorEl = document.getElementById("category-error");
  const payload = {
    name: document.getElementById("category-name").value.trim(),
    label: document.getElementById("category-label").value.trim(),
    css_class: document.getElementById("category-css-class").value.trim(),
    display_order: document.getElementById("category-display-order").value || 0,
  };

  try {
    const url = editingCategoryId
      ? `${CATEGORIES_API}?action=update&id=${editingCategoryId}`
      : `${CATEGORIES_API}?action=create`;

    const res  = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
    const data = await res.json();

    if (data.success) {
      closeCategoryForm();
      loadCategories();
    } else {
      errorEl.textContent = data.message || "No se pudo guardar la categoría.";
      errorEl.style.display = "block";
    }
  } catch (err) {
    errorEl.textContent = "Error de conexión, intenta de nuevo.";
    errorEl.style.display = "block";
  }
}

async function toggleCategoryActive(id, newActive) {
  try {
    const res  = await fetch(`${CATEGORIES_API}?action=toggleActive&id=${id}`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ active: newActive }),
    });
    const data = await res.json();
    if (data.success) {
      loadCategories();
    } else {
      alert(data.message || "No se pudo cambiar el estado.");
    }
  } catch (err) {
    alert("Error de conexión, intenta de nuevo.");
  }
}

async function deleteCategory(id) {
  if (!confirm("Esto eliminará la categoría permanentemente. Los servicios que la usen quedarán con una categoría que ya no existe. ¿Continuar?")) return;

  try {
    const res  = await fetch(`${CATEGORIES_API}?action=delete&id=${id}`, { method: "POST" });
    const data = await res.json();
    if (data.success) {
      loadCategories();
    } else {
      alert(data.message || "No se pudo eliminar.");
    }
  } catch (err) {
    alert("Error de conexión, intenta de nuevo.");
  }
}