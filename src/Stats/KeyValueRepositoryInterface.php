<?php

namespace App\Stats;

/**
 * @template K Key type
 * @template V Value type
 */
interface KeyValueRepositoryInterface
{
    /**
     * @return array<K, V>
     */
    public function getAll(string $storeId): array;

    /**
     * @param K $key
     *
     * @return V|null
     */
    public function get(string $storeId, mixed $key): mixed;

    /**
     * @param K $key
     * @param V $value
     *
     * @return KeyValueRepositoryInterface<K, V>
     */
    public function set(string $storeId, mixed $key, mixed $value): KeyValueRepositoryInterface;

    public function commit(string $storeId): void;
}
