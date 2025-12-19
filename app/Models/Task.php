<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'project_name',
        'item_id',
        'item_name',
        'step',
        'machine_id',
        'target_qty',
        'completed_qty',
        'defect_qty',
        'shift',
        'status',
        'downtime_start',
        'total_downtime_minutes',
    ];

    protected $casts = [
        'project_id' => 'integer',
        'item_id' => 'integer',
        'machine_id' => 'integer',
        'downtime_start' => 'datetime',
        'total_downtime_minutes' => 'integer',
        'target_qty' => 'integer',
        'completed_qty' => 'integer',
        'defect_qty' => 'integer',
        'shift' => 'string',
    ];

    // Relationship with Project
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    // Relationship with ProjectItem
    public function projectItem()
    {
        return $this->belongsTo(ProjectItem::class, 'item_id', 'id');
    }

    // Relationship with Machine
    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id', 'id');
    }

    // Accessor to check if task is completed
    public function getIsCompletedAttribute()
    {
        return $this->status === 'COMPLETED';
    }

    // Accessor to check if task is in progress
    public function getIsInProgressAttribute()
    {
        return $this->status === 'IN_PROGRESS';
    }

    // Mutator to format status
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = strtoupper($value);
    }
}
