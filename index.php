<?php
// Habilitar errores para entorno de desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Carga de Controladores
require_once 'app/controllers/TransaccionController.php';
require_once 'app/controllers/DeudaController.php';

$transaccionController = new TransaccionController();
$deudaController = new DeudaController();

// 1. Identificación del usuario (Hardcodeado a ID 1 para el MVP)
$id_usuario_actual = 1; 

// 2. Sistema de enrutamiento
$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

// ==========================================
// 3. PROCESAMIENTO DE FORMULARIOS (POST)
// ==========================================

// Módulo: Transacciones
if ($action === 'registrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_categoria = $_POST['id_categoria'];
    $monto = $_POST['monto'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha_transaccion'];

    $resultado = $transaccionController->procesarNuevaTransaccion($id_usuario_actual, $id_categoria, $monto, $descripcion, $fecha);

    if ($resultado === true) {
        header('Location: index.php?action=dashboard');
        exit;
    } else {
        echo "<h3 style='color:red;'>$resultado</h3>";
        echo "<a href='index.php'>Volver</a>";
        exit;
    }
}

// Módulo: Deudas (NUEVO)
if ($action === 'registrar_deuda' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre_deuda'];
    $saldo = $_POST['saldo_total'];
    $tasa = $_POST['tasa_intereses'];
    $cuota = $_POST['cuota_mensual'];

    // Enviamos los datos al controlador para validación matemática e inserción
    $resultado = $deudaController->procesarNuevaDeuda($id_usuario_actual, $nombre, $saldo, $tasa, $cuota);

    if ($resultado === true) {
        // Redirección PRG para evitar reenvío de formulario al recargar
        header('Location: index.php?action=deudas');
        exit;
    } else {
        echo "<h3 style='color:red;'>$resultado</h3>";
        echo "<a href='index.php?action=deudas'>Volver al Asesor de Deudas</a>";
        exit;
    }
}

// ==========================================
// 4. CARGA DE VISTAS (GET - RENDERIZADO)
// ==========================================

if ($action === 'dashboard') {
    $datos = $transaccionController->obtenerDatosDashboard($id_usuario_actual);
    require_once 'app/views/dashboard_view.php';
} elseif ($action === 'deudas') {
    $deudas = $deudaController->obtenerResumenAvalancha($id_usuario_actual);
    $controlador = $deudaController; 
    require_once 'app/views/lista_deudas_view.php';
} else {
    echo "<h2>Error 404: Módulo no encontrado.</h2>";
}
?>