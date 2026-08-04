<?php
session_start();

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

<main class="admin-main">
  <section class="admin-toolbar">
    <h2>Servicios</h2>
    <button class="btn-reservar" onclick="openServiceForm()">+ Nuevo servicio</button>
  </section>

  <table class="admin-table" id="services-table">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Categoría</th>
        <th>Precio</th>
        <th>Duración</th>
        <th>Popular</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody id="services-tbody"></tbody>
  </table>
</main>

<!-- FORM MODAL -->
<div id="service-modal" class="modal-overlay">
  <div class="modal-box">
    <h2 class="modal-title" id="service-modal-title">Nuevo servicio</h2>
    <input type="hidden" id="service-id" />
    <input class="modal-input" id="service-name" placeholder="Nombre del servicio" />
    <select class="modal-input" id="service-category">
      <option value="Peluquería">Peluquería</option>
      <option value="Manicure y pedicure">Manicure y pedicure</option>
      <option value="Maquillaje">Maquillaje</option>
      <option value="Spa">Spa</option>
      <option value="Depilación">Depilación</option>
    </select>
    <input class="modal-input" id="service-price" type="number" placeholder="Precio" />
    <label class="modal-checkbox">
      <input type="checkbox" id="service-from-of" /> Precio "a partir de"
    </label>
    <input class="modal-input" id="service-duration" placeholder="Duración (ej: 45 min)" />
    <label class="modal-checkbox">
      <input type="checkbox" id="service-popular" /> Marcar como popular
    </label>
    <textarea class="modal-input" id="service-description" placeholder="Descripción"></textarea>
    <input class="modal-input" id="service-image" placeholder="URL de imagen (opcional)" />

    <button onclick="saveService()" class="btn-reservar" style="width:100%; padding:10px;">Guardar</button>
    <p id="service-error" class="modal-error" style="display:none;"></p>
    <button onclick="closeServiceForm()" class="modal-cancel">Cancelar</button>
  </div>
</div>

<script src="/peluqueria/admin/js/admin.js"></script>
</body>
</html>
