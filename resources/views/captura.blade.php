<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    function cargarNotificaciones() {
        fetch("http://127.0.0.1:8000/api/notificaciones", {
            headers: {
                "Authorization": "Bearer " + document.querySelector('meta[name="token-sanctum"]').content,
                "Accept": "application/json"
            }
        })
        .then(response => {
            if (!response.ok) throw new Error("Respuesta no OK");
            return response.json();
        })
        .then(data => {
            const ul = document.getElementById("notificaciones-lista");
            ul.innerHTML = '';

            if (!data.notificaciones || !Array.isArray(data.notificaciones)) {
                const li = document.createElement('li');
                li.textContent = "⚠️ Error en los datos recibidos.";
                ul.appendChild(li);
                return;
            }

            if (data.notificaciones.length === 0) {
                const li = document.createElement('li');
                li.textContent = "🕓 No hay notificaciones aún.";
                li.style.fontStyle = "italic";
                ul.appendChild(li);
            } else {
                data.notificaciones.forEach(noti => {
                    const li = document.createElement('li');
                    li.innerHTML = `📌 ${noti.mensaje}`;
                    li.style.padding = "4px 0";
                    ul.appendChild(li);
                });
            }
        })
        .catch(err => {
            console.error("Error cargando notificaciones:", err);
            const ul = document.getElementById("notificaciones-lista");
            ul.innerHTML = '';
            const li = document.createElement('li');
            li.textContent = "❌ No se pudieron cargar las notificaciones.";
            li.style.color = "red";
            ul.appendChild(li);
        });
    }


document.addEventListener("DOMContentLoaded", () => {
    cargarNotificaciones();                  // Carga al entrar
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
        fetch('http://127.0.0.1:5000/detect', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('Resultado Flask YOLO:', data);

            if (data.detections && data.detections.length > 0) {
                const mensaje = data.detections.map(d => `${d.class} (${d.confidence})`).join(', ');

                // 4. Enviar notificación a Laravel
                fetch("http://127.0.0.1:8000/api/notificaciones", {
                    method: "POST",
                    headers: {
                        "Authorization": "Bearer " + document.querySelector('meta[name="token-sanctum"]').content,
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ mensaje: mensaje })
                })
                .then(res => res.json())
                .then(resp => {
                    console.log("Notificación enviada:", resp);
                    cargarNotificaciones(); // ✅ Refresca la lista
                })
                .catch(err => console.error("Error enviando notificación:", err));
            }

            // 5. Guardar imagen en Laravel
            document.getElementById('form-guardar').submit();
        })
        .catch(error => {
            document.getElementById('resultado').textContent =
                '❌ Error al procesar la imagen: ' + error;
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
