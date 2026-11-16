<?php

namespace App\Filament\Resources;
use App\Filament\Resources\AsistenciaResource\Pages;
use App\Models\Asistencia;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AsistenciaResource extends Resource
{
    protected static ?string $model = Asistencia::class;

    protected static ?string $navigationGroup = 'Gestión Académica';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Asistencias';
    protected static ?string $modelLabel = 'Asistencia';
    protected static ?string $pluralModelLabel = 'Asistencias';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Registro de Asistencia')
                ->description('Registra la fecha y el estado de asistencia del alumno.')
                ->icon('heroicon-o-clipboard-document-check')
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

                    DatePicker::make('fecha')
                        ->label('Fecha de Asistencia')
                        ->required()
                        ->native(false)
                        ->displayFormat('d/m/Y'),

                    Select::make('estado')
                        ->label('Estado')
                        ->options([
                            'presente' => 'Presente',
                            'ausente' => 'Ausente',
                            'tardanza' => 'Tardanza',
                            'justificado' => 'Justificado',
                        ])
                        ->default('presente')
                        ->required()
                        ->native(false),

                    Textarea::make('observacion')
                        ->label('Observaciones')
                        ->placeholder('Escribe alguna observación...')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            InfoSection::make('Detalle de Asistencia')
                ->icon('heroicon-o-clipboard-document-check')
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

                    TextEntry::make('fecha')
                        ->label('Fecha')
                        ->date('d/m/Y'),

                    TextEntry::make('estado')
                        ->label('Estado')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'presente' => 'success',
                            'ausente' => 'danger',
                            'tardanza' => 'warning',
                            'justificado' => 'info',
                            default => 'gray',
                        }),

                    TextEntry::make('observacion')
                        ->label('Observaciones')
                        ->placeholder('Sin observaciones')
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

                Tables\Columns\TextColumn::make('matricula.grupo.nombre')
                    ->label('Grupo')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'presente' => 'success',
                        'ausente' => 'danger',
                        'tardanza' => 'warning',
                        'justificado' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('observacion')
                    ->label('Observación')
                    ->limit(35)
                    ->placeholder('Sin observación'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Ver detalles')->iconButton(),
                Tables\Actions\EditAction::make()->label('Editar')->iconButton(),
                Tables\Actions\DeleteAction::make()->label('Eliminar')->iconButton()->requiresConfirmation(),
            ])
            ->actionsColumnLabel('Acciones')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados')
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('fecha', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAsistencias::route('/'),
            'create' => Pages\CreateAsistencia::route('/create'),
            'edit' => Pages\EditAsistencia::route('/{record}/edit'),
        ];
    }
}