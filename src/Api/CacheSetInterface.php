<?php

namespace Siarko\CacheFiles\Api;

/**
 * This interface represents the cache implementation. It supports operations to
 * check if a cache exists, retrieve data from the cache, load data from a file into the cache, and set
 * new data to the cache. It also provides functionality to clear the cache.
 */
interface CacheSetInterface
{

    /**
     * Returns true if the cache exists, false otherwise.
     * @param string $type
     * @return bool
     */
    public function exists(string $type): bool;

    /**
     * Returns the cache data for the given type.
     * @param string $type
     * @return array
     */
    public function get(string $type): array;

    /**
     * Saves the new data to the cache. It also provides functionality to clear the cache.
     * @param string $type
     * @param array $data
     * @return void
     */
    public function set(string $type, array $data);

    /**
     * Clears the cache.
     * @param string|null $type
     * @return void
     */
    public function clear(?string $type = null): void;

}