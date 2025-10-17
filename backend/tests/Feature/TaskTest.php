<?php

use App\Jobs\SendTaskCompletedEmail;
use App\Jobs\SendTaskCreatedEmail;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

test('authenticated user can create task', function () {
    Queue::fake();

    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $response = $this->actingAs($user, 'api')
        ->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => '2025-12-31',
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'description',
                'status',
                'priority',
                'due_date',
                'user',
            ],
        ])
        ->assertJson([
            'data' => [
                'title' => 'Test Task',
                'description' => 'Test Description',
                'status' => 'pending',
                'priority' => 'high',
            ],
        ]);

    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task',
        'user_id' => $user->id,
        'tenant_id' => $tenant->id,
    ]);
});

test('authenticated user can list their tasks', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    Task::factory()->count(3)->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/tasks');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'description', 'status', 'priority', 'user'],
            ],
        ]);

    expect($response->json('data'))->toHaveCount(3);
});

test('authenticated user can view task details', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $task = Task::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
        'title' => 'Specific Task',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $task->id,
                'title' => 'Specific Task',
            ],
        ]);
});

test('authenticated user can update task', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $task = Task::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
        'title' => 'Original Title',
    ]);

    $response = $this->actingAs($user, 'api')
        ->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'status' => 'in_progress',
            'priority' => 'low',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $task->id,
                'title' => 'Updated Title',
                'status' => 'in_progress',
            ],
        ]);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Updated Title',
    ]);
});

test('authenticated user can delete task', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $task = Task::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user, 'api')
        ->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Tarefa excluída com sucesso',
        ]);

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});

test('user cannot view tasks from another tenant', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    $user1 = User::factory()->create(['tenant_id' => $tenant1->id]);
    $user2 = User::factory()->create(['tenant_id' => $tenant2->id]);

    $task = Task::factory()->create([
        'tenant_id' => $tenant2->id,
        'user_id' => $user2->id,
    ]);

    $response = $this->actingAs($user1, 'api')
        ->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(404);
});

test('status filter works correctly', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    Task::factory()->count(2)->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
        'status' => 'pending',
    ]);

    Task::factory()->count(3)->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
        'status' => 'completed',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/tasks?status=pending');

    $response->assertStatus(200);

    expect($response->json('data'))->toHaveCount(2);
    expect($response->json('data.0.status'))->toBe('pending');
});

test('priority filter works correctly', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    Task::factory()->count(2)->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
        'priority' => 'high',
    ]);

    Task::factory()->count(3)->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
        'priority' => 'low',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/tasks?priority=high');

    $response->assertStatus(200);

    expect($response->json('data'))->toHaveCount(2);
    expect($response->json('data.0.priority'))->toBe('high');
});

test('email is dispatched when creating task', function () {
    Queue::fake();

    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $this->actingAs($user, 'api')
        ->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'priority' => 'medium',
        ]);

    Queue::assertPushed(SendTaskCreatedEmail::class);
});

test('email is dispatched when completing task', function () {
    Queue::fake();

    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $task = Task::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
        'status' => 'pending',
    ]);

    $this->actingAs($user, 'api')
        ->putJson("/api/tasks/{$task->id}", [
            'title' => $task->title,
            'description' => $task->description,
            'status' => 'completed',
            'priority' => $task->priority,
        ]);

    Queue::assertPushed(SendTaskCompletedEmail::class);
});
