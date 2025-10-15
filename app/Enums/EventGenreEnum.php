<?php

namespace App\Enums;

enum EventGenreEnum: string
{
    use  EnumFunctions;

    case ROCK = 'rock';
    case METAL = 'metal';
    case ALL = 'all';
}
