<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MatriculaResource\Pages;
use App\Models\Matricula;
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

class MatriculaResource extends Resource
{
    protected static ?string $model = Matricula::class;

    protected static ?string $navigationGroup = 'Gestión Académica';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Matrículas';
    protected static ?string $modelLabel = 'Matrícula';
    protected static ?string $pluralModelLabel = 'Matrículas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Información de Matrícula')
                ->description('Registra al estudiante y el grupo al que pertenece.')
                ->icon('heroicon-o-clipboard-document-list')
                ->columns(2)
                ->schema([
                    Select::make('alumnos_id')
                        ->label('Alumno')
                        ->relationship('alumno', 'nombres')
                        ->getOptionLabelFromRecordUsing(fn ($record) =>
                            "{$record->nombres} {$record->apellidos} — DNI: {$record->dni}"
                        )
                        ->searchable(['nombres', 'apellidos', 'dni'])
                        ->preload()
                        ->required(),

                    Select::make('grupos_id')
                        ->label('Grupo')
                        ->relationship('grupo', 'nombre')
                        ->searchable()
                        ->preload()
                        ->required(),

                    DatePicker::make('fecha_matricula')
                        ->label('Fecha de Matrícula')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->required(),

                    Select::make('estado')
                        ->label('Estado de Matrícula')
                        ->options([
                            'pendiente' => 'Pendiente',
                            'pagado' => 'Pagado',
                            'cancelado' => 'Cancelado',
                        ])
                        ->default('pendiente')
                        ->required()
                        ->native(false),

                    TextInput::make('precio_total')
                        ->label('Precio Total')
                        ->numeric()
                        ->prefix('S/')
                        ->minValue(0)
                        ->required(),
                ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            InfoSection::make('Detalle de Matrícula')
                ->icon('heroicon-o-clipboard-document-list')
                ->columns(2)
                ->schema([
                    TextEntry::make('alumno.nombres')
                        ->label('Alumno')
                        ->formatStateUsing(fn ($record) =>
                            "{$record->alumno->nombres} {$record->alumno->apellidos}"
                        ),

                    TextEntry::make('grupo.nombre')
                        ->label('Grupo')
                        ->badge()
                        ->color('info'),

                    TextEntry::make('fecha_matricula')
                        ->label('Fecha de Matrícula')
                        ->date('d/m/Y'),

                    TextEntry::make('estado')
                        ->label('Estado')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'pagado' => 'success',
                            'pendiente' => 'warning',
                            'cancelado' => 'danger',
                            default => 'gray',
                        }),

                    TextEntry::make('precio_total')
                        ->label('Precio Total')
                        ->money('PEN'),

                    TextEntry::make('created_at')
                        ->label('Fecha de Registro')
                        ->dateTime('d/m/Y H:i'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['alumno', 'grupo']))
            ->columns([
                Tables\Columns\TextColumn::make('alumno.nombres')
                    ->label('Estudiante')
                    ->formatStateUsing(fn ($record) =>
                        "{$record->alumno->nombres} {$record->alumno->apellidos}"
                    )
                    ->description(fn ($record) => 'DNI: ' . $record->alumno->dni)
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('grupo.nombre')
                    ->label('Grupo')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_matricula')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('precio_total')
                    ->label('Importe')
                    ->money('PEN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pagado' => 'success',
                        'pendiente' => 'warning',
                        'cancelado' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'pagado' => 'Pagado',
                        'cancelado' => 'Cancelado',
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
            ->defaultSort('fecha_matricula', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMatriculas::route('/'),
            'create' => Pages\CreateMatricula::route('/create'),
            'edit' => Pages\EditMatricula::route('/{record}/edit'),
        ];
    }
}