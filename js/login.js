/* ══════════════════════════════════════════
   LOGIN
   Login modal open/close + submit.
   ══════════════════════════════════════════ */

function openLogin() {
  document.getElementById("login-modal").classList.add("active");
}

function closeLogin() {
  document.getElementById("login-modal").classList.remove("active");
}

async function submitLogin() {
  const email    = document.getElementById("login-email").value;
  const password = document.getElementById("login-password").value;
  const errorEl  = document.getElementById("login-error");

  try {
    const res  = await fetch("/peluqueria/php/auth/login.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password }),
    });
    const data = await res.json();

    if (data.success) {
      window.location.href = data.redirect;
    } else {
      errorEl.textContent = data.message;
      errorEl.style.display = "block";

      const resendWrap = document.getElementById("resend-verification-wrap");
      if (resendWrap) {
        resendWrap.style.display = data.requires_verification ? "block" : "none";
      }
    }
  } catch (err) {
    errorEl.textContent = "Error de conexión, intenta de nuevo";
    errorEl.style.display = "block";
  }
}

// ── REGISTER MODAL ──
function openRegister() {
  document.getElementById("register-modal").classList.add("active");
}

function closeRegister() {
  document.getElementById("register-modal").classList.remove("active");
}

function switchToRegister() {
  closeLogin();
  openRegister();
}

function switchToLogin() {
  closeRegister();
  openLogin();
}

async function submitRegister() {
  const errorEl   = document.getElementById("reg-error");
  const firstName = document.getElementById("reg-first-name").value.trim();
  const lastName  = document.getElementById("reg-last-name").value.trim();
  const email     = document.getElementById("reg-email").value.trim();
  const password  = document.getElementById("reg-password").value.trim();
  const confirmPassword = document.getElementById("reg-password-confirm").value.trim();

  if (password !== confirmPassword) {
    errorEl.textContent = "Las contraseñas no coinciden.";
    errorEl.style.display = "block";
    return;
  }

  try {
    const res  = await fetch("/peluqueria/php/auth/register.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        first_name: firstName,
        last_name:  lastName,
        email,
        password,
        password_confirm: confirmPassword,
      }),
    });
    const data = await res.json();

    if (data.success) {
      closeRegister();
      if (typeof showAlert === "function") {
        showAlert(data.message, data.email_sent === false ? "warning" : "success", { duration: 7500 });
      }
      document.getElementById("register-form").reset();
    } else {
      errorEl.textContent   = data.message;
      errorEl.style.display = "block";
    }
  } catch (err) {
    errorEl.textContent   = "No pudimos completar la solicitud. Comprueba que el servidor esté disponible e inténtalo de nuevo.";
    errorEl.style.display = "block";
  }
}

async function resendVerification() {
  const email = document.getElementById("login-email").value.trim();
  const errorEl = document.getElementById("login-error");
  const resendBtn = document.getElementById("resend-verification-btn");

  if (!email) {
    errorEl.textContent = "Escribe tu correo electrónico para reenviar la verificación.";
    errorEl.style.display = "block";
    return;
  }

  resendBtn.disabled = true;
  resendBtn.textContent = "Enviando...";

  try {
    const res = await fetch("/peluqueria/php/auth/resend_verification.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email }),
    });
    const data = await res.json();

    if (data.success) {
      errorEl.style.display = "none";
      if (typeof showAlert === "function") {
        showAlert(data.message, "success", { duration: 7500 });
      }
    } else {
      errorEl.textContent = data.message;
      errorEl.style.display = "block";
    }
  } catch (err) {
    errorEl.textContent = "No pudimos reenviar el correo. Comprueba que el servidor esté disponible e inténtalo de nuevo.";
    errorEl.style.display = "block";
  } finally {
    resendBtn.disabled = false;
    resendBtn.textContent = "Reenviar correo de verificación";
  }
}
