# 🌿 Proyecto PlaGA: 
Sistema de Monitoreo y Detección de Plagas
Proyecto PlaGA es un sistema integral diseñado para el monitoreo y la detección temprana de plagas en plantas utilizando inteligencia artificial. Combina una robusta aplicación web (Laravel) con un backend de procesamiento de imágenes (Flask) para ofrecer una solución en tiempo real. Los usuarios pueden capturar imágenes de plantas, analizarlas con un modelo YOLOv5 entrenado para identificar plagas, y recibir notificaciones instantáneas sobre las detecciones. Los resultados, incluyendo las imágenes procesadas y los detalles de las plagas, se almacenan para un seguimiento histórico y una gestión eficiente.

# 📦 Características Principales
Captura de Imágenes en Tiempo Real: Permite la captura de imágenes desde una cámara web o WiFi directamente desde la interfaz de usuario.

Detección Inteligente de Plagas: Utiliza un modelo YOLOv5 personalizado para identificar y clasificar plagas en las imágenes capturadas.

Notificaciones Automáticas: Genera y gestiona notificaciones en tiempo real sobre las plagas detectadas, almacenándolas y mostrándolas en la interfaz.

Almacenamiento Persistente: Guarda las imágenes originales y los resultados de detección en una base de datos para consulta y análisis histórico.

API RESTful: Proporciona endpoints para la comunicación entre el frontend, el backend de detección y la gestión de notificaciones.

Autenticación Segura: Implementa autenticación JWT para proteger los endpoints de la API.

Pruebas Automatizadas: Incluye pruebas unitarias y de integración para asegurar la fiabilidad del sistema.

# 🛠️ Tecnologías y Dependencias
Backend (Laravel - PHP)
Laravel: Framework PHP para la lógica de negocio, autenticación, gestión de la base de datos (Eloquent ORM) y API REST.

Laravel Sanctum: Para la autenticación API ligera y basada en tokens.

GuzzleHttp: Cliente HTTP para las solicitudes entre Laravel y Flask.

PostgreSQL: Base de datos relacional para almacenar usuarios, notificaciones y registros de detecciones.

Backend (Flask - Python para IA)
Flask: Micro-framework Python que aloja el modelo de IA.

Flask-CORS: Para habilitar solicitudes de origen cruzado.

PyTorch: Librería de aprendizaje automático para el motor de inferencia.

Ultralytics YOLOv5: Implementación del modelo de detección de objetos.

Pillow (PIL): Librería para procesamiento de imágenes.

Frontend
Blade: Motor de plantillas de Laravel.

Vite: Herramienta de construcción frontend para CSS y JavaScript.

Webcam.js: Librería JavaScript para la interacción con la cámara.

Instalación de Paquetes Principales:
# Para Laravel (desde la raíz del proyecto)
composer install
npm install

# Para Flask (desde scripts/my_model)
# Después de activar el entorno virtual
pip install flask flask-cors torch ultralytics pillow

# 💻 Requerimientos del Sistema
Software:
PHP: Versión 8.1 o superior.

Composer: Última versión estable.

Node.js: Versión 16.x o superior (con npm).

Python: Versión 3.8 o superior.

pip: Gestor de paquetes de Python.

Git: Para clonar el repositorio.

Servidor Web: Apache (recomendado con XAMPP) o Nginx.

Base de Datos: PostgreSQL (configurado y accesible).

Hardware:
CPU: Procesador moderno.

RAM: 8 GB o más.

Cámara: Webcam USB o compatible con tu sistema.

GPU (opcional): Tarjeta gráfica compatible con CUDA para acelerar YOLOv5.

# 📋 Guía de Instalación y Configuración
1. Clonar el Repositorio
git clone https://github.com/Sleide69/Proyecto_plaGA.git
cd Proyecto_plaGA

2. Configuración del Backend de Laravel
Navega a la raíz del proyecto Proyecto_plaGA:

# Instalar dependencias de Composer
composer install

# Copiar el archivo de configuración de entorno
cp .env.example .env

# Generar la clave de la aplicación Laravel
php artisan key:generate

# Configurar la base de datos en el archivo .env
# Abre el archivo .env y ajusta los detalles de conexión a tu base de datos PostgreSQL:
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=nombre_de_tu_db
# DB_USERNAME=tu_usuario_db
# DB_PASSWORD=tu_contraseña_db

# Ejecutar las migraciones para crear las tablas de la base de datos
# Esto creará las tablas 'users', 'notificaciones', etc.
php artisan migrate

# Crear el enlace simbólico para el almacenamiento público de imágenes
# Crucial para que las imágenes guardadas en storage/app/public sean accesibles vía URL.
php artisan storage:link
# Si ya existe, es normal que muestre "No se puede crear un archivo que ya existe."

3. Configuración del Frontend
Desde la raíz del proyecto:

# Instalar dependencias de Node.js (Vite)
npm install

# Compilar los assets de frontend para desarrollo
npm run dev
# Para producción, usar: npm run build

4. Configuración del Backend de IA (Flask)
Navega a la carpeta de los scripts de Python:

cd scripts/my_model

Crea y activa un entorno virtual de Python, luego instala las dependencias:

# Crear entorno virtual
python -m venv venv

# Activar entorno virtual
# En Windows:
.\venv\Scripts\activate
# En macOS/Linux:
source venv/bin/activate

# Instalar dependencias de Python
pip install flask flask-cors torch ultralytics pillow

# Desactivar el entorno virtual (opcional, puedes dejarlo activo si vas a iniciar Flask de inmediato)
deactivate

