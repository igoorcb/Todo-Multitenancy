<?php

use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;

test('tenant can have users', function () {
    $tenant = Tenant::factory()->create();

    $users = User::factory()->count(3)->create([
        'tenant_id' => $tenant->id,
    ]);

    expect($tenant->users)->toHaveCount(3);
    expect($tenant->users->first())->toBeInstanceOf(User::class);
    expect($tenant->users->pluck('id')->toArray())->toEqual($users->pluck('id')->toArray());
});

test('tenant can have tasks', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $tasks = Task::factory()->count(5)->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);

    expect($tenant->tasks)->toHaveCount(5);
    expect($tenant->tasks->first())->toBeInstanceOf(Task::class);
    expect($tenant->tasks->pluck('id')->toArray())->toEqual($tasks->pluck('id')->toArray());
});

test('tenant has correct fillable attributes', function () {
    $tenant = Tenant::factory()->create([
        'name' => 'Test Company',
        'domain' => 'testcompany.com',
        'database' => 'tenant_test',
    ]);

    expect($tenant->name)->toBe('Test Company');
    expect($tenant->domain)->toBe('testcompany.com');
    expect($tenant->database)->toBe('tenant_test');
});

test('tenant users relationship returns correct type', function () {
    $tenant = Tenant::factory()->create();

    expect($tenant->users())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('tenant tasks relationship returns correct type', function () {
    $tenant = Tenant::factory()->create();

    expect($tenant->tasks())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});
