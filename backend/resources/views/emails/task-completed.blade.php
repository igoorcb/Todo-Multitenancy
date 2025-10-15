<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Task Completed</title>
</head>
<body>
    <h1>Task Completed</h1>

    <p>Hello {{ $task->user->name }},</p>

    <p>Congratulations! The following task has been completed:</p>

    <ul>
        <li><strong>Title:</strong> {{ $task->title }}</li>
        <li><strong>Description:</strong> {{ $task->description ?? 'N/A' }}</li>
        <li><strong>Priority:</strong> {{ ucfirst($task->priority) }}</li>
        <li><strong>Completed At:</strong> {{ $task->updated_at->format('Y-m-d H:i:s') }}</li>
    </ul>

    <p>Best regards,<br>{{ config('app.name') }}</p>
</body>
</html>
