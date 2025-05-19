<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum OS: string
{
    use EnumToArray;

    case WINDOWS = 'windows';
    case WIN10 = 'win10';
    case WIN11 = 'win11';
    case LINUX = 'linux';
    case MAC = 'mac';

}
