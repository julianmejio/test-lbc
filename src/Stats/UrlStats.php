<?php

namespace App\Stats;

use App\Stats\Dto\MostPopularUrlResponseDto;

/**
 * Manages the hits done on URLs.
 */
class UrlStats
{
    /**
     * Store ID used for this type of stats.
     */
    private const string STATS_ID = 'popular_urls';

    /**
     * @param KeyValueRepositoryInterface<string, int> $keyValueRepository
     */
    public function __construct(
        private readonly KeyValueRepositoryInterface $keyValueRepository,
    ) {
    }

    /**
     * Increments the hit counter of a URL.
     *
     * @param string $url  URL to increment the counter
     * @param int    $hits Value to increment by the counter. Defaults to 1.
     *
     * @throws \JsonException
     */
    public function hitUrl(string $url, int $hits = 1): void
    {
        $this->keyValueRepository
            ->set(self::STATS_ID, $url, ($this->keyValueRepository->get(self::STATS_ID, $url) ?? 0) + $hits)
            ->commit(self::STATS_ID);
    }

    /**
     * Sort the array with {@see arsort()} and return the first value, considered as the most hit.
     *
     * @return MostPopularUrlResponseDto|null returns a DTO containing the URL and the number of hits, or null if no stats found
     */
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

    /**
     * Deletes all the stats' data.
     */
    public function reset(): void
    {
        $this->keyValueRepository->clear(self::STATS_ID);
    }
}
