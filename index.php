<?php
// 1. Inicialización de Sesiones (DEBE ser la primera línea)
session_start();

// Habilitar errores para entorno de desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ==========================================
// CARGA DE CONTROLADORES
// ==========================================
require_once 'app/controllers/TransaccionController.php';
require_once 'app/controllers/DeudaController.php';
require_once 'app/controllers/InversionController.php';
require_once 'app/controllers/ImpuestoController.php';
require_once 'app/controllers/UsuarioController.php';
require_once 'app/controllers/MetaController.php';

$metaController = new MetaController();
$transaccionController = new TransaccionController();
$deudaController = new DeudaController();
$inversionController = new InversionController();
$impuestoController = new ImpuestoController();
$usuarioController = new UsuarioController();

$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

// ==========================================
// 2. PROTECCIÓN DE RUTAS (MIDDLEWARE BÁSICO)
// ==========================================
$rutas_publicas = ['login', 'login_post', 'registro', 'registro_post'];
$id_usuario_actual = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : null;

// Si no está logueado y la ruta solicitada no es pública, redirigir al login
if (!$id_usuario_actual && !in_array($action, $rutas_publicas)) {
    header('Location: index.php?action=login');
    exit;
}

// ==========================================
// 3. PROCESAMIENTO DE AUTENTICACIÓN
// ==========================================

if ($action === 'login') {
    require_once 'app/views/login_view.php';
} elseif ($action === 'login_post') {
    $resultado = $usuarioController->procesarLogin($_POST['email'], $_POST['password']);
    if ($resultado === true) {
        header('Location: index.php?action=dashboard');
        exit;
    } else {
        $error = $resultado;
        require_once 'app/views/login_view.php';
    }
} elseif ($action === 'registro') {
    require_once 'app/views/registro_view.php';
} elseif ($action === 'registro_post') {
    $resultado = $usuarioController->procesarRegistro($_POST['nombre'], $_POST['email'], $_POST['password']);
    if ($resultado === true) {
        // Redirigir al login tras un registro exitoso
        header('Location: index.php?action=login');
        exit;
    } else {
        $error = $resultado;
        require_once 'app/views/registro_view.php';
    }
} elseif ($action === 'logout') {
    $usuarioController->cerrarSesion();
    header('Location: index.php?action=login');
    exit;
}

// ==========================================
// 4. PROCESAMIENTO DE FORMULARIOS (POST)
// ==========================================

// Módulo: Transacciones (Crear)
elseif ($action === 'registrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
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

// Módulo: Deudas (Crear)
elseif ($action === 'registrar_deuda' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre_deuda'];
    $saldo = $_POST['saldo_total'];
    $tasa = $_POST['tasa_intereses'];
    $cuota = $_POST['cuota_mensual'];

    $resultado = $deudaController->procesarNuevaDeuda($id_usuario_actual, $nombre, $saldo, $tasa, $cuota);

    if ($resultado === true) {
        header('Location: index.php?action=deudas');
        exit;
    } else {
        echo "<h3 style='color:red;'>$resultado</h3>";
        echo "<a href='index.php?action=deudas'>Volver al Asesor de Deudas</a>";
        exit;
    }
}

// Módulo: Deudas (Actualizar)
elseif ($action === 'actualizar_deuda' && $_SERVER['REQUEST_METHOD'] === 'POST') {
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

// Módulo: Gastos Recurrentes (Crear)
elseif ($action === 'registrar_recurrente' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $transaccionController->procesarNuevoGastoRecurrente(
        $id_usuario_actual,
        $_POST['id_categoria'],
        $_POST['monto'],
        $_POST['descripcion'],
        $_POST['dia_cobro']
    );

    if ($resultado === true) {
        header('Location: index.php?action=gastos_recurrentes');
        exit;
    } else {
        echo "<h3 style='color:red;'>$resultado</h3>";
        echo "<a href='index.php?action=gastos_recurrentes'>Volver</a>";
        exit;
    }
}

// Módulo: Metas (Crear Nueva)
elseif ($action === 'registrar_meta' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $metaController->procesarNuevaMeta(
        $id_usuario_actual, $_POST['nombre_meta'], $_POST['monto_objetivo'], $_POST['fecha_limite']
    );

    if ($resultado === true) {
        header('Location: index.php?action=metas');
        exit;
    } else {
        echo "<h3 style='color:red;'>$resultado</h3><a href='index.php?action=metas'>Volver</a>";
        exit;
    }
}

// Módulo: Metas (Agregar Ahorro)
elseif ($action === 'agregar_ahorro' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $metaController->procesarAhorro(
        $_POST['id_meta'], $id_usuario_actual, $_POST['monto_deposito']
    );

    if ($resultado === true) {
        header('Location: index.php?action=metas');
        exit;
    } else {
        echo "<h3 style='color:red;'>$resultado</h3><a href='index.php?action=metas'>Volver</a>";
        exit;
    }
}

// ==========================================
// 5. CARGA DE VISTAS (GET - RENDERIZADO Y OTROS CÁLCULOS)
// ==========================================

elseif ($action === 'dashboard') {
    $datos = $transaccionController->obtenerDatosDashboard($id_usuario_actual);
    require_once 'app/views/dashboard_view.php';
} 

elseif ($action === 'deudas') {
    $deudas = $deudaController->obtenerResumenAvalancha($id_usuario_actual);
    $controlador = $deudaController; 
    require_once 'app/views/lista_deudas_view.php';
} 

elseif ($action === 'editar_deuda') {
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
} 

elseif ($action === 'inversiones') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $inversion_inicial = $_POST['inversion_inicial'];
        $adicion_mensual = $_POST['adicion_mensual'];
        $tasa_anual = $_POST['tasa_anual'];
        $anos = $_POST['anos'];
        
        $resultados = $inversionController->calcularProyeccion($inversion_inicial, $adicion_mensual, $tasa_anual, $anos);
    }
    require_once 'app/views/simulador_inversion_view.php';
} 

elseif ($action === 'impuestos') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $brutos = $_POST['ingresos_brutos'];
        $deducibles = $_POST['gastos_deducibles'];
        $iva = $_POST['porcentaje_iva'];
        $iibb = $_POST['porcentaje_iibb'];
        $ganancias = $_POST['porcentaje_ganancias'];
        
        $resultados = $impuestoController->calcularReservaFiscal($brutos, $deducibles, $iva, $ganancias, $iibb);
    }
    require_once 'app/views/calculadora_impuestos_view.php';
} 

// Módulo: Gastos Recurrentes (Vista)
elseif ($action === 'gastos_recurrentes') {
    $datos = $transaccionController->obtenerDatosGastosRecurrentes($id_usuario_actual);
    require_once 'app/views/gastos_recurrentes_view.php';
}

// Módulo: Metas Financieras (Vista)
elseif ($action === 'metas') {
    $metas = $metaController->obtenerDatosMetas($id_usuario_actual);
    require_once 'app/views/metas_view.php';
}

else {
    echo "<h2>Error 404: Módulo no encontrado.</h2>";
    echo "<a href='index.php'>Volver al inicio</a>";
}
?>