<?php

declare(strict_types=1);

namespace Pachico\HoverPHP;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use InvalidArgumentException;
use Pachico\HoverPHP\Entity\Simulation;
use Pachico\HoverPHP\Exception\CannotConnectToHoverfly;
use Pachico\HoverPHP\Exception\Runtime;
use Throwable;

/**
 * @todo: manage exceptions
 */
class Client
{
    public const MODE_SIMULATION = 'simulate';
    public const MODE_CAPTURE = 'capture';

    private static array $allowedModes = [
        self::MODE_SIMULATION,
        self::MODE_CAPTURE
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

    /**
     * Returns a Hoverfly client by specifying its connection (host:port).
     * Additionally, a Guzzle client can be passed
     */
    public static function new(string $hoverflyDSN, ClientInterface $httpClient = null): self
    {
        return new self($hoverflyDSN, $httpClient);
    }

    /**
     * Test connectivity with Hoverfly.
     * If successful, returns (string) "pong"
     */
    public function ping(): string
    {
        try {
            $this->httpClient->request(
                'GET',
                'state',
                [
                    'headers' => ['Content-Type' => 'application-json'],
                ]
            );

            return 'pong';
        } catch (ConnectException $exception) {
            throw new CannotConnectToHoverfly('Could not connect to hoverfly. ' . $exception->getMessage());
        } catch (Throwable $th) {
            throw new Runtime(sprintf('An error occurred: %s', $th->getMessage()));
        }
    }

    /**
     * Sets Hoverfly's mode
     * @see https://docs.hoverfly.io/en/latest/pages/keyconcepts/modes/modify.html
     */
    public function setMode(string $mode): void
    {
        if (false === in_array($mode, self::$allowedModes)) {
            throw new InvalidArgumentException('Invalid mode. Got: ' . $mode);
        }

        try {
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
        } catch (ConnectException $exception) {
            throw new CannotConnectToHoverfly('Could not connect to hoverfly. ' . $exception->getMessage());
        } catch (Throwable $th) {
            throw new Runtime(sprintf('An error occurred: %s', $th->getMessage()));
        }
    }

    /**
     * Sets an entire simulation set
     * @see https://docs.hoverfly.io/en/latest/pages/keyconcepts/simulations/simulations.html
     */
    public function setSimulation(Simulation $simulation): void
    {
        try {
            $this->httpClient->request(
                'POST',
                'simulation',
                [
                    'headers' => ['Content-Type' => 'application-json'],
                    'json' => $simulation
                ]
            );
        } catch (ConnectException $exception) {
            throw new CannotConnectToHoverfly('Could not connect to hoverfly. ' . $exception->getMessage());
        } catch (Throwable $th) {
            throw new Runtime(sprintf('An error occurred: %s', $th->getMessage()));
        }
    }

    /**
     * Deletes the entire simulation set
     * @see https://docs.hoverfly.io/en/latest/pages/keyconcepts/simulations/simulations.html
     */
    public function deleteSimulation(): void
    {
        try {
            $this->httpClient->request('DELETE', 'simulation');
        } catch (ConnectException $exception) {
            throw new CannotConnectToHoverfly('Could not connect to hoverfly. ' . $exception->getMessage());
        } catch (Throwable $th) {
            throw new Runtime(sprintf('An error occurred: %s', $th->getMessage()));
        }
    }

    /**
     * Exports the entire simulation set as JSON string
     * @see https://docs.hoverfly.io/en/latest/pages/keyconcepts/simulations/simulations.html
     */
    public function exportSimulation(): string
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                'simulation',
                [
                    'headers' => ['Content-Type' => 'application-json']
                ]
            );
            return $response->getBody()->getContents();
        } catch (ConnectException $exception) {
            throw new CannotConnectToHoverfly('Could not connect to hoverfly. ' . $exception->getMessage());
        } catch (Throwable $th) {
            throw new Runtime(sprintf('An error occurred: %s', $th->getMessage()));
        }
    }
}
