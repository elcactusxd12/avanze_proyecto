<?php

namespace App\Filament\Resources\IdiomaResource\Pages;

use App\Filament\Resources\IdiomaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIdiomas extends ListRecords
{
    protected static string $resource = IdiomaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
