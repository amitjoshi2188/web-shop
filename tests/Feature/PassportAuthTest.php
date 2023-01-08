<?php

namespace Tests\Feature;

namespace Tests\Feature\Http\Controllers\Api\Auth;

use App\Models\User;
use DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\HasApiTokens;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;
use Tests\TestCase;


class PassportAuthTest extends TestCase
{
    use RefreshDatabase, HasApiTokens;

    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function testRequiredFieldsForRegistration(): void
    {
        $this->json('POST', 'api/v1/register', ['Accept' => 'application/json'])
            ->assertStatus(HTTPResponse::HTTP_UNAUTHORIZED)
            ->assertJson([
                "status" => "error",
                "message" => [
                    "name" => ["The name field is required."],
                    "email" => ["The email field is required."],
                    "password" => ["The password field is required."],
                    "c_password" => ["The c password field is required."],
                ]
            ]);
    }

    /**
     * Successful registration call.
     *
     * @return void
     */
    public function testSuccessfulRegistration(): void
    {
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "password" => "demo12345",
            "c_password" => "demo12345"
        ];

        $this->json('POST', 'api/v1/register',
            $userData, ['Accept' => 'application/json'])
            ->assertStatus(HTTPResponse::HTTP_OK)
            ->assertJsonStructure([
                "token"
            ]);
    }

    public function testRequiredFieldsForLogin()
    {
        $this->json('POST', 'api/v1/login', ['Accept' => 'application/json'])
            ->assertStatus(HTTPResponse::HTTP_UNAUTHORIZED)
            ->assertJson([
                "status" => "error",
                "message" => [
                    "email" => ["The email field is required."],
                    "password" => ["The password field is required."],
                ]
            ]);
    }

    public function testSuccessLogin()
    {
        // Creating Users
        $test = User::create([
            'name' => 'Manoj',
            'email' => $email = time() . '@gmail.com',
            'password' => $password = bcrypt('12345678')
        ]);

        // Simulated landing
        $userData = [
            'email' => $email,
            'password' => "12345678",
        ];

        $response = $this->json('POST', 'api/v1/login',
            $userData, ['Accept' => 'application/json'])
            ->assertStatus(HTTPResponse::HTTP_OK)
            ->assertJsonStructure(["status", "message", "token"]);
        $this->assertEquals('success', $response->json()['status']);
        $this->assertEquals('Login successful.', $response->json()['message']);
    }

    public function testUnauthorizedLogin()
    {
        // Creating Users
        $test = User::create([
            'name' => 'Manoj',
            'email' => $email = time() . '@gmail.com',
            'password' => $password = bcrypt('12345678')
        ]);

        // Simulated landing
        $userData = [
            'email' => $email,
            'password' => "123456789",
        ];

        $response = $this->json('POST', 'api/v1/login',
            $userData, ['Accept' => 'application/json'])
            ->assertStatus(HTTPResponse::HTTP_UNAUTHORIZED)
            ->assertJsonStructure(["status", "message"]);
        $this->assertEquals('error', $response->json()['status']);
        $this->assertEquals('Unauthorised', $response->json()['message']);
    }

    public function testUserInfo()
    {
        // Creating Users
        $token = $this->authenticate();


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', 'api/v1/get-user')->assertStatus(HTTPResponse::HTTP_OK)
            ->assertJsonStructure(["status", "message", "user"]);

        $this->assertEquals('success', $response->json()['status']);
        $this->assertEquals('User details.', $response->json()['message']);
    }

    /**
     * Authenticate user.
     */
    private function authenticate()
    {

        User::create([
            'name' => 'Manoj',
            'email' => $email = time() . '@gmail.com',
            'password' => $password = bcrypt('12345678')
        ]);
        if (!auth()->attempt(['email' => $email, 'password' => '12345678'])) {
            return response(['message' => 'Login credentials are invaild']);
        }

        return $accessToken = auth()->user()->createToken('authToken')->accessToken;
    }
}



