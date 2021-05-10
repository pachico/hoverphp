<?php

declare(strict_types=1);

namespace Pachico\HoverPHPITest\SettingMode;

use Pachico\HoverPHPITest\AbstractTestCase;
use Pachico\HoverPHP\Client;

class ClientTest extends AbstractTestCase
{
    public function testSettingSimulationModeSetsState()
    {
        // Arrange
        // Act
        $this->hClient->setMode(Client::MODE_SIMULATION);
        $currentStateResponse = $this->httpClient->request('GET', '/api/v2/hoverfly/mode');
        // Assert
        $this->assertSame('simulate', json_decode($currentStateResponse->getBody()->getContents(), true)['mode']);
    }

    public function testSettingCaptureModeSetsState()
    {
        // Arrange
        // Act
        $this->hClient->setMode(Client::MODE_CAPTURE);
        $currentStateResponse = $this->httpClient->request('GET', '/api/v2/hoverfly/mode');
        // Assert
        $this->assertSame('capture', json_decode($currentStateResponse->getBody()->getContents(), true)['mode']);
    }
}
