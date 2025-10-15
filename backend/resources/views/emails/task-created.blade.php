<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Task Created</title>
</head>
<body>
    <h1>New Task Created</h1>

    <p>Hello {{ $task->user->name }},</p>

    <p>A new task has been created:</p>

    <ul>
        <li><strong>Title:</strong> {{ $task->title }}</li>
        <li><strong>Description:</strong> {{ $task->description ?? 'N/A' }}</li>
        <li><strong>Status:</strong> {{ ucfirst($task->status) }}</li>
        <li><strong>Priority:</strong> {{ ucfirst($task->priority) }}</li>
        <li><strong>Due Date:</strong> {{ $task->due_date ? $task->due_date->format('Y-m-d H:i:s') : 'N/A' }}</li>
    </ul>

    <p>Best regards,<br>{{ config('app.name') }}</p>
</body>
</html>
