<?php

namespace App\Http\Controllers;

use App\Models\BomItem;
use App\Models\ProjectItem;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BomItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse|View
    {
        $bomItems = BomItem::with(['item', 'material'])->paginate(10);

        if ($request->expectsJson()) {
            return response()->json($bomItems);
        }

        return view('bom-items.index', compact('bomItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $projectItems = ProjectItem::all();
        $materials = Material::all();
        
        return view('bom-items.create', compact('projectItems', 'materials'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:project_items,id',
            'material_id' => 'required|exists:materials,id',
            'quantity_per_unit' => 'required|integer|min:1',
            'total_required' => 'required|integer|min:1',
            'allocated' => 'required|integer|min:0',
            'realized' => 'required|integer|min:0',
        ]);

        $bomItem = BomItem::create($validated);

        if ($request->expectsJson()) {
            return response()->json($bomItem, 201);
        }

        return redirect()->route('bom-items.index')->with('success', 'BOM Item created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BomItem $bomItem, Request $request): JsonResponse|View
    {
        $bomItem->load(['item', 'material']);

        if ($request->expectsJson()) {
            return response()->json($bomItem);
        }

        return view('bom-items.show', compact('bomItem'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BomItem $bomItem): View
    {
        $projectItems = ProjectItem::all();
        $materials = Material::all();
        
        return view('bom-items.edit', compact('bomItem', 'projectItems', 'materials'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BomItem $bomItem): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'item_id' => 'sometimes|required|exists:project_items,id',
            'material_id' => 'sometimes|required|exists:materials,id',
            'quantity_per_unit' => 'sometimes|required|integer|min:1',
            'total_required' => 'sometimes|required|integer|min:1',
            'allocated' => 'sometimes|required|integer|min:0',
            'realized' => 'sometimes|required|integer|min:0',
        ]);

        $bomItem->update($validated);

        if ($request->expectsJson()) {
            return response()->json($bomItem);
        }

        return redirect()->route('bom-items.index')->with('success', 'BOM Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BomItem $bomItem, Request $request): JsonResponse|RedirectResponse
    {
        $bomItem->delete();

        if ($request->expectsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('bom-items.index')->with('success', 'BOM Item deleted successfully.');
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