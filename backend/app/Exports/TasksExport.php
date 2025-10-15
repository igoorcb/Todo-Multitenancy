<?php

namespace App\Exports;

use App\Models\Task;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

class TasksExport
{
    public function __construct(
        private int $tenantId,
        private array $filters = []
    ) {}

    public function store(string $filePath): void
    {
        $writer = new Writer();
        $writer->openToFile($filePath);

        $headerRow = Row::fromValues([
            'ID',
            'Title',
            'Description',
            'Status',
            'Priority',
            'Due Date',
            'User',
            'Created At',
            'Updated At',
        ]);
        $writer->addRow($headerRow);

        $query = Task::query()
            ->where('tenant_id', $this->tenantId)
            ->with('user');

        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['priority'])) {
            $query->where('priority', $this->filters['priority']);
        }

        if (isset($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        if (isset($this->filters['search'])) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->filters['search']}%")
                  ->orWhere('description', 'like', "%{$this->filters['search']}%");
            });
        }

        if (isset($this->filters['due_date_from'])) {
            $query->where('due_date', '>=', $this->filters['due_date_from']);
        }

        if (isset($this->filters['due_date_to'])) {
            $query->where('due_date', '<=', $this->filters['due_date_to']);
        }

        $query->orderBy('created_at', 'desc')
            ->chunk(1000, function ($tasks) use ($writer) {
                foreach ($tasks as $task) {
                    $row = Row::fromValues([
                        $task->id,
                        $task->title,
                        $task->description ?? '',
                        $task->status,
                        $task->priority,
                        $task->due_date?->format('Y-m-d H:i:s') ?? '',
                        $task->user->name,
                        $task->created_at->format('Y-m-d H:i:s'),
                        $task->updated_at->format('Y-m-d H:i:s'),
                    ]);
                    $writer->addRow($row);
                }
            });

        $writer->close();
    }
}
