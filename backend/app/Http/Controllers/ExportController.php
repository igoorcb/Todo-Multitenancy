<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateTasksExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function export(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'priority', 'user_id', 'search', 'due_date_from', 'due_date_to']);
        $tenantId = auth()->user()->tenant_id;
        $userId = auth()->id();

        $filename = "tasks_export_{$tenantId}_{$userId}_" . time() . ".xlsx";

        GenerateTasksExport::dispatch($tenantId, $userId, $filters, $filename);

        return response()->json([
            'message' => 'Export is being processed',
            'filename' => $filename,
        ], 202);
    }

    public function status(string $filename): JsonResponse
    {
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
}
