<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMaterialPurchase extends Model
{
    protected $fillable = [
        'project_id',
        'material_category_id',
        'quote_material_item_id',
        'annulled_by_user_id',
        'concept',
        'store',
        'payment_method',
        'buyer_name',
        'actual_quantity',
        'actual_unit_price',
        'actual_subtotal',
        'purchased_at',
        'is_annulled',
        'annulled_at',
        'annulment_reason',
    ];

    protected $casts = [
        'actual_quantity' => 'decimal:4',
        'actual_unit_price' => 'decimal:4',
        'actual_subtotal' => 'decimal:4',
        'purchased_at' => 'date',
        'is_annulled' => 'boolean',
        'annulled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (ProjectMaterialPurchase $purchase) {
            $qty = (float) ($purchase->actual_quantity ?? 0);
            $price = (float) ($purchase->actual_unit_price ?? 0);
            $purchase->actual_subtotal = $qty * $price;
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function materialCategory(): BelongsTo
    {
        return $this->belongsTo(MaterialCategory::class, 'material_category_id');
    }

    public function quoteMaterialItem(): BelongsTo
    {
        return $this->belongsTo(QuoteMaterialItem::class, 'quote_material_item_id');
    }

    public function annulledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'annulled_by_user_id');
    }
}
