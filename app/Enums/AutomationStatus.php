<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum AutomationStatus : int
{
    use EnumToArray;

    case NOT_AUTOMATED = 1;
    case AUTOMATED = 2;
    case IN_PROGRESS = 3;
    case CAN_NOT_BE_AUTOMATED = 4;

}
