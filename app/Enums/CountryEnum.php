<?php

namespace App\Enums;

enum CountryEnum: string
{
    use EnumFunctions;

    case ARMENIA = 'am';
    case GEORGIA = 'ge';
    case ALL = 'all';
}
