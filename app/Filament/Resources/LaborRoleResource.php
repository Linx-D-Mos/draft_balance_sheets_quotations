<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\LaborRoleResource\Pages;
use App\Models\LaborRole;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LaborRoleResource extends Resource
{
    protected static ?string $model = LaborRole::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $modelLabel = 'Rol Laboral';
    protected static ?string $pluralModelLabel = 'Roles Laborales';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre del Rol')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('base_salary')
                    ->label('Salario Base por Hora')
                    ->numeric()
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (callable $set, callable $get) {
                        $base = (float) ($get('base_salary') ?? 0);
                        $pct = (float) ($get('social_load_pct') ?? 0);
                        $set('hourly_cost', number_format($base * (1 + $pct / 100), 4, '.', ''));
                    }),
                Forms\Components\TextInput::make('social_load_pct')
                    ->label('% Carga Social Patronal')
                    ->numeric()
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (callable $set, callable $get) {
                        $base = (float) ($get('base_salary') ?? 0);
                        $pct = (float) ($get('social_load_pct') ?? 0);
                        $set('hourly_cost', number_format($base * (1 + $pct / 100), 4, '.', ''));
                    }),
                Forms\Components\TextInput::make('hourly_cost')
                    ->label('Costo Cargado por Hora (C_ch)')
                    ->numeric()
                    ->readOnly()
                    ->placeholder('Calculado al guardar'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('base_salary')
                    ->label('Salario Base')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('social_load_pct')
                    ->label('Carga Social %')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('hourly_cost')
                    ->label('Costo por Hora (C_ch)')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListLaborRoles::route('/'),
            'create' => Pages\CreateLaborRole::route('/create'),
            'edit' => Pages\EditLaborRole::route('/{record}/edit'),
        ];
    }
}
