<?php

declare(strict_types=1);

namespace App\Filament\Resources\FixedExpenseResource\Pages;

use App\Filament\Resources\FixedExpenseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFixedExpense extends CreateRecord
{
    protected static string $resource = FixedExpenseResource::class;
}
