<?php

namespace App\Enums;

enum EventStatusEnum: string
{
    use EnumFunctions;

    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case DELETED = 'deleted';
}
