<?php

declare(strict_types=1);

namespace Pachico\HoverPHP\Entity;

class Matcher
{

    public const EXACT = 'exact';
    public const GLOB = 'glob';
    public const REGEX = 'regex';
    public const DEFAULT = self::EXACT;

    private static array $validMatchers = [
        self::EXACT,
        self::GLOB,
        self::REGEX
    ];

    public static function isValidMatcher(string $matcher): bool
    {
        if (in_array($matcher, self::$validMatchers)) {
            return true;
        }

        return false;
    }
}
