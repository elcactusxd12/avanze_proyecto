<?php

namespace App\Filament\Resources;
use App\Filament\Resources\AlumnoResource\Pages;
use App\Models\Alumno;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AlumnoResource extends Resource
{
    protected static ?string $model = Alumno::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Gestión Académica';
    protected static ?string $navigationLabel = 'Alumnos';
    protected static ?string $modelLabel = 'Alumno';
    protected static ?string $pluralModelLabel = 'Alumnos';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información personal')
                ->description('Datos de identificación del alumno.')
                ->icon('heroicon-o-identification')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('dni')
                        ->label('DNI')
                        ->required()
                        ->length(8)
                        ->regex('/^[0-9]{8}$/')
                        ->unique(ignoreRecord: true)
                        ->placeholder('Ingrese el DNI'),

                    Forms\Components\TextInput::make('nombres')
                        ->label('Nombres')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\TextInput::make('apellidos')
                        ->label('Apellidos')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\TextInput::make('email')
                        ->label('Correo electrónico')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true),
                ]),

            Forms\Components\Section::make('Información de contacto')
                ->description('Medios de contacto y domicilio del alumno.')
                ->icon('heroicon-o-phone')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('telefono')
                        ->label('Teléfono')
                        ->tel()
                        ->maxLength(20),

                    Forms\Components\TextInput::make('direccion')
                        ->label('Dirección')
                        ->maxLength(255)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            InfoSection::make('Datos del alumno')
                ->icon('heroicon-o-user')
                ->columns(2)
                ->schema([
                    TextEntry::make('dni')->label('DNI'),
                    TextEntry::make('nombres')->label('Nombres'),
                    TextEntry::make('apellidos')->label('Apellidos'),
                    TextEntry::make('email')->label('Correo'),
                    TextEntry::make('telefono')->label('Teléfono')->placeholder('No registrado'),
                    TextEntry::make('direccion')->label('Dirección')->placeholder('No registrada'),
                    TextEntry::make('created_at')->label('Fecha de registro')->dateTime('d/m/Y H:i'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('matriculas'))
            ->columns([
                Tables\Columns\TextColumn::make('dni')
                    ->label('DNI')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-identification'),

                Tables\Columns\TextColumn::make('nombres')
                    ->label('Alumno')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->nombres . ' ' . $record->apellidos
                    )
                    ->searchable(['nombres', 'apellidos'])
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Correo electrónico')
                    ->searchable()
                    ->icon('heroicon-o-envelope'),

                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->placeholder('Sin teléfono'),

                Tables\Columns\TextColumn::make('matriculas_count')
                    ->label('Matrículas')
                    ->badge()
                    ->color('info')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver detalles')
                    ->iconButton()
                    ->tooltip('Ver información'),

                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->iconButton()
                    ->tooltip('Editar alumno'),

                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->iconButton()
                    ->tooltip('Eliminar alumno')
                    ->requiresConfirmation(),
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
            'index' => Pages\ListAlumnos::route('/'),
            'create' => Pages\CreateAlumno::route('/create'),
            'edit' => Pages\EditAlumno::route('/{record}/edit'),
        ];
    }
}