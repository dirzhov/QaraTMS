<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum TestingType : int
{
    use EnumToArray;

    case Acceptance = 1;
    case Function = 2;
    case Installation = 3;
    case Integration = 4;
    case Performance = 5;
    case Product = 6;
    case Regression = 7;
    case Smoke = 8;
    case System = 9;
    case Unit = 10;
}
