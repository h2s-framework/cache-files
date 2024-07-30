<?php

namespace Siarko\CacheFiles;

use Siarko\CacheFiles\Api\CacheSetInterface;

/**
 * This class represents the cache manager implementation.
 */
class Manager
{


    /**
     * @param CacheSetInterface[] $caches
     */
    public function __construct(
        private readonly array $caches = []
    )
    {
    }

    /**
     * @return array
     */
    public function getTypes(): array
    {
        return array_keys($this->caches);
    }

    /**
     * @param string|null $type
     * @return void
     */
    public function clear(?string $type = null): void
    {
        /** @var CacheSetInterface $cache */
        foreach ($this->caches as $cacheType => $cache) {
            if($type === null || $type === $cacheType){
                $cache->clear();
            }
        }
    }
}