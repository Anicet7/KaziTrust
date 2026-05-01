<?php

namespace App\Filament\Supramanager\Resources\TenantResource\Pages;

use App\Filament\Supramanager\Resources\TenantResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;
}
