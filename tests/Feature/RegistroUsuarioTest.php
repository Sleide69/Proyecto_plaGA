<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RegistroUsuarioTest extends TestCase
{
    use RefreshDatabase;

        public function test_registro_de_usuario_funciona()
    {
        $response = $this->post('/register', [
            'name' => 'Usuario XP',
            'cedula' => '1234567890',
            'email' => 'usuarioxp@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/login'); // ajusta según tu redirección real

        $this->withSession(['_token' => csrf_token()]);

        $this->assertDatabaseHas('users', [
            'email' => 'usuarioxp@example.com',
            'cedula' => '1234567890',
        ]);
    }

}
