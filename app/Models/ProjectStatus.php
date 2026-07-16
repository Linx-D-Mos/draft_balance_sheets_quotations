<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectStatus extends Model
{
    protected $fillable = ['display_name', 'code'];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
