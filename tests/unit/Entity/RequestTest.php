<?php

declare(strict_types=1);

namespace Pachico\HoverPHPUTest\Entity;

use GuzzleHttp\Psr7\Request as Psr7Request;
use InvalidArgumentException;
use Pachico\HoverPHP\Entity\Matcher;
use Pachico\HoverPHP\Entity\Request;
use Pachico\HoverPHPUTest\AbstractTestCase;

class RequestTest extends AbstractTestCase
{
    public function testNewReturnsProperInstance()
    {
        // Act
        $sut = Request::new();
        // Assert
        $this->assertInstanceOf(Request::class, $sut);
        $this->assertJsonStringEqualsJsonString(
            '{"method":[],"scheme":[],"destination":[],"path":[],"body":[]}',
            json_encode($sut)
        );
    }

    public function testFromPSR7TransformsDataCorrectly()
    {
        // Arrange
        $psr7Request = new Psr7Request('PUT', 'http://www.foo.bar/baz?foo=123', ['Content-Type' => 'application/json'], '{12345}');
        $sut = Request::fromPSR7($psr7Request);
        $this->assertInstanceOf(Request::class, $sut);
        $this->assertJsonStringEqualsJsonString(
            '{"method":[{"value":"PUT","matcher":"exact"}],"scheme":[{"value":"http","matcher":"exact"}],"destination":[{"value":"www.foo.bar","matcher":"exact"}],"path":[{"value":"\/baz","matcher":"exact"}],"body":[{"value":"{12345}","matcher":"exact"}],"query":{"foo":[{"value":"123","matcher":"exact"}]},"headers":{"Content-Type":[{"value":"application\/json","matcher":"exact"}]}}',
            json_encode($sut)
        );
    }


    public function dataProviderRequestWithMatcherAndExpectedJSON(): array
    {
        return [
            0 => [
                Request::new()->withMethodMatcher(Matcher::GLOB, 'foo'),
                '{"method":[{"value":"foo","matcher":"glob"}],"scheme":[],"destination":[],"path":[],"body":[]}'
            ],
            1 => [
                Request::new()->withSchemeMatcher(Matcher::GLOB, 'foo'),
                '{"method":[],"scheme":[{"value":"foo","matcher":"glob"}],"destination":[],"path":[],"body":[]}'
            ],
            2 => [
                Request::new()->withDestinationMatcher(Matcher::GLOB, 'foo'),
                '{"method":[],"scheme":[],"destination":[{"value":"foo","matcher":"glob"}],"path":[],"body":[]}'
            ],
            3 => [
                Request::new()->withPathMatcher(Matcher::GLOB, 'foo'),
                '{"method":[],"scheme":[],"destination":[],"path":[{"value":"foo","matcher":"glob"}],"body":[]}'
            ],
            4 => [
                Request::new()->withBodyMatcher(Matcher::GLOB, 'foo'),
                '{"method":[],"scheme":[],"destination":[],"path":[],"body":[{"value":"foo","matcher":"glob"}]}'
            ],
            5 => [
                Request::new()->withQueryMatcher('bar', Matcher::GLOB, 'foo'),
                '{"method":[],"scheme":[],"destination":[],"path":[],"body":[],"query":{"bar":[{"value":"foo","matcher":"glob"}]}}'
            ],
            6 => [
                Request::new()->withHeaderMatcher('bar', Matcher::GLOB, 'foo'),
                '{"method":[],"scheme":[],"destination":[],"path":[],"body":[],"headers":{"bar":[{"value":"foo","matcher":"glob"}]}}'
            ],
            7 => [
                Request::new()->withMethodMatcher(Matcher::GLOB, 'foo'),
                '{"method":[{"value":"foo","matcher":"glob"}],"scheme":[],"destination":[],"path":[],"body":[]}'
            ],
            8 => [
                Request::new()->withMethodMatcher(Matcher::GLOB, 'foo')
                    ->withSchemeMatcher(Matcher::GLOB, 'foo')
                    ->withDestinationMatcher(Matcher::GLOB, 'foo')
                    ->withPathMatcher(Matcher::GLOB, 'foo')
                    ->withBodyMatcher(Matcher::GLOB, 'foo')
                    ->withQueryMatcher('bar', Matcher::GLOB, 'foo')
                    ->withHeaderMatcher('bar', Matcher::GLOB, 'foo')
                    ->withMethodMatcher(Matcher::GLOB, 'foo'),
                '{"method":[{"value":"foo","matcher":"glob"},{"value":"foo","matcher":"glob"}],"scheme":[{"value":"foo","matcher":"glob"}],"destination":[{"value":"foo","matcher":"glob"}],"path":[{"value":"foo","matcher":"glob"}],"query":{"bar":[{"value":"foo","matcher":"glob"}]},"headers":{"bar":[{"value":"foo","matcher":"glob"}]},"body":[{"value":"foo","matcher":"glob"}]}'
            ]
        ];
    }

    /**
     * @dataProvider dataProviderRequestWithMatcherAndExpectedJSON
     */
    public function testWithMatchersSetProperDataInRequest(Request $sut, string $expectedJSON)
    {
        // Arrange
        $this->assertJsonStringEqualsJsonString($expectedJSON, json_encode($sut));
    }

    public function testValidateIsValidMatcherThrowsExceptionIfInvalid()
    {
        // Arrange
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid matcher. Got invalid_matcher');
        $sut = Request::new();
        // Act
        $sut->withMethodMatcher('invalid_matcher', '*');
    }
}
