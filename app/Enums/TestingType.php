<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum TestingType : int
{
    use EnumToArray;

    case Acceptance = 1;
    case Functional = 2;
    case Installation = 3;
    case Integration = 4;
    case Performance = 5;
    case Product = 6;
    case Regression = 7;
    case Smoke = 8;
    case System = 9;
    case Unit = 10;

    public function cls(): string {
        return match ($this) {
            self::Acceptance => 'bi-ui-checks text-warning',
            self::Functional => 'bi-ui-checks text-success',
            self::Installation => 'bi-ui-checks text-info',
            self::Integration => 'bi-puzzle-fill text-success',
            self::Performance => 'bi-reception-4 text-primary',
            self::Product => 'bi-ui-checks text-primary',
            self::Regression => 'bi-recycle text-primary',
            self::Smoke => 'bi-ui-checks text-success',
            self::System => 'bi-terminal-fill text-primary',
            self::Unit => 'bi-list-check text-primary',
        };
    }

}
