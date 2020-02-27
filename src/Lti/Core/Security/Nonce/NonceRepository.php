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

namespace App\Lti\Core\Security\Nonce;

class NonceRepository implements NonceRepositoryInterface
{
    /** @var NonceInterface[] */
    private $nonces;

    public function __construct(array $nonces = [])
    {
        foreach ($nonces as $nonce) {
            $this->add($nonce);
        }
    }

    public function add(NonceInterface $nonce): self
    {
        $this->nonces[$nonce->getValue()] = $nonce;

        return $this;
    }

    public function find(string $value): ?NonceInterface
    {
        return $this->nonces[$value] ?? null;
    }

    public function save(NonceInterface $nonce): void
    {
        $this->add($nonce);
    }
}