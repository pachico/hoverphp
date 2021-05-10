<?php

declare(strict_types=1);

namespace Pachico\HoverPHPITest\ExportingSimulation;

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
    public function testExportSimulationExportsItCorrectly()
    {
        // Arrange
        $simulation  = Simulation::new()->withPair(
            Request::new()->withDestinationMatcher(Matcher::GLOB, '*'),
            Response::fromPSR7(new Psr7Response(410, ['Content-Type' => 'application/html'], 'I do not exist anymore'))
        );
        $this->hClient->setMode(Client::MODE_SIMULATION);
        $this->hClient->setSimulation($simulation);
        $this->httpClient->request('GET', 'http://www.altavista.com', [
            'proxy' => 'localhost:8500',
            'headers' => ['Content-Type' => 'application/json']
        ]);

        // Act

        $output = $this->hClient->exportSimulation();

        // Assert
        $this->assertJson($output);
        $this->assertNotEmpty($output);
    }

    public function testExportSimulationToFileExportsItCorrectly()
    {
        // Arrange
        $simulation  = Simulation::new()->withPair(
            Request::new()->withDestinationMatcher(Matcher::GLOB, '*'),
            Response::fromPSR7(new Psr7Response(410, ['Content-Type' => 'application/html'], 'I do not exist anymore'))
        );
        $this->hClient->setMode(Client::MODE_SIMULATION);
        $this->hClient->setSimulation($simulation);
        $this->httpClient->request('GET', 'http://www.altavista.com', [
            'proxy' => 'localhost:8500',
            'headers' => ['Content-Type' => 'application/json']
        ]);

        // Act
        $destinationFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('simulation', true) . '.json';
        $this->hClient->exportSimulationToFile($destinationFile);

        // Assert
        $this->assertJson(file_get_contents($destinationFile));
        $this->assertNotEmpty(file_get_contents($destinationFile));

        unlink($destinationFile);
    }
}
