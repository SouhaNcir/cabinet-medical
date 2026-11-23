<?php
$current = trim((string) ($_GET['url'] ?? 'dashboard/index'), '/');
$user = $user ?? null;
$flash = $flash ?? null;
$role = $user['role'] ?? 'guest';
$roleLabel = $role === 'medecin' ? 'Espace medecin' : ($role === 'secretaire' ? 'Espace secretaire' : 'Espace invite');
$isDoctor = $role === 'medecin';
$isSecretary = $role === 'secretaire';
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? APP_NAME) ?> - <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/dashboard.css">
</head>
<body class="dashboard-page">
<div id="globalLoader" class="global-loader">
    <div class="loader-ring"></div>
    <p>Chargement...</p>
</div>

<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="fa-solid fa-staff-snake"></i></div>
            <div>
                <h2><?= e(APP_NAME) ?></h2>
                <p>Gestion medicale premium</p>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a class="nav-link <?= str_starts_with($current, 'dashboard') ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/dashboard/index">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>
            <a class="nav-link <?= str_starts_with($current, 'patient') ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/patient/index">
                <i class="fa-solid fa-user-injured"></i> Patients
            </a>
            <a class="nav-link <?= str_starts_with($current, 'appointment') ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/appointment/index">
                <i class="fa-regular fa-calendar-check"></i> Rendez-vous
            </a>
            <?php if ($isDoctor): ?>
                <a class="nav-link <?= str_starts_with($current, 'consultation') ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/consultation/index">
                    <i class="fa-solid fa-notes-medical"></i> Consultations
                </a>
                <a class="nav-link <?= str_starts_with($current, 'prescription') ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/prescription/index">
                    <i class="fa-solid fa-prescription-bottle-medical"></i> Ordonnances
                </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <span class="role-badge role-<?= e($user['role'] ?? 'guest') ?>">
                <i class="fa-solid fa-user-shield"></i>
                <?= e(ucfirst($user['role'] ?? 'guest')) ?>
            </span>
            <p class="sidebar-role-hint"><?= e($roleLabel) ?></p>
            <a class="nav-link" href="<?= e(BASE_URL) ?>/auth/logout">
                <i class="fa-solid fa-right-from-bracket"></i> Deconnexion
            </a>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="topbar-title">
                <h1><?= str_starts_with($current, 'dashboard') ? 'Dashboard' : e($title ?? 'Dashboard') ?></h1>
                <?php if (str_starts_with($current, 'dashboard')): ?>
                    <p>Bienvenue, Dr. Yassine Alaoui</p>
                <?php else: ?>
                    <p><?= e($roleLabel) ?> - Bienvenue, <?= e($user['full_name'] ?? '') ?></p>
                <?php endif; ?>
            </div>
        </header>

        <?php if ($flash): ?>
            <div class="notification notification-<?= e($flash['type']) ?>" data-auto-dismiss="true">
                <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
                <span><?= e($flash['message']) ?></span>
            </div>
        <?php endif; ?>

        <section class="content-area">
            <?= $content ?>
        </section>
    </main>
</div>

<script src="<?= e(BASE_URL) ?>/assets/js/app.js"></script>
</body>
</html>
