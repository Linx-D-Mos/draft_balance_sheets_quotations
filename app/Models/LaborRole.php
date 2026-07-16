<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaborRole extends Model
{
    protected $fillable = ['name', 'base_salary', 'social_load_pct', 'hourly_cost', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'base_salary' => 'decimal:4',
        'social_load_pct' => 'decimal:4',
        'hourly_cost' => 'decimal:4',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (LaborRole $role) {
            $base = (float) $role->base_salary;
            $pct = (float) $role->social_load_pct;
            $role->hourly_cost = $base * (1 + $pct / 100);
        });
    }

    public function laborAssignments(): HasMany
    {
        return $this->hasMany(QuoteLaborAssignment::class);
    }
}
