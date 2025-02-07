<?php

namespace App\Filament\Clusters\Server\Resources\SourceResource\Pages;

use App\Filament\Clusters\Server\Resources\SourceResource;
use App\Infolists\Components\MaskedTextEntry;
use CodeWithDennis\SimpleAlert\Components\Infolists\SimpleAlert;
use Filament\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewSource extends ViewRecord
{
    protected static string $resource = SourceResource::class;

    public function getHeading(): string|Htmlable
    {
        return 'GitHub App' . $this->record->app_name;
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Your Private GitHub App for private repositories.';
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Group::make([
                    SimpleAlert::make('example')
                        ->columnSpanFull()
                        ->danger()
                        ->title('You must complete this step before you can use this source!'),
                    Section::make('Details')->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('app_name'),
                            TextEntry::make('Webhook_Endpoint')
                                ->label('Webhook Endpoint')
                                ->icon('tabler-help')
                                ->iconPosition('after')
                                ->iconColor('yellow')
                                ->tooltip('This is the endpoint that will receive the webhook data.')
                                ->default('https://example.com/webhook'),
                            \Filament\Infolists\Components\Actions::make([
                                Action::make('register')
                                    ->label('Register Now')
                                    ->color('primary') // Better conveys starting or initiating an action
                                    ->icon('tabler-plus') // A plus icon to signify adding or registering
                                    ->iconPosition('after')
                                    ->url(route('github.create-app', ['source' => $this->record->uuid]))
                                    ->visible(!$this->record->is_registered),
                                Action::make('install')
                                    ->label('Install Now')
                                    ->color('success') // Green color for a successful or positive action
                                    ->icon('tabler-download') // Download icon for installation action
                                    ->iconPosition('after')
                                    ->url("https://github.com/apps/" . $this->record->app_name . "/installations/new")
                                    ->visible($this->record->is_registered)

                            ])

                        ]),

                    ])
                ])->columnSpanFull()->visible(!$this->record->isCompleted),

                Group::make([
                    Section::make('Details')->schema([
                        Grid::make(3)->schema([
                            MaskedTextEntry::make('client_id')
                                ->label('Client ID'),
                            TextEntry::make('client_secret')
                                ->label('Client Secret'),
                            TextEntry::make('webhook_secret')
                                ->label('Webhook Secret'),
                        ]),

                    ])
                ])->visible($this->record->isCompleted)->columnSpanFull()

            ]);
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
