/* ══════════════════════════════════════════
   ADMIN SETTINGS
   Site configuration form (WhatsApp,
   about us, schedule, address).
   ══════════════════════════════════════════ */

const SETTINGS_API = "/peluqueria/php/api/settings_api.php";
const SITE_BASE    = "/peluqueria/";
const UPLOAD_API   = "/peluqueria/php/api/upload_api.php";

async function loadSettings() {
  try {
    const res      = await fetch(`${SETTINGS_API}?action=all`);
    const settings = await res.json();
    settings.forEach((s) => {
      const input = document.getElementById(`setting-${s.setting_key}`);
      if (input) input.value = s.setting_value;

      const previewMap = {
        hero_image:        "hero-image-preview",
        portfolio_image_1: "portfolio-1-preview",
        portfolio_image_2: "portfolio-2-preview",
        portfolio_image_3: "portfolio-3-preview",
      };
      if (previewMap[s.setting_key] && s.setting_value) {
        const preview = document.getElementById(previewMap[s.setting_key]);
        if (preview) {
          preview.src          = SITE_BASE + s.setting_value;
          preview.style.display = "block";
        }
      }
    });
  } catch (err) {
    console.error("Error loading settings:", err);
  }
}

async function saveSettings() {
  const messageEl = document.getElementById("settings-message");
  const payload = {
    whatsapp_number: document.getElementById("setting-whatsapp_number").value.trim(),
    about_us_text:   document.getElementById("setting-about_us_text").value.trim(),
    schedule_today:  document.getElementById("setting-schedule_today").value.trim(),
    address:         document.getElementById("setting-address").value.trim(),
  };

  try {
    const res  = await fetch(`${SETTINGS_API}?action=update`, {
      method:  "POST",
      headers: { "Content-Type": "application/json" },
      body:    JSON.stringify(payload),
    });
    const data = await res.json();

    messageEl.style.display = "block";
    if (data.success) {
      messageEl.style.color = "var(--admin-success)";
      messageEl.textContent = "Cambios guardados correctamente.";
    } else {
      messageEl.style.color = "var(--admin-danger)";
      messageEl.textContent = data.message || "No se pudieron guardar los cambios.";
    }
  } catch (err) {
    messageEl.style.display = "block";
    messageEl.style.color   = "var(--admin-danger)";
    messageEl.textContent   = "Error de conexión, intenta de nuevo.";
  }
}

async function uploadImage(file, folder, subfolder, oldPath) {
  const formData = new FormData();
  formData.append("image", file);
  formData.append("folder", folder);
  if (subfolder) formData.append("subfolder", subfolder);
  if (oldPath) formData.append("old_path", oldPath);

  const res  = await fetch(UPLOAD_API, { method: "POST", body: formData });
  const data = await res.json();
  return data;
}

async function handleSiteImageUpload(inputId, previewId, settingKey, folder) {
  const input = document.getElementById(inputId);
  const file  = input.files[0];
  if (!file) return;

  const currentInput = document.getElementById(`setting-${settingKey}`);
  const oldPath = currentInput ? currentInput.value : "";

  const uploadResult = await uploadImage(file, folder, null, oldPath);
  if (!uploadResult.success) {
    alert(uploadResult.message || "No se pudo subir la imagen.");
    return;
  }

  const saveResult = await fetch(`${SETTINGS_API}?action=update`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ [settingKey]: uploadResult.path }),
  });
  const saveData = await saveResult.json();

  if (saveData.success) {
    const preview = document.getElementById(previewId);
    preview.src = SITE_BASE + uploadResult.path;
    preview.style.display = "block";
    if (currentInput) currentInput.value = uploadResult.path; 
  } else {
    alert(saveData.message || "La imagen se subió pero no se pudo guardar.");
  }
}

document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("hero-image-file")?.addEventListener("change", () =>
    handleSiteImageUpload("hero-image-file", "hero-image-preview", "hero_image", "logos")
  );
  document.getElementById("portfolio-1-file")?.addEventListener("change", () =>
    handleSiteImageUpload("portfolio-1-file", "portfolio-1-preview", "portfolio_image_1", "portfolio")
  );
  document.getElementById("portfolio-2-file")?.addEventListener("change", () =>
    handleSiteImageUpload("portfolio-2-file", "portfolio-2-preview", "portfolio_image_2", "portfolio")
  );
  document.getElementById("portfolio-3-file")?.addEventListener("change", () =>
    handleSiteImageUpload("portfolio-3-file", "portfolio-3-preview", "portfolio_image_3", "portfolio")
  );
});