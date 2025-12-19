<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BomItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_id',
        'material_id',
        'quantity_per_unit',
        'total_required',
        'allocated',
        'realized',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'total_required' => 'integer',
        'allocated' => 'integer',
        'realized' => 'integer',
    ];

    /**
     * Get the project item associated with the BOM item.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(ProjectItem::class, 'item_id', 'id');
    }

    /**
     * Get the material associated with the BOM item.
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id', 'id');
    }
}