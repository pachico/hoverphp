<?php

declare(strict_types=1);

namespace Pachico\HoverPHP;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client as GuzzleClient;
use InvalidArgumentException;
use Pachico\HoverPHP\Entity\Simulation;

/**
 * @todo: manage exceptions
 */
class Client
{
    public const MODE_SIMULATION = 'simulate';

    private static array $allowedModes = [
        self::MODE_SIMULATION
    ];

    private string $hoverflyDSN;
    private ClientInterface $httpClient;
    private string $apiPath = '/api/v2/';

    private function __construct(string $hoverflyDSN, ClientInterface $httpClient = null)
    {
        $this->hoverflyDSN = rtrim($hoverflyDSN, '/');

        $this->httpClient = !is_null($httpClient)
            ? $httpClient
            : new GuzzleClient([
                'base_uri'        => $this->hoverflyDSN . $this->apiPath,
                'timeout'         => 0,
                'allow_redirects' => false
            ]);
    }

    public static function new(string $hoverflyDSN, ClientInterface $httpClient = null): self
    {
        return new self($hoverflyDSN, $httpClient);
    }

    public function setMode(string $mode): void
    {
        if (false === in_array($mode, self::$allowedModes)) {
            throw new InvalidArgumentException('Invalid mode. Got: ' . $mode);
        }

        // Minimum body to set mode. It might eventually grow over version
        $body = ['mode' => $mode];

        $this->httpClient->request(
            'PUT',
            'hoverfly/mode',
            [
                'headers' => ['Content-Type' => 'application-json'],
                'json' => $body
            ]
        );
    }

    public function setSimulation(Simulation $simulation): void
    {
        $this->httpClient->request(
            'POST',
            'simulation',
            [
                'headers' => ['Content-Type' => 'application-json'],
                'json' => $simulation
            ]
        );
    }

    public function deleteSimulation(): void
    {
        $this->httpClient->request('DELETE', 'simulation');
    }
}
