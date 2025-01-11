<?php

namespace App\Filament\Resources\UserResource\Schemas;

use Filament\Tables\Columns\TextColumn;

class UserTable
{
    public static function get()
    {
        return [
            TextColumn::make('name')
                ->searchable(),
            TextColumn::make('email')
                ->searchable(),
            TextColumn::make('roles')
                ->badge()
                ->searchable(),
        ];
    }
}
