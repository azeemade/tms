<?php

namespace Tests\Unit\Http\Controllers\Auth;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SignupRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $authController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authController = new AuthController();
    }

    public function test_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $request = new LoginRequest([
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        Auth::shouldReceive('attempt')->once()->andReturn('fake-token');
        Auth::shouldReceive('user')->once()->andReturn($user);

        $response = $this->authController->login($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('success', $response->getData()->status);
        $this->assertEquals('User successfully logged in', $response->getData()->message);
    }

    public function test_login_with_invalid_credentials()
    {
        $request = new LoginRequest([
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        Auth::shouldReceive('attempt')->once()->andReturn(false);

        $response = $this->authController->login($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('error', $response->getData()->status);
        $this->assertEquals('Unauthorized', $response->getData()->message);
    }

    public function test_signup_with_valid_data()
    {
        $request = new SignupRequest([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response = $this->authController->signup($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('success', $response->getData()->status);
        $this->assertEquals('User created successfully. Please login!', $response->getData()->message);
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    public function test_logout()
    {
        Auth::shouldReceive('logout')->once();

        $response = $this->authController->logout();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('success', $response->getData()->status);
        $this->assertEquals('Successfully logged out', $response->getData()->message);
    }

    public function test_refresh()
    {
        $user = User::factory()->create();
        Auth::shouldReceive('user')->once()->andReturn($user);
        Auth::shouldReceive('refresh')->once()->andReturn('new-token');

        $response = $this->authController->refresh();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('success', $response->getData()->status);
        $this->assertEquals('Token refreshed successfully', $response->getData()->message);
    }
}