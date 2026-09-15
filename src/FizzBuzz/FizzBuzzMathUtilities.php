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
        return ($int1 * $int2) / self::gcd($int1, $int2);
    }
}
