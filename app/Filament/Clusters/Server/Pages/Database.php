<?php

namespace App\Filament\Clusters\Server\Pages;

use App\Filament\Clusters\Server;
use App\Rules\DatabaseDoesNotExist;
use App\Rules\UserDoesNotExist;
use App\Services\DatabaseServices\CreateDatabaseService;
use App\Services\DatabaseServices\CreateDatabaseUserService;
use App\Services\DatabaseServices\RemoveDatabaseService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\DeleteAction;
use Filament\Actions\MountableAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @property mixed $createDatabaseForm
 * @property mixed $addDatabaseUserForm
 */
class Database extends Page
{
    use InteractsWithActions;
    use InteractsWithForms;


    protected static ?string $navigationIcon = 'tabler-database';
    protected static string $view = 'filament.clusters.server.pages.database';
    protected static ?string $cluster = Server::class;
    public ?array $databaseData = [];
    public ?array $databaseUser = [
        'databases' => [], // Initialize databases as an array
    ];

    public function getBreadcrumbs(): array
    {
        return [
            Server::getSlug() => Server::getSlug(),
            __('Database') => static::getSlug(),
        ];
    }


    public function createDatabaseForm(Form $form): Form
    {
        return $form->schema([
            TextInput::make('database')
                ->label(__('Database Name'))
                ->unique('databases', 'name')
                ->rules(['regex:/^[a-zA-Z0-9_]+$/', 'max:64', 'unique:databases,name'])
                ->required()
                ->placeholder(__('Enter the database name')),
            TextInput::make('username')
                ->unique('db_users', 'username')
                ->rules(['nullable', 'regex:/^[a-zA-Z0-9_\-.]+$/', 'max:32'])
                ->label(__('Username (optional)'))
                ->helperText(__('Leave empty to use the default username (laraship)'))
                ->placeholder(__('Enter the username')),
            TextInput::make('password')
                ->label(__('Password (optional)'))
                ->requiredWith('username')
                ->minLength(8)
                ->placeholder(__('Enter the password'))
                ->suffixAction(
                    FormAction::make('generate')
                        ->icon('tabler-refresh')
                        ->Action(fn(Set $set) => $set('password', Str::random()))
                ),

        ])->statePath('databaseData');
    }

    //Add Database UserFrom
    public function addDatabaseUserForm(Form $form): Form
    {
        return $form->schema([
            TextInput::make('username')
                ->label(__('Username'))
                ->unique('db_users', 'username')
                ->rules(['regex:/^[a-zA-Z0-9_\-.]+$/', 'max:32'])
                ->required()
                ->placeholder(__('Enter the username')),
            TextInput::make('password')
                ->label(__('Password'))
                ->required()
                ->placeholder(__('Enter the password'))
                ->suffixAction(
                    FormAction::make('generate')
                        ->icon('tabler-refresh')
                        ->Action(fn(Set $set) => $set('password', Str::random()))
                ),
            Select::make('databases')
                ->label(__('Database'))
                ->placeholder(__('Select the database'))
                ->multiple()
                ->options(fn() => $this->getDatabases()->pluck('name', 'name')->toArray())
        ])->statePath('databaseUser');
    }

    /**
     * @throws \Exception
     */
    public function createDatabase(): void
    {
        $data = $this->createDatabaseForm->getState();
        $createDatabaseService = new CreateDatabaseService();
        try {
            $results = $createDatabaseService->execute($data['database'], $data['username'], $data['password']);
            $results = explode("\n", trim($results));
            foreach ($results as $result) {
                Notification::make()
                    ->title("Provisioning Database")
                    ->body($result)
                    ->success()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title("Provisioning Database")
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

    }

    public function addDatabaseUser(): void
    {
        //TODO: create a new database user Logic here
        $data = $this->addDatabaseUserForm->getState();
        $createDatabaseUserService = new CreateDatabaseUserService();
        try {
            $results = $createDatabaseUserService->execute($data['username'], $data['password'], $data['databases']);
            $results = explode("\n", trim($results));
            foreach ($results as $result) {
                Notification::make()
                    ->title("Provisioning Database User")
                    ->body($result)
                    ->success()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title("Provisioning Database User")
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

    }

    public function getDatabases(): Collection
    {
        //TODO: implement getDatabases method
        return Filament::getTenant()->databases;
    }

    public function getDatabaseUsers(): Collection
    {
        //TODO: implement getDatabases method
        return Filament::getTenant()->databaseUsers;
    }

    public function removeDatabaseAction(): Action
    {
        return DeleteAction::make('removeDatabase')
            ->link()
            ->label(__('Remove'))
            ->color('danger')
            ->modalDescription(fn($record) => new \Illuminate\Support\HtmlString(__('Please type the name of the database <strong>(:name)</strong> to confirm its removal. This action is irreversible.', ['name' => $record->name])))
            ->form([
                TextInput::make('name')
                    ->label(__('Database Name'))
                    ->in(fn($record) => $record->name)
                    ->required()
                    ->placeholder(__('Enter the database name')),
            ])
            ->record(fn($arguments) => \App\Models\Database::find($arguments['database']))
            ->action(function ($record, MountableAction $action) {
                $removeDatabaseService = new RemoveDatabaseService();
                try {
                    $results = $removeDatabaseService->execute($record);
                    $results = explode("\n", trim($results));
                    foreach ($results as $result) {
                        Notification::make()
                            ->title("Removing Database")
                            ->body($result)
                            ->success()
                            ->send();
                    }
                } catch (\Exception $e) {
                    Notification::make()
                        ->title("Removing Database")
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    //edit Database UserAction
    public function editDatabaseUserAction(): Action
    {
        return Action::make('editDatabaseUser')
            ->label('Edit')
            ->link()

            ->label(__('Edit Database User'));
    }

    protected function getForms(): array
    {
        return [
            'createDatabaseForm',
            'addDatabaseUserForm',
        ];
    }


    protected function getFormActions(?string $label = 'save'): array
    {
        return [
            Action::make('save')
                ->label(__($label))
                ->submit('save'),
        ];
    }


}
