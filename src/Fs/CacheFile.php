<?php

namespace Siarko\CacheFiles\Fs;

use Siarko\Paths\Provider\ProjectPathProvider;

class CacheFile
{

    /**
     * @param ProjectPathProvider $pathProvider
     * @param string $fileName
     */
    public function __construct(
        private readonly ProjectPathProvider $pathProvider,
        private readonly string              $fileName
    )
    {
    }

    /**
     * @param string $data
     * @return bool
     */
    public function write(string $data): bool
    {
        if(!file_exists($this->pathProvider->getConstructedPath())){
            mkdir($this->pathProvider->getConstructedPath(), 0777, true);
        }
        return file_put_contents($this->pathProvider->getConstructedPath($this->fileName), $data) !== false;
    }

    /**
     * @return string|null
     */
    public function read(): ?string
    {
        $data = false;
        if(file_exists($this->pathProvider->getConstructedPath($this->fileName))){
            $data = file_get_contents($this->pathProvider->getConstructedPath($this->fileName));
        }
        if($data === false){
            return null;
        }
        return $data;
    }

}