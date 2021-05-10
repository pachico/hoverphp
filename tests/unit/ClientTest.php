<?php

declare(strict_types=1);

namespace Pachico\HoverPHPUTest;

use GuzzleHttp\ClientInterface;
use InvalidArgumentException;
use Pachico\HoverPHP\Client;
use Pachico\HoverPHP\Entity\Simulation;

class ClienTest extends AbstractTestCase
{

    private ClientInterface $httpClient;

    public function setUp(): void
    {
        $this->httpClient = $this->getMockForAbstractClass(ClientInterface::class);
    }

    public function testNewReturnsProperInstance()
    {
        // Arrange
        // Act
        $sut = Client::new('foo');
        // Assert
        $this->assertInstanceOf(Client::class, $sut);
    }

    public function testNewAcceptsInstanceOfClientInterface()
    {
        // Arrange
        // Act
        $sut = Client::new('foo', $this->httpClient);
        // Assert
        $this->assertInstanceOf(Client::class, $sut);
    }

    public function testSetModeSendsSetModeRequestIfValid()
    {
        // Arrange
        $sut = Client::new('foo', $this->httpClient);
        $this->httpClient->expects($spy = $this->once())->method('request');

        // Act
        $sut->setMode(Client::MODE_SIMULATION);
        // Assert
        $this->assertSame(1, $spy->getInvocationCount());
    }

    public function testSetModeDoesNotSendSetModeRequestIfInvalidAndThrowException()
    {
        // Arrange
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid mode. Got: invalid_mode');
        $sut = Client::new('foo', $this->httpClient);
        $this->httpClient->expects($spy = $this->never())->method('request');

        // Act
        $sut->setMode('invalid_mode');
        // Assert
        $this->assertSame(0, $spy->getInvocationCount());
    }

    public function testDeleteSimulationSendsDeleteSimulationRequest()
    {
        // Arrange
        $sut = Client::new('foo', $this->httpClient);
        $this->httpClient->expects($spy = $this->once())->method('request');

        // Act
        $sut->deleteSimulation();
        // Assert
        $this->assertSame(1, $spy->getInvocationCount());
    }

    public function testSetSimulationSendsSetSimulationRequest()
    {
        // Arrange
        $sut = Client::new('foo', $this->httpClient);
        $this->httpClient->expects($spy = $this->once())->method('request');

        // Act
        $sut->setSimulation(Simulation::new());
        // Assert
        $this->assertSame(1, $spy->getInvocationCount());
    }
}
