-- ============================================================
-- ClariFi — Datos de prueba para usuario "Test Use" (id_usuario = 2)
-- Contexto: Persona de 28 años, sueldo $1.200.000, con deudas típicas
-- ============================================================
USE gestor_financiero;

-- 1. CATEGORÍAS
INSERT INTO categorias (id_usuario, nombre_categoria, tipo_flujo) VALUES
(2, 'Salario',         'ingreso'),
(2, 'Freelance',       'ingreso'),
(2, 'Alquiler',        'gasto'),
(2, 'Servicios',       'gasto'),
(2, 'Supermercado',    'gasto'),
(2, 'Transporte',      'gasto'),
(2, 'Ocio',            'gasto'),
(2, 'Salud',           'gasto'),
(2, 'Suscripciones',   'gasto'),
(2, 'Vestimenta',      'gasto');

-- 2. DEUDAS (Ordenadas por CFT para método avalancha)
-- Tarjeta con CFT altísimo = prioridad #1
INSERT INTO deudas (id_usuario, nombre_deuda, tipo_deuda, saldo_total, cft, tna, tea, cuota_mensual, limite_credito, dia_cierre, dia_vencimiento, cuotas_totales, cuotas_pagadas, fecha_inicio) VALUES
(2, 'Visa Banco Galicia',    'tarjeta_credito', 385000.00, 245.80, 180.00, NULL, 42000.00, 800000.00, 10, 27, NULL, NULL, NULL),
(2, 'Mastercard BBVA',       'tarjeta_credito', 128000.00, 198.50, 152.00, NULL, 18500.00, 500000.00, 15, 5, NULL, NULL, NULL),
(2, 'Préstamo Personal BNA', 'prestamo',        920000.00, 165.00, 120.00, NULL, 78000.00, NULL, NULL, 10, 24, 12, '2025-11-01'),
(2, 'Crédito Automotor',     'prestamo',       2400000.00,  95.50,  72.00, NULL, 145000.00, NULL, NULL, 15, 48, 18, '2025-02-01');

-- 3. TRANSACCIONES DEL MES ACTUAL (Mayo 2026)
INSERT INTO transacciones (id_usuario, id_categoria, monto, descripcion, fecha_transaccion, id_deuda) VALUES
-- Ingresos
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Salario' LIMIT 1),    1200000.00, 'Sueldo Mayo',                          '2026-05-01', NULL),
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Freelance' LIMIT 1),    85000.00, 'Diseño logo cliente',                  '2026-05-03', NULL),
-- Gastos fijos
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Alquiler' LIMIT 1),    320000.00, 'Alquiler departamento',                '2026-05-01', NULL),
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Servicios' LIMIT 1),    18500.00, 'Luz EDENOR',                           '2026-05-05', NULL),
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Servicios' LIMIT 1),    12000.00, 'Gas Metrogas',                         '2026-05-05', NULL),
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Servicios' LIMIT 1),    15800.00, 'Internet Fibertel 300mb',              '2026-05-06', NULL),
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Servicios' LIMIT 1),     8900.00, 'Celular Personal',                     '2026-05-06', NULL),
-- Gastos variables (efectivo/débito)
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Supermercado' LIMIT 1),  95000.00, 'Compra semanal Coto',                  '2026-05-02', NULL),
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Supermercado' LIMIT 1),  42000.00, 'Verdulería y carnicería',              '2026-05-04', NULL),
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Transporte' LIMIT 1),   28000.00, 'Carga SUBE + peajes',                  '2026-05-03', NULL),
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Ocio' LIMIT 1),         35000.00, 'Cena con amigos',                      '2026-05-07', NULL),
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Salud' LIMIT 1),        22000.00, 'Farmacia + vitaminas',                 '2026-05-04', NULL),
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Suscripciones' LIMIT 1), 5500.00, 'Spotify Premium',                      '2026-05-01', NULL),
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Suscripciones' LIMIT 1), 8200.00, 'Netflix Estándar',                     '2026-05-01', NULL),
-- Gastos con tarjeta de crédito (suman al saldo)
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Vestimenta' LIMIT 1),   65000.00, 'Zapatillas Nike (3 cuotas sin interés)', '2026-05-06',
   (SELECT id_deuda FROM deudas WHERE id_usuario=2 AND nombre_deuda='Visa Banco Galicia' LIMIT 1)),
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Ocio' LIMIT 1),         18000.00, 'Entrada recital + consumición',        '2026-05-08',
   (SELECT id_deuda FROM deudas WHERE id_usuario=2 AND nombre_deuda='Mastercard BBVA' LIMIT 1));

-- 4. GASTOS RECURRENTES (Plantillas de automatización)
INSERT INTO gastos_recurrentes (id_usuario, id_categoria, monto, descripcion, dia_cobro, ultimo_procesamiento) VALUES
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Alquiler' LIMIT 1),     320000.00, 'Alquiler departamento',       1, '2026-05-01'),
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Servicios' LIMIT 1),     15800.00, 'Internet Fibertel 300mb',     6, '2026-05-06'),
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Servicios' LIMIT 1),      8900.00, 'Celular Personal',            6, '2026-05-06'),
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Suscripciones' LIMIT 1),  5500.00, 'Spotify Premium',             1, '2026-05-01'),
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Suscripciones' LIMIT 1),  8200.00, 'Netflix Estándar',            1, '2026-05-01'),
(2, (SELECT id_categoria FROM categorias WHERE id_usuario=2 AND nombre_categoria='Transporte' LIMIT 1),    28000.00, 'Carga SUBE mensual',         15, NULL);

-- 5. METAS DE AHORRO
INSERT INTO metas_financieras (id_usuario, nombre_meta, monto_objetivo, saldo_actual, fecha_limite) VALUES
(2, 'Fondo de Emergencia (3 meses)', 3600000.00, 450000.00, '2026-12-31'),
(2, 'Viaje Europa 2027',             2500000.00, 180000.00, '2027-06-15'),
(2, 'Notebook nueva',                 800000.00, 620000.00, '2026-08-01');
