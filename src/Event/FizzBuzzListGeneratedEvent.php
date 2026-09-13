<?php

namespace App\Event;

use App\FizzBuzz\Dto\FizzBuzzListRequest;
use Symfony\Contracts\EventDispatcher\Event;

final class FizzBuzzListGeneratedEvent extends Event
{
    public const string NAME = 'fizzbuzzlist.generated';

    public function __construct(
        private readonly FizzBuzzListRequest $parameters,
    ) {
    }

    public function getParameters(): FizzBuzzListRequest
    {
        return $this->parameters;
    }
}
