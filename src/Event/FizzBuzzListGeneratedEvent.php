<?php

namespace App\Event;

use App\FizzBuzz\Dto\FizzBuzzListRequestDto;
use Symfony\Contracts\EventDispatcher\Event;

final class FizzBuzzListGeneratedEvent extends Event
{
    public const string NAME = 'fizzbuzzlist.generated';

    public function __construct(
        private readonly FizzBuzzListRequestDto $parameters,
    ) {
    }

    public function getParameters(): FizzBuzzListRequestDto
    {
        return $this->parameters;
    }
}
