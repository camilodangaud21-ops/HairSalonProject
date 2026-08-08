<?php
session_start();
require_once 'php/controllers/settings_controller.php';

$settingsController = new settings_controller();
$settings = $settingsController->getAllAsMap();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Isabel Rojas Beauty Salón & Spa</title>

  <link rel="stylesheet" href="css/base.css" />
  <link rel="stylesheet" href="css/layout.css" />
  <link rel="stylesheet" href="css/components.css" />
</head>
<body>

<!-- HERO -->
<div class="hero">
  <img class="hero-bg" src="https://images.unsplash.com/photo-1560066984-138dadb4c035?w=1200&q=80" alt="Isabel Rojas Beauty Salón" />
  <div class="hero-overlay"></div>
</div>

<!-- INFO BAR -->
<div class="info-bar">
  <div style="display:flex; justify-content:flex-end; margin-bottom:8px;">
    <?php if (isset($_SESSION['user'])): ?>
      <?php if ($_SESSION['user']['role'] === 'admin'): ?>
        <a href="admin/pages/dashboard.php" class="btn-login">⚙️ Panel admin</a>
      <?php else: ?>
        <a href="php/auth/logout.php" class="btn-login">👤 Cerrar sesión</a>
      <?php endif; ?>
    <?php else: ?>
      <button class="btn-login" onclick="openLogin()">Iniciar sesión</button>
    <?php endif; ?>
  </div>
  <h1>isabel rojas <span>peluquería y spa</span></h1>
  <div class="meta-row">
    <span class="star">★</span>
    <span>4.3 (6 reseñas)</span>
    <span>·</span>
    <span class="open-badge">Abierto hoy <?= htmlspecialchars($settings['schedule_today'] ?? '09:00 - 20:00') ?></span>
  </div>
  <div class="meta-row">
    <span class="info-icon">📍</span>
    <span><?= htmlspecialchars($settings['address'] ?? '') ?></span>
  </div>
  <div class="badges">
    <span class="badge">Salón de belleza</span>
    <span class="badge">Spa</span>
    <span class="badge">Nail</span>
    <span class="badge">Estética</span>
  </div>
</div>

<!-- TABS -->
<div class="tabs">
  <div class="tab active" data-tab="servicios">Servicios</div>
  <div class="tab" data-tab="equipo">Equipo</div>
  <div class="tab" data-tab="resenas">Reseñas</div>
</div>

<!-- ── PANEL SERVICIOS ── -->
<div class="panel active" id="panel-servicios">

  <!-- About -->
  <div class="about-grid" style="margin-bottom:20px;">
    <div class="about-text">
      <h2>Sobre nosotros</h2>
      <p style="color:var(--muted); margin: 8px 0 14px;"><?= htmlspecialchars($settings['about_us_text'] ?? '') ?></p>
      <div class="about-cats">
        <span class="about-cat">Salón de belleza</span>
        <span class="about-cat">Spa</span>
        <span class="about-cat">Nail</span>
        <span class="about-cat">Estética</span>
        <span class="about-cat">Spa de cejas y pestañas</span>
      </div>
    </div>
    <div class="info-card">
      <h3>Dirección</h3>
      <div class="info-row">
        <span class="info-icon">📍</span>
        <span><?= htmlspecialchars($settings['address'] ?? '') ?></span>
      </div>
      <h3 style="margin-top:10px;">Horario hoy</h3>
      <div class="info-row">
        <span class="info-icon">🕐</span>
        <span><?= htmlspecialchars($settings['schedule_today'] ?? '') ?></span>
      </div>
      <div class="portfolio-grid">
        <img src="https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=200&q=70" alt="foto 1"/>
        <img src="https://images.unsplash.com/photo-1562322140-8baeececf3df?w=200&q=70" alt="foto 2"/>
        <img src="https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?w=200&q=70" alt="foto 3"/>
      </div>
    </div>
  </div>

  <!-- Search -->
  <div class="search-wrap">
    <span class="search-icon">🔍</span>
    <input type="text" id="search-input" placeholder="Buscar servicios..."/>
  </div>

  <!-- Category filter -->
  <div class="cat-scroll">
    <button class="cat-btn active" data-cat="todos">Todos los servicios <span class="cat-count">43</span></button>
    <button class="cat-btn" data-cat="Peluquería">Peluquería</button>
    <button class="cat-btn" data-cat="Manicure y pedicure">Manicure y pedicure</button>
    <button class="cat-btn" data-cat="Maquillaje">Maquillaje</button>
    <button class="cat-btn" data-cat="Spa">Spa</button>
    <button class="cat-btn" data-cat="Depilación">Depilación</button>
  </div>

  <!-- Services grid -->
  <div class="services-grid" id="services-grid"></div>
  <p class="no-results" id="no-results" style="display:none">No se encontraron servicios.</p>

