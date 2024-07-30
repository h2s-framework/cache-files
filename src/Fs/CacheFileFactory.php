<?php

namespace Siarko\CacheFiles\Fs;


use Siarko\Api\Factory\AbstractFactory;

class CacheFileFactory extends AbstractFactory
{
	public function create(array $data = []): CacheFile
	{
		return parent::_create(CacheFile::class, $data);
	}
}
