<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteMaterialItem extends Model
{
    protected $fillable = [
        'quote_id',
        'concept',
        'estimated_quantity',
        'estimated_unit_price',
        'subtotal',
    ];

    protected $casts = [
        'estimated_quantity' => 'decimal:4',
        'estimated_unit_price' => 'decimal:4',
        'subtotal' => 'decimal:4',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (QuoteMaterialItem $item) {
            $qty = (float) $item->estimated_quantity;
            $price = (float) $item->estimated_unit_price;
            $item->subtotal = $qty * $price;
        });
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(ProjectMaterialPurchase::class, 'quote_material_item_id');
    }
}
