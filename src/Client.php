<?php

declare(strict_types=1);

namespace Pachico\HoverPHP;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use InvalidArgumentException;
use Pachico\HoverPHP\Entity\Simulation;
use Pachico\HoverPHP\Exception\CannotConnectToHoverfly;
use Pachico\HoverPHP\Exception\CannotExportSimulation;

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

    public static function new(string $hoverflyDSN, ClientInterface $httpClient = null): self
    {
        return new self($hoverflyDSN, $httpClient);
    }

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
        } catch (\Throwable $th) {
            throw $th;
        }
    }

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
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function deleteSimulation(): void
    {
        try {
            $this->httpClient->request('DELETE', 'simulation');
        } catch (ConnectException $exception) {
            throw new CannotConnectToHoverfly('Could not connect to hoverfly. ' . $exception->getMessage());
        } catch (\Throwable $th) {
            throw $th;
        }
    }

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
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function exportSimulationToFile(string $destinationPath, $overwrite = false): void
    {
        if (file_exists($destinationPath) && false === $overwrite) {
            throw new CannotExportSimulation(
                sprintf('Cannot export simulation since destination already exists. Got: %s', $destinationPath)
            );
        }

        $destinationDir = dirname($destinationPath);
        if (!is_dir($destinationDir) || !is_writable($destinationDir)) {
            throw new CannotExportSimulation(
                sprintf(
                    'Cannot export simulation since destination folder does not exist or is not writable. Got: %s',
                    $destinationDir
                )
            );
        }

        file_put_contents($destinationPath, $this->exportSimulation(), LOCK_EX);
    }
}
