<?php

namespace Siarko\CacheFiles;

use Siarko\Api\State\AppMode;
use Siarko\Api\State\AppState;
use Siarko\Paths\Provider\AbstractPathProvider;
use Siarko\Serialization\Api\SerializerInterface;
use Siarko\Files\Path\PathInfo;
use Siarko\CacheFiles\Api\CacheSetInterface;
use Siarko\CacheFiles\Fs\CacheFile;
use Siarko\CacheFiles\Fs\CacheFileFactory;

/**
 * This class represents the cache implementation. It supports operations to
 * check if a cache exists, retrieve data from the cache, load data from a file into the cache, and set
 * new data to the cache. It also provides functionality to clear the cache.
 */
class CacheSet implements CacheSetInterface
{
    public const CACHE_FILE_EXTENSION = 'cache';

    /**
     * @var CacheFile[]
     */
    protected array $cacheFiles = [];

    /**
     * @var array
     */
    protected array $caches = [];

    /**
     * @param CacheFileFactory $cacheFileFactory
     * @param AbstractPathProvider $cacheDirectory
     * @param SerializerInterface $serializer
     * @param PathInfo $pathInfo
     * @param AppState $appState
     */
    public function __construct(
        protected readonly CacheFileFactory     $cacheFileFactory,
        protected readonly AbstractPathProvider $cacheDirectory,
        protected readonly SerializerInterface  $serializer,
        protected readonly PathInfo             $pathInfo,
        protected readonly AppState             $appState
    )
    {
    }

    /**
     * @param string $type
     * @return bool
     */
    public function exists(string $type): bool
    {
        if ($this->appState->isAppMode(AppMode::PROD)) {
            $this->loadFromFile($type);
        }
        return array_key_exists($type, $this->caches);
    }

    /**
     * @param string $type
     * @return array
     */
    public function get(string $type): array
    {
        if ($this->appState->isAppMode(AppMode::PROD)) {
            $this->loadFromFile($type);
        }
        return $this->caches[$type];
    }

    /**
     * @param string $type
     */
    protected function loadFromFile(string $type): void
    {
        if (!array_key_exists($type, $this->caches)) {
            $cacheFile = $this->getCacheFile($type);
            $content = $cacheFile->read();
            if ($content != null) {
                $this->caches[$type] = $this->serializer->deserialize($content);
            }
        }
    }

    /**
     * @param string $type
     * @param array $data
     */
    public function set(string $type, array $data)
    {
        $this->caches[$type] = $data;
        $cacheFile = $this->getCacheFile($type);
        if(!$cacheFile->write($this->serializer->serialize($data))){
            throw new \RuntimeException('Cannot write cache file');
        }
    }

    /**
     * @param string $type
     * @return CacheFile
     */
    protected function getCacheFile(string $type): CacheFile
    {
        if (array_key_exists($type, $this->cacheFiles)) {
            return $this->cacheFiles[$type];
        }

        $cacheFile = $this->cacheFileFactory->create([
            'pathProvider' => $this->cacheDirectory,
            'fileName' => $type . '.' . self::CACHE_FILE_EXTENSION
        ]);
        $this->cacheFiles[$type] = $cacheFile;
        return $cacheFile;
    }

    /**
     * Clear all config files
     */
    public function clear(?string $type = null): void
    {
        $type = is_null($type) ? '.*' : $type;
        $pathInfo = $this->pathInfo->read($this->cacheDirectory->getConstructedPath());
        $files = $pathInfo->readDirectoryFiles('/'.$type.'\.' . self::CACHE_FILE_EXTENSION . '$/', false);
        foreach ($files as $file) {
            unlink($file);
        }
        $this->caches = [];
    }
}