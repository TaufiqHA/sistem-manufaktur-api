<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Material::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('code', 'like', "%{$searchTerm}%")
                  ->orWhere('name', 'like', "%{$searchTerm}%")
                  ->orWhere('category', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by category
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }

        // Filter by low stock
        if ($request->has('low_stock') && $request->low_stock == 'true') {
            $query->whereRaw('current_stock < safety_stock');
        }

        $materials = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $materials,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|unique:materials,code',
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'current_stock' => 'required|integer|min:0',
            'safety_stock' => 'required|integer|min:0',
            'price_per_unit' => 'required|numeric|min:0',
            'category' => [
                'required',
                Rule::in(['RAW', 'FINISHING', 'HARDWARE'])
            ],
        ]);

        $material = Material::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Material created successfully',
            'data' => $material,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Material $material)
    {
        return response()->json([
            'success' => true,
            'data' => $material,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Material $material)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|unique:materials,code,' . $material->id,
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'current_stock' => 'required|integer|min:0',
            'safety_stock' => 'required|integer|min:0',
            'price_per_unit' => 'required|numeric|min:0',
            'category' => [
                'required',
                Rule::in(['RAW', 'FINISHING', 'HARDWARE'])
            ],
        ]);

        $material->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Material updated successfully',
            'data' => $material,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Material $material)
    {
        $material->delete();

        return response()->json([
            'success' => true,
            'message' => 'Material deleted successfully',
        ]);
    }

    /**
     * Update material stock
     */
    public function updateStock(Request $request, Material $material)
    {
        $request->validate([
            'stock_change' => 'required|integer',
            'operation' => 'required|in:add,reduce'
        ]);

        $stockChange = $request->stock_change;
        $operation = $request->operation;

        if ($operation === 'add') {
            $material->increment('current_stock', abs($stockChange));
        } elseif ($operation === 'reduce') {
            if ($material->current_stock < abs($stockChange)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock to reduce',
                ], 400);
            }
            $material->decrement('current_stock', abs($stockChange));
        }

        $material->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully',
            'data' => $material,
        ]);
    }

    /**
     * Get materials with low stock
     */
    public function lowStock()
    {
        $materials = Material::whereColumn('current_stock', '<', 'safety_stock')
                            ->orderBy('current_stock', 'asc')
                            ->get();

        return response()->json([
            'success' => true,
            'data' => $materials,
        ]);
    }
}