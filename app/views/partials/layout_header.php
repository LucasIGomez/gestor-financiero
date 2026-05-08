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
    <link rel="stylesheet" href="app/views/assets/style.css">
</head>
<body>
    <div class="app-layout">

        <!-- Mobile Toggle -->
        <button class="mobile-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open'); document.querySelector('.mobile-overlay').classList.toggle('active');">☰</button>
        <div class="mobile-overlay" onclick="document.querySelector('.sidebar').classList.remove('open'); this.classList.remove('active');"></div>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <h1>ClariFi</h1>
                <span>Claridad Financiera</span>
            </div>

            <nav class="sidebar-nav">
                <a href="index.php?action=dashboard" class="<?= $current_action === 'dashboard' ? 'active' : '' ?>">
                    <span class="icon">📊</span> Dashboard
                </a>
                <a href="index.php?action=gastos_recurrentes" class="<?= $current_action === 'gastos_recurrentes' ? 'active' : '' ?>">
                    <span class="icon">🔄</span> Gastos Fijos
                </a>
                <a href="index.php?action=deudas" class="<?= $current_action === 'deudas' || $current_action === 'editar_deuda' ? 'active' : '' ?>">
                    <span class="icon">💳</span> Deudas
                </a>

                <div class="nav-divider"></div>

                <a href="index.php?action=metas" class="<?= $current_action === 'metas' ? 'active' : '' ?>">
                    <span class="icon">🎯</span> Metas de Ahorro
                </a>
                <a href="index.php?action=inversiones" class="<?= $current_action === 'inversiones' ? 'active' : '' ?>">
                    <span class="icon">📈</span> Inversiones
                </a>
                <a href="index.php?action=impuestos" class="<?= $current_action === 'impuestos' ? 'active' : '' ?>">
                    <span class="icon">🧾</span> Fiscal
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="index.php?action=logout">
                    <span class="icon">🚪</span> Cerrar Sesión
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
