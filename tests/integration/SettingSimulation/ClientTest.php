<?php

declare(strict_types=1);

namespace Pachico\HoverPHPITest\SettingSimulation;

use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Pachico\HoverPHPITest\AbstractTestCase;
use Pachico\HoverPHP\Client;
use Pachico\HoverPHP\Entity\Matcher;
use Pachico\HoverPHP\Entity\Request;
use Pachico\HoverPHP\Entity\Response;
use Pachico\HoverPHP\Entity\Simulation;

class ClientTest extends AbstractTestCase
{
    public function testSetSimulationSetsItCorrectly()
    {
        // Arrange
        $simulation  = Simulation::new()->withPair(
            Request::new()->withDestinationMatcher(Matcher::GLOB, '*'),
            Response::fromPSR7(new Psr7Response(410, ['Content-Type' => 'application/html'], 'I do not exist anymore'))
        );

        // Act
        $this->hClient->setMode(Client::MODE_SIMULATION);

        try {
            $this->hClient->setSimulation($simulation);
            $response = $this->httpClient->request('GET', 'http://www.altavista.com', [
                'proxy' => 'localhost:8500',
                'headers' => ['Content-Type' => 'application/json']
            ]);
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }

        $this->assertSame(410, $response->getStatusCode());
        $this->assertSame('I do not exist anymore', $response->getBody()->getContents());
    }

    public function testSetSimulationFromPSR7SetsItCorrectly()
    {
        // Arrange
        $psr7Request = new Psr7Request('GET', 'http://simple-http.com/');

        $simulation  = Simulation::new()->withPair(
            Request::fromPSR7($psr7Request),
            Response::fromPSR7(new Psr7Response(200, ['Content-Type' => 'text/html; charset=UTF-8'], 'I am alive!'))
        );

        // Act
        $this->hClient->setMode(Client::MODE_SIMULATION);

        $this->hClient->setSimulation($simulation);
        $response = $this->httpClient->send($psr7Request, ['proxy' => 'localhost:8500']);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('I am alive!', $response->getBody()->getContents());
    }
}
