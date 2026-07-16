<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model
{
    protected $fillable = [
        'standard_monthly_hours',
        'default_overhead_rate_applied',
        'default_profit_margin',
        'overtime_multiplier',
    ];

    protected $casts = [
        'standard_monthly_hours' => 'decimal:4',
        'default_overhead_rate_applied' => 'decimal:4',
        'default_profit_margin' => 'decimal:4',
        'overtime_multiplier' => 'decimal:4',
    ];

    public static function calculateHours(int $year): float
    {
        $startDate = new \DateTime("$year-01-01");
        $endDate = new \DateTime("$year-12-31");
        $holidays = \Yasumi\Yasumi::create('USA', $year);
        $workingDays = 0;
        
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($startDate, $interval, $endDate->modify('+1 day'));
        
        foreach ($period as $date) {
            $dayOfWeek = (int) $date->format('N'); // 1 = Mon, 7 = Sun
            if ($dayOfWeek >= 6) {
                continue;
            }
            if ($holidays->isHoliday($date)) {
                continue;
            }
            $workingDays++;
        }
        
        return ($workingDays * 8.0) / 12.0;
    }

    public static function recalculateOverhead(): void
    {
        $year = (int) date('Y');
        $standardHours = self::calculateHours($year);
        
        $totalMonthlyOverhead = FixedExpense::where('is_active', true)->sum('amount');
        
        $overheadRate = $standardHours > 0 ? ($totalMonthlyOverhead / $standardHours) : 0.0;
        
        $settings = self::first();
        if ($settings) {
            $settings->update([
                'standard_monthly_hours' => $standardHours,
                'default_overhead_rate_applied' => $overheadRate,
            ]);
        } else {
            self::create([
                'standard_monthly_hours' => $standardHours,
                'default_overhead_rate_applied' => $overheadRate,
                'default_profit_margin' => 30.00,
                'overtime_multiplier' => 1.5,
            ]);
        }
    }
}
