<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegistroUsuarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_registro_de_usuario_funciona()
    {
        // Desactiva solo el middleware CSRF para evitar error 419 en la prueba
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $response = $this->post('/register', [
            // No es necesario enviar _token porque desactivamos el middleware CSRF
            'name' => 'Usuario XP',
            'cedula' => '1234567890',
            'email' => 'usuarioxp@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        // Verifica que la respuesta sea una redirección (status 302) a la ruta /login
        $response->assertRedirect(route('login')); // Use named route for better accuracy

        // Verifica que el usuario fue creado en la base de datos
        $this->assertDatabaseHas('users', [
            'email' => 'usuarioxp@example.com',
        ]);

        // Verifica que la contraseña fue hasheada correctamente
        $user = User::where('email', 'usuarioxp@example.com')->first();
        $this->assertTrue(Hash::check('Password123', $user->password));
    }
}
