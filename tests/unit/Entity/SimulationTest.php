<?php

declare(strict_types=1);

namespace Pachico\HoverPHPUTest\Entity;

use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Pachico\HoverPHP\Entity\Matcher;
use Pachico\HoverPHP\Entity\Request;
use Pachico\HoverPHP\Entity\Response;
use Pachico\HoverPHP\Entity\Simulation;
use Pachico\HoverPHPUTest\AbstractTestCase;

class SimulationTest extends AbstractTestCase
{

    public function dataProviderSimulations(): array
    {
        $psr7Request = new Psr7Request(
            'GET',
            'https://packagist.org/packages/list.json?vendor=pachico',
            ['Content-Type' => 'application/json'],
            '{"foo":"bar"}'
        );

        $psr7Response = new Psr7Response(200, ['Content-Type' => 'application/json'], '{"foo":"baz"}');

        return [
            0 => [Simulation::new(), '{"data":{"pairs":[]},"meta":{"schemaVersion":"v5"}}'],
            1 => [
                Simulation::new()->withPair(
                    Request::fromPSR7($psr7Request),
                    Response::new(400, '{"foo":"baz"}', false, [], false)
                ),
                '{"data":{"pairs":[{"request":{"method":[{"value":"GET","matcher":"exact"}],"scheme":[{"value":"https","matcher":"exact"}],"destination":[{"value":"packagist.org","matcher":"exact"}],"path":[{"value":"\/packages\/list.json","matcher":"exact"}],"query":{"vendor":[{"matcher":"exact","value":"pachico"}]},"headers":{"Host":[{"matcher":"exact","value":"packagist.org"}],"Content-Type":[{"matcher":"exact","value":"application\/json"}]},"body":[{"matcher":"exact","value":"{\"foo\":\"bar\"}"}]},"response":{"status":400,"body":"{\"foo\":\"baz\"}","encodedBody":false,"headers":[],"templated":false}}]},"meta":{"schemaVersion":"v5"}}'
            ],
            2 => [
                Simulation::new()->withPair(
                    Request::fromPSR7($psr7Request),
                    Response::fromPSR7($psr7Response)
                ),
                '{"data":{"pairs":[{"request":{"method":[{"value":"GET","matcher":"exact"}],"scheme":[{"value":"https","matcher":"exact"}],"destination":[{"value":"packagist.org","matcher":"exact"}],"path":[{"value":"\/packages\/list.json","matcher":"exact"}],"query":{"vendor":[{"matcher":"exact","value":"pachico"}]},"headers":{"Host":[{"matcher":"exact","value":"packagist.org"}],"Content-Type":[{"matcher":"exact","value":"application\/json"}]},"body":[{"matcher":"exact","value":""}]},"response":{"status":200,"body":"{\"foo\":\"baz\"}","encodedBody":false,"headers":{"Content-Type":["application\/json"]},"templated":false}}]},"meta":{"schemaVersion":"v5"}}'
            ],
            3 => [
                Simulation::new()->withPair(
                    Request::fromPSR7($psr7Request)->withMethodMatcher(Matcher::GLOB, '*'),
                    Response::fromPSR7($psr7Response)
                ),
                '{"data":{"pairs":[{"request":{"method":[{"value":"GET","matcher":"exact"},{"value":"*","matcher":"glob"}],"scheme":[{"value":"https","matcher":"exact"}],"destination":[{"value":"packagist.org","matcher":"exact"}],"path":[{"value":"\/packages\/list.json","matcher":"exact"}],"query":{"vendor":[{"value":"pachico","matcher":"exact"}]},"headers":{"Host":[{"value":"packagist.org","matcher":"exact"}],"Content-Type":[{"value":"application\/json","matcher":"exact"}]},"body":[{"value":"","matcher":"exact"}]},"response":{"status":200,"body":"","encodedBody":false,"headers":{"Content-Type":["application\/json"]},"templated":false}}]},"meta":{"schemaVersion":"v5"}}'
            ],
            4 => [
                Simulation::new()->withPair(
                    Request::fromPSR7($psr7Request)->withSchemeMatcher(Matcher::GLOB, '*'),
                    Response::fromPSR7($psr7Response)
                ),
                '{"data":{"pairs":[{"request":{"method":[{"value":"GET","matcher":"exact"}],"scheme":[{"value":"https","matcher":"exact"},{"value":"*","matcher":"glob"}],"destination":[{"value":"packagist.org","matcher":"exact"}],"path":[{"value":"\/packages\/list.json","matcher":"exact"}],"query":{"vendor":[{"value":"pachico","matcher":"exact"}]},"headers":{"Host":[{"value":"packagist.org","matcher":"exact"}],"Content-Type":[{"value":"application\/json","matcher":"exact"}]},"body":[{"value":"","matcher":"exact"}]},"response":{"status":200,"body":"","encodedBody":false,"headers":{"Content-Type":["application\/json"]},"templated":false}}]},"meta":{"schemaVersion":"v5"}}'
            ],
            5 => [
                Simulation::new()->withPair(
                    Request::fromPSR7($psr7Request)->withDestinationMatcher(Matcher::GLOB, '*'),
                    Response::fromPSR7($psr7Response)
                ),
                '{"data":{"pairs":[{"request":{"method":[{"value":"GET","matcher":"exact"}],"scheme":[{"value":"https","matcher":"exact"}],"destination":[{"value":"packagist.org","matcher":"exact"},{"value":"*","matcher":"glob"}],"path":[{"value":"\/packages\/list.json","matcher":"exact"}],"query":{"vendor":[{"value":"pachico","matcher":"exact"}]},"headers":{"Host":[{"value":"packagist.org","matcher":"exact"}],"Content-Type":[{"value":"application\/json","matcher":"exact"}]},"body":[{"value":"","matcher":"exact"}]},"response":{"status":200,"body":"","encodedBody":false,"headers":{"Content-Type":["application\/json"]},"templated":false}}]},"meta":{"schemaVersion":"v5"}}'
            ],
            6 => [
                Simulation::new()->withPair(
                    Request::fromPSR7($psr7Request)->withPathMatcher(Matcher::GLOB, '*'),
                    Response::fromPSR7($psr7Response)
                ),
                '{"data":{"pairs":[{"request":{"method":[{"value":"GET","matcher":"exact"}],"scheme":[{"value":"https","matcher":"exact"}],"destination":[{"value":"packagist.org","matcher":"exact"}],"path":[{"value":"\/packages\/list.json","matcher":"exact"},{"value":"*","matcher":"glob"}],"query":{"vendor":[{"value":"pachico","matcher":"exact"}]},"headers":{"Host":[{"value":"packagist.org","matcher":"exact"}],"Content-Type":[{"value":"application\/json","matcher":"exact"}]},"body":[{"value":"","matcher":"exact"}]},"response":{"status":200,"body":"","encodedBody":false,"headers":{"Content-Type":["application\/json"]},"templated":false}}]},"meta":{"schemaVersion":"v5"}}'
            ],
            7 => [
                Simulation::new()->withPair(
                    Request::fromPSR7($psr7Request)->withQueryMatcher('foobar', Matcher::GLOB, '*'),
                    Response::fromPSR7($psr7Response)
                ),
                '{"data":{"pairs":[{"request":{"method":[{"value":"GET","matcher":"exact"}],"scheme":[{"value":"https","matcher":"exact"}],"destination":[{"value":"packagist.org","matcher":"exact"}],"path":[{"value":"\/packages\/list.json","matcher":"exact"}],"query":{"vendor":[{"value":"pachico","matcher":"exact"}],"foobar":[{"value":"*","matcher":"glob"}]},"headers":{"Host":[{"value":"packagist.org","matcher":"exact"}],"Content-Type":[{"value":"application\/json","matcher":"exact"}]},"body":[{"value":"","matcher":"exact"}]},"response":{"status":200,"body":"","encodedBody":false,"headers":{"Content-Type":["application\/json"]},"templated":false}}]},"meta":{"schemaVersion":"v5"}}'
            ],
            8 => [
                Simulation::new()->withPair(
                    Request::fromPSR7($psr7Request)->withHeaderMatcher('foobar', Matcher::GLOB, '*'),
                    Response::fromPSR7($psr7Response)
                ),
                '{"data":{"pairs":[{"request":{"method":[{"value":"GET","matcher":"exact"}],"scheme":[{"value":"https","matcher":"exact"}],"destination":[{"value":"packagist.org","matcher":"exact"}],"path":[{"value":"\/packages\/list.json","matcher":"exact"}],"query":{"vendor":[{"value":"pachico","matcher":"exact"}]},"headers":{"Host":[{"value":"packagist.org","matcher":"exact"}],"Content-Type":[{"value":"application\/json","matcher":"exact"}],"foobar":[{"value":"*","matcher":"glob"}]},"body":[{"value":"","matcher":"exact"}]},"response":{"status":200,"body":"","encodedBody":false,"headers":{"Content-Type":["application\/json"]},"templated":false}}]},"meta":{"schemaVersion":"v5"}}'
            ],
            9 => [
                Simulation::new()->withPair(
                    Request::fromPSR7($psr7Request)->withBodyMatcher(Matcher::GLOB, '*'),
                    Response::fromPSR7($psr7Response)
                ),
                '{"data":{"pairs":[{"request":{"method":[{"value":"GET","matcher":"exact"}],"scheme":[{"value":"https","matcher":"exact"}],"destination":[{"value":"packagist.org","matcher":"exact"}],"path":[{"value":"\/packages\/list.json","matcher":"exact"}],"query":{"vendor":[{"value":"pachico","matcher":"exact"}]},"headers":{"Host":[{"value":"packagist.org","matcher":"exact"}],"Content-Type":[{"value":"application\/json","matcher":"exact"}]},"body":[{"value":"","matcher":"exact"},{"value":"*","matcher":"glob"}]},"response":{"status":200,"body":"","encodedBody":false,"headers":{"Content-Type":["application\/json"]},"templated":false}}]},"meta":{"schemaVersion":"v5"}}'
            ],
            10 => [
                Simulation::new()->withPair(
                    Request::new()->withDestinationMatcher(Matcher::GLOB, '*'),
                    Response::fromPSR7($psr7Response)
                ),
                '1'
            ],
        ];
    }

    /**
     * @dataProvider dataProviderSimulations
     */
    public function testSchemaEmpty(Simulation $simulation, string $simulationAsJSON)
    {
        // Assert
        $this->assertSimulationCompliesWithSchema($simulation);
        // $this->assertJsonStringEqualsJsonString($simulationAsJSON, json_encode($simulation));
    }

    public function testNewReturnsProperInstance()
    {
        // Act
        $sut = Simulation::new();
        // Assert
        $this->assertInstanceOf(Simulation::class, $sut);
        $this->assertJsonStringEqualsJsonString(
            '{"data":{"pairs":[]},"meta":{"schemaVersion":"v5"}}',
            json_encode($sut)
        );
    }

    public function testWithPairAddsPairToData()
    {
        // Arrange
        $sut = Simulation::new();
        // Act
        $sut->withPair(Request::new(), Response::new(200, 'body and mind'));
        // Assert
        $this->assertJsonStringEqualsJsonString(
            '{"data":{"pairs":[{"request":{"method":[],"scheme":[],"destination":[],"path":[],"body":[]},"response":{"status":200,"body":"body and mind","encodedBody":false,"headers":[],"templated":false}}]},"meta":{"schemaVersion":"v5"}}',
            json_encode($sut)
        );
    }
}
