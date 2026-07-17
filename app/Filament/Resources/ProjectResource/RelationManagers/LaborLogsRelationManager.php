<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\LaborRole;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LaborLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'laborLogs';

    protected static ?string $title = 'Bitácora de Horas';

    protected static ?string $modelLabel = 'Jornada';
    protected static ?string $pluralModelLabel = 'Jornadas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->label('Empleado')
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('labor_role_id')
                    ->label('Rol Laboral')
                    ->relationship('laborRole', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $role = LaborRole::find($state);
                            if ($role) {
                                $set('hourly_rate_actual', $role->hourly_cost);
                            }
                        }
                    }),
                Forms\Components\TextInput::make('hourly_rate_actual')
                    ->label('Tarifa Real por Hora')
                    ->numeric()
                    ->required()
                    ->default(0.00),
                Forms\Components\TextInput::make('actual_hours_regular')
                    ->label('Horas Regulares')
                    ->integer()
                    ->required()
                    ->default(0),
                Forms\Components\TextInput::make('actual_hours_extra')
                    ->label('Horas Extras')
                    ->integer()
                    ->required()
                    ->default(0),
                Forms\Components\DatePicker::make('logged_at')
                    ->label('Fecha')
                    ->required()
                    ->default(now()->format('Y-m-d')),
            ]);
    }

    public function table(Table $table): Table
    {
        $isProjectClosed = in_array($this->getOwnerRecord()->status->code, ['completed', 'canceled']);

        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('logged_at')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Empleado')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('laborRole.name')
                    ->label('Rol')
                    ->sortable(),
                Tables\Columns\TextColumn::make('actual_hours_regular')
                    ->label('H. Regulares')
                    ->alignRight(),
                Tables\Columns\TextColumn::make('actual_hours_extra')
                    ->label('H. Extras')
                    ->alignRight(),
                Tables\Columns\TextColumn::make('hourly_rate_actual')
                    ->label('Tarifa real')
                    ->money('USD')
                    ->alignRight(),
                Tables\Columns\TextColumn::make('actual_subtotal')
                    ->label('Subtotal Real')
                    ->money('USD')
                    ->alignRight(),
                Tables\Columns\IconColumn::make('is_annulled')
                    ->label('Anulado')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('annulledByUser.name')
                    ->label('Anulado por')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('annulment_reason')
                    ->label('Motivo Anulación')
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_annulled')
                    ->label('Estado de Anulación')
                    ->placeholder('Todos')
                    ->trueLabel('Solo Anulados')
                    ->falseLabel('Activos')
                    ->default(false),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->disabled($isProjectClosed),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->disabled(fn ($record) => $record->is_annulled || $isProjectClosed),
                Tables\Actions\Action::make('anular')
                    ->label('Anular')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Anular Registro de Mano de Obra')
                    ->modalDescription('¿Está seguro de que desea anular esta jornada? Esta acción no se puede deshacer.')
                    ->form([
                        Forms\Components\Textarea::make('annulment_reason')
                            ->label('Motivo de la Anulación')
                            ->required(),
                    ])
                    ->action(function ($record, array $data): void {
                        $record->update([
                            'is_annulled' => true,
                            'annulled_at' => now(),
                            'annulment_reason' => $data['annulment_reason'],
                            'annulled_by_user_id' => auth()->id(),
                        ]);
                    })
                    ->visible(fn ($record) => !$record->is_annulled)
                    ->disabled($isProjectClosed),
            ])
            ->bulkActions([
                // Physical deletions are prohibited
            ]);
    }
}
