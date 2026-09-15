<?php

namespace App\FizzBuzz;

/**
 * Implementation of a FizzBuzz generator list.
 */
class FizzBuzzGenerator implements FizzBuzzGeneratorInterface
{
    /**
     * First number of the generated list.
     */
    private const int LIST_START = 1;

    public function generate(int $int1, int $int2, int $limit, string $str1, string $str2): array
    {
        return array_map(
            fn ($listItem) => $this->transform($listItem, $int1, $int2, $str1, $str2),
            range(self::LIST_START, $limit)
        );
    }

    /**
     * Evaluates an item from the list and performs the specific transformations that dictates the exercise.
     *
     * In the order of precedence, first, the product of $int1 and $int2 is evaluated, then $int1, and finally $int2.
     * If the number is not a multiple in any case, it is transformed to string and returned as it.
     *
     * @param int    $listItem List of the item to transform
     * @param int    $int1     number which multiples are replaced by $str1
     * @param int    $int2     number which multiples are replaced by $str2
     * @param string $str1     string which replaces all multiples of $int1
     * @param string $str2     string which replaces all multiples of $int2
     *
     * @return string transformed item
     */
    private function transform(int $listItem, int $int1, int $int2, string $str1, string $str2): string
    {
        $lcm = FizzBuzzMathUtilities::lcm($int1, $int2);

        return match (true) {

            // List of transformation cases.

            // Case 1: Multiples of $int1 * $int2 are replaced by "$str1$str2"
            0 === $listItem % $lcm => "{$str1}{$str2}",

            // Case 2: Multiples of $int1 are replaced by $str1
            0 === $listItem % $int1 => $str1,

            // Case 3: Multiples of $int2 are replaced by $str2
            0 === $listItem % $int2 => $str2,

            // No match (No replacement but cast to string).
            default => (string) $listItem,

        };
    }
}
