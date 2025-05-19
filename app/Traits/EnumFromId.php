<?php

namespace App\Traits;

trait EnumFromId
{
    public static function fromId(int $id) {
        foreach (self::cases() as $case)
            if($case->value == $id) return $case;
    }

}