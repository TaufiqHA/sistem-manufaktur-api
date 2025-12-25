<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinishedGoodsWarehouse extends Model
{
    /** @use HasFactory<\Database\Factories\FinishedGoodsWarehouseFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'item_name',
        'total_produced',
        'shipped_qty',
        'available_stock',
        'unit',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'total_produced' => 'integer',
        'shipped_qty' => 'integer',
        'available_stock' => 'integer',
    ];

    /**
     * Get the project that owns the finished goods warehouse record.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
