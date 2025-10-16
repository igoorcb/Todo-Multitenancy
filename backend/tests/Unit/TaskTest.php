<?php

use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;

test('task belongs to a tenant', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $task = Task::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);

    expect($task->tenant)->toBeInstanceOf(Tenant::class);
    expect($task->tenant->id)->toBe($tenant->id);
});

test('task belongs to a user', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $task = Task::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);

    expect($task->user)->toBeInstanceOf(User::class);
    expect($task->user->id)->toBe($user->id);
});

test('task has valid status values', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $statuses = ['pending', 'in_progress', 'completed'];

    foreach ($statuses as $status) {
        $task = Task::factory()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'status' => $status,
        ]);

        expect($task->status)->toBe($status);
    }
});

test('task has valid priority values', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $priorities = ['low', 'medium', 'high'];

    foreach ($priorities as $priority) {
        $task = Task::factory()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'priority' => $priority,
        ]);

        expect($task->priority)->toBe($priority);
    }
});

test('task has correct fillable attributes', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $task = Task::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
        'title' => 'Test Task Title',
        'description' => 'Test Task Description',
        'status' => 'pending',
        'priority' => 'high',
        'due_date' => '2025-12-31',
    ]);

    expect($task->title)->toBe('Test Task Title');
    expect($task->description)->toBe('Test Task Description');
    expect($task->status)->toBe('pending');
    expect($task->priority)->toBe('high');
    expect($task->due_date)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

test('task tenant relationship returns correct type', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    $task = Task::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);

    expect($task->tenant())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

test('task user relationship returns correct type', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    $task = Task::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);

    expect($task->user())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

test('task due_date is cast to datetime', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $task = Task::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
        'due_date' => '2025-12-31 23:59:59',
    ]);

    expect($task->due_date)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});
