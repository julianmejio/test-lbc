<?php

namespace App\Stats;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

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
        $this->filesystem->dumpFile($this->getSafePath($storeId), json_encode($stagedContents, JSON_THROW_ON_ERROR));
        unset($this->uncommitedChanges[$storeId]);
    }

    public function clear(string $storeId): void
    {
        $this->filesystem->remove($this->getSafePath($storeId));
    }

    /**
     * @return array<K, V>
     *
     * @throws \JsonException
     */
    private function loadContents(string $storeId): array
    {
        if (!$this->filesystem->exists($this->getSafePath($storeId))) {
            return [];
        }

        return json_decode($this->filesystem->readFile($this->getSafePath($storeId)), true, 512, JSON_THROW_ON_ERROR);
    }

    private function getSafePath(string $storeId): string
    {
        $storePath = Path::join($this->tmpDir, $storeId);
        $safePath = Path::canonicalize($this->tmpDir);
        $safeStorePath = Path::canonicalize($storePath);

        if (!str_starts_with($safeStorePath, "{$safePath}/")) {
            throw new AccessDeniedException($storePath);
        }

        return $safeStorePath;
    }
}
