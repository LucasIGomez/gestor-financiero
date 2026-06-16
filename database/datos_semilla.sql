USE gestor_financiero;

-- 1. Insertar Usuario de Prueba
INSERT INTO usuarios (nombre, email, password_hash, fecha_registro) 
VALUES ('Usuario Portfolio', 'admin@portfolio.com', 'hash_simulado_123', CURRENT_TIMESTAMP);

-- 2. Insertar Categorías (Ingresos y Gastos)
INSERT INTO categorias (id_usuario, nombre_categoria, tipo_flujo) VALUES 
(1, 'Salario', 'ingreso'),
(1, 'Servicios', 'gasto'),
(1, 'Supermercado', 'gasto'),
(1, 'Ocio', 'gasto');

-- 3. Insertar Deudas (Estructuradas para probar el Método Avalancha)
INSERT INTO deudas (id_usuario, nombre_deuda, saldo_total, cft, cuota_mensual, tipo_deuda) VALUES 
(1, 'Tarjeta de Crédito Visa', 250000.00, 120.50, 20000.00, 'tarjeta_credito'),
(1, 'Préstamo Automotor', 1500000.00, 65.00, 85000.00, 'prestamo'),
(1, 'Crédito Personal', 100000.00, 140.00, 15000.00, 'prestamo');

-- 4. Insertar Transacciones
INSERT INTO transacciones (id_usuario, id_categoria, monto, descripcion, fecha_transaccion) VALUES 
(1, 1, 950000.00, 'Salario Mensual', '2023-10-01'),
(1, 2, 45000.00, 'Internet y Luz', '2023-10-05'),
(1, 3, 120000.00, 'Compra mensual', '2023-10-10');