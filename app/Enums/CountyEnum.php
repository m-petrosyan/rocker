<?php

namespace App\Enums;

enum CountyEnum: string
{
    use EnumFunctions;

    case ARMENIA = 'am';
    case GEORGIA = 'ge';
    case ALL = 'all';
}
