<?php

namespace App\Filament\Clusters\Server\Pages;

use App\Filament\Clusters\Server;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Page;

class Php extends Page implements HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'tabler-brand-php';

    protected static string $view = 'filament.clusters.server.pages.php';

    protected static ?string $cluster = Server::class;

    //install php Action
    public function installPhpAction(): Action
    {
        return Action::make('installPhp');
    }

    //patch php Action
    public function patchPhpAction(): Action
    {
        return Action::make('patchPhp')
            ->label('Patch')
            ->link();
    }
}
