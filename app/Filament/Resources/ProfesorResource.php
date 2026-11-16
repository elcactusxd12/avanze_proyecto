<?php

namespace App\Filament\Resources;
use App\Filament\Resources\ProfesorResource\Pages;
use App\Models\Profesor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProfesorResource extends Resource
{
    protected static ?string $model = Profesor::class;

    protected static ?string $navigationGroup = 'Gestión Académica';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Profesores';
    protected static ?string $modelLabel = 'Profesor';
    protected static ?string $pluralModelLabel = 'Profesores';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Identificación del Profesor')
                ->description('Datos personales y documento de identidad del docente.')
                ->icon('heroicon-o-identification')
                ->columns(3)
                ->schema([
                    TextInput::make('dni')
                        ->label('DNI')
                        ->prefixIcon('heroicon-o-identification')
                        ->required()
                        ->length(8)
                        ->numeric()
                        ->unique(ignoreRecord: true),

                    TextInput::make('nombres')
                        ->label('Nombres')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('apellidos')
                        ->label('Apellidos')
                        ->required()
                        ->maxLength(100),
                ]),

            Section::make('Información Profesional y Contacto')
                ->icon('heroicon-o-briefcase')
                ->columns(2)
                ->schema([
                    TextInput::make('especialidad')
                        ->label('Especialidad')
                        ->placeholder('Ej. Idioma Inglés')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('email')
                        ->label('Correo Electrónico')
                        ->email()
                        ->prefixIcon('heroicon-o-envelope')
                        ->required()
                        ->maxLength(150)
                        ->unique(ignoreRecord: true),

                    TextInput::make('telefono')
                        ->label('Teléfono')
                        ->tel()
                        ->prefixIcon('heroicon-o-phone')
                        ->maxLength(20),
                ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            InfoSection::make('Información del Profesor')
                ->icon('heroicon-o-academic-cap')
                ->columns(2)
                ->schema([
                    TextEntry::make('dni')->label('DNI')->badge(),
                    TextEntry::make('nombres')->label('Nombres'),
                    TextEntry::make('apellidos')->label('Apellidos'),
                    TextEntry::make('especialidad')->label('Especialidad')->badge()->color('info'),
                    TextEntry::make('email')->label('Correo')->icon('heroicon-o-envelope'),
                    TextEntry::make('telefono')->label('Teléfono')->placeholder('No registrado'),
                    TextEntry::make('created_at')->label('Fecha de Registro')->dateTime('d/m/Y H:i'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dni')
                    ->label('DNI')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nombres')
                    ->label('Profesor')
                    ->formatStateUsing(fn (Profesor $record) =>
                        "{$record->nombres} {$record->apellidos}"
                    )
                    ->weight('bold')
                    ->description(fn (Profesor $record) => $record->email)
                    ->searchable(['nombres', 'apellidos', 'email'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('especialidad')
                    ->label('Especialidad')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->placeholder('Sin registro'),

                Tables\Columns\TextColumn::make('grupos_count')
                    ->label('Grupos')
                    ->counts('grupos')
                    ->badge()
                    ->color('success'),
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
            ->defaultSort('nombres')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProfesors::route('/'),
            'create' => Pages\CreateProfesor::route('/create'),
            'edit' => Pages\EditProfesor::route('/{record}/edit'),
        ];
    }
}