<?php

namespace App\Filament\Resources\SiteResource\Pages;

use App\Filament\Resources\SiteResource;
use App\Models\Site;
use App\Services\SiteServices\InitializeSiteService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewSite extends ViewRecord
{
    protected static string $resource = SiteResource::class;

    public $defaultAction = 'InitializeSite';


    public function InitializeSiteAction(): Action
    {
        return Action::make('InitializeSite')
            ->action(function (Site $site) {
                try {
                    if ($site->initialized) {
                        return;
                    }

                    $service = new InitializeSiteService();
                    $service->execute($site);

                    Notification::make()
                        ->title('Site Initialized')
                        ->body('The site has been initialized successfully.')
                        ->success()
                        ->send();

                    return redirect(self::getUrl([$site]));
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Failed to Initialize Site')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            ViewEntry::make('loading')
                ->columnSpanFull()
                ->view('test')->visible(fn(Site $site) => !$site->initialized),
            ViewEntry::make('domain')
                ->visible(fn(Site $site) => $site->initialized),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    //InitializeSiteAction
}
