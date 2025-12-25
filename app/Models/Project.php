<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'customer',
        'start_date',
        'deadline',
        'status',
        'progress',
        'qty_per_unit',
        'procurement_qty',
        'total_qty',
        'unit',
        'is_locked',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'date',
        'progress' => 'integer',
        'qty_per_unit' => 'integer',
        'procurement_qty' => 'integer',
        'total_qty' => 'integer',
        'is_locked' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the finished goods warehouses for the project.
     */
    public function finishedGoodsWarehouses(): HasMany
    {
        return $this->hasMany(FinishedGoodsWarehouse::class);
    }
}
