<?php

declare(strict_types=1);

namespace Pachico\HoverPHP\Entity;

use JsonSerializable;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class Response implements JsonSerializable
{
    private int $status;
    private string $body;
    private bool $isBodyEncoded;
    private array $headers = [];
    private bool $isTemplated = false;

    public static function new(
        int $status,
        string $body,
        bool $isBodyEncoded = false,
        array $headers = [],
        bool $isTemplated = false
    ): self {
        return new self($status, $body, $isBodyEncoded, $headers, $isTemplated);
    }

    private function __construct(
        int $status,
        string $body,
        bool $isBodyEncoded = false,
        array $headers = [],
        bool $isTemplated = false
    ) {
        $this->status = $status;
        $this->body = $body;
        $this->isBodyEncoded = $isBodyEncoded;
        $this->headers = $headers;
        $this->isTemplated = $isTemplated;
    }

    // @todo: find out if body is encoded or not
    // @todo: find out if it's templated
    public static function fromPSR7(ResponseInterface $response): self
    {
        $bodyContents = $response->getBody()->getContents();
        $isBodyEncoded = !preg_match('//u', $bodyContents) ? true : false;

        return new self(
            $response->getStatusCode(),
            $bodyContents,
            $isBodyEncoded,
            $response->getHeaders()
        );
    }

    public function jsonSerialize()
    {
        return [
            'status' => $this->status,
            'body' => $this->body,
            'encodedBody' => $this->isBodyEncoded,
            'headers' => $this->headers,
            'templated' => $this->isTemplated
        ];
    }
}
