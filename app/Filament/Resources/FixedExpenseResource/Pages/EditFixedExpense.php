<?php

declare(strict_types=1);

namespace App\Filament\Resources\FixedExpenseResource\Pages;

use App\Filament\Resources\FixedExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFixedExpense extends EditRecord
{
    protected static string $resource = FixedExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
