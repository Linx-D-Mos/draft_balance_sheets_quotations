<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\GlobalSettingResource\Pages;
use App\Models\GlobalSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GlobalSettingResource extends Resource
{
    protected static ?string $model = GlobalSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $modelLabel = 'Configuración Global';
    protected static ?string $pluralModelLabel = 'Configuraciones Globales';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('standard_monthly_hours')
                    ->label('Horas Laborables Mensuales Promedio')
                    ->numeric()
                    ->readOnly()
                    ->helperText('Calculado automáticamente usando los feriados federales de EE. UU. (Yasumi).'),
                Forms\Components\TextInput::make('default_overhead_rate_applied')
                    ->label('Tasa de Overhead por Hora (T_oh)')
                    ->numeric()
                    ->readOnly()
                    ->helperText('Calculado dividiendo el total de gastos fijos activos por la capacidad de horas.'),
                Forms\Components\TextInput::make('default_profit_margin')
                    ->label('Margen de Ganancia por Defecto (%)')
                    ->numeric()
                    ->required()
                    ->default(30.00),
                Forms\Components\TextInput::make('overtime_multiplier')
                    ->label('Multiplicador de Horas Extras')
                    ->numeric()
                    ->required()
                    ->default(1.5),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('standard_monthly_hours')
                    ->label('Capacidad Mensual (Hrs)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('default_overhead_rate_applied')
                    ->label('Tasa Overhead (T_oh)')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('default_profit_margin')
                    ->label('Margen por Defecto')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('overtime_multiplier')
                    ->label('Multiplicador Extras')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGlobalSettings::route('/'),
            'create' => Pages\CreateGlobalSetting::route('/create'),
            'edit' => Pages\EditGlobalSetting::route('/{record}/edit'),
        ];
    }
}
