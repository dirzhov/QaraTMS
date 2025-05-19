<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum UserStatus: int
{
    use EnumToArray;
    case ACTIVE = 1;
    case SUSPENDED = 2;
    case DELETED = 3;

}
