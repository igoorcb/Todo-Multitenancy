<?php

use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;

test('user belongs to a tenant', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    expect($user->tenant)->toBeInstanceOf(Tenant::class);
    expect($user->tenant->id)->toBe($tenant->id);
});

test('user can have tasks', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $tasks = Task::factory()->count(4)->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);

    expect($user->tasks)->toHaveCount(4);
    expect($user->tasks->first())->toBeInstanceOf(Task::class);
    expect($user->tasks->pluck('id')->toArray())->toEqual($tasks->pluck('id')->toArray());
});

test('user implements jwt correctly', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    expect($user)->toBeInstanceOf(\PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject::class);
});

test('user getJWTIdentifier returns user id', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    expect($user->getJWTIdentifier())->toBe($user->id);
});

test('user getJWTCustomClaims includes tenant_id', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $claims = $user->getJWTCustomClaims();

    expect($claims)->toBeArray();
    expect($claims)->toHaveKey('tenant_id');
    expect($claims['tenant_id'])->toBe($tenant->id);
});

test('user has correct fillable attributes', function () {
    $tenant = Tenant::factory()->create();

    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => bcrypt('password123'),
    ]);

    expect($user->tenant_id)->toBe($tenant->id);
    expect($user->name)->toBe('John Doe');
    expect($user->email)->toBe('john@example.com');
});

test('user password is hidden in array', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $array = $user->toArray();

    expect($array)->not()->toHaveKey('password');
    expect($array)->not()->toHaveKey('remember_token');
});

test('user tenant relationship returns correct type', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    expect($user->tenant())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

test('user tasks relationship returns correct type', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    expect($user->tasks())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('user password is hashed', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'password' => 'plainpassword',
    ]);

    expect($user->password)->not()->toBe('plainpassword');
    expect(strlen($user->password))->toBeGreaterThan(20);
});
