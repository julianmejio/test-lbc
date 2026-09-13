<?php

namespace App\Stats;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

/**
 * @template K Key type
 * @template V Value type
 *
 * @implements KeyValueRepositoryInterface<K, V>
 */
class KeyValueFilesystemRepository implements KeyValueRepositoryInterface
{
    /**
     * @var array<string, array<K, V>>
     */
    private array $uncommitedChanges = [];
    private readonly Filesystem $filesystem;

    public function __construct(
        #[Autowire('%kernel.cache_dir%')]
        private readonly string $tmpDir,
    ) {
        $this->filesystem = new Filesystem();
    }

    /**
     * @throws \JsonException
     */
    public function getAll(string $storeId): array
    {
        return $this->loadContents($storeId);
    }

    /**
     * @throws \JsonException
     */
    public function get(string $storeId, mixed $key): mixed
    {
        $contents = $this->loadContents($storeId);
        if (!isset($contents[$key])) {
            return null;
        }

        return $contents[$key];
    }

    public function set(string $storeId, mixed $key, mixed $value): KeyValueRepositoryInterface
    {
        if (!isset($this->uncommitedChanges[$storeId])) {
            $this->uncommitedChanges[$storeId] = [];
        }
        $this->uncommitedChanges[$storeId][$key] = $value;

        return $this;
    }

    /**
     * @throws \JsonException
     */
    public function commit(string $storeId): void
    {
        $contents = $this->loadContents($storeId);
        $stagedContents = array_merge($contents, $this->uncommitedChanges[$storeId]);
        $this->filesystem->dumpFile(Path::join($this->tmpDir, $storeId), json_encode($stagedContents, JSON_THROW_ON_ERROR));
        unset($this->uncommitedChanges[$storeId]);
    }

    /**
     * @return array<K, V>
     *
     * @throws \JsonException
     */
    private function loadContents(string $storeId): array
    {
        if (!$this->filesystem->exists(Path::join($this->tmpDir, $storeId))) {
            return [];
        }

        return json_decode($this->filesystem->readFile(Path::join($this->tmpDir, $storeId)), true, 512, JSON_THROW_ON_ERROR);
    }
}
