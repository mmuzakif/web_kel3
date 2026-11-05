<?php

namespace App\Filament\Resources\Acaras\Pages;

use App\Filament\Resources\Acaras\AcaraResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAcaras extends ListRecords
{
    protected static string $resource = AcaraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
