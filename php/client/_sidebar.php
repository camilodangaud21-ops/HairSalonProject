<?php
$clientName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
$initials = strtoupper(substr($user['first_name'] ?? 'C', 0, 1) . substr($user['last_name'] ?? 'D', 0, 1));
?>
<button class="client-account-trigger" type="button" aria-label="Abrir mi cuenta" aria-controls="client-drawer" aria-expanded="false" onclick="toggleClientDrawer()"><span class="client-avatar"><?= htmlspecialchars($initials) ?></span></button>
<div id="client-drawer-overlay" class="client-drawer-overlay" onclick="closeClientDrawer()"></div>
<aside id="client-drawer" class="client-drawer" aria-hidden="true">
  <div class="client-drawer-head"><span>MI CUENTA</span><button type="button" onclick="closeClientDrawer()" aria-label="Cerrar">✕</button></div>
  <div class="client-drawer-profile"><span class="client-avatar client-avatar-lg"><?= htmlspecialchars($initials) ?></span><strong><?= htmlspecialchars($clientName ?: 'Cliente') ?></strong><small>✓ Correo verificado</small></div>
  <nav class="client-nav">
    <a href="profile.php">👤 <span>Mi perfil</span></a>
    <a href="reservations.php">📅 <span>Mis reservas</span></a>
    <a href="calendar.php">🕐 <span>Mi horario</span></a>
    <a href="reviews.php">⭐ <span>Mis reseñas</span></a>
    <div class="client-nav-separator"></div>
    <a href="account.php">⚙️ <span>Opciones de cuenta</span></a>
    <a href="account.php#security">🔒 <span>Seguridad</span></a>
    <a class="client-logout" href="../auth/logout.php">🚪 <span>Cerrar sesión</span></a>
  </nav>
</aside>