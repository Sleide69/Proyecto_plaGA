<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Captura de Imagen - Monitoreo</title>
    @vite(['resources/css/captura.css'])
</head>
<body>
    <div class="contenedor">
        <div class="formulario">
            <h1>📷 Captura de Imagen</h1>
            <p>Usa tu cámara para registrar el estado de las plantas</p>

            <div class="camara" id="mi_camera"></div>

            <button onclick="capturar()">📸 Capturar y Analizar</button>

            <div id="resultado" style="margin-top: 20px; font-family: monospace;"></div>

            <form method="POST" action="{{ route('guardar.imagen') }}">
                @csrf
                <input type="hidden" name="imagen" id="imagen">
                <button type="submit">💾 Guardar Imagen</button>
            </form>
        </div>
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

    function capturar() {
        Webcam.snap(function(data_uri) {
            // 1. Guardar base64 para Laravel
            document.getElementById('imagen').value = data_uri;

            // 2. Enviar a Flask para detección
            const blob = dataURItoBlob(data_uri);
            const formData = new FormData();
            formData.append('image', blob, 'captura.jpg');

            fetch('http://127.0.0.1:5000/detect', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Mostrar los resultados en consola (opcional)
                console.log('Resultado Flask YOLO:', data);

                // 3. Enviar automáticamente el formulario Laravel
                document.querySelector('form[action="{{ route('guardar.imagen') }}"]').submit();
            })
            .catch(error => {
                document.getElementById('resultado').textContent =
                    '❌ Error al procesar la imagen: ' + error;
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
