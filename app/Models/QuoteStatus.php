<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteStatus extends Model
{
    protected $fillable = ['display_name', 'code'];

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'status_id');
    }
}
