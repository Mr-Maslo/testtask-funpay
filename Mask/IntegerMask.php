<?php

namespace FpDbTest\Mask;

class IntegerMask extends Mask
{
    public function apply($arg): string
    {
        return $arg === null ? 'NULL' : (int)$arg;
    }
}
