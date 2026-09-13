<?php

namespace App\Stats\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class MostPopularUrlResponseDto
{
    public function __construct(
        #[Assert\NotBlank()]
        public string $url,
        #[Assert\Type('integer')]
        public int $hits,
    ) {
    }
}
