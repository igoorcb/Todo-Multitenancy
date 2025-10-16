<?php

use App\Jobs\GenerateTasksExport;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

test('authenticated user can request export', function () {
    Queue::fake();

    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $response = $this->actingAs($user, 'api')
        ->postJson('/api/exports/tasks');

    $response->assertStatus(202)
        ->assertJsonStructure([
            'message',
            'filename',
        ])
        ->assertJson([
            'message' => 'Export is being processed',
        ]);

    expect($response->json('filename'))->toContain('tasks_export_');
});

test('export returns job filename', function () {
    Queue::fake();

    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $response = $this->actingAs($user, 'api')
        ->postJson('/api/exports/tasks');

    $response->assertStatus(202);

    $filename = $response->json('filename');

    expect($filename)->toBeString();
    expect($filename)->toContain("tasks_export_{$tenant->id}_{$user->id}");
});

test('user can check export status', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $filename = "tasks_export_{$tenant->id}_{$user->id}_" . time() . ".xlsx";

    $response = $this->actingAs($user, 'api')
        ->getJson("/api/exports/tasks/{$filename}/status");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'filename',
        ])
        ->assertJson([
            'status' => 'processing',
            'filename' => $filename,
        ]);
});

test('export is processed via job', function () {
    Queue::fake();

    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $this->actingAs($user, 'api')
        ->postJson('/api/exports/tasks', [
            'status' => 'completed',
            'priority' => 'high',
        ]);

    Queue::assertPushed(GenerateTasksExport::class, function ($job) use ($tenant, $user) {
        return $job->tenantId === $tenant->id
            && $job->userId === $user->id
            && isset($job->filters['status'])
            && isset($job->filters['priority']);
    });
});

test('user can check completed export status', function () {
    Storage::fake();

    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $filename = "tasks_export_{$tenant->id}_{$user->id}_" . time() . ".xlsx";
    Storage::put("exports/{$filename}", 'fake content');

    $response = $this->actingAs($user, 'api')
        ->getJson("/api/exports/tasks/{$filename}/status");

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'completed',
            'filename' => $filename,
        ]);
});

test('user can download completed export', function () {
    Storage::fake();

    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $filename = "tasks_export_{$tenant->id}_{$user->id}_" . time() . ".xlsx";
    Storage::put("exports/{$filename}", 'fake export content');

    $response = $this->actingAs($user, 'api')
        ->get("/api/exports/tasks/{$filename}/download");

    $response->assertStatus(200);
    $response->assertDownload($filename);
});

test('download returns 404 for non-existent file', function () {
    Storage::fake();

    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $filename = "tasks_export_{$tenant->id}_{$user->id}_" . time() . ".xlsx";

    $response = $this->actingAs($user, 'api')
        ->get("/api/exports/tasks/{$filename}/download");

    $response->assertStatus(404)
        ->assertJson([
            'message' => 'File not found or still processing',
        ]);
});

test('download returns 400 for invalid filename', function () {
    Storage::fake();

    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $filename = "invalid_filename.xlsx";

    $response = $this->actingAs($user, 'api')
        ->get("/api/exports/tasks/{$filename}/download");

    $response->assertStatus(400)
        ->assertJson([
            'message' => 'Invalid filename',
        ]);
});
