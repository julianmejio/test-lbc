<?php

namespace App\Stats\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class MostPopularUrlResponse
{
    public function __construct(
        #[Assert\NotBlank()]
        public readonly string $url,
        #[Assert\Type('numeric')]
        public readonly int $hits,
    ) {
    }
}
