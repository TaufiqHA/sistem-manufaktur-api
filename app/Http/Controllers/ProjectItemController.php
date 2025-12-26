<?php

namespace App\Http\Controllers;

use App\Models\ProjectItem;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProjectItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $projectItems = ProjectItem::with('project')->paginate(10);

        return response()->json($projectItems);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|integer|exists:projects,id',
            'name' => 'required|string|max:255',
            'dimensions' => 'required|string|max:255',
            'thickness' => 'required|string|max:255',
            'qty_set' => 'required|integer|min:0',
            'qty_per_product' => 'required|integer|min:0',
            'total_required_qty' => 'required|integer|min:0',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'is_bom_locked' => 'boolean',
            'is_workflow_locked' => 'boolean',
            'workflow' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $projectItem = ProjectItem::create($validated);

        return response()->json($projectItem, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProjectItem $projectItem): JsonResponse
    {
        $projectItem->load('project');

        return response()->json($projectItem);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectItem $projectItem): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'sometimes|required|string|exists:projects,id',
            'name' => 'sometimes|required|string|max:255',
            'dimensions' => 'sometimes|required|string|max:255',
            'thickness' => 'sometimes|required|string|max:255',
            'qty_set' => 'sometimes|required|integer|min:0',
            'qty_per_product' => 'sometimes|required|integer|min:0',
            'total_required_qty' => 'sometimes|required|integer|min:0',
            'quantity' => 'sometimes|required|integer|min:0',
            'unit' => 'sometimes|required|string|max:50',
            'is_bom_locked' => 'sometimes|boolean',
            'is_workflow_locked' => 'sometimes|boolean',
            'workflow' => 'sometimes|nullable|array',
        ]);

        $projectItem->update($validated);

        return response()->json($projectItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectItem $projectItem): JsonResponse
    {
        $projectItem->delete();

        return response()->json(['message' => 'Project Item deleted successfully']);
    }

    /**
     * Get items based on project_id.
     */
    public function getByProjectId(string $projectId): JsonResponse
    {
        $projectItems = ProjectItem::where('project_id', $projectId)->get();

        return response()->json([
            'data' => $projectItems
        ]);
    }
}