<?php

namespace App\Filament\Resources\Acaras\Pages;

use App\Filament\Resources\Acaras\AcaraResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAcara extends EditRecord
{
    protected static string $resource = AcaraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
