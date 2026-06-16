-- ============================================================
-- ClariFi — Esquema de Base de Datos Unificado
-- ============================================================

CREATE DATABASE IF NOT EXISTS gestor_financiero CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestor_financiero;

-- Borrar tablas dependientes primero para evitar violaciones de clave foránea
DROP TABLE IF EXISTS reglas_categorizacion;
DROP TABLE IF EXISTS presupuestos;
DROP TABLE IF EXISTS inversiones;
DROP TABLE IF EXISTS historico_saldos;
DROP TABLE IF EXISTS conexiones_bancarias;
DROP TABLE IF EXISTS transacciones;
DROP TABLE IF EXISTS metas_financieras;
DROP TABLE IF EXISTS gastos_recurrentes;
DROP TABLE IF EXISTS deudas;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS usuarios;

-- 1. Tabla: usuarios
CREATE TABLE usuarios (
  id_usuario int NOT NULL AUTO_INCREMENT,
  nombre varchar(50) NOT NULL,
  email varchar(100) NOT NULL,
  password_hash varchar(255) NOT NULL,
  fecha_registro datetime NOT NULL,
  PRIMARY KEY (id_usuario),
  UNIQUE KEY email_UNIQUE (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabla: categorias
CREATE TABLE categorias (
  id_categoria int NOT NULL AUTO_INCREMENT,
  nombre_categoria varchar(50) NOT NULL,
  tipo_flujo enum('ingreso','gasto') NOT NULL,
  id_usuario int NOT NULL,
  PRIMARY KEY (id_categoria),
  KEY fk_categorias_usuarios_idx (id_usuario),
  CONSTRAINT fk_categorias_usuarios FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabla: deudas
CREATE TABLE deudas (
  id_deuda int NOT NULL AUTO_INCREMENT,
  nombre_deuda varchar(100) NOT NULL,
  saldo_total decimal(18,2) NOT NULL,
  cuota_mensual decimal(18,2) NOT NULL,
  tipo_deuda enum('prestamo','tarjeta_credito') NOT NULL,
  cft decimal(10,2) NOT NULL,
  tna decimal(10,2) DEFAULT NULL,
  tea int DEFAULT NULL,
  id_usuario int NOT NULL,
  limite_credito decimal(18,2) DEFAULT NULL,
  dia_cierre int DEFAULT NULL,
  dia_vencimiento int DEFAULT NULL,
  cuotas_totales int DEFAULT NULL,
  cuotas_pagadas int DEFAULT NULL,
  fecha_inicio date DEFAULT NULL,
  PRIMARY KEY (id_deuda),
  KEY fk_deudas_usuarios1_idx (id_usuario),
  CONSTRAINT fk_deudas_usuarios1 FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabla: transacciones
CREATE TABLE transacciones (
  id_transaccion int NOT NULL AUTO_INCREMENT,
  monto decimal(18,2) NOT NULL,
  descripcion varchar(255) DEFAULT NULL,
  fecha_transaccion date NOT NULL,
  id_usuario int NOT NULL,
  id_categoria int NOT NULL,
  id_deuda int DEFAULT NULL,
  PRIMARY KEY (id_transaccion),
  KEY fk_transacciones_usuarios1_idx (id_usuario),
  KEY fk_transacciones_categorias1_idx (id_categoria),
  KEY fk_transacciones_deudas1_idx (id_deuda),
  CONSTRAINT fk_transacciones_categorias1 FOREIGN KEY (id_categoria) REFERENCES categorias (id_categoria) ON DELETE RESTRICT,
  CONSTRAINT fk_transacciones_deudas1 FOREIGN KEY (id_deuda) REFERENCES deudas (id_deuda) ON DELETE SET NULL,
  CONSTRAINT fk_transacciones_usuarios1 FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabla: gastos_recurrentes
CREATE TABLE gastos_recurrentes (
  id_recurrente int NOT NULL AUTO_INCREMENT,
  monto decimal(18,2) NOT NULL,
  descripcion varchar(255) DEFAULT NULL,
  dia_cobro int NOT NULL,
  ultimo_procesamiento date DEFAULT NULL,
  id_usuario int NOT NULL,
  id_categoria int NOT NULL,
  PRIMARY KEY (id_recurrente),
  KEY fk_gastos_recurrentes_usuarios1_idx (id_usuario),
  KEY fk_gastos_recurrentes_categorias1_idx (id_categoria),
  CONSTRAINT fk_gastos_recurrentes_categorias1 FOREIGN KEY (id_categoria) REFERENCES categorias (id_categoria) ON DELETE RESTRICT,
  CONSTRAINT fk_gastos_recurrentes_usuarios1 FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tabla: metas_financieras
CREATE TABLE metas_financieras (
  id_meta int NOT NULL AUTO_INCREMENT,
  nombre_meta varchar(100) NOT NULL,
  monto_objetivo decimal(18,2) NOT NULL,
  saldo_actual decimal(18,2) NOT NULL,
  fecha_limite date NOT NULL,
  id_usuario int NOT NULL,
  PRIMARY KEY (id_meta),
  KEY fk_metas_financieras_usuarios1_idx (id_usuario),
  CONSTRAINT fk_metas_financieras_usuarios1 FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Tabla: conexiones_bancarias
CREATE TABLE conexiones_bancarias (
  id_conexion int NOT NULL AUTO_INCREMENT,
  id_usuario int NOT NULL,
  billetera varchar(50) NOT NULL,
  access_token text NOT NULL,
  alias_personal varchar(100) DEFAULT NULL,
  tasa_anual decimal(10,2) DEFAULT 0.00,
  saldo_simulado decimal(18,2) DEFAULT 0.00,
  estado varchar(20) DEFAULT 'activo',
  PRIMARY KEY (id_conexion),
  UNIQUE KEY idx_usuario_billetera (id_usuario, billetera),
  CONSTRAINT fk_conexiones_usuarios FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Tabla: historico_saldos
CREATE TABLE historico_saldos (
  id_historico int NOT NULL AUTO_INCREMENT,
  id_usuario int NOT NULL,
  periodo date NOT NULL,
  total_ingresos decimal(18,2) DEFAULT 0.00,
  total_gastos decimal(18,2) DEFAULT 0.00,
  total_deudas decimal(18,2) DEFAULT 0.00,
  total_ahorros decimal(18,2) DEFAULT 0.00,
  patrimonio_neto decimal(18,2) DEFAULT 0.00,
  PRIMARY KEY (id_historico),
  UNIQUE KEY idx_usuario_periodo (id_usuario, periodo),
  CONSTRAINT fk_historico_usuarios FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Tabla: inversiones
CREATE TABLE inversiones (
  id_inversion int NOT NULL AUTO_INCREMENT,
  id_usuario int NOT NULL,
  plataforma varchar(100) NOT NULL,
  monto_invertido decimal(18,2) NOT NULL,
  tasa_retorno_mensual decimal(10,2) DEFAULT 0.00,
  fecha_inicio date NOT NULL,
  PRIMARY KEY (id_inversion),
  CONSTRAINT fk_inversiones_usuarios FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Tabla: presupuestos
CREATE TABLE presupuestos (
  id_presupuesto int NOT NULL AUTO_INCREMENT,
  id_usuario int NOT NULL,
  id_categoria int NOT NULL,
  monto_limite decimal(18,2) NOT NULL,
  periodo date NOT NULL,
  PRIMARY KEY (id_presupuesto),
  UNIQUE KEY idx_usuario_categoria_periodo (id_usuario, id_categoria, periodo),
  CONSTRAINT fk_presupuestos_usuarios FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario) ON DELETE CASCADE,
  CONSTRAINT fk_presupuestos_categorias FOREIGN KEY (id_categoria) REFERENCES categorias (id_categoria) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Tabla: reglas_categorizacion
CREATE TABLE reglas_categorizacion (
  id_regla int NOT NULL AUTO_INCREMENT,
  id_usuario int NOT NULL,
  patron varchar(100) NOT NULL,
  id_categoria int NOT NULL,
  nombre_fantasia varchar(100) DEFAULT NULL,
  PRIMARY KEY (id_regla),
  UNIQUE KEY idx_usuario_patron (id_usuario, patron),
  CONSTRAINT fk_reglas_usuarios FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario) ON DELETE CASCADE,
  CONSTRAINT fk_reglas_categorias FOREIGN KEY (id_categoria) REFERENCES categorias (id_categoria) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
