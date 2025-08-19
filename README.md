# Sistema de Inventario JoseSoft

Un sistema de inventario web robusto y seguro, diseñado para gestionar eficientemente productos, espacios y movimientos de inventario en tiempo real. La aplicación está construida con PHP, MySQL y JavaScript, ofreciendo una interfaz de usuario limpia y funcional gracias a Tailwind CSS.

## Tecnologías Utilizadas
<p align="left">
  <a href="https://www.php.net/" target="_blank" rel="noreferrer">
    <img src="https://www.svgrepo.com/show/452088/php.svg" alt="PHP" width="40" height="40"/>
  </a>
  <a href="https://www.mysql.com/" target="_blank" rel="noreferrer">
    <img src="https://www.svgrepo.com/show/303251/mysql-logo.svg" alt="MySQL" width="40" height="40"/>
  </a>
  <a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript" target="_blank" rel="noreferrer">
    <img src="https://www.svgrepo.com/show/303206/javascript-logo.svg" alt="JavaScript" width="40" height="40"/>
  </a>
  <a href="https://www.w3.org/html/" target="_blank" rel="noreferrer">
    <img src="https://www.svgrepo.com/show/17484/html-5-logo.svg" alt="HTML5" width="40" height="40"/>
  </a>
  <a href="https://tailwindcss.com/" target="_blank" rel="noreferrer">
    <img src="https://www.svgrepo.com/show/374118/tailwind.svg" alt="Tailwind CSS" width="40" height="40"/>
  </a>
</p>

## Funcionalidades

El sistema cuenta con un control de acceso basado en roles, ofreciendo distintas capacidades según el tipo de usuario.

### Roles de Usuario
* **Administrador**: Acceso total al sistema. Puede gestionar productos, espacios, movimientos, usuarios y ver todos los reportes.
* **Supervisor**: Acceso a la operativa diaria. Puede gestionar productos, espacios, movimientos y generar reportes. No tiene acceso a la gestión de usuarios.
* **CEO**: Rol de solo lectura. Puede acceder únicamente a la sección de reportes para supervisar el consumo y la operativa.

### Módulos Principales

#### 1. Dashboard
* **Vista General**: Tarjetas de resumen con métricas clave: total de productos, total de espacios, alertas de stock bajo y movimientos del día.
* **Productos con Menor Stock**: Una tabla que muestra los 10 productos con el stock más bajo, resaltando en rojo aquellos que están por debajo del mínimo establecido.
* **Actividad Reciente**: Visualización en tiempo real de los últimos 5 movimientos (entradas o salidas) registrados durante el día.
* **Alertas Globales**: Un ícono de campana en la barra de navegación que notifica visualmente sobre productos con stock bajo, accesible desde cualquier página.

#### 2. Gestión de Movimientos
* **Registro de Entradas**: Permite registrar el ingreso de nuevos productos al inventario general.
* **Registro de Salidas**: Facilita el registro de la salida de productos hacia un espacio específico (ej. aulas, oficinas), indicando la cantidad y la jornada.
* **Historial y Filtros**: Muestra un historial completo de todos los movimientos con paginación. Se puede filtrar por rango de fechas para una búsqueda más precisa.
* **Corrección de Movimientos**: Permite anular un movimiento erróneo. El sistema crea automáticamente un movimiento inverso para balancear el stock, manteniendo la integridad del historial.

#### 3. Gestión de Productos
* **CRUD de Productos**: Funcionalidad completa para Crear, Leer, Actualizar y Eliminar (borrado lógico) productos.
* **Control de Stock**: Cada producto cuenta con campos para `stock_actual` y `stock_minimo`, fundamentales para las alertas y reportes.

#### 4. Gestión de Espacios
* **CRUD de Espacios**: Permite administrar las ubicaciones físicas (aulas, bodegas, pisos) a donde se destinan los productos.

#### 5. Gestión de Usuarios (Solo Administrador)
* **CRUD de Usuarios**: El administrador puede crear, leer, actualizar y eliminar (borrado lógico) las cuentas de usuario.
* **Asignación de Roles**: Permite asignar los roles de Administrador, Supervisor o CEO a cada usuario.

#### 6. Reportes
* **Reportes Dinámicos**: Genera reportes de consumo de productos por día, semana o mes.
* **Múltiples Vistas**:
    * **Consumo General**: Muestra el total consumido por cada producto en el período seleccionado.
    * **Consumo Detallado**: Desglosa el consumo por espacio y jornada.
* **Exportación**: Permite exportar los reportes generados a formatos **PDF** y **Excel (CSV)** para su análisis y archivo. Los PDFs generados incluyen un encabezado y pie de página personalizados con el logo y nombre de la empresa.

### Seguridad
* **Sistema de Login Seguro**: Protege contra ataques de fuerza bruta bloqueando temporalmente el acceso tras múltiples intentos fallidos.
* **Gestión de Sesiones Robusta**:
    * Verifica que un usuario esté logueado para acceder a cualquier página interna.
    * Cierra la sesión automáticamente después de 30 minutos de inactividad.
    * Previene la visualización de páginas cacheadas después de cerrar sesión (soluciona el problema del "botón atrás" del navegador).
* **Prevención de Inyección SQL**: Uso de consultas preparadas (PDO) en toda la aplicación.
* **Protección XSS**: Saneamiento de todas las entradas de datos que se muestran en la interfaz para prevenir ataques de Cross-Site Scripting.
* **Contraseñas Seguras**: Almacenamiento de contraseñas utilizando `password_hash()` y `password_verify()`.

## Instalación

1.  **Clonar el repositorio o copiar los archivos** en tu servidor web (por ejemplo, en la carpeta `htdocs` de XAMPP).
2.  **Base de Datos**: Importa el archivo `sistema_inventario_db.sql` en tu gestor de base de datos MySQL o MariaDB. Esto creará la estructura de tablas necesaria.
3.  **Configurar la Conexión**: Abre el archivo `config/Conexion.php` y modifica las siguientes variables con tus credenciales de base de datos:
    ```php
    private $host = "localhost";
    private $db_name = "sistema_inventario_db";
    private $username = "root";
    private $password = "";
    ```
4.  **Acceder al Sistema**: Abre tu navegador y dirígete a `http://localhost/nombre_de_tu_carpeta/login.php`.

## Licencia
Este proyecto es de uso privado y desarrollado por **CelestiumSoft** para **CGE**. Todos los derechos reservados.