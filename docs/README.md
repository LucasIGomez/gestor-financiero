# Gestor Financiero APP - Producto Mínimo Viable (MVP)

Una aplicación web "All-in-One" diseñada para la gestión integral de finanzas personales. Más que un simple registro de gastos, este sistema actúa como un asesor financiero automatizado, optimizando el pago de deudas, proyectando inversiones y calculando retenciones fiscales.

## 🚀 Características Principales (Módulos)

1. **Gestión de Ingresos y Gastos:** Registro transaccional (CRUD) con categorización inteligente. Calcula dinámicamente el Flujo de Caja y el Patrimonio Neto Real del usuario cruzando activos y pasivos.
2. **Asesor de Prioridades de Deuda:** Implementación algorítmica del **Método Avalancha**. Ordena automáticamente las deudas por su Tasa Nominal Anual (TNA/TEA) y simula el impacto en tiempo y dinero de realizar pagos extra.
3. **Simulador de Interés Compuesto:** Proyección financiera a largo plazo basada en la fórmula matemática $A = P(1 + \frac{r}{n})^{nt}$. Demuestra el crecimiento exponencial del capital mediante aportes regulares.
4. **Automatización Fiscal:** Calculadora tributaria paramétrica para trabajadores independientes, discriminando ingresos brutos, gastos deducibles, IVA, Ingresos Brutos y Ganancias para obtener el ingreso neto real.

## 🛠️ Arquitectura y Pila Tecnológica

El sistema fue construido aplicando ingeniería de software orientada a la escalabilidad:

* **Arquitectura:** Patrón MVC (Modelo-Vista-Controlador) estricto. Separación absoluta entre la lógica de negocio, el acceso a datos y la interfaz de usuario.
* **Backend:** PHP puro.
* **Base de Datos:** MySQL Relacional (Consultas preparadas con PDO para prevención de Inyección SQL).
* **Modelado de Datos:** MySQL Workbench (Ingeniería Hacia Adelante / Forward Engineering).
* **Entorno de Desarrollo:** Laragon / Visual Studio Code.
* **Control de Versiones:** Git & GitHub.

## ⚙️ Instalación y Despliegue Local

Para ejecutar este proyecto en un entorno local, sigue estos pasos:

1. **Clonar el repositorio:**
   ```bash
   git clone [https://github.com/LucasIGomez/gestor-financiero.git](https://github.com/LucasIGomez/gestor-financiero.git)

2. **Entorno Local: ** Mueve la carpeta del proyecto al directorio raiz de tu servidor web local (ej. C:\laragon\www\ si utilizas Laragon)

3. **Base de Datos: **
* Abre MySQL Workbench o phpMyAdmin.
* Crea un esquema llamado gestor-financiero.
* Ejecuta el script de datos semilla ubicado en database/datos_semilla.sql para generar las tablas (usuarios, categorias, transacciones, deudas) y poblar el sistema con informacion de prueba.

4. **Ejecución:** Abre tu navegador web e ingresa a https://localhost/gestor-financiero/. El enrutador principal (index.php) gestionara automaticamente las vistas.

## 🔮 Escalabilidad Futura (Roadmap)
El sistema está preparado a nivel base de datos y controladores para recibir futuras actualizaciones, incluyendo:

* Integración de APIs bancarias para sincronización automática de transacciones.

* Implementación de Machine Learning para perfilado de gastos y sugerencias de inversión automatizadas.

* Sistema de autenticación de usuarios con encriptación AES-256.