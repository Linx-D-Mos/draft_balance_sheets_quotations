<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = ['client_id', 'project_status_id', 'title'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ProjectStatus::class, 'project_status_id');
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function laborLogs(): HasMany
    {
        return $this->hasMany(ProjectLaborLog::class);
    }

    public function materialPurchases(): HasMany
    {
        return $this->hasMany(ProjectMaterialPurchase::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(ProjectDeposit::class);
    }
}
