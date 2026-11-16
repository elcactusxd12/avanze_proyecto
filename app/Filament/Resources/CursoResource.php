<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CursoResource\Pages;
use App\Models\Curso;
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

class CursoResource extends Resource
{
    protected static ?string $model = Curso::class;

    protected static ?string $navigationGroup = 'Catálogo Académico';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Cursos';
    protected static ?string $modelLabel = 'Curso';
    protected static ?string $pluralModelLabel = 'Cursos';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Información del Curso')
                ->description('Configura los datos generales y académicos del curso.')
                ->icon('heroicon-o-book-open')
                ->columns(2)
                ->schema([
                    TextInput::make('nombre')
                        ->label('Nombre del Curso')
                        ->placeholder('Ej. Inglés Básico')
                        ->required()
                        ->maxLength(100),

                    Select::make('idiomas_id')
                        ->label('Idioma')
                        ->relationship('idioma', 'nombre')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('nivel')
                        ->label('Nivel')
                        ->options([
                            'Básico' => 'Básico',
                            'Intermedio' => 'Intermedio',
                            'Avanzado' => 'Avanzado',
                        ])
                        ->searchable()
                        ->required()
                        ->native(false),

                    TextInput::make('duracion_horas')
                        ->label('Duración en Horas')
                        ->numeric()
                        ->minValue(1)
                        ->required(),

                    TextInput::make('precio')
                        ->label('Precio del Curso')
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
            InfoSection::make('Detalle del Curso')
                ->icon('heroicon-o-book-open')
                ->columns(2)
                ->schema([
                    TextEntry::make('nombre')->label('Nombre del Curso')->weight('bold'),
                    TextEntry::make('idioma.nombre')->label('Idioma')->badge()->color('info'),
                    TextEntry::make('nivel')->label('Nivel')->badge()->color('success'),
                    TextEntry::make('duracion_horas')->label('Duración')->suffix(' horas'),
                    TextEntry::make('precio')->label('Precio')->money('PEN'),
                    TextEntry::make('created_at')->label('Fecha de Registro')->dateTime('d/m/Y H:i'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Curso')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('idioma.nombre')
                    ->label('Idioma')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nivel')
                    ->label('Nivel')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Básico' => 'success',
                        'Intermedio' => 'warning',
                        'Avanzado' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('duracion_horas')
                    ->label('Duración')
                    ->suffix(' h')
                    ->sortable(),

                Tables\Columns\TextColumn::make('precio')
                    ->label('Precio')
                    ->money('PEN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('grupos_count')
                    ->label('Grupos')
                    ->counts('grupos')
                    ->badge()
                    ->color('primary'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('idiomas_id')
                    ->label('Idioma')
                    ->relationship('idioma', 'nombre'),
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
            ->defaultSort('nombre')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCursos::route('/'),
            'create' => Pages\CreateCurso::route('/create'),
            'edit' => Pages\EditCurso::route('/{record}/edit'),
        ];
    }
}