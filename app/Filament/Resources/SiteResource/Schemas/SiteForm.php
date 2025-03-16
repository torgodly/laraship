<?php

namespace App\Filament\Resources\SiteResource\Schemas;

use App\Actions\GithubActions\GetBranchesFromRepoAction;
use App\Actions\PhpActions\ListPhpVersionsAction;
use App\Actions\SourceActions\GetRepositoryBranches;
use App\Enums\PhpVersionsEnum;
use App\Enums\SiteTypes;
use App\Filament\Clusters\Server\Resources\SourceResource\Pages\ListSources;
use App\Models\Repository;
use App\Models\Source;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;

class SiteForm
{
    public static function schema()
    {
        return [

            Wizard::make([
                Step::make('Server')
                    ->schema([
                        Grid::make(5)->schema([
                            TextInput::make('domain')
                                ->live()
                                ->afterStateUpdated(fn($set, $get) => $set('aliases', $get('wildcard') ? ['*.' . $get('domain')] : []))
                                ->placeholder('example.com')
                                ->unique(ignoreRecord: true)
                                ->columnSpan(3)
                                ->required(),
                            Select::make('type')
                                ->placeholder('Select Type')
                                ->label('Project Type')
                                ->native(false)
                                ->default(SiteTypes::php->value)
                                ->options(fn() => (new ListPhpVersionsAction())->execute())
                                ->live()
                                ->afterStateUpdated(fn($state, $set) => $set('web_directory', $state === SiteTypes::php->value ? '/public' : '/'))
                                ->columnSpan(2)
                                ->required(),
                        ]),
                        Grid::make(5)->schema([
                            TagsInput::make('aliases')
                                ->live()
                                ->placeholder('sub.example.com, www.example.com')
                                ->columnSpan(3),
                            Select::make('team_id')
                                ->placeholder('Select Team')
                                ->searchable()
                                ->preload()
                                ->relationship('team', 'name')
                                ->default(Filament::getTenant()->id)
                                ->required()
                                ->columnSpan(2),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('web_directory')
                                ->required()
                                ->maxLength(255)
                                ->default('/public'),
                            Select::make('php_version')
                                ->searchable()
                                ->default(PhpVersionsEnum::PHP84->value)
                                ->options(collect(PhpVersionsEnum::cases())->mapWithKeys(fn($version) => [$version->value => $version->label()]))
                                ->required(),
                        ]),
                        Group::make()->schema([
                            Grid::make(3)->schema([
                                Toggle::make('wildcard')
                                    ->live()
                                    ->afterStateUpdated(fn($set, $get) => $set('aliases', $get('domain') ? ['*.' . $get('domain')] : []))
                                    ->required(),
                                Toggle::make('isolation')
                                    ->required(),
                                Group::make()->schema([
                                    Toggle::make('create_database')
                                        ->live()
                                        ->required(),
                                    TextInput::make('database_name')
                                        ->visible(fn($get) => $get('create_database'))
                                        ->maxLength(255),
                                ]),

                            ]),

                        ])->columnSpan(['lg' => 3]),
                    ]),
                Step::make('Providers')
                    ->schema([
                        ToggleButtons::make('Providers')
                            ->dehydrated(false)
                            ->live()
                            ->hint('Connect additional source control providers in your')
                            ->hintAction(
                                Action::make('source_settings')
                                    ->label('Source settings.')
                                    ->url(ListSources::getUrl())
                            )
                            ->inline()
                            ->default('github')
                            ->view('components.custom-filament.Form.Inputs.toggle-buttons')
                            ->icons([
                                'github' => 'bi-github',
                                'custom' => 'bi-git',
                            ])
                            ->options([
                                'github' => 'Github',
                                'custom' => 'Custom',
                            ])
                    ]),
                Step::make('Repository')
                    ->schema([
                        Group::make([
                            Select::make('source')
                                ->live()
                                ->options(Source::pluck('app_name', 'id')),
                            Grid::make(3)->schema([
                                Select::make('repository')
                                    ->live()
                                    ->searchable()
                                    ->columnSpan(2)
                                    ->options(function ($get) {
                                        if ($get('source')) {
                                            Repository::setSource(Source::find($get('source')));
                                            return Repository::all()->pluck('name', 'id');
                                        }
                                        return [];
                                    }),
                                Select::make('branch')
                                    ->live()
                                    ->searchable()
                                    ->default('main')
                                    ->disabled(fn($get) => !$get('repository'))
                                    ->options(function ($get) {
                                        if ($get('repository')) {
                                            Repository::setSource(Source::find($get('source')));
                                            $repository = Repository::find($get('repository'));
                                            return (new GetRepositoryBranches($repository))->execute();
                                        }
                                        return [];
                                    }),
                            ])
                        ])->visible(fn($get) => $get('Providers') === 'github'),
                        Group::make([
                            Grid::make(3)->schema([
                                TextInput::make('repository')
                                    ->live()
                                    ->required()
                                    ->columnSpan(2),
                                Select::make('branch')
                                    ->live()
                                    ->default('main')
                                    ->disabled(fn($get) => !$get('repository'))
                                    ->options(function ($get) {
                                        $repository = $get('repository');
                                        if ($repository) {
                                            return (new GetBranchesFromRepoAction())->execute($repository);
                                        }
                                        return [];
                                    }),
                            ])
                        ])->visible(fn($get) => $get('Providers') === 'custom'),
                    ])
            ])->columnSpanFull()
        ];
    }
}
