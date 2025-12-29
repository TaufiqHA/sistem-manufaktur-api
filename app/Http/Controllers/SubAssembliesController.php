<?php

namespace App\Http\Controllers;

use App\Models\SubAssemblies;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class SubAssembliesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $subAssemblies = subAssemblies::with('material')->paginate(10);
        return response()->json($subAssemblies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'qty_per_parent' => 'required|integer|min:0',
            'material_id' => 'required|exists:materials,id',
            'processes' => 'required|json',
            'total_needed' => 'required|integer|min:0',
            'completed_qty' => 'nullable|integer|min:0',
            'total_produced' => 'nullable|integer|min:0',
            'consumed_qty' => 'nullable|integer|min:0',
            'step_stats' => 'nullable|json',
            'is_locked' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $subAssembly = subAssemblies::create($validator->validated());

        return response()->json($subAssembly, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(subAssemblies $subAssembly): JsonResponse
    {
        return response()->json($subAssembly->load('material'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, subAssemblies $subAssembly): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'sometimes|string|max:255',
            'name' => 'sometimes|string|max:255',
            'qty_per_parent' => 'sometimes|integer|min:0',
            'material_id' => 'sometimes|exists:materials,id',
            'processes' => 'sometimes|json',
            'total_needed' => 'sometimes|integer|min:0',
            'completed_qty' => 'sometimes|integer|min:0',
            'total_produced' => 'sometimes|integer|min:0',
            'consumed_qty' => 'sometimes|integer|min:0',
            'step_stats' => 'sometimes|json',
            'is_locked' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $subAssembly->update($validator->validated());

        return response()->json($subAssembly);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(subAssemblies $subAssembly): JsonResponse
    {
        $subAssembly->delete();

        return response()->json(null, 204);
    }
}
