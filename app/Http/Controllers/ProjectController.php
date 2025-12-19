<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $projects = Project::orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $projects
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:255|unique:projects,code',
            'name' => 'required|string|max:255',
            'customer' => 'required|string|max:255',
            'start_date' => 'required|date',
            'deadline' => 'required|date',
            'status' => 'required|string|in:PLANNED,IN_PROGRESS,COMPLETED,ON_HOLD,CANCELLED',
            'progress' => 'required|integer|min:0|max:100',
            'qty_per_unit' => 'required|integer|min:0',
            'procurement_qty' => 'required|integer|min:0',
            'total_qty' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'is_locked' => 'boolean',
        ]);

        // Additional validation for date relationship
        if (strtotime($validatedData['start_date']) > strtotime($validatedData['deadline'])) {
            return response()->json([
                'message' => 'The start date must be before or equal to the deadline.',
                'errors' => [
                    'start_date' => ['The start date must be before or equal to the deadline.'],
                    'deadline' => ['The deadline must be after or equal to the start date.']
                ]
            ], 422);
        }

        $validatedData['is_locked'] = $validatedData['is_locked'] ?? false;

        $project = Project::create($validatedData);

        return response()->json([
            'message' => 'Project created successfully.',
            'data' => $project
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, Request $request): JsonResponse
    {
        return response()->json([
            'data' => $project
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project): JsonResponse
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:255|unique:projects,code,' . $project->id,
            'name' => 'required|string|max:255',
            'customer' => 'required|string|max:255',
            'start_date' => 'required|date',
            'deadline' => 'required|date',
            'status' => 'required|string|in:PLANNED,IN_PROGRESS,COMPLETED,ON_HOLD,CANCELLED',
            'progress' => 'required|integer|min:0|max:100',
            'qty_per_unit' => 'required|integer|min:0',
            'procurement_qty' => 'required|integer|min:0',
            'total_qty' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'is_locked' => 'boolean',
        ]);

        // Additional validation for date relationship
        if (strtotime($validatedData['start_date']) > strtotime($validatedData['deadline'])) {
            return response()->json([
                'message' => 'The start date must be before or equal to the deadline.',
                'errors' => [
                    'start_date' => ['The start date must be before or equal to the deadline.'],
                    'deadline' => ['The deadline must be after or equal to the start date.']
                ]
            ], 422);
        }

        $project->update($validatedData);

        return response()->json([
            'message' => 'Project updated successfully.',
            'data' => $project
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Project $project): JsonResponse
    {
        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully.'
        ]);
    }

    /**
     * Search projects via AJAX
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q');

        $projects = Project::where('name', 'LIKE', "%{$query}%")
            ->orWhere('code', 'LIKE', "%{$query}%")
            ->orWhere('customer', 'LIKE', "%{$query}%")
            ->paginate(10);

        return response()->json($projects);
    }
}
