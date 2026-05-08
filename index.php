<?php
session_start();

// 1. INCLUSIÓN DE CONTROLADORES
require_once 'app/controllers/UsuarioController.php';
require_once 'app/controllers/TransaccionController.php';
require_once 'app/controllers/DeudaController.php';
require_once 'app/controllers/MetaController.php';
require_once 'app/controllers/ImpuestoController.php';
require_once 'app/controllers/InversionController.php';

// 2. INSTANCIACIÓN DE CONTROLADORES
$usuarioController = new UsuarioController();
$transaccionController = new TransaccionController();
$deudaController = new DeudaController();
$metaController = new MetaController();
$impuestoController = new ImpuestoController();
$inversionController = new InversionController();

$action = $_GET['action'] ?? 'login';

// 3. RUTAS PÚBLICAS (AUTENTICACIÓN)
if ($action === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $resultado = $usuarioController->procesarLogin($_POST['email'], $_POST['password']);
        if ($resultado === true) {
            header("Location: index.php?action=bienvenida");
            exit;
        } else {
            $error = $resultado;
            require_once 'app/views/login_view.php';
        }
    } else {
        require_once 'app/views/login_view.php';
    }
    exit;
} elseif ($action === 'registro') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $resultado = $usuarioController->procesarRegistro($_POST['nombre'], $_POST['email'], $_POST['password']);
        if ($resultado === true) {
            header("Location: index.php?action=login&registrado=1");
            exit;
        } else {
            $error = $resultado;
            require_once 'app/views/registro_view.php';
        }
    } else {
        require_once 'app/views/registro_view.php';
    }
    exit;
} elseif ($action === 'logout') {
    $usuarioController->cerrarSesion();
    header("Location: index.php?action=login");
    exit;
}

// PROTECCIÓN DE RUTAS PRIVADAS
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php?action=login");
    exit;
}

// 4. PROCESAMIENTO DE FORMULARIOS (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if ($action === 'registrar') {
        // Procesamiento de Nueva Transacción (Con soporte para tarjeta de crédito V3)
        $id_deuda_post = !empty($_POST['id_deuda']) ? $_POST['id_deuda'] : null;
        
        $resultado = $transaccionController->procesarNuevaTransaccion(
            $_SESSION['id_usuario'], 
            $_POST['id_categoria'], 
            $_POST['monto'], 
            $_POST['descripcion'], 
            $_POST['fecha_transaccion'],
            $id_deuda_post
        );
        
        if ($resultado === true) {
            header("Location: index.php?action=dashboard");
            exit;
        } else {
            echo "<script>alert('$resultado'); window.history.back();</script>";
        }
        
    } elseif ($action === 'registrar_deuda') {
        // Procesamiento Masivo de Deudas / Tarjetas
        $resultado = $deudaController->procesarNuevaDeuda($_POST, $_SESSION['id_usuario']);
        if ($resultado === true) {
            header("Location: index.php?action=deudas");
            exit;
        } else {
            echo "<script>alert('$resultado'); window.history.back();</script>";
        }
        
    } elseif ($action === 'editar_deuda_procesar') {
        // Actualización Masiva de Deudas / Tarjetas
        $resultado = $deudaController->procesarEdicionDeuda($_POST, $_SESSION['id_usuario']);
        if ($resultado === true) {
            header("Location: index.php?action=deudas");
            exit;
        } else {
            echo "<script>alert('$resultado'); window.history.back();</script>";
        }
        
    } elseif ($action === 'registrar_gasto_recurrente') {
        // Registro de Gastos Fijos (Pseudo-Cron)
        $resultado = $transaccionController->procesarNuevoGastoRecurrente(
            $_SESSION['id_usuario'], 
            $_POST['id_categoria'], 
            $_POST['monto'], 
            $_POST['descripcion'], 
            $_POST['dia_cobro']
        );
        if ($resultado === true) {
            header("Location: index.php?action=gastos_recurrentes");
            exit;
        } else {
            echo "<script>alert('$resultado'); window.history.back();</script>";
        }

    } elseif ($action === 'registrar_meta') {
        // Registro de Nueva Meta de Ahorro
        $resultado = $metaController->procesarNuevaMeta(
            $_SESSION['id_usuario'],
            $_POST['nombre_meta'],
            $_POST['monto_objetivo'],
            $_POST['fecha_limite']
        );
        if ($resultado === true) {
            header("Location: index.php?action=metas");
            exit;
        } else {
            echo "<script>alert('$resultado'); window.history.back();</script>";
        }

    } elseif ($action === 'agregar_ahorro') {
        // Depósito de Ahorro en una Meta Existente
        $resultado = $metaController->procesarAhorro(
            $_POST['id_meta'],
            $_SESSION['id_usuario'],
            $_POST['monto_deposito']
        );
        if ($resultado === true) {
            header("Location: index.php?action=metas");
            exit;
        } else {
            echo "<script>alert('$resultado'); window.history.back();</script>";
        }

    } elseif ($action === 'inversiones') {
        // Simulador de Interés Compuesto (POST calcula, luego renderiza)
        $resultados = $inversionController->calcularProyeccion(
            $_POST['inversion_inicial'],
            $_POST['adicion_mensual'],
            $_POST['tasa_anual'],
            $_POST['anos']
        );
        require_once 'app/views/simulador_inversion_view.php';
        exit;

    } elseif ($action === 'impuestos') {
        // Calculadora Fiscal (POST calcula, luego renderiza)
        $resultados = $impuestoController->calcularReservaFiscal(
            $_POST['ingresos_brutos'],
            $_POST['gastos_deducibles'],
            $_POST['porcentaje_iva'],
            $_POST['porcentaje_ganancias'],
            $_POST['porcentaje_iibb']
        );
        require_once 'app/views/calculadora_impuestos_view.php';
        exit;

    } elseif ($action === 'registrar_pago_deuda') {
        $resultado = $deudaController->procesarPagoDeuda($_POST, $_SESSION['id_usuario']);
        header("Location: index.php?action=deudas");
        exit;
    }
}

