<?php

namespace App\Enums;

enum EventTypeEnum: string
{
    use EnumFunctions;

    case CONCERTS_EVENTS = 'concerts_events';
    case CONCERT = 'concert';
    case EVENT = 'event';
}
