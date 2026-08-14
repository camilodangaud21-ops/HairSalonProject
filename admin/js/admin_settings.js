/* ══════════════════════════════════════════
   ADMIN SETTINGS
   Site configuration form (WhatsApp,
   about us, schedule, address).
   ══════════════════════════════════════════ */

const SETTINGS_API = "/peluqueria/php/api/settings_api.php";

async function loadSettings() {
  try {
    const res      = await fetch(`${SETTINGS_API}?action=all`);
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
    whatsapp_number: document.getElementById("setting-whatsapp_number").value.trim(),
    about_us_text: document.getElementById("setting-about_us_text").value.trim(),
    schedule_today: document.getElementById("setting-schedule_today").value.trim(),
    address: document.getElementById("setting-address").value.trim(),
  };

  try {
    const res  = await fetch(`${SETTINGS_API}?action=update`, {
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
      messageEl.textContent = data.message || "No se pudieron guardar los cambios.";
    }
  } catch (err) {
    messageEl.style.display = "block";
    messageEl.style.color = "var(--admin-danger)";
    messageEl.textContent = "Error de conexión, intenta de nuevo.";
  }
}