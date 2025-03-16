<?php

namespace App\Filament\Resources\SiteResource\Pages;

use App\Actions\SystemActions\SiteActions\CreateSiteAction;
use App\Filament\Resources\SiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSites extends ListRecords
{
    protected static string $resource = SiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->action(function ($data) {
                    $record = (new CreateSiteAction())->execute($data);
                    return redirect(ViewSite::getUrl([$record]));
                })
            ,
        ];
    }

}
