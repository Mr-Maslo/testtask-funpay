<?php

namespace FpDbTest\Mask;

use Exception;

class AutoMask extends Mask
{
    /**
     * @param string|int|float|bool|null $arg
     * @return string
     * @throws Exception
     */
    public function apply($arg): string
    {
        switch (true) {
            case $arg === null:
                return 'NULL';
            case is_string($arg):
                return '\'' . str_replace('\'', '\\\'', $arg) . '\'';
            case is_int($arg):
            case is_float($arg):
                return $arg;
            case is_bool($arg):
                return (int)$arg;
        }

        throw new Exception('Argument must be string, int, float, bool, null for wildcard "?"');
    }
}
