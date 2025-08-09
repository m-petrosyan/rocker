<?php

namespace App\Enums;

enum LanguageEnum: string
{
    use EnumFunctions;

    case ENGLISH = 'en';
    case RUSSIAN = 'ru';
}
