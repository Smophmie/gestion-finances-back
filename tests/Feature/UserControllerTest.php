<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserControllerTest extends TestCase
{

    public function testRegisterWithValidDatas()
{
    $response = $this->postJson('/register', [
        'name' => 'John Doe',
        'email' => 'johndoe@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201);
            
    $this->assertDatabaseHas('users', [
        'email' => 'johndoe@example.com',
    ]);

    User::where('email', 'johndoe@example.com')->delete();
}

    public function testRegisterWithInvalidName()
    {
        $response = $this->postJson('/register', [
            'name' => '',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'errors' => [
                         'name',
                     ],
                 ]);
    }

    public function testRegisterWithInvalidEmail()
    {
        $response = $this->postJson('/register', [
            'name' => 'John Doe',
            'email' => 'johndoeexample.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'errors' => [
                         'email',
                     ],
                 ]);
    }

    public function testRegisterWithShortPassword()
    {
        $response = $this->postJson('/register', [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'passw',
            'password_confirmation' => 'passw',
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'errors' => [
                         'password',
                     ],
                 ]);
    }

    public function testRegisterWithBadPasswordConfirmation()
    {
        $response = $this->postJson('/register', [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password124',
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'errors' => [
                         'password',
                     ],
                 ]);
    }
}