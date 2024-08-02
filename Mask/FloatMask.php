<?php

namespace FpDbTest\Mask;

class FloatMask extends Mask
{
    public function apply($arg): string
    {
        return $arg === null ? 'NULL' : (float)$arg;
    }
}
