<?php

namespace App\Filament\Resources;


use App\Filament\Resources\IdiomaResource\Pages;
use App\Models\Idioma;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class IdiomaResource extends Resource
{
    protected static ?string $model = Idioma::class;
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationGroup = 'Catálogo Académico';
    protected static ?string $navigationLabel = 'Idiomas';
    protected static ?string $modelLabel = 'Idioma';
    protected static ?string $pluralModelLabel = 'Idiomas';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información del idioma')
                ->description('Registra el idioma y su código de identificación.')
                ->icon('heroicon-o-language')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('nombre')
                        ->label('Nombre del idioma')
                        ->required()
                        ->maxLength(100)
                        ->placeholder('Ej. Inglés'),

                    Forms\Components\TextInput::make('codigo')
                        ->label('Código')
                        ->required()
                        ->maxLength(10)
                        ->unique(ignoreRecord: true)
                        ->placeholder('Ej. EN')
                        ->dehydrateStateUsing(fn ($state) => strtoupper($state)),
                ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            InfoSection::make('Detalles del idioma')
                ->icon('heroicon-o-globe-alt')
                ->columns(2)
                ->schema([
                    TextEntry::make('nombre')->label('Idioma'),
                    TextEntry::make('codigo')->label('Código')->badge()->color('info'),
                    TextEntry::make('created_at')->label('Fecha de registro')->dateTime('d/m/Y H:i'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('cursos'))
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nombre')
                    ->label('Idioma')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-globe-alt'),

                Tables\Columns\TextColumn::make('cursos_count')
                    ->label('Cursos registrados')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de registro')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
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
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIdiomas::route('/'),
            'create' => Pages\CreateIdioma::route('/create'),
            'edit' => Pages\EditIdioma::route('/{record}/edit'),
        ];
    }
}