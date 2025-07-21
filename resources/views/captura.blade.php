<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- ¡ATENCIÓN! No se debe pasar el token de Sanctum directamente en una meta tag para el frontend.
         El token de Sanctum lo recibe el cliente al iniciar sesión y lo gestiona, por ejemplo, en localStorage.
         Si el usuario ya está autenticado, puedes obtenerlo del almacenamiento local.
         Por ahora, lo mantendremos para que funcione con tu estructura actual,
         pero considera una gestión más segura en producción. --}}
    <meta name="token-sanctum" content="{{ session('token_sanctum') }}">
    <title>Captura de Imagen - Monitoreo</title>
    @vite(['resources/css/captura.css'])
</head>
<body>
    <div class="contenedor">
        <div class="formulario">
            <h1>📷 Captura de Imagen</h1>
            <p>Usa tu cámara para registrar el estado de las plantas</p>

            <div class="camara" id="mi_camera"></div>

            <div id="resultado" style="margin-top: 20px; font-family: monospace;"></div>

            <form id="form-guardar" action="{{ route('captura.imagen') }}" method="POST" onsubmit="return false;">
                @csrf
                <input type="hidden" name="imagen" id="imagen">
                <button type="button" onclick="capturar()">📸 Capturar, Analizar y Guardar</button>
            </form>

        </div>
    </div>
    <div id="notificaciones" style="margin-top: 20px; border: 1px solid #ccc; padding: 10px;">
        <h3>🔔 Notificaciones:</h3>
        {{-- CORRECCIÓN 1: El ID de la lista de notificaciones estaba mal.
            Era 'lista-notificaciones' en el HTML y se buscaba 'notificaciones-lista' en JS. --}}
        <ul id="lista-notificaciones"></ul>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
    <script>
    Webcam.set({
        width: 400,
        height: 300,
        image_format: 'jpeg',
        jpeg_quality: 90
    });
    Webcam.attach('#mi_camera');

    // Función para obtener el token de Sanctum
    function getSanctumToken() {
        // Opción 1: Obtener de la meta tag (como lo tienes ahora)
        const tokenMeta = document.querySelector('meta[name="token-sanctum"]');
        if (tokenMeta && tokenMeta.content) {
            return tokenMeta.content;
        }

        // Opción 2: Obtener de localStorage (si lo guardas ahí al iniciar sesión)
        // Ejemplo: return localStorage.getItem('sanctum_token');
        console.warn("Token de Sanctum no encontrado en la meta tag. Asegúrate de que el usuario está logueado y el token está disponible.");
        return null; // Devuelve null si no hay token
    }

    function cargarNotificaciones() {
        const sanctumToken = getSanctumToken();

        // CORRECCIÓN 2: Si no hay token, no intentes cargar las notificaciones
        if (!sanctumToken) {
            console.warn("No hay token de autenticación disponible para cargar notificaciones.");
            const ul = document.getElementById("lista-notificaciones"); // CORRECCIÓN 3: ID correcto
            ul.innerHTML = '<li>⚠️ Necesitas iniciar sesión para ver notificaciones.</li>';
            return;
        }

        fetch("http://127.0.0.1:8000/api/notificaciones", {
            headers: {
                "Authorization": `Bearer ${sanctumToken}`, // Usar template literals para claridad
                "Accept": "application/json"
            }
        })
        .then(response => {
            // CORRECCIÓN 4: Mejor manejo de respuestas de error.
            // Si la respuesta no es OK (ej. 401 Unauthorized, 500 Internal Server Error),
            // intenta leer el JSON de error si existe para más detalles.
            if (!response.ok) {
                // Si es un 401, el usuario no está autenticado. Podrías redirigir al login.
                if (response.status === 401) {
                    console.error("Acceso no autorizado a notificaciones. Redirigiendo a login...");
                    // window.location.href = '/login'; // O la ruta de tu login
                    throw new Error("No autorizado. Inicia sesión.");
                }
                // Intenta leer el mensaje de error del backend si es un JSON
                return response.json().then(errorData => {
                    throw new Error(`Respuesta no OK: ${response.status} - ${errorData.message || response.statusText}`);
                }).catch(() => {
                    // Si no es JSON o hay otro error al leer el JSON, simplemente arroja el error de red
                    throw new Error(`Respuesta no OK: ${response.status} - ${response.statusText}`);
                });
            }
            return response.json();
        })
        .then(data => {
            const ul = document.getElementById("lista-notificaciones"); // CORRECCIÓN 3: ID correcto
            ul.innerHTML = ''; // Limpia la lista existente

            // CORRECCIÓN 5: Asegúrate de que `data.notificaciones` sea un array antes de iterar.
            // Esto previene errores si el backend devuelve un formato inesperado en un caso de éxito.
            if (!data.notificaciones || !Array.isArray(data.notificaciones)) {
                const li = document.createElement('li');
                li.textContent = "⚠️ Error: Formato de datos de notificaciones inesperado.";
                ul.appendChild(li);
                return; // Sal de la función si los datos no son válidos
            }

            if (data.notificaciones.length === 0) {
                const li = document.createElement('li');
                li.textContent = "🕓 No hay notificaciones aún.";
                li.style.fontStyle = "italic";
                ul.appendChild(li);
            } else {
                data.notificaciones.forEach(noti => {
                    const li = document.createElement('li');
                    // CORRECCIÓN 6: Acceder a 'mensaje' de forma segura.
                    li.innerHTML = `📌 ${noti.mensaje || 'Mensaje no disponible'}`;
                    li.style.padding = "4px 0";
                    ul.appendChild(li);
                });
            }
        })
        .catch(err => {
            console.error("Error cargando notificaciones:", err);
            const ul = document.getElementById("lista-notificaciones"); // CORRECCIÓN 3: ID correcto

            // CORRECCIÓN 7: Asegurarse de que `ul` no sea null antes de modificarlo.
            if (ul) {
                ul.innerHTML = '';
                const li = document.createElement('li');
                li.textContent = `❌ Error: ${err.message || 'No se pudieron cargar las notificaciones.'}`;
                li.style.color = "red";
                ul.appendChild(li);
            } else {
                console.error("Elemento 'lista-notificaciones' no encontrado en el DOM.");
            }
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        cargarNotificaciones();              // Carga al entrar
        setInterval(cargarNotificaciones, 5000); // Y recarga cada 5 segundos
    });

    function capturar() {
        const btns = document.querySelectorAll('button');
        btns.forEach(b => {
            b.disabled = true;
            b.textContent = "⏳ Procesando...";
        });

        Webcam.snap(function(data_uri) {
            // 1. Guardar en campo oculto
            document.getElementById('imagen').value = data_uri;

            // 2. Convertir a blob para Flask
            const blob = dataURItoBlob(data_uri);
            const formData = new FormData();
            formData.append('image', blob, 'captura.jpg');

            // 3. Enviar a Flask
            fetch('http://localhost:5000/detect', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // CORRECCIÓN 8: Manejo de errores para la respuesta de Flask.
                if (!response.ok) {
                    throw new Error(`Error de Flask: ${response.status} - ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Resultado Flask YOLO:', data);

                // CORRECCIÓN 9: El backend de Flask devuelve `name` y `confidence`, no `class`.
                // Revisa la estructura de tu JSON de Flask si es diferente.
                // Tu controlador Flask actual devuelve: [{"name": "nombre", "confidence": 0.X}]
                if (data && Array.isArray(data) && data.length > 0) {
                    const mensaje = data.map(d => `${d.name} (${(d.confidence * 100).toFixed(2)}%)`).join(', ');

                    // 4. Enviar notificación a Laravel
                    // Asegúrate de que el token de Sanctum esté disponible para esta llamada POST también
                    const sanctumToken = getSanctumToken();
                    if (!sanctumToken) {
                        console.error("No hay token de autenticación para enviar la notificación.");
                        document.getElementById('resultado').textContent = '❌ Error: No se pudo enviar la notificación (token ausente).';
                        return;
                    }

                    fetch("http://127.0.0.1:8000/api/notificaciones", {
                        method: "POST",
                        headers: {
                            "Authorization": `Bearer ${sanctumToken}`,
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            // "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content // CSRF es para POST de Laravel, no para API con Sanctum si la API está Stateless
                        },
                        body: JSON.stringify({ mensaje: mensaje })
                    })
                    .then(res => {
                        // CORRECCIÓN 10: Manejo de errores para la notificación POST
                        if (!res.ok) {
                            return res.json().then(errorData => {
                                throw new Error(`Error al enviar notificación: ${res.status} - ${errorData.message || res.statusText}`);
                            }).catch(() => {
                                throw new Error(`Error al enviar notificación: ${res.status} - ${res.statusText}`);
                            });
                        }
                        return res.json();
                    })
                    .then(resp => {
                        console.log("Notificación enviada:", resp);
                        cargarNotificaciones(); // ✅ Refresca la lista
                    })
                    .catch(err => console.error("Error enviando notificación:", err));
                } else {
                    document.getElementById('resultado').textContent = '✅ No se detectaron plagas en esta imagen.';
                }

                // 5. Guardar imagen en Laravel (tu formulario principal)
                // Esto enviará la imagen capturada por la cámara (base64) al controlador Laravel `CapturaController@guardarImagen`
                document.getElementById('form-guardar').submit();
            })
            .catch(error => {
                console.error('Error al procesar la imagen con Flask o al guardar:', error);
                document.getElementById('resultado').textContent =
                    '❌ Error al procesar la imagen: ' + (error.message || 'Error desconocido.');
            })
            .finally(() => {
                btns.forEach(b => {
                    b.disabled = false;
                    b.textContent = "📸 Capturar y Analizar";
                });
            });
        });
    }

    function dataURItoBlob(dataURI) {
        const byteString = atob(dataURI.split(',')[1]);
        const mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
        const ab = new ArrayBuffer(byteString.length);
        const ia = new Uint8Array(ab);
        for (let i = 0; i < byteString.length; i++) {
            ia[i] = byteString.charCodeAt(i);
        }
        return new Blob([ab], { type: mimeString });
    }
    </script>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>
</body>
</html>