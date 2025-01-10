<?php

namespace App\Enums;

use App\Traits\Enum;

enum DatabaseTypesEnum: string
{
    use Enum;
//    case NONE = '';
    case MYSQL8 = 'mysql8';
//    case MARIADB1011 = 'mariadb1011';
//    case MARIADB114 = 'mariadb114';
//    case POSTGRES12 = 'postgres';
//    case POSTGRES13 = 'postgres13';
//    case POSTGRES14 = 'postgres14';
//    case POSTGRES15 = 'postgres15';
//    case POSTGRES16 = 'postgres16';
//    case POSTGRES17 = 'postgres17';

    public function label(): string
    {
        return match($this) {
//            self::NONE => 'None',
            self::MYSQL8 => 'MySQL 8',
            self::MARIADB1011 => 'MariaDB 10.11',
            self::MARIADB114 => 'MariaDB 11.4',
            self::POSTGRES12 => 'PostgreSQL 12',
            self::POSTGRES13 => 'PostgreSQL 13',
            self::POSTGRES14 => 'PostgreSQL 14',
            self::POSTGRES15 => 'PostgreSQL 15',
            self::POSTGRES16 => 'PostgreSQL 16',
            self::POSTGRES17 => 'PostgreSQL 17',
        };
    }
}
