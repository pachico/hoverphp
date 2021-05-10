<?php

declare(strict_types=1);

namespace Pachico\HoverPHPUTest\Entity;

use Pachico\HoverPHP\Entity\Matcher;
use Pachico\HoverPHPUTest\AbstractTestCase;

class MatcherTest extends AbstractTestCase
{

    public function dataProviderMatchersAndOutputs(): array
    {
        return [
            [Matcher::EXACT, true],
            [Matcher::GLOB, true],
            [Matcher::REGEX, true],
            ['non_exiting', false],
            ['', false],
        ];
    }

    /**
     * @dataProvider dataProviderMatchersAndOutputs
     */
    public function testIsValidMatcherReturnsProperValue(string $matcher, bool $expectedOutput)
    {
        $outcome = Matcher::isValidMatcher($matcher);
        $this->assertSame($expectedOutput, $outcome);
    }
}
