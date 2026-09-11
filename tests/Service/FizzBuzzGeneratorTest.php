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
    public function testGenerate(int $int1, int $int2, int $limit, string $str1, string $str2, array $expected): void {
        $actual = self::$fizzBuzzGenerator->generate($int1, $int2, $limit, $str1, $str2);
        $this->assertEquals($expected, $actual);
    }

    public static function generateListDataProvider(): array
    {
        return [
            // Original FizzBuzz
            [3, 5, 16, 'fizz', 'buzz', ['1', '2', 'fizz', '4', 'buzz', 'fizz', '7', '8', 'fizz', 'buzz', '11', 'fizz', '13', '14', 'fizzbuzz', '16']],
            // Case 1
            [1, 2, 10, 'foo', 'bar', ['foo', 'foobar', 'foo', 'foobar', 'foo', 'foobar', 'foo', 'foobar', 'foo', 'foobar']]
        ];
    }
}
