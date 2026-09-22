/* Reusable site alerts. Safe to call from any page after DOM is ready. */
(() => {
  const TYPE_CLASS = {
    success: "app-alert-success",
    error: "app-alert-error",
    info: "app-alert-info",
    warning: "app-alert-warning",
  };

  window.showAlert = function showAlert(message, type = "info", options = {}) {
    const {
      duration = 3200,
      reload = false,
      reloadDelay = 1100,
    } = options;

    let alertEl = document.getElementById("app-alert");
    if (!alertEl) {
      alertEl = document.createElement("div");
      alertEl.id = "app-alert";
      alertEl.className = "app-alert";
      alertEl.setAttribute("role", "status");
      alertEl.setAttribute("aria-live", "polite");
      document.body.appendChild(alertEl);
    }

    clearTimeout(alertEl._hideTimer);
    clearTimeout(alertEl._reloadTimer);

    alertEl.className = `app-alert ${TYPE_CLASS[type] || TYPE_CLASS.info}`;
    alertEl.textContent = message;
    requestAnimationFrame(() => alertEl.classList.add("is-visible"));

    if (reload) {
      alertEl._reloadTimer = setTimeout(() => window.location.reload(), reloadDelay);
      return alertEl;
    }

    alertEl._hideTimer = setTimeout(() => {
      alertEl.classList.remove("is-visible");
    }, duration);

    return alertEl;
  };
})();