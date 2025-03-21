<?php

namespace App\Filament\Resources\SiteResource\Pages;

use App\Filament\Resources\SiteResource;
use App\Models\Site;
use App\Services\SiteServices\InitializeSiteService;
use App\Services\SiteServices\InstallSiteService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
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
                if (!$site->isPending) {
                    return; // Early return if already initialized
                }

                try {
                    // Execute the site initialization
                    (new InitializeSiteService())->execute($site);

                    // Send success notification
                    Notification::make()
                        ->title('Site Initialized')
                        ->body('The site has been initialized successfully.')
                        ->success()
                        ->send();

                    // Return redirection to the site URL
                    return redirect(self::getUrl([$site]));
                } catch (\Exception $e) {
                    // Send failure notification
                    Notification::make()
                        ->title('Failed to Initialize Site')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();

                    // Return to the previous page or handle the error gracefully
                    return back();
                }
            });
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            ViewEntry::make('loading')
                ->columnSpanFull()
                ->view('test')->visible(fn(Site $site) => $site->isPending),
            TextEntry::make('domain')
                ->columnSpanFull()
                ->visible(fn(Site $site) => !$site->isPending),
            \Filament\Infolists\Components\Actions::make([
                \Filament\Infolists\Components\Actions\Action::make('install_site')
                    ->label('Install Site')
                    ->action(function (Site $site) {
                        try {
                            // Execute the site installation
                            (new InstallSiteService())->execute($site);

                            // Send success notification
                            Notification::make()
                                ->title('Site Installed')
                                ->body('The site has been installed successfully.')
                                ->actions([
                                    \Filament\Notifications\Actions\Action::make('View Site')
                                        ->url($site->url)
                                        ->openUrlInNewTab(),
                                ])
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Failed to Install Site')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                            return back();
                        }
                    }),

            ]),
//            TextEntry::make('domain')
//                ->visible(fn(Site $site) => $site->initialized),
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
