<?php

namespace App\Http\Controllers;

use App\Models\RfqItem;
use App\Models\Rfq;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class RfqItemController extends Controller
{
    /**
     * Display a listing of the RFQ items.
     */
    public function index(): JsonResponse
    {
        $rfqItems = RfqItem::with(['rfq', 'material'])->paginate();

        return response()->json($rfqItems);
    }

    /**
     * Store a newly created RFQ item in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'rfq_id' => 'required|exists:rfqs,id',
            'material_id' => 'required|exists:materials,id',
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedData = $validator->validated();

        $rfqItem = RfqItem::create($validatedData);

        return response()->json([
            'message' => 'RFQ item created successfully',
            'data' => $rfqItem->load(['rfq', 'material'])
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified RFQ item.
     */
    public function show(RfqItem $rfqItem): JsonResponse
    {
        $rfqItem->load(['rfq', 'material']);

        return response()->json([
            'data' => $rfqItem
        ]);
    }

    /**
     * Update the specified RFQ item in storage.
     */
    public function update(Request $request, RfqItem $rfqItem): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'rfq_id' => 'sometimes|required|exists:rfqs,id',
            'material_id' => 'sometimes|required|exists:materials,id',
            'name' => 'sometimes|required|string|max:255',
            'qty' => 'sometimes|required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedData = $validator->validated();

        $rfqItem->update($validatedData);

        return response()->json([
            'message' => 'RFQ item updated successfully',
            'data' => $rfqItem->load(['rfq', 'material'])
        ]);
    }

    /**
     * Remove the specified RFQ item from storage.
     */
    public function destroy(RfqItem $rfqItem): JsonResponse
    {
        $rfqItem->delete();

        return response()->json([
            'message' => 'RFQ item deleted successfully'
        ], Response::HTTP_NO_CONTENT);
    }

    /**
     * Get all RFQ items for a specific RFQ.
     */
    public function getByRfq(int $rfqId): JsonResponse
    {
        $rfq = Rfq::findOrFail($rfqId);
        $rfqItems = RfqItem::where('rfq_id', $rfqId)->with(['rfq', 'material'])->get();

        return response()->json([
            'data' => $rfqItems
        ]);
    }
}
