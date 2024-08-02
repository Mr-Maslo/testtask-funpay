<?php

namespace FpDbTest;

use Exception;
use FpDbTest\Mask\Mask;
use mysqli;

class Database implements DatabaseInterface
{
    private mysqli $mysqli;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    /**
     * @throws Exception
     */
    public function buildQuery(string $query, array $args = []): string
    {
        return $this->doBuildQuery($query, $args);
    }

    public function skip(): SkipArg
    {
        return new SkipArg();
    }

    /**
     * @throws Exception
     */
    protected function doBuildQuery(string $query, array &$args = [], bool $isOptPart = false): string
    {
        $result = [];
        $matches = [];
        if (!preg_match_all(
            '#((?<mask>\?[dfa\#]?)|(\{(?<opt>[^}]+)})|(?<text>[^?{}]+)|(?<error>(}|{(?!}))))#m',
            $query,
            $matches,
            PREG_UNMATCHED_AS_NULL
        )) {
            throw new Exception('Incorrect query template');
        }

        $hasSkipArgs = false;
        $matches = array_intersect_key($matches, ['mask' => true, 'opt' => true, 'text' => true, 'error' => true]);
        for ($i = 0; $i < count($matches['text']); ++$i) {
            switch (true) {
                case isset($matches['mask'][$i]):
                    if (key($args) === null) {
                        throw new Exception('Count of arguments is less than count of wildcards in template');
                    }
                    $arg = current($args);
                    next($args);

                    if ($arg instanceof SkipArg) {
                        if ($isOptPart) {
                            $hasSkipArgs = true;
                        } else {
                            throw new Exception('Skip argument can be only in optional part of template');
                        }
                    } else {
                        $result[] = Mask::create($matches['mask'][$i])->apply($arg);
                    }
                    break;

                case isset($matches['opt'][$i]):
                    $optPart = $matches['opt'][$i];
                    if (preg_match('#[{}]#', $optPart)) {
                        throw new Exception('Incorrect query template');
                    }

                    $result[] = $this->doBuildQuery($optPart, $args, true);
                    break;

                case isset($matches['text'][$i]):
                    $result[] = $matches['text'][$i];
                    break;

                case isset($matches['error'][$i]):
                    throw new Exception('Incorrect query template');
            }
        }

        return $isOptPart && $hasSkipArgs
            ? ''
            : implode('', $result)
        ;
    }
}
