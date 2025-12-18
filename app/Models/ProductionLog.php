<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionLog extends Model
{
    /** @use HasFactory<\Database\Factories\ProductionLogFactory> */
    use HasFactory;

    protected $fillable = [
        'task_id',
        'machine_id',
        'item_id',
        'project_id',
        'step',
        'shift',
        'good_qty',
        'defect_qty',
        'operator',
        'logged_at',
        'type',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
    ];

    // Relationships
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }

    public function item()
    {
        return $this->belongsTo(ProjectItem::class, 'item_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
