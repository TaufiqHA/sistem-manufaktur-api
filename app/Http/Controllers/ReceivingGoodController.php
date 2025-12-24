<?php

namespace App\Http\Controllers;

use App\Models\ReceivingGood;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ReceivingGoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 10);
        $receivingGoods = ReceivingGood::with(['purchaseOrder', 'supplier'])->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $receivingGoods
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:receiving_goods,code',
            'po_id' => 'nullable|exists:purchase_orders,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $receivingGood = ReceivingGood::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Receiving Good created successfully.',
            'data' => $receivingGood->load(['purchaseOrder', 'supplier'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ReceivingGood $receivingGood): JsonResponse
    {
        $receivingGood->load(['purchaseOrder', 'supplier']);

        return response()->json([
            'success' => true,
            'data' => $receivingGood
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReceivingGood $receivingGood): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:receiving_goods,code,' . $receivingGood->id,
            'po_id' => 'nullable|exists:purchase_orders,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $receivingGood->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Receiving Good updated successfully.',
            'data' => $receivingGood->load(['purchaseOrder', 'supplier'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReceivingGood $receivingGood): JsonResponse
    {
        $receivingGood->delete();

        return response()->json([
            'success' => true,
            'message' => 'Receiving Good deleted successfully.'
        ]);
    }
}