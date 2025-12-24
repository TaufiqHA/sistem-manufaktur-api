<?php

namespace App\Http\Controllers;

use App\Models\ReceivingGood;
use App\Models\ReceivingItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ReceivingItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 10);
        $receivingItems = ReceivingItem::with(['receiving', 'material'])->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $receivingItems
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'receiving_id' => 'nullable|exists:receiving_goods,id',
            'material_id' => 'nullable|exists:materials,id',
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $receivingItem = ReceivingItem::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Receiving Item created successfully.',
            'data' => $receivingItem->load(['receiving', 'material'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ReceivingItem $receivingItem): JsonResponse
    {
        $receivingItem->load(['receiving', 'material']);

        return response()->json([
            'success' => true,
            'data' => $receivingItem
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReceivingItem $receivingItem): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'receiving_id' => 'nullable|exists:receiving_goods,id',
            'material_id' => 'nullable|exists:materials,id',
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $receivingItem->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Receiving Item updated successfully.',
            'data' => $receivingItem->load(['receiving', 'material'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReceivingItem $receivingItem): JsonResponse
    {
        $receivingItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Receiving Item deleted successfully.'
        ]);
    }

    /**
     * Get receiving items by receiving ID.
     */
    public function getByReceiving(ReceivingGood $receiving): JsonResponse
    {
        $receivingItems = $receiving->items()->with(['material'])->get();

        return response()->json([
            'success' => true,
            'data' => $receivingItems
        ]);
    }
}
