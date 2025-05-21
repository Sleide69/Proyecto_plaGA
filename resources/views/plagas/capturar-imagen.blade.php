<div id="mi_camera"></div>
<button onclick="capturar()">Capturar</button>
<form method="POST" action="{{ route('guardar.imagen') }}">
    @csrf
    <input type="hidden" name="imagen" id="imagen">
    <button type="submit">Guardar Imagen</button>
</form>

<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
<script>
    Webcam.set({
        width: 320,
        height: 240,
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
