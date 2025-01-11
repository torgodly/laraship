<?php

namespace App\Filament\Resources\SiteResource\Schemas;

use App\Enums\PhpVersionsEnum;
use App\Enums\SiteTypes;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;

class SiteForm
{
    public static function schema(): array
    {
        return [
            Group::make()->schema([
                Grid::make()->schema([
                    TextInput::make('domain')
                        ->placeholder('example.com')
                        ->columnSpanFull()
                        ->required(),
                    TagsInput::make('aliases')
                        ->placeholder('sub.example.com, www.example.com')
                        ->columnSpanFull(),
                    TextInput::make('web_directory')
                        ->required()
                        ->maxLength(255)
                        ->default('/public'),
                    Select::make('php_version')
                        ->searchable()
                        ->default(PhpVersionsEnum::PHP83->value)
                        ->options(collect(PhpVersionsEnum::cases())->mapWithKeys(fn($version) => [$version->value => $version->label()]))
                        ->required(),
                ])
            ])->columnSpan(['lg' => 2]),
            Group::make()->schema([
                Select::make('type')
                    ->placeholder('Select Type')
                    ->label('Project Type')
                    ->native(false)
                    ->default(SiteTypes::php->value)
                    ->options(collect(SiteTypes::cases())->mapWithKeys(fn($version) => [$version->value => $version->label()]))
                    ->required(),
                Select::make('team_id')
                    ->placeholder('Select Team')
                    ->searchable()
                    ->preload()
                    ->relationship('team', 'name')
                    ->required(),
            ])->columnSpan(['lg' => 1]),
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
                            ->visible(fn(Get $get) => $get('create_database'))
                            ->maxLength(255),
                    ]),

                ]),

            ])->columnSpan(['lg' => 3]),

        ];
    }
}
