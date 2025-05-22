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

            <button onclick="capturar()">📸 Capturar</button>

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
                document.getElementById('imagen').value = data_uri;
            });
        }
    </script>
    <form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit">Cerrar sesión</button>
</form>
</body>
</html>
