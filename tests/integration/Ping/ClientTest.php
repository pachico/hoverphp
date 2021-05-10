<?php

declare(strict_types=1);

namespace Pachico\HoverPHPITest\Ping;

use Pachico\HoverPHPITest\AbstractTestCase;

class ClientTest extends AbstractTestCase
{
    public function testSettingSimulationModeSetsState()
    {
        // Arrange
        // Act
        $pingResponse = $this->hClient->ping();
        // Assert
        $this->assertSame('pong', $pingResponse);
    }
}
