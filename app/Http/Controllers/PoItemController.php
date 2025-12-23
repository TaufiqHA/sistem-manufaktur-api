<?php

namespace App\Http\Controllers;

use App\Models\PoItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PoItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = PoItem::query();

        // Eager load relationships
        $query->with(['purchaseOrder', 'material']);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhereHas('purchaseOrder', function ($subQuery) use ($searchTerm) {
                      $subQuery->where('code', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('material', function ($subQuery) use ($searchTerm) {
                      $subQuery->where('name', 'like', "%{$searchTerm}%")
                               ->orWhere('code', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Filter by PO ID
        if ($request->has('po_id') && !empty($request->po_id)) {
            $query->where('po_id', $request->po_id);
        }

        // Filter by material ID
        if ($request->has('material_id') && !empty($request->material_id)) {
            $query->where('material_id', $request->material_id);
        }

        // Filter by quantity range
        if ($request->has('min_qty') && !empty($request->min_qty)) {
            $query->where('qty', '>=', $request->min_qty);
        }
        if ($request->has('max_qty') && !empty($request->max_qty)) {
            $query->where('qty', '<=', $request->max_qty);
        }

        // Filter by price range
        if ($request->has('min_price') && !empty($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && !empty($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }

        $perPage = $request->query('per_page', 10);
        $poItems = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $poItems
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'po_id' => 'required|exists:purchase_orders,id',
            'material_id' => 'required|exists:materials,id',
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $poItem = PoItem::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'PO Item created successfully.',
            'data' => $poItem
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PoItem $poItem): JsonResponse
    {
        $poItem->load(['purchaseOrder', 'material']);

        return response()->json([
            'success' => true,
            'data' => $poItem
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PoItem $poItem): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'po_id' => 'required|exists:purchase_orders,id',
            'material_id' => 'required|exists:materials,id',
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $poItem->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'PO Item updated successfully.',
            'data' => $poItem
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PoItem $poItem): JsonResponse
    {
        $poItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'PO Item deleted successfully.'
        ]);
    }
}
