<?php

namespace App\Enums;

use App\Actions\PhpActions\ListPhpVersionsAction;
use App\Traits\Enum;
use Illuminate\Support\Facades\Cache;

enum PhpVersionsEnum: string
{
    use Enum;

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

    public static function installed(): array
    {
        return Cache::remember('php_versions_installed', now()->addMinutes(60), function () {
            return (new ListPhpVersionsAction())->execute();
        });
    }

    //Version of PHP

    public function label(): string
    {
        return match ($this) {
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

    //installed php versions

    public function version(): string
    {
        return match ($this) {
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

//    isInstalled
    public function isInstalled(): bool
    {
        return in_array($this->value, self::installed(), true);
    }

    //is Default
    public function isDefault(): bool
    {
        $php = shell_exec('php -v');
        return str_contains($php, $this->version());
    }

    //get ini path
    public function getCliConfigPath(): string
    {
        return shell_exec($this->value . ' -r "echo php_ini_loaded_file();"');
    }
    //get FPM Configuration Path
    public function getFpmConfigPath(): string
    {
        return '/etc/php/' . $this->version() . '/fpm/php-fpm.conf';
    }
}
