# ClariFi — Gestor Financiero Inteligente

Una aplicación web "All-in-One" y de nivel premium diseñada para la gestión integral de finanzas personales. Más que un simple registro de gastos, ClariFi actúa como un asesor financiero de alta gama, optimizando deudas, automatizando categorías, proyectando inversiones, gestionando presupuestos tipo semáforo y visualizando la trayectoria patrimonial en tiempo real.

---

## 🚀 Módulos y Características Principales

1. **Dashboard & Safe-to-Spend (Gasto Seguro):**
   * Control transaccional completo (Ingresos y Gastos) con cálculo de flujo de caja y patrimonio neto en tiempo real.
   * Algoritmo de **Gasto Diario Seguro** que calcula dinámicamente cuánta liquidez libre tiene el usuario para gastar en el día de hoy, dividiendo la liquidez restante del mes por los días faltantes.

2. **Asesor Financiero Pro (Método Avalancha):**
   * Implementación matemática del **Método Avalancha** para optimizar el desendeudamiento.
   * Ordena los pasivos por su **Costo Financiero Total (CFT)** de mayor a menor y calcula los meses exactos necesarios para liquidar cada deuda aplicando ecuaciones logarítmicas de amortización.
   * Simulador de ahorro que muestra el impacto de realizar un pago extra mensual (ej. $50.000), revelando cuántos meses e intereses se ahorra el usuario.

3. **Open Banking & Conciliación Bancaria (Simulada):**
   * Módulo de conexión para billeteras virtuales y bancos (Mercado Pago, Naranja X, Ualá, Lemon) con encriptación AES-256-ECB de tokens de seguridad.
   * Comparador en tiempo real de las tasas de rendimiento anuales (TNA) de saldos remunerados.
   * Motor de **Reglas de Clasificación Inteligente** que categoriza de forma automática las transacciones entrantes cruzando patrones de texto heurísticos (ej. "Coto" -> Supermercado).

4. **Presupuestos Semáforo:**
   * Establecimiento de límites de gasto por categorías.
   * Interfaz interactiva tipo semáforo que cambia de color dinámicamente según el consumo del mes (Verde < 70%, Amarillo 70%-99%, Rojo >= 100%).

5. **Simulador de Inversión y Calculadora Fiscal:**
   * Proyección financiera interactiva de interés compuesto a largo plazo con aportes recurrentes mensuales y gráficos exponenciales.
   * Calculadora fiscal para trabajadores independientes (Monotributo/Autónomos) que calcula IVA, Ingresos Brutos y retenciones de Ganancias sobre los ingresos netos.

6. **Motor de Gastos Recurrentes (Pseudo-Cron):**
   * Automatización inteligente que evalúa plantillas de cobro mensuales cada vez que el usuario ingresa al sistema, procesando transacciones pendientes sin necesidad de demonios de fondo.

---

## 🛠️ Arquitectura y Stack Tecnológico

El sistema aplica los más altos estándares de desarrollo y mantenibilidad de software:
* **Arquitectura:** Patrón **MVC (Modelo-Vista-Controlador)** puro y estricto. Separación completa de responsabilidades en PHP.
* **Seguridad del Backend:** 
  * Prevención total contra *SQL Injection* mediante sentencias preparadas y tipado estricto con PDO.
  * Resguardo de contraseñas mediante hashing BCRYPT robusto.
  * Mitigación de secuestro de sesiones mediante regeneración constante de identificadores de sesión.
  * Encriptación simétrica AES-256-ECB para credenciales de APIs bancarias.
* **Base de Datos:** MySQL Relacional optimizado con tipos de datos de alta precisión decimal (`DECIMAL(18,2)`) en todas sus columnas financieras para prevenir desbordamientos de capital.
* **Frontend:** Interfaz web interactiva e inmersiva basada en plantillas PHP nativas y CSS moderno con transiciones dinámicas y micro-animaciones.

---

## ⚙️ Instalación y Configuración Local

### Requisitos Previos
* Servidor local Apache/PHP 8.2 o superior (Recomendado: **Laragon** o XAMPP).
* Servidor MySQL 8.0 o superior.

### Pasos para el despliegue
1. **Clonar el proyecto** dentro de la carpeta raíz de tu servidor (ej. `C:\laragon\www\gestor-financiero`):
   ```bash
   git clone https://github.com/LucasIGomez/gestor-financiero.git
   ```
2. **Configurar la Base de Datos:**
   * Abre tu gestor de base de datos preferido (MySQL Workbench, phpMyAdmin, etc.).
   * Importa y ejecuta el script de estructura general:
     [database/schema.sql](file:///c:/laragon/www/gestor-financiero/database/schema.sql)
   * Importa y ejecuta el archivo de datos semilla básicos para poblar las categorías estándar:
     [database/datos_semilla.sql](file:///c:/laragon/www/gestor-financiero/database/datos_semilla.sql)
   * (Opcional para pruebas) Importa y ejecuta el script de datos de prueba completos para el usuario de test:
     [database/datos_test_user.sql](file:///c:/laragon/www/gestor-financiero/database/datos_test_user.sql)
     *(Este script creará al usuario `test@clari.fi` con contraseña simulada y poblará sus deudas, transacciones, inversiones, reglas y conexiones de prueba).*

3. **Ejecutar el Sistema:**
   * Abre tu navegador e ingresa a `http://localhost/gestor-financiero/` (o la URL local asignada por Laragon, ej. `http://gestor-financiero.test`).
   * Para ingresar con el usuario de pruebas, utiliza las credenciales:
     * **Usuario:** `test@clari.fi`
     * **Contraseña:** `test` *(El hash cargado en la base de datos validará esta clave)*.
