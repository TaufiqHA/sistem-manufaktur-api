<?php

namespace App\Http\Controllers;

use App\Models\Rfq;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 10);
        $purchaseOrders = PurchaseOrder::with(['rfq', 'supplier'])->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $purchaseOrders
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:purchase_orders,code',
            'rfq_id' => 'nullable|exists:rfqs,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'grand_total' => 'required|numeric|min:0',
            'status' => 'required|in:OPEN,RECEIVED',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $purchaseOrder = PurchaseOrder::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order created successfully.',
            'data' => $purchaseOrder
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $purchaseOrder->load(['rfq', 'supplier']);

        return response()->json([
            'success' => true,
            'data' => $purchaseOrder
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:purchase_orders,code,' . $purchaseOrder->id,
            'rfq_id' => 'nullable|exists:rfqs,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'grand_total' => 'required|numeric|min:0',
            'status' => 'required|in:OPEN,RECEIVED',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $purchaseOrder->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order updated successfully.',
            'data' => $purchaseOrder
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $purchaseOrder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order deleted successfully.'
        ]);
    }
}