<?php

namespace App\Filament\Clusters\Server\Pages;

use App\Actions\Common\UpdateFileContentAction;
use App\Actions\PhpActions\InstallPhpAction;
use App\Actions\PhpActions\SetPhpVersionAsDefaultAction;
use App\Actions\PhpActions\UninstallPhpAction;
use App\Actions\PhpActions\UpdatePhpIniFileAction;
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
        return Action::make('installPhp')
            ->requiresConfirmation()
            ->modalDescription(fn($arguments) => 'Are you sure you want to install ' . $arguments['php_label'] . '?')
            ->modalIcon('tabler-brand-php')
            ->action(function ($arguments) {
                $installPhpAction = new InstallPhpAction();
                $output = $installPhpAction->execute($arguments['php_version']);
                Notification::make()
                    ->title('PHP Installed')
                    ->body($output)
                    ->send();
            });
    }

    //set as default php Action
    public function setAsDefaultPhpAction(): Action
    {
        return Action::make('setAsDefaultPhp')
            ->label('Set as Default')
            ->requiresConfirmation()
            ->modalDescription(fn($arguments) => 'Are you sure you want to set ' . $arguments['php_label'] . ' as the default PHP version?')
            ->modalIcon('tabler-brand-php')
            ->action(function ($arguments) {
                $setPhpVersionAsDefaultAction = new SetPhpVersionAsDefaultAction();
                $output = $setPhpVersionAsDefaultAction->execute($arguments['php_version']);
                Notification::make()
                    ->title('PHP Version Set as Default')
                    ->body($output)
                    ->send();
            });
    }

    //uninstallPhpAction
    public function uninstallPhpAction(): Action
    {
        return Action::make('uninstallPhp')
            ->label('Uninstall')
            ->requiresConfirmation()
            ->modalDescription(fn($arguments) => 'Are you sure you want to uninstall ' . $arguments['php_label'] . '?')
            ->modalIcon('tabler-brand-php')
            ->action(function ($arguments) {
                $uninstallPhpAction = new UninstallPhpAction();
                $output = $uninstallPhpAction->execute($arguments['php_version']);
                Notification::make()
                    ->title('PHP Uninstalled')
                    ->body($output)
                    ->send();
            });
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


    //EditphpFpmConfigAction
    public function editPhpFpmConfigAction(): Action
    {
        return Action::make('editPhpFpmConfig')
            ->label('Edit FPM Configuration')
            ->fillForm(function ($arguments) {
                return [
                    'config_content' => file_get_contents($arguments['config_path']),
                ];
            })
            ->modalHeading(fn($arguments) => 'Edit ' . $arguments['php_label'] . ' FPM Configuration')
            ->form([
                AceEditor::make('config_content')
                    ->hiddenLabel()
                    ->height('48rem')
                    ->mode('ini')
                    ->theme('dracula'),
            ])
            ->action(function ($data, $arguments) {
                $updatePhpIniFileAction = new UpdateFileContentAction();
                $output = $updatePhpIniFileAction->execute($arguments['config_path'], $data['config_content']);
                Notification::make()
                    ->title('PHP FPM Configuration Updated')
                    ->body($output)
                    ->send();
            });
    }

    //EditphpCliConfigAction
    public function editPhpCliConfigAction(): Action
    {
        return Action::make('editPhpCliConfig')
            ->label('Edit CLI Configuration')
            ->fillForm(function ($arguments) {
                return [
                    'config_content' => file_get_contents($arguments['config_path']),
                ];
            })
            ->modalHeading(fn($arguments) => 'Edit ' . $arguments['php_label'] . ' CLI Configuration')
            ->form([
                AceEditor::make('config_content')
                    ->hiddenLabel()
                    ->height('48rem')
                    ->mode('ini')
                    ->theme('dracula'),
            ])
            ->action(function ($data, $arguments) {
                $updatePhpIniFileAction = new UpdateFileContentAction();
                $output = $updatePhpIniFileAction->execute($arguments['config_path'], $data['config_content']);
                Notification::make()
                    ->title('PHP CLI Configuration Updated')
                    ->body($output)
                    ->send();
            });
    }

    //phpFpmRestartAction
    public function phpFpmRestartAction(): Action
    {
        return Action::make('phpFpmRestart')
            ->label('Restart FPM');
    }


    //patch php Action
    public function updatePhpAction(): Action
    {
        return Action::make('updatePhp')
            ->label('Update')
            ->requiresConfirmation()
            ->link()
            ->modalDescription(fn($arguments) => 'Are you sure you want to update ' . $arguments['php_label'] . '?')
            ->modalIcon('tabler-brand-php')
            ->action(function ($arguments) {
                $installPhpAction = new InstallPhpAction();
                $output = $installPhpAction->execute($arguments['php_version']);
                Notification::make()
                    ->title('PHP Updated')
                    ->body($output)
                    ->send();
            });
    }
}
