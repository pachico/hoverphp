<?php

declare(strict_types=1);

namespace Pachico\HoverPHPUTest;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Pachico\HoverPHP\Client;
use Pachico\HoverPHP\Entity\Simulation;
use Pachico\HoverPHP\Exception\CannotConnectToHoverfly;
use Pachico\HoverPHP\Exception\Runtime;
use Psr\Http\Message\RequestInterface;

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

    public function testPingReturnsPongIfOK()
    {
        // Arrange
        $sut = Client::new('foo', $this->httpClient);
        $this->httpClient->expects($spy = $this->once())->method('request');

        // Act
        $output = $sut->ping();
        // Assert
        $this->assertSame('pong', $output);
        $this->assertSame(1, $spy->getInvocationCount());
    }


    public function testExportSimulationReturnsContentOfResponseFromHoverfly()
    {
        // Arrange
        $sut = Client::new('foo', $this->httpClient);
        $this->httpClient->expects($spy = $this->once())->method('request')
        ->willReturn(new Response(200, [], '{7}'));

        // Act
        $output = $sut->exportSimulation();
        // Assert
        $this->assertSame(1, $spy->getInvocationCount());
        $this->assertSame('{7}', $output);
    }


    public function dataProviderMethodsArgsToTriggerException(): array
    {
        return [
            ['ping', null],
            ['setMode', Client::MODE_CAPTURE],
            ['setSimulation', Simulation::new()],
            ['deleteSimulation', null],
            ['exportSimulation', null],
        ];
    }

    /**
     * @dataProvider dataProviderMethodsArgsToTriggerException
     */
    public function testThrowsCannotConnectToHoverflyExceptionWhenApplicable(string $methodName, ...$args)
    {
        // Arrange
        $this->expectException(CannotConnectToHoverfly::class);
        $this->expectExceptionMessage('Could not connect to hoverfly. Where is it?');

        $sut = Client::new('foo', $this->httpClient);
        $this->httpClient->expects($this->any())->method('request')->willThrowException(
            new ConnectException('Where is it?', $this->getMockForAbstractClass(RequestInterface::class))
        );

        // Act
        call_user_func_array([$sut, $methodName], $args);
    }

    /**
     * @dataProvider dataProviderMethodsArgsToTriggerException
     */
    public function testThrowsExceptionWhenApplicable(string $methodName, ...$args)
    {
        // Arrange
        $this->expectException(Runtime::class);
        $this->expectExceptionMessage('An error occurred: Something happened!');

        $sut = Client::new('foo', $this->httpClient);
        $this->httpClient->expects($this->any())->method('request')->willThrowException(
            new Runtime('Something happened!')
        );

        // Act
        call_user_func_array([$sut, $methodName], $args);
    }
}
