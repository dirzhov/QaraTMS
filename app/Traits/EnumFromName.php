<?php

namespace App\Traits;


trait EnumFromName
{
    public static function tryFromName(string $name): ?static
    {
        foreach (self::cases() as $case) {
            if (strtolower($case->name) === strtolower($name)) {
                return $case;
            }
        }
        return null;
        // for php8.4
        //return array_column(static::cases(), null, 'name')[$name] ?? null;
    }

    public static function fromName(string $name): static
    {
        return self::tryFromName($name)
            ?? throw new \ValueError(sprintf(
                '%s is not a valid case for enum %s',
                $name,
                static::class
            ));
    }

}