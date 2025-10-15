<?php

namespace App\Enums;

enum CityEnum: string
{
    use EnumFunctions;

    case Yerevan = 'yerevan';
    case Gyumri = 'gyumri';
    case Dilijan = 'dilijan';
    case Tbilisi = 'tbilisi';
    case Batumi = 'batumi';
    case Kutaisi = 'kutaisi';
    case ALL = 'all';

    public static function am(): array
    {
        return [
            self::Yerevan,
            self::Gyumri,
            self::Dilijan,
            self::ALL,
        ];
    }

    public static function ge(): array
    {
        return [
            self::Tbilisi,
            self::Batumi,
            self::Kutaisi,
            self::ALL,
        ];
    }

    public static function all(): array
    {
        return [
            self::ALL,
        ];
    }
}
