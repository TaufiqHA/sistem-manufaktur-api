<?php

namespace App\Http\Controllers;

use App\Models\ProductionLog;
use App\Models\Task;
use App\Models\Machine;
use App\Models\ProjectItem;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProductionLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ProductionLog::with(['task', 'machine', 'item', 'project']);

        // Filter by project_id if provided
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by machine_id if provided
        if ($request->has('machine_id')) {
            $query->where('machine_id', $request->machine_id);
        }

        // Filter by date range if provided
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('logged_at', [$request->from_date, $request->to_date]);
        } elseif ($request->has('date')) {
            $query->whereDate('logged_at', $request->date);
        }

        // Filter by shift if provided
        if ($request->has('shift')) {
            $query->where('shift', $request->shift);
        }

        // Filter by task_id if provided
        if ($request->has('task_id')) {
            $query->where('task_id', $request->task_id);
        }

        // Filter by type if provided (OUTPUT, DOWNTIME_START, DOWNTIME_END)
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $productionLogs = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $productionLogs
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required|integer|exists:tasks,id',
            'machine_id' => 'required|integer|exists:machines,id',
            'item_id' => 'required|integer|exists:project_items,id',
            'project_id' => 'required|integer|exists:projects,id',
            'step' => 'required|string',
            'shift' => 'required|string',
            'good_qty' => 'required|integer|min:0',
            'defect_qty' => 'required|integer|min:0',
            'operator' => 'required|string',
            'logged_at' => 'required|date',
            'type' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $productionLog = ProductionLog::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Production log created successfully',
                'data' => $productionLog->load(['task', 'machine', 'item', 'project'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create production log',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $productionLog = ProductionLog::with(['task', 'machine', 'item', 'project'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $productionLog
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Production log not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $productionLog = ProductionLog::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'task_id' => 'sometimes|required|integer|exists:tasks,id',
                'machine_id' => 'sometimes|required|integer|exists:machines,id',
                'item_id' => 'sometimes|required|integer|exists:project_items,id',
                'project_id' => 'sometimes|required|integer|exists:projects,id',
                'step' => 'sometimes|required|string',
                'shift' => 'sometimes|required|string',
                'good_qty' => 'sometimes|required|integer|min:0',
                'defect_qty' => 'sometimes|required|integer|min:0',
                'operator' => 'sometimes|required|string',
                'logged_at' => 'sometimes|required|date',
                'type' => 'sometimes|required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $productionLog->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Production log updated successfully',
                'data' => $productionLog->load(['task', 'machine', 'item', 'project'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update production log',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $productionLog = ProductionLog::findOrFail($id);
            $productionLog->delete();

            return response()->json([
                'success' => true,
                'message' => 'Production log deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete production log',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get production logs by project.
     */
    public function getByProject(string $projectId, Request $request): JsonResponse
    {
        $query = ProductionLog::with(['task', 'machine', 'item', 'project'])
            ->where('project_id', $projectId);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('shift')) {
            $query->where('shift', $request->shift);
        }

        $productionLogs = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $productionLogs
        ]);
    }

    /**
     * Get production logs by machine.
     */
    public function getByMachine(string $machineId, Request $request): JsonResponse
    {
        $query = ProductionLog::with(['task', 'machine', 'item', 'project'])
            ->where('machine_id', $machineId);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('shift')) {
            $query->where('shift', $request->shift);
        }

        $productionLogs = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $productionLogs
        ]);
    }

    /**
     * Get summary of production statistics.
     */
    public function getProductionSummary(Request $request): JsonResponse
    {
        $query = ProductionLog::selectRaw(
            'SUM(good_qty) as total_good_qty, 
             SUM(defect_qty) as total_defect_qty, 
             COUNT(*) as total_logs,
             AVG(good_qty) as avg_good_qty,
             AVG(defect_qty) as avg_defect_qty'
        );

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('machine_id')) {
            $query->where('machine_id', $request->machine_id);
        }

        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('logged_at', [$request->from_date, $request->to_date]);
        }

        $summary = $query->first();

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }
}