<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum Environment: int
{
    use EnumToArray;

    case PRODUCTION = 1;
    case STAGING = 2;
    case UAT = 3;
    case DEV = 4;
    case LOCAL = 5;

}
