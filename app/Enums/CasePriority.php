<?php

namespace App\Enums;

use App\Traits\EnumFromId;
use App\Traits\EnumFromName;
use App\Traits\EnumToArray;

enum CasePriority: int
{
    use EnumToArray;
    use EnumFromId;
    use EnumFromName;

    case HIGHEST = 1;
    case HIGH = 2;
    case MEDIUM = 3;
    case LOW = 4;
    case LOWEST = 5;

//    public static function fromId(int $id) {
//        foreach (CasePriority::cases() as $case)
//            if($case->value == $id) return $case;
//    }
    public function gridValue(): string {
        return match ($this) {
            self::LOWEST => 'P5',
            self::LOW => 'P4',
            self::MEDIUM => 'P3',
            self::HIGH => 'P2',
            self::HIGHEST => 'P1',
        };
    }

    public static function fromGridValue(string $value): CasePriority {
        return match ($value) {
            'P5' => self::LOWEST,
            'P4' => self::LOW,
            'P3' => self::MEDIUM,
            'P2' => self::HIGH,
            'P1' => self::HIGHEST,
        };
    }

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
