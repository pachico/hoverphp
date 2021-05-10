<?php

declare(strict_types=1);

namespace Pachico\HoverPHPITest;

use GuzzleHttp\Client as GuzzleHttpClient;
use Pachico\HoverPHP\Client;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected Client $hClient;
    protected GuzzleHttpClient $httpClient;

    public function setUp(): void
    {
        parent::setUp();
        $hoverflyDSN = sprintf('http://%s:%s', getenv('HOVER_PHP_HOVERFLY_HOST'), getenv('HOVER_PHP_HOVERFLY_PORT'));
        $this->hClient = Client::new($hoverflyDSN);
        $this->httpClient = new GuzzleHttpClient([
            'base_uri' => $hoverflyDSN,
            'http_errors' => false
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->hClient->deleteSimulation();
    }
}
