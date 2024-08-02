<?php

namespace FpDbTest\Mask;

abstract class Mask
{
    public static function create(string $mask): static
    {
        return match ($mask) {
            '?d' => new IntegerMask(),
            '?f' => new FloatMask(),
            '?#' => new IdentifierMask(),
            '?a' => new ArrayMask(),
            '?' => new AutoMask(),
        };
    }

    abstract public function apply($arg): string;

    protected function __construct() { }
}
