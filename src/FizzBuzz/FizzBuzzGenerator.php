<?php

namespace App\FizzBuzz;

use App\FizzBuzz\FizzBuzzGeneratorInterface;

class FizzBuzzGenerator implements FizzBuzzGeneratorInterface
{

    /**
     * @inheritDoc
     */
    public function generate(int $int1, int $int2, int $limit, string $str1, string $str2)
    {
        return array_values(array_map(fn($listItem) => $this->transform($listItem, $int1, $int2, $str1, $str2), range(1, $limit)));
    }

    /**
     * Evaluates an item from the list and performs the specific transformations that dictates the exercise.
     *
     * In the order of precedence, first, the product of $int1 and $int2 is evaluated, then $int1, and finally $int2.
     * If the number is not a multiple in any case, it is transformed to string and returned as it.
     *
     * @param int $listItem List of the item to transform
     * @param int $int1 Number which multiples are replaced by $str1.
     * @param int $int2 Number which multiples are replaced by $str2.
     * @param string $str1 String which replaces all multiples of $int1.
     * @param string $str2 String which replaces all multiples of $int2.
     * @return string Transformed item.
     */
    private function transform(int $listItem, int $int1, int $int2, string $str1, string $str2): string {
        return match (true) {

            // List of transformation cases.

            // Case 1: Multiples of $int1 * $int2
            $listItem % ($int1 * $int2) === 0 => "{$str1}{$str2}",

            // Case 2: Multiples of $int1
            $listItem % $int1 === 0 => $str1,

            // Case 3: Multiples of $int2
            $listItem % $int2 === 0 => $str2,

            // No match (return same value but cast to string).
            default => (string) $listItem

        };
    }
}
