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

  try {
    const res  = await fetch("/peluqueria/php/auth/register.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        first_name: firstName,
        last_name:  lastName,
        email,
        password,
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