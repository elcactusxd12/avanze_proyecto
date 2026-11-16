<?php

namespace App\Filament\Resources;
use App\Filament\Resources\PagoResource\Pages;
use App\Models\Pago;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PagoResource extends Resource
{
    protected static ?string $model = Pago::class;

    protected static ?string $navigationGroup = 'Gestión Académica';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Pagos';
    protected static ?string $modelLabel = 'Pago';
    protected static ?string $pluralModelLabel = 'Pagos';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Información del Pago')
                ->description('Registra el importe, concepto y método de pago.')
                ->icon('heroicon-o-banknotes')
                ->columns(2)
                ->schema([
                    Select::make('matriculas_id')
                        ->label('Matrícula')
                        ->relationship('matricula', 'id')
                        ->getOptionLabelFromRecordUsing(fn ($record) =>
                            $record->alumno->nombres . ' ' .
                            $record->alumno->apellidos . ' — ' .
                            $record->grupo->nombre
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('monto')
                        ->label('Monto')
                        ->numeric()
                        ->prefix('S/')
                        ->minValue(0.01)
                        ->required(),

                    DatePicker::make('fecha_pago')
                        ->label('Fecha de Pago')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->required(),

                    Select::make('metodo_pago')
                        ->label('Método de Pago')
                        ->options([
                            'Efectivo' => 'Efectivo',
                            'Transferencia' => 'Transferencia bancaria',
                            'Tarjeta' => 'Tarjeta',
                            'Yape' => 'Yape',
                            'Plin' => 'Plin',
                        ])
                        ->searchable()
                        ->native(false)
                        ->required(),

                    TextInput::make('concepto')
                        ->label('Concepto')
                        ->placeholder('Ej. Cuota mensual, matrícula...')
                        ->maxLength(100)
                        ->required()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            InfoSection::make('Detalle del Pago')
                ->icon('heroicon-o-banknotes')
                ->columns(2)
                ->schema([
                    TextEntry::make('matricula.alumno.nombres')
                        ->label('Alumno')
                        ->formatStateUsing(fn ($record) =>
                            $record->matricula->alumno->nombres . ' ' .
                            $record->matricula->alumno->apellidos
                        ),

                    TextEntry::make('matricula.grupo.nombre')
                        ->label('Grupo'),

                    TextEntry::make('monto')
                        ->label('Monto Pagado')
                        ->money('PEN')
                        ->weight('bold'),

                    TextEntry::make('fecha_pago')
                        ->label('Fecha de Pago')
                        ->date('d/m/Y'),

                    TextEntry::make('metodo_pago')
                        ->label('Método de Pago')
                        ->badge()
                        ->color('info'),

                    TextEntry::make('concepto')
                        ->label('Concepto')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['matricula.alumno', 'matricula.grupo']))
            ->columns([
                Tables\Columns\TextColumn::make('matricula.alumno.nombres')
                    ->label('Alumno')
                    ->formatStateUsing(fn ($record) =>
                        $record->matricula->alumno->nombres . ' ' .
                        $record->matricula->alumno->apellidos
                    )
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('monto')
                    ->label('Monto')
                    ->money('PEN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_pago')
                    ->label('Fecha de Pago')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('metodo_pago')
                    ->label('Método')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('concepto')
                    ->label('Concepto')
                    ->limit(35)
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('metodo_pago')
                    ->label('Método de Pago')
                    ->options([
                        'Efectivo' => 'Efectivo',
                        'Transferencia' => 'Transferencia',
                        'Tarjeta' => 'Tarjeta',
                        'Yape' => 'Yape',
                        'Plin' => 'Plin',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Ver detalles')->iconButton(),
                Tables\Actions\EditAction::make()->label('Editar')->iconButton(),
                Tables\Actions\DeleteAction::make()->label('Eliminar')->iconButton()->requiresConfirmation(),
            ])
            ->actionsColumnLabel('Acciones')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Eliminar seleccionados')->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('fecha_pago', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPagos::route('/'),
            'create' => Pages\CreatePago::route('/create'),
            'edit' => Pages\EditPago::route('/{record}/edit'),
        ];
    }
}