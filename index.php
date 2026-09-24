<?php
session_start();
// Public homepage: renders site with settings
require_once 'php/controllers/settings_controller.php';
require_once 'php/controllers/team_controller.php';
$teamController = new team_controller();
$teamMembers = $teamController->getAllActive();
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
  <img class="hero-bg" src="<?= htmlspecialchars(!empty($settings['hero_image']) ? $settings['hero_image'] : 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=1200&q=80') ?>" alt="Isabel Rojas Beauty Salón" />
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

<!-- ── SERVICES PANEL ── -->
<div class="panel active" id="panel-servicios">
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
        <img src="<?= htmlspecialchars(!empty($settings['portfolio_image_1']) ? $settings['portfolio_image_1'] : 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=200&q=70') ?>" alt="foto 1"/>
        <img src="<?= htmlspecialchars(!empty($settings['portfolio_image_2']) ? $settings['portfolio_image_2'] : 'https://images.unsplash.com/photo-1562322140-8baeececf3df?w=200&q=70') ?>" alt="foto 2"/>
        <img src="<?= htmlspecialchars(!empty($settings['portfolio_image_3']) ? $settings['portfolio_image_3'] : 'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?w=200&q=70') ?>" alt="foto 3"/>
      </div>
    </div>
  </div>

  <div class="search-wrap">
    <span class="search-icon">🔍</span>
    <input type="search" id="search-input" name="service-query" placeholder="Buscar servicios..." autocomplete="off" autocapitalize="off" spellcheck="false" inputmode="search" readonly />
  </div>

  <div class="cat-scroll"></div>
  <div class="services-grid" id="services-grid"></div>
  <p class="no-results" id="no-results" style="display:none">No se encontraron servicios.</p>
</div>

<!-- ── TEAM PANEL ── -->
<div class="panel" id="panel-equipo">
  <div class="equipo-grid">
    <?php foreach ($teamMembers as $member): ?>
      <div class="estilista-card">
        <?php if (!empty($member['photo'])): ?>
          <img class="estilista-avatar" src="<?= htmlspecialchars($member['photo']) ?>" alt="<?= htmlspecialchars($member['name']) ?>" />
        <?php else: ?>
          <div class="estilista-avatar-ph">💇</div>
        <?php endif; ?>
        <div class="estilista-name"><?= htmlspecialchars($member['name']) ?></div>
        <div class="estilista-role"><?= htmlspecialchars($member['role']) ?></div>
        <div class="estilista-stars">
          <?= $member['rating'] !== null ? '★★★★★ ' . htmlspecialchars($member['rating']) : '★★★★ —' ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- ── REVIEWS PANEL ── -->
<div class="panel" id="panel-resenas">
  <div class="rating-summary">
    <div class="rating-big">
      <div class="num">—</div>
      <div class="star-big">★</div>
      <div class="count">cargando...</div>
    </div>
    <div class="rating-bars">
      <div class="bar-row"><span class="lbl">5</span><div class="bar-track"><div class="bar-fill" style="width:0%"></div></div><span class="bar-num">0</span></div>
      <div class="bar-row"><span class="lbl">4</span><div class="bar-track"><div class="bar-fill" style="width:0%"></div></div><span class="bar-num">0</span></div>
      <div class="bar-row"><span class="lbl">3</span><div class="bar-track"><div class="bar-fill" style="width:0%"></div></div><span class="bar-num">0</span></div>
      <div class="bar-row"><span class="lbl">2</span><div class="bar-track"><div class="bar-fill" style="width:0%"></div></div><span class="bar-num">0</span></div>
      <div class="bar-row"><span class="lbl">1</span><div class="bar-track"><div class="bar-fill" style="width:0%"></div></div><span class="bar-num">0</span></div>
    </div>
  </div>

  <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'client'): ?>
  <button class="btn-reservar" style="margin:16px 0;" onclick="openReviewForm()">+ Nueva reseña</button>
  <?php endif; ?>

  <div id="resenas-list"></div>
  <button id="btn-ver-todas-resenas" class="btn-login" style="display:none; margin:16px auto 0; width:100%;" onclick="loadAllReviews()">Ver todas las reseñas</button>
