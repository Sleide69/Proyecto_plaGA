🌿 Proyecto PlaGA: Sistema de Monitoreo y Detección de Plagas
📖 Descripción del Proyecto
Proyecto PlaGA es un innovador sistema diseñado para el monitoreo y la detección temprana de plagas en plantas, aprovechando el poder de la inteligencia artificial. La solución integra una robusta aplicación web con un backend de procesamiento de imágenes dedicado, permitiendo a los usuarios capturar imágenes de plantas, analizarlas en tiempo real para identificar plagas específicas, y recibir notificaciones instantáneas sobre las detecciones. Los resultados, incluyendo las imágenes procesadas y los detalles de las plagas, se almacenan para un seguimiento histórico y una gestión eficiente.

🚀 Funcionalidades Principales
Captura de Imágenes Dinámica:

Permite a los usuarios capturar imágenes de plantas directamente desde la interfaz web, utilizando una webcam o una cámara compatible.

La imagen capturada se prepara en el lado del cliente y se envía de forma eficiente al servidor de procesamiento para su análisis.

Detección de Plagas con IA:

Un servidor Flask independiente recibe las imágenes capturadas.

Utiliza un modelo YOLOv5 personalizado (entrenado específicamente para identificar plagas) para analizar la imagen y detectar la presencia de insectos o enfermedades.

Proporciona resultados detallados, incluyendo el nombre de la plaga y el nivel de confianza de la detección.

Notificaciones en Tiempo Real:

Una vez que el modelo de IA detecta una plaga, el sistema Laravel genera y gestiona notificaciones automáticas.

Estas notificaciones se almacenan en la base de datos y se muestran dinámicamente en la interfaz web para alertar al usuario sobre posibles infestaciones.

Gestión y Almacenamiento de Datos:

Las imágenes originales y los resultados de detección (incluyendo el tipo de plaga y la confianza) se almacenan de forma persistente en la base de datos.

Permite consultar un historial de detecciones para un monitoreo a largo plazo y análisis de tendencias.

Interfaz Web Intuitiva:

Desarrollada con Laravel Blade, ofrece una experiencia de usuario sencilla para la captura de imágenes, la visualización de los resultados de detección y la gestión de notificaciones.

🛠️ Tecnologías Utilizadas
Backend (Laravel):
Laravel: Framework PHP robusto para la lógica de negocio, autenticación, gestión de la base de datos (Eloquent ORM), y API REST.

Laravel Sanctum: Para la autenticación API ligera y basada en tokens.

Composer: Gestor de dependencias de PHP.

PostgreSQL: Sistema de gestión de base de datos relacional para el almacenamiento de usuarios, notificaciones e historial de detecciones.

Backend (Flask - AI Processing):
Flask: Micro-framework Python para crear un servidor de API ligero dedicado al procesamiento de imágenes.

Flask-CORS: Para manejar las políticas de Cross-Origin Resource Sharing, permitiendo la comunicación entre el frontend de Laravel y el backend de Flask.

PyTorch: Librería de aprendizaje automático para ejecutar el modelo YOLOv5.

Ultralytics YOLOv5: Implementación específica del modelo de detección de objetos.

Pillow (PIL): Librería para procesamiento de imágenes en Python.

Frontend:
Blade: Motor de plantillas de Laravel para la construcción de la interfaz de usuario.

Vite: Herramienta de construcción frontend para una experiencia de desarrollo rápida y optimizada.

Webcam.js: Librería JavaScript sencilla para acceder a la cámara del navegador y capturar imágenes.

📦 Dependencias Específicas
Laravel (gestionadas por composer.json):
laravel/framework

laravel/sanctum

laravel/tinker

guzzlehttp/guzzle (para las solicitudes HTTP a Flask)

Python (gestionadas por requirements.txt en la carpeta Flask):
flask

flask-cors

torch

ultralytics

pillow

💻 Requerimientos del Sistema
Software:
PHP: Versión 8.1 o superior.

Composer: Última versión estable.

Node.js: Versión 16.x o superior (con npm).

Python: Versión 3.8 o superior.

pip: Gestor de paquetes de Python.

Git: Para clonar el repositorio.

Servidor Web: Apache (recomendado con XAMPP) o Nginx.

Base de Datos: PostgreSQL (o MySQL si se adapta la configuración de Laravel).

Hardware:
CPU: Procesador moderno.

RAM: 8 GB o más.

Cámara: Webcam USB o una cámara WiFi compatible con tu sistema operativo.