// 5. CARGA DE VISTAS (GET - RENDERIZADO)
if ($action === 'bienvenida') {
    require_once 'app/views/bienvenida_view.php';

} elseif ($action === 'dashboard') {
    $datos = $transaccionController->obtenerDatosDashboard($_SESSION['id_usuario']);
    require_once 'app/views/dashboard_view.php';
    
} elseif ($action === 'deudas') {
    $datos = $deudaController->obtenerDatosDeudas($_SESSION['id_usuario']);
    require_once 'app/views/lista_deudas_view.php';
    
} elseif ($action === 'editar_deuda') {
    if (isset($_GET['id'])) {
        $deuda = $deudaController->obtenerDeuda($_GET['id'], $_SESSION['id_usuario']);
        if ($deuda) {
            require_once 'app/views/editar_deuda_view.php';
        } else {
            echo "<script>alert('Error: Deuda no encontrada.'); window.location.href='index.php?action=deudas';</script>";
        }
    }
    
} elseif ($action === 'eliminar_deuda') {
    if (isset($_GET['id'])) {
        $resultado = $deudaController->procesarEliminacionDeuda($_GET['id'], $_SESSION['id_usuario']);
        header("Location: index.php?action=deudas");
        exit;
    }

} elseif ($action === 'gastos_recurrentes') {
    $datos = $transaccionController->obtenerDatosGastosRecurrentes($_SESSION['id_usuario']);
    require_once 'app/views/gastos_recurrentes_view.php';

} elseif ($action === 'metas') {
    $metas = $metaController->obtenerDatosMetas($_SESSION['id_usuario']);
    require_once 'app/views/metas_view.php';

} elseif ($action === 'inversiones') {
    require_once 'app/views/simulador_inversion_view.php';

} elseif ($action === 'impuestos') {
    require_once 'app/views/calculadora_impuestos_view.php';
}
?>