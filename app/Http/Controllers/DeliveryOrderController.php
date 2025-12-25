<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DeliveryOrderController extends Controller
{
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
        $validatedData = $request->validate([
            'code' => 'required|string|unique:delivery_orders,code',
            'date' => 'required|date',
            'customer' => 'required|string',
            'address' => 'required|string',
            'driver_name' => 'required|string',
            'vehicle_plate' => 'required|string',
        ]);

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
        $validatedData = $request->validate([
            'code' => 'sometimes|required|string|unique:delivery_orders,code,' . $deliveryOrder->id,
            'date' => 'sometimes|required|date',
            'customer' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
            'driver_name' => 'sometimes|required|string',
            'vehicle_plate' => 'sometimes|required|string',
        ]);

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
