<?php

namespace App\FizzBuzz;

class FizzBuzzMathUtilities
{
    /**
     * Gets the greatest common divisor between two numbers.
     *
     * @param int $int1 Number 1
     * @param int $int2 Number 2
     *
     * @return int GCM of $int1 and $int2
     */
    public static function gcd(int $int1, int $int2): int
    {
        return 0 === $int2 ? $int1 : static::gcd($int2, $int1 % $int2);
    }

    /**
     * Gets the Least Common Multiple between two numbers.
     *
     * @param int $int1 Number 1
     * @param int $int2 Number 2
     *
     * @return int LCM of $int 1 and $int2
     */
    public static function lcm(int $int1, int $int2): int
    {
        self::verifyIntInputsOrDie($int1, $int2);

        return ($int1 * $int2) / self::gcd($int1, $int2);
    }

    /**
     * Guard utility that verifies $int1 and $int2 are valid by:
     * * Checking both are greater than 0
     * * They and their computations are under PHP_INT_MAX to avoid overflow or silent float casts.
     */
    private static function verifyIntInputsOrDie(int $int1, int $int2): void
    {
        if ($int1 <= 0 || $int2 <= 0) {
            throw new \InvalidArgumentException('int1 and int2 must be both greater than 0');
        }

        // Catch silent float cast due to overflow in PHP_INT_MAX.
        if ($int1 > PHP_INT_MAX / $int2) {
            throw new \OverflowException('int1 and int2 cannot be used to generate a fizzbuzz list. Select lower numbers than those ones.');
        }

    }
}
