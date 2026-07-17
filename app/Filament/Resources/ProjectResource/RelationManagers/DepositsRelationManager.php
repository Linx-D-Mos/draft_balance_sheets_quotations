<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DepositsRelationManager extends RelationManager
{
    protected static string $relationship = 'deposits';

    protected static ?string $title = 'Anticipos y Pagos del Cliente';

    protected static ?string $modelLabel = 'Anticipo';
    protected static ?string $pluralModelLabel = 'Anticipos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->label('Monto del Anticipo')
                    ->numeric()
                    ->required()
                    ->default(0.00),
                Forms\Components\Select::make('payment_method')
                    ->label('Método de Pago')
                    ->options([
                        'Cash' => 'Cash',
                        'Check' => 'Check',
                        'Credit Card' => 'Credit Card',
                        'Zelle' => 'Zelle',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('received_at')
                    ->label('Fecha de Recepción')
                    ->required()
                    ->default(now()->format('Y-m-d')),
                Forms\Components\TextInput::make('reference_number')
                    ->label('Número de Referencia (Opcional)')
                    ->maxLength(255)
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        $isProjectClosed = in_array($this->getOwnerRecord()->status->code, ['completed', 'canceled']);

        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('received_at')
                    ->label('Fecha de Recepción')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->money('USD')
                    ->alignRight()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Método de Pago')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Referencia')
                    ->placeholder('N/A'),
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
                    ->modalHeading('Anular Anticipo')
                    ->modalDescription('¿Está seguro de que desea anular este anticipo? Esta acción no se puede deshacer.')
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
