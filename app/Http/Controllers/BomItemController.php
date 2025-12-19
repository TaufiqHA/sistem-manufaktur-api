<?php

namespace App\Http\Controllers;

use App\Models\BomItem;
use App\Models\ProjectItem;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BomItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $bomItems = BomItem::with(['item', 'material'])->paginate(10);

        return response()->json($bomItems);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:project_items,id',
            'material_id' => 'required|exists:materials,id',
            'quantity_per_unit' => 'required|numeric|min:1',
            'total_required' => 'required|integer|min:1',
            'allocated' => 'required|integer|min:0',
            'realized' => 'required|integer|min:0',
        ]);

        $bomItem = BomItem::create($validated);

        return response()->json($bomItem, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(BomItem $bomItem, Request $request): JsonResponse
    {
        $bomItem->load(['item', 'material']);

        return response()->json($bomItem);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BomItem $bomItem): JsonResponse
    {
        $validated = $request->validate([
            'item_id' => 'sometimes|required|exists:project_items,id',
            'material_id' => 'sometimes|required|exists:materials,id',
            'quantity_per_unit' => 'sometimes|required|numeric|min:1',
            'total_required' => 'sometimes|required|integer|min:1',
            'allocated' => 'sometimes|required|integer|min:0',
            'realized' => 'sometimes|required|integer|min:0',
        ]);

        $bomItem->update($validated);

        return response()->json($bomItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BomItem $bomItem, Request $request): JsonResponse
    {
        $bomItem->delete();

        return response()->json(null, 204);
    }

    /**
     * Get BOM items by project item
     */
    public function getByProjectItem(string $projectItemId): JsonResponse
    {
        $bomItems = BomItem::where('item_id', $projectItemId)
            ->with(['item', 'material'])
            ->get();

        return response()->json($bomItems);
    }
}