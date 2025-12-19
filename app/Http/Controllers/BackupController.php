<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BackupController extends Controller
{
    /**
     * Display a listing of the backups.
     */
    public function index(): JsonResponse
    {
        $backups = Backup::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $backups
        ]);
    }

    /**
     * Store a newly created backup in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'sometimes|string|in:full,incremental,selective',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Here you would typically call a backup service to create the actual backup file
        // For now, we'll simulate creating a backup entry
        
        $backup = Backup::create([
            'filename' => 'backup_' . now()->format('Y_m_d_H_i_s') . '.sql',
            'path' => 'backups/' . now()->format('Y/m/'),
            'disk' => 'local',
            'status' => 'pending',
            'type' => $request->input('type', 'full'),
            'created_by' => auth()->user()?->name ?? 'system'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Backup creation initiated',
            'data' => $backup
        ], 201);
    }

    /**
     * Display the specified backup.
     */
    public function show(string $id): JsonResponse
    {
        $backup = Backup::find($id);

        if (!$backup) {
            return response()->json([
                'success' => false,
                'message' => 'Backup not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $backup
        ]);
    }

    /**
     * Update the specified backup in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $backup = Backup::find($id);

        if (!$backup) {
            return response()->json([
                'success' => false,
                'message' => 'Backup not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|string|in:pending,processing,completed,failed',
            'size' => 'sometimes|integer',
            'completed_at' => 'sometimes|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $backup->update($request->only(['status', 'size', 'completed_at']));

        return response()->json([
            'success' => true,
            'message' => 'Backup updated successfully',
            'data' => $backup
        ]);
    }

    /**
     * Remove the specified backup from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $backup = Backup::find($id);

        if (!$backup) {
            return response()->json([
                'success' => false,
                'message' => 'Backup not found'
            ], 404);
        }

        // Delete the actual backup file from storage
        if (Storage::disk($backup->disk)->exists($backup->path . $backup->filename)) {
            Storage::disk($backup->disk)->delete($backup->path . $backup->filename);
        }

        $backup->delete();

        return response()->json([
            'success' => true,
            'message' => 'Backup deleted successfully'
        ]);
    }

    /**
     * Download the specified backup file.
     */
    public function download(string $id): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $backup = Backup::find($id);

        if (!$backup) {
            abort(404, 'Backup not found');
        }

        $filePath = storage_path('app/' . $backup->path . $backup->filename);

        if (!file_exists($filePath)) {
            abort(404, 'Backup file not found');
        }

        return response()->download($filePath, $backup->filename);
    }

    /**
     * Get statistics about backups.
     */
    public function stats(): JsonResponse
    {
        $totalBackups = Backup::count();
        $totalSize = Backup::sum('size');
        $latestBackup = Backup::latest('created_at')->first();
        $statusCounts = Backup::groupBy('status')
            ->selectRaw('status, count(*) as count')
            ->pluck('count', 'status');

        return response()->json([
            'success' => true,
            'data' => [
                'total_backups' => $totalBackups,
                'total_size_bytes' => $totalSize,
                'total_size_formatted' => $this->formatBytes($totalSize),
                'latest_backup' => $latestBackup,
                'status_counts' => $statusCounts
            ]
        ]);
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(?int $bytes, int $precision = 2): string
    {
        if ($bytes === null || $bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}