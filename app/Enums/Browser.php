<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum Browser: string
{
    use EnumToArray;

    case FIREFOX = 'firefox';
    case CHROME = 'chrome';
    case SAFARI = 'safari';
    case OPERA = 'opera';
    case WEBKIT = 'webkit';
    case MOBILE = 'mobile';
}
