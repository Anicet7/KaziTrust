<?php

namespace App\Filament\Resources\TrustLogResource\Pages;

use App\Filament\Resources\TrustLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTrustLog extends ViewRecord
{
    protected static string $resource = TrustLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
