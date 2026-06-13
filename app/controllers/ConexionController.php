<?php
require_once 'app/models/ConexionModel.php';
require_once 'app/models/ReglaModel.php';
require_once 'app/models/CategoriaModel.php';
require_once 'app/models/TransaccionModel.php';

class ConexionController {
    private $conexionModel;
    private $reglaModel;
    private $categoriaModel;
    private $transaccionModel;

    public function __construct() {
        $this->conexionModel = new ConexionModel();
        $this->reglaModel = new ReglaModel();
        $this->categoriaModel = new CategoriaModel();
        $this->transaccionModel = new TransaccionModel();
    }

    // Carga todos los datos necesarios para la vista de conexiones
    public function obtenerDatosConexiones($id_usuario) {
        $conexiones = $this->conexionModel->obtenerConexionesUsuario($id_usuario);
        $reglas = $this->reglaModel->obtenerReglasUsuario($id_usuario);
        $categorias = $this->categoriaModel->obtenerCategorias($id_usuario);

        // Tasas por defecto en el mercado
        $tasas_mercado = [
            'mercado_pago' => 68.00,
            'naranja_x'    => 72.00,
            'uala'         => 65.00,
            'lemon_cash'   => 50.00
        ];

        // Mapear nombres legibles de billeteras
        $nombres_billeteras = [
            'mercado_pago' => 'Mercado Pago',
            'naranja_x'    => 'Naranja X',
            'uala'         => 'Ualá',
            'lemon_cash'   => 'Lemon Cash'
        ];

        // Lógica de Asesoramiento de Rendimiento (Yield Advisor)
        $recomendaciones = [];
        $mejor_billetera = 'naranja_x'; // 72% TNA es el máximo por defecto
        
        $mp_conectado = false;
        $nx_conectado = false;
        $saldo_mp = 0.00;
        $saldo_nx = 0.00;

        foreach ($conexiones as $con) {
            if ($con['billetera'] === 'mercado_pago') {
                $mp_conectado = true;
                $saldo_mp = (float)$con['saldo_simulado'];
            }
            if ($con['billetera'] === 'naranja_x') {
                $nx_conectado = true;
                $saldo_nx = (float)$con['saldo_simulado'];
            }
        }

        if ($mp_conectado && $nx_conectado && $saldo_mp > $saldo_nx) {
            $diferencia_tasa = 72.00 - 68.00;
            $rendimiento_anual_adicional = $saldo_mp * ($diferencia_tasa / 100);
            $recomendaciones[] = [
                'tipo'    => 'optimizar',
                'titulo'  => '¡Optimizá tus Rendimientos!',
                'mensaje' => "Tenés $" . number_format($saldo_mp, 2) . " en Mercado Pago (68% TNA). Si movés este saldo a Naranja X (72% TNA), ganarías aproximadamente $" . number_format($rendimiento_anual_adicional, 2) . " adicionales al año de interés nominal libre."
            ];
        }

        if (empty($conexiones)) {
            $recomendaciones[] = [
                'tipo'    => 'conectar',
                'titulo'  => 'Sin billeteras vinculadas',
                'mensaje' => 'Conectá tu cuenta de Mercado Pago o Naranja X para comparar tasas automáticas y empezar a categorizar tus movimientos sin esfuerzo.'
            ];
        }

        return [
            'conexiones'         => $conexiones,
            'reglas'             => $reglas,
            'categorias'         => $categorias,
            'tasas_mercado'      => $tasas_mercado,
            'nombres_billeteras' => $nombres_billeteras,
            'recomendaciones'    => $recomendaciones
        ];
    }

    // Procesa el formulario de vinculación de billetera
    public function procesarVinculacion($id_usuario, $post) {
        $billetera = $post['billetera'] ?? '';
        $token = $post['access_token'] ?? '';
        $alias = $post['alias_personal'] ?? '';
        $saldo = floatval($post['saldo_inicial'] ?? 0);

        // Validar tasa según mercado
        $tasas = [
            'mercado_pago' => 68.00,
            'naranja_x'    => 72.00,
            'uala'         => 65.00,
            'lemon_cash'   => 50.00
        ];

        $tasa = $tasas[$billetera] ?? 0.00;

        if (empty($billetera) || empty($token) || empty($alias) || $saldo < 0) {
            return "Error: Todos los campos del formulario de conexión son requeridos.";
        }

        $exito = $this->conexionModel->vincularBilletera($id_usuario, $billetera, $token, $alias, $tasa, $saldo);
        return $exito ? true : "Error: No se pudo conectar la billetera.";
    }

    // Desconecta una billetera
    public function procesarDesconexion($id_conexion, $id_usuario) {
        return $this->conexionModel->desconectarBilletera($id_conexion, $id_usuario);
    }

