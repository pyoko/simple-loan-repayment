<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    const API_REGISTER = '/api/v1/register';
    const API_LOGIN    = '/api/v1/login';
    const API_LOGOUT   = '/api/v1/logout';
    const API_USER     = '/api/v1/users/';


    public function testRegisterRequiresPasswordEmailName()
    {
        $response = $this->json('post', self::API_REGISTER);
        $response->assertStatus(422)
                 ->assertJson([
                    'errors' => [
                        'family_name' => ['The family name field is required.'],
                        'first_name'  => ['The first name field is required.'],
                        'email'       => ['The email field is required.'],
                        'password'    => ['The password field is required.'],
                    ],
                 ]);
    }

    public function testRegisterSuccess()
    {
        $newUser = [
            'family_name' => 'User',
            'first_name'  => 'Test',
            'email'       => 'testuser@example.com',
            'phone'       => '+652212332',
            'password'    => 'secret',
        ];

        $response = $this->json('post', self::API_REGISTER, $newUser);
        $response->assertStatus(201)
                 ->assertJsonStructure([
                    'id',
                    'family_name',
                    'first_name',
                    'email',
                    'phone',
                    'created_at',
                 ]);
    }

    public function testLoginFailUserNotFound()
    {
        $user = factory(User::class)->create([
            'email'    => 'testlogin@example.com',
            'password' => bcrypt('secret')
        ]);

        $userToLogin = [
            'email'    => 'test@example.com',
            'password' => 'secrets',
        ];

        $response = $this->json('post', self::API_LOGIN, $userToLogin);
        $response->assertStatus(422);
    }

    public function testLoginSuccess()
    {
        $user = factory(User::class)->create([
            'email'    => 'testlogin@example.com',
            'password' => bcrypt('secret')
        ]);

        $userToLogin = [
            'email'    => 'testlogin@example.com',
            'password' => 'secret',
        ];

        $response = $this->json('post', self::API_LOGIN, $userToLogin);
        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'id',
                    'family_name',
                    'first_name',
                    'email',
                    'phone',
                    'created_at',
                 ]);
    }

    public function testLogoutSuccess()
    {
        $user = factory(User::class)->create([
            'email' => 'test@example.com'
        ]);
        $token = $user->generateToken();

        $response = $this->json('post', self::API_LOGOUT, [], ['Authorization' => "Bearer $token"]);
        $response->assertStatus(200);

        $user = User::find($user->id);
        $this->assertEquals(null, $user->api_token);
    }

    public function testUserAuthFailNoToken()
    {
        $response = $this->json('get', self::API_USER);
        $response->assertStatus(404);
    }

    public function testUserAuthSuccess()
    {
        $user = factory(User::class)->create();
        $token = $user->generateToken();

        $response = $this->json('get', self::API_USER . $user->id, [], [
            'Authorization' => "Bearer $token",
        ]);
        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'id',
                    'family_name',
                    'first_name',
                    'email',
                    'phone',
                    'created_at',
                 ]);
    }

    public function testUserUpdate()
    {
        $user = factory(User::class)->create();
        $token = $user->generateToken();

        $toBeUpdated = [
            'family_name' => 'Updated',
            'email'       => 'updated@email.com'
        ];

        $response = $this->json('put', self::API_USER . $user->id, $toBeUpdated, [
            'Authorization' => "Bearer $token",
        ]);
        $response->assertStatus(200)
                 ->assertJson([
                    'family_name' => $toBeUpdated['family_name'],
                    'email'       => $toBeUpdated['email'],
                 ]);

    }

    public function testUserDelete()
    {
        $user = factory(User::class)->create();
        $token = $user->generateToken();

        $response = $this->json('delete', self::API_USER . $user->id, [], [
            'Authorization' => "Bearer $token",
        ]);
        $response->assertStatus(204);


        $response = $this->json('get', self::API_USER . $user->id, [], [
            'Authorization' => "Bearer $token",
        ]);
        $response->assertStatus(404);
    }
}
