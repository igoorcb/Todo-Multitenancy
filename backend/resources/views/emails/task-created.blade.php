<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nova Tarefa Criada</title>
</head>
<body>
    <h1>Nova Tarefa Criada</h1>

    <p>Olá {{ $task->user->name }},</p>

    <p>Uma nova tarefa foi criada:</p>

    <ul>
        <li><strong>Título:</strong> {{ $task->title }}</li>
        <li><strong>Descrição:</strong> {{ $task->description ?? 'N/A' }}</li>
        <li><strong>Status:</strong> {{ ucfirst($task->status) }}</li>
        <li><strong>Prioridade:</strong> {{ ucfirst($task->priority) }}</li>
        <li><strong>Data de Vencimento:</strong> {{ $task->due_date ? $task->due_date->format('d/m/Y H:i:s') : 'N/A' }}</li>
    </ul>

    <p>Atenciosamente,<br>{{ config('app.name') }}</p>
</body>
</html>
