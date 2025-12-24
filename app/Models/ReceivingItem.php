<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivingItem extends Model
{
    /** @use HasFactory<\Database\Factories\ReceivingItemFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'receiving_id',
        'material_id',
        'name',
        'qty',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'qty' => 'integer',
    ];

    /**
     * Get the receiving that owns the receiving item.
     */
    public function receiving()
    {
        return $this->belongsTo(ReceivingGood::class, 'receiving_id');
    }


    /**
     * Get the material that the receiving item relates to.
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
