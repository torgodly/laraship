<?php

namespace App\Filament\Clusters\Server\Resources;

use App\Filament\Clusters\Server;
use App\Filament\Clusters\Server\Resources\SourceResource\Pages;
use App\Filament\Clusters\Server\Resources\SourceResource\RelationManagers;
use App\Models\Source;
use Filament\Facades\Filament;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SourceResource extends Resource
{
    protected static ?string $model = Source::class;
    protected static ?string $navigationIcon = 'tabler-brand-github';

    protected static ?string $tenantOwnershipRelationshipName = 'owners';
    protected static ?string $cluster = Server::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()->schema([
                    TextInput::make('app_name')
                        ->label('Name')
                        ->required(),
                    TextInput::make('organization_name')
                        ->placeholder('If empty, your GitHub user will be used. e.g. Vortex')
                        ->label('Organization (on GitHub)')
                        ->hintIcon('tabler-alert-square-rounded')
                        ->hintColor('warning')
                        ->hintIconTooltip('This is the organization or user on GitHub that owns the app.')
                ]),
                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull()
                    ->placeholder('A short description of the app.'),
                Radio::make('owner')
                    ->dehydrated(false)
                    ->live()
                    ->columnSpanFull()
                    ->inline()
                    ->inlineLabel(false)
                    ->options([
                        'teams' => 'Teams',
                        'users' => 'Users',
                        'me' => 'Just me',
                    ])
                    ->descriptions([
                        'teams' => 'Share this source with a team.',
                        'users' => 'Share this source with a user.',
                        'me' => 'Keep this source private to yourself.',
                    ]),

                Select::make('teamOwners')
                    ->label('Teams')
                    ->columnSpanFull()
                    ->options(fn() => \Auth::user()->teams->pluck('name', 'id')->toArray())
                    ->default(fn() => [Filament::getTenant()->id])
                    ->multiple()
                    ->visible(fn($get) => $get('owner') === 'teams')
                    ->required(),
                Select::make('userOwners')
                    ->label('users')
                    ->columnSpanFull()
                    ->options(fn() => \Auth::user()->usersInSameTeams->pluck('name', 'id')->toArray())
                    ->multiple()
                    ->default([\Auth::user()->id])
                    ->visible(fn($get) => $get('owner') === 'users')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('app_name')
                    ->label('Name')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSources::route('/'),
            'view' => Pages\ViewSource::route('/{record}'),
        ];
    }
}
