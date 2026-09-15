<?php

namespace App\Stats;

/**
 * Key-value data store used for counting or relational stats.
 *
 * @template K Key type
 * @template V Value type
 */
interface KeyValueRepositoryInterface
{
    /**
     * Get all the items in the data store.
     *
     * @return array<K, V> returns the array containing all the elements of the store
     */
    public function getAll(string $storeId): array;

    /**
     * Get the value stored for a $key in the data store $storeId.
     *
     * @param string $storeId store ID
     * @param K      $key     Key
     *
     * @return V|null returns the value that matches $key at $storeId; null otherwise
     */
    public function get(string $storeId, mixed $key): mixed;

    /**
     * Stores a value inside a $key within the $storeId.
     *
     * @param string $storeId store ID
     * @param K      $key     Key
     * @param V      $value   Value
     *
     * @return KeyValueRepositoryInterface<K, V>
     */
    public function set(string $storeId, mixed $key, mixed $value): KeyValueRepositoryInterface;

    /**
     * Persists all the changes made with {@see KeyValueRepositoryInterface::set()}.
     *
     * @param string $storeId store ID
     */
    public function commit(string $storeId): void;

    /**
     * Deletes all the data inside data store.
     *
     * @param string $storeId store ID
     */
    public function clear(string $storeId): void;
}
