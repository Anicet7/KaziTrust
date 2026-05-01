<?php

namespace App\Filament\Resources\TrustLogResource\Pages;

use App\Filament\Resources\TrustLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrustLog extends EditRecord
{
    protected static string $resource = TrustLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
