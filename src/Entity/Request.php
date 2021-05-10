<?php

declare(strict_types=1);

namespace Pachico\HoverPHP\Entity;

use InvalidArgumentException;
use JsonSerializable;
use League\Uri\QueryString;
use Psr\Http\Message\RequestInterface;
use stdClass;

class Request implements JsonSerializable
{
    private array $method = [];
    private array $scheme  = [];
    private array $destination  = [];
    private array $path  = [];
    private array $query = [];
    private array $headers = [];
    private array $body  = [];

    private function __construct()
    {
    }

    public static function new(): self
    {
        return new self();
    }

    public static function fromPSR7(RequestInterface $request): self
    {
        $self = self::new();
        $self->withMethodMatcher(Matcher::DEFAULT, $request->getMethod());
        $self->withSchemeMatcher(Matcher::DEFAULT, $request->getUri()->getScheme());
        $self->withDestinationMatcher(Matcher::DEFAULT, $request->getUri()->getHost());
        $self->withPathMatcher(Matcher::DEFAULT, $request->getUri()->getPath());
        $self->withBodyMatcher(Matcher::DEFAULT, $request->getBody()->getContents());

        foreach (QueryString::parse($request->getUri()->getQuery()) as $keyValues) {
            // Do not add anything if key-pair is empty (why is it even there?)
            if ('' === $keyValues[0] && is_null($keyValues[1])) {
                continue;
            }
            $self->withQueryMatcher($keyValues[0], Matcher::DEFAULT, $keyValues[1] ?? '');
        }

        foreach ($request->getHeaders() as $key => $val) {
            // Ignore this host. It's strictly PSR but not cURL
            if ('Host' === $key) {
                continue;
            }
            $self->withHeaderMatcher($key, Matcher::DEFAULT, current($val));
        }

        return $self;
    }

    public function withMethodMatcher(string $matcher, string $value): self
    {
        $this->validateIsValidMatcher($matcher);
        $this->method[] = ['value' => $value, 'matcher' => $matcher];
        return $this;
    }

    public function withSchemeMatcher(string $matcher, string $value): self
    {
        $this->validateIsValidMatcher($matcher);
        $this->scheme[] = ['value' => $value, 'matcher' => $matcher];
        return $this;
    }

    public function withDestinationMatcher(string $matcher, string $value): self
    {
        $this->validateIsValidMatcher($matcher);
        $this->destination[] = ['value' => $value, 'matcher' => $matcher];
        return $this;
    }

    public function withPathMatcher(string $matcher, string $value): self
    {
        $this->validateIsValidMatcher($matcher);
        $this->path[] = ['value' => $value, 'matcher' => $matcher];
        return $this;
    }

    public function withBodyMatcher(string $matcher, string $value): self
    {
        $this->validateIsValidMatcher($matcher);
        $this->body[] = ['value' => $value, 'matcher' => $matcher];
        return $this;
    }

    public function withQueryMatcher(string $key, string $matcher, string $value): self
    {
        $this->validateIsValidMatcher($matcher);
        $this->query[$key][] = ['value' => $value, 'matcher' => $matcher];
        return $this;
    }

    public function withHeaderMatcher(string $key, string $matcher, string $value): self
    {
        $this->validateIsValidMatcher($matcher);
        $this->headers[$key][] = ['value' => $value, 'matcher' => $matcher];
        return $this;
    }

    private function validateIsValidMatcher(string $matcher): void
    {
        if (false === Matcher::isValidMatcher($matcher)) {
            throw new InvalidArgumentException('Invalid matcher. Got ' . $matcher);
        }
    }


    public function jsonSerialize()
    {
        $data = [
            'method' => $this->method,
            'scheme' => $this->scheme,
            'destination' => $this->destination,
            'path' => $this->path,
            'body' => $this->body,
        ];

        if (!empty($this->query)) {
            $data['query'] = $this->query;
        }

        if (!empty($this->headers)) {
            $data['headers'] = $this->headers;
        }

        return $data;
    }
}
