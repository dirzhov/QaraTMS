<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum CommonStatus: int
{
    use EnumToArray;

    case ACTIVE = 1;
    case DELETED = 3;

}
