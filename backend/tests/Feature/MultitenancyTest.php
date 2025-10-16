<?php

use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;

test('user only sees their own tasks', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    Task::factory()->count(3)->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);

    $anotherUser = User::factory()->create(['tenant_id' => $tenant->id]);
    Task::factory()->count(2)->create([
        'tenant_id' => $tenant->id,
        'user_id' => $anotherUser->id,
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/tasks');

    $response->assertStatus(200);

    expect($response->json('data'))->toHaveCount(5);
});

test('user cannot access tasks from another tenant', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    $user1 = User::factory()->create(['tenant_id' => $tenant1->id]);
    $user2 = User::factory()->create(['tenant_id' => $tenant2->id]);

    $task1 = Task::factory()->create([
        'tenant_id' => $tenant1->id,
        'user_id' => $user1->id,
    ]);

    $task2 = Task::factory()->create([
        'tenant_id' => $tenant2->id,
        'user_id' => $user2->id,
    ]);

    $response = $this->actingAs($user1, 'api')
        ->getJson("/api/tasks/{$task2->id}");

    $response->assertStatus(404);
});

test('tenant_id is filled automatically when creating task', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $response = $this->actingAs($user, 'api')
        ->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'priority' => 'medium',
        ]);

    $response->assertStatus(201);

    $task = Task::find($response->json('data.id'));

    expect($task->tenant_id)->toBe($tenant->id);
});

test('global scope filters automatically by tenant', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    $user1 = User::factory()->create(['tenant_id' => $tenant1->id]);
    $user2 = User::factory()->create(['tenant_id' => $tenant2->id]);

    Task::factory()->count(3)->create([
        'tenant_id' => $tenant1->id,
        'user_id' => $user1->id,
    ]);

    Task::factory()->count(2)->create([
        'tenant_id' => $tenant2->id,
        'user_id' => $user2->id,
    ]);

    $this->actingAs($user1, 'api');

    $tasks = Task::all();

    expect($tasks)->toHaveCount(3);
    expect($tasks->pluck('tenant_id')->unique()->toArray())->toBe([$tenant1->id]);
});

test('user cannot update task from another tenant', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    $user1 = User::factory()->create(['tenant_id' => $tenant1->id]);
    $user2 = User::factory()->create(['tenant_id' => $tenant2->id]);

    $task = Task::factory()->create([
        'tenant_id' => $tenant2->id,
        'user_id' => $user2->id,
    ]);

    $response = $this->actingAs($user1, 'api')
        ->putJson("/api/tasks/{$task->id}", [
            'title' => 'Hacked Title',
            'description' => 'Hacked Description',
            'status' => 'completed',
            'priority' => 'high',
        ]);

    $response->assertStatus(404);

    $task->refresh();

    expect($task->title)->not()->toBe('Hacked Title');
});

test('user cannot delete task from another tenant', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    $user1 = User::factory()->create(['tenant_id' => $tenant1->id]);
    $user2 = User::factory()->create(['tenant_id' => $tenant2->id]);

    $task = Task::factory()->create([
        'tenant_id' => $tenant2->id,
        'user_id' => $user2->id,
    ]);

    $response = $this->actingAs($user1, 'api')
        ->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(404);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
    ]);
});

test('tasks are isolated between tenants in list', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    $user1 = User::factory()->create(['tenant_id' => $tenant1->id]);
    $user2 = User::factory()->create(['tenant_id' => $tenant2->id]);

    Task::factory()->count(5)->create([
        'tenant_id' => $tenant1->id,
        'user_id' => $user1->id,
    ]);

    Task::factory()->count(3)->create([
        'tenant_id' => $tenant2->id,
        'user_id' => $user2->id,
    ]);

    $response1 = $this->actingAs($user1, 'api')
        ->getJson('/api/tasks');

    $response2 = $this->actingAs($user2, 'api')
        ->getJson('/api/tasks');

    expect($response1->json('data'))->toHaveCount(5);
    expect($response2->json('data'))->toHaveCount(3);
});
