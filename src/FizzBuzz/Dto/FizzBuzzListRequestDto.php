<?php

namespace App\FizzBuzz\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * List of valid parameters accepted for the generation of the FizzBuzz list generation.
 */
readonly class FizzBuzzListRequestDto
{
    /**
     * @param int    $int1  number whose multiples are replaced by {@see FizzBuzzListRequest::$str1}
     * @param int    $int2  number whose multiples are replaced by {@see FizzBuzzListRequest::$str2}
     * @param int    $limit last number to be generated on the list
     * @param string $str1  string that will replace the multiples of {@see FizzBuzzListRequest::$int1}
     * @param string $str2  string that will replace the multiples of {@see FizzBuzzListRequest::$int2}
     */
    public function __construct(
        #[Assert\NotBlank(message: 'Provide a number where multiples will be replaced by str1')]
        #[Assert\Type('integer', message: 'Provide a valid integer')]
        #[Assert\GreaterThan(0, message: 'This value must be greater than 0')]
        public int $int1,
        #[Assert\NotBlank(message: 'Provide a number where multiples will be replaced by str2')]
        #[Assert\Type('integer', message: 'Provide a valid integer')]
        #[Assert\GreaterThan(0, message: 'This value must be greater than 0')]
        public int $int2,
        #[Assert\NotBlank(message: 'Provide the max number to generate in the list')]
        #[Assert\Range(notInRangeMessage: 'Provide a number between 1 and 100,000. Greater lists are not possible', min: 1, max: 100_000)]
        #[Assert\Type('integer', message: 'Provide a valid integer')]
        public int $limit,
        #[Assert\NotBlank(message: 'Provide a string that replaces the multiples of int1')]
        public string $str1,
        #[Assert\NotBlank(message: 'Provide a string that replaces the multiples of str2')]
        public string $str2,
    ) {
    }

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
