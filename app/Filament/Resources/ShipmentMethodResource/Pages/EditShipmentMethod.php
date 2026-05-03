<?php

namespace App\Filament\Resources\ShipmentMethodResource\Pages;

use App\Filament\Resources\ShipmentMethodResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShipmentMethod extends EditRecord
{
    protected static string $resource = ShipmentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
