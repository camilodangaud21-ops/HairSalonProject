<?php
if (!isset($user) || !is_array($user)) {
    $user = $_SESSION['user'] ?? [];
}

$clientName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
$initials = strtoupper(
    substr($user['first_name'] ?? 'C', 0, 1) .
    substr($user['last_name'] ?? 'D', 0, 1)
);

$projectBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if (basename($projectBase) === 'client') {
    $projectBase = rtrim(str_replace('\\', '/', dirname($projectBase)), '/');
}
$clientUrl = $projectBase . '/client';
$logoutUrl = $projectBase . '/php/auth/logout.php';
?>
<button class="client-account-trigger" type="button" aria-label="Abrir mi cuenta" aria-controls="client-drawer" aria-expanded="false" onclick="toggleClientDrawer()"><span class="client-avatar"><?= htmlspecialchars($initials) ?></span></button>
<div id="client-drawer-overlay" class="client-drawer-overlay" onclick="closeClientDrawer()"></div>
<aside id="client-drawer" class="client-drawer" aria-hidden="true">
<div class="client-drawer-head"><span>MI CUENTA</span><button type="button" onclick="closeClientDrawer()" aria-label="Cerrar">✕</button></div>
<div class="client-drawer-profile"><span class="client-avatar client-avatar-lg"><?= htmlspecialchars($initials) ?></span><strong><?= htmlspecialchars($clientName) ?></strong><small>✓ Correo verificado</small></div>
<nav class="client-nav">
<a href="<?= htmlspecialchars($clientUrl . '/profile.php') ?>">👤 <span>Mi perfil</span></a>
<a href="<?= htmlspecialchars($clientUrl . '/reservations.php') ?>">📅 <span>Mis reservas</span></a>
<a href="<?= htmlspecialchars($clientUrl . '/calendar.php') ?>">🕐 <span>Mi horario</span></a>
<a href="<?= htmlspecialchars($clientUrl . '/reviews.php') ?>">⭐ <span>Mis reseñas</span></a>
<div class="client-nav-separator"></div>
<a href="<?= htmlspecialchars($clientUrl . '/account.php') ?>">⚙️ <span>Opciones de cuenta</span></a>
<a href="<?= htmlspecialchars($clientUrl . '/account.php#security') ?>">🔒 <span>Seguridad</span></a>
<a class="client-logout" href="<?= htmlspecialchars($logoutUrl) ?>">🚪 <span>Cerrar sesión</span></a>
</nav>
</aside>
