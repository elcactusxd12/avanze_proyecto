<?php

namespace App\Filament\Resources\IdiomaResource\Pages;

use App\Filament\Resources\IdiomaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIdioma extends EditRecord
{
    protected static string $resource = IdiomaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
