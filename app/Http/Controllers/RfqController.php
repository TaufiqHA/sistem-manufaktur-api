<?php

namespace App\Http\Controllers;

use App\Models\Rfq;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Validation\Rule;

class RfqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Rfq::query();

        // Search functionality - search in code or description
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $status = $request->get('status');
            $query->where('status', $status);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $query->whereBetween('date', [$startDate, $endDate]);
        } elseif ($request->has('date')) {
            $date = $request->get('date');
            $query->whereDate('date', $date);
        }

        $rfqs = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'message' => 'RFQs retrieved successfully',
            'data' => $rfqs->items(),
            'pagination' => [
                'current_page' => $rfqs->currentPage(),
                'last_page' => $rfqs->lastPage(),
                'per_page' => $rfqs->perPage(),
                'total' => $rfqs->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:rfqs,code',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['DRAFT', 'PO_CREATED'])],
        ]);

        $rfq = Rfq::create($validated);

        return response()->json([
            'message' => 'RFQ created successfully',
            'data' => $rfq
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Rfq $rfq): JsonResource
    {
        return new class($rfq) extends JsonResource {
            public function toArray($request): array
            {
                return [
                    'id' => $this->id,
                    'code' => $this->code,
                    'date' => $this->date,
                    'description' => $this->description,
                    'status' => $this->status,
                    'created_at' => $this->created_at,
                    'updated_at' => $this->updated_at,
                ];
            }
        };
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rfq $rfq): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'sometimes|string|max:50|unique:rfqs,code,' . $rfq->id,
            'date' => 'sometimes|date',
            'description' => 'sometimes|nullable|string',
            'status' => ['sometimes', Rule::in(['DRAFT', 'PO_CREATED'])],
        ]);

        $rfq->update($validated);

        return response()->json([
            'message' => 'RFQ updated successfully',
            'data' => $rfq
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rfq $rfq): JsonResponse
    {
        $rfq->delete();

        return response()->json([
            'message' => 'RFQ deleted successfully'
        ]);
    }
}
