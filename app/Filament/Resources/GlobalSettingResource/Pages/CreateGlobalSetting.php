<?php

declare(strict_types=1);

namespace App\Filament\Resources\GlobalSettingResource\Pages;

use App\Filament\Resources\GlobalSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGlobalSetting extends CreateRecord
{
    protected static string $resource = GlobalSettingResource::class;
}