GPU (opcional): Una tarjeta gráfica compatible con CUDA (NVIDIA) puede acelerar significativamente el procesamiento de imágenes con YOLOv5.

📋 Guía de Instalación y Configuración
Sigue estos pasos para poner en marcha el proyecto en tu entorno local:

1. Clonar el Repositorio
Abre tu terminal (CMD o PowerShell en Windows) y ejecuta:

git clone https://github.com/Sleide69/Proyecto_plaGA.git
cd Proyecto_plaGA

2. Configuración del Backend de Laravel
Navega a la raíz del proyecto Proyecto_plaGA.

# Instalar dependencias de Composer
composer install

# Copiar el archivo de configuración .env
cp .env.example .env

# Generar la clave de la aplicación
php artisan key:generate

# Configurar la base de datos en el archivo .env
# Asegúrate de que los detalles de conexión a PostgreSQL (u otra DB) sean correctos:
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=tu_base_de_datos
# DB_USERNAME=tu_usuario_db
# DB_PASSWORD=tu_contraseña_db

# Ejecutar las migraciones para crear las tablas de la base de datos
# Esto creará las tablas 'users', 'notificaciones', etc.
php artisan migrate

# Crear el enlace simbólico para el almacenamiento público de imágenes
# Esto es crucial para que las imágenes guardadas en storage/app/public sean accesibles vía URL.
php artisan storage:link
# Si ya existe, verás un mensaje de "No se puede crear un archivo que ya existe." que es normal.

3. Configuración del Frontend
Asegúrate de tener Node.js y npm instalados.

# Instalar dependencias de Node.js (Vite, etc.)
npm install

# Compilar los assets de frontend para desarrollo
npm run dev
# Para producción, usar: npm run build

4. Configuración del Backend de Procesamiento de IA (Flask)
Navega a la carpeta donde se encuentra el script de Flask:

cd scripts/my_model

Crea un entorno virtual de Python (altamente recomendado) e instala las dependencias:

# Crear entorno virtual
python -m venv venv

# Activar entorno virtual
# En Windows:
.\venv\Scripts\activate
# En macOS/Linux:
source venv/bin/activate

# Instalar dependencias de Python
# Asegúrate de que tienes un archivo requirements.txt con todas las dependencias listadas
# Si no lo tienes, puedes instalar una por una:
pip install flask flask-cors torch ultralytics pillow

# Desactivar el entorno virtual cuando termines de instalar:
deactivate

Ubicación del Modelo YOLOv5 best.pt:
Asegúrate de que tu archivo best.pt (el modelo YOLOv5 entrenado) se encuentre en la ruta correcta, tal como se especifica en tu servidor_flask.py:
C:/xampp/htdocs/Proyecto_plaGA/scripts/my_model/train/weights/best.pt
Si tu modelo no está en esta ubicación, el servidor Flask no podrá cargarlo.

5. Ejecutar los Servidores
Para que el sistema funcione completamente, necesitarás dos terminales:

Terminal 1: Iniciar el Servidor Laravel

Navega a la raíz del proyecto Proyecto_plaGA:

php artisan serve

Esto iniciará el servidor de desarrollo de Laravel, accesible en http://127.0.0.1:8000.

Terminal 2: Iniciar el Servidor Flask (AI Processing)

Navega a la carpeta de tu script de Flask (scripts/my_model):

cd scripts/my_model

# Activa tu entorno virtual si lo desactivaste
# En Windows:
.\venv\Scripts\activate
# En macOS/Linux:
source venv/bin/activate

# Iniciar el servidor Flask
python servidor_flask.py

Esto iniciará el servidor Flask, accesible en http://127.0.0.1:5000.

6. Acceder a la Aplicación
Una vez que ambos servidores estén ejecutándose, abre tu navegador web y ve a:

http://127.0.0.1:8000

Si has configurado un Virtual Host (ej. proyecto-plaga.test), accede a través de ese dominio. Asegúrate de iniciar sesión en tu aplicación Laravel para que el sistema de notificaciones funcione correctamente, ya que utiliza autenticación Sanctum.

🧪 Ejecución de Pruebas
Para ejecutar las pruebas de Laravel (Unit y Feature):

php artisan test

🤝 Contribución
Si deseas contribuir a este proyecto, por favor, sigue las prácticas estándar de Pull Requests (PRs) y crea un issue para cualquier error o mejora propuesta.

📄 Licencia
Este proyecto está bajo la licencia MIT License.