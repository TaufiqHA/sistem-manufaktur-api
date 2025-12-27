<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DeliveryOrderController extends Controller
{
    /**
     * Get the validation rules for delivery orders on creation.
     */
    protected function getCreateValidationRules(): array
    {
        return [
            'code' => 'required|string|unique:delivery_orders,code',
            'date' => 'required|date',
            'customer' => 'required|string',
            'address' => 'required|string',
            'driver_name' => 'required|string',
            'vehicle_plate' => 'required|string',
            'status' => 'sometimes|string|in:draft,validated,send,archived',
            'note' => 'sometimes|nullable|string',
        ];
    }

    /**
     * Get the validation rules for delivery orders on update.
     */
    protected function getUpdateValidationRules(DeliveryOrder $deliveryOrder): array
    {
        return [
            'code' => 'sometimes|required|string|unique:delivery_orders,code,' . $deliveryOrder->id,
            'date' => 'sometimes|required|date',
            'customer' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
            'driver_name' => 'sometimes|required|string',
            'vehicle_plate' => 'sometimes|required|string',
            'status' => 'sometimes|string|in:draft,validated,send,archived',
            'note' => 'sometimes|nullable|string',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $deliveryOrders = DeliveryOrder::all();
        return response()->json($deliveryOrders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = \Validator::make($request->all(), $this->getCreateValidationRules());

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedData = $validator->validated();

        $deliveryOrder = DeliveryOrder::create($validatedData);

        return response()->json($deliveryOrder, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(DeliveryOrder $deliveryOrder): JsonResponse
    {
        return response()->json($deliveryOrder);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeliveryOrder $deliveryOrder): JsonResponse
    {
        $validator = \Validator::make($request->all(), $this->getUpdateValidationRules($deliveryOrder));

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedData = $validator->validated();

        $deliveryOrder->update($validatedData);

        return response()->json($deliveryOrder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeliveryOrder $deliveryOrder): JsonResponse
    {
        $deliveryOrder->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
