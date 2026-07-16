<?php

declare(strict_types=1);

namespace App\Filament\Resources\LaborRoleResource\Pages;

use App\Filament\Resources\LaborRoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaborRoles extends ListRecords
{
    protected static string $resource = LaborRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
