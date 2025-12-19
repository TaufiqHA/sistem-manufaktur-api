<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Task::query();

        // Apply filters if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->has('machine_id')) {
            $query->where('machine_id', $request->machine_id);
        }

        if ($request->has('shift')) {
            $query->where('shift', $request->shift);
        }

        $tasks = $query->with(['project', 'projectItem', 'machine'])->paginate(
            $request->get('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'data' => $tasks,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|integer|exists:projects,id',
            'project_name' => 'required|string|max:255',
            'item_id' => 'required|integer|exists:project_items,id',
            'item_name' => 'required|string|max:255',
            'step' => 'required|string|max:255',
            'machine_id' => 'required|integer|exists:machines,id',
            'target_qty' => 'required|integer|min:1',
            'completed_qty' => 'nullable|integer|min:0',
            'defect_qty' => 'nullable|integer|min:0',
            'shift' => 'nullable|string|max:50',
            'status' => 'required|string|in:PENDING,IN_PROGRESS,PAUSED,COMPLETED,DOWNTIME',
            'downtime_start' => 'nullable|date',
            'total_downtime_minutes' => 'nullable|integer|min:0',
        ]);

        $validated['completed_qty'] = $validated['completed_qty'] ?? 0;
        $validated['defect_qty'] = $validated['defect_qty'] ?? 0;
        $validated['total_downtime_minutes'] = $validated['total_downtime_minutes'] ?? 0;

        $task = Task::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully.',
            'data' => $task->load(['project', 'projectItem', 'machine']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $task = Task::with(['project', 'projectItem', 'machine'])->find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $task,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found.',
            ], 404);
        }

        $validated = $request->validate([
            'project_id' => 'sometimes|integer|exists:projects,id',
            'project_name' => 'sometimes|string|max:255',
            'item_id' => 'sometimes|integer|exists:project_items,id',
            'item_name' => 'sometimes|string|max:255',
            'step' => 'sometimes|string|max:255',
            'machine_id' => 'sometimes|integer|exists:machines,id',
            'target_qty' => 'sometimes|integer|min:1',
            'completed_qty' => 'sometimes|integer|min:0',
            'defect_qty' => 'sometimes|integer|min:0',
            'shift' => 'sometimes|nullable|string|max:50',
            'status' => 'sometimes|string|in:PENDING,IN_PROGRESS,PAUSED,COMPLETED,DOWNTIME',
            'downtime_start' => 'nullable|date',
            'total_downtime_minutes' => 'nullable|integer|min:0',
        ]);

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully.',
            'data' => $task->load(['project', 'projectItem', 'machine']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found.',
            ], 404);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully.',
        ]);
    }

    /**
     * Update the status of a task.
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found.',
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:PENDING,IN_PROGRESS,PAUSED,COMPLETED,DOWNTIME',
        ]);

        $task->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully.',
            'data' => $task->refresh(),
        ]);
    }

    /**
     * Update quantities of a task (completed and defect).
     */
    public function updateQuantities(Request $request, string $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found.',
            ], 404);
        }

        $validated = $request->validate([
            'completed_qty' => 'required|integer|min:0',
            'defect_qty' => 'required|integer|min:0',
        ]);

        // Ensure completed quantity doesn't exceed target
        if ($validated['completed_qty'] > $task->target_qty) {
            return response()->json([
                'success' => false,
                'message' => 'Completed quantity cannot exceed target quantity.',
            ], 400);
        }

        // Update completed and defect quantities
        $task->update([
            'completed_qty' => $validated['completed_qty'],
            'defect_qty' => $validated['defect_qty'],
        ]);

        // Optionally update status based on quantities
        if ($validated['completed_qty'] >= $task->target_qty) {
            $task->update(['status' => 'COMPLETED']);
        } else {
            if ($task->status !== 'PENDING') {
                $task->update(['status' => 'IN_PROGRESS']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Task quantities updated successfully.',
            'data' => $task->refresh(),
        ]);
    }

    /**
     * Start downtime for a task.
     */
    public function startDowntime(Request $request, string $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found.',
            ], 404);
        }

        $task->update([
            'status' => 'DOWNTIME',
            'downtime_start' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Downtime started for the task.',
            'data' => $task->refresh(),
        ]);
    }

    /**
     * End downtime for a task.
     */
    public function endDowntime(string $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found.',
            ], 404);
        }

        if ($task->downtime_start) {
            $downtimeMinutes = $task->downtime_start->diffInRealMinutes(now());
            $newTotalDowntime = $task->total_downtime_minutes + $downtimeMinutes;

            $task->update([
                'status' => 'IN_PROGRESS', // or previous status
                'downtime_start' => null,
                'total_downtime_minutes' => $newTotalDowntime,
            ]);
        } else {
            $task->update([
                'status' => 'IN_PROGRESS',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Downtime ended for the task.',
            'data' => $task->refresh(),
        ]);
    }

    /**
     * Get tasks statistics.
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total' => Task::count(),
            'pending' => Task::where('status', 'PENDING')->count(),
            'in_progress' => Task::where('status', 'IN_PROGRESS')->count(),
            'paused' => Task::where('status', 'PAUSED')->count(),
            'downtime' => Task::where('status', 'DOWNTIME')->count(),
            'completed' => Task::where('status', 'COMPLETED')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
