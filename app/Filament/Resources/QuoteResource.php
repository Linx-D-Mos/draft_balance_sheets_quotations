<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Models\Quote;
use App\Models\GlobalSetting;
use App\Models\LaborRole;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $modelLabel = 'Cotización';
    protected static ?string $pluralModelLabel = 'Cotizaciones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Quote Tabs')
                    ->tabs([
                        // Tab 1: General
                        Forms\Components\Tabs\Tab::make('General')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Título de la Cotización')
                                    ->required(),
                                Forms\Components\Select::make('project_id')
                                    ->label('Proyecto')
                                    ->relationship('project', 'title')
                                    ->required()
                                    ->preload(),
                                Forms\Components\Select::make('status_id')
                                    ->label('Estado')
                                    ->relationship('status', 'display_name')
                                    ->required()
                                    ->live()
                                    ->preload(),
                                Forms\Components\TextInput::make('margin_applied')
                                    ->label('Margen de Ganancia Pactado (%)')
                                    ->numeric()
                                    ->required()
                                    ->default(fn () => GlobalSetting::first()?->default_profit_margin ?? 30.00)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (callable $get, callable $set) => self::updateTotals($get, $set)),
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Fecha Estimada de Inicio')
                                    ->required(),
                                Forms\Components\DatePicker::make('end_date')
                                    ->label('Fecha Estimada de Fin')
                                    ->required(),
                                Forms\Components\Toggle::make('work_weekends')
                                    ->label('Trabajar Fines de Semana')
                                    ->default(false),
                            ])->columns(2),

                        // Tab 2: Mano de Obra
                        Forms\Components\Tabs\Tab::make('Mano de Obra')
                            ->schema([
                                Forms\Components\Repeater::make('laborAssignments')
                                    ->relationship('laborAssignments')
                                    ->schema([
                                        Forms\Components\Select::make('labor_role_id')
                                            ->label('Rol Laboral')
                                            ->relationship('laborRole', 'name')
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                if ($state) {
                                                    $role = LaborRole::find($state);
                                                    if ($role) {
                                                        $set('hourly_rate_at_estimation', $role->hourly_cost);
                                                    }
                                                }
                                                self::updateRowSubtotal($get, $set);
                                                self::updateTotals($get, $set);
                                            }),
                                        Forms\Components\Select::make('employee_id')
                                            ->label('Empleado (Opcional)')
                                            ->relationship('employee', 'name')
                                            ->preload()
                                            ->nullable(),
                                        Forms\Components\TextInput::make('worker_name_placeholder')
                                            ->label('Placeholder del Trabajador')
                                            ->placeholder('Ej: Pintor 1'),
                                        Forms\Components\TextInput::make('estimated_hours_regular')
                                            ->label('Horas Regulares')
                                            ->numeric()
                                            ->default(0)
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (callable $set, callable $get) {
                                                self::updateRowSubtotal($get, $set);
                                                self::updateTotals($get, $set);
                                            }),
                                        Forms\Components\TextInput::make('estimated_hours_extra')
                                            ->label('Horas Extras')
                                            ->numeric()
                                            ->default(0)
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (callable $set, callable $get) {
                                                self::updateRowSubtotal($get, $set);
                                                self::updateTotals($get, $set);
                                            }),
                                        Forms\Components\TextInput::make('hourly_rate_at_estimation')
                                            ->label('Costo por Hora (C_ch)')
                                            ->numeric()
                                            ->readOnly()
                                            ->default(0),
                                        Forms\Components\TextInput::make('estimated_subtotal')
                                            ->label('Subtotal Mano de Obra')
                                            ->numeric()
                                            ->readOnly()
                                            ->default(0),
                                    ])
                                    ->columns(2)
                                    ->live()
                                    ->afterStateUpdated(fn (callable $get, callable $set) => self::updateTotals($get, $set)),
                            ]),

                        // Tab 3: Materiales
                        Forms\Components\Tabs\Tab::make('Materiales')
                            ->schema([
                                Forms\Components\Repeater::make('materialItems')
                                    ->relationship('materialItems')
                                    ->schema([
                                        Forms\Components\TextInput::make('concept')
                                            ->label('Concepto/Material')
                                            ->required()
                                            ->placeholder('Ej: Pintura Benjamin Moore 5G'),
                                        Forms\Components\TextInput::make('estimated_quantity')
                                            ->label('Cantidad Estimada')
                                            ->numeric()
                                            ->default(1)
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (callable $set, callable $get) {
                                                self::updateMaterialSubtotal($get, $set);
                                                self::updateTotals($get, $set);
                                            }),
                                        Forms\Components\TextInput::make('estimated_unit_price')
                                            ->label('Precio Unitario Estimado')
                                            ->numeric()
                                            ->default(0)
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (callable $set, callable $get) {
                                                self::updateMaterialSubtotal($get, $set);
                                                self::updateTotals($get, $set);
                                            }),
                                        Forms\Components\TextInput::make('subtotal')
                                            ->label('Subtotal Materiales')
                                            ->numeric()
                                            ->readOnly()
                                            ->default(0),
                                    ])
                                    ->columns(2)
                                    ->live()
                                    ->afterStateUpdated(fn (callable $get, callable $set) => self::updateTotals($get, $set)),
                            ]),
                    ])
                    ->columnSpanFull(),

                // Resumen Financiero Section
                Forms\Components\Section::make('Resumen Financiero (Cálculo en Caliente)')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('total_hours')
                                    ->label('Total Horas Estimadas')
                                    ->numeric()
                                    ->readOnly()
                                    ->default(0),
                                Forms\Components\TextInput::make('direct_labor_cost')
                                    ->label('Costo Directo Mano de Obra')
                                    ->numeric()
                                    ->readOnly()
                                    ->prefix('$')
                                    ->default(0),
                                Forms\Components\TextInput::make('direct_materials_cost')
                                    ->label('Costo Directo Materiales')
                                    ->numeric()
                                    ->readOnly()
                                    ->prefix('$')
                                    ->default(0),
                                Forms\Components\TextInput::make('direct_cost')
                                    ->label('Costo Directo Total (CD)')
                                    ->numeric()
                                    ->readOnly()
                                    ->prefix('$')
                                    ->default(0),
                                Forms\Components\TextInput::make('overhead_cost')
                                    ->label('Overhead Absorbido (OH)')
                                    ->numeric()
                                    ->readOnly()
                                    ->prefix('$')
                                    ->default(0),
                                Forms\Components\TextInput::make('equilibrium_cost')
                                    ->label('Costo de Equilibrio (CE)')
                                    ->numeric()
                                    ->readOnly()
                                    ->prefix('$')
                                    ->default(0),
                                Forms\Components\TextInput::make('total_price')
                                    ->label('Precio de Venta Sugerido (PV)')
                                    ->numeric()
                                    ->readOnly()
                                    ->prefix('$')
                                    ->default(0),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->disabled(function ($record) {
                if (!$record) {
                    return false;
                }
                $record->loadMissing('status');
                return $record->status && $record->status->code === 'approved';
            });
}

    public static function updateRowSubtotal(callable $get, callable $set): void
    {
        $regHours = (float) ($get('estimated_hours_regular') ?? 0);
        $extHours = (float) ($get('estimated_hours_extra') ?? 0);
        $rate = (float) ($get('hourly_rate_at_estimation') ?? 0);
        
        $globalSettings = GlobalSetting::first();
        $overtimeMultiplier = $globalSettings ? (float) $globalSettings->overtime_multiplier : 1.5;
        
        $subtotal = ($regHours * $rate) + ($extHours * $rate * $overtimeMultiplier);
        $set('estimated_subtotal', number_format($subtotal, 4, '.', ''));
    }

    public static function updateMaterialSubtotal(callable $get, callable $set): void
    {
        $qty = (float) ($get('estimated_quantity') ?? 0);
        $price = (float) ($get('estimated_unit_price') ?? 0);
        $set('subtotal', number_format($qty * $price, 4, '.', ''));
    }

    public static function updateTotals(callable $get, callable $set): void
    {
        // 1. Calculate direct labor cost
        $laborAssignments = $get('laborAssignments') ?? [];
        $directLaborCost = 0.0;
        $totalHours = 0;
        
        $globalSettings = GlobalSetting::first();
        $overtimeMultiplier = $globalSettings ? (float) $globalSettings->overtime_multiplier : 1.5;
        $overheadRate = $globalSettings ? (float) $globalSettings->default_overhead_rate_applied : 0.0;
        
        foreach ($laborAssignments as $assignment) {
            $roleId = $assignment['labor_role_id'] ?? null;
            $regHours = (float) ($assignment['estimated_hours_regular'] ?? 0);
            $extHours = (float) ($assignment['estimated_hours_extra'] ?? 0);
            
            $roleHourlyCost = 0.0;
            if ($roleId) {
                $role = LaborRole::find($roleId);
                if ($role) {
                    $roleHourlyCost = (float) $role->hourly_cost;
                }
            }
            
            $subtotal = ($regHours * $roleHourlyCost) + ($extHours * $roleHourlyCost * $overtimeMultiplier);
            $directLaborCost += $subtotal;
            $totalHours += (int) ($regHours + $extHours);
        }
        
        // 2. Calculate direct materials cost
        $materialItems = $get('materialItems') ?? [];
        $directMaterialsCost = 0.0;
        
        foreach ($materialItems as $item) {
            $qty = (float) ($item['estimated_quantity'] ?? 0);
            $price = (float) ($item['estimated_unit_price'] ?? 0);
            $directMaterialsCost += ($qty * $price);
        }
        
        // 3. Direct cost
        $directCost = $directLaborCost + $directMaterialsCost;
        
        // 4. Overhead cost
        $overheadCost = $totalHours * $overheadRate;
        
        // 5. Equilibrium cost
        $equilibriumCost = $directCost + $overheadCost;
        
        // 6. Total price
        $margin = (float) ($get('margin_applied') ?? 0);
        $divider = 1.0 - ($margin / 100.0);
        $totalPrice = $divider > 0 ? ($equilibriumCost / $divider) : $equilibriumCost;
        
        // Set all computed fields
        $set('direct_labor_cost', number_format($directLaborCost, 4, '.', ''));
        $set('direct_materials_cost', number_format($directMaterialsCost, 4, '.', ''));
        $set('direct_cost', number_format($directCost, 4, '.', ''));
        $set('overhead_cost', number_format($overheadCost, 4, '.', ''));
        $set('equilibrium_cost', number_format($equilibriumCost, 4, '.', ''));
        $set('total_price', number_format($totalPrice, 4, '.', ''));
        $set('total_hours', $totalHours);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.title')
                    ->label('Proyecto')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status.display_name')
                    ->label('Estado')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_hours')
                    ->label('Horas Totales')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Precio Total')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Fecha Inicio')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fecha Fin')
                    ->date()
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
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}
