<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryOrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\DeliveryOrderItemFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'delivery_order_id',
        'warehouse_id',
        'project_id',
        'project_name',
        'item_name',
        'qty',
        'unit',
    ];

    /**
     * Get the delivery order that owns the delivery order item.
     */
    public function deliveryOrder(): BelongsTo
    {
        return $this->belongsTo(DeliveryOrder::class, 'delivery_order_id');
    }

    /**
     * Get the warehouse that owns the delivery order item.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(FinishedGoodsWarehouse::class, 'warehouse_id');
    }

    /**
     * Get the project that owns the delivery order item.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
