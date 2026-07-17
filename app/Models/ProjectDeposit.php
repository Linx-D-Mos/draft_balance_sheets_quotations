<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectDeposit extends Model
{
    protected $fillable = [
        'project_id',
        'annulled_by_user_id',
        'amount',
        'payment_method',
        'received_at',
        'reference_number',
        'is_annulled',
        'annulled_at',
        'annulment_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'received_at' => 'date',
        'is_annulled' => 'boolean',
        'annulled_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function annulledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'annulled_by_user_id');
    }
}
