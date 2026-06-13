<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthSessionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['jwt.secret' => 'testing-secret']);
        $this->seed(DatabaseSeeder::class);
    }

    public function test_user_can_login_with_demo_credentials(): void
    {
        $response = $this
            ->withHeader('X-Tenant-ID', DemoUserSeeder::TENANT_ID)
            ->postJson('/api/v1/auth/login', [
                'email' => DemoUserSeeder::EMAIL,
                'password' => DemoUserSeeder::PASSWORD,
            ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'roles',
                    'tenant',
                ],
            ])
            ->assertJsonPath('token_type', 'bearer')
            ->assertJsonPath('user.email', DemoUserSeeder::EMAIL)
            ->assertJsonPath('user.tenant.id', DemoUserSeeder::TENANT_ID);
    }

    public function test_login_requires_valid_credentials(): void
    {
        $response = $this
            ->withHeader('X-Tenant-ID', DemoUserSeeder::TENANT_ID)
            ->postJson('/api/v1/auth/login', [
                'email' => DemoUserSeeder::EMAIL,
                'password' => 'incorrecta',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_tenant_header_is_required_for_login(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => DemoUserSeeder::EMAIL,
            'password' => DemoUserSeeder::PASSWORD,
        ]);

        $response
            ->assertBadRequest()
            ->assertJsonPath('message', 'La cabecera X-Tenant-ID es obligatoria.');
    }

    public function test_authenticated_user_can_read_current_session(): void
    {
        $token = $this->loginAndReturnToken();

        $response = $this
            ->withHeader('X-Tenant-ID', DemoUserSeeder::TENANT_ID)
            ->withToken($token)
            ->getJson('/api/v1/auth/me');

        $response
            ->assertOk()
            ->assertJsonPath('user.email', DemoUserSeeder::EMAIL);
    }

    public function test_authenticated_request_rejects_mismatched_tenant(): void
    {
        $token = $this->loginAndReturnToken();
        $otherTenant = Tenant::factory()->create();

        $response = $this
            ->withHeader('X-Tenant-ID', $otherTenant->id)
            ->withToken($token)
            ->getJson('/api/v1/auth/me');

        $response
            ->assertForbidden()
            ->assertJsonPath('message', 'El tenant indicado no coincide con el usuario del token.');
    }

    public function test_logout_invalidates_current_token(): void
    {
        $token = $this->loginAndReturnToken();

        $this
            ->withHeader('X-Tenant-ID', DemoUserSeeder::TENANT_ID)
            ->withToken($token)
            ->postJson('/api/v1/auth/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Sesión cerrada correctamente.');

        $this
            ->withHeader('X-Tenant-ID', DemoUserSeeder::TENANT_ID)
            ->withToken($token)
            ->getJson('/api/v1/auth/me')
            ->assertUnauthorized();
    }

    private function loginAndReturnToken(): string
    {
        return $this
            ->withHeader('X-Tenant-ID', DemoUserSeeder::TENANT_ID)
            ->postJson('/api/v1/auth/login', [
                'email' => DemoUserSeeder::EMAIL,
                'password' => DemoUserSeeder::PASSWORD,
            ])
            ->json('access_token');
    }
}
