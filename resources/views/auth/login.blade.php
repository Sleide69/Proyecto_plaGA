<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    @vite(['resources/css/auth.css'])
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Iniciar Sesión</h1>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="email" name="email" placeholder="Correo electrónico" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button class="boton" type="submit">Entrar</button>
            </form>
            <a href="{{ route('register.form') }}">¿No tienes cuenta? Regístrate</a>
        </div>
    </div>
</body>
</html>

