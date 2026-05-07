<?php

namespace App\Filament\Supramanager\Resources\PlanResource\Pages;

use App\Filament\Supramanager\Resources\PlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use App\Filament\Supramanager\Widgets\PlanStatsWidget; 


class ListPlans extends ListRecords
{
    protected static string $resource = PlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


       protected function getHeaderWidgets(): array
    {
        return [
            
           // PlanStatsWidget::class,
        ];
    }


}
