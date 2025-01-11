<?php

namespace App\Filament\Resources\UserResource\Schemas;

use Filament\Tables\Columns\TextColumn;

class UserTable
{
    public static function schema(): array
    {
        return [
            TextColumn::make('name')
                ->searchable(),
            TextColumn::make('email')
                ->searchable(),
            TextColumn::make('teams.name')
                ->badge()
                ->searchable(),
        ];
    }
}
