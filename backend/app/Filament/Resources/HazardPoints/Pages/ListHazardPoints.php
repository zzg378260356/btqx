<?php

namespace App\Filament\Resources\HazardPoints\Pages;

use App\Filament\Resources\HazardPoints\HazardPointResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHazardPoints extends ListRecords
{
    protected static string $resource = HazardPointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
