<?php

declare(strict_types=1);

namespace Pachico\HoverPHPUTest\Entity;

use GuzzleHttp\Psr7\Response as Psr7Response;
use Pachico\HoverPHP\Entity\Response;
use Pachico\HoverPHPUTest\AbstractTestCase;

class ResponseTest extends AbstractTestCase
{
    public function testNewCreatesProperInstance()
    {
        // Arrange
        $sut = Response::new(502, '{"nice":"body"');
        // Assert
        $this->assertInstanceOf(Response::class, $sut);
        $this->assertJsonStringEqualsJsonString(
            '{"status":502,"body":"{\"nice\":\"body\"","encodedBody":false,"headers":{},"templated":false}',
            json_encode($sut)
        );
    }

    public function testFromPSR7TransformsDataCorrectly()
    {
        // Arrange
        $psr7Response = new Psr7Response(404, ['Foo'=>'Bar'], 'helloooo!');
        // Act
        $sut = Response::fromPSR7($psr7Response);
        // Assert
        $this->assertInstanceOf(Response::class, $sut);
        $this->assertJsonStringEqualsJsonString(
            '{"status":404,"body":"helloooo!","encodedBody":false,"headers":{"Foo":["Bar"]},"templated":false}',
            json_encode($sut)
        );
    }
}
