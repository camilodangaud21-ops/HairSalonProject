<?php
session_start();
// Admin dashboard page
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header('Location: /peluqueria/index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Panel Admin - Isabel Rojas Beauty Salón & Spa</title>
  <link rel="stylesheet" href="/peluqueria/css/base.css" />
  <link rel="stylesheet" href="/peluqueria/css/layout.css" />
  <link rel="stylesheet" href="/peluqueria/css/components.css" />
  <link rel="stylesheet" href="/peluqueria/admin/css/admin.css" />
</head>
<body>
<header class="admin-header">
  <h1>Panel de administración</h1>
  <div class="admin-header-right">
    <span>Hola, <?= htmlspecialchars($_SESSION['user']['first_name']) ?></span>
    <a href="/peluqueria/index.php" class="btn-login">🏠 Ir al sitio</a>
    <a href="/peluqueria/php/auth/logout.php" class="btn-login">👤 Cerrar sesión</a>
  </div>
</header>

<div class="tabs">
  <div class="tab active" data-admintab="servicios">Servicios</div>
  <div class="tab" data-admintab="equipo">Equipo</div>
  <div class="tab" data-admintab="categorias">Categorías</div>
  <div class="tab" data-admintab="resenas">Reseñas</div>
  <div class="tab" data-admintab="configuracion">Configuración</div>
</div>

<main class="admin-main">
  <div class="admin-panel active" id="admin-panel-servicios">
    <section class="admin-toolbar"><h2>Servicios</h2><button class="btn-reservar" onclick="openServiceForm()">+ Nuevo servicio</button></section>
    <table class="admin-table" id="services-table"><thead><tr><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Duración</th><th>Popular</th><th>Estado</th><th>Acciones</th></tr></thead><tbody id="services-tbody"></tbody></table>
  </div>

  <div class="admin-panel" id="admin-panel-categorias">
    <section class="admin-toolbar"><h2>Categorías</h2><button class="btn-reservar" onclick="openCategoryForm()">+ Nueva categoría</button></section>
    <table class="admin-table" id="categories-table"><thead><tr><th>Nombre</th><th>Etiqueta</th><th>Clase CSS</th><th>Orden</th><th>Estado</th><th>Acciones</th></tr></thead><tbody id="categories-tbody"></tbody></table>
  </div>

  <div class="admin-panel" id="admin-panel-configuracion">
    <section class="admin-toolbar"><h2>Configuración del sitio</h2></section>
    <div class="settings-form">
      <label class="settings-label">Número de WhatsApp (con código de país, sin +)<input class="modal-input" id="setting-whatsapp_number" placeholder="573000000000" /></label>
      <label class="settings-label">Texto "Sobre nosotros"<textarea class="modal-input" id="setting-about_us_text" rows="4"></textarea></label>
      <label class="settings-label">Horario de hoy<input class="modal-input" id="setting-schedule_today" placeholder="09:00 - 20:00" /></label>
      <label class="settings-label">Dirección<input class="modal-input" id="setting-address" placeholder="Dirección completa" /></label>
      <label class="settings-label">Imagen del banner principal (hero)<input type="file" id="hero-image-file" accept="image/jpeg,image/png,image/webp" /><img id="hero-image-preview" style="max-width:200px; margin-top:8px; display:none; border-radius:6px;" /></label>
      <label class="settings-label">Foto 1 (galería "Sobre nosotros")<input type="file" id="portfolio-1-file" accept="image/jpeg,image/png,image/webp" /><img id="portfolio-1-preview" style="max-width:150px; margin-top:8px; display:none; border-radius:6px;" /></label>
      <label class="settings-label">Foto 2 (galería "Sobre nosotros")<input type="file" id="portfolio-2-file" accept="image/jpeg,image/png,image/webp" /><img id="portfolio-2-preview" style="max-width:150px; margin-top:8px; display:none; border-radius:6px;" /></label>
      <label class="settings-label">Foto 3 (galería "Sobre nosotros")<input type="file" id="portfolio-3-file" accept="image/jpeg,image/png,image/webp" /><img id="portfolio-3-preview" style="max-width:150px; margin-top:8px; display:none; border-radius:6px;" /></label>
      <button onclick="saveSettings()" class="btn-reservar" style="padding:10px 24px;">Guardar cambios</button>
      <p id="settings-message" style="display:none; margin-top:10px;"></p>
    </div>
  </div>

  <div class="admin-panel" id="admin-panel-equipo">
    <section class="admin-toolbar"><h2>Equipo</h2><button class="btn-reservar" onclick="openTeamForm()">+ Nuevo miembro</button></section>
    <table class="admin-table" id="team-table"><thead><tr><th>Nombre</th><th>Rol</th><th>Rating</th><th>Orden</th><th>Estado</th><th>Acciones</th></tr></thead><tbody id="team-tbody"></tbody></table>
  </div>

  <div class="admin-panel" id="admin-panel-resenas">
    <section class="admin-toolbar"><h2>Reseñas</h2><button class="btn-reservar" onclick="openReviewForm()">+ Nueva reseña</button></section>
    <table class="admin-table" id="reviews-table"><thead><tr><th>Autor</th><th>Calificación</th><th>Comentario</th><th>Destacada</th><th>Estado</th><th>Fecha</th><th>Acciones</th></tr></thead><tbody id="reviews-tbody"></tbody></table>
  </div>
</main>

<div id="service-modal" class="modal-overlay"><div class="modal-box">
  <h2 class="modal-title" id="service-modal-title">Nuevo servicio</h2><input type="hidden" id="service-id" />
  <input class="modal-input" id="service-name" placeholder="Nombre del servicio" />
  <select class="modal-input" id="service-category"><option value="Peluquería">Peluquería</option><option value="Manicure y pedicure">Manicure y pedicure</option><option value="Maquillaje">Maquillaje</option><option value="Spa">Spa</option><option value="Depilación">Depilación</option></select>
  <input class="modal-input" id="service-price" type="number" placeholder="Precio" />
  <label class="modal-checkbox"><input type="checkbox" id="service-from-of" /> Precio "a partir de"</label>
  <input class="modal-input" id="service-duration" placeholder="Duración (ej: 45 min)" />
  <label class="modal-checkbox"><input type="checkbox" id="service-popular" /> Marcar como popular</label>
  <textarea class="modal-input" id="service-description" placeholder="Descripción"></textarea>
  <label class="settings-label" style="margin-top:8px;">Imagen del servicio<input type="file" id="service-image-file" accept="image/jpeg,image/png,image/webp" /><img id="service-image-preview" style="max-width:150px; margin-top:8px; display:none; border-radius:6px;" /></label>
  <input type="hidden" id="service-image" />
  <button onclick="saveService()" class="btn-reservar" style="width:100%; padding:10px;">Guardar</button>
  <p id="service-error" class="modal-error" style="display:none;"></p><button onclick="closeServiceForm()" class="modal-cancel">Cancelar</button>
</div></div>

<div id="category-modal" class="modal-overlay"><div class="modal-box">
  <h2 class="modal-title" id="category-modal-title">Nueva categoría</h2><input type="hidden" id="category-id" />
  <input class="modal-input" id="category-name" placeholder="Nombre (ej: Peluquería)" />
  <input class="modal-input" id="category-label" placeholder="Etiqueta visible (ej: PELUQUERÍA)" />
  <input class="modal-input" id="category-css-class" placeholder="Clase CSS (ej: cat-pelq)" />
  <input class="modal-input" id="category-display-order" type="number" placeholder="Orden de aparición" />
  <button onclick="saveCategory()" class="btn-reservar" style="width:100%; padding:10px;">Guardar</button>
  <p id="category-error" class="modal-error" style="display:none;"></p><button onclick="closeCategoryForm()" class="modal-cancel">Cancelar</button>
</div></div>

<div id="team-modal" class="modal-overlay"><div class="modal-box">
  <h2 class="modal-title" id="team-modal-title">Nuevo miembro</h2><input type="hidden" id="team-id" />
  <input class="modal-input" id="team-name" placeholder="Nombre completo" /><input class="modal-input" id="team-role" placeholder="Rol (ej: Estilista)" />
  <input class="modal-input" id="team-rating" type="number" step="0.1" min="0" max="5" placeholder="Rating (ej: 4.5)" /><input class="modal-input" id="team-display-order" type="number" placeholder="Orden de aparición" />
  <input type="hidden" id="team-photo" /><label style="color:var(--muted); font-size:.8rem; margin-bottom:4px; display:block;">Foto del miembro</label>
  <input type="file" id="team-photo-file" accept="image/*" class="modal-input" style="padding:6px;" /><img id="team-photo-preview" src="" alt="preview" style="display:none; width:80px; height:80px; object-fit:cover; border-radius:50%; border:2px solid var(--gold); margin:8px 0;" />
  <button onclick="saveTeamMember()" class="btn-reservar" style="width:100%; padding:10px;">Guardar</button><p id="team-error" class="modal-error" style="display:none;"></p><button onclick="closeTeamForm()" class="modal-cancel">Cancelar</button>
</div></div>

<div id="review-modal" class="modal-overlay"><div class="modal-box">
  <h2 class="modal-title" id="review-modal-title">Nueva reseña</h2><input type="hidden" id="review-id" />
  <input class="modal-input" id="review-author" placeholder="Nombre del cliente" /><select class="modal-input" id="review-rating"><option value="5">5 estrellas</option><option value="4">4 estrellas</option><option value="3">3 estrellas</option><option value="2">2 estrellas</option><option value="1">1 estrella</option></select>
  <textarea class="modal-input" id="review-comment" placeholder="Comentario del cliente"></textarea><label class="modal-checkbox"><input type="checkbox" id="review-featured" /> Destacar en la página principal</label>
  <button onclick="saveReview()" class="btn-reservar" style="width:100%; padding:10px;">Guardar</button><p id="review-error" class="modal-error" style="display:none;"></p><button onclick="closeReviewForm()" class="modal-cancel">Cancelar</button>
</div></div>

<script src="/peluqueria/js/alerts.js"></script>
<script src="/peluqueria/admin/js/admin_settings.js"></script>
<script src="/peluqueria/admin/js/admin_services.js"></script>
<script src="/peluqueria/admin/js/admin_team.js"></script>
<script src="/peluqueria/admin/js/admin_categories.js"></script>
<script src="/peluqueria/admin/js/admin_reviews.js"></script>
<script src="/peluqueria/admin/js/admin_core.js"></script>
</body>
</html>