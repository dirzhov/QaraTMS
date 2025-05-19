<?php

namespace App\Enums;

use App\Traits\EnumFromName;
use App\Traits\EnumToArray;

enum TestCaseStatus: int
{
    use EnumToArray;
    use EnumFromName;

    case PASSED = 1;
    case FAILED = 2;
    case SKIPPED = 3;

    public function cls(): string {
        return match ($this) {
            self::PASSED => 'bi-check-circle-fill text-success',
            self::FAILED => 'bi-x-circle-fill text-danger',
            self::SKIPPED => 'bi-reply-all-fill text-secondary',
        };
    }
}
