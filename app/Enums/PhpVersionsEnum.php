<?php

namespace App\Enums;

enum PhpVersionsEnum: string
{
    case PHP84 = 'php8.4';
    case PHP83 = 'php8.3';
    case PHP82 = 'php8.2';
    case PHP81 = 'php8.1';
    case PHP80 = 'php8.0';
    case PHP74 = 'php7.4';
    case PHP73 = 'php7.3';
    case PHP72 = 'php7.2';
    case PHP71 = 'php7.1';
    case PHP70 = 'php7.0';
    case PHP56 = 'php5.6';

    public function label(): string
    {
        return match($this) {
            self::PHP84 => 'PHP 8.4',
            self::PHP83 => 'PHP 8.3',
            self::PHP82 => 'PHP 8.2',
            self::PHP81 => 'PHP 8.1',
            self::PHP80 => 'PHP 8.0',
            self::PHP74 => 'PHP 7.4',
            self::PHP73 => 'PHP 7.3',
            self::PHP72 => 'PHP 7.2',
            self::PHP71 => 'PHP 7.1',
            self::PHP70 => 'PHP 7.0',
            self::PHP56 => 'PHP 5.6',
        };
    }

    //Version of PHP
    public function version(): string
    {
        return match($this) {
            self::PHP84 => '8.4',
            self::PHP83 => '8.3',
            self::PHP82 => '8.2',
            self::PHP81 => '8.1',
            self::PHP80 => '8.0',
            self::PHP74 => '7.4',
            self::PHP73 => '7.3',
            self::PHP72 => '7.2',
            self::PHP71 => '7.1',
            self::PHP70 => '7.0',
            self::PHP56 => '5.6',
        };
    }
}
