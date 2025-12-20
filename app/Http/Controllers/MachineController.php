<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MachineController extends Controller
{
    /**
     * Display a listing of the machines.
     */
    public function index(): JsonResponse
    {
        $machines = Machine::all();

        return response()->json([
            'success' => true,
            'data' => $machines,
        ]);
    }

    /**
     * Store a newly created machine in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:machines,code|max:255',
            'name' => 'required|string|max:255',
            'type' => ['required', 'string', Rule::in(['POTONG', 'PLONG', 'PRESS', 'LASPEN', 'LAS_MIG', 'PHOSPATHING', 'POWDER', 'PACKING'])],
            'capacity_per_hour' => 'required|integer|min:0',
            'status' => ['required', 'string', Rule::in(['IDLE', 'RUNNING', 'MAINTENANCE', 'OFFLINE', 'DOWNTIME'])],
            'personnel' => 'required|array',
            'is_maintenance' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        $machine = Machine::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Machine created successfully',
            'data' => $machine,
        ], 201);
    }

    /**
     * Display the specified machine.
     */
    public function show(Machine $machine): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $machine,
        ]);
    }

    /**
     * Update the specified machine in storage.
     */
    public function update(Request $request, Machine $machine): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => ['string', 'max:255', Rule::unique('machines', 'code')->ignore($machine->id)],
            'name' => 'string|max:255',
            'type' => ['string', Rule::in(['POTONG', 'PLONG', 'PRESS', 'LAS', 'WT', 'POWDER', 'QC'])],
            'capacity_per_hour' => 'integer|min:0',
            'status' => ['string', Rule::in(['IDLE', 'RUNNING', 'MAINTENANCE', 'OFFLINE', 'DOWNTIME'])],
            'personnel' => 'array',
            'is_maintenance' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        $machine->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Machine updated successfully',
            'data' => $machine,
        ]);
    }

    /**
     * Remove the specified machine from storage.
     */
    public function destroy(Machine $machine): JsonResponse
    {
        $machine->delete();

        return response()->json([
            'success' => true,
            'message' => 'Machine deleted successfully',
        ]);
    }

    /**
     * Toggle maintenance status for the specified machine.
     */
    public function toggleMaintenance(Machine $machine): JsonResponse
    {
        $machine->update([
            'is_maintenance' => !$machine->is_maintenance,
            'status' => !$machine->is_maintenance ? 'MAINTENANCE' : 'IDLE'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance status updated successfully',
            'data' => $machine,
        ]);
    }

    /**
     * Get machines by type.
     */
    public function getByType(string $type): JsonResponse
    {
        $validTypes = ['POTONG', 'PLONG', 'PRESS', 'LAS', 'WT', 'POWDER', 'QC'];

        if (!in_array(strtoupper($type), $validTypes)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid machine type',
            ], 400);
        }

        $machines = Machine::where('type', strtoupper($type))->get();

        return response()->json([
            'success' => true,
            'data' => $machines,
        ]);
    }

    /**
     * Get machines by status.
     */
    public function getByStatus(string $status): JsonResponse
    {
        $validStatuses = ['IDLE', 'RUNNING', 'MAINTENANCE', 'OFFLINE', 'DOWNTIME'];

        if (!in_array(strtoupper($status), $validStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid machine status',
            ], 400);
        }

        $machines = Machine::where('status', strtoupper($status))->get();

        return response()->json([
            'success' => true,
            'data' => $machines,
        ]);
    }
}
