<?php
// Determina la acción actual para el link activo del sidebar
$current_action = $_GET['action'] ?? 'dashboard';
$nombre_usuario = $_SESSION['nombre_usuario'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'ClariFi' ?> — ClariFi</title>
    <meta name="description" content="Gestor financiero personal. Administrá tus ingresos, deudas e inversiones.">
    <!-- FontAwesome for Premium Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="app/views/assets/style.css">
</head>
<body>
    <div class="app-layout">

        <!-- Mobile Toggle -->
        <button class="mobile-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open'); document.querySelector('.mobile-overlay').classList.toggle('active');">☰</button>
        <div class="mobile-overlay" onclick="document.querySelector('.sidebar').classList.remove('open'); this.classList.remove('active');"></div>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand" style="display: flex; align-items: center; gap: 12px; padding: 24px 20px;">
                <img src="app/views/assets/img/logo.png" alt="ClariFi Logo" style="width: 32px; height: 32px; object-fit: contain; border-radius: 8px;">
                <div>
                    <h1 style="font-size: 1.4rem; margin: 0; line-height: 1.1;">ClariFi</h1>
                    <span style="font-size: 0.7rem; letter-spacing: 0.5px;">Claridad Financiera</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="index.php?action=dashboard" class="<?= $current_action === 'dashboard' ? 'active' : '' ?>">
                    <span class="icon"><i class="fa-solid fa-chart-pie"></i></span> Dashboard
                </a>
                <a href="index.php?action=gastos_recurrentes" class="<?= $current_action === 'gastos_recurrentes' ? 'active' : '' ?>">
                    <span class="icon"><i class="fa-solid fa-calendar-days"></i></span> Gastos Fijos
                </a>
                <a href="index.php?action=deudas" class="<?= $current_action === 'deudas' || $current_action === 'editar_deuda' ? 'active' : '' ?>">
                    <span class="icon"><i class="fa-solid fa-credit-card"></i></span> Deudas
                </a>

                <div class="nav-divider"></div>

                <a href="index.php?action=metas" class="<?= $current_action === 'metas' ? 'active' : '' ?>">
                    <span class="icon"><i class="fa-solid fa-bullseye"></i></span> Metas de Ahorro
                </a>
                <a href="index.php?action=inversiones" class="<?= $current_action === 'inversiones' ? 'active' : '' ?>">
                    <span class="icon"><i class="fa-solid fa-chart-line"></i></span> Inversiones
                </a>
                <a href="index.php?action=impuestos" class="<?= $current_action === 'impuestos' ? 'active' : '' ?>">
                    <span class="icon"><i class="fa-solid fa-receipt"></i></span> Fiscal
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="index.php?action=logout">
                    <span class="icon"><i class="fa-solid fa-arrow-right-from-bracket"></i></span> Cerrar Sesión
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
