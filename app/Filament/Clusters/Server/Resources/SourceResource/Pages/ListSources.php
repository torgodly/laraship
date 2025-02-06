<?php

namespace App\Filament\Clusters\Server\Resources\SourceResource\Pages;

use App\Filament\Clusters\Server\Resources\SourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSources extends ListRecords
{
    protected static string $resource = SourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->requiresConfirmation()
                ->modalWidth('4xl')
                ->modalHeading('New GitHub App')
                ->modalDescription('This is required, if you would like to get full integration (commit / pull request deployments, etc) with GitHub.')
                ->createAnother(false)
        ];
    }
}
