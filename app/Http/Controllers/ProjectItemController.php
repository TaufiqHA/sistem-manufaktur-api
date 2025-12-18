<?php

namespace App\Http\Controllers;

use App\Models\ProjectItem;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse|View
    {
        $projectItems = ProjectItem::with('project')->paginate(10);

        if ($request->expectsJson()) {
            return response()->json($projectItems);
        }

        return view('project-items.index', compact('projectItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $projects = Project::all();
        
        return view('project-items.create', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|string|exists:projects,id',
            'name' => 'required|string|max:255',
            'dimensions' => 'required|string|max:255',
            'thickness' => 'required|string|max:255',
            'qty_set' => 'required|integer|min:0',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'is_bom_locked' => 'boolean',
            'is_workflow_locked' => 'boolean',
            'workflow' => 'nullable|array',
        ]);

        $projectItem = ProjectItem::create($validated);

        if ($request->expectsJson()) {
            return response()->json($projectItem, 201);
        }

        return redirect()->route('project-items.show', $projectItem->id)
                         ->with('success', 'Project Item created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProjectItem $projectItem): JsonResponse|View
    {
        $projectItem->load('project');

        if (request()->expectsJson()) {
            return response()->json($projectItem);
        }

        return view('project-items.show', compact('projectItem'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProjectItem $projectItem): View
    {
        $projects = Project::all();
        
        return view('project-items.edit', compact('projectItem', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectItem $projectItem): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'sometimes|required|string|exists:projects,id',
            'name' => 'sometimes|required|string|max:255',
            'dimensions' => 'sometimes|required|string|max:255',
            'thickness' => 'sometimes|required|string|max:255',
            'qty_set' => 'sometimes|required|integer|min:0',
            'quantity' => 'sometimes|required|integer|min:0',
            'unit' => 'sometimes|required|string|max:50',
            'is_bom_locked' => 'sometimes|boolean',
            'is_workflow_locked' => 'sometimes|boolean',
            'workflow' => 'sometimes|nullable|array',
        ]);

        $projectItem->update($validated);

        if ($request->expectsJson()) {
            return response()->json($projectItem);
        }

        return redirect()->route('project-items.show', $projectItem->id)
                         ->with('success', 'Project Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectItem $projectItem): JsonResponse|RedirectResponse
    {
        $projectItem->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Project Item deleted successfully']);
        }

        return redirect()->route('project-items.index')
                         ->with('success', 'Project Item deleted successfully.');
    }
}