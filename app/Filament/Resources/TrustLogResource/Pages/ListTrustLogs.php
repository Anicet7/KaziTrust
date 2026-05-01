<?php

namespace App\Filament\Resources\TrustLogResource\Pages;

use App\Filament\Resources\TrustLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrustLogs extends ListRecords
{
    protected static string $resource = TrustLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
