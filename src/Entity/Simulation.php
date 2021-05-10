<?php

declare(strict_types=1);

namespace Pachico\HoverPHP\Entity;

use JsonSerializable;

class Simulation implements JsonSerializable
{
    /** @var array[] $pairs */
    private array $pairs =  [];

    /** @var string[] $meta */
    private array $meta = [
        'schemaVersion' => 'v5'
    ];

    public static function new(): self
    {
        return new self();
    }

    public function withPair(Request $request, Response $response): self
    {
        $this->pairs[] = [
            'request' => $request,
            'response' => $response
        ];

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'data' => [
                'pairs' => $this->pairs
            ],
            'meta' => $this->meta
        ];
    }
}
