-- ============================================================
-- ClariFi — Inicialización para Railway
-- Copiar y pegar en el Query Editor de MySQL en Railway
-- ============================================================

CREATE TABLE IF NOT EXISTS usuarios (
  id_usuario int NOT NULL AUTO_INCREMENT,
  nombre varchar(50) NOT NULL,
  email varchar(100) NOT NULL,
  password_hash varchar(255) NOT NULL,
  fecha_registro datetime NOT NULL,
  PRIMARY KEY (id_usuario),
  UNIQUE KEY email_UNIQUE (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categorias (
  id_categoria int NOT NULL AUTO_INCREMENT,
  nombre_categoria varchar(50) NOT NULL,
  tipo_flujo enum('ingreso','gasto') NOT NULL,
  id_usuario int NOT NULL,
  PRIMARY KEY (id_categoria),
  KEY fk_categorias_usuarios_idx (id_usuario),
  CONSTRAINT fk_categorias_usuarios FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS deudas (
  id_deuda int NOT NULL AUTO_INCREMENT,
  nombre_deuda varchar(100) NOT NULL,
  saldo_total decimal(12,2) NOT NULL,
  cuota_mensual decimal(10,2) NOT NULL,
  tipo_deuda enum('prestamo','tarjeta_credito') NOT NULL,
  cft decimal(10,2) NOT NULL,
  tna decimal(10,2) DEFAULT NULL,
  tea int DEFAULT NULL,
  id_usuario int NOT NULL,
  limite_credito decimal(12,2) DEFAULT NULL,
  dia_cierre int DEFAULT NULL,
  dia_vencimiento int DEFAULT NULL,
  cuotas_totales int DEFAULT NULL,
  cuotas_pagadas int DEFAULT NULL,
  fecha_inicio date DEFAULT NULL,
  PRIMARY KEY (id_deuda),
  KEY fk_deudas_usuarios1_idx (id_usuario),
  CONSTRAINT fk_deudas_usuarios1 FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS transacciones (
  id_transaccion int NOT NULL AUTO_INCREMENT,
  monto decimal(10,2) NOT NULL,
  descripcion varchar(255) DEFAULT NULL,
  fecha_transaccion date NOT NULL,
  id_usuario int NOT NULL,
  id_categoria int NOT NULL,
  id_deuda int DEFAULT NULL,
  PRIMARY KEY (id_transaccion),
  KEY fk_transacciones_usuarios1_idx (id_usuario),
  KEY fk_transacciones_categorias1_idx (id_categoria),
  KEY fk_transacciones_deudas1_idx (id_deuda),
  CONSTRAINT fk_transacciones_categorias1 FOREIGN KEY (id_categoria) REFERENCES categorias (id_categoria),
  CONSTRAINT fk_transacciones_deudas1 FOREIGN KEY (id_deuda) REFERENCES deudas (id_deuda),
  CONSTRAINT fk_transacciones_usuarios1 FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS gastos_recurrentes (
  id_recurrente int NOT NULL AUTO_INCREMENT,
  monto decimal(10,2) NOT NULL,
  descripcion varchar(255) DEFAULT NULL,
  dia_cobro int NOT NULL,
  ultimo_procesamiento date DEFAULT NULL,
  id_usuario int NOT NULL,
  id_categoria int NOT NULL,
  PRIMARY KEY (id_recurrente),
  KEY fk_gastos_recurrentes_usuarios1_idx (id_usuario),
  KEY fk_gastos_recurrentes_categorias1_idx (id_categoria),
  CONSTRAINT fk_gastos_recurrentes_categorias1 FOREIGN KEY (id_categoria) REFERENCES categorias (id_categoria),
  CONSTRAINT fk_gastos_recurrentes_usuarios1 FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS metas_financieras (
  id_meta int NOT NULL AUTO_INCREMENT,
  nombre_meta varchar(100) NOT NULL,
  monto_objetivo decimal(12,2) NOT NULL,
  saldo_actual decimal(12,2) NOT NULL,
  fecha_limite date NOT NULL,
  id_usuario int NOT NULL,
  PRIMARY KEY (id_meta),
  KEY fk_metas_financieras_usuarios1_idx (id_usuario),
  CONSTRAINT fk_metas_financieras_usuarios1 FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
