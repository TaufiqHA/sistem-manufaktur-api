<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubAssemblies extends Model
{
    /** @use HasFactory<\Database\Factories\SubAssembliesFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_id',
        'name',
        'qty_per_parent',
        'material_id',
        'processes',
        'total_needed',
        'completed_qty',
        'total_produced',
        'consumed_qty',
        'step_stats',
        'is_locked',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'processes' => 'array',
        'step_stats' => 'array',
        'is_locked' => 'boolean',
    ];

    /**
     * Get the material that owns the sub assembly.
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
