<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace App\Lti\Core\Security\Key;

use Lcobucci\JWT\Signer\Key;

class KeyChain implements KeyChainInterface
{
    /** @var string */
    private $id;

    /** @var string */
    private $group;

    /** @var string */
    private $publicKey;

    /** @var string|null */
    private $privateKey;

    /** @var string|null */
    private $privateKeyPassPhrase;

    public function __construct(
        string $id,
        string $group,
        string $publicKey,
        string $privateKey = null,
        string $privateKeyPassPhrase = null
    ) {
        $this->id = $id;
        $this->group = $group;
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->privateKeyPassPhrase = $privateKeyPassPhrase;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getPublicKey(): Key
    {
        return new Key($this->publicKey);
    }

    public function getPrivateKey(): ?Key
    {
        return null !== $this->privateKey
            ? new Key($this->privateKey, $this->privateKeyPassPhrase)
            : null;
    }
}
