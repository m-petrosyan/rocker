<?php

namespace App\Enums;

enum EventStatusEnum: string
{
    use EnumFunctions;

    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case DECLINED = 'declined';
    case DELETED = 'deleted';
}
