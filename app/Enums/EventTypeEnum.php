<?php

namespace App\Enums;

enum EventTypeEnum: int
{
    use EnumFunctions;

    case CONCERTS_EVENTS = 1;
    case CONCERT = 2;
    case EVENT = 3;
}
