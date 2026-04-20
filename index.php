<?php
// Habilitar errores para entorno de desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Carga de Controladores
require_once 'app/controllers/TransaccionController.php';
require_once 'app/controllers/DeudaController.php';
require_once 'app/controllers/InversionController.php';
require_once 'app/controllers/ImpuestoController.php';

$impuestoController = new ImpuestoController();
$transaccionController = new TransaccionController();
$deudaController = new DeudaController();
$inversionController = new InversionController();

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

// Módulo: Deudas (ACTUALIZAR)
if ($action === 'actualizar_deuda' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_deuda = $_POST['id_deuda'];
    $nombre = $_POST['nombre_deuda'];
    $saldo = $_POST['saldo_total'];
    $tasa = $_POST['tasa_intereses'];
    $cuota = $_POST['cuota_mensual'];

    $resultado = $deudaController->procesarActualizacionDeuda($id_deuda, $id_usuario_actual, $nombre, $saldo, $tasa, $cuota);

    if ($resultado === true) {
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
    // Carga la vista principal de ingresos y gastos
    $datos = $transaccionController->obtenerDatosDashboard($id_usuario_actual);
    require_once 'app/views/dashboard_view.php';

} elseif ($action === 'deudas') {
    // Carga la vista de la lista de deudas (Método Avalancha)
    $deudas = $deudaController->obtenerResumenAvalancha($id_usuario_actual);
    $controlador = $deudaController; 
    require_once 'app/views/lista_deudas_view.php';

} elseif ($action === 'editar_deuda') {
    // Carga la vista del formulario para editar una deuda específica
    // Validar que se reciba un ID por la URL
    if (isset($_GET['id'])) {
        $deuda = $deudaController->obtenerDeudaEspecifica($_GET['id'], $id_usuario_actual);
        if ($deuda) {
            require_once 'app/views/editar_deuda_view.php';
        } else {
            echo "<h2>Error: Deuda no encontrada o acceso denegado.</h2>";
            echo "<a href='index.php?action=deudas'>Volver al Asesor de Deudas</a>";
        }
    } else {
        echo "<h2>Error: ID de deuda no especificado.</h2>";
        echo "<a href='index.php?action=deudas'>Volver al Asesor de Deudas</a>";
    }

} elseif ($action === 'inversiones') {
    // Si el usuario envía el formulario, procesamos la lógica
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $inversion_inicial = $_POST['inversion_inicial'];
        $adicion_mensual = $_POST['adicion_mensual'];
        $tasa_anual = $_POST['tasa_anual'];
        $anos = $_POST['anos'];
        
        $resultados = $inversionController->calcularProyeccion($inversion_inicial, $adicion_mensual, $tasa_anual, $anos);
    }
    // Renderizamos la vista del simulador
    require_once 'app/views/simulador_inversion_view.php'; 
} elseif ($action === 'impuestos') {
    // Procesamiento de la calculadora fiscal
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $brutos = $_POST['ingresos_brutos'];
        $deducibles = $_POST['gastos_deducibles'];
        $iva = $_POST['porcentaje_iva'];
        $iibb = $_POST['porcentaje_iibb'];
        $ganancias = $_POST['porcentaje_ganancias'];
        
        $resultados = $impuestoController->calcularReservaFiscal($brutos, $deducibles, $iva, $ganancias, $iibb);
    }
    require_once 'app/views/calculadora_impuestos_view.php';

} else {
    // Manejo de rutas inexistentes (Error 404)
    echo "<h2>Error 404: Módulo no encontrado.</h2>";
    echo "<a href='index.php'>Volver al inicio</a>";
}
?>