Ubicación del Modelo YOLOv5 (best.pt):
Asegúrate de que tu archivo best.pt (el modelo YOLOv5 entrenado) se encuentre en la ruta correcta, tal como se especifica en tu servidor_flask.py (por defecto: C:/xampp/htdocs/Proyecto_plaGA/scripts/my_model/train/weights/best.pt).

5. Ejecutar los Servidores
Necesitarás dos terminales abiertas simultáneamente:

Terminal 1: Servidor Laravel

Navega a la raíz del proyecto Proyecto_plaGA:

php artisan serve

El servidor Laravel estará accesible en http://127.0.0.1:8000.

Terminal 2: Servidor Flask (AI Processing)

Navega a la carpeta de tu script de Flask (scripts/my_model):

cd scripts/my_model

# Activa tu entorno virtual si lo desactivaste (muy importante)
# En Windows:
.\venv\Scripts\activate
# En macOS/Linux:
source venv/bin/activate

# Iniciar el servidor Flask
python servidor_flask.py

El servidor Flask estará accesible en http://127.0.0.1:5000.

6. Acceder a la Aplicación
Con ambos servidores en ejecución, abre tu navegador web y ve a:

http://127.0.0.1:8000

Si has configurado un Virtual Host (ej. proyecto-plaga.test), accede a través de ese dominio. Asegúrate de iniciar sesión en tu aplicación Laravel para que el sistema de notificaciones funcione correctamente, ya que utiliza autenticación Sanctum.

# 🧪 Pruebas
Para ejecutar las pruebas de Laravel (Unitarias y de Característica):

php artisan test

# 📁 Estructura del Proyecto (Vista General)
Proyecto_plaGA/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   └── NotificacionController.php   
│   │   │   ├── CapturaController.php           
│   │   │   └── DeteccionController.php        
│   │   └── Middleware/
│   ├── Models/
│   │   ├── Notificacion.php                   
│   │   └── User.php                       
│   └── Providers/
│
├── config/                                     
├── database/
│   ├── migrations/                             
│   │   └── YYYY_MM_DD_HHMMSS_create_notificaciones_table.php 
│   └── seeders/
│
├── public/                                     
│   └── storage -> ../storage/app/public        
│
├── resources/
│   ├── css/
│   ├── js/
│   ├── views/
│   │   ├── plagas/
│   │   │   └── captura-imagen.blade.php        
│   │   └── auth/                               
│   │
├── routes/
│   ├── api.php                                 
│   └── web.php                                 
│
├── scripts/
│   └── my_model/
│       ├── servidor_flask.py                   
│       ├── venv/                              
│       └── train/weights/
│           └── best.pt                         
│
├── storage/
│   ├── app/
│   │   └── public/
│   │       └── capturas/                       
│   └── logs/                                   
│
├── tests/                                      
│   └── Feature/
│       └── RegistroUsuarioTest.php             
│
├── .env.example
├── .env
├── composer.json
├── package.json
└── README.md

# 📡 Endpoints Clave de la API
📍 Detección de Plagas (Flask)
POST http://127.0.0.1:5000/detect

Envía una imagen (multipart/form-data) para detección de plagas.

Cuerpo de solicitud:
Content-Type: multipart/form-data
Body: image (file)

Ejemplo de respuesta:
[
  {
    "name": "mosquito",
    "confidence": 0.95
  },
  {
    "name": "araña",
    "confidence": 0.88
  }
]

# 📍 Notificaciones (Laravel API)
Las rutas de notificaciones requieren autenticación con Laravel Sanctum (token JWT en el encabezado Authorization: Bearer).

GET /api/notificaciones
Lista las últimas 10 notificaciones del usuario autenticado.

Encabezado requerido:
Authorization: Bearer {tu_token_sanctum}
Accept: application/json

Ejemplo de respuesta:
{
  "notificaciones": [
    {
      "id": 1,
      "user_id": 1,
      "mensaje": "Plagas detectadas: mosquito (95.00%), araña (88.00%)",
      "leida": false,
      "created_at": "2023-10-27T10:00:00.000000Z",
      "updated_at": "2023-10-27T10:00:00.000000Z"
    },
    {
      "id": 2,
      "user_id": 1,
      "mensaje": "No se detectaron plagas en la última imagen.",
      "leida": false,
      "created_at": "2023-10-27T09:30:00.000000Z",
      "updated_at": "2023-10-27T09:30:00.000000Z"
    }
  ]
}

POST /api/notificaciones
Crea una nueva notificación.

Encabezados requeridos:
Authorization: Bearer {tu_token_sanctum}
Content-Type: application/json
Accept: application/json

Cuerpo de solicitud:
{
  "mensaje": "Plagas detectadas: pulgón (0.75), cochinilla (0.60)"
}

Ejemplo de respuesta:
{
  "success": true,
  "notificacion": {
    "user_id": 1,
    "mensaje": "Plagas detectadas: pulgón (0.75), cochinilla (0.60)",
    "leida": false,
    "updated_at": "2023-10-27T11:00:00.000000Z",
    "created_at": "2023-10-27T11:00:00.000000Z",
    "id": 3
  }
}

📝 Notas Importantes
Asegúrate de que tu usuario esté autenticado en Laravel para que las llamadas a las APIs de notificaciones funcionen correctamente.

Verifica la ruta de tu modelo best.pt en servidor_flask.py para que coincida con la ubicación real.

Si experimentas errores 404 al cargar imágenes, verifica que php artisan storage:link se ejecutó correctamente y que tu servidor web (Apache) está configurado para servir la carpeta public de Laravel.

Para entornos de producción, considera configurar un servidor WSGI (como Gunicorn para Flask) y un servidor HTTP más robusto (como Nginx o Apache) para tu aplicación Laravel, en lugar de los servidores de desarrollo.

Cambia las claves JWT y las credenciales por defecto por valores seguros en producción.