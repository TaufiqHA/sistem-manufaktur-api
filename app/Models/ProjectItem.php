<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectItem extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectItemFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'dimensions',
        'thickness',
        'qty_set',
        'qty_per_product',
        'total_required_qty',
        'quantity',
        'unit',
        'is_bom_locked',
        'is_workflow_locked',
        'workflow',
    ];

    protected $casts = [
        'workflow' => 'array',
        'is_bom_locked' => 'boolean',
        'is_workflow_locked' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
