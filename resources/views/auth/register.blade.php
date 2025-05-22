<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    @vite(['resources/css/auth.css'])
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Crear Cuenta</h1>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="text" name="name" placeholder="Nombre completo" required>
                <input type="email" name="email" placeholder="Correo electrónico" required>
                <input type="number" name="cedula" placeholder="Cédula" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <input type="password" name="password_confirmation" placeholder="Confirmar contraseña" required>
                <button class="boton" type="submit">Registrarse</button>
            </form>
            <a href="{{ route('login.form') }}">¿Ya tienes cuenta? Inicia sesión</a>
        </div>
    </div>
</body>
</html>
