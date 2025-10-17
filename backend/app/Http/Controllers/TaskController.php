<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Jobs\SendTaskCompletedEmail;
use App\Jobs\SendTaskCreatedEmail;
use App\Models\Task;
use App\QueryBuilders\TaskQueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Task::query()->with('user');

        $queryBuilder = new TaskQueryBuilder($query);
        $filteredQuery = $queryBuilder->applyFilters($request->all())->get();

        $tasks = $filteredQuery->orderBy('created_at', 'desc')->paginate(15);

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? 'pending',
            'priority' => $request->priority ?? 'medium',
            'due_date' => $request->due_date,
        ]);

        SendTaskCreatedEmail::dispatch($task);

        return (new TaskResource($task->load('user')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Task $task): TaskResource
    {
        return new TaskResource($task->load('user'));
    }

    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        $oldStatus = $task->status;

        $task->update($request->validated());

        if ($oldStatus !== 'completed' && $task->status === 'completed') {
            SendTaskCompletedEmail::dispatch($task);
        }

        return new TaskResource($task->load('user'));
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([
            'message' => 'Tarefa excluída com sucesso'
        ]);
    }
}
