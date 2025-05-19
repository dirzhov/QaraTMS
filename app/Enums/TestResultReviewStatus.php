<?php

namespace App\Enums;

use App\Traits\EnumFromName;
use App\Traits\EnumToArray;

enum TestResultReviewStatus: int
{
    use EnumToArray;
    use EnumFromName;

    case NOT_REVIEWED = 5;
    case ON_REVIEW = 4;
    case PASSED = 1;
    case FAILED = 2;
    case SKIPPED = 3;

    public function cls(): string {
        return match ($this) {
            self::PASSED => 'bi-check-circle-fill text-success',
            self::FAILED => 'bi-x-circle-fill text-danger',
            self::SKIPPED => 'bi-reply-all-fill text-secondary',
            self::ON_REVIEW => 'bi-eye-fill text-primary',
            self::NOT_REVIEWED => 'bi-eye-slash-fill text-secondary',
        };
    }

}