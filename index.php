<?php
// Habilitar errores para entorno de desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'app/controllers/TransaccionController.php';

$controlador = new TransaccionController();

// 1. Identificación del usuario (Hardcodeado a ID 1 para el MVP)
$id_usuario_actual = 1; 

// 2. Sistema básico de enrutamiento
$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

// 3. Procesamiento de formulario (Guardar en Base de Datos)
if ($action === 'registrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_categoria = $_POST['id_categoria'];
    $monto = $_POST['monto'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha_transaccion'];

    $resultado = $controlador->procesarNuevaTransaccion($id_usuario_actual, $id_categoria, $monto, $descripcion, $fecha);

    if ($resultado === true) {
        // Patrón PRG (Post/Redirect/Get) para evitar reenvío de formularios al recargar
        header('Location: index.php?action=dashboard');
        exit;
    } else {
        // En caso de error, muestra el mensaje y detiene la ejecución
        echo "<h3 style='color:red;'>$resultado</h3>";
        echo "<a href='index.php'>Volver</a>";
        exit;
    }
}

// 4. Carga de la Vista Principal
if ($action === 'dashboard') {
    // Extraer array multidimensional con datos financieros [cite: 387, 388]
    $datos = $controlador->obtenerDatosDashboard($id_usuario_actual);
    require_once 'app/views/dashboard_view.php';
}
?>