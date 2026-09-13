<?php

namespace App\Stats;

use App\Stats\Dto\MostPopularUrlResponseDto;

class UrlStats
{
    private const string STATS_ID = 'popular_urls';

    /**
     * @param KeyValueRepositoryInterface<string, int> $keyValueRepository
     */
    public function __construct(
        private readonly KeyValueRepositoryInterface $keyValueRepository,
    ) {
    }

    /**
     * @throws \JsonException
     */
    public function hitUrl(string $url, int $hits = 1): void
    {
        $this->keyValueRepository
            ->set(self::STATS_ID, $url, ($this->keyValueRepository->get(self::STATS_ID, $url) ?? 0) + $hits)
            ->commit(self::STATS_ID);
    }

    public function getMostPopularUrl(): ?MostPopularUrlResponseDto
    {
        $stats = $this->keyValueRepository->getAll(self::STATS_ID);
        if (0 >= count($stats)) {
            return null;
        }
        arsort($stats);
        $popularUrl = array_keys($stats)[0];

        return new MostPopularUrlResponseDto($popularUrl, $stats[$popularUrl]);
    }
}
