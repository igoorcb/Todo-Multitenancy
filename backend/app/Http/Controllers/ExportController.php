<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportTasksRequest;
use App\Jobs\GenerateTasksExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function export(ExportTasksRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $tenantId = auth()->user()->tenant_id;
        $userId = auth()->id();

        $filename = $this->generateFilename($tenantId, $userId);

        GenerateTasksExport::dispatch($tenantId, $userId, $filters, $filename);

        return response()->json([
            'message' => 'Export is being processed',
            'filename' => $filename,
        ], 202);
    }

    public function status(string $filename): JsonResponse
    {
        if (!$this->isValidFilename($filename)) {
            return response()->json([
                'message' => 'Invalid filename'
            ], 400);
        }

        $path = "exports/{$filename}";

        if (Storage::exists($path)) {
            return response()->json([
                'status' => 'completed',
                'filename' => $filename,
            ]);
        }

        return response()->json([
            'status' => 'processing',
            'filename' => $filename,
        ]);
    }

    public function download(string $filename): BinaryFileResponse|JsonResponse
    {
        if (!$this->isValidFilename($filename)) {
            return response()->json([
                'message' => 'Invalid filename'
            ], 400);
        }

        $path = "exports/{$filename}";

        if (!Storage::exists($path)) {
            return response()->json([
                'message' => 'File not found or still processing'
            ], 404);
        }

        return response()->download(
            Storage::path($path),
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    private function generateFilename(int $tenantId, int $userId): string
    {
        return "tasks_export_{$tenantId}_{$userId}_" . time() . ".xlsx";
    }

    private function isValidFilename(string $filename): bool
    {
        return preg_match('/^tasks_export_\d+_\d+_\d+\.xlsx$/', $filename) === 1;
    }
}
