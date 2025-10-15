<?php

namespace App\Jobs;

use App\Exports\TasksExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateTasksExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $tenantId,
        public int $userId,
        public array $filters,
        public string $filename
    ) {}

    public function handle(): void
    {
        $export = new TasksExport($this->tenantId, $this->filters);

        $path = "exports/{$this->filename}";

        Storage::put($path, '');

        $export->store(Storage::path($path));
    }
}
