<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\Schemas\UserForm;
use App\Filament\Resources\UserResource\Schemas\UserTable;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'tabler-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(UserForm::get());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(UserTable::get())
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->requiresConfirmation()
                    ->icon('heroicon-o-pencil')
                    ->modalIcon('heroicon-o-pencil'),
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
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}
