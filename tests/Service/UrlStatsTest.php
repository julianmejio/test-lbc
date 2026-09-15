<?php

namespace App\Tests\Service;

use App\Stats\Dto\MostPopularUrlResponseDto;
use App\Stats\UrlStats;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UrlStatsTest extends KernelTestCase
{
    private static UrlStats $urlStats;

    public static function setUpBeforeClass(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        static::$urlStats = $container->get(UrlStats::class);
        static::$urlStats->reset();
    }

    protected function tearDown(): void
    {
        static::$urlStats->reset();
    }

    /**
     * @param array<int, array{string, int}> $hits
     *
     * @throws \JsonException
     */
    #[DataProvider('hitUrlDataProvider')]
    public function testGetMostPopularUrl(array $hits, MostPopularUrlResponseDto $expected): void
    {
        foreach ($hits as $hit) {
            static::$urlStats->hitUrl($hit[0], $hit[1]);
        }

        $result = static::$urlStats->getMostPopularUrl();

        $this->assertEquals($expected, $result);
    }

    public function testNoStatsYet(): void
    {
        $expected = null;
        $result = static::$urlStats->getMostPopularUrl();

        $this->assertEquals($expected, $result);
    }

    public static function hitUrlDataProvider(): array
    {
        return [
            [[['/test1?foo=bar', 5]], new MostPopularUrlResponseDto('/test1?foo=bar', 5)],
            [
                [
                    ['/test?foo=bar', 1],
                    ['/test?foobar=helloworld', 1],
                    ['/test?foo=bar', 1],
                ],
                new MostPopularUrlResponseDto('/test?foo=bar', 2),
            ],
            [
                [
                    ['/url1?a=1&b=2', 1],
                    ['/url1?a=1&b=2', 1],
                    ['/url1?a=1&b=2', 1],
                    ['/url1?a=1&b=2', 1],
                    ['/url1?a=1&b=2', 1],
                    ['/url1?a=1&b=3', 1],
                    ['/url1?a=1&b=3', 1],
                    ['/url1?a=1&b=3', 1],
                    ['/url1?a=1&b=3', 1],
                    ['/url1?a=1&b=3', 1],
                ],
                new MostPopularUrlResponseDto('/url1?a=1&b=2', 5),
            ],
        ];
    }
}
