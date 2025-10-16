<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tarefa Concluída</title>
</head>
<body>
    <h1>Tarefa Concluída</h1>

    <p>Olá {{ $task->user->name }},</p>

    <p>Parabéns! A seguinte tarefa foi concluída:</p>

    <ul>
        <li><strong>Título:</strong> {{ $task->title }}</li>
        <li><strong>Descrição:</strong> {{ $task->description ?? 'N/A' }}</li>
        <li><strong>Prioridade:</strong> {{ ucfirst($task->priority) }}</li>
        <li><strong>Concluída em:</strong> {{ $task->updated_at->format('d/m/Y H:i:s') }}</li>
    </ul>

    <p>Atenciosamente,<br>{{ config('app.name') }}</p>
</body>
</html>
