<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Jobs\SendTaskCompletedEmail;
use App\Jobs\SendTaskCreatedEmail;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Task::query()->with('user');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('due_date_from')) {
            $query->where('due_date', '>=', $request->due_date_from);
        }

        if ($request->has('due_date_to')) {
            $query->where('due_date', '<=', $request->due_date_to);
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($tasks);
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

        return response()->json($task->load('user'), 201);
    }

    public function show(Task $task): JsonResponse
    {
        return response()->json($task->load('user'));
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $oldStatus = $task->status;

        $task->update($request->validated());

        if ($oldStatus !== 'completed' && $task->status === 'completed') {
            SendTaskCompletedEmail::dispatch($task);
        }

        return response()->json($task->load('user'));
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }
}
