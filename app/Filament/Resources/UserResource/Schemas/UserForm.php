<?php

namespace App\Filament\Resources\UserResource\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;

class UserForm
{
    public static function get()
    {
        return [
            TextInput::make('name')
                ->columnSpanFull()
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->columnSpanFull()
                ->email()
                ->required()
                ->maxLength(255),
            Select::make('roles')
                ->relationship('roles', 'name')
                ->multiple()
                ->preload()
                ->searchable(),
            Checkbox::make('change_password')
                ->dehydrated(false)
                ->hint('check to change password')
                ->live()
                ->columnSpanFull(),
            TextInput::make('password')
                ->visible(fn(Get $get) => $get('change_password'))
                ->columnSpanFull()
                ->password()
                ->required()
                ->maxLength(255),
            TextInput::make('password_confirmation')
                ->visible(fn(Get $get) => $get('change_password'))
                ->columnSpanFull()
                ->password()
                ->required()
                ->maxLength(255)
                ->same('password')
                ->label('Confirm Password'),

        ];
    }
}
