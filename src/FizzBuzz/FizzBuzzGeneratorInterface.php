<?php

namespace App\FizzBuzz;

/**
 * Represents a *FizzBuzz* service capable of generating a list, given the specified parameters.
 */
interface FizzBuzzGeneratorInterface
{
    /**
     * Generates a list of strings from 1 to $limit, where all multiples of $int1 are replaced by $str1, all multiples of $int2 are replaced by $str2, and all multiples of $int1 times $int2 are replaced by $str1$str2.
     *
     * @param int    $int1  number which multiples are replaced by $str1
     * @param int    $int2  number which multiples are replaced by $str2
     * @param int    $limit last number of the generated list, inclusive
     * @param string $str1  string which replaces all multiples of $int1
     * @param string $str2  string which replaces all multiples of $int2
     *
     * @return string[] a list of numbers with the replacements for $int1, $int2, and $int1 * $int2
     */
    public function generate(
        int $int1,
        int $int2,
        int $limit,
        string $str1,
        string $str2,
    ): array;
}
