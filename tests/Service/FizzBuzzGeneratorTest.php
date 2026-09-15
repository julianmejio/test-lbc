<?php

namespace App\Tests\Service;

use App\FizzBuzz\FizzBuzzGenerator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FizzBuzzGeneratorTest extends KernelTestCase
{
    private static FizzBuzzGenerator $fizzBuzzGenerator;

    public static function setUpBeforeClass(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        self::$fizzBuzzGenerator = $container->get(FizzBuzzGenerator::class);
    }

    #[DataProvider('generateListDataProvider')]
    public function testGenerate(int $int1, int $int2, int $limit, string $str1, string $str2, array $expected): void
    {
        $actual = self::$fizzBuzzGenerator->generate($int1, $int2, $limit, $str1, $str2);
        $this->assertEquals($expected, $actual);
    }

    public function testOverflowException(): void
    {
        $this->expectException(\OverflowException::class);
        $this->expectExceptionMessageIs('int1 and int2 cannot be used to generate a fizzbuzz list. Select lower numbers than those ones.');
        self::$fizzBuzzGenerator->generate(3037000500, 3037000500000, 100, 'A', 'B');
    }

    public function testOverflowExceptionInListArray(): void
    {
        $this->expectException(\ValueError::class);
        self::$fizzBuzzGenerator->generate(1, 10, 1000000000000, 'A', 'B');
    }

    public function testZeroOrNegativeDivisors(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        self::$fizzBuzzGenerator->generate(0, 10, 100, 'A', 'B');
        self::$fizzBuzzGenerator->generate(-19, 10, 100, 'A', 'B');
        self::$fizzBuzzGenerator->generate(-19, 0, 100, 'A', 'B');
        self::$fizzBuzzGenerator->generate(-19, -19, 100, 'A', 'B');
        self::$fizzBuzzGenerator->generate(10, -19, 100, 'A', 'B');
    }

    public static function generateListDataProvider(): array
    {
        return [
            // Original FizzBuzz
            [3, 5, 16, 'fizz', 'buzz', ['1', '2', 'fizz', '4', 'buzz', 'fizz', '7', '8', 'fizz', 'buzz', '11', 'fizz', '13', '14', 'fizzbuzz', '16']],
            // Case 1
            [1, 2, 10, 'foo', 'bar', ['foo', 'foobar', 'foo', 'foobar', 'foo', 'foobar', 'foo', 'foobar', 'foo', 'foobar']],
            // int1 = int2
            [6, 6, 30, 'A', 'B', ['1', '2', '3', '4', '5', 'AB', '7', '8', '9', '10', '11', 'AB', '13', '14', '15', '16', '17', 'AB', '19', '20', '21', '22', '23', 'AB', '25', '26', '27', '28', '29', 'AB']],
            // int1 = 1
            [1, 8, 30, 'A', 'B', ['A', 'A', 'A', 'A', 'A', 'A', 'A', 'AB', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'AB', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'AB', 'A', 'A', 'A', 'A', 'A', 'A']],
            // limit = 1 (start of list)
            [1, 8, 1, 'A', 'B', ['A']],
        ];
    }
}
