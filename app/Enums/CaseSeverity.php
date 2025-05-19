<?php

namespace App\Enums;

use App\Traits\EnumFromId;
use App\Traits\EnumToArray;

enum CaseSeverity : int
{
    use EnumToArray;
    use EnumFromId;

    case HIGHEST = 5;
    case HIGH = 4;
    case MEDIUM = 3;
    case LOW = 2;
    case LOWEST = 1;

//    public static function fromId(int $id) {
//        foreach (CasePriority::cases() as $case)
//            if($case->value == $id) return $case;
//    }
    public function cls(): string {
        return match ($this) {
            self::LOWEST => 'bi-chevron-double-down text-warning',
            self::LOW => 'bi-chevron-down text-warning',
            self::MEDIUM => 'bi-list text-info',
            self::HIGH => 'bi-chevron-up text-danger',
            self::HIGHEST => 'bi-chevron-double-up text-danger',
        };
    }
}
