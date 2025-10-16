<?php

use App\Models\Tenant;
use App\Models\User;

test('user can register with valid data', function () {
    $response = $this->postJson('/api/auth/register', [
        'tenant_name' => 'Test Company',
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'user' => ['id', 'name', 'email', 'tenant_id'],
            'token',
            'token_type',
            'expires_in',
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
    ]);

    $this->assertDatabaseHas('tenants', [
        'name' => 'Test Company',
    ]);
});

test('user can login', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'user',
            'token',
            'token_type',
            'expires_in',
        ]);
});

test('user receives jwt token on login', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200);

    expect($response->json('token'))->not()->toBeNull();
    expect($response->json('token_type'))->toBe('bearer');
    expect($response->json('expires_in'))->toBeInt();
});

test('user cannot register with duplicate email', function () {
    $tenant = Tenant::factory()->create();
    User::factory()->create([
        'tenant_id' => $tenant->id,
        'email' => 'test@example.com',
    ]);

    $response = $this->postJson('/api/auth/register', [
        'tenant_name' => 'Another Company',
        'name' => 'Another User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('user can logout', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $response = $this->actingAs($user, 'api')
        ->postJson('/api/auth/logout');

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Successfully logged out',
        ]);
});

test('user can access me route when authenticated', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/auth/me');

    $response->assertStatus(200)
        ->assertJson([
            'id' => $user->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'tenant_id' => $tenant->id,
        ]);
});
