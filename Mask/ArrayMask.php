<?php

namespace FpDbTest\Mask;

use Exception;

class ArrayMask extends Mask
{
    /**
     * @param array $arg
     * @return string
     * @throws Exception
     */
    public function apply($arg): string
	{
		if (!is_array($arg)) {
			throw new Exception('Argument must be array for wildcard "?a"');
		}

		$result = [];
		$isAssociative = $arg !== array_values($arg);
		foreach ($arg as $key => $value) {
			try {
                $result[] = $isAssociative
                    ? (new IdentifierMask())->apply($key) . ' = ' . (new AutoMask())->apply($value)
                    : (new AutoMask())->apply($value)
                ;
            } catch (Exception) {
                throw new Exception('Each argument element must be string, int, float, bool, null for wildcard "?a"');
            }
		}

		return implode(', ', $result);
	}
}
