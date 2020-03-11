<?php

declare(strict_types=1);

namespace App\Security\OAuth2\Factory;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use League\OAuth2\Server\CryptKey;

class OAuth2CryptKeyFactory
{
    /**
     * @throws FileNotFoundException
     */
    public function create(string $keyPath, string $keyPassPhrase = null): CryptKey
    {
        return new CryptKey(file_get_contents($keyPath), $keyPassPhrase, false);
    }
}
