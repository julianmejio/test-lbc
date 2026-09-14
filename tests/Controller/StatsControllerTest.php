<?php

namespace App\Tests\Controller;

use App\Stats\Dto\MostPopularUrlResponseDto;
use App\Stats\UrlStats;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StatsControllerTest extends WebTestCase
{
    #[DataProvider('statsDataProvider')]
    public function testGetMostPopularUrl(?MostPopularUrlResponseDto $stats, string $expected): void
    {
        $client = self::createClient();
        $urlStats = $this->createStub(UrlStats::class);
        $urlStats->method('getMostPopularUrl')->willReturn($stats);
        $container = static::getContainer();
        $container->set(UrlStats::class, $urlStats);
        $client->request('GET', '/stats');
        $this->assertJsonStringEqualsJsonString($expected, $client->getResponse()->getContent());
    }

    public static function statsDataProvider()
    {
        return [
            // With stats
            [new MostPopularUrlResponseDto('/fizzbuzz?int1=1', 123), '{"url":"/fizzbuzz?int1=1", "hits":123}'],

            // No stats yet
            [null, '{}'],
        ];
    }
}
