<?php

namespace App\Filament\Clusters\Server\Pages;

use App\Filament\Clusters\Server;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Pages\Page;
use Illuminate\Support\Str;

class Database extends Page
{
    use InteractsWithActions;
    use InteractsWithForms;


    protected static ?string $navigationIcon = 'tabler-database';
    protected static string $view = 'filament.clusters.server.pages.database';
    protected static ?string $cluster = Server::class;
    public ?array $databaseData = [];
    public ?array $databaseUser = [];

    public function getBreadcrumbs(): array
    {
        return [
            Server::getSlug() => Server::getSlug(),
            __('Database') => static::getSlug(),
        ];
    }

    //create a new database
    public function createDatabaseForm(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label(__('Database Name'))
                ->required()
                ->placeholder(__('Enter the database name')),
            TextInput::make('username')
                ->label(__('Username (optional)'))
                ->required()
                ->placeholder(__('Enter the username')),
            TextInput::make('password')
                ->label(__('Password (optional)'))
                ->required()
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
                ->options($this->getDatabases())
        ])->statePath('databaseUser');
    }

    public function createDatabase(): void
    {
        //TODO: create a new database Logic here
    }

    public function addDatabaseUser(): void
    {
        //TODO: create a new database user Logic here
    }

    public function getDatabases(): array
    {
        //TODO: implement getDatabases method
        return [
            'forage',
            'forage2',
            'forage3'
        ];
    }
    public function getDatabaseUsers(): array
    {
        //TODO: implement getDatabases method
        return [
            'laraship',
            'laraship2',
            'laraship4'
        ];
    }

    public function removeDatabaseAction(): Action
    {
        return Action::make('removeDatabase')
            ->link()
            ->color('red')
            ->label(__('Remove Database'));
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
