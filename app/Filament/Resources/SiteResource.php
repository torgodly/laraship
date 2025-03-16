<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Server;
use App\Filament\Clusters\SitesCluster;
use App\Filament\Resources\SiteResource\Pages;
use App\Filament\Resources\SiteResource\RelationManagers;
use App\Filament\Resources\SiteResource\Schemas\SiteForm;
use App\Models\Site;
use Filament\Actions\EditAction;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Table;

class SiteResource extends Resource
{
    protected static ?string $model = Site::class;
    protected static ?string $cluster = Server::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(SiteForm::schema())
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(
                [
                    Tables\Columns\TextColumn::make('domain')
                        ->description('No App Installed')
                        ->label('Site')
                        ->size(TextColumnSize::Medium)
                        ->weight(FontWeight::Bold)
                        ->color('primary'),
                    Tables\Columns\TextColumn::make('team.name')
                        ->label('Team')
                        ->badge(),
                    Tables\Columns\TextColumn::make('php_version')
                        ->formatStateUsing(fn($state) => str_replace('php', '', $state))
                        ->label('PHP'),
                    //Deployed
                    Tables\Columns\TextColumn::make('status')
                        ->placeholder('Never Deployed')
                        ->badge()
                        ->label('Deployed'),
                    //initialized
                ],
            )
            ->actions([
                Tables\Actions\EditAction::make()
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
            'index' => Pages\ListSites::route('/'),
            'view' => Pages\ViewSite::route('/{record}'),
        ];
    }
}
