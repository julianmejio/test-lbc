<?php

namespace App\FizzBuzz\Event;

use App\FizzBuzz\Dto\FizzBuzzListRequestDto;
use Symfony\Contracts\EventDispatcher\Event;

final class FizzBuzzListGeneratedEvent extends Event
{
    public const string NAME = 'fizzbuzzlist.generated';

    public function __construct(
        private readonly FizzBuzzListRequestDto $parameters,
        private readonly ?string $endpoint = null,
    ) {
    }

    public function getParameters(): FizzBuzzListRequestDto
    {
        return $this->parameters;
    }

    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }
}
