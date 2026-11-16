<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GrupoResource\Pages;
use App\Models\Grupo;
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

class GrupoResource extends Resource
{
    protected static ?string $model = Grupo::class;

    protected static ?string $navigationGroup = 'Gestión Académica';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Grupos';
    protected static ?string $modelLabel = 'Grupo';
    protected static ?string $pluralModelLabel = 'Grupos';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Configuración del Grupo')
                ->description('Asigna el curso, docente y capacidad del grupo.')
                ->icon('heroicon-o-user-group')
                ->columns(2)
                ->schema([
                    TextInput::make('nombre')
                        ->label('Nombre del Grupo')
                        ->placeholder('Ej. Inglés Básico - Grupo A')
                        ->required()
                        ->maxLength(100),

                    Select::make('cursos_id')
                        ->label('Curso')
                        ->relationship('curso', 'nombre')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('profesores_id')
                        ->label('Profesor')
                        ->relationship('profesor', 'nombres')
                        ->getOptionLabelFromRecordUsing(fn ($record) =>
                            "{$record->nombres} {$record->apellidos}"
                        )
                        ->searchable(['nombres', 'apellidos'])
                        ->preload()
                        ->required(),

                    TextInput::make('horario')
                        ->label('Horario')
                        ->placeholder('Ej. Lunes y miércoles - 08:00 a 10:00')
                        ->prefixIcon('heroicon-o-clock')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('cupo_maximo')
                        ->label('Cupo Máximo')
                        ->numeric()
                        ->minValue(1)
                        ->required(),

                    DatePicker::make('fecha_inicio')
                        ->label('Fecha de Inicio')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->required(),

                    DatePicker::make('fecha_fin')
                        ->label('Fecha de Finalización')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->afterOrEqual('fecha_inicio')
                        ->required(),
                ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            InfoSection::make('Información del Grupo')
                ->icon('heroicon-o-user-group')
                ->columns(2)
                ->schema([
                    TextEntry::make('nombre')->label('Nombre del Grupo')->weight('bold'),
                    TextEntry::make('curso.nombre')->label('Curso')->badge()->color('info'),
                    TextEntry::make('profesor.nombres')
                        ->label('Profesor')
                        ->formatStateUsing(fn ($record) =>
                            "{$record->profesor->nombres} {$record->profesor->apellidos}"
                        ),
                    TextEntry::make('horario')->label('Horario')->icon('heroicon-o-clock'),
                    TextEntry::make('fecha_inicio')->label('Fecha de Inicio')->date('d/m/Y'),
                    TextEntry::make('fecha_fin')->label('Fecha de Finalización')->date('d/m/Y'),
                    TextEntry::make('cupo_maximo')->label('Cupo Máximo')->suffix(' alumnos'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['curso', 'profesor']))
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Grupo')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('curso.nombre')
                    ->label('Curso')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('profesor.nombres')
                    ->label('Profesor')
                    ->formatStateUsing(fn ($record) =>
                        "{$record->profesor->nombres} {$record->profesor->apellidos}"
                    )
                    ->searchable(),

                Tables\Columns\TextColumn::make('horario')
                    ->label('Horario')
                    ->icon('heroicon-o-clock')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_fin')
                    ->label('Finalización')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('cupo_maximo')
                    ->label('Cupo')
                    ->suffix(' alumnos')
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('cursos_id')
                    ->label('Curso')
                    ->relationship('curso', 'nombre'),

                Tables\Filters\SelectFilter::make('profesores_id')
                    ->label('Profesor')
                    ->relationship('profesor', 'nombres'),
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
            ->defaultSort('fecha_inicio', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGrupos::route('/'),
            'create' => Pages\CreateGrupo::route('/create'),
            'edit' => Pages\EditGrupo::route('/{record}/edit'),
        ];
    }
}