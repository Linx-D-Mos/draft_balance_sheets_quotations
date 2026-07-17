<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialCategory extends Model
{
    protected $fillable = [
        'display_name',
        'code',
    ];

    public function purchases(): HasMany
    {
        return $this->hasMany(ProjectMaterialPurchase::class, 'material_category_id');
    }
}