</div>

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
    <div class="info-row" style="margin-bottom:8px;"><span class="info-icon">📍</span><span style="font-size:.8rem;color:var(--muted);"><?= htmlspecialchars($settings['address'] ?? '') ?></span></div>
    <div class="info-row"><span class="info-icon">🕐</span><span style="font-size:.8rem;color:var(--muted);"><?= htmlspecialchars($settings['schedule_today'] ?? '') ?></span></div>
  </div>
  <div class="footer-copy">© 2026 Isabel Rojas Beauty Salón & Spa · Cartagena, Colombia</div>
</footer>

<div id="review-modal" class="modal-overlay">
  <div class="modal-box">
    <h2 class="modal-title">Deja tu reseña</h2>
    <div id="review-stars" style="font-size:1.5rem; cursor:pointer; margin-bottom:8px;">
      <span data-val="1">☆</span><span data-val="2">☆</span><span data-val="3">☆</span><span data-val="4">☆</span><span data-val="5">☆</span>
    </div>
    <input type="hidden" id="review-rating" value="0" />
    <textarea class="modal-input" id="review-comment" rows="3" placeholder="Cuéntanos tu experiencia..."></textarea>
    <button onclick="submitReview()" class="btn-reservar" style="width:100%; padding:10px;">Enviar reseña</button>
    <p id="review-error" class="modal-error" style="display:none;"></p>
    <button onclick="closeReviewForm()" class="modal-cancel">Cancelar</button>
  </div>
</div>

<div id="login-modal" class="modal-overlay">
  <div class="modal-box">
    <h2 class="modal-title">Iniciar sesión</h2>
    <form id="login-form" autocomplete="on" onsubmit="submitLogin(); return false;">
      <input id="login-email" name="email" class="modal-input" type="email" autocomplete="username" placeholder="Correo electrónico"/>
      <input id="login-password" name="password" class="modal-input" type="password" autocomplete="current-password" placeholder="Contraseña"/>
      <button type="submit" class="btn-reservar" style="width:100%; padding:10px;">Ingresar</button>
    </form>
    <button onclick="switchToRegister()" class="modal-cancel">¿No tienes cuenta? Regístrate</button>
    <p id="login-error" class="modal-error" style="display:none;"></p>
    <div id="resend-verification-wrap" style="display:none; margin-top:10px;">
      <button type="button" id="resend-verification-btn" class="modal-cancel" onclick="resendVerification()">Reenviar correo de verificación</button>
    </div>
    <button onclick="closeLogin()" class="modal-cancel">Cancelar</button>
  </div>
</div>

<div id="register-modal" class="modal-overlay">
  <div class="modal-box">
    <h2 class="modal-title">Crear cuenta</h2>
    <form id="register-form" autocomplete="on" onsubmit="submitRegister(); return false;">
      <input id="reg-first-name" name="given-name" class="modal-input" type="text" autocomplete="given-name" placeholder="Nombre" />
      <input id="reg-last-name" name="family-name" class="modal-input" type="text" autocomplete="family-name" placeholder="Apellido" />
      <input id="reg-email" name="email" class="modal-input" type="email" autocomplete="email" placeholder="Correo electrónico" />
      <input id="reg-password" name="new-password" class="modal-input" type="password" autocomplete="new-password" placeholder="Contraseña (mín. 6 caracteres)" />
      <input id="reg-password-confirm" name="new-password-confirm" class="modal-input" type="password" autocomplete="new-password" placeholder="Confirmar contraseña" />
      <button type="submit" class="btn-reservar" style="width:100%; padding:10px;">Crear cuenta</button>
    </form>
    <p id="reg-error" class="modal-error"></p>
    <button onclick="switchToLogin()" class="modal-cancel">¿Ya tienes cuenta? Inicia sesión</button>
  </div>
</div>

<script>
  const SITE_WHATSAPP = <?= json_encode($settings['whatsapp_number'] ?? '573000000000') ?>;
</script>
<script src="js/alerts.js"></script>
<script src="js/login.js"></script>
<script src="js/categories.js"></script>
<script src="js/services.js"></script>
<script src="js/tabs.js"></script>
<script src="js/main.js"></script>
<script src="js/reviews.js"></script>
</body>
</html>