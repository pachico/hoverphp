<?php

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Pachico\HoverPHP\Client;
use Pachico\HoverPHP\Entity\Pair;
use Pachico\HoverPHP\Entity\Request;
use Pachico\HoverPHP\Entity\Response;
use Pachico\HoverPHP\Entity\Simulation;
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../vendor/autoload.php';


class MyAwesomeIntegrationTest extends TestCase
{
    /**
     * This test will make sure HTTP Repository "SuperHTTPRepo"
     * Communicates with SuperService without mocking the HTTP client
     * but by using a simulation in
     */
    public function testSuperAppDoesMagicWithSuperService()
    {
        // Arrange

        $hClient = new Client('http://myhoverflyhost:8888');

        // I am instantiating this Guzzle Client by changing its base_uri pointing
        // to Hoverfly's hostname and its webserver port.
        // Alternatively, you could use Hoverfly proxy to serve simulations
        $httpClient = new GuzzleHttpClient(['base_uri' => 'http://myhoverflyhost:8888']);

        // I pass this HTTP client to my HTTP Repo
        $superRepo = new SuperHTTPRepo($httpClient);

        // Make sure Hoverfly is set in simulation mode
        $hClient->setMode(Client::MODE_SIMULATION);

        // And set simulation in Hoverfly from within the test
        $hClient->setSimulation(Simulation::new()->withPair(
            Request::fromPSR7(
                new Psr7Request('GET', '/superapi/foo', ['Content-Type' => 'application/json'])
            ),
            Response::fromPSR7(
                new Psr7Response(200, ['Content-Type' => 'application/json'], '{"bar": "true"')
            )
        ));

        // Act

        // Now your repo can do its work by triggering a real HTTP request to simulated service
        $returnedValue = $superRepo->doMyAwesomeWork();

        // Assert

        // Finally, you can do your assertions
        $this->assertTrue($returnedValue);

        // Clean UP
        // Either here or in TearDown(), you might want to clear simulations with
        $hClient->deleteSimulation();
    }
}
