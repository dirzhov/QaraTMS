<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum TestCaseType : int
{
    use EnumToArray;

    case MANUAL = 0;
    case AUTOMATED = 1;

}
