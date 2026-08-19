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