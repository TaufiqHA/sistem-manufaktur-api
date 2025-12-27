<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrderItem;
use App\Models\DeliveryOrder;
use App\Models\FinishedGoodsWarehouse;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DeliveryOrderItemController extends Controller
{
    /**
     * Get the validation rules for delivery order items.
     */
    protected function getValidationRules(): array
    {
        return [
            'delivery_order_id' => 'required|exists:delivery_orders,id',
            'warehouse_id' => 'required|exists:finished_goods_warehouses,id',
            'project_id' => 'required|exists:projects,id',
            'project_name' => 'required|string|max:255',
            'item_name' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'unit' => 'required|string|max:50',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $deliveryOrderItems = DeliveryOrderItem::with(['deliveryOrder', 'warehouse', 'project'])->paginate(10);

        return response()->json($deliveryOrderItems);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $deliveryOrders = DeliveryOrder::all();
        $warehouses = FinishedGoodsWarehouse::all();
        $projects = Project::all();

        return view('delivery-order-items.create', compact('deliveryOrders', 'warehouses', 'projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate($this->getValidationRules());

        $deliveryOrderItem = DeliveryOrderItem::create($validatedData);

        return response()->json($deliveryOrderItem, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(DeliveryOrderItem $deliveryOrderItem): JsonResponse
    {
        $deliveryOrderItem->load(['deliveryOrder', 'warehouse', 'project']);

        return response()->json($deliveryOrderItem);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DeliveryOrderItem $deliveryOrderItem): View
    {
        $deliveryOrders = DeliveryOrder::all();
        $warehouses = FinishedGoodsWarehouse::all();
        $projects = Project::all();

        return view('delivery-order-items.edit', compact('deliveryOrderItem', 'deliveryOrders', 'warehouses', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeliveryOrderItem $deliveryOrderItem): JsonResponse
    {
        $validatedData = $request->validate($this->getValidationRules());

        $deliveryOrderItem->update($validatedData);

        return response()->json($deliveryOrderItem, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeliveryOrderItem $deliveryOrderItem): JsonResponse
    {
        $deliveryOrderItem->delete();

        return response()->json(null, 204);
    }
}