    // Crea una nueva regla de categorización inteligente
    public function procesarNuevaRegla($id_usuario, $post) {
        $patron = trim($post['patron'] ?? '');
        $id_categoria = intval($post['id_categoria'] ?? 0);
        $nombre_fantasia = trim($post['nombre_fantasia'] ?? '');

        if (empty($patron) || $id_categoria <= 0) {
            return "Error: El patrón y la categoría son requeridos.";
        }

        $exito = $this->reglaModel->registrarRegla($id_usuario, $patron, $id_categoria, $nombre_fantasia);
        return $exito ? true : "Error: No se pudo registrar la regla.";
    }

    // Elimina una regla
    public function procesarEliminarRegla($id_regla, $id_usuario) {
        return $this->reglaModel->eliminarRegla($id_regla, $id_usuario);
    }

    // Simulación Sandbox: Descarga movimientos mock de las billeteras vinculadas
    public function procesarSincronizacionSimulada($id_usuario) {
        $conexiones = $this->conexionModel->obtenerConexionesUsuario($id_usuario);
        if (empty($conexiones)) {
            return "Error: Primero debés conectar al menos una billetera virtual.";
        }

        $id_por_clasificar = $this->categoriaModel->obtenerOCrearCategoriaPorClasificar($id_usuario);
        $id_rendimientos = $this->categoriaModel->obtenerOCrearCategoriaRendimientos($id_usuario);

        $fecha_actual = date('Y-m-d');
        $transacciones_sincronizadas = 0;

        foreach ($conexiones as $con) {
            $billetera = $con['billetera'];
            $saldo_actual = (float)$con['saldo_simulado'];
            
            // Generamos movimientos mock adaptados
            $movimientos = [];
            if ($billetera === 'mercado_pago') {
                $movimientos = [
                    [
                        'tipo' => 'gasto',
                        'monto' => 4560.00,
                        'descripcion' => 'Transferencia enviada a Coto S.A. CUIT 30-5464...',
                        'patron_busqueda' => 'Coto'
                    ],
                    [
                        'tipo' => 'ingreso_rendimiento',
                        'monto' => 1250.00,
                        'descripcion' => 'Rendimiento diario Mercado Fondo'
                    ],
                    [
                        'tipo' => 'gasto',
                        'monto' => 5400.00,
                        'descripcion' => 'Transferencia enviada a verduleria.pepe alias MP',
                        'patron_busqueda' => 'verduleria.pepe'
                    ]
                ];
            } elseif ($billetera === 'naranja_x') {
                $movimientos = [
                    [
                        'tipo' => 'ingreso_rendimiento',
                        'monto' => 2100.00,
                        'descripcion' => 'Rendimiento diario Cuenta Remunerada Naranja X'
                    ],
                    [
                        'tipo' => 'gasto',
                        'monto' => 120000.00,
                        'descripcion' => 'Transferencia enviada a Juan Perez por Alquiler Junio',
                        'patron_busqueda' => 'Juan Perez'
                    ]
                ];
            } elseif ($billetera === 'uala') {
                $movimientos = [
                    [
                        'tipo' => 'gasto',
                        'monto' => 8500.00,
                        'descripcion' => 'Consumo tarjeta prepaga Uala: Cervecería Patagonia',
                        'patron_busqueda' => 'Patagonia'
                    ]
                ];
            } elseif ($billetera === 'lemon_cash') {
                $movimientos = [
                    [
                        'tipo' => 'gasto',
                        'monto' => 3500.00,
                        'descripcion' => 'Compra Lemon Card en Farmacity Suc. Palermo',
                        'patron_busqueda' => 'Farmacity'
                    ]
                ];
            }

            // Procesar cada movimiento e insertarlo
            foreach ($movimientos as $mov) {
                $monto = $mov['monto'];
                $descripcion = $mov['descripcion'];

                // 1. Evaluar si coincide con alguna regla inteligente
                $id_cat_asignada = null;
                if ($mov['tipo'] === 'gasto') {
                    $coincidencia = $this->reglaModel->evaluarDescripcion($id_usuario, $descripcion);
                    if ($coincidencia) {
                        $id_cat_asignada = $coincidencia['id_categoria'];
                        $descripcion = $coincidencia['nombre_fantasia'] . " (Auto)";
                    } else {
                        $id_cat_asignada = $id_por_clasificar;
                    }
                    $saldo_actual -= $monto;
                } elseif ($mov['tipo'] === 'ingreso_rendimiento') {
                    $id_cat_asignada = $id_rendimientos;
                    $saldo_actual += $monto;
                }

                // 2. Registrar la transacción en la tabla principal
                $this->transaccionModel->registrarTransaccion(
                    $id_usuario,
                    $id_cat_asignada,
                    $monto,
                    $descripcion,
                    $fecha_actual
                );

                $transacciones_sincronizadas++;
            }

            // 3. Guardar el nuevo saldo de la billetera
            $this->conexionModel->actualizarSaldoBilletera($con['id_conexion'], $id_usuario, $saldo_actual);
        }

        return $transacciones_sincronizadas;
    }
}
?>
