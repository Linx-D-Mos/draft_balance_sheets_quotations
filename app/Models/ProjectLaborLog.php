<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectLaborLog extends Model
{
    protected $fillable = [
        'project_id',
        'employee_id',
        'labor_role_id',
        'annulled_by_user_id',
        'actual_hours_regular',
        'actual_hours_extra',
        'hourly_rate_actual',
        'overtime_multiplier_applied',
        'actual_subtotal',
        'logged_at',
        'is_annulled',
        'annulled_at',
        'annulment_reason',
    ];

    protected $casts = [
        'actual_hours_regular' => 'integer',
        'actual_hours_extra' => 'integer',
        'hourly_rate_actual' => 'decimal:4',
        'overtime_multiplier_applied' => 'decimal:4',
        'actual_subtotal' => 'decimal:4',
        'logged_at' => 'date',
        'is_annulled' => 'boolean',
        'annulled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (ProjectLaborLog $log) {
            $settings = GlobalSetting::first();
            $multiplier = $settings ? $settings->overtime_multiplier : 1.5;
            $log->overtime_multiplier_applied = $multiplier;

            $hoursRegular = (float) ($log->actual_hours_regular ?? 0);
            $hoursExtra = (float) ($log->actual_hours_extra ?? 0);
            $rate = (float) ($log->hourly_rate_actual ?? 0);
            $mult = (float) $log->overtime_multiplier_applied;

            $log->actual_subtotal = ($hoursRegular * $rate) + ($hoursExtra * $rate * $mult);
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function laborRole(): BelongsTo
    {
        return $this->belongsTo(LaborRole::class);
    }

    public function annulledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'annulled_by_user_id');
    }
}
