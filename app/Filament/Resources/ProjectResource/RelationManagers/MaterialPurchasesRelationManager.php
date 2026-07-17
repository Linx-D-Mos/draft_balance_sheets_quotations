<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\QuoteMaterialItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MaterialPurchasesRelationManager extends RelationManager
{
    protected static string $relationship = 'materialPurchases';

    protected static ?string $title = 'Tickets de Compra de Materiales';

    protected static ?string $modelLabel = 'Compra';
    protected static ?string $pluralModelLabel = 'Compras';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('material_category_id')
                    ->label('Categoría de Material')
                    ->relationship('materialCategory', 'display_name')
                    ->required()
                    ->preload(),
                Forms\Components\Select::make('quote_material_item_id')
                    ->label('Insumo Estimado de Cotización Aprobada')
                    ->options(function ($livewire) {
                        $project = $livewire->ownerRecord;
                        $approvedQuote = $project->quotes()
                            ->whereHas('status', fn ($query) => $query->where('code', 'approved'))
                            ->first();

                        if (!$approvedQuote) {
                            return [];
                        }

                        return QuoteMaterialItem::where('quote_id', $approvedQuote->id)
                            ->pluck('concept', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->nullable()
                    ->helperText('Opcional. Seleccione si corresponde a un insumo ya presupuestado.'),
                Forms\Components\TextInput::make('concept')
                    ->label('Concepto / Insumo Real')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('store')
                    ->label('Establecimiento / Proveedor')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('payment_method')
                    ->label('Método de Pago')
                    ->options([
                        'Cash' => 'Cash',
                        'Check' => 'Check',
                        'Credit Card' => 'Credit Card',
                        'Zelle' => 'Zelle',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('buyer_name')
                    ->label('Comprador')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('actual_quantity')
                    ->label('Cantidad Real')
                    ->numeric()
                    ->required()
                    ->default(1.00),
                Forms\Components\TextInput::make('actual_unit_price')
                    ->label('Precio Unitario Real')
                    ->numeric()
                    ->required()
                    ->default(0.00),
                Forms\Components\DatePicker::make('purchased_at')
                    ->label('Fecha de Compra')
                    ->required()
                    ->default(now()->format('Y-m-d')),
            ]);
    }

    public function table(Table $table): Table
    {
        $isProjectClosed = in_array($this->getOwnerRecord()->status->code, ['completed', 'canceled']);

        return $table
            ->recordTitleAttribute('concept')
            ->columns([
                Tables\Columns\TextColumn::make('purchased_at')
                    ->label('Fecha de Compra')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('materialCategory.display_name')
                    ->label('Categoría')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quoteMaterialItem.concept')
                    ->label('Insumo Estimado')
                    ->sortable()
                    ->placeholder('N/A (No presupuestado)'),
                Tables\Columns\TextColumn::make('concept')
                    ->label('Insumo Real')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('store')
                    ->label('Establecimiento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Pago'),
                Tables\Columns\TextColumn::make('buyer_name')
                    ->label('Comprador')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('actual_quantity')
                    ->label('Cant.')
                    ->alignRight(),
                Tables\Columns\TextColumn::make('actual_unit_price')
                    ->label('P. Unitario')
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
                    ->modalHeading('Anular Registro de Compra')
                    ->modalDescription('¿Está seguro de que desea anular esta compra de material? Esta acción no se puede deshacer.')
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
