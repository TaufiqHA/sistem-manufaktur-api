<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'unit',
        'current_stock',
        'safety_stock',
        'price_per_unit',
        'category'
    ];

    protected $casts = [
        'current_stock' => 'integer',
        'safety_stock' => 'integer',
        'price_per_unit' => 'decimal:2'
    ];

    /**
     * Check if stock is below safety level
     */
    public function isLowStock()
    {
        return $this->current_stock < $this->safety_stock;
    }

    /**
     * Get all receiving items for this material.
     */
    public function receivingItems()
    {
        return $this->hasMany(ReceivingItem::class, 'material_id');
    }
}