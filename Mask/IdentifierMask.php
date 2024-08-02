<?php

namespace FpDbTest\Mask;

class IdentifierMask extends Mask
{
    public function apply($arg): string
    {
        $result = [];
        foreach ((array)$arg as $argValue) {
            $result[] = '`' . str_replace('`', '``', $argValue)  . '`';
        }

        return implode(', ', $result);
    }
}
