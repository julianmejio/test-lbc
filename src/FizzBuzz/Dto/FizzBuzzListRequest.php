<?php

namespace App\FizzBuzz\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class FizzBuzzListRequest
{
    public function __construct(
        #[Assert\NotBlank()]
        #[Assert\Type('integer')]
        private readonly int $int1,

        #[Assert\NotBlank()]
        #[Assert\Type('integer')]
        private readonly int $int2,

        #[Assert\NotBlank()]
        #[Assert\Type('integer')]
        private readonly int $limit,

        #[Assert\NotBlank()]
        private readonly string $str1,

        #[Assert\NotBlank()]
        private readonly string $str2,
    ) {}

    public function getInt1(): int
    {
        return $this->int1;
    }

    public function getInt2(): int
    {
        return $this->int2;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getStr1(): string
    {
        return $this->str1;
    }

    public function getStr2(): string
    {
        return $this->str2;
    }
}
