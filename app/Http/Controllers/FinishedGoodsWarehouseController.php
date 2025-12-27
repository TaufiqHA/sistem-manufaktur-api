<?php

namespace App\Http\Controllers;

use App\Models\FinishedGoodsWarehouse;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class FinishedGoodsWarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $finishedGoodsWarehouses = FinishedGoodsWarehouse::with('project')->paginate(10);

        return response()->json($finishedGoodsWarehouses);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $projects = Project::all();
        
        return view('finished_goods_warehouses.create', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'item_name' => 'required|string|max:255',
            'total_produced' => 'required|integer|min:0',
            'shipped_qty' => 'required|integer|min:0',
            'available_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'status' => 'nullable|in:not validate,validated',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // Ensure available_stock doesn't exceed total_produced
        if ($validated['available_stock'] > $validated['total_produced']) {
            return response()->json([
                'message' => 'Available stock cannot exceed total produced quantity'
            ], 422);
        }

        $finishedGoodsWarehouse = FinishedGoodsWarehouse::create($validated);

        return response()->json($finishedGoodsWarehouse, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(FinishedGoodsWarehouse $finishedGoodsWarehouse): JsonResponse
    {
        return response()->json($finishedGoodsWarehouse->load('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FinishedGoodsWarehouse $finishedGoodsWarehouse): View
    {
        $projects = Project::all();
        
        return view('finished_goods_warehouses.edit', compact('finishedGoodsWarehouse', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FinishedGoodsWarehouse $finishedGoodsWarehouse): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'item_name' => 'required|string|max:255',
            'total_produced' => 'required|integer|min:0',
            'shipped_qty' => 'required|integer|min:0',
            'available_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'status' => 'nullable|in:not validate,validated',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // Ensure available_stock doesn't exceed total_produced
        if ($validated['available_stock'] > $validated['total_produced']) {
            return response()->json([
                'message' => 'Available stock cannot exceed total produced quantity'
            ], 422);
        }

        $finishedGoodsWarehouse->update($validated);

        return response()->json($finishedGoodsWarehouse);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FinishedGoodsWarehouse $finishedGoodsWarehouse): JsonResponse
    {
        $finishedGoodsWarehouse->delete();

        return response()->json([
            'message' => 'Finished Goods Warehouse deleted successfully.'
        ]);
    }

    /**
     * Display a listing of the resource as JSON.
     */
    public function apiIndex(): JsonResponse
    {
        $finishedGoodsWarehouses = FinishedGoodsWarehouse::with('project')->get();
        
        return response()->json($finishedGoodsWarehouses);
    }

    /**
     * Get the specified resource as JSON.
     */
    public function apiShow(FinishedGoodsWarehouse $finishedGoodsWarehouse): JsonResponse
    {
        return response()->json($finishedGoodsWarehouse->load('project'));
    }
}