<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FixedExpense extends Model
{
    protected $fillable = ['concept', 'amount', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'amount' => 'decimal:4',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            GlobalSetting::recalculateOverhead();
        });

        static::deleted(function () {
            GlobalSetting::recalculateOverhead();
        });
    }
}
