<?php

namespace App\Enums;

enum EventStatusEnum: int
{
    use EnumFunctions;

    case PENDING = 1;
    case ACCEPTED = 2;
    case DECLINED = 3;
    case DELETED = 4;
}
