StockMaster Pro es una aplicación web desarrollada en PHP que permite a los usuarios consultar cotizaciones bursátiles en tiempo real, buscar empresas y gestionar una cartera de inversión virtual. El proyecto utiliza una arquitectura orientada a objetos, gestión de dependencias mediante Composer y consumo de APIs externas.

Propósito del Proyecto
El objetivo principal es ofrecer una interfaz moderna y funcional para la monitorización de activos financieros, permitiendo:
Autenticación de usuarios mediante base de datos.
Búsqueda dinámica de símbolos bursátiles (autocompletado).
Consulta de precios, variaciones y detalles de acciones.
Gestión de un "Carrito de Inversión" (Cartera) mediante sesiones de PHP.

Tecnologías y APIs Utilizadas

1. Alpha Vantage API
   La aplicación consume datos de Alpha Vantage, un proveedor de datos financieros de vanguardia.

Endpoints utilizados:

GLOBAL_QUOTE: Para obtener el precio actual y cambio porcentual de un símbolo específico.
SYMBOL_SEARCH: Para el motor de sugerencias y búsqueda de empresas por nombre.

Limitaciones: La versión gratuita tiene un límite de consultas (25 al día/5 por minuto), gestionado en el código mediante avisos al usuario.

2. Stack Tecnológico
   Backend: PHP 7.4+ (Orientado a Objetos).
   Gestión de Dependencias: Composer (Autoloading PSR-4).
   Base de Datos: MySQL (para la gestión de usuarios).
   Frontend: Bootstrap 4, FontAwesome 5 y Google Fonts (JetBrains Mono/Inter).
   Interactividad: JavaScript (Fetch API para autocompletado en tiempo real).

Configuración y Requisitos
Para poner en marcha este proyecto en un entorno local (como XAMPP o Laragon), sigue estos pasos:

1. Requisitos Previos
   Servidor web (Apache/Nginx).
   PHP 7.4 o superior instalado.
   Composer instalado.
   Base de datos MySQL/MariaDB.

2. Instalación de Dependencias
   Ejecuta el siguiente comando en la raíz del proyecto para generar el autoloader:

Bash
composer install
Nota: Aunque el composer.json actual no requiere librerías externas, es necesario para cargar la clase StockService bajo el namespace jrosalessueiro\Tarea8.

3. Configuración de la Base de Datos
   Crea una base de datos llamada empresa_dwcs.
   Crea una tabla usuarios con los campos usuario y pass.
   Asegúrate de que los datos en conexion.php coincidan con tu configuración:

Usuario: gestor
Password: secreto

4. Configuración de la API Key
   El archivo StockService.php incluye una API Key por defecto. Si deseas usar la tuya propia:
   Consíguela gratis en Alpha Vantage.
   Actualiza la variable $apiKey en src/StockService.php y en sugerencias.php.

🖥️ Cómo probar el ejemplo
Inicia tus servicios de Apache y MySQL.
Accede a login.php desde tu navegador.
Login: Introduce las credenciales configuradas en tu base de datos (por defecto: gestor / secreto).
Buscador: En el terminal, empieza a escribir el nombre de una empresa (ej: "Microsoft" o "Apple"). Aparecerá un desplegable con sugerencias.
Detalles: Haz clic en "Consultar" para ver el precio actual y el cambio porcentual.
Cartera: Haz clic en "Añadir al Portafolio" para guardar la acción en tu cesta. Puedes ver el valor total acumulado en la sección "Mi Cartera".
Cierre: Usa el botón de encendido/apagado para destruir la sesión de forma segura.
