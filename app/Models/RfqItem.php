<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfqItem extends Model
{
    /** @use HasFactory<\Database\Factories\RfqItemFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rfq_id',
        'material_id',
        'name',
        'qty',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'qty' => 'integer',
    ];

    /**
     * Get the RFQ that owns the RFQ item.
     */
    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }

    /**
     * Get the material associated with the RFQ item.
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
