<?php

namespace App\Filament\Resources\SiteResource\Schemas;

use App\Actions\SourceActions\GetRepositoryBranches;
use App\Enums\PhpVersionsEnum;
use App\Enums\SiteTypes;
use App\Models\Repository;
use App\Models\Source;
use Filament\Facades\Filament;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;

class SiteForm
{
//    public static function schema(): array
//    {
//        return [
//            Wizard::make([
//                Step::make('Server')
//                    ->schema([
//                        Group::make()->schema([
//                            Grid::make()->schema([
//                                TextInput::make('domain')
//                                    ->placeholder('example.com')
//                                    ->columnSpanFull()
//                                    ->required(),
//                                TagsInput::make('aliases')
//                                    ->placeholder('sub.example.com, www.example.com')
//                                    ->columnSpanFull(),
//                                TextInput::make('web_directory')
//                                    ->required()
//                                    ->maxLength(255)
//                                    ->default('/public'),
//                                Select::make('php_version')
//                                    ->searchable()
//                                    ->default(PhpVersionsEnum::PHP83->value)
//                                    ->options(collect(PhpVersionsEnum::cases())->mapWithKeys(fn($version) => [$version->value => $version->label()]))
//                                    ->required(),
//                            ])
//                        ])->columnSpan(['lg' => 2]),
//                        Group::make()->schema([
//                            Select::make('type')
//                                ->placeholder('Select Type')
//                                ->label('Project Type')
//                                ->native(false)
//                                ->default(SiteTypes::php->value)
//                                ->options(collect(SiteTypes::cases())->mapWithKeys(fn($version) => [$version->value => $version->label()]))
//                                ->required(),
//                            Select::make('team_id')
//                                ->placeholder('Select Team')
//                                ->searchable()
//                                ->preload()
//                                ->default(Filament::getTenant()->id)
//                                ->relationship('team', 'name')
//                                ->required(),
//                        ])->columnSpan(['lg' => 1]),
//                        Group::make()->schema([
//                            Grid::make(3)->schema([
//                                Toggle::make('wildcard')
//                                    ->required(),
//                                Toggle::make('isolation')
//                                    ->required(),
//                                Group::make()->schema([
//                                    Toggle::make('create_database')
//                                        ->live()
//                                        ->required(),
//                                    TextInput::make('database_name')
//                                        ->visible(fn(Get $get) => $get('create_database'))
//                                        ->maxLength(255),
//                                ]),
//
//                            ]),
//
//                        ])->columnSpan(['lg' => 3]),
//                    ]),
//                Step::make('Order')
//                    ->schema([
//                        Group::make()->schema([
//                            Grid::make()->schema([
//                                TextInput::make('domain')
//                                    ->placeholder('example.com')
//                                    ->columnSpanFull()
//                                    ->required(),
//                                TagsInput::make('aliases')
//                                    ->placeholder('sub.example.com, www.example.com')
//                                    ->columnSpanFull(),
//                                TextInput::make('web_directory')
//                                    ->required()
//                                    ->maxLength(255)
//                                    ->default('/public'),
//                                Select::make('php_version')
//                                    ->searchable()
//                                    ->default(PhpVersionsEnum::PHP83->value)
//                                    ->options(collect(PhpVersionsEnum::cases())->mapWithKeys(fn($version) => [$version->value => $version->label()]))
//                                    ->required(),
//                            ])
//                        ])->columnSpan(['lg' => 2]),
//                        Group::make()->schema([
//                            Select::make('type')
//                                ->placeholder('Select Type')
//                                ->label('Project Type')
//                                ->native(false)
//                                ->default(SiteTypes::php->value)
//                                ->options(collect(SiteTypes::cases())->mapWithKeys(fn($version) => [$version->value => $version->label()]))
//                                ->required(),
//                            Select::make('team_id')
//                                ->placeholder('Select Team')
//                                ->searchable()
//                                ->preload()
//                                ->relationship('team', 'name')
//                                ->required(),
//                        ])->columnSpan(['lg' => 1]),
//                        Group::make()->schema([
//                            Grid::make(3)->schema([
//                                Toggle::make('wildcard')
//                                    ->required(),
//                                Toggle::make('isolation')
//                                    ->required(),
//                                Group::make()->schema([
//                                    Toggle::make('create_database')
//                                        ->live()
//                                        ->required(),
//                                    TextInput::make('database_name')
//                                        ->visible(fn(Get $get) => $get('create_database'))
//                                        ->maxLength(255),
//                                ]),
//
//                            ]),
//
//                        ])->columnSpan(['lg' => 3]),
//                    ]),
//
//
//            ])->columnSpanFull(),
//        ];
//    }


    public static function schema()
    {
        return [
            Wizard::make([
                Step::make('Server')
                    ->schema([
                        Grid::make(5)->schema([
                            TextInput::make('domain')
                                ->placeholder('example.com')
                                ->columnSpan(3)
                                ->required(),
                            Select::make('type')
                                ->placeholder('Select Type')
                                ->label('Project Type')
                                ->native(false)
                                ->default(SiteTypes::php->value)
                                ->options(collect(SiteTypes::cases())->mapWithKeys(fn($version) => [$version->value => $version->label()]))
                                ->live()
                                ->afterStateUpdated(fn($state, $set) => $set('web_directory', $state === SiteTypes::php->value ? '/public' : '/'))
                                ->columnSpan(2)
                                ->required(),
                        ]),
                        Grid::make(5)->schema([
                            TagsInput::make('aliases')
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
                                ->default(PhpVersionsEnum::PHP83->value)
                                ->options(collect(PhpVersionsEnum::cases())->mapWithKeys(fn($version) => [$version->value => $version->label()]))
                                ->required(),
                        ]),
                        Group::make()->schema([
                            Grid::make(3)->schema([
                                Toggle::make('wildcard')
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
                Step::make('source')
                    ->schema([
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
                                        $repository = Repository::find($get('repository'));
                                        return (new GetRepositoryBranches($repository))->execute();
                                    }
                                    return [];
                                }),
                        ])
                    ])
            ])->columnSpanFull()->startOnStep(2),


        ];
    }
}