</div>

<!-- ── PANEL EQUIPO ── -->
<div class="panel" id="panel-equipo">
  <div class="equipo-grid">
    <div class="estilista-card">
      <div class="estilista-avatar-ph">💇</div>
      <div class="estilista-name">Isabel Argote</div>
      <div class="estilista-role">Estilista</div>
      <div class="estilista-stars">★★★★★ 5.0</div>
    </div>
    <div class="estilista-card">
      <div class="estilista-avatar-ph">💇</div>
      <div class="estilista-name">Camila Ramírez Peña</div>
      <div class="estilista-role">Estilista</div>
      <div class="estilista-stars">★★★★ —</div>
    </div>
    <div class="estilista-card">
      <div class="estilista-avatar-ph">💇</div>
      <div class="estilista-name">Dailis Martínez</div>
      <div class="estilista-role">Estilista</div>
      <div class="estilista-stars">★★★★ 3.7</div>
    </div>
  </div>
</div>

<!-- ── PANEL RESEÑAS ── -->
<div class="panel" id="panel-resenas">
  <div class="rating-summary">
    <div class="rating-big">
      <div class="num">4.3</div>
      <div class="star-big">★</div>
      <div class="count">6 reseñas</div>
    </div>
    <div class="rating-bars">
      <div class="bar-row"><span class="lbl">5</span><div class="bar-track"><div class="bar-fill" style="width:83%"></div></div><span class="bar-num">5</span></div>
      <div class="bar-row"><span class="lbl">4</span><div class="bar-track"><div class="bar-fill" style="width:0%"></div></div><span class="bar-num">0</span></div>
      <div class="bar-row"><span class="lbl">3</span><div class="bar-track"><div class="bar-fill" style="width:0%"></div></div><span class="bar-num">0</span></div>
      <div class="bar-row"><span class="lbl">2</span><div class="bar-track"><div class="bar-fill" style="width:0%"></div></div><span class="bar-num">0</span></div>
      <div class="bar-row"><span class="lbl">1</span><div class="bar-track"><div class="bar-fill" style="width:17%"></div></div><span class="bar-num">1</span></div>
    </div>
  </div>

  <div id="resenas-list">

  </div>
</div>

<!-- FOOTER -->
<footer>
  <div class="footer-brand">
    <h4>isabel rojas peluquería y spa</h4>
    <p><?= htmlspecialchars($settings['about_us_text'] ?? '') ?></p>
    <div class="footer-social">
      <a class="social-btn" href="#" aria-label="Instagram">📸</a>
      <a class="social-btn" href="#" aria-label="Facebook">📘</a>
      <a class="social-btn" href="https://wa.me/<?= htmlspecialchars($settings['whatsapp_number'] ?? '') ?>" aria-label="WhatsApp">💬</a>
    </div>
  </div>
  <div class="footer-links">
    <h5>Navegación</h5>
    <a onclick="switchTab('servicios')">Servicios</a>
    <a onclick="switchTab('equipo')">Colaboradores</a>
    <a onclick="switchTab('resenas')">Reseñas</a>
  </div>
  <div class="footer-links">
    <h5>Más información</h5>
    <div class="info-row" style="margin-bottom:8px;">
      <span class="info-icon">📍</span>
      <span style="font-size:.8rem;color:var(--muted);"><?= htmlspecialchars($settings['address'] ?? '') ?></span>
    </div>
    <div class="info-row">
      <span class="info-icon">🕐</span>
      <span style="font-size:.8rem;color:var(--muted);"><?= htmlspecialchars($settings['schedule_today'] ?? '') ?></span>
    </div>
  </div>
  <div class="footer-copy">© 2026 Isabel Rojas Beauty Salón & Spa · Cartagena, Colombia</div>
</footer>

<!-- LOGIN MODAL -->
<div id="login-modal" class="modal-overlay">
  <div class="modal-box">
    <h2 class="modal-title">Iniciar sesión</h2>
    <input id="login-email" class="modal-input" type="email" placeholder="Correo electrónico"/>
    <input id="login-password" class="modal-input" type="password" placeholder="Contraseña"/>
    <button onclick="submitLogin()" class="btn-reservar" style="width:100%; padding:10px;">Ingresar</button>
    <p id="login-error" class="modal-error">Correo o contraseña incorrectos</p>
    <button onclick="closeLogin()" class="modal-cancel">Cancelar</button>
  </div>
</div>

<script>
  const SITE_WHATSAPP = <?= json_encode($settings['whatsapp_number'] ?? '573000000000') ?>;
</script>
<script src="js/ui.js"></script>
</body>
</html>