<?php

namespace App\Filament\Clusters\Server\Pages;

use App\Filament\Clusters\Server;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Riodwanto\FilamentAceEditor\AceEditor;

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

    //set as default php Action
    public function setAsDefaultPhpAction(): Action
    {
        return Action::make('setAsDefaultPhp')
            ->label('Set as Default');
    }

    //removePhpAction
    public function removePhpAction(): Action
    {
        return Action::make('removePhp')
            ->label('Remove');
    }

    //phpInfoAction
    public function phpInfoAction(): Action
    {
        return Action::make('phpInfo')
            ->label('PHP Info');
    }

    //phpExtensionsAction
    public function phpExtensionsAction(): Action
    {
        return Action::make('phpExtensions')
            ->label('Extensions');
    }

    //editPhpIniAction
    public function editPhpIniAction(): Action
    {
        return Action::make('editPhpIni')
            ->fillForm(function ($arguments) {
                return [
                    'php_ini' => file_get_contents($arguments['ini_path']),
                ];
            })
            ->modalHeading(fn($arguments) => 'Edit ' . $arguments['php_label'] . ' ini Configuration')
            ->form([
                AceEditor::make('php_ini')
                    ->hiddenLabel()
                    ->height('48rem')
                    ->mode('php')
                    ->theme('dracula'),
            ])
            ->action(function ($data, $arguments) {
                file_put_contents($arguments['ini_path'], $data['php_ini']);
                Notification::make()
                    ->title('PHP ini Configuration Updated')
                    ->body('The PHP ini configuration has been updated.')
                    ->send();
            });
    }

    //phpFpmConfigAction
    public function phpFpmConfigAction(): Action
    {
        return Action::make('phpFpmConfig')
            ->label('FPM Config');
    }

    //phpFpmRestartAction
    public function phpFpmRestartAction(): Action
    {
        return Action::make('phpFpmRestart')
            ->label('Restart FPM');
    }


    //patch php Action
    public function patchPhpAction(): Action
    {
        return Action::make('patchPhp')
            ->label('Patch')
            ->link();
    }
}
