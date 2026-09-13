<?php

namespace App\Stats;

use App\Stats\Dto\MostPopularUrlResponse;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class UrlStatsStore
{
    private const STORE_NAME = 'stats.json';
    private readonly Filesystem $filesystem;

    public function __construct(
        #[Autowire('%kernel.cache_dir%')]
        private readonly string $tmpDirectory,
    ) {
        $this->filesystem = new Filesystem();
    }

    /**
     * @throws \JsonException
     */
    public function hitUrl(string $url, int $hits = 1): void
    {
        $stats = $this->getStoreContents();
        $previousHits = $stats[$url][1] ?? 0;
        $stats[$url] = [$url, $previousHits + $hits];
        $this->saveStoreContents($stats);
    }

    public function getMostPopularUrl(?string $prefix = null): ?MostPopularUrlResponse
    {
        $stats = $this->getStoreContents();
        if (0 >= count($stats)) {
            return null;
        }
        usort($stats, fn ($url1, $url2) => $url2[1] - $url1[1]);
        $mostPopular = $stats[0];

        return new MostPopularUrlResponse($mostPopular[0], $mostPopular[1]);
    }

    /**
     * @return array<string, list<mixed>>
     *
     * @throws \JsonException
     */
    private function getStoreContents(): array
    {
        $statsFile = Path::join($this->tmpDirectory, self::STORE_NAME);
        $statsContents = $this->filesystem->exists($statsFile) ? $this->filesystem->readFile($statsFile) : '[]';

        return json_decode($statsContents, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param array<string, list<mixed>> $contents
     *
     * @throws \JsonException
     */
    private function saveStoreContents(array $contents): void
    {
        $this->filesystem->dumpFile(Path::join($this->tmpDirectory, self::STORE_NAME), json_encode($contents, JSON_THROW_ON_ERROR));
    }
}